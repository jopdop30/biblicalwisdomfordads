<?php
/**
 * Provision the site content from the theme's page patterns.
 *
 * Run with WP-CLI from the WordPress root:
 *
 *   wp eval-file wp-content/themes/bwfd/bin/import-pages.php
 *
 * Pass page slugs to limit the run, e.g. `... import-pages.php home purchase`
 * (existing edits on other pages are then left untouched).
 *
 * It is idempotent: pages are matched by slug and updated in place, images
 * are imported into the Media Library once, the primary navigation menu is
 * written, and the home page is set as the front page.
 *
 * @package bwfd
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit( "Run this file with: wp eval-file bin/import-pages.php\n" );
}

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$bwfd_pages = array(
	// slug => [ title, pattern slug, template, featured image (theme file, optional) ].
	'home'                => array( 'Home', 'bwfd/page-home', '' ),
	'about-the-book'      => array( 'About the book', 'bwfd/page-about-book', '' ),
	'about-the-author'    => array( 'About the author', 'bwfd/page-about-author', '' ),
	'purchase'            => array( 'Purchase', 'bwfd/page-purchase', '' ),
	'small-group-guide'   => array( 'Small Group Guide', 'bwfd/page-small-group-guide', '' ),
	'other-books'         => array( 'Other books', 'bwfd/page-other-books', '' ),
	'enjoyed'             => array( 'Enjoyed?', 'bwfd/page-enjoyed', '' ),
	'churches-and-retail' => array( 'Churches & retail', 'bwfd/page-churches-retail', '' ),
	'launch'              => array( 'Launch event', 'bwfd/page-launch', '', 'bwfd-cover.webp' ),
	'privacy-policy'      => array( 'Privacy policy', 'bwfd/page-privacy-policy', 'page-with-title' ),
);

$bwfd_nav = array(
	array( 'About the book', 'about-the-book', '' ),
	array( 'Launch event', 'launch', '' ),
	array( 'About the author', 'about-the-author', '' ),
	array( 'Small Group Guide', 'small-group-guide', '' ),
	array( 'Other books', 'other-books', '' ),
	array( 'Enjoyed?', 'enjoyed', '' ),
	array( 'Buy the book', 'purchase', 'bwfd-nav-button' ),
);

/**
 * Import a theme image into the Media Library once; return its URL.
 */
function bwfd_import_theme_image( string $file ): string {
	$existing = get_posts(
		array(
			'post_type'   => 'attachment',
			'meta_key'    => '_bwfd_source',
			'meta_value'  => $file,
			'numberposts' => 1,
			'fields'      => 'ids',
		)
	);
	if ( $existing ) {
		return wp_get_attachment_url( (int) $existing[0] );
	}

	$path = BWFD_DIR . '/assets/images/' . $file;
	if ( ! file_exists( $path ) ) {
		WP_CLI::warning( "Missing image: $file" );
		return '';
	}

	$tmp = wp_tempnam( $file );
	copy( $path, $tmp );
	$file_array = array(
		'name'     => $file,
		'tmp_name' => $tmp,
	);
	$id         = media_handle_sideload( $file_array, 0, pathinfo( $file, PATHINFO_FILENAME ) );
	if ( is_wp_error( $id ) ) {
		WP_CLI::warning( "Could not import $file: " . $id->get_error_message() );
		return '';
	}
	update_post_meta( $id, '_bwfd_source', $file );
	WP_CLI::log( "Imported $file as attachment $id" );
	return wp_get_attachment_url( $id );
}

$bwfd_only = array_values( array_filter( array_map( 'sanitize_title', (array) ( $args ?? array() ) ) ) );
if ( $bwfd_only ) {
	$bwfd_pages = array_intersect_key( $bwfd_pages, array_flip( $bwfd_only ) );
	if ( ! $bwfd_pages ) {
		WP_CLI::error( 'No matching page slugs: ' . implode( ', ', $bwfd_only ) );
	}
}

$bwfd_registry = WP_Block_Patterns_Registry::get_instance();

