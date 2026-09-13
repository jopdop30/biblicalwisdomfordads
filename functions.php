<?php
/**
 * Biblical Wisdom for Dads – theme bootstrap.
 *
 * @package bwfd
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

define( 'BWFD_VERSION', wp_get_theme()->get( 'Version' ) ?: '1.0.0' );
define( 'BWFD_DIR', get_template_directory() );
define( 'BWFD_URI', get_template_directory_uri() );

require_once BWFD_DIR . '/inc/icons.php';
require_once BWFD_DIR . '/inc/blocks.php';
require_once BWFD_DIR . '/inc/block-styles.php';
require_once BWFD_DIR . '/inc/patterns.php';

/**
 * Theme supports and editor assets.
 */
function bwfd_setup(): void {
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style.css' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'style', 'script', 'search-form', 'gallery', 'caption' ) );

	load_theme_textdomain( 'bwfd', BWFD_DIR . '/languages' );
}
add_action( 'after_setup_theme', 'bwfd_setup' );

/**
 * Front-end stylesheet.
 */
function bwfd_enqueue_assets(): void {
	wp_enqueue_style(
		'bwfd-style',
		get_stylesheet_uri(),
		array(),
		BWFD_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'bwfd_enqueue_assets' );

/**
 * Preload the two variable fonts so the hero heading does not flash.
 */
function bwfd_preload_fonts(): void {
	$fonts = array( 'bitter-latin.woff2', 'source-sans-3-latin.woff2' );
	foreach ( $fonts as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( BWFD_URI . '/assets/fonts/' . $font )
		);
	}
}
add_action( 'wp_head', 'bwfd_preload_fonts', 1 );

/**
 * Helper: URL of a theme image, for patterns.
 */
function bwfd_image( string $file ): string {
	return esc_url( BWFD_URI . '/assets/images/' . ltrim( $file, '/' ) );
}

/**
 * Helper: internal page URL, for patterns.
 */
function bwfd_url( string $path = '/' ): string {
	return esc_url( home_url( $path ) );
}
