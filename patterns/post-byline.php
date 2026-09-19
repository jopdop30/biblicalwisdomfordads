<?php
/**
 * Title: Post byline
 * Slug: bwfd/post-byline
 * Categories: bwfd-sections
 * Block Types: core/post-date
 * Inserter: no
 * Description: The author's portrait, "By Stephen Parker" (from the structured data settings, linked to About the author) and the publication date. Used by the Insight template.
 *
 * @package bwfd
 */

declare( strict_types=1 );

$bwfd_byline_author = (string) bwfd_schema_data()['author']['name'];
$bwfd_byline_page   = (int) bwfd_schema_data()['pages']['about_author'];
$bwfd_byline_link   = $bwfd_byline_page ? get_permalink( $bwfd_byline_page ) : bwfd_url( '/about-the-author/' );
?>
<!-- wp:group {"className":"bwfd-byline","style":{"spacing":{"blockGap":"6px 14px"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group bwfd-byline">
	<!-- wp:html -->
	<?php echo bwfd_author_avatar( 44, 'bwfd-byline__avatar' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped values. ?>
	<!-- /wp:html -->
	<!-- wp:group {"className":"bwfd-byline__meta","style":{"spacing":{"blockGap":"0 6px"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
	<div class="wp-block-group bwfd-byline__meta">
		<?php if ( '' !== $bwfd_byline_author ) : ?>
		<!-- wp:paragraph {"className":"bwfd-byline__author","fontSize":"base"} -->
		<p class="bwfd-byline__author has-base-font-size"><?php
			printf(
				/* translators: %s: linked author name. */
				esc_html__( 'By %s', 'bwfd' ),
				'<a href="' . esc_url( (string) $bwfd_byline_link ) . '">' . esc_html( $bwfd_byline_author ) . '</a>'
			);
		?> &middot;</p>
		<!-- /wp:paragraph -->
		<?php endif; ?>
		<!-- wp:post-date {"className":"bwfd-byline__date","fontSize":"base"} /-->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
