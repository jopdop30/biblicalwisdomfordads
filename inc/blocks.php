<?php
/**
 * Register the theme's custom blocks from the build directory.
 *
 * @package bwfd
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Register all blocks found in /build/blocks.
 *
 * Uses the blocks manifest when available (WordPress 6.8+), falling back to
 * per-directory registration for older installs.
 */
function bwfd_register_blocks(): void {
	$build_dir = BWFD_DIR . '/build/blocks';
	$manifest  = BWFD_DIR . '/build/blocks-manifest.php';

	if ( ! is_dir( $build_dir ) ) {
		return;
	}

	if ( function_exists( 'wp_register_block_types_from_metadata_collection' ) && file_exists( $manifest ) ) {
		wp_register_block_types_from_metadata_collection( $build_dir, $manifest );
		return;
	}

	foreach ( glob( $build_dir . '/*/block.json' ) ?: array() as $block_json ) {
		register_block_type( dirname( $block_json ) );
	}
}
add_action( 'init', 'bwfd_register_blocks' );

/**
 * Custom block category so the theme's blocks sit together in the inserter.
 *
 * @param array $categories Existing categories.
 * @return array
 */
function bwfd_block_category( array $categories ): array {
	array_unshift(
		$categories,
		array(
			'slug'  => 'bwfd',
			'title' => __( 'Biblical Wisdom for Dads', 'bwfd' ),
			'icon'  => null,
		)
	);
	return $categories;
}
add_filter( 'block_categories_all', 'bwfd_block_category' );

/**
 * Expose the icon library to the editor scripts.
 */
function bwfd_editor_icon_data(): void {
	wp_add_inline_script(
		'bwfd-icon-editor-script',
		'window.bwfdIcons = ' . wp_json_encode( bwfd_icon_paths() ) . ';',
		'before'
	);
}
add_action( 'enqueue_block_editor_assets', 'bwfd_editor_icon_data' );
