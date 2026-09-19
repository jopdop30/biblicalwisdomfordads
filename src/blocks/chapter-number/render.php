<?php
/**
 * Chapter number – "Chapter 22" from the chapter post's Order field.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner content (unused).
 * @var WP_Block $block      Block instance.
 *
 * @package bwfd
 */

declare( strict_types=1 );

$bwfd_post = get_post( (int) ( $block->context['postId'] ?? get_the_ID() ) );
if ( ! $bwfd_post instanceof WP_Post ) {
	return;
}
$bwfd_label = bwfd_chapter_label( $bwfd_post, (string) ( $attributes['format'] ?? 'label' ) );
if ( '' === $bwfd_label ) {
	return;
}
printf(
	'<span %s>%s</span>',
	get_block_wrapper_attributes( array( 'class' => 'bwfd-chapter-number' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by core.
	esc_html( $bwfd_label )
);
