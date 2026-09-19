<?php
/**
 * Title: Latest news
 * Slug: bwfd/news-latest
 * Categories: bwfd-sections
 * Description: The three most recent news items as a ruled list of date and title, with a link to the full news archive. Used on the Churches & retail page under the media kit.
 *
 * @package bwfd
 */

declare( strict_types=1 );
?>
<!-- wp:group {"className":"bwfd-section","style":{"spacing":{"padding":{"top":"70px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group bwfd-section" style="padding-top:70px">
	<!-- wp:bwfd/section-heading {"content":"Latest news","style":{"spacing":{"margin":{"bottom":"24px"}}}} -->
	<div class="wp-block-bwfd-section-heading bwfd-section-heading has-text-align-left" style="margin-bottom:24px"><h2 class="bwfd-section-heading__title has-xxl-font-size"><?php esc_html_e( 'Latest news', 'bwfd' ); ?></h2><span class="bwfd-section-heading__rule" aria-hidden="true"></span></div>
	<!-- /wp:bwfd/section-heading -->

	<!-- wp:paragraph {"className":"bwfd-measure-narrow","fontSize":"lg","style":{"spacing":{"margin":{"bottom":"28px"}}}} -->
	<p class="bwfd-measure-narrow has-lg-font-size" style="margin-bottom:28px"><?php esc_html_e( 'Announcements and press releases for churches, retailers and media.', 'bwfd' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:query {"queryId":11,"query":{"perPage":3,"pages":0,"offset":0,"postType":"bwfd_news","order":"desc","orderBy":"date","inherit":false},"className":"bwfd-rows"} -->
	<div class="wp-block-query bwfd-rows">
		<!-- wp:post-template {"layout":{"type":"default"}} -->
			<!-- wp:group {"className":"bwfd-rows__item","style":{"spacing":{"blockGap":"8px 30px"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"top"}} -->
			<div class="wp-block-group bwfd-rows__item">
				<!-- wp:post-date {"className":"bwfd-rows__date"} /-->
				<!-- wp:post-title {"isLink":true,"level":3,"className":"bwfd-rows__title"} /-->
			</div>
			<!-- /wp:group -->
		<!-- /wp:post-template -->

		<!-- wp:query-no-results -->
			<!-- wp:paragraph {"className":"bwfd-empty-note"} -->
			<p class="bwfd-empty-note"><?php esc_html_e( 'No news has been published yet.', 'bwfd' ); ?></p>
			<!-- /wp:paragraph -->
		<!-- /wp:query-no-results -->
	</div>
	<!-- /wp:query -->

	<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"26px"}}}} -->
	<div class="wp-block-buttons" style="margin-top:26px">
		<!-- wp:button {"className":"is-style-bwfd-text-link"} -->
		<div class="wp-block-button is-style-bwfd-text-link"><a class="wp-block-button__link wp-element-button" href="<?php echo bwfd_url( '/news/' ); ?>"><?php esc_html_e( 'All news', 'bwfd' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
