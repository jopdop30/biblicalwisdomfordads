<?php
/**
 * Block style variations for core blocks. CSS lives in style.css.
 *
 * @package bwfd
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Register block styles.
 */
function bwfd_register_block_styles(): void {
	$styles = array(
		'core/group'  => array(
			'bwfd-navy'    => __( 'Navy texture', 'bwfd' ),
			'bwfd-apricot' => __( 'Apricot texture', 'bwfd' ),
			'bwfd-marble'  => __( 'Marble', 'bwfd' ),
		),
		'core/button' => array(
			'bwfd-apricot'   => __( 'Apricot', 'bwfd' ),
			'bwfd-text-link' => __( 'Text link', 'bwfd' ),
			'bwfd-cta'       => __( 'Large call to action', 'bwfd' ),
			'bwfd-compact'   => __( 'Compact', 'bwfd' ),
			'bwfd-social'    => __( 'Social', 'bwfd' ),
		),
		'core/table'  => array(
			'bwfd-key-info' => __( 'Key information', 'bwfd' ),
			'bwfd-pricing'  => __( 'Pricing', 'bwfd' ),
		),
	);

	foreach ( $styles as $block => $variations ) {
		foreach ( $variations as $name => $label ) {
			register_block_style(
				$block,
				array(
					'name'  => $name,
					'label' => $label,
				)
			);
		}
	}
}
add_action( 'init', 'bwfd_register_block_styles' );
