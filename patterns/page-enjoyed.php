<?php
/**
 * Title: Enjoyed? page
 * Slug: bwfd/page-enjoyed
 * Categories: bwfd-pages
 * Block Types: core/post-content
 * Post Types: page
 * Viewport Width: 1280
 * Description: Five ways readers can help others find the book.
 *
 * @package bwfd
 */

declare( strict_types=1 );
?>
<!-- wp:group {"className":"bwfd-section bwfd-section--first","layout":{"type":"constrained"}} -->
<div class="wp-block-group bwfd-section bwfd-section--first">
	<!-- wp:group {"className":"bwfd-measure-narrow","style":{"spacing":{"blockGap":"28px"}},"layout":{"type":"default"}} -->
	<div class="wp-block-group bwfd-measure-narrow">
		<!-- wp:bwfd/section-heading {"content":"Enjoyed?","level":1,"size":"4xl"} -->
		<div class="wp-block-bwfd-section-heading bwfd-section-heading has-text-align-left"><h1 class="bwfd-section-heading__title has-4xl-font-size">Enjoyed?</h1><span class="bwfd-section-heading__rule" aria-hidden="true"></span></div>
		<!-- /wp:bwfd/section-heading -->

		<!-- wp:paragraph {"textColor":"navy","fontSize":"3xl","fontFamily":"serif","style":{"typography":{"lineHeight":"1.35"}}} -->
		<p class="has-navy-color has-text-color has-serif-font-family has-3xl-font-size" style="line-height:1.35">If you enjoyed <em>Biblical Wisdom for Dads</em>, there are lots of ways you can help others to find out about it &ndash; and help the author!</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"bwfd-section bwfd-section--last","style":{"spacing":{"padding":{"top":"52px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group bwfd-section bwfd-section--last" style="padding-top:52px">
	<!-- wp:bwfd/card-grid {"minWidth":280,"centered":true} -->
	<div class="wp-block-bwfd-card-grid bwfd-card-grid bwfd-card-grid--auto bwfd-card-grid--centered" style="--bwfd-grid-min:280px;--bwfd-grid-gap:22px">
		<!-- wp:bwfd/card {"minHeight":"264px","style":{"spacing":{"blockGap":"16px"}}} -->
		<div class="wp-block-bwfd-card bwfd-card bwfd-card--accent-blue bwfd-card--marble bwfd-card--pad-regular" style="min-height:264px">
			<!-- wp:bwfd/icon {"icon":"chat"} /-->
			<!-- wp:paragraph {"fontSize":"md"} -->
			<p class="has-md-font-size">Tell another dad about some of the wisdom you saw in the Scriptures</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:bwfd/card -->

		<!-- wp:bwfd/card {"minHeight":"264px","style":{"spacing":{"blockGap":"16px"}}} -->
		<div class="wp-block-bwfd-card bwfd-card bwfd-card--accent-blue bwfd-card--marble bwfd-card--pad-regular" style="min-height:264px">
			<!-- wp:bwfd/icon {"icon":"star"} /-->
			<!-- wp:paragraph {"fontSize":"md"} -->
			<p class="has-md-font-size">Please rate and review the book on Amazon and Goodreads (this massively helps others to find them)</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:bwfd/card -->

		<!-- wp:bwfd/card {"minHeight":"264px","style":{"spacing":{"blockGap":"16px"}}} -->
		<div class="wp-block-bwfd-card bwfd-card bwfd-card--accent-blue bwfd-card--marble bwfd-card--pad-regular" style="min-height:264px">
			<!-- wp:bwfd/icon {"icon":"camera"} /-->
			<!-- wp:paragraph {"fontSize":"md"} -->
			<p class="has-md-font-size">Share a picture of yourself with the book on social media (and tag the book on <a href="https://facebook.com/biblicalwisdomfordads" target="_blank" rel="noreferrer noopener">Facebook</a> or <a href="https://instagram.com/biblicalwisdomfordads" target="_blank" rel="noreferrer noopener">Instagram</a>)</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:bwfd/card -->

		<!-- wp:bwfd/card {"minHeight":"264px","style":{"spacing":{"blockGap":"16px"}}} -->
		<div class="wp-block-bwfd-card bwfd-card bwfd-card--accent-blue bwfd-card--marble bwfd-card--pad-regular" style="min-height:264px">
			<!-- wp:bwfd/icon {"icon":"group"} /-->
			<!-- wp:paragraph {"fontSize":"md"} -->
			<p class="has-md-font-size">Invite some guys to meet together as a small group to discuss</p>
			<!-- /wp:paragraph -->
			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"is-style-bwfd-text-link"} -->
				<div class="wp-block-button is-style-bwfd-text-link"><a class="wp-block-button__link wp-element-button" href="<?php echo bwfd_url( '/small-group-guide/' ); ?>">See the small group guide</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:bwfd/card -->

		<!-- wp:bwfd/card {"minHeight":"264px","style":{"spacing":{"blockGap":"16px"}}} -->
		<div class="wp-block-bwfd-card bwfd-card bwfd-card--accent-blue bwfd-card--marble bwfd-card--pad-regular" style="min-height:264px">
			<!-- wp:bwfd/icon {"icon":"gift"} /-->
			<!-- wp:paragraph {"fontSize":"md"} -->
			<p class="has-md-font-size">Purchase extra copies in bulk for small groups or gifts for weddings, new dads, Father&rsquo;s Day or Christmas</p>
			<!-- /wp:paragraph -->
			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"is-style-bwfd-text-link"} -->
				<div class="wp-block-button is-style-bwfd-text-link"><a class="wp-block-button__link wp-element-button" href="<?php echo bwfd_url( '/purchase/' ); ?>">Buy the book</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:bwfd/card -->
	</div>
	<!-- /wp:bwfd/card-grid -->
</div>
<!-- /wp:group -->
