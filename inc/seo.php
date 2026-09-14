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
 * Prices and postage describe the direct (Square) purchase of the paperback.
 * `release_date` drives offer availability: PreOrder before it, InStock after.
 *
 * @return array<string, mixed>
 */
function bwfd_book_data(): array {
	return apply_filters(
		'bwfd_book_data',
		array(
			'name'         => 'Biblical Wisdom for Dads',
			'author'       => 'Stephen Parker',
			'publisher'    => 'Running Forever Press',
			'foreword'     => 'Richard Blackaby',
			'isbn'         => '978-1-7635863-3-8',
			'isbn_ebook'   => '978-1-7635863-4-5',
			'isbn_audio'   => '978-1-7635863-5-2',
			'pages'        => 142,
			'language'     => 'en',
			'release_date' => '2026-10-27',
			'genre'        => 'Christian living',
			'audience'     => 'Christian fathers',
			'image'        => BWFD_URI . '/assets/images/bwfd-cover.webp',
			'image_width'  => 800,
			'image_height' => 1215,
			'description'  => 'In 40 short, practical chapters, dads discover how our Heavenly Father sets the ultimate example in compassion, strength, discipline, instruction and more.',
			'sku'          => 'BWFD-PB',
			'price'        => '24.99',
			'postage'      => '9.99',
			'currency'     => 'AUD',
			'ships_to'     => 'AU',
			'buy_url'      => 'https://square.link/u/dQKD6ORW',
			'social'       => array(
				'https://www.facebook.com/biblicalwisdomfordads',
				'https://www.instagram.com/biblicalwisdomfordads',
			),
		)
	);
}

/**
 * Author facts used for structured data. Filter `bwfd_author_data` to change.
 *
 * @return array<string, mixed>
 */
