<?php
/**
 * Title: Site header
 * Slug: bwfd/header
 * Categories: bwfd-sections
 * Block Types: core/template-part/header
 * Inserter: no
 *
 * @package bwfd
 */

declare( strict_types=1 );
?>
<!-- wp:group {"align":"full","className":"bwfd-header","style":{"spacing":{"padding":{"top":"0","bottom":"0"}}},"backgroundColor":"navy","textColor":"white","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull bwfd-header has-white-color has-navy-background-color has-text-color has-background" style="padding-top:0;padding-bottom:0">
	<!-- wp:group {"style":{"spacing":{"padding":{"top":"20px","bottom":"16px"},"blockGap":"14px 32px"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"bottom"}} -->
	<div class="wp-block-group" style="padding-top:20px;padding-bottom:16px">
		<!-- wp:paragraph {"className":"bwfd-brand"} -->
		<p class="bwfd-brand"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Biblical Wisdom', 'bwfd' ); ?> <mark style="color:#f8bb7c" class="has-inline-color"><?php esc_html_e( 'for Dads', 'bwfd' ); ?></mark></a></p>
		<!-- /wp:paragraph -->

		<!-- wp:navigation {"overlayMenu":"mobile","icon":"menu","overlayBackgroundColor":"navy","overlayTextColor":"white","style":{"spacing":{"blockGap":"20px"}},"layout":{"type":"flex","justifyContent":"right","flexWrap":"wrap"}} /-->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"bwfd-stripe","gradient":"brand-stripe","style":{"spacing":{"padding":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull bwfd-stripe has-brand-stripe-gradient-background has-background" style="padding-top:0;padding-bottom:0"></div>
<!-- /wp:group -->
