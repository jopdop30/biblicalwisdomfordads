<?php
/**
 * Title: About the author page
 * Slug: bwfd/page-about-author
 * Categories: bwfd-pages
 * Block Types: core/post-content
 * Post Types: page
 * Viewport Width: 1280
 * Description: Author portrait, biography and a contact card.
 *
 * @package bwfd
 */

declare( strict_types=1 );

$bwfd_headshot = bwfd_image( 'stephen-headshot.jpg' );
?>
<!-- wp:group {"className":"bwfd-section bwfd-section--first bwfd-section--last","layout":{"type":"constrained"}} -->
<div class="wp-block-group bwfd-section bwfd-section--first bwfd-section--last">
	<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"56px"}}}} -->
	<div class="wp-block-columns">
		<!-- wp:column {"width":"36%"} -->
		<div class="wp-block-column" style="flex-basis:36%">
			<!-- wp:bwfd/framed-image {"maxWidth":"100%","aspectRatio":"4 / 5"} -->
			<figure class="wp-block-bwfd-framed-image bwfd-framed-image bwfd-framed-image--shadow-cover" style="max-width:100%"><img src="<?php echo $bwfd_headshot; ?>" alt="Stephen Parker" style="aspect-ratio:4 / 5;object-fit:cover"/></figure>
			<!-- /wp:bwfd/framed-image -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"64%"} -->
		<div class="wp-block-column" style="flex-basis:64%">
			<!-- wp:bwfd/section-heading {"content":"About the author","level":1,"size":"4xl","style":{"spacing":{"margin":{"bottom":"26px"}}}} -->
			<div class="wp-block-bwfd-section-heading bwfd-section-heading has-text-align-left" style="margin-bottom:26px"><h1 class="bwfd-section-heading__title has-4xl-font-size">About the author</h1><span class="bwfd-section-heading__rule" aria-hidden="true"></span></div>
			<!-- /wp:bwfd/section-heading -->

			<!-- wp:paragraph {"fontSize":"lg"} -->
			<p class="has-lg-font-size">Stephen Parker loves being a dad, and is fascinated by the wisdom found in the pages of Scripture. He is a husband, father, runner and loved child of God, with one wonderful wife and four delightful daughters. From an early age, Stephen wanted to be a dad, and now being one fills him with an overwhelming joy.</p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"fontSize":"lg"} -->
			<p class="has-lg-font-size">He has served as a Youth Pastor at Springwood Church of Christ, State Youth Director for the Churches of Christ in Queensland, and helped found the Australian National Youth Ministry Convention and State Youth Games (Queensland). With 30 years of ministry experience, he is currently an Associate Professor at the Australian College of Ministries. He&rsquo;s been preaching for over 30 years, teaching its art for almost 20, and telling stories all of his life.</p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"fontSize":"lg"} -->
			<p class="has-lg-font-size">His previous books include <em>The Heart of an Elder</em> and <em>There is No Finish: The Backyard Ultra Story</em>.</p>
			<!-- /wp:paragraph -->

			<!-- wp:bwfd/card {"lift":true,"style":{"spacing":{"blockGap":"20px","margin":{"top":"32px"}}}} -->
			<div class="wp-block-bwfd-card bwfd-card bwfd-card--accent-blue bwfd-card--marble bwfd-card--pad-regular bwfd-card--lift">
				<!-- wp:paragraph {"fontSize":"base"} -->
				<p class="has-base-font-size">For podcast, speaking requests, media and other inquiries, contact <a href="mailto:stephen@biblicalwisdomfordads.au">stephen@biblicalwisdomfordads.au</a>.</p>
				<!-- /wp:paragraph -->
				<!-- wp:buttons {"style":{"spacing":{"blockGap":"12px"}}} -->
				<div class="wp-block-buttons">
					<!-- wp:button {"className":"is-style-bwfd-compact"} -->
					<div class="wp-block-button is-style-bwfd-compact"><a class="wp-block-button__link wp-element-button" href="mailto:stephen@biblicalwisdomfordads.au">Email Stephen</a></div>
					<!-- /wp:button -->
					<!-- wp:button {"className":"is-style-outline"} -->
					<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo bwfd_url( '/other-books/' ); ?>">Other books</a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
			</div>
			<!-- /wp:bwfd/card -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
