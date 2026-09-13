<?php
/**
 * Technical SEO: meta description, Open Graph / Twitter cards, JSON-LD,
 * and a few performance trims. No content is changed; descriptions come
 * from the page excerpt (editable in the editor) or the page's own text.
 *
 * @package bwfd
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Pages get an Excerpt panel so editors can write meta descriptions.
 */
function bwfd_seo_page_excerpts(): void {
	add_post_type_support( 'page', 'excerpt' );
}
add_action( 'init', 'bwfd_seo_page_excerpts' );

/**
 * Book facts used for structured data. Filter `bwfd_book_data` to change.
 *
 * @return array<string, mixed>
 */
function bwfd_book_data(): array {
	return apply_filters(
		'bwfd_book_data',
		array(
			'name'        => 'Biblical Wisdom for Dads',
			'author'      => 'Stephen Parker',
			'publisher'   => 'Running Forever Press',
			'isbn'        => '978-1-7635863-3-8',
			'isbn_ebook'  => '978-1-7635863-4-5',
			'isbn_audio'  => '978-1-7635863-5-2',
			'pages'       => 142,
			'language'    => 'en',
			'image'       => BWFD_URI . '/assets/images/bwfd-cover.webp',
			'description' => 'In 40 short, practical chapters, dads discover how our Heavenly Father sets the ultimate example in compassion, strength, discipline, instruction and more.',
		)
	);
}

/**
 * Description for the current request: excerpt, else the first substantial
 * paragraphs of the content, else the site tagline.
 */
function bwfd_seo_description(): string {
	$text = '';

	if ( is_singular() ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post ) {
			if ( has_excerpt( $post ) ) {
				$text = $post->post_excerpt;
			} else {
				$html   = apply_filters( 'the_content', $post->post_content );
				$blocks = array();
				if ( preg_match_all( '#<p\b[^>]*>(.*?)</p>#si', $html, $m ) ) {
					foreach ( $m[1] as $paragraph ) {
						$plain = trim( wp_strip_all_tags( $paragraph ) );
						// Skip kickers, labels and other very short lines.
						if ( mb_strlen( $plain ) >= 60 ) {
							$blocks[] = $plain;
						}
						if ( mb_strlen( implode( ' ', $blocks ) ) >= 160 ) {
							break;
						}
					}
				}
				$text = implode( ' ', $blocks );
			}
		}
	} elseif ( is_search() ) {
		$text = sprintf( 'Search results for "%s".', get_search_query() );
	} elseif ( is_archive() ) {
		$text = wp_strip_all_tags( get_the_archive_description() );
	}

	if ( '' === trim( $text ) ) {
		$text = get_bloginfo( 'description', 'display' );
	}

	$text = html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );
	$text = preg_replace( '/\s+/u', ' ', trim( $text ) );

	if ( mb_strlen( $text ) > 158 ) {
		$cut  = mb_substr( $text, 0, 158 );
		$text = rtrim( mb_substr( $cut, 0, (int) mb_strrpos( $cut, ' ' ) ), " ,;:–-" );
		if ( ! preg_match( '/[.!?]$/u', $text ) ) {
			$text .= '…';
		}
	}

	return $text;
}

/**
 * Social share image: featured image, else the first image in the content,
 * else the book cover.
 *
 * @return array{url:string,width:int,height:int}
 */
function bwfd_seo_image(): array {
	if ( is_singular() && has_post_thumbnail() ) {
		$src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
		if ( $src ) {
			return array(
				'url'    => $src[0],
				'width'  => (int) $src[1],
				'height' => (int) $src[2],
			);
		}
	}

	if ( is_singular() ) {
		$post = get_queried_object();
		// Prefer a framed image (book cover or portrait) over incidental images such as logos.
		$pattern = '#<figure class="[^"]*bwfd-framed-image[^"]*"[^>]*>.*?(<img[^>]+src="([^"]+)"[^>]*>)#is';
		if ( $post instanceof WP_Post && preg_match( $pattern, $post->post_content, $fm ) ) {
			$m = array( $fm[1], $fm[2] );
		} elseif ( $post instanceof WP_Post && ! preg_match( '#<img[^>]+src="([^"]+)"[^>]*>#i', $post->post_content, $m ) ) {
			$m = null;
		}
		if ( $m ) {
			$attrs = array( 'url' => $m[1], 'width' => 0, 'height' => 0 );
			if ( preg_match( '#width="(\d+)"#', $m[0], $w ) && preg_match( '#height="(\d+)"#', $m[0], $h ) ) {
				$attrs['width']  = (int) $w[1];
				$attrs['height'] = (int) $h[1];
			}
			return $attrs;
		}
	}

	return array(
		'url'    => bwfd_book_data()['image'],
		'width'  => 800,
		'height' => 1215,
	);
}

