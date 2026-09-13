<?php
/**
 * Title: About the book page
 * Slug: bwfd/page-about-book
 * Categories: bwfd-pages
 * Block Types: core/post-content
 * Post Types: page
 * Viewport Width: 1280
 * Description: Book blurb with cover, key information reveal, six reasons the book makes a difference and all endorsements.
 *
 * @package bwfd
 */

declare( strict_types=1 );

$bwfd_cover = bwfd_image( 'bwfd-cover.jpg' );
?>
<!-- wp:group {"className":"bwfd-section bwfd-section--first","layout":{"type":"constrained"}} -->
<div class="wp-block-group bwfd-section bwfd-section--first">
	<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"56px"}}}} -->
	<div class="wp-block-columns">
		<!-- wp:column {"width":"65%"} -->
		<div class="wp-block-column" style="flex-basis:65%">
			<!-- wp:bwfd/section-heading {"content":"About the book","level":1,"size":"display","style":{"spacing":{"margin":{"bottom":"26px"}}}} -->
			<div class="wp-block-bwfd-section-heading bwfd-section-heading has-text-align-left" style="margin-bottom:26px"><h1 class="bwfd-section-heading__title has-display-font-size">About the book</h1><span class="bwfd-section-heading__rule" aria-hidden="true"></span></div>
			<!-- /wp:bwfd/section-heading -->

			<!-- wp:paragraph {"fontSize":"lg"} -->
			<p class="has-lg-font-size">In a world where men struggle to find models of healthy masculinity, with the manosphere offering toxic but tempting voices, <em>Biblical Wisdom for Dads</em> invites Christian fathers to explore the life-changing wisdom Scripture offers for their vital role. In 40 short, practical chapters, dads discover how our Heavenly Father sets the ultimate example in compassion, strength, discipline, instruction and more.</p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"fontSize":"lg"} -->
			<p class="has-lg-font-size"><em>Biblical Wisdom for Dads</em> is for the great dads, those who adore their children and want them to flourish. It&rsquo;s for dads who wisely use self-control, discipline and encouragement. It&rsquo;s for the courageous spiritual leaders, who demonstrate compassion and act to protect their families. And it&rsquo;s for those of us who get it wrong constantly, are exhausted and need all the help we can get.</p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"fontSize":"lg"} -->
			<p class="has-lg-font-size">Whether you're preparing for fatherhood, raising little children, guiding young adults, or enjoying life as a grandfather, <em>Biblical Wisdom for Dads</em> will encourage and equip you for the joys and challenges ahead. Honest, practical, and rooted in God's Word, <em>Biblical Wisdom for Dads</em> will help you bless your family with greater faith, wisdom, and courage.</p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"fontSize":"lg"} -->
			<p class="has-lg-font-size">It is designed to be read slowly, whether one chapter each day, or one per week. Hopefully, each chapter will prompt you to reflect on things you have done, and things you would like to do. Most likely, it will challenge you to act differently towards your children, and think differently about them too. Therefore, don&rsquo;t try to just get through it all quickly, but take your time to read, pray, reflect, discuss and apply it carefully to your life and to your family.</p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"layout":{"type":"flex","verticalAlignment":"center"},"style":{"spacing":{"margin":{"top":"30px"}}}} -->
			<div class="wp-block-buttons" style="margin-top:30px">
				<!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo bwfd_url( '/purchase/' ); ?>">Buy the book</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->

			<!-- wp:pattern {"slug":"bwfd/key-information"} /-->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"35%"} -->
		<div class="wp-block-column" style="flex-basis:35%">
			<!-- wp:bwfd/framed-image {"maxWidth":"320px"} -->
			<figure class="wp-block-bwfd-framed-image bwfd-framed-image bwfd-framed-image--shadow-cover" style="max-width:320px"><img src="<?php echo $bwfd_cover; ?>" alt="Biblical Wisdom for Dads"/></figure>
			<!-- /wp:bwfd/framed-image -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"bwfd-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group bwfd-section">
	<!-- wp:bwfd/section-heading {"content":"Why this book makes a difference","style":{"spacing":{"margin":{"bottom":"30px"}}}} -->
	<div class="wp-block-bwfd-section-heading bwfd-section-heading has-text-align-left" style="margin-bottom:30px"><h2 class="bwfd-section-heading__title has-xxl-font-size">Why this book makes a difference</h2><span class="bwfd-section-heading__rule" aria-hidden="true"></span></div>
	<!-- /wp:bwfd/section-heading -->

	<!-- wp:bwfd/card-grid {"minWidth":280} -->
	<div class="wp-block-bwfd-card-grid bwfd-card-grid bwfd-card-grid--auto" style="--bwfd-grid-min:280px;--bwfd-grid-gap:22px">
		<?php
		$bwfd_reasons = array(
			array( 'compass', 'Clear Guidance', 'With men wrestling with how to discipline, teach, correct and care for their children, this book provides clear, actionable principles' ),
			array( 'book', 'Scriptural Wisdom', 'It connects dads with the incredible parenting wisdom in Scripture' ),
			array( 'chat', 'Practical', 'Written for men who don&rsquo;t read much, with practical application and stories' ),
			array( 'shield', 'Compelling Vision', 'Addresses the growing crisis of male identity with a compelling biblical vision of presence, courage, humility, love, blessing, and grace' ),
			array( 'group', 'Assists Small Groups', 'Useful for men&rsquo;s small groups, with free discussion guides available' ),
			array( 'gift', 'A Great Gift!', 'A meaningful gift for Father&rsquo;s Day, new dads, weddings and Christmas' ),
		);
		foreach ( $bwfd_reasons as list( $bwfd_icon, $bwfd_title, $bwfd_text ) ) :
			?>
		<!-- wp:bwfd/card {"style":{"spacing":{"blockGap":"16px"}}} -->
		<div class="wp-block-bwfd-card bwfd-card bwfd-card--accent-blue bwfd-card--marble bwfd-card--pad-regular" style="gap:16px">
			<!-- wp:bwfd/icon {"icon":"<?php echo esc_attr( $bwfd_icon ); ?>"} /-->
			<!-- wp:heading {"level":3,"fontSize":"xl"} -->
			<h3 class="wp-block-heading has-xl-font-size"><?php echo $bwfd_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static pattern copy. ?></h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"fontSize":"md"} -->
			<p class="has-md-font-size"><?php echo $bwfd_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static pattern copy. ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:bwfd/card -->
		<?php endforeach; ?>
	</div>
	<!-- /wp:bwfd/card-grid -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"bwfd-section bwfd-section--last","layout":{"type":"constrained"}} -->
<div class="wp-block-group bwfd-section bwfd-section--last">
	<!-- wp:bwfd/section-heading {"content":"Endorsements","style":{"spacing":{"margin":{"bottom":"28px"}}}} -->
	<div class="wp-block-bwfd-section-heading bwfd-section-heading has-text-align-left" style="margin-bottom:28px"><h2 class="bwfd-section-heading__title has-xxl-font-size">Endorsements</h2><span class="bwfd-section-heading__rule" aria-hidden="true"></span></div>
	<!-- /wp:bwfd/section-heading -->

	<!-- wp:pattern {"slug":"bwfd/endorsements-full"} /-->
</div>
<!-- /wp:group -->
