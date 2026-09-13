<?php
/**
 * Title: Other books page
 * Slug: bwfd/page-other-books
 * Categories: bwfd-pages
 * Block Types: core/post-content
 * Post Types: page
 * Viewport Width: 1280
 * Description: Stephen Parker’s other titles, each with cover, blurb and purchase links.
 *
 * @package bwfd
 */

declare( strict_types=1 );

$bwfd_no_finish = bwfd_image( 'no-finish-front.webp' );
$bwfd_elder     = bwfd_image( 'heart-elder-front.webp' );
?>
<!-- wp:group {"className":"bwfd-section bwfd-section--first","layout":{"type":"constrained"}} -->
<div class="wp-block-group bwfd-section bwfd-section--first">
	<!-- wp:bwfd/section-heading {"content":"Other books","level":1,"size":"display"} -->
	<div class="wp-block-bwfd-section-heading bwfd-section-heading has-text-align-left"><h1 class="bwfd-section-heading__title has-display-font-size">Other books</h1><span class="bwfd-section-heading__rule" aria-hidden="true"></span></div>
	<!-- /wp:bwfd/section-heading -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"bwfd-section","style":{"spacing":{"padding":{"top":"52px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group bwfd-section" style="padding-top:52px">
	<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"44px"}}}} -->
	<div class="wp-block-columns">
		<!-- wp:column {"width":"30%"} -->
		<div class="wp-block-column" style="flex-basis:30%">
			<!-- wp:bwfd/framed-image {"shadow":"book","maxWidth":"100%","width":700,"height":1054} -->
			<figure class="wp-block-bwfd-framed-image bwfd-framed-image bwfd-framed-image--shadow-book" style="max-width:100%"><img src="<?php echo $bwfd_no_finish; ?>" alt="There is No Finish: The Backyard Ultra Story" width="700" height="1054"/></figure>
			<!-- /wp:bwfd/framed-image -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"70%"} -->
		<div class="wp-block-column" style="flex-basis:70%">
			<!-- wp:heading {"level":2,"fontSize":"xxxl","style":{"spacing":{"margin":{"bottom":"18px"}}}} -->
			<h2 class="wp-block-heading has-xxxl-font-size" style="margin-bottom:18px">There is No Finish: The Backyard Ultra Story</h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"style":{"typography":{"fontSize":"18px"}}} -->
			<p style="font-size:18px"><em>There is No Finish</em> introduces the exploding world of the Backyard Ultra, a new ultramarathon format. Birthed from the high school musings of Lazarus Lake, renowned for the Barkley Marathons, the running event has spread globally&mdash;to over eighty countries&mdash; with sky-rocketing numbers.</p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"style":{"typography":{"fontSize":"18px"}}} -->
			<p style="font-size:18px">Runners have up to one hour to complete a 4.1-mile lap. At the start of the next hour, again they attempt to complete the same lap. The runners continue, striving to see how many laps they can complete before they collapse. The race keeps going, with no finish, until all except one person gives up. The winner is the last one standing who does one lap more than anyone else.</p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"style":{"typography":{"fontSize":"18px"}}} -->
			<p style="font-size:18px">With participants running until they drop, every race becomes an enthralling adventure with epic stories. Stephen Parker dives deep into the Backyard Ultra scene, running events, crewing for others, and exploring its amazing history. With races in Belgium, the USA, Australia, Sweden, Canada, Finland, South Africa, and many more, the book shares the agony and adrenaline of battles between colourful characters in stunning settings.</p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"textColor":"navy","fontSize":"lg","fontFamily":"serif","style":{"typography":{"fontStyle":"italic","fontWeight":"400"}}} -->
			<p class="has-navy-color has-text-color has-serif-font-family has-lg-font-size" style="font-style:italic;font-weight:400">If there is no finish, how far can you run?</p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"layout":{"type":"flex","verticalAlignment":"center"},"style":{"spacing":{"margin":{"top":"24px"}}}} -->
			<div class="wp-block-buttons" style="margin-top:24px">
				<!-- wp:button {"className":"is-style-bwfd-compact"} -->
				<div class="wp-block-button is-style-bwfd-compact"><a class="wp-block-button__link wp-element-button" href="http://www.amazon.com/dp/B0DD42GD27/ref=nosim?tag=runningforeve-20" target="_blank" rel="noreferrer noopener">Amazon: There is No Finish</a></div>
				<!-- /wp:button -->
				<!-- wp:button {"className":"is-style-bwfd-text-link"} -->
				<div class="wp-block-button is-style-bwfd-text-link"><a class="wp-block-button__link wp-element-button" href="https://runningforever.au/" target="_blank" rel="noreferrer noopener">Other Purchase Links</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"bwfd-section bwfd-section--last","style":{"spacing":{"padding":{"top":"62px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group bwfd-section bwfd-section--last" style="padding-top:62px">
	<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"44px"}}}} -->
	<div class="wp-block-columns">
		<!-- wp:column {"width":"30%"} -->
		<div class="wp-block-column" style="flex-basis:30%">
			<!-- wp:bwfd/framed-image {"shadow":"book","maxWidth":"100%","width":700,"height":1067} -->
			<figure class="wp-block-bwfd-framed-image bwfd-framed-image bwfd-framed-image--shadow-book" style="max-width:100%"><img src="<?php echo $bwfd_elder; ?>" alt="The Heart of an Elder" width="700" height="1067"/></figure>
			<!-- /wp:bwfd/framed-image -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"70%"} -->
		<div class="wp-block-column" style="flex-basis:70%">
			<!-- wp:heading {"level":2,"fontSize":"xxxl","style":{"spacing":{"margin":{"bottom":"18px"}}}} -->
			<h2 class="wp-block-heading has-xxxl-font-size" style="margin-bottom:18px">The Heart of an Elder</h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"style":{"typography":{"fontSize":"18px"}}} -->
			<p style="font-size:18px">Are you an elder, or considering serving as an Elder in your church? <em>The Heart of an Elder</em> invites you on a two-week journey to explore what the Scriptures have to say about a crucial role. This practical book will help you to discern whether this opportunity is best for you now, and help you to serve in healthy, biblical and sustainable ways.</p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"style":{"typography":{"fontSize":"18px"}}} -->
			<p style="font-size:18px">Although designed for prospective elders, many church boards have used this as a training tool in their meetings to help them in their tasks of oversight and shepherding.</p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"style":{"typography":{"fontSize":"18px"}}} -->
			<p style="font-size:18px">The heart of eldership is not about business meetings and budgets, but discerning the leading of God for a community of faith. Instead of recruiting board professionals, elderships should be filled with those pursuing unity, humility and the mission of Jesus. Together, the elders watch over not just their congregations, but one another as well.</p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"24px"}}}} -->
			<div class="wp-block-buttons" style="margin-top:24px">
				<!-- wp:button {"className":"is-style-bwfd-compact"} -->
				<div class="wp-block-button is-style-bwfd-compact"><a class="wp-block-button__link wp-element-button" href="https://amzn.to/3VhFBf1" target="_blank" rel="noreferrer noopener">Amazon: The Heart of an Elder</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
