<?php
/**
 * Scripture helpers shared by the Scripture block, the Featured insight
 * block and the structured data.
 *
 * @package bwfd
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Normalised attributes for a scripture quotation.
 *
 * @param array<string, mixed> $attributes Block attributes.
 * @return array{text:string,reference:string,translation:string,link:bool}
 */
function bwfd_scripture_attributes( array $attributes ): array {
	return array(
		'text'        => trim( (string) ( $attributes['text'] ?? '' ) ),
		'reference'   => trim( (string) ( $attributes['reference'] ?? '' ) ),
		'translation' => trim( (string) ( $attributes['translation'] ?? '' ) ),
		'link'        => ! isset( $attributes['linkReference'] ) || (bool) $attributes['linkReference'],
	);
}

/**
 * Link to the passage on Bible Gateway, for the reference. Filter
 * `bwfd_scripture_link` to point somewhere else (or return '' for none).
 */
function bwfd_scripture_link( string $reference, string $translation = '' ): string {
	if ( '' === $reference ) {
		return '';
	}
	$args = array( 'search' => $reference );
	if ( '' !== $translation ) {
		$args['version'] = strtoupper( preg_replace( '/[^A-Za-z0-9]/', '', $translation ) );
	}
	$url = add_query_arg( array_map( 'rawurlencode', $args ), 'https://www.biblegateway.com/passage/' );
	return (string) apply_filters( 'bwfd_scripture_link', $url, $reference, $translation );
}

/**
 * Markup for a scripture quotation: the quote-mark disc, the verse and the
 * reference, matching the Insight design.
 *
 * @param array<string, mixed> $attributes Block attributes (text, reference, translation, linkReference).
 * @param string               $wrapper    Wrapper attributes already escaped (from get_block_wrapper_attributes()), or '' for a bare figure.
 */
function bwfd_scripture_markup( array $attributes, string $wrapper = '' ): string {
	$verse = bwfd_scripture_attributes( $attributes );
	if ( '' === $verse['text'] ) {
		return '';
	}

	$reference = '';
	if ( '' !== $verse['reference'] ) {
		$label = esc_html( $verse['reference'] );
		if ( '' !== $verse['translation'] ) {
			$label .= ' <span class="bwfd-scripture__translation">' . esc_html( $verse['translation'] ) . '</span>';
		}
		$link = $verse['link'] ? bwfd_scripture_link( $verse['reference'], $verse['translation'] ) : '';
		$reference = '' !== $link
			? sprintf( '<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>', esc_url( $link ), $label )
			: $label;
		$reference = '<figcaption class="bwfd-scripture__reference">' . $reference . '</figcaption>';
	}

	if ( '' === $wrapper ) {
		$wrapper = 'class="bwfd-scripture"';
	}

	return sprintf(
		'<figure %1$s><span class="bwfd-scripture__mark" aria-hidden="true">&ldquo;</span><blockquote class="bwfd-scripture__text"><p>%2$s</p></blockquote>%3$s</figure>',
		$wrapper,
		wp_kses( $verse['text'], array( 'em' => array(), 'strong' => array(), 'br' => array(), 'span' => array( 'class' => array() ) ) ),
		$reference
	);
}

/**
 * The first Scripture block in a post, or null. Used for the featured
 * reflection on the Insights archive and the article's citation in the
 * structured data.
 *
 * @return array{text:string,reference:string,translation:string,link:bool}|null
 */
function bwfd_post_scripture( WP_Post $post ): ?array {
	if ( ! has_block( 'bwfd/scripture', $post ) ) {
		return null;
	}
	$found = null;
	$walk  = static function ( array $blocks ) use ( &$walk, &$found ): void {
		foreach ( $blocks as $block ) {
			if ( null !== $found ) {
				return;
			}
			if ( 'bwfd/scripture' === ( $block['blockName'] ?? '' ) ) {
				$verse = bwfd_scripture_attributes( (array) ( $block['attrs'] ?? array() ) );
				if ( '' !== $verse['text'] ) {
					$found = $verse;
					return;
				}
			}
			if ( ! empty( $block['innerBlocks'] ) ) {
				$walk( $block['innerBlocks'] );
			}
		}
	};
	$walk( parse_blocks( $post->post_content ) );
	return $found;
}

