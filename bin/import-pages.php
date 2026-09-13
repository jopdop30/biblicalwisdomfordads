<?php
/**
 * Provision the site content from the theme's page patterns.
 *
 * Run with WP-CLI from the WordPress root:
 *
 *   wp eval-file wp-content/themes/bwfd/bin/import-pages.php
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
	// slug => [ title, pattern slug, template ].
	'home'                => array( 'Home', 'bwfd/page-home', '' ),
	'about-the-book'      => array( 'About the book', 'bwfd/page-about-book', '' ),
	'about-the-author'    => array( 'About the author', 'bwfd/page-about-author', '' ),
	'purchase'            => array( 'Purchase', 'bwfd/page-purchase', '' ),
	'small-group-guide'   => array( 'Small Group Guide', 'bwfd/page-small-group-guide', '' ),
	'other-books'         => array( 'Other books', 'bwfd/page-other-books', '' ),
	'enjoyed'             => array( 'Enjoyed?', 'bwfd/page-enjoyed', '' ),
	'churches-and-retail' => array( 'Churches & retail', 'bwfd/page-churches-retail', '' ),
);

$bwfd_nav = array(
	array( 'About the book', 'about-the-book', '' ),
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
	$bwfd_ids[ $bwfd_slug ] = (int) $bwfd_id;
	WP_CLI::log( sprintf( '%s page "%s" (#%d)', $bwfd_existing ? 'Updated' : 'Created', $bwfd_title, $bwfd_id ) );
}

// Site title, unless it has already been customised.
if ( in_array( get_option( 'blogname' ), array( 'biblicalwisdomfordads', 'My WordPress' ), true ) ) {
	update_option( 'blogname', 'Biblical Wisdom for Dads' );
	update_option( 'blogdescription', 'By Stephen Parker. Foreword by Richard Blackaby.' );
	WP_CLI::log( 'Set the site title and tagline.' );
}

// Front page.
if ( isset( $bwfd_ids['home'] ) ) {
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $bwfd_ids['home'] );
	WP_CLI::log( 'Set Home as the static front page.' );
}

// Primary navigation (wp_navigation post used by the header's Navigation block).
$bwfd_links = '';
foreach ( $bwfd_nav as list( $bwfd_label, $bwfd_slug, $bwfd_class ) ) {
	if ( ! isset( $bwfd_ids[ $bwfd_slug ] ) ) {
		continue;
	}
	$bwfd_attrs = array(
		'label' => $bwfd_label,
		'type'  => 'page',
		'id'    => $bwfd_ids[ $bwfd_slug ],
		'url'   => get_permalink( $bwfd_ids[ $bwfd_slug ] ),
		'kind'  => 'post-type',
	);
	if ( $bwfd_class ) {
		$bwfd_attrs['className'] = $bwfd_class;
	}
	$bwfd_links .= '<!-- wp:navigation-link ' . wp_json_encode( $bwfd_attrs, JSON_UNESCAPED_SLASHES ) . ' /-->' . "\n";
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

// Tidy the default sample page.
$bwfd_sample = get_page_by_path( 'sample-page', OBJECT, 'page' );
if ( $bwfd_sample && 'publish' === $bwfd_sample->post_status ) {
	wp_update_post( array( 'ID' => $bwfd_sample->ID, 'post_status' => 'draft' ) );
	WP_CLI::log( 'Sample Page moved to draft.' );
}

WP_CLI::success( 'Site content provisioned from the Biblical Wisdom for Dads patterns.' );