function bwfd_author_data(): array {
	return apply_filters(
		'bwfd_author_data',
		array(
			'name'         => 'Stephen Parker',
			'job_title'    => 'Author and Associate Professor',
			'employer'     => 'Australian College of Ministries',
			'email'        => 'stephen@biblicalwisdomfordads.au',
			'image'        => BWFD_URI . '/assets/images/stephen-headshot.webp',
			'image_width'  => 900,
			'image_height' => 1350,
			'description'  => 'Stephen Parker loves being a dad, and is fascinated by the wisdom found in the pages of Scripture. He is a husband, father, runner and loved child of God, with one wonderful wife and four delightful daughters. With 30 years of ministry experience, he is currently an Associate Professor at the Australian College of Ministries.',
			'same_as'      => array(
				'https://www.linkedin.com/in/stephendparkeraus',
				'https://www.facebook.com/Stephendparker',
			),
			'other_books'  => array(
				array(
					'name'  => 'There is No Finish: The Backyard Ultra Story',
					'image' => BWFD_URI . '/assets/images/no-finish-front.webp',
					'url'   => 'https://www.amazon.com/dp/B0DD42GD27',
				),
				array(
					'name'  => 'The Heart of an Elder',
					'image' => BWFD_URI . '/assets/images/heart-elder-front.webp',
					'url'   => 'https://amzn.to/3VhFBf1',
				),
			),
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
 * Offer availability for the paperback: PreOrder until the launch date
 * (with `availabilityStarts`), InStock from then on.
 *
 * @param array<string, mixed> $book Book data.
 * @return array{0:string,1:?string}
 */
function bwfd_book_availability( array $book ): array {
	$release = (string) ( $book['release_date'] ?? '' );
	if ( '' !== $release && $release > current_time( 'Y-m-d' ) ) {
		return array( 'https://schema.org/PreOrder', $release );
	}
	return array( 'https://schema.org/InStock', null );
}

/**
 * Endorsements on a page, read from its bwfd/endorsement blocks so the
 * structured data follows whatever editors write.
 *
 * @return array<int, array{text:string,name:string,role:string}>
 */
function bwfd_page_endorsements( WP_Post $post ): array {
	$found = array();

	$walk = static function ( array $blocks ) use ( &$walk, &$found ): void {
		foreach ( $blocks as $block ) {
			if ( 'bwfd/endorsement' === ( $block['blockName'] ?? '' ) ) {
				$html  = (string) $block['innerHTML'];
				$quote = preg_match( '#class="bwfd-endorsement__quote"[^>]*>(.*?)</p>#s', $html, $q ) ? $q[1] : '';
				$who   = preg_match( '#class="bwfd-endorsement__who"[^>]*>(.*?)</figcaption>#s', $html, $w ) ? $w[1] : '';

				$clean = static fn( string $text ): string => trim( preg_replace( '/\s+/u', ' ', html_entity_decode( wp_strip_all_tags( $text ), ENT_QUOTES, 'UTF-8' ) ) );
				$quote = $clean( $quote );
				$who   = rtrim( $clean( $who ), '.' );

				if ( '' === $quote || '' === $who ) {
					continue;
				}

				// "Name, role and organisation" → name plus role.
				$parts   = explode( ',', $who, 2 );
				$found[] = array(
					'text' => $quote,
					'name' => trim( $parts[0] ),
					'role' => isset( $parts[1] ) ? trim( $parts[1] ) : '',
				);
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
 * JSON-LD graph for the current request.
 *
 * Every page: Organization (publisher), WebSite and WebPage.
 * Book pages (Home, About the book, Purchase, Churches & retail): the Book
 * as a work with its three editions, the paperback edition doubling as a
 * Product (Google's "co-type Product with Book" guidance) carrying the
 * direct-purchase Offer, plus the author Person.
 * Endorsements on a book page are emitted as Quotation nodes about the
 * Book (not as reviews: Google requires a star rating on reviews, and
 * these have none).
 * About the author: WebPage becomes a ProfilePage whose mainEntity is the
 * author Person.
 * Other books: Book entries for the author's earlier titles.
 *
 * Note: Google's "Book actions" feature is fed by partner data feeds, not
 * on-page markup, so the Book nodes here are for general search engines
 * and knowledge-graph use; the Product and ProfilePage nodes target Google
 * rich results.
 */
function bwfd_seo_json_ld(): void {
	if ( is_admin() || is_feed() || is_embed() || is_404() ) {
		return;
	}

	$book   = bwfd_book_data();
	$author = bwfd_author_data();
	$home   = home_url( '/' );
	$ids    = array(
		'site'      => $home . '#website',
		'org'       => $home . '#organization',
		'person'    => $home . '#author',
		'book'      => $home . '#book',
		'paperback' => $home . '#book-paperback',
		'ebook'     => $home . '#book-ebook',
		'audiobook' => $home . '#book-audiobook',
	);
	$ref    = static fn( string $key ) => array( '@id' => $ids[ $key ] );

	$slug          = '';
	$post          = is_singular() ? get_queried_object() : null;
	if ( $post instanceof WP_Post ) {
		$slug = $post->post_name;
	}
	$is_book_page   = is_front_page() || in_array( $slug, array( 'about-the-book', 'purchase', 'churches-and-retail' ), true );
	$is_author_page = 'about-the-author' === $slug;
	$is_other_books = 'other-books' === $slug;

	$logo  = get_site_icon_url( 512 );
	$graph = array(
		array(
			'@type'  => 'Organization',
			'@id'    => $ids['org'],
			'name'   => $book['publisher'],
			'url'    => $home,
			'logo'   => $logo ?: null,
			'sameAs' => $book['social'],
		),
		array(
			'@type'       => 'WebSite',
			'@id'         => $ids['site'],
			'url'         => $home,
			'name'        => get_bloginfo( 'name' ),
			'description' => get_bloginfo( 'description' ),
			'publisher'   => $ref( 'org' ),
			'inLanguage'  => get_bloginfo( 'language' ),
		),
	);

	if ( $post instanceof WP_Post ) {
		$type = 'WebPage';
		if ( is_singular( 'post' ) ) {
			$type = 'Article';
		} elseif ( $is_author_page ) {
			$type = 'ProfilePage';
		}

		$page = array(
			'@type'              => $type,
			'@id'                => get_permalink() . '#webpage',
			'url'                => get_permalink(),
			'name'               => html_entity_decode( wp_get_document_title(), ENT_QUOTES, 'UTF-8' ),
			'headline'           => html_entity_decode( get_the_title(), ENT_QUOTES, 'UTF-8' ),
			'description'        => bwfd_seo_description(),
			'isPartOf'           => $ref( 'site' ),
			'inLanguage'         => get_bloginfo( 'language' ),
			'datePublished'      => get_the_date( DATE_W3C, $post ),
			'dateModified'       => get_the_modified_date( DATE_W3C, $post ),
			'primaryImageOfPage' => array(
				'@type' => 'ImageObject',
				'url'   => bwfd_seo_image()['url'],
			),
		);

		if ( $is_author_page ) {
			$page['dateCreated'] = get_the_date( DATE_W3C, $post );
			$page['mainEntity']  = $ref( 'person' );
		} elseif ( 'purchase' === $slug ) {
			$page['mainEntity'] = $ref( 'paperback' );
		} elseif ( $is_book_page && ! is_front_page() ) {
			$page['mainEntity'] = $ref( 'book' );
		}

		$graph[] = $page;
	}

	if ( $is_book_page || $is_author_page || $is_other_books ) {
		$graph[] = array(
			'@type'       => 'Person',
			'@id'         => $ids['person'],
			'name'        => $author['name'],
			'url'         => home_url( '/about-the-author/' ),
			'image'       => array(
				'@type'  => 'ImageObject',
				'url'    => $author['image'],
				'width'  => $author['image_width'],
				'height' => $author['image_height'],
			),
			'description' => $author['description'],
			'jobTitle'    => $author['job_title'],
			'worksFor'    => array(
				'@type' => 'Organization',
				'name'  => $author['employer'],
			),
			'email'       => $author['email'],
			'sameAs'      => $author['same_as'],
		);
	}

	if ( $is_book_page ) {
		$cover = array(
			'@type'  => 'ImageObject',
			'url'    => $book['image'],
			'width'  => $book['image_width'],
			'height' => $book['image_height'],
		);

		// The work, with the paperback referenced and the other editions inline.
		$graph[] = array(
			'@type'         => 'Book',
			'@id'           => $ids['book'],
			'name'          => $book['name'],
			'author'        => $ref( 'person' ),
			'contributor'   => array(
				'@type' => 'Person',
				'name'  => $book['foreword'],
			),
			'publisher'     => $ref( 'org' ),
			'url'           => home_url( '/about-the-book/' ),
			'image'         => $cover,
			'description'   => $book['description'],
			'datePublished' => $book['release_date'],
			'inLanguage'    => $book['language'],
			'numberOfPages' => $book['pages'],
			'genre'         => $book['genre'],
			'audience'      => array(
				'@type'        => 'PeopleAudience',
				'audienceType' => $book['audience'],
			),
			'workExample'   => array(
				$ref( 'paperback' ),
				array(
					'@type'      => 'Book',
					'@id'        => $ids['ebook'],
					'name'       => $book['name'],
					'author'     => $ref( 'person' ),
					'isbn'       => $book['isbn_ebook'],
					'bookFormat' => 'https://schema.org/EBook',
					'inLanguage' => $book['language'],
				),
				array(
					'@type'      => 'Book',
					'@id'        => $ids['audiobook'],
					'name'       => $book['name'],
					'author'     => $ref( 'person' ),
					'isbn'       => $book['isbn_audio'],
					'bookFormat' => 'https://schema.org/AudiobookFormat',
					'inLanguage' => $book['language'],
				),
			),
		);

		// The paperback edition, also a Product so Google can show price and availability.
		list( $availability, $available_from ) = bwfd_book_availability( $book );

		$offer = array(
			'@type'           => 'Offer',
			'url'             => home_url( '/purchase/' ),
			'price'           => $book['price'],
			'priceCurrency'   => $book['currency'],
			'availability'    => $availability,
			'itemCondition'   => 'https://schema.org/NewCondition',
			'eligibleRegion'  => array(
				'@type' => 'Country',
				'name'  => $book['ships_to'],
			),
			'seller'          => $ref( 'org' ),
			'shippingDetails' => array(
				'@type'               => 'OfferShippingDetails',
				'shippingRate'        => array(
					'@type'    => 'MonetaryAmount',
					'value'    => $book['postage'],
					'currency' => $book['currency'],
				),
				'shippingDestination' => array(
					'@type'          => 'DefinedRegion',
					'addressCountry' => $book['ships_to'],
				),
			),
		);
		if ( $available_from ) {
			$offer['availabilityStarts'] = $available_from;
		}

		$graph[] = array(
			'@type'         => array( 'Product', 'Book' ),
			'@id'           => $ids['paperback'],
			'name'          => $book['name'],
			'image'         => array( $book['image'] ),
			'description'   => $book['description'],
			'sku'           => $book['sku'],
			'isbn'          => $book['isbn'],
			'gtin13'        => preg_replace( '/\D+/', '', $book['isbn'] ),
			'brand'         => array(
				'@type' => 'Brand',
				'name'  => $book['publisher'],
			),
			'category'      => 'Media > Books',
			'author'        => $ref( 'person' ),
			'publisher'     => $ref( 'org' ),
			'bookFormat'    => 'https://schema.org/Paperback',
			'inLanguage'    => $book['language'],
			'numberOfPages' => $book['pages'],
			'datePublished' => $book['release_date'],
			'exampleOfWork' => $ref( 'book' ),
			'offers'        => $offer,
		);
	}

	if ( $is_book_page && $post instanceof WP_Post ) {
		foreach ( bwfd_page_endorsements( $post ) as $endorsement ) {
			$creator = array(
				'@type' => 'Person',
				'name'  => $endorsement['name'],
			);
			if ( '' !== $endorsement['role'] ) {
				$creator['jobTitle'] = $endorsement['role'];
			}
			$graph[] = array(
				'@type'   => 'Quotation',
				'text'    => $endorsement['text'],
				'creator' => $creator,
				'about'   => $ref( 'book' ),
			);
		}
	}

	if ( $is_other_books ) {
		foreach ( $author['other_books'] as $title ) {
			$graph[] = array(
				'@type'  => 'Book',
				'name'   => $title['name'],
				'author' => $ref( 'person' ),
				'image'  => $title['image'],
				'url'    => $title['url'],
			);
		}
	}

	// Drop null and empty values from each node.
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
			JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG
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
