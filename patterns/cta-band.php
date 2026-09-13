<?php
/**
 * Title: Navy call-to-action band
 * Slug: bwfd/cta-band
 * Categories: bwfd-sections
 * Description: Full-width navy texture band with one large apricot button.
 *
 * @package bwfd
 */

declare( strict_types=1 );
?>
<!-- wp:group {"align":"full","className":"is-style-bwfd-navy","style":{"spacing":{"padding":{"top":"60px","bottom":"60px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull is-style-bwfd-navy" style="padding-top:60px;padding-bottom:60px">
	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
	<div class="wp-block-buttons">
		<!-- wp:button {"className":"is-style-bwfd-cta"} -->
		<div class="wp-block-button is-style-bwfd-cta"><a class="wp-block-button__link wp-element-button" href="<?php echo bwfd_url( '/about-the-book/' ); ?>">About the book &rarr;</a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
