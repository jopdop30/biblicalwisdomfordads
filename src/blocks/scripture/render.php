<?php
/**
 * Scripture block – server render so the markup is shared with the
 * Featured insight block (inc/scripture.php).
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner content (unused).
 * @var WP_Block $block      Block instance.
 *
 * @package bwfd
 */

declare( strict_types=1 );

echo bwfd_scripture_markup( $attributes, get_block_wrapper_attributes( array( 'class' => 'bwfd-scripture' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in bwfd_scripture_markup().
