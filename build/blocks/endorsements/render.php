<?php
/**
 * Endorsements carousel – server render with Interactivity API directives.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Rendered inner blocks (unused – slides are rendered individually).
 * @var WP_Block $block      Block instance.
 *
 * @package bwfd
 */

declare( strict_types=1 );

$bwfd_slides = array_values(
	array_filter(
		$block->parsed_block['innerBlocks'] ?? array(),
		static fn( array $inner ): bool => 'bwfd/endorsement' === ( $inner['blockName'] ?? '' )
	)
);
$bwfd_count = count( $bwfd_slides );

if ( 0 === $bwfd_count ) {
	return;
}

$bwfd_interval   = max( 2000, (int) ( $attributes['interval'] ?? 8000 ) );
$bwfd_autoplay   = ! empty( $attributes['autoplay'] ) && $bwfd_count > 1;
$bwfd_navigation = ( $attributes['navigation'] ?? 'dots' ) === 'count' ? 'count' : 'dots';
$bwfd_min_height = $attributes['minHeight'] ?? '';
$bwfd_min_height = preg_match( '/^\d+(px|em|rem|vh)$/', (string) $bwfd_min_height ) ? $bwfd_min_height : '300px';

$bwfd_context = array(
	'index'    => 0,
	'count'    => $bwfd_count,
	'interval' => $bwfd_interval,
	'autoplay' => $bwfd_autoplay,
	'paused'   => false,
	'hovering' => false,
	'running'  => false,
);

$bwfd_wrapper = get_block_wrapper_attributes(
	array(
		'class'                  => 'bwfd-endorsements bwfd-endorsements--nav-' . $bwfd_navigation,
		'style'                  => sprintf( '--bwfd-interval:%dms;--bwfd-min-height:%s', $bwfd_interval, $bwfd_min_height ),
		'data-wp-interactive'    => 'bwfd/endorsements',
		'data-wp-context'        => wp_json_encode( $bwfd_context ),
		'data-wp-init'           => 'callbacks.init',
		'data-wp-on--mouseenter' => 'actions.hoverIn',
		'data-wp-on--mouseleave' => 'actions.hoverOut',
		'data-wp-on--focusin'    => 'actions.hoverIn',
		'data-wp-on--focusout'   => 'actions.hoverOut',
		'data-wp-on--keydown'    => 'actions.onKeydown',
		'role'                   => 'region',
		'aria-roledescription'   => esc_attr__( 'carousel', 'bwfd' ),
		'aria-label'             => esc_attr__( 'Endorsements', 'bwfd' ),
	)
);
?>
<div <?php echo $bwfd_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?>>
	<?php if ( $bwfd_autoplay ) : ?>
		<div class="bwfd-endorsements__progress" aria-hidden="true">
			<div class="bwfd-endorsements__bar" data-wp-class--is-running="context.running" data-wp-class--is-paused="state.isPaused"></div>
		</div>
	<?php endif; ?>
	<div class="bwfd-endorsements__frame" aria-hidden="true"></div>
	<div class="bwfd-endorsements__inner">
		<div class="bwfd-endorsements__slides" aria-live="polite" aria-atomic="false">
			<?php foreach ( $bwfd_slides as $bwfd_i => $bwfd_slide ) : ?>
				<div
					class="bwfd-endorsements__slide<?php echo 0 === $bwfd_i ? ' is-active' : ''; ?>"
					role="group"
					aria-roledescription="<?php esc_attr_e( 'slide', 'bwfd' ); ?>"
					<?php /* translators: 1: slide number, 2: total slides */ ?>
					aria-label="<?php echo esc_attr( sprintf( __( '%1$d of %2$d', 'bwfd' ), $bwfd_i + 1, $bwfd_count ) ); ?>"
					data-wp-context='<?php echo wp_json_encode( array( 'i' => $bwfd_i ) ); ?>'
					data-wp-class--is-active="state.isActive"
					data-wp-bind--aria-hidden="!state.isActive"
					<?php echo 0 === $bwfd_i ? '' : 'aria-hidden="true"'; ?>
				>
					<?php echo render_block( $bwfd_slide ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block output. ?>
				</div>
			<?php endforeach; ?>
		</div>

		<?php if ( $bwfd_count > 1 ) : ?>
			<div class="bwfd-endorsements__controls">
				<button type="button" class="bwfd-endorsements__btn" data-wp-on--click="actions.prev" aria-label="<?php esc_attr_e( 'Previous endorsement', 'bwfd' ); ?>">&#8592;</button>

				<?php if ( 'dots' === $bwfd_navigation ) : ?>
					<div class="bwfd-endorsements__dots" role="tablist" aria-label="<?php esc_attr_e( 'Choose endorsement', 'bwfd' ); ?>">
						<?php for ( $bwfd_i = 0; $bwfd_i < $bwfd_count; $bwfd_i++ ) : ?>
							<button
								type="button"
								role="tab"
								class="bwfd-endorsements__dot<?php echo 0 === $bwfd_i ? ' is-active' : ''; ?>"
								data-wp-context='<?php echo wp_json_encode( array( 'i' => $bwfd_i ) ); ?>'
								data-wp-class--is-active="state.isActive"
								data-wp-bind--aria-selected="state.isActive"
								data-wp-on--click="actions.goTo"
								<?php /* translators: 1: slide number, 2: total slides */ ?>
								aria-label="<?php echo esc_attr( sprintf( __( 'Show endorsement %1$d of %2$d', 'bwfd' ), $bwfd_i + 1, $bwfd_count ) ); ?>"
							></button>
						<?php endfor; ?>
					</div>
				<?php else : ?>
					<span class="bwfd-endorsements__count" data-wp-text="state.countLabel"><?php echo esc_html( sprintf( '1 of %d', $bwfd_count ) ); ?></span>
				<?php endif; ?>

				<?php if ( $bwfd_autoplay ) : ?>
					<button
						type="button"
						class="bwfd-endorsements__btn bwfd-endorsements__btn--pause"
						data-wp-on--click="actions.togglePause"
						data-wp-bind--aria-label="state.pauseLabel"
						data-wp-bind--aria-pressed="context.paused"
						aria-label="<?php esc_attr_e( 'Pause endorsements', 'bwfd' ); ?>"
					><span data-wp-text="state.pauseGlyph">&#10073;&#10073;</span></button>
				<?php endif; ?>

				<button type="button" class="bwfd-endorsements__btn" data-wp-on--click="actions.next" aria-label="<?php esc_attr_e( 'Next endorsement', 'bwfd' ); ?>">&#8594;</button>
			</div>
		<?php endif; ?>
	</div>
</div>
