<?php
/**
 * Create or update the book's chapter pages from a plain text list.
 *
 *   wp eval-file wp-content/themes/bwfd/bin/import-chapters.php chapters.txt [publish]
 *
 * One chapter per line, number then title, optionally a summary after a
 * pipe, in any of these shapes:
 *
 *   22. Compassion
 *   22 | Compassion | What the chapter is about, in a sentence.
 *   Chapter 22: Compassion
 *
 * Chapters are matched by number (the post's Order field), so the file can
 * be run again to fix titles. New chapters are created as drafts unless
 * `publish` is passed. The summary goes into the excerpt; the body is left
 * for the editor. Saving each post also creates its entry in the Insights
 * "Chapters" checklist.
 *
 * @package bwfd
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit( "Run this file with: wp eval-file bin/import-chapters.php chapters.txt\n" );
}

$bwfd_file = (string) ( $args[0] ?? '' );
if ( '' === $bwfd_file || ! is_readable( $bwfd_file ) ) {
	WP_CLI::error( 'Pass the path to a readable text file with one chapter per line.' );
}
$bwfd_status = 'publish' === ( $args[1] ?? '' ) ? 'publish' : 'draft';

$bwfd_existing = array();
foreach ( get_posts( array( 'post_type' => 'bwfd_chapter', 'post_status' => 'any', 'posts_per_page' => -1 ) ) as $bwfd_post ) {
	if ( (int) $bwfd_post->menu_order > 0 ) {
		$bwfd_existing[ (int) $bwfd_post->menu_order ] = $bwfd_post;
	}
}

$bwfd_created = 0;
$bwfd_updated = 0;
foreach ( file( $bwfd_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) as $bwfd_line ) {
	$bwfd_line = trim( $bwfd_line );
	if ( '' === $bwfd_line || '#' === $bwfd_line[0] ) {
		continue;
	}
	$bwfd_parts = array_map( 'trim', explode( '|', $bwfd_line ) );
	$bwfd_head  = $bwfd_parts[0];
	if ( count( $bwfd_parts ) >= 2 && is_numeric( $bwfd_head ) ) {
		$bwfd_number  = (int) $bwfd_head;
		$bwfd_title   = $bwfd_parts[1];
		$bwfd_summary = $bwfd_parts[2] ?? '';
	} elseif ( preg_match( '/^(?:chapter\s+)?(\d+)\s*[.:)\-–]\s*(.+)$/iu', $bwfd_head, $bwfd_m ) ) {
		$bwfd_number  = (int) $bwfd_m[1];
		$bwfd_title   = trim( $bwfd_m[2] );
		$bwfd_summary = $bwfd_parts[1] ?? '';
	} else {
		WP_CLI::warning( "Skipped (no chapter number): $bwfd_line" );
		continue;
	}
	if ( $bwfd_number <= 0 || '' === $bwfd_title ) {
		WP_CLI::warning( "Skipped: $bwfd_line" );
		continue;
	}

	$bwfd_postarr = array(
		'post_type'    => 'bwfd_chapter',
		'post_title'   => $bwfd_title,
		'post_excerpt' => $bwfd_summary,
		'menu_order'   => $bwfd_number,
	);
	if ( isset( $bwfd_existing[ $bwfd_number ] ) ) {
		$bwfd_postarr['ID'] = $bwfd_existing[ $bwfd_number ]->ID;
		if ( '' === $bwfd_summary ) {
			unset( $bwfd_postarr['post_excerpt'] );
		}
		$bwfd_id = wp_update_post( wp_slash( $bwfd_postarr ), true );
		$bwfd_updated++;
	} else {
		$bwfd_postarr['post_status'] = $bwfd_status;
		$bwfd_id = wp_insert_post( wp_slash( $bwfd_postarr ), true );
		$bwfd_created++;
	}
	if ( is_wp_error( $bwfd_id ) ) {
		WP_CLI::warning( "Chapter $bwfd_number: " . $bwfd_id->get_error_message() );
		continue;
	}
	WP_CLI::log( sprintf( 'Chapter %d: %s (#%d, %s)', $bwfd_number, $bwfd_title, $bwfd_id, get_post_status( $bwfd_id ) ) );
}
WP_CLI::success( "$bwfd_created created, $bwfd_updated updated." );