/**
 * Replace <!-- wp:pattern {"slug":"…"} /--> references with the pattern's
 * own blocks so the saved page holds literal, editable content.
 */
function bwfd_inline_patterns( string $content, WP_Block_Patterns_Registry $registry, int $depth = 0 ): string {
	if ( $depth > 5 ) {
		return $content;
	}
	return (string) preg_replace_callback(
		'/<!--\s+wp:pattern\s+(\{.*?\})\s+\/-->/s',
		static function ( array $m ) use ( $registry, $depth ): string {
			$attrs   = json_decode( $m[1], true );
			$pattern = is_array( $attrs ) && ! empty( $attrs['slug'] ) ? $registry->get_registered( $attrs['slug'] ) : null;
			if ( ! $pattern ) {
				return $m[0];
			}
			return bwfd_inline_patterns( $pattern['content'], $registry, $depth + 1 );
		},
		$content
	);
}
$bwfd_images   = array();
$bwfd_ids      = array();

foreach ( $bwfd_pages as $bwfd_slug => list( $bwfd_title, $bwfd_pattern, $bwfd_template ) ) {
	$bwfd_registered = $bwfd_registry->get_registered( $bwfd_pattern );
	if ( ! $bwfd_registered ) {
		WP_CLI::warning( "Pattern $bwfd_pattern not registered – is the bwfd theme active?" );
		continue;
	}
	$bwfd_content = bwfd_inline_patterns( $bwfd_registered['content'], $bwfd_registry );

	// Swap theme asset URLs for Media Library copies so editors can manage them.
	$bwfd_base = BWFD_URI . '/assets/images/';
	if ( preg_match_all( '#' . preg_quote( $bwfd_base, '#' ) . '([\w.\-]+)#', $bwfd_content, $bwfd_matches ) ) {
		foreach ( array_unique( $bwfd_matches[1] ) as $bwfd_file ) {
			if ( ! isset( $bwfd_images[ $bwfd_file ] ) ) {
				$bwfd_images[ $bwfd_file ] = bwfd_import_theme_image( $bwfd_file );
			}
			if ( $bwfd_images[ $bwfd_file ] ) {
				$bwfd_content = str_replace( $bwfd_base . $bwfd_file, $bwfd_images[ $bwfd_file ], $bwfd_content );
			}
		}
	}

	$bwfd_existing = get_page_by_path( $bwfd_slug, OBJECT, 'page' );
	$bwfd_postarr  = array(
		'post_title'   => $bwfd_title,
		'post_name'    => $bwfd_slug,
		'post_content' => $bwfd_content,
		'post_status'  => 'publish',
		'post_type'    => 'page',
	);
	if ( $bwfd_existing ) {
		$bwfd_postarr['ID'] = $bwfd_existing->ID;
		$bwfd_id            = wp_update_post( wp_slash( $bwfd_postarr ), true );
	} else {
		$bwfd_id = wp_insert_post( wp_slash( $bwfd_postarr ), true );
	}
	if ( is_wp_error( $bwfd_id ) ) {
		WP_CLI::warning( "Could not save $bwfd_slug: " . $bwfd_id->get_error_message() );
		continue;
	}
	if ( $bwfd_template ) {
		update_post_meta( $bwfd_id, '_wp_page_template', $bwfd_template );
	}
	// Featured image (the social share image), only when the page has none yet.
	$bwfd_thumb = $bwfd_pages[ $bwfd_slug ][3] ?? '';
	if ( '' !== $bwfd_thumb && ! has_post_thumbnail( (int) $bwfd_id ) ) {
		if ( ! isset( $bwfd_images[ $bwfd_thumb ] ) ) {
			$bwfd_images[ $bwfd_thumb ] = bwfd_import_theme_image( $bwfd_thumb );
		}
		$bwfd_thumb_id = $bwfd_images[ $bwfd_thumb ] ? bwfd_perf_attachment_id( $bwfd_images[ $bwfd_thumb ] ) : 0;
		if ( $bwfd_thumb_id ) {
			set_post_thumbnail( (int) $bwfd_id, $bwfd_thumb_id );
		}
	}
	$bwfd_ids[ $bwfd_slug ] = (int) $bwfd_id;
	WP_CLI::log( sprintf( '%s page "%s" (#%d)', $bwfd_existing ? 'Updated' : 'Created', $bwfd_title, $bwfd_id ) );
}