/**
 * Print description, Open Graph and Twitter tags.
 */
function bwfd_seo_meta(): void {
	if ( is_admin() || is_feed() || is_embed() ) {
		return;
	}

	$description = bwfd_seo_description();
	$title       = wp_get_document_title();
	$url         = is_singular() ? get_permalink() : home_url( add_query_arg( array(), $GLOBALS['wp']->request ) );
	$image       = bwfd_seo_image();
	$type        = is_front_page() ? 'website' : ( is_singular( 'post' ) ? 'article' : 'website' );
	$locale      = str_replace( '-', '_', get_bloginfo( 'language' ) );

	$tags = array(
		array( 'name', 'description', $description ),
		array( 'property', 'og:type', $type ),
		array( 'property', 'og:site_name', get_bloginfo( 'name' ) ),
		array( 'property', 'og:locale', $locale ),
		array( 'property', 'og:title', $title ),
		array( 'property', 'og:description', $description ),
		array( 'property', 'og:url', $url ),
		array( 'property', 'og:image', $image['url'] ),
		array( 'name', 'twitter:card', 'summary_large_image' ),
		array( 'name', 'twitter:title', $title ),
		array( 'name', 'twitter:description', $description ),
		array( 'name', 'twitter:image', $image['url'] ),
	);
	if ( $image['width'] && $image['height'] ) {
		$tags[] = array( 'property', 'og:image:width', (string) $image['width'] );
		$tags[] = array( 'property', 'og:image:height', (string) $image['height'] );
	}
	if ( is_singular( 'post' ) ) {
		$tags[] = array( 'property', 'article:published_time', get_the_date( DATE_W3C ) );
		$tags[] = array( 'property', 'article:modified_time', get_the_modified_date( DATE_W3C ) );
	}

	echo "\n<!-- Biblical Wisdom for Dads: SEO -->\n";
	foreach ( $tags as list( $attr, $key, $value ) ) {
		if ( '' === (string) $value ) {
			continue;
		}
		printf( '<meta %s="%s" content="%s">' . "\n", esc_attr( $attr ), esc_attr( $key ), esc_attr( $value ) );
	}
}
add_action( 'wp_head', 'bwfd_seo_meta', 2 );

/**
 * JSON-LD: WebSite and Organization on every page; the Book on the front
 * page and the About the book page; WebPage for the current request.
 */
