<?php
/**
 * Facebook Page Plugin embed, loaded on request.
 *
 * The plugin iframe weighs over half a megabyte and sets cookies, so the
 * page renders a small placeholder and view.js swaps in the iframe when the
 * visitor asks for it. Without JavaScript the link to the page remains.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner content (unused).
 * @var WP_Block $block      Block instance.
 *
 * @package bwfd
 */

declare( strict_types=1 );

$bwfd_url = esc_url_raw( (string) ( $attributes['url'] ?? '' ) );
if ( ! preg_match( '#^https://(www\.)?facebook\.com/.+#i', $bwfd_url ) ) {
	return;
}

$bwfd_height = max( 130, min( 1200, (int) ( $attributes['height'] ?? 600 ) ) );
$bwfd_tabs   = preg_replace( '/[^a-z,]/', '', (string) ( $attributes['tabs'] ?? 'timeline' ) );
$bwfd_title  = (string) ( $attributes['title'] ?? '' ) ?: __( 'Facebook page', 'bwfd' );

$bwfd_src = add_query_arg(
	array(
		'href'                  => rawurlencode( $bwfd_url ),
		'tabs'                  => $bwfd_tabs,
		'width'                 => 500,
		'height'                => $bwfd_height,
		'small_header'          => ! empty( $attributes['smallHeader'] ) ? 'true' : 'false',
		'adapt_container_width' => 'true',
		'hide_cover'            => ! empty( $attributes['hideCover'] ) ? 'true' : 'false',
		'show_facepile'         => ! isset( $attributes['showFacepile'] ) || $attributes['showFacepile'] ? 'true' : 'false',
	),
	'https://www.facebook.com/plugins/page.php'
);

$bwfd_wrapper = get_block_wrapper_attributes(
	array(
		'class'       => 'bwfd-facebook-page',
		'data-src'    => $bwfd_src,
		'data-title'  => $bwfd_title,
		'data-height' => (string) $bwfd_height,
	)
);
?>
<div <?php echo $bwfd_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?>>
	<div class="bwfd-facebook-page__placeholder">
		<?php echo bwfd_icon_svg( 'facebook', 36, array( 'class' => 'bwfd-facebook-page__icon', 'aria-hidden' => 'true' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG built from the theme's own icon paths. ?>
		<p class="bwfd-facebook-page__title"><?php echo esc_html( $bwfd_title ); ?></p>
		<p class="bwfd-facebook-page__note"><?php esc_html_e( 'The latest posts load from Facebook when you ask for them.', 'bwfd' ); ?></p>
		<button type="button" class="bwfd-facebook-page__load wp-element-button"><?php esc_html_e( 'Show Facebook posts', 'bwfd' ); ?></button>
	</div>
	<p class="bwfd-facebook-page__fallback"><a href="<?php echo esc_url( $bwfd_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Visit the page on Facebook', 'bwfd' ); ?></a></p>
</div>