// Site title, unless it has already been customised.
if ( in_array( get_option( 'blogname' ), array( 'biblicalwisdomfordads', 'My WordPress' ), true ) ) {
	update_option( 'blogname', 'Biblical Wisdom for Dads' );
	update_option( 'blogdescription', 'By Stephen Parker. Foreword by Richard Blackaby.' );
	WP_CLI::log( 'Set the site title and tagline.' );
}

// Privacy policy page (WordPress links it from the login screen).
if ( isset( $bwfd_ids['privacy-policy'] ) && (int) get_option( 'wp_page_for_privacy_policy' ) !== $bwfd_ids['privacy-policy'] ) {
	update_option( 'wp_page_for_privacy_policy', $bwfd_ids['privacy-policy'] );
	WP_CLI::log( 'Set the privacy policy page.' );
}

// Front page.
if ( isset( $bwfd_ids['home'] ) && 'page' !== get_option( 'show_on_front' ) ) {
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $bwfd_ids['home'] );
	WP_CLI::log( 'Set Home as the static front page.' );
}

// Primary navigation (wp_navigation post used by the header's Navigation block).

/**
 * Navigation link block markup for a page.
 */
function bwfd_nav_link_block( string $label, int $page_id, string $class ): string {
	$attrs = array(
		'label' => $label,
		'type'  => 'page',
		'id'    => $page_id,
		'url'   => get_permalink( $page_id ),
		'kind'  => 'post-type',
	);
	if ( $class ) {
		$attrs['className'] = $class;
	}
	return '<!-- wp:navigation-link ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES ) . ' /-->' . "\n";
}

$bwfd_menus = get_posts(
	array(
		'post_type'   => 'wp_navigation',
		'post_status' => 'publish',
		'numberposts' => 1,
		'orderby'     => 'date',
		'order'       => 'DESC',
	)
);

if ( $bwfd_only ) {
	// Partial run: leave the menu as the editors have it, but slot in any
	// item for the pages just imported that is missing, after the nearest
	// preceding item of the designed order that is present.
	$bwfd_added = array();
	if ( $bwfd_menus ) {
		$bwfd_blocks  = parse_blocks( $bwfd_menus[0]->post_content );
		$bwfd_menu_id = static function ( array $block ): int {
			return 'core/navigation-link' === ( $block['blockName'] ?? '' ) ? (int) ( $block['attrs']['id'] ?? 0 ) : 0;
		};
		foreach ( $bwfd_nav as $bwfd_index => list( $bwfd_label, $bwfd_slug, $bwfd_class ) ) {
			if ( ! isset( $bwfd_ids[ $bwfd_slug ] ) ) {
				continue;
			}
			if ( in_array( $bwfd_ids[ $bwfd_slug ], array_map( $bwfd_menu_id, $bwfd_blocks ), true ) ) {
				continue;
			}
			$bwfd_position = 0;
			for ( $bwfd_prev = $bwfd_index - 1; $bwfd_prev >= 0; $bwfd_prev-- ) {
				$bwfd_prev_page = get_page_by_path( $bwfd_nav[ $bwfd_prev ][1], OBJECT, 'page' );
				if ( ! $bwfd_prev_page ) {
					continue;
				}
				$bwfd_found = array_search( (int) $bwfd_prev_page->ID, array_map( $bwfd_menu_id, $bwfd_blocks ), true );
				if ( false !== $bwfd_found ) {
					$bwfd_position = (int) $bwfd_found + 1;
					break;
				}
			}
			$bwfd_new = parse_blocks( bwfd_nav_link_block( $bwfd_label, $bwfd_ids[ $bwfd_slug ], $bwfd_class ) );
			array_splice( $bwfd_blocks, $bwfd_position, 0, array( $bwfd_new[0] ) );
			$bwfd_added[] = $bwfd_label;
		}
		if ( $bwfd_added ) {
			wp_update_post(
				wp_slash(
					array(
						'ID'           => $bwfd_menus[0]->ID,
						'post_content' => serialize_blocks( $bwfd_blocks ),
					)
				)
			);
			WP_CLI::log( 'Added to navigation menu #' . $bwfd_menus[0]->ID . ': ' . implode( ', ', $bwfd_added ) );
		}
	}
	WP_CLI::success( 'Updated: ' . implode( ', ', array_keys( $bwfd_ids ) ) . ( $bwfd_added ? '.' : '. Navigation left unchanged for a partial run.' ) );
	return;
}

