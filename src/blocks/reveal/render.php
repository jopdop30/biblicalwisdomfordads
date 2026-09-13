<?php
/**
 * Reveal panel – server render with Interactivity API directives.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Rendered inner blocks.
 * @var WP_Block $block      Block instance.
 *
 * @package bwfd
 */

declare( strict_types=1 );

$bwfd_open_label  = $attributes['openLabel'] ?: __( 'Key information', 'bwfd' );
$bwfd_close_label = $attributes['closeLabel'] ?: __( 'Hide key information', 'bwfd' );
$bwfd_open        = ! empty( $attributes['defaultOpen'] );
$bwfd_panel_id    = wp_unique_id( 'bwfd-reveal-' );

$bwfd_wrapper = get_block_wrapper_attributes(
	array(
		'class'               => 'bwfd-reveal',
		'data-wp-interactive' => 'bwfd/reveal',
		'data-wp-context'     => wp_json_encode(
			array(
				'open'       => $bwfd_open,
				'openLabel'  => $bwfd_open_label,
				'closeLabel' => $bwfd_close_label,
			)
		),
	)
);
?>
<div <?php echo $bwfd_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?>>
	<button
		type="button"
		class="bwfd-reveal__toggle"
		aria-controls="<?php echo esc_attr( $bwfd_panel_id ); ?>"
		aria-expanded="<?php echo $bwfd_open ? 'true' : 'false'; ?>"
		data-wp-on--click="actions.toggle"
		data-wp-bind--aria-expanded="context.open"
		data-wp-text="state.label"
	><?php echo esc_html( $bwfd_open ? $bwfd_close_label : $bwfd_open_label ); ?></button>
	<div
		id="<?php echo esc_attr( $bwfd_panel_id ); ?>"
		class="bwfd-reveal__panel"
		data-wp-bind--hidden="!context.open"
		<?php echo $bwfd_open ? '' : 'hidden'; ?>
	>
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inner block output. ?>
	</div>
</div>
