<?php
/**
 * Promote headings that skip a level so each page's outline descends one
 * step at a time (h1 then h3 becomes h1 then h2). Fixes the "Heading
 * elements are not in a sequentially-descending order" accessibility audit
 * on pages that were edited after import.
 *
 * Usage (all published pages, or only the slugs given):
 *
 *   wp eval-file wp-content/themes/bwfd/bin/fix-heading-levels.php
 *   wp eval-file wp-content/themes/bwfd/bin/fix-heading-levels.php home enjoyed
 *
 * Idempotent; prints every change it makes.
 *
 * @package bwfd
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit( "Run this script with `wp eval-file`.\n" );
}

/**
 * Walk blocks in document order, lowering heading levels that skip a step.
 *
 * A promotion is remembered for later headings of the same original level
 * (a row of cards each headed h3 all become h2, not just the first) until a
 * heading of a higher rank starts a new section.
 *
 * @param array $blocks  Parsed blocks (by reference).
 * @param int   $last    Level of the previous heading, 0 before the first.
 * @param array $map     Original level => promoted level, by reference.
 * @param array $changes Collected change descriptions (by reference).
 */
function bwfd_fix_heading_levels( array &$blocks, int &$last, array &$map, array &$changes ): void {
	foreach ( $blocks as &$block ) {
		$name = $block['blockName'] ?? '';
		if ( 'core/heading' === $name || 'bwfd/section-heading' === $name ) {
			$level = (int) ( $block['attrs']['level'] ?? 2 );
			foreach ( array_keys( $map ) as $mapped ) {
				if ( $mapped > $level ) {
					unset( $map[ $mapped ] );
				}
			}
			if ( isset( $map[ $level ] ) ) {
				$new = $map[ $level ];
			} elseif ( $last > 0 && $level > $last + 1 ) {
				$new           = $last + 1;
				$map[ $level ] = $new;
			} else {
				$new = $level;
			}
			if ( $new !== $level ) {
				$replace = static function ( $html ) use ( $level, $new ) {
					return is_string( $html ) ? preg_replace( "/<(\/?)h{$level}\b/i", "<\$1h{$new}", $html ) : $html;
				};
				$block['innerHTML']    = $replace( $block['innerHTML'] ?? '' );
				$block['innerContent'] = array_map( $replace, $block['innerContent'] ?? array() );
				if ( 2 === $new ) {
					unset( $block['attrs']['level'] );
				} else {
					$block['attrs']['level'] = $new;
				}
				$text      = trim( wp_strip_all_tags( $block['innerHTML'] ) );
				$changes[] = sprintf( 'h%d -> h%d  %s', $level, $new, mb_substr( $text, 0, 60 ) );
			}
			$last = $new;
		}
		if ( ! empty( $block['innerBlocks'] ) ) {
			bwfd_fix_heading_levels( $block['innerBlocks'], $last, $map, $changes );
		}
	}
}

$bwfd_slugs = array_values( array_filter( $args ?? array(), 'is_string' ) );
$bwfd_query = array(
	'post_type'      => 'page',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'orderby'        => 'menu_order title',
	'order'          => 'ASC',
);
if ( $bwfd_slugs ) {
	$bwfd_query['post_name__in'] = $bwfd_slugs;
}

$bwfd_total = 0;
foreach ( get_posts( $bwfd_query ) as $bwfd_page ) {
	// Pages whose template prints the title start their outline at that h1.
	$bwfd_last    = 'page-with-title' === get_page_template_slug( $bwfd_page ) ? 1 : 0;
	$bwfd_changes = array();
	$bwfd_map     = array();
	$bwfd_blocks  = parse_blocks( $bwfd_page->post_content );
	bwfd_fix_heading_levels( $bwfd_blocks, $bwfd_last, $bwfd_map, $bwfd_changes );

	if ( ! $bwfd_changes ) {
		WP_CLI::log( sprintf( '%-20s ok', $bwfd_page->post_name ) );
		continue;
	}
	$bwfd_result = wp_update_post(
		array(
			'ID'           => $bwfd_page->ID,
			'post_content' => wp_slash( serialize_blocks( $bwfd_blocks ) ),
		),
		true
	);
	if ( is_wp_error( $bwfd_result ) ) {
		WP_CLI::warning( sprintf( '%s: %s', $bwfd_page->post_name, $bwfd_result->get_error_message() ) );
		continue;
	}
	WP_CLI::log( sprintf( '%-20s %d heading(s) promoted', $bwfd_page->post_name, count( $bwfd_changes ) ) );
	foreach ( $bwfd_changes as $bwfd_change ) {
		WP_CLI::log( '  ' . $bwfd_change );
	}
	$bwfd_total += count( $bwfd_changes );
}
WP_CLI::success( sprintf( '%d heading(s) promoted.', $bwfd_total ) );
