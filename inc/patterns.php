<?php
/**
 * Pattern categories. Pattern files live in /patterns and are auto-registered.
 *
 * @package bwfd
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Register pattern categories.
 */
function bwfd_register_pattern_categories(): void {
	register_block_pattern_category(
		'bwfd-pages',
		array(
			'label'       => __( 'BWFD pages', 'bwfd' ),
			'description' => __( 'Complete page layouts from the Biblical Wisdom for Dads design.', 'bwfd' ),
		)
	);
	register_block_pattern_category(
		'bwfd-sections',
		array(
			'label'       => __( 'BWFD sections', 'bwfd' ),
			'description' => __( 'Reusable sections: calls to action, card rows, endorsements and more.', 'bwfd' ),
		)
	);
}
add_action( 'init', 'bwfd_register_pattern_categories' );

/**
 * Remove the core and remote patterns so editors only see the design system.
 */
function bwfd_trim_patterns(): void {
	remove_theme_support( 'core-block-patterns' );
}
add_action( 'after_setup_theme', 'bwfd_trim_patterns' );
add_filter( 'should_load_remote_block_patterns', '__return_false' );
