<?php
/**
 * Facebook Page Plugin embed.
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

$bwfd_wrapper = get_block_wrapper_attributes( array( 'class' => 'bwfd-facebook-page' ) );
?>
<div <?php echo $bwfd_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?>>
	<iframe
		src="<?php echo esc_url( $bwfd_src ); ?>"
		title="<?php echo esc_attr( $bwfd_title ); ?>"
		height="<?php echo (int) $bwfd_height; ?>"
		style="border:none;overflow:hidden"
		scrolling="no"
		loading="lazy"
		allowfullscreen="true"
		allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"
	></iframe>
	<p class="bwfd-facebook-page__fallback"><a href="<?php echo esc_url( $bwfd_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Visit the page on Facebook', 'bwfd' ); ?></a></p>
</div>
