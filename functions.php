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
require_once BWFD_DIR . '/inc/schema-settings.php';
require_once BWFD_DIR . '/inc/seo.php';
require_once BWFD_DIR . '/inc/performance.php';

/**
 * Theme supports and editor assets.
 */
function bwfd_setup(): void {
	add_theme_support( 'editor-styles' );
	add_editor_style( array( 'style.css', 'assets/css/editor.css' ) );
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
	$fonts = array( 'bitter-latin-2.woff2', 'source-sans-3-latin-2.woff2' );
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

/**
 * Texture image tokens as absolute URLs.
 *
 * In the editor they sit on :root so any block can use them. On the front
 * end each token is declared only on the selectors that paint it: Chrome
 * downloads url() values held in custom properties as soon as they are
 * computed, so a :root declaration fetched every texture on every page,
 * including the blue one that only the editor canvas uses.
 */
function bwfd_texture_tokens(): void {
	$textures = array(
		'navy'    => array( 'tex-navy.webp', '.is-style-bwfd-navy,.bwfd-card--navy,.bwfd-endorsements' ),
		'apricot' => array( 'tex-apricot.webp', '.is-style-bwfd-apricot,.bwfd-card--apricot,.bwfd-framed-image__panel,.wp-block-bwfd-icon.bwfd-icon--circle' ),
		'marble'  => array( 'tex-white.webp', '.is-style-bwfd-marble,.bwfd-card--marble' ),
		'blue'    => array( 'tex-blue.webp', '' ),
	);
	$css = '';
	if ( is_admin() ) {
		$css = ':root{';
		foreach ( $textures as $token => list( $file ) ) {
			$css .= sprintf( '--bwfd-tex-%s:url(%s);', $token, bwfd_image( $file ) );
		}
		$css .= '}';
	} else {
		foreach ( $textures as $token => list( $file, $selectors ) ) {
			if ( '' !== $selectors ) {
				$css .= sprintf( '%s{--bwfd-tex-%s:url(%s)}', $selectors, $token, bwfd_image( $file ) );
			}
		}
	}

	wp_register_style( 'bwfd-tokens', false, array(), BWFD_VERSION );
	wp_add_inline_style( 'bwfd-tokens', $css );
	wp_enqueue_style( 'bwfd-tokens' );
}
add_action( 'enqueue_block_assets', 'bwfd_texture_tokens' );

/**
 * Edit pages inside their template ("template-locked" rendering).
 *
 * The designed pages carry their own heading in the content, and the default
 * page template does not render the post title, so editing with the template
 * shown avoids a duplicate title field above the content. Templates that do
 * render the title (Page with title) still show it as an editable block.
 *
 * WordPress resolves the mode from, in order: the user's own toggle, the post
 * type's editor support `default-mode`, then the editor setting.
 */
function bwfd_page_rendering_mode_support(): void {
	$existing = get_all_post_type_supports( 'page' )['editor'] ?? true;
	$args     = is_array( $existing ) ? array_values( $existing ) : array();
	foreach ( $args as $arg ) {
		if ( is_array( $arg ) && isset( $arg['default-mode'] ) ) {
			return;
		}
	}
	$args[] = array( 'default-mode' => 'template-locked' );
	add_post_type_support( 'page', 'editor', ...$args );
}
add_action( 'init', 'bwfd_page_rendering_mode_support', 20 );

/**
 * Fallback for the same default via the editor settings.
 *
 * @param array                   $settings Editor settings.
 * @param WP_Block_Editor_Context $context  Editor context.
 * @return array
 */
function bwfd_page_rendering_mode_setting( array $settings, WP_Block_Editor_Context $context ): array {
	if ( isset( $context->post ) && 'page' === $context->post->post_type ) {
		$settings['defaultRenderingMode'] = 'template-locked';
	}
	return $settings;
}
add_filter( 'block_editor_settings_all', 'bwfd_page_rendering_mode_setting', 10, 2 );