/**
 * Media Library attachment for one of the theme's images, when the
 * provisioning script imported it (it records the source file in
 * `_bwfd_source`). Lets blocks serve a right-sized copy of the cover or the
 * author portrait instead of the full theme asset.
 */
function bwfd_theme_image_attachment( string $file ): int {
	static $cache = array();
	if ( isset( $cache[ $file ] ) ) {
		return $cache[ $file ];
	}
	$found = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'meta_key'       => '_bwfd_source', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => $file, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);
	$cache[ $file ] = $found ? (int) $found[0] : 0;
	return $cache[ $file ];
}

/**
 * The author's portrait as an <img>, sized for an avatar. Uses the Media
 * Library copy when the site has one, else the URL from the settings.
 */
function bwfd_author_avatar( int $size = 44, string $class = 'bwfd-avatar' ): string {
	$author = bwfd_schema_data()['author'];
	$id     = (int) ( $author['image']['id'] ?? 0 ) ?: bwfd_theme_image_attachment( 'stephen-headshot.webp' );
	$attrs  = array(
		'class'    => $class,
		'width'    => $size,
		'height'   => $size,
		'alt'      => '',
		'loading'  => 'lazy',
		'decoding' => 'async',
	);
	if ( $id ) {
		$img = wp_get_attachment_image( $id, 'thumbnail', false, $attrs );
		if ( '' !== $img ) {
			return $img;
		}
	}
	$url = (string) ( $author['image']['url'] ?? '' );
	if ( '' === $url ) {
		return '';
	}
	$html = '<img src="' . esc_url( $url ) . '"';
	foreach ( $attrs as $key => $value ) {
		$html .= ' ' . $key . '="' . esc_attr( (string) $value ) . '"';
	}
	return $html . '>';
}

/**
 * The book cover as an <img> at a small display width (sidebars and bands).
 */
function bwfd_cover_image( int $width, string $class = 'bwfd-cover-thumb' ): string {
	$cover = bwfd_schema_data()['book']['image'];
	$id    = (int) ( $cover['id'] ?? 0 );
	$ratio = ( (int) ( $cover['width'] ?? 0 ) > 0 && (int) ( $cover['height'] ?? 0 ) > 0 )
		? (int) $cover['height'] / (int) $cover['width']
		: 1.52;
	$attrs = array(
		'class'    => $class,
		'width'    => $width,
		'height'   => (int) round( $width * $ratio ),
		'alt'      => (string) bwfd_schema_data()['book']['name'],
		'loading'  => 'lazy',
		'decoding' => 'async',
		'sizes'    => $width . 'px',
	);
	if ( $id ) {
		$img = wp_get_attachment_image( $id, 'medium', false, $attrs );
		if ( '' !== $img ) {
			return $img;
		}
	}
	$url = (string) ( $cover['url'] ?? '' );
	if ( '' === $url ) {
		return '';
	}
	unset( $attrs['sizes'] );
	$html = '<img src="' . esc_url( $url ) . '"';
	foreach ( $attrs as $key => $value ) {
		$html .= ' ' . $key . '="' . esc_attr( (string) $value ) . '"';
	}
	return $html . '>';
}

/**
 * The Insight to feature: the one marked "Feature on the Insights page"
 * (post meta `bwfd_featured`, see inc/content-types.php), else the most
 * recent published Insight. Null when none is published.
 */
function bwfd_featured_insight(): ?WP_Post {
	static $featured = false;
	if ( false !== $featured ) {
		return $featured;
	}
	$base = array(
		'post_type'      => 'bwfd_insight',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'no_found_rows'  => true,
	);
	$posts = get_posts(
		$base + array(
			'meta_key'   => BWFD_FEATURED_META, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value' => '1', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	if ( ! $posts ) {
		$posts = get_posts( $base );
	}
	$featured = $posts ? $posts[0] : null;
	return $featured;
}