function bwfd_seo_json_ld(): void {
	if ( is_admin() || is_feed() || is_embed() || is_404() ) {
		return;
	}

	$book    = bwfd_book_data();
	$site_id = home_url( '/#website' );
	$org_id  = home_url( '/#organization' );
	$logo    = get_site_icon_url( 512 );

	$graph = array(
		array(
			'@type'    => 'Organization',
			'@id'      => $org_id,
			'name'     => $book['publisher'],
			'url'      => home_url( '/' ),
			'logo'     => $logo ?: null,
			'sameAs'   => array(
				'https://www.facebook.com/biblicalwisdomfordads',
				'https://www.instagram.com/biblicalwisdomfordads',
			),
		),
		array(
			'@type'       => 'WebSite',
			'@id'         => $site_id,
			'url'         => home_url( '/' ),
			'name'        => get_bloginfo( 'name' ),
			'description' => get_bloginfo( 'description' ),
			'publisher'   => array( '@id' => $org_id ),
			'inLanguage'  => get_bloginfo( 'language' ),
		),
	);

	if ( is_singular() ) {
		$post    = get_queried_object();
		$graph[] = array(
			'@type'         => is_singular( 'post' ) ? 'Article' : 'WebPage',
			'@id'           => get_permalink() . '#webpage',
			'url'           => get_permalink(),
			'name'          => wp_get_document_title(),
			'headline'      => get_the_title(),
			'description'   => bwfd_seo_description(),
			'isPartOf'      => array( '@id' => $site_id ),
			'inLanguage'    => get_bloginfo( 'language' ),
			'datePublished' => get_the_date( DATE_W3C, $post ),
			'dateModified'  => get_the_modified_date( DATE_W3C, $post ),
			'primaryImageOfPage' => array(
				'@type' => 'ImageObject',
				'url'   => bwfd_seo_image()['url'],
			),
		);
	}

	$is_book_page = is_front_page() || ( is_page() && 'about-the-book' === get_queried_object()->post_name );
	if ( $is_book_page ) {
		$graph[] = array(
			'@type'         => 'Book',
			'@id'           => home_url( '/#book' ),
			'name'          => $book['name'],
			'author'        => array(
				'@type' => 'Person',
				'name'  => $book['author'],
				'url'   => home_url( '/about-the-author/' ),
			),
			'publisher'     => array( '@id' => $org_id ),
			'isbn'          => $book['isbn'],
			'numberOfPages' => $book['pages'],
			'inLanguage'    => $book['language'],
			'image'         => $book['image'],
			'description'   => $book['description'],
			'bookFormat'    => 'https://schema.org/Paperback',
			'url'           => home_url( '/about-the-book/' ),
			'workExample'   => array(
				array(
					'@type'      => 'Book',
					'isbn'       => $book['isbn_ebook'],
					'bookFormat' => 'https://schema.org/EBook',
				),
				array(
					'@type'      => 'Book',
					'isbn'       => $book['isbn_audio'],
					'bookFormat' => 'https://schema.org/AudiobookFormat',
				),
			),
		);
	}

	if ( is_page() && 'about-the-author' === get_queried_object()->post_name ) {
		$graph[] = array(
			'@type'    => 'Person',
			'@id'      => home_url( '/about-the-author/#person' ),
			'name'     => $book['author'],
			'url'      => get_permalink(),
			'jobTitle' => 'Author',
			'email'    => 'mailto:stephen@biblicalwisdomfordads.au',
		);
	}

	// Drop null values.
	$graph = array_map(
		static fn( array $node ): array => array_filter( $node, static fn( $v ) => null !== $v && '' !== $v ),
		$graph
	);

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode(
			array(
				'@context' => 'https://schema.org',
				'@graph'   => $graph,
			),
			JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
		)
	);
}
add_action( 'wp_head', 'bwfd_seo_json_ld', 3 );

/**
 * Trim what the site sends: no emoji script/styles, no generator tag, no
 * shortlink, no RSD/WLW manifests. The Facebook embed keeps its own origin.
 */
function bwfd_seo_trim_head(): void {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'emoji_svg_url', '__return_false' );

	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
}
add_action( 'init', 'bwfd_seo_trim_head' );

/**
 * Preconnect to Facebook for the page-plugin embed, only where it is used.
 */
function bwfd_seo_resource_hints( array $urls, string $relation ): array {
	if ( 'preconnect' === $relation && is_singular() && has_block( 'bwfd/facebook-page' ) ) {
		$urls[] = array( 'href' => 'https://www.facebook.com', 'crossorigin' => false );
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'bwfd_seo_resource_hints', 10, 2 );

/**
 * Generate WebP sub-sizes for uploaded JPEG and PNG images.
 *
 * @param array $formats Output format map.
 * @return array
 */
function bwfd_seo_webp_uploads( array $formats ): array {
	$formats['image/jpeg'] = 'image/webp';
	$formats['image/png']  = 'image/webp';
	return $formats;
}
add_filter( 'image_editor_output_format', 'bwfd_seo_webp_uploads' );

/**
 * The hero cover is the largest above-the-fold image on the front page:
 * preload it so the browser fetches it before parsing the content.
 */
function bwfd_seo_preload_hero(): void {
	if ( ! is_front_page() || ! is_singular() ) {
		return;
	}
	$post = get_queried_object();
	if ( ! $post instanceof WP_Post || ! has_block( 'bwfd/hero', $post ) ) {
		return;
	}
	if ( preg_match( '#<section[^>]*bwfd-hero[^>]*>.*?<img[^>]+src="([^"]+)"#si', $post->post_content, $m ) ) {
		printf( '<link rel="preload" as="image" href="%s" fetchpriority="high">' . "\n", esc_url( $m[1] ) );
	}
}
add_action( 'wp_head', 'bwfd_seo_preload_hero', 1 );
