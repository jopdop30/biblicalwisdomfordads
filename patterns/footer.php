<?php
/**
 * Title: Site footer
 * Slug: bwfd/footer
 * Categories: bwfd-sections
 * Block Types: core/template-part/footer
 * Inserter: no
 *
 * @package bwfd
 */

declare( strict_types=1 );
?>
<!-- wp:group {"align":"full","className":"is-style-bwfd-navy bwfd-footer","style":{"spacing":{"padding":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull is-style-bwfd-navy bwfd-footer" style="padding-top:0;padding-bottom:0">
	<!-- wp:group {"align":"full","className":"bwfd-stripe","gradient":"footer-stripe","style":{"spacing":{"padding":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
	<div class="wp-block-group alignfull bwfd-stripe has-footer-stripe-gradient-background has-background" style="padding-top:0;padding-bottom:0"></div>
	<!-- /wp:group -->

	<!-- wp:group {"style":{"spacing":{"padding":{"top":"50px","bottom":"54px"},"blockGap":"34px 48px"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"top"}} -->
	<div class="wp-block-group" style="padding-top:50px;padding-bottom:54px">
		<!-- wp:group {"style":{"spacing":{"blockGap":"6px"},"layout":{"selfStretch":"fixed","flexSize":"22em"}},"layout":{"type":"flex","orientation":"vertical"}} -->
		<div class="wp-block-group">
			<!-- wp:paragraph {"className":"bwfd-brand"} -->
			<p class="bwfd-brand"><?php esc_html_e( 'Biblical Wisdom', 'bwfd' ); ?> <mark style="color:#f8bb7c" class="has-inline-color"><?php esc_html_e( 'for Dads', 'bwfd' ); ?></mark></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"className":"bwfd-footer-email","fontSize":"base"} -->
			<p class="bwfd-footer-email has-base-font-size"><a href="mailto:stephen@biblicalwisdomfordads.au">stephen@biblicalwisdomfordads.au</a></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"className":"bwfd-footer-muted","style":{"typography":{"fontSize":"15px"}}} -->
			<p class="bwfd-footer-muted" style="font-size:15px">www.biblicalwisdomfordads.au</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:list {"className":"bwfd-footer-links","fontSize":"base"} -->
		<ul class="wp-block-list bwfd-footer-links has-base-font-size">
			<!-- wp:list-item --><li><a href="<?php echo esc_url( home_url( '/about-the-book/' ) ); ?>"><?php esc_html_e( 'About the book', 'bwfd' ); ?></a></li><!-- /wp:list-item -->
			<!-- wp:list-item --><li><a href="<?php echo esc_url( home_url( '/about-the-author/' ) ); ?>"><?php esc_html_e( 'About the author', 'bwfd' ); ?></a></li><!-- /wp:list-item -->
			<!-- wp:list-item --><li><a href="<?php echo esc_url( home_url( '/insights/' ) ); ?>"><?php esc_html_e( 'Insights', 'bwfd' ); ?></a></li><!-- /wp:list-item -->
			<!-- wp:list-item --><li><a href="<?php echo esc_url( home_url( '/chapters/' ) ); ?>"><?php esc_html_e( 'Chapter by chapter', 'bwfd' ); ?></a></li><!-- /wp:list-item -->
			<!-- wp:list-item --><li><a href="<?php echo esc_url( home_url( '/purchase/' ) ); ?>"><?php esc_html_e( 'Buy the book', 'bwfd' ); ?></a></li><!-- /wp:list-item -->
		</ul>
		<!-- /wp:list -->

		<!-- wp:list {"className":"bwfd-footer-links","fontSize":"base"} -->
		<ul class="wp-block-list bwfd-footer-links has-base-font-size">
			<!-- wp:list-item --><li><a href="<?php echo esc_url( home_url( '/small-group-guide/' ) ); ?>"><?php esc_html_e( 'Small Group Guide', 'bwfd' ); ?></a></li><!-- /wp:list-item -->
			<!-- wp:list-item --><li><a href="<?php echo esc_url( home_url( '/other-books/' ) ); ?>"><?php esc_html_e( 'Other books', 'bwfd' ); ?></a></li><!-- /wp:list-item -->
			<!-- wp:list-item --><li><a href="<?php echo esc_url( home_url( '/enjoyed/' ) ); ?>"><?php esc_html_e( 'Enjoyed?', 'bwfd' ); ?></a></li><!-- /wp:list-item -->
			<!-- wp:list-item --><li><a href="<?php echo esc_url( home_url( '/news/' ) ); ?>"><?php esc_html_e( 'News', 'bwfd' ); ?></a></li><!-- /wp:list-item -->
		</ul>
		<!-- /wp:list -->

		<!-- wp:list {"className":"bwfd-footer-links","fontSize":"base"} -->
		<ul class="wp-block-list bwfd-footer-links has-base-font-size">
			<!-- wp:list-item --><li><a href="<?php echo esc_url( home_url( '/launch/' ) ); ?>"><?php esc_html_e( 'Launch event', 'bwfd' ); ?></a></li><!-- /wp:list-item -->
			<!-- wp:list-item --><li><a href="<?php echo esc_url( home_url( '/churches-and-retail/' ) ); ?>"><?php esc_html_e( 'Churches &amp; retail', 'bwfd' ); ?></a></li><!-- /wp:list-item -->
			<!-- wp:list-item --><li><a href="https://facebook.com/biblicalwisdomfordads" target="_blank" rel="noopener">Facebook</a></li><!-- /wp:list-item -->
			<!-- wp:list-item --><li><a href="https://instagram.com/biblicalwisdomfordads" target="_blank" rel="noopener">Instagram</a></li><!-- /wp:list-item -->
		</ul>
		<!-- /wp:list -->

		<!-- wp:group {"className":"bwfd-footer-muted","style":{"spacing":{"blockGap":"6px"},"typography":{"fontSize":"15px"},"layout":{"selfStretch":"fixed","flexSize":"20em"}},"layout":{"type":"flex","orientation":"vertical"}} -->
		<div class="wp-block-group bwfd-footer-muted" style="font-size:15px">
			<!-- wp:paragraph -->
			<p><?php esc_html_e( 'Published by Running Forever Press, Brisbane, Australia.', 'bwfd' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph -->
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> Stephen Parker. <?php esc_html_e( 'All rights reserved.', 'bwfd' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph -->
			<p><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy policy', 'bwfd' ); ?></a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
