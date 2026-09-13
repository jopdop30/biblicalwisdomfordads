<?php
/**
 * Title: Home page
 * Slug: bwfd/page-home
 * Categories: bwfd-pages
 * Block Types: core/post-content
 * Post Types: page
 * Viewport Width: 1280
 * Description: Hero, navy call to action, three endorsements, three info cards, social links and the Facebook page feed.
 *
 * @package bwfd
 */

declare( strict_types=1 );

$bwfd_cover = bwfd_image( 'bwfd-cover.jpg' );
$bwfd_art   = bwfd_image( 'art-silhouette.jpg' );
?>
<!-- wp:bwfd/hero {"backgroundUrl":"<?php echo $bwfd_art; ?>"} -->
<section class="wp-block-bwfd-hero alignfull bwfd-hero bwfd-hero--radial" style="background-image:url(<?php echo $bwfd_art; ?>)"><div class="bwfd-hero__overlay" aria-hidden="true"></div><div class="bwfd-hero__inner">
	<!-- wp:bwfd/framed-image {"shadow":"cover-dark"} -->
	<figure class="wp-block-bwfd-framed-image bwfd-framed-image bwfd-framed-image--shadow-cover-dark" style="max-width:360px"><img src="<?php echo $bwfd_cover; ?>" alt="Biblical Wisdom for Dads, by Stephen Parker"/></figure>
	<!-- /wp:bwfd/framed-image -->

	<!-- wp:group {"style":{"spacing":{"blockGap":"22px"}},"layout":{"type":"flex","orientation":"vertical"}} -->
	<div class="wp-block-group">
		<!-- wp:paragraph {"className":"bwfd-kicker"} -->
		<p class="bwfd-kicker">Foreword by Richard Blackaby</p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"level":1,"fontSize":"hero"} -->
		<h1 class="wp-block-heading has-hero-font-size">What if the best way to bless our children was to learn from <mark style="color:#ffd388" class="has-inline-color">the perfect Father?</mark></h1>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"fontSize":"lg"} -->
		<p class="has-lg-font-size">In 40 short, practical chapters, dads discover how our Heavenly Father sets the ultimate example in compassion, strength, discipline, instruction and more.</p>
		<!-- /wp:paragraph -->

		<!-- wp:buttons {"layout":{"type":"flex","verticalAlignment":"center"}} -->
		<div class="wp-block-buttons">
			<!-- wp:button {"className":"is-style-bwfd-apricot"} -->
			<div class="wp-block-button is-style-bwfd-apricot"><a class="wp-block-button__link wp-element-button" href="<?php echo bwfd_url( '/purchase/' ); ?>">Buy the book</a></div>
			<!-- /wp:button -->
			<!-- wp:button {"className":"is-style-bwfd-text-link"} -->
			<div class="wp-block-button is-style-bwfd-text-link"><a class="wp-block-button__link wp-element-button" href="<?php echo bwfd_url( '/about-the-book/' ); ?>">About the book</a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</div></section>
<!-- /wp:bwfd/hero -->

<!-- wp:pattern {"slug":"bwfd/cta-band"} /-->