$bwfd_links = '';
foreach ( $bwfd_nav as list( $bwfd_label, $bwfd_slug, $bwfd_class ) ) {
	if ( isset( $bwfd_ids[ $bwfd_slug ] ) ) {
		$bwfd_links .= bwfd_nav_link_block( $bwfd_label, $bwfd_ids[ $bwfd_slug ], $bwfd_class );
	}
}
$bwfd_menu_arr = array(
	'post_type'    => 'wp_navigation',
	'post_status'  => 'publish',
	'post_title'   => 'Primary navigation',
	'post_name'    => 'primary-navigation',
	'post_content' => $bwfd_links,
);
if ( $bwfd_menus ) {
	$bwfd_menu_arr['ID'] = $bwfd_menus[0]->ID;
	wp_update_post( wp_slash( $bwfd_menu_arr ) );
	WP_CLI::log( 'Updated navigation menu #' . $bwfd_menus[0]->ID );
} else {
	$bwfd_menu_id = wp_insert_post( wp_slash( $bwfd_menu_arr ) );
	WP_CLI::log( 'Created navigation menu #' . $bwfd_menu_id );
}

// Site icon from the theme's icon set, unless one is already chosen.
if ( ! get_option( 'site_icon' ) ) {
	$bwfd_icon_file = BWFD_DIR . '/assets/icons/icon-512.png';
	if ( file_exists( $bwfd_icon_file ) ) {
		$bwfd_tmp = wp_tempnam( 'bwfd-site-icon.png' );
		copy( $bwfd_icon_file, $bwfd_tmp );
		// Favicons stay PNG: skip the theme's WebP conversion for this upload.
		$bwfd_site_icon = new WP_Site_Icon();
		add_filter( 'intermediate_image_sizes_advanced', array( $bwfd_site_icon, 'additional_sizes' ) );
		remove_filter( 'image_editor_output_format', 'bwfd_seo_webp_uploads' );
		$bwfd_icon_id = media_handle_sideload(
			array(
				'name'     => 'bwfd-site-icon.png',
				'tmp_name' => $bwfd_tmp,
			),
			0,
			'Biblical Wisdom for Dads site icon'
		);
		add_filter( 'image_editor_output_format', 'bwfd_seo_webp_uploads' );
		remove_filter( 'intermediate_image_sizes_advanced', array( $bwfd_site_icon, 'additional_sizes' ) );
		if ( ! is_wp_error( $bwfd_icon_id ) ) {
			update_post_meta( $bwfd_icon_id, '_wp_attachment_context', 'site-icon' );
			update_option( 'site_icon', $bwfd_icon_id );
			WP_CLI::log( 'Set the site icon.' );
		}
	}
}

// Permalinks: pretty URLs are assumed by the navigation and footer links.
if ( ! get_option( 'permalink_structure' ) ) {
	update_option( 'permalink_structure', '/%postname%/' );
	flush_rewrite_rules();
	WP_CLI::log( 'Set permalinks to /%postname%/.' );
}

// Tidy the default sample page.
$bwfd_sample = get_page_by_path( 'sample-page', OBJECT, 'page' );
if ( $bwfd_sample && 'publish' === $bwfd_sample->post_status ) {
	wp_update_post( array( 'ID' => $bwfd_sample->ID, 'post_status' => 'draft' ) );
	WP_CLI::log( 'Sample Page moved to draft.' );
}

WP_CLI::success( 'Site content provisioned from the Biblical Wisdom for Dads patterns.' );
