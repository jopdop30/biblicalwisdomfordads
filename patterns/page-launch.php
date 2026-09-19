<?php
/**
 * Title: Launch event page
 * Slug: bwfd/page-launch
 * Categories: bwfd-pages
 * Block Types: core/post-content
 * Post Types: page
 * Viewport Width: 1280
 * Description: Navy invitation band with the Book Launch wordmark and date card, RSVP band, the top ten reasons to attend, “on the night” pills, event details and a closing apricot panel.
 *
 * @package bwfd
 */

declare( strict_types=1 );

$bwfd_wordmark = bwfd_image( 'book-launch-wordmark.webp' );
$bwfd_rsvp     = 'https://www.trybooking.com/DQBEO';
$bwfd_facebook = 'https://facebook.com/biblicalwisdomfordads';
?>
<!-- wp:group {"align":"full","className":"is-style-bwfd-navy bwfd-event-hero","style":{"spacing":{"padding":{"top":"56px","bottom":"64px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull is-style-bwfd-navy bwfd-event-hero" style="padding-top:56px;padding-bottom:64px">
	<!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"left":"52px"}}}} -->
	<div class="wp-block-columns are-vertically-aligned-center">
		<!-- wp:column {"verticalAlignment":"center","width":"66%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:66%">
			<!-- wp:group {"style":{"spacing":{"blockGap":"22px"}},"layout":{"type":"flex","orientation":"vertical"}} -->
			<div class="wp-block-group">
				<!-- wp:heading {"level":1,"className":"screen-reader-text"} -->
				<h1 class="wp-block-heading screen-reader-text">Book launch: Biblical Wisdom for Dads</h1>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"className":"bwfd-kicker","textColor":"apricot"} -->
				<p class="bwfd-kicker has-apricot-color has-text-color">You&rsquo;re invited</p>
				<!-- /wp:paragraph -->

				<!-- wp:image {"width":"520px","sizeSlug":"full","linkDestination":"none","className":"bwfd-event-wordmark"} -->
				<figure class="wp-block-image size-full is-resized bwfd-event-wordmark"><img src="<?php echo $bwfd_wordmark; ?>" alt="Book launch" style="width:520px"/></figure>
				<!-- /wp:image -->

				<!-- wp:paragraph {"className":"bwfd-event-lead","fontSize":"lg"} -->
				<p class="bwfd-event-lead has-lg-font-size">You are invited to the launch of <em>Biblical Wisdom for Dads</em> on Tuesday, October 27 at Springwood Church of Christ. For grandfathers, future dads and everyone in between.</p>
				<!-- /wp:paragraph -->

				<!-- wp:buttons {"layout":{"type":"flex","verticalAlignment":"center"},"style":{"spacing":{"margin":{"top":"6px"}}}} -->
				<div class="wp-block-buttons" style="margin-top:6px">
					<!-- wp:button {"className":"is-style-bwfd-apricot bwfd-button--large"} -->
					<div class="wp-block-button is-style-bwfd-apricot bwfd-button--large"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $bwfd_rsvp ); ?>" target="_blank" rel="noreferrer noopener">RSVP now</a></div>
					<!-- /wp:button -->
					<!-- wp:button {"className":"is-style-outline bwfd-button--large bwfd-button--facebook"} -->
					<div class="wp-block-button is-style-outline bwfd-button--large bwfd-button--facebook"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $bwfd_facebook ); ?>" target="_blank" rel="noreferrer noopener">Facebook Event<small class="bwfd-button__hint">Click &ldquo;going&rdquo;</small></a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"center","width":"34%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:34%">
			<!-- wp:group {"className":"bwfd-event-card","style":{"spacing":{"padding":{"top":"34px","right":"34px","bottom":"34px","left":"34px"},"blockGap":"2px"}},"layout":{"type":"flex","orientation":"vertical"}} -->
			<div class="wp-block-group bwfd-event-card" style="padding-top:34px;padding-right:34px;padding-bottom:34px;padding-left:34px">
				<!-- wp:paragraph {"className":"bwfd-kicker","textColor":"gold"} -->
				<p class="bwfd-kicker has-gold-color has-text-color">Tuesday</p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"className":"bwfd-event-card__date"} -->
				<p class="bwfd-event-card__date">27 October</p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"className":"bwfd-event-card__time"} -->
				<p class="bwfd-event-card__time">7.00 &ndash; 8.15pm</p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"className":"bwfd-event-card__where"} -->
				<p class="bwfd-event-card__where">Springwood Church of Christ<br>178 Springwood Road<br>Springwood QLD 4127</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"bwfd-rsvp-band","style":{"spacing":{"padding":{"top":"26px","bottom":"26px"}}},"backgroundColor":"navy","textColor":"white","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull bwfd-rsvp-band has-white-color has-navy-background-color has-text-color has-background" style="padding-top:26px;padding-bottom:26px">
	<!-- wp:group {"style":{"spacing":{"blockGap":"14px 28px"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
	<div class="wp-block-group">
		<!-- wp:paragraph {"fontSize":"xl","fontFamily":"serif"} -->
		<p class="has-serif-font-family has-xl-font-size">Attendance is limited and catering needs numbers &ndash; please RSVP by Tuesday 20 October.</p>
		<!-- /wp:paragraph -->

		<!-- wp:buttons -->
		<div class="wp-block-buttons">
			<!-- wp:button {"className":"is-style-bwfd-apricot"} -->
			<div class="wp-block-button is-style-bwfd-apricot"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $bwfd_rsvp ); ?>" target="_blank" rel="noreferrer noopener">Book a free ticket</a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"bwfd-section","style":{"spacing":{"padding":{"top":"66px","bottom":"72px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group bwfd-section" style="padding-top:66px;padding-bottom:72px">
	<!-- wp:bwfd/section-heading {"content":"The top 10 reasons to attend","style":{"spacing":{"margin":{"bottom":"22px"}}}} -->
	<div class="wp-block-bwfd-section-heading bwfd-section-heading has-text-align-left" style="margin-bottom:22px"><h2 class="bwfd-section-heading__title has-xxl-font-size">The top 10 reasons to attend</h2><span class="bwfd-section-heading__rule" aria-hidden="true"></span></div>
	<!-- /wp:bwfd/section-heading -->

	<!-- wp:list {"ordered":true,"className":"bwfd-numbered-list","fontSize":"lg"} -->
	<ol class="wp-block-list bwfd-numbered-list has-lg-font-size">
		<!-- wp:list-item --><li>The cheapest way to get a copy &ndash; Launch special $19.99 (RRP $24.99 in stores and online)</li><!-- /wp:list-item -->
		<!-- wp:list-item --><li>Bring a group of guys from your Church as a men&rsquo;s event</li><!-- /wp:list-item -->
		<!-- wp:list-item --><li>Hear the behind-the-scenes stories of how the book came to be</li><!-- /wp:list-item -->
		<!-- wp:list-item --><li>Enjoy some snacks and drinks</li><!-- /wp:list-item -->
		<!-- wp:list-item --><li>Hear from special guests via Zoom</li><!-- /wp:list-item -->
		<!-- wp:list-item --><li>Explore free small group resources to discuss biblical parenting</li><!-- /wp:list-item -->
		<!-- wp:list-item --><li>Pick up Christmas presents for dads you know</li><!-- /wp:list-item -->
		<!-- wp:list-item --><li>Support a local Christian author</li><!-- /wp:list-item -->
		<!-- wp:list-item --><li>Listen to stories from the book itself</li><!-- /wp:list-item -->
		<!-- wp:list-item --><li>Pickup bulk copies for small groups and giveaways at $15.99</li><!-- /wp:list-item -->
	</ol>
	<!-- /wp:list -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"is-style-bwfd-navy","style":{"spacing":{"padding":{"top":"56px","bottom":"56px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull is-style-bwfd-navy" style="padding-top:56px;padding-bottom:56px">
	<!-- wp:bwfd/section-heading {"content":"On the night","textColor":"white","style":{"spacing":{"margin":{"bottom":"26px"}}}} -->
	<div class="wp-block-bwfd-section-heading bwfd-section-heading has-text-align-left has-white-color has-text-color" style="margin-bottom:26px"><h2 class="bwfd-section-heading__title has-xxl-font-size">On the night</h2><span class="bwfd-section-heading__rule" aria-hidden="true"></span></div>
	<!-- /wp:bwfd/section-heading -->

	<!-- wp:group {"className":"bwfd-pills","style":{"spacing":{"blockGap":"14px"}},"layout":{"type":"flex","flexWrap":"wrap"}} -->
	<div class="wp-block-group bwfd-pills">
		<!-- wp:paragraph {"className":"bwfd-pill"} -->
		<p class="bwfd-pill">Interviews</p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"className":"bwfd-pill"} -->
		<p class="bwfd-pill">Games</p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"className":"bwfd-pill"} -->
		<p class="bwfd-pill">Discounted prices</p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"className":"bwfd-pill"} -->
		<p class="bwfd-pill">Why the book matters</p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"className":"bwfd-pill"} -->
		<p class="bwfd-pill">Hope for exhausted dads</p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"className":"bwfd-pill"} -->
		<p class="bwfd-pill">Help for small groups</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"bwfd-section","style":{"spacing":{"padding":{"top":"66px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group bwfd-section" style="padding-top:66px">
	<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"56px"}}}} -->
	<div class="wp-block-columns">
		<!-- wp:column {"width":"65%"} -->
		<div class="wp-block-column" style="flex-basis:65%">
			<!-- wp:bwfd/section-heading {"content":"The details","style":{"spacing":{"margin":{"bottom":"26px"}}}} -->
			<div class="wp-block-bwfd-section-heading bwfd-section-heading has-text-align-left" style="margin-bottom:26px"><h2 class="bwfd-section-heading__title has-xxl-font-size">The details</h2><span class="bwfd-section-heading__rule" aria-hidden="true"></span></div>
			<!-- /wp:bwfd/section-heading -->

			<!-- wp:table {"hasFixedLayout":false,"className":"is-style-bwfd-details","fontSize":"lg"} -->
			<figure class="wp-block-table is-style-bwfd-details has-lg-font-size"><table><tbody><tr><td>When</td><td>Tuesday 27 October 2026, 7.00 &ndash; 8.15pm</td></tr><tr><td>Where</td><td>Springwood Church of Christ<br>178 Springwood Road, Springwood QLD 4127</td></tr><tr><td>Cost</td><td>Free to attend. Books at the launch special of $19.99.</td></tr><tr><td>RSVP</td><td>By Tuesday 20 October &ndash; <a href="<?php echo esc_url( $bwfd_rsvp ); ?>" target="_blank" rel="noreferrer noopener">book your free ticket here</a></td></tr></tbody></table></figure>
			<!-- /wp:table -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"35%"} -->
		<div class="wp-block-column" style="flex-basis:35%">
			<!-- wp:group {"className":"bwfd-stack","layout":{"type":"default"}} -->
			<div class="wp-block-group bwfd-stack">
				<!-- wp:bwfd/card {"accent":"apricot","surface":"apricot","padding":"compact","style":{"spacing":{"blockGap":"12px"}}} -->
				<div class="wp-block-bwfd-card bwfd-card bwfd-card--accent-apricot bwfd-card--apricot bwfd-card--pad-compact" style="gap:12px">
					<!-- wp:heading {"level":3,"fontSize":"xl"} -->
					<h3 class="wp-block-heading has-xl-font-size">Bringing a group?</h3>
					<!-- /wp:heading -->
					<!-- wp:paragraph {"fontSize":"base"} -->
					<p class="has-base-font-size">Bulk copies for small groups and giveaways are $15.99 each on the night &ndash; a good reason to fill a table with the men from your church.</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:bwfd/card -->

				<!-- wp:bwfd/card {"padding":"compact","style":{"spacing":{"blockGap":"12px"}}} -->
				<div class="wp-block-bwfd-card bwfd-card bwfd-card--accent-blue bwfd-card--marble bwfd-card--pad-compact" style="gap:12px">
					<!-- wp:heading {"level":3,"fontSize":"xl"} -->
					<h3 class="wp-block-heading has-xl-font-size">Can&rsquo;t make it?</h3>
					<!-- /wp:heading -->
					<!-- wp:paragraph {"fontSize":"base"} -->
					<p class="has-base-font-size">Follow along on <a href="<?php echo esc_url( $bwfd_facebook ); ?>" target="_blank" rel="noreferrer noopener">Facebook</a> or <a href="https://instagram.com/biblicalwisdomfordads" target="_blank" rel="noreferrer noopener">Instagram</a>, or order a copy for release day.</p>
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
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"bwfd-section bwfd-section--last","style":{"spacing":{"padding":{"top":"70px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group bwfd-section bwfd-section--last" style="padding-top:70px">
	<!-- wp:group {"className":"is-style-bwfd-apricot bwfd-note","style":{"spacing":{"padding":{"top":"clamp(30px,4vw,44px)","right":"clamp(30px,4vw,44px)","bottom":"clamp(30px,4vw,44px)","left":"clamp(30px,4vw,44px)"},"blockGap":"14px"}},"layout":{"type":"default"}} -->
	<div class="wp-block-group is-style-bwfd-apricot bwfd-note" style="padding-top:clamp(30px,4vw,44px);padding-right:clamp(30px,4vw,44px);padding-bottom:clamp(30px,4vw,44px);padding-left:clamp(30px,4vw,44px)">
		<!-- wp:paragraph {"textColor":"navy","fontSize":"xxl","fontFamily":"serif","style":{"typography":{"lineHeight":"1.35"}}} -->
		<p class="has-navy-color has-text-color has-serif-font-family has-xxl-font-size" style="line-height:1.35">In 40 short chapters, this journey through Scripture explores the challenging, encouraging and life-changing wisdom God offers for our role as dads.</p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"fontSize":"lg"} -->
		<p class="has-lg-font-size">Come and celebrate the launch &ndash; Tuesday 27 October, 7.00pm, Springwood Church of Christ.</p>
		<!-- /wp:paragraph -->
		<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"24px"}}}} -->
		<div class="wp-block-buttons" style="margin-top:24px">
			<!-- wp:button -->
			<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $bwfd_rsvp ); ?>" target="_blank" rel="noreferrer noopener">RSVP at TryBooking</a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