<!-- wp:group {"className":"bwfd-section","style":{"spacing":{"padding":{"top":"var:preset|spacing|80"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group bwfd-section" style="padding-top:var(--wp--preset--spacing--80)">
	<!-- wp:pattern {"slug":"bwfd/endorsements-home"} /-->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"bwfd-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group bwfd-section">
	<!-- wp:bwfd/card-grid {"minWidth":268,"gap":24} -->
	<div class="wp-block-bwfd-card-grid bwfd-card-grid bwfd-card-grid--auto" style="--bwfd-grid-min:268px;--bwfd-grid-gap:24px">
		<!-- wp:bwfd/card -->
		<div class="wp-block-bwfd-card bwfd-card bwfd-card--accent-blue bwfd-card--marble bwfd-card--pad-regular">
			<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"23px"}}} -->
			<h3 class="wp-block-heading" style="font-size:23px">Buy the book</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"fontSize":"base"} -->
			<p class="has-base-font-size"><em>Biblical Wisdom for Dads</em> is available in retail stores, through Amazon as a paper or eBook, or as a direct purchase.</p>
			<!-- /wp:paragraph -->
			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"is-style-bwfd-text-link"} -->
				<div class="wp-block-button is-style-bwfd-text-link"><a class="wp-block-button__link wp-element-button" href="<?php echo bwfd_url( '/purchase/' ); ?>">Purchase links</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:bwfd/card -->

		<!-- wp:bwfd/card -->
		<div class="wp-block-bwfd-card bwfd-card bwfd-card--accent-blue bwfd-card--marble bwfd-card--pad-regular">
			<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"23px"}}} -->
			<h3 class="wp-block-heading" style="font-size:23px">Small Group Guide</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"fontSize":"base"} -->
			<p class="has-base-font-size">Please feel free to download the free PDF discussion guides to help your small group.</p>
			<!-- /wp:paragraph -->
			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"is-style-bwfd-text-link"} -->
				<div class="wp-block-button is-style-bwfd-text-link"><a class="wp-block-button__link wp-element-button" href="<?php echo bwfd_url( '/small-group-guide/' ); ?>">Small Group Guide</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:bwfd/card -->

		<!-- wp:bwfd/card -->
		<div class="wp-block-bwfd-card bwfd-card bwfd-card--accent-blue bwfd-card--marble bwfd-card--pad-regular">
			<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"23px"}}} -->
			<h3 class="wp-block-heading" style="font-size:23px">Bulk orders</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"fontSize":"base"} -->
			<p class="has-base-font-size">For churches or other groups who would like bulk orders of 20 or more, a discounted price of $15.99 per book is available.</p>
			<!-- /wp:paragraph -->
			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"is-style-bwfd-text-link"} -->
				<div class="wp-block-button is-style-bwfd-text-link"><a class="wp-block-button__link wp-element-button" href="<?php echo bwfd_url( '/churches-and-retail/' ); ?>">Churches &amp; retail</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:bwfd/card -->
	</div>
	<!-- /wp:bwfd/card-grid -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"bwfd-section bwfd-section--last","layout":{"type":"constrained"}} -->
<div class="wp-block-group bwfd-section bwfd-section--last">
	<!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"left":"44px"}}}} -->
	<div class="wp-block-columns are-vertically-aligned-center">
		<!-- wp:column {"verticalAlignment":"center"} -->
		<div class="wp-block-column is-vertically-aligned-center">
			<!-- wp:bwfd/section-heading {"content":"Social Media","level":2,"size":"xxl"} -->
			<div class="wp-block-bwfd-section-heading bwfd-section-heading has-text-align-left"><h2 class="bwfd-section-heading__title has-xxl-font-size">Social Media</h2><span class="bwfd-section-heading__rule" aria-hidden="true"></span></div>
			<!-- /wp:bwfd/section-heading -->

			<!-- wp:buttons {"style":{"spacing":{"blockGap":"12px"}}} -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"is-style-bwfd-social"} -->
				<div class="wp-block-button is-style-bwfd-social"><a class="wp-block-button__link wp-element-button" href="https://facebook.com/biblicalwisdomfordads" target="_blank" rel="noreferrer noopener">Facebook</a></div>
				<!-- /wp:button -->
				<!-- wp:button {"className":"is-style-bwfd-social"} -->
				<div class="wp-block-button is-style-bwfd-social"><a class="wp-block-button__link wp-element-button" href="https://instagram.com/biblicalwisdomfordads" target="_blank" rel="noreferrer noopener">Instagram</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"center"} -->
		<div class="wp-block-column is-vertically-aligned-center">
			<!-- wp:bwfd/card {"accent":"none","padding":"compact","style":{"spacing":{"blockGap":"16px"}}} -->
			<div class="wp-block-bwfd-card bwfd-card bwfd-card--accent-none bwfd-card--marble bwfd-card--pad-compact" style="gap:16px">
				<!-- wp:paragraph {"align":"center","className":"bwfd-kicker","textColor":"blue"} -->
				<p class="has-text-align-center bwfd-kicker has-blue-color has-text-color">Facebook page feed</p>
				<!-- /wp:paragraph -->
				<!-- wp:bwfd/facebook-page {"url":"https://www.facebook.com/biblicalwisdomfordads","height":520} /-->
			</div>
			<!-- /wp:bwfd/card -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
