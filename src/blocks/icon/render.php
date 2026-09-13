<?php
/**
 * Icon block – server render so the SVG library lives in one place.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner content (unused).
 * @var WP_Block $block      Block instance.
 *
 * @package bwfd
 */

declare( strict_types=1 );

$bwfd_icon    = sanitize_key( $attributes['icon'] ?? 'compass' );
$bwfd_variant = ( $attributes['variant'] ?? 'plain' ) === 'circle' ? 'circle' : 'plain';
$bwfd_size    = max( 12, min( 160, (int) ( $attributes['size'] ?? 34 ) ) );
$bwfd_draw    = 'circle' === $bwfd_variant ? (int) round( $bwfd_size * 0.48 ) : $bwfd_size;

$bwfd_wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'bwfd-icon--' . $bwfd_variant,
		'style' => sprintf( '--bwfd-icon-size:%dpx', $bwfd_size ),
	)
);
?>
<span <?php echo $bwfd_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?>><?php echo bwfd_icon_svg( $bwfd_icon, $bwfd_draw ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG library. ?></span>
