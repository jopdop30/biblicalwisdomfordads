<?php
/**
 * Title: Purchase page
 * Slug: bwfd/page-purchase
 * Categories: bwfd-pages
 * Block Types: core/post-content
 * Post Types: page
 * Viewport Width: 1280
 * Description: Four purchase options (Amazon, direct, audio, bulk), supporting text and the cover with quick links.
 *
 * @package bwfd
 */

declare( strict_types=1 );

$bwfd_cover  = bwfd_image( 'bwfd-cover.jpg' );
$bwfd_amazon = bwfd_image( 'amazon-wordmark.webp' );
?>
<!-- wp:group {"className":"bwfd-section bwfd-section--first bwfd-section--last","layout":{"type":"constrained"}} -->
<div class="wp-block-group bwfd-section bwfd-section--first bwfd-section--last">
	<!-- wp:bwfd/section-heading {"content":"Purchase","level":1,"size":"display","style":{"spacing":{"margin":{"bottom":"30px"}}}} -->
	<div class="wp-block-bwfd-section-heading bwfd-section-heading has-text-align-left" style="margin-bottom:30px"><h1 class="bwfd-section-heading__title has-display-font-size">Purchase</h1><span class="bwfd-section-heading__rule" aria-hidden="true"></span></div>
	<!-- /wp:bwfd/section-heading -->

	<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"40px"}}}} -->
	<div class="wp-block-columns">
		<!-- wp:column {"width":"65%"} -->
		<div class="wp-block-column" style="flex-basis:65%">
			<!-- wp:bwfd/card-grid {"columns":2,"minWidth":240,"gap":20} -->
			<div class="wp-block-bwfd-card-grid bwfd-card-grid bwfd-card-grid--fixed" style="--bwfd-grid-min:240px;--bwfd-grid-gap:20px;--bwfd-grid-cols:2">
				<!-- wp:bwfd/card {"minHeight":"196px","style":{"spacing":{"blockGap":"14px"}}} -->
				<div class="wp-block-bwfd-card bwfd-card bwfd-card--accent-blue bwfd-card--marble bwfd-card--pad-regular" style="min-height:196px;gap:14px">
					<!-- wp:heading {"level":2,"fontSize":"xl"} -->
					<h2 class="wp-block-heading has-xl-font-size">Amazon Print or eBook</h2>
					<!-- /wp:heading -->
					<!-- wp:image {"width":"150px","sizeSlug":"full","linkDestination":"custom"} -->
					<figure class="wp-block-image size-full is-resized"><a href="#"><img src="<?php echo $bwfd_amazon; ?>" alt="Buy on Amazon" style="width:150px"/></a></figure>
					<!-- /wp:image -->
					<!-- wp:bwfd/badge -->
					<div class="wp-block-bwfd-badge"><span>URL TBA</span></div>
					<!-- /wp:bwfd/badge -->
				</div>
				<!-- /wp:bwfd/card -->

				<!-- wp:bwfd/card {"minHeight":"196px","style":{"spacing":{"blockGap":"14px"}}} -->
				<div class="wp-block-bwfd-card bwfd-card bwfd-card--accent-blue bwfd-card--marble bwfd-card--pad-regular" style="min-height:196px;gap:14px">
					<!-- wp:heading {"level":2,"fontSize":"xl"} -->
					<h2 class="wp-block-heading has-xl-font-size">Direct Purchase $24.99 + $9.99 postage</h2>
					<!-- /wp:heading -->
					<!-- wp:buttons -->
					<div class="wp-block-buttons">
						<!-- wp:button {"className":"is-style-bwfd-compact"} -->
						<div class="wp-block-button is-style-bwfd-compact"><a class="wp-block-button__link wp-element-button" href="https://square.link/u/dQKD6ORW" target="_blank" rel="noreferrer noopener">Buy the book</a></div>
						<!-- /wp:button -->
					</div>
					<!-- /wp:buttons -->
				</div>
				<!-- /wp:bwfd/card -->

				<!-- wp:bwfd/card {"minHeight":"196px","style":{"spacing":{"blockGap":"14px"}}} -->
				<div class="wp-block-bwfd-card bwfd-card bwfd-card--accent-blue bwfd-card--marble bwfd-card--pad-regular" style="min-height:196px;gap:14px">
					<!-- wp:heading {"level":2,"fontSize":"xl"} -->
					<h2 class="wp-block-heading has-xl-font-size">Audio Book (coming soon)</h2>
					<!-- /wp:heading -->
					<!-- wp:bwfd/badge -->
					<div class="wp-block-bwfd-badge"><span>Coming soon</span></div>
					<!-- /wp:bwfd/badge -->
				</div>
				<!-- /wp:bwfd/card -->

				<!-- wp:bwfd/card {"accent":"apricot","surface":"apricot","minHeight":"196px","style":{"spacing":{"blockGap":"14px"}}} -->
				<div class="wp-block-bwfd-card bwfd-card bwfd-card--accent-apricot bwfd-card--apricot bwfd-card--pad-regular" style="min-height:196px;gap:14px">
					<!-- wp:heading {"level":2,"fontSize":"xl"} -->
					<h2 class="wp-block-heading has-xl-font-size">Bulk Orders (20+) $15.99&nbsp;free&nbsp;postage</h2>
					<!-- /wp:heading -->
					<!-- wp:buttons -->
					<div class="wp-block-buttons">
						<!-- wp:button {"className":"is-style-bwfd-compact"} -->
						<div class="wp-block-button is-style-bwfd-compact"><a class="wp-block-button__link wp-element-button" href="https://square.link/u/iEQMs8Pq" target="_blank" rel="noreferrer noopener">Buy the book</a></div>
						<!-- /wp:button -->
					</div>
					<!-- /wp:buttons -->
				</div>
				<!-- /wp:bwfd/card -->
			</div>
			<!-- /wp:bwfd/card-grid -->

			<!-- wp:group {"className":"bwfd-measure-narrow","style":{"spacing":{"blockGap":"14px","margin":{"top":"26px"}}},"layout":{"type":"default"}} -->
			<div class="wp-block-group bwfd-measure-narrow" style="margin-top:26px">
				<!-- wp:paragraph {"fontSize":"lg"} -->
				<p class="has-lg-font-size"><a href="<?php echo bwfd_url( '/about-the-book/' ); ?>"><em>Biblical Wisdom for Dads</em></a> is available in retail stores, through Amazon as a paper or eBook, or as a direct purchase. For churches or other groups who would like bulk orders of 20 or more, perhaps as gifts for dads or for small groups, a discounted price of $15.99 per book is available.</p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph {"fontSize":"lg"} -->
				<p class="has-lg-font-size">Please feel free to download the free PDF discussion guides to help your <a href="<?php echo bwfd_url( '/small-group-guide/' ); ?>">small group</a>.</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"35%"} -->
		<div class="wp-block-column" style="flex-basis:35%">
			<!-- wp:group {"style":{"spacing":{"blockGap":"24px"}},"layout":{"type":"default"}} -->
			<div class="wp-block-group">
				<!-- wp:bwfd/framed-image {"maxWidth":"100%"} -->
				<figure class="wp-block-bwfd-framed-image bwfd-framed-image bwfd-framed-image--shadow-cover" style="max-width:100%"><img src="<?php echo $bwfd_cover; ?>" alt="Biblical Wisdom for Dads"/></figure>
				<!-- /wp:bwfd/framed-image -->

				<!-- wp:buttons {"style":{"spacing":{"blockGap":"12px"}}} -->
				<div class="wp-block-buttons">
					<!-- wp:button {"className":"is-style-bwfd-compact"} -->
					<div class="wp-block-button is-style-bwfd-compact"><a class="wp-block-button__link wp-element-button" href="<?php echo bwfd_url( '/about-the-book/' ); ?>">About the book</a></div>
					<!-- /wp:button -->
					<!-- wp:button {"className":"is-style-outline"} -->
					<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo bwfd_url( '/small-group-guide/' ); ?>">Small Group Guide</a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
