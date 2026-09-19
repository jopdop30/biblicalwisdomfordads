<?php
/**
 * Title: From the Insights
 * Slug: bwfd/insight-latest
 * Categories: bwfd-sections
 * Description: The featured (else latest) Insight as the two-panel feature, with a heading and a link to all insights. Used on the home page.
 *
 * @package bwfd
 */

declare( strict_types=1 );
?>
<!-- wp:group {"className":"bwfd-section bwfd-home-insight","layout":{"type":"constrained"}} -->
<div class="wp-block-group bwfd-section bwfd-home-insight">
	<!-- wp:bwfd/section-heading {"content":"From the Insights","style":{"spacing":{"margin":{"bottom":"12px"}}}} -->
	<div class="wp-block-bwfd-section-heading bwfd-section-heading has-text-align-left" style="margin-bottom:12px"><h2 class="bwfd-section-heading__title has-xxl-font-size"><?php esc_html_e( 'From the Insights', 'bwfd' ); ?></h2><span class="bwfd-section-heading__rule" aria-hidden="true"></span></div>
	<!-- /wp:bwfd/section-heading -->

	<!-- wp:paragraph {"className":"bwfd-measure-narrow","fontSize":"lg","style":{"spacing":{"margin":{"bottom":"28px"}}}} -->
	<p class="bwfd-measure-narrow has-lg-font-size" style="margin-bottom:28px"><?php esc_html_e( 'Short reflections from Stephen on fatherhood and the wisdom found in Scripture. A few minutes each.', 'bwfd' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:bwfd/featured-insight {"kicker":"Latest reflection"} /-->

	<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"26px"}}}} -->
	<div class="wp-block-buttons" style="margin-top:26px">
		<!-- wp:button {"className":"is-style-bwfd-text-link"} -->
		<div class="wp-block-button is-style-bwfd-text-link"><a class="wp-block-button__link wp-element-button" href="<?php echo bwfd_url( '/insights/' ); ?>"><?php esc_html_e( 'All insights', 'bwfd' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
