<?php
/**
 * Front-end performance.
 *
 * Addresses what PageSpeed flags on the designed pages: the hero's
 * background image is the Largest Contentful Paint element but is only
 * discoverable from an inline style, the cover image ships at full size
 * to a 220px slot on phones, and the theme stylesheet blocks first paint.
 *
 * @package bwfd
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Inline the theme stylesheet with the block styles core already inlines,
 * so first paint does not wait on separate render-blocking CSS requests.
 * The default budget (40 KB) is raised so the theme sheet and core's
 * navigation sheet both fit.
 */
function bwfd_perf_inline_theme_style(): void {
	wp_style_add_data( 'bwfd-style', 'path', BWFD_DIR . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'bwfd_perf_inline_theme_style', 20 );

add_filter( 'styles_inline_size_limit', static fn(): int => 80000 );

/**
 * A 440px sub-size: twice the 220px the hero gives the cover on phones, so
 * high-density phones get a sharp image without the 800px original.
 * Existing uploads need `wp media regenerate --only-missing` to gain it.
 */
function bwfd_perf_image_sizes(): void {
	add_image_size( 'bwfd-framed-sm', 440, 0 );
}
add_action( 'after_setup_theme', 'bwfd_perf_image_sizes' );

/**
 * Attachment ID for an upload URL, cached because the lookup is a query
 * and the same cover appears on several pages.
 */
function bwfd_perf_attachment_id( string $url ): int {
	if ( '' === $url ) {
		return 0;
	}
	$key    = 'bwfd_att_' . md5( $url );
	$cached = get_transient( $key );
	if ( false !== $cached ) {
		return (int) $cached;
	}
	$id = (int) attachment_url_to_postid( $url );
	set_transient( $key, $id, $id ? MONTH_IN_SECONDS : DAY_IN_SECONDS );
	return $id;
}

/**
 * Clear the URL cache when an attachment changes or is deleted.
 */
function bwfd_perf_forget_attachment( int $post_id ): void {
	$url = wp_get_attachment_url( $post_id );
	if ( $url ) {
		delete_transient( 'bwfd_att_' . md5( $url ) );
	}
}
add_action( 'delete_attachment', 'bwfd_perf_forget_attachment' );
add_action( 'attachment_updated', 'bwfd_perf_forget_attachment' );

/**
 * Responsive sources for a framed image.
 *
 * The `sizes` mirror the framed-image and hero stylesheets: a framed image
 * is 100% wide up to its max-width; inside the hero it is capped at 220px
 * on phones and 260px on tablets (see src/blocks/hero/style.scss).
 *
 * @param int    $id        Attachment ID.
 * @param string $src       Rendered image URL.
 * @param int    $width     Rendered intrinsic width.
 * @param int    $height    Rendered intrinsic height.
 * @param string $max_width Block max-width (CSS length).
 * @param bool   $in_hero   Whether the block sits directly inside a hero.
 * @return array{srcset:string,sizes:string}|null
 */
function bwfd_perf_framed_image_sources( int $id, string $src, int $width, int $height, string $max_width, bool $in_hero ): ?array {
	$meta = wp_get_attachment_metadata( $id );
	if ( ! $meta ) {
		return null;
	}
	if ( ! $width || ! $height ) {
		$width  = (int) ( $meta['width'] ?? 0 );
		$height = (int) ( $meta['height'] ?? 0 );
	}
	if ( ! $width || ! $height ) {
		return null;
	}
	$srcset = wp_calculate_image_srcset( array( $width, $height ), $src, $meta, $id );
	if ( ! $srcset ) {
		return null;
	}
	if ( ! preg_match( '/^\d+(\.\d+)?(px|rem|em)$/', $max_width ) ) {
		$max_width = '360px';
	}
	$sizes = $in_hero
		? sprintf( '(max-width: 639px) 220px, (max-width: 899px) 260px, %s', $max_width )
		: sprintf( '(max-width: %1$s) 100vw, %1$s', $max_width );

	return array(
		'srcset' => $srcset,
		'sizes'  => $sizes,
	);
}

/**
 * Flag framed images that sit directly inside a hero so the render filter
 * can size them for the hero's column widths.
 *
 * @param array         $parsed_block The block being rendered.
 * @param array         $source_block Unmodified copy.
 * @param WP_Block|null $parent_block Parent block, if any.
 * @return array
 */
function bwfd_perf_mark_hero_children( array $parsed_block, array $source_block, ?WP_Block $parent_block ): array {
	if ( 'bwfd/framed-image' === ( $parsed_block['blockName'] ?? '' ) && $parent_block && 'bwfd/hero' === $parent_block->name ) {
		$parsed_block['attrs']['_inHero'] = true;
	}
	return $parsed_block;
}
add_filter( 'render_block_data', 'bwfd_perf_mark_hero_children', 10, 3 );

/**
 * Add srcset/sizes to framed images whose file lives in the Media Library.
 * The block saves a plain <img>, so core's srcset filter (which keys on a
 * wp-image-{id} class) does not apply to it.
 *
 * @param string $content Block HTML.
 * @param array  $block   Parsed block.
 * @return string
 */
function bwfd_perf_framed_image_srcset( string $content, array $block ): string {
	if ( '' === $content || str_contains( $content, ' srcset=' ) ) {
		return $content;
	}
	$processor = new WP_HTML_Tag_Processor( $content );
	if ( ! $processor->next_tag( 'img' ) ) {
		return $content;
	}
	$src   = (string) $processor->get_attribute( 'src' );
	$attrs = $block['attrs'] ?? array();
	$id    = (int) ( $attrs['id'] ?? 0 ) ?: bwfd_perf_attachment_id( $src );
	if ( ! $id ) {
		return $content;
	}
	$sources = bwfd_perf_framed_image_sources(
		$id,
		$src,
		(int) $processor->get_attribute( 'width' ),
		(int) $processor->get_attribute( 'height' ),
		(string) ( $attrs['maxWidth'] ?? '360px' ),
		! empty( $attrs['_inHero'] )
	);
	if ( ! $sources ) {
		return $content;
	}
	$processor->set_attribute( 'srcset', $sources['srcset'] );
	$processor->set_attribute( 'sizes', $sources['sizes'] );
	return $processor->get_updated_html();
}
add_filter( 'render_block_bwfd/framed-image', 'bwfd_perf_framed_image_srcset', 10, 2 );

/**
 * First hero block in a parsed block tree.
 *
 * @param array $blocks Parsed blocks.
 * @return array|null
 */
function bwfd_perf_find_hero( array $blocks ): ?array {
	foreach ( $blocks as $block ) {
		if ( 'bwfd/hero' === ( $block['blockName'] ?? '' ) ) {
			return $block;
		}
		if ( ! empty( $block['innerBlocks'] ) ) {
			$found = bwfd_perf_find_hero( $block['innerBlocks'] );
			if ( $found ) {
				return $found;
			}
		}
	}
	return null;
}

/**
 * Preload the hero's two images on pages that open with a hero.
 *
 * The silhouette artwork is a CSS background on the section, so without a
 * preload the browser only finds it after the stylesheet and inline style
 * are applied; PageSpeed measured 1.8 s of that "resource load delay" on
 * the Largest Contentful Paint. The cover is preloaded with the same
 * srcset/sizes the <img> gets so the browser fetches one candidate once.
 */
function bwfd_perf_preload_hero(): void {
	if ( ! is_singular() ) {
		return;
	}
	$post = get_queried_object();
	if ( ! $post instanceof WP_Post || ! has_block( 'bwfd/hero', $post ) ) {
		return;
	}
	$hero = bwfd_perf_find_hero( parse_blocks( $post->post_content ) );
	if ( ! $hero ) {
		return;
	}

	$background = (string) ( $hero['attrs']['backgroundUrl'] ?? '' );
	if ( '' === $background && preg_match( '#background-image:\s*url\(\s*[\'"]?([^\'")]+)#i', $hero['innerHTML'] ?? '', $m ) ) {
		$background = $m[1];
	}
	if ( '' !== $background ) {
		printf( '<link rel="preload" as="image" href="%s" fetchpriority="high">' . "\n", esc_url( $background ) );
	}

	foreach ( $hero['innerBlocks'] ?? array() as $inner ) {
		if ( 'bwfd/framed-image' !== ( $inner['blockName'] ?? '' ) ) {
			continue;
		}
		$processor = new WP_HTML_Tag_Processor( $inner['innerHTML'] ?? '' );
		if ( ! $processor->next_tag( 'img' ) ) {
			break;
		}
		$src = (string) $processor->get_attribute( 'src' );
		if ( '' === $src ) {
			break;
		}
		$attrs   = $inner['attrs'] ?? array();
		$id      = (int) ( $attrs['id'] ?? 0 ) ?: bwfd_perf_attachment_id( $src );
		$sources = $id ? bwfd_perf_framed_image_sources(
			$id,
			$src,
			(int) $processor->get_attribute( 'width' ),
			(int) $processor->get_attribute( 'height' ),
			(string) ( $attrs['maxWidth'] ?? '360px' ),
			true
		) : null;

		if ( $sources ) {
			printf(
				'<link rel="preload" as="image" href="%s" imagesrcset="%s" imagesizes="%s" fetchpriority="high">' . "\n",
				esc_url( $src ),
				esc_attr( $sources['srcset'] ),
				esc_attr( $sources['sizes'] )
			);
		} else {
			printf( '<link rel="preload" as="image" href="%s" fetchpriority="high">' . "\n", esc_url( $src ) );
		}
		break;
	}
}
add_action( 'wp_head', 'bwfd_perf_preload_hero', 1 );
