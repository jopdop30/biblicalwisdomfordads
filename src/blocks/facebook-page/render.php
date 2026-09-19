<?php
/**
 * Facebook Page Plugin embed, inserted as the block scrolls into reach.
 *
 * The plugin iframe weighs over half a megabyte. Rather than let the
 * browser decide when a lazy iframe loads (Chrome starts several screens
 * early), the block reserves the iframe's height and view.js inserts the
 * iframe once the block is within a screen of the viewport. Without
 * JavaScript the iframe is inside <noscript> and loads as before.
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

// Facebook draws the plugin at this width (180–500) whatever size the
// iframe is given, so view.js rewrites it to the block's measured width
// before inserting the iframe. 500 remains for the <noscript> copy.
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
$bwfd_iframe  = sprintf(
	'<iframe src="%s" title="%s" height="%d" style="border:none;overflow:hidden" scrolling="no" loading="lazy" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>',
	esc_url( $bwfd_src ),
	esc_attr( $bwfd_title ),
	(int) $bwfd_height
);
?>
<div <?php echo $bwfd_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?>>
	<div class="bwfd-facebook-page__frame" style="min-height:<?php echo (int) $bwfd_height; ?>px" aria-hidden="true"></div>
	<noscript><?php echo $bwfd_iframe; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built above from escaped values. ?></noscript>
	<p class="bwfd-facebook-page__fallback"><a href="<?php echo esc_url( $bwfd_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Visit the page on Facebook', 'bwfd' ); ?></a></p>
</div>
