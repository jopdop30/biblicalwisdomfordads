<?php
/**
 * Featured insight – the Insight marked "Feature on the Insights page",
 * else the latest, as a two-panel feature.
 *
 * The Insights archive excludes this post from its list (see
 * bwfd_insights_archive_query()), so it appears once, here. On page two
 * onwards nothing is rendered.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner content (unused).
 * @var WP_Block $block      Block instance.
 *
 * @package bwfd
 */

declare( strict_types=1 );

if ( is_paged() ) {
	return;
}

$bwfd_post = ! empty( $attributes['postId'] ) ? get_post( (int) $attributes['postId'] ) : bwfd_featured_insight();
if ( ! $bwfd_post instanceof WP_Post || 'publish' !== $bwfd_post->post_status || 'bwfd_insight' !== $bwfd_post->post_type ) {
	return;
}

$bwfd_kicker  = trim( (string) ( $attributes['kicker'] ?? '' ) );
$bwfd_more    = trim( (string) ( $attributes['moreLabel'] ?? '' ) ) ?: __( 'Read more', 'bwfd' );
$bwfd_verse   = bwfd_post_scripture( $bwfd_post );
$bwfd_link    = (string) get_permalink( $bwfd_post );
$bwfd_excerpt = get_the_excerpt( $bwfd_post );
$bwfd_author  = (string) bwfd_schema_data()['author']['name'];
$bwfd_image   = ! $bwfd_verse && has_post_thumbnail( $bwfd_post )
	? get_the_post_thumbnail( $bwfd_post, 'large', array( 'class' => 'bwfd-featured-insight__image', 'loading' => 'lazy' ) )
	: '';

$bwfd_classes = array( 'bwfd-featured-insight' );
if ( ! $bwfd_verse && '' === $bwfd_image ) {
	$bwfd_classes[] = 'bwfd-featured-insight--single';
}
$bwfd_wrapper = get_block_wrapper_attributes( array( 'class' => implode( ' ', $bwfd_classes ) ) );
?>
<article <?php echo $bwfd_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?>>
	<?php if ( $bwfd_verse ) : ?>
	<div class="bwfd-featured-insight__verse">
		<?php if ( '' !== $bwfd_kicker ) : ?>
		<span class="bwfd-featured-insight__kicker"><?php echo esc_html( $bwfd_kicker ); ?></span>
		<?php endif; ?>
		<blockquote class="bwfd-featured-insight__quote"><p><?php echo wp_kses( $bwfd_verse['text'], array( 'em' => array(), 'strong' => array(), 'br' => array() ) ); ?></p></blockquote>
		<?php if ( '' !== $bwfd_verse['reference'] ) : ?>
		<span class="bwfd-featured-insight__reference"><?php echo esc_html( $bwfd_verse['reference'] ); ?></span>
		<?php endif; ?>
	</div>
	<?php elseif ( '' !== $bwfd_image ) : ?>
	<a class="bwfd-featured-insight__media" href="<?php echo esc_url( $bwfd_link ); ?>" tabindex="-1" aria-hidden="true"><?php echo $bwfd_image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core image markup. ?></a>
	<?php endif; ?>
	<div class="bwfd-featured-insight__body">
		<?php if ( ! $bwfd_verse && '' !== $bwfd_kicker ) : ?>
		<span class="bwfd-featured-insight__kicker"><?php echo esc_html( $bwfd_kicker ); ?></span>
		<?php endif; ?>
		<time class="bwfd-featured-insight__date" datetime="<?php echo esc_attr( get_the_date( DATE_W3C, $bwfd_post ) ); ?>"><?php echo esc_html( get_the_date( '', $bwfd_post ) ); ?></time>
		<h2 class="bwfd-featured-insight__title"><a href="<?php echo esc_url( $bwfd_link ); ?>"><?php echo esc_html( get_the_title( $bwfd_post ) ); ?></a></h2>
		<?php if ( '' !== $bwfd_excerpt ) : ?>
		<p class="bwfd-featured-insight__excerpt"><?php echo esc_html( wp_strip_all_tags( $bwfd_excerpt ) ); ?></p>
		<?php endif; ?>
		<div class="bwfd-featured-insight__meta">
			<?php echo bwfd_author_avatar( 46, 'bwfd-featured-insight__avatar' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped values. ?>
			<span class="bwfd-featured-insight__author"><?php echo esc_html( $bwfd_author ); ?></span>
			<a class="bwfd-featured-insight__more" href="<?php echo esc_url( $bwfd_link ); ?>"><?php echo esc_html( $bwfd_more ); ?></a>
		</div>
	</div>
</article>
