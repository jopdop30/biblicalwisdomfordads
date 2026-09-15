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
		$cut = mb_substr( $text, 0, 158 );
		// End on a full sentence when one fills most of the space; otherwise
		// break on a word and mark the cut.
		$sentence = '';
		if ( preg_match_all( '/[.!?]["”’)]?(?=\s|$)/u', $cut, $ends, PREG_OFFSET_CAPTURE ) ) {
			$last     = end( $ends[0] );
			$sentence = substr( $cut, 0, $last[1] + strlen( $last[0] ) );
		}
		if ( mb_strlen( $sentence ) >= 90 ) {
			$text = $sentence;
		} else {
			$text = rtrim( mb_substr( $cut, 0, (int) mb_strrpos( $cut, ' ' ) ), " ,;:–-" );
			if ( ! preg_match( '/[.!?]$/u', $text ) ) {
				$text .= '…';
			}
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

	$cover = bwfd_schema_data()['book']['image'];
	return array(
		'url'    => (string) $cover['url'],
		'width'  => (int) $cover['width'],
		'height' => (int) $cover['height'],
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
 * Offer availability for the paperback: PreOrder until the release date
 * (with `availabilityStarts`), InStock from then on.
 *
 * @return array{0:string,1:?string}
 */
function bwfd_book_availability( string $release_date ): array {
	if ( '' !== $release_date && $release_date > current_time( 'Y-m-d' ) ) {
		return array( 'https://schema.org/PreOrder', $release_date );
	}
	return array( 'https://schema.org/InStock', null );
}

/**
 * Drop empty values from a graph node, recursively. A typed node left with
 * nothing but its @type is dropped too, and lists stay lists.
 *
 * @param mixed $value Node, list or scalar.
 * @return mixed Cleaned value, or null when nothing is left.
 */
function bwfd_schema_clean( $value ) {
	if ( ! is_array( $value ) ) {
		return ( null === $value || '' === $value ) ? null : $value;
	}
	$is_list = array_keys( $value ) === range( 0, count( $value ) - 1 );
	$out     = array();
	foreach ( $value as $key => $item ) {
		$item = bwfd_schema_clean( $item );
		if ( null !== $item ) {
			$out[ $key ] = $item;
		}
	}
	if ( array() === $out || ( isset( $out['@type'] ) && 1 === count( $out ) ) ) {
		return null;
	}
	return $is_list ? array_values( $out ) : $out;
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
 * MerchantReturnPolicy from the offer settings, or null when no return
 * window or policy page is set. Used on the Organization (Google's
 * placement for a standard policy) and repeated on the Offer.
 *
 * @param array<string, mixed> $offer Offer settings.
 * @return array<string, mixed>|null
 */
function bwfd_return_policy( array $offer ): ?array {
	$days = (int) $offer['return_days'];
	$link = (string) $offer['return_url'];
	if ( $days <= 0 && '' === $link ) {
		return null;
	}

	$fees = array(
		'free'     => 'https://schema.org/FreeReturn',
		'customer' => 'https://schema.org/ReturnFeesCustomerResponsibility',
		'fixed'    => 'https://schema.org/ReturnShippingFees',
	);
	$refunds = array(
		'full'             => 'https://schema.org/FullRefund',
		'full_or_exchange' => array( 'https://schema.org/FullRefund', 'https://schema.org/ExchangeRefund' ),
		'exchange'         => 'https://schema.org/ExchangeRefund',
		'credit'           => 'https://schema.org/StoreCreditRefund',
	);
	$fee_key   = (string) $offer['return_fees'];
	$fee       = $fees[ $fee_key ] ?? null;
	$is_fixed  = 'fixed' === $fee_key && '' !== $offer['return_postage'];
	$customer  = in_array( $fee_key, array( 'customer', 'fixed' ), true );

	return array(
		'@type'                    => 'MerchantReturnPolicy',
		'merchantReturnLink'       => $link,
		'applicableCountry'        => $offer['ships_to'],
		'returnPolicyCountry'      => $offer['ships_to'],
		'returnPolicyCategory'     => $days > 0 ? 'https://schema.org/MerchantReturnFiniteReturnWindow' : null,
		'merchantReturnDays'       => $days > 0 ? $days : null,
		'returnMethod'             => 'https://schema.org/ReturnByMail',
		'itemCondition'            => 'https://schema.org/NewCondition',
		'refundType'               => $refunds[ (string) $offer['refund_type'] ] ?? null,
		'returnFees'               => $fee,
		'returnShippingFeesAmount' => $is_fixed ? array(
			'@type'    => 'MonetaryAmount',
			'value'    => $offer['return_postage'],
			'currency' => $offer['currency'],
		) : null,
		'returnLabelSource'        => $customer ? 'https://schema.org/ReturnLabelCustomerResponsibility' : null,
		// Two cases from the policy: change of mind, and damaged or faulty.
		'customerRemorseReturnFees' => $fee,
		'itemDefectReturnFees'      => ! empty( $offer['return_defect_free'] ) ? 'https://schema.org/FreeReturn' : $fee,
	);
}

/**
 * JSON-LD graph for the current request. Facts come from Settings →
 * Structured data (bwfd_schema_data()); endorsements from the page's blocks.
 *
 * Every page: Organization (publisher), WebSite and WebPage.
 * Book pages (About the book, Purchase and any extra pages chosen in the
 * settings): the Book as a work with its three editions, the paperback
 * edition doubling as a Product (Google's "co-type Product with Book"
 * guidance) carrying the direct-purchase Offer, the author Person, and a
 * Quotation per endorsement (not reviews: Google requires a star rating on
 * reviews, and these have none).
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

	$data      = bwfd_schema_data();
	$book      = $data['book'];
	$offer     = $data['offer'];
	$publisher = $data['publisher'];
	$author    = $data['author'];
	$pages     = $data['pages'];

	$home = home_url( '/' );
	$ids  = array(
		'site'      => $home . '#website',
		'org'       => $home . '#organization',
		'person'    => $home . '#author',
		'book'      => $home . '#book',
		'paperback' => $home . '#book-paperback',
		'ebook'     => $home . '#book-ebook',
		'audiobook' => $home . '#book-audiobook',
	);
	$ref  = static fn( string $key ): array => array( '@id' => $ids[ $key ] );

	$positive   = static fn( $value ): ?int => (int) $value > 0 ? (int) $value : null;
	$image_node = static fn( array $image ): array => array(
		'@type'  => 'ImageObject',
		'url'    => (string) $image['url'],
		'width'  => $positive( $image['width'] ),
		'height' => $positive( $image['height'] ),
	);
	$editions   = $book['editions'];
	$page_url   = static function ( $page_id, string $fallback ): string {
		$link = $page_id ? get_permalink( (int) $page_id ) : false;
		return is_string( $link ) ? $link : $fallback;
	};

	$post    = is_singular() ? get_queried_object() : null;
	$post_id = $post instanceof WP_Post ? (int) $post->ID : 0;

	$book_page_ids  = array_map( 'intval', array_merge( array( $pages['about_book'], $pages['purchase'] ), (array) $pages['extra_book_pages'] ) );
	$is_book_page   = $post_id && in_array( $post_id, $book_page_ids, true );
	$is_author_page = $post_id && $post_id === (int) $pages['about_author'];
	$is_other_books = $post_id && $post_id === (int) $pages['other_books'];

	$book_url   = $page_url( $pages['about_book'], $home );
	$offer_url  = $page_url( $pages['purchase'], $book_url );
	$author_url = $page_url( $pages['about_author'], $home );

	$logo    = get_site_icon_url( 512 );
	$returns = bwfd_return_policy( $offer );
	$graph   = array(
		array(
			'@type'                   => 'Organization',
			'@id'                     => $ids['org'],
			'name'                    => $publisher['name'],
			'url'                     => $home,
			'logo'                    => $logo ?: null,
			'sameAs'                  => $publisher['same_as'],
			'hasMerchantReturnPolicy' => $returns,
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
		} elseif ( $post_id === (int) $pages['purchase'] ) {
			$page['mainEntity'] = $ref( 'paperback' );
		} elseif ( $post_id === (int) $pages['about_book'] ) {
			$page['mainEntity'] = $ref( 'book' );
		}

		$graph[] = $page;
	}

	if ( $is_book_page || $is_author_page || $is_other_books ) {
		$graph[] = array(
			'@type'       => 'Person',
			'@id'         => $ids['person'],
			'name'        => $author['name'],
			'url'         => $author_url,
			'image'       => $image_node( $author['image'] ),
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
			'url'           => $book_url,
			'image'         => $image_node( $book['image'] ),
			'description'   => $book['description'],
			'datePublished' => $book['release_date'],
			'inLanguage'    => $book['language'],
			'numberOfPages' => $positive( $book['pages'] ),
			'genre'         => $book['genre'],
			'audience'      => array(
				'@type'        => 'PeopleAudience',
				'audienceType' => $book['audience'],
			),
			'workExample'   => array(
				$ref( 'paperback' ),
				array(
					'@type'         => 'Book',
					'@id'           => $ids['ebook'],
					'name'          => $book['name'],
					'author'        => $ref( 'person' ),
					'isbn'          => $editions['ebook']['isbn'],
					'bookFormat'    => 'https://schema.org/EBook',
					'inLanguage'    => $book['language'],
					'url'           => $editions['ebook']['url'],
					'datePublished' => $editions['ebook']['release_date'],
				),
				array(
					'@type'         => 'Book',
					'@id'           => $ids['audiobook'],
					'name'          => $book['name'],
					'author'        => $ref( 'person' ),
					'isbn'          => $editions['audiobook']['isbn'],
					'bookFormat'    => 'https://schema.org/AudiobookFormat',
					'inLanguage'    => $book['language'],
					'url'           => $editions['audiobook']['url'],
					'datePublished' => $editions['audiobook']['release_date'],
					'readBy'        => array(
						'@type' => 'Person',
						'name'  => $editions['audiobook']['narrator'],
					),
					'duration'      => $positive( $editions['audiobook']['minutes'] )
						? sprintf( 'PT%dH%dM', intdiv( (int) $editions['audiobook']['minutes'], 60 ), (int) $editions['audiobook']['minutes'] % 60 )
						: null,
				),
			),
		);

		// The paperback edition, also a Product so Google can show price and availability.
		list( $availability, $available_from ) = bwfd_book_availability( (string) $book['release_date'] );

		$delivery = null;
		if ( $positive( $offer['handling_max'] ) || $positive( $offer['transit_max'] ) ) {
			$window   = static fn( $min, $max ): ?array => $positive( $max ) ? array(
				'@type'    => 'QuantitativeValue',
				'minValue' => (int) $min,
				'maxValue' => (int) $max,
				'unitCode' => 'DAY',
			) : null;
			$delivery = array(
				'@type'        => 'ShippingDeliveryTime',
				'handlingTime' => $window( $offer['handling_min'], $offer['handling_max'] ),
				'transitTime'  => $window( $offer['transit_min'], $offer['transit_max'] ),
			);
		}

		$offer_node = array(
			'@type'              => 'Offer',
			'url'                => $offer_url,
			'price'              => $offer['price'],
			'priceCurrency'      => $offer['currency'],
			'priceValidUntil'    => $offer['price_valid_until'],
			'availability'       => $availability,
			'availabilityStarts' => $available_from,
			'itemCondition'      => 'https://schema.org/NewCondition',
			'hasMerchantReturnPolicy' => $returns,
			'eligibleRegion'     => array(
				'@type' => 'Country',
				'name'  => $offer['ships_to'],
			),
			'seller'             => $ref( 'org' ),
			'shippingDetails'    => array(
				'@type'               => 'OfferShippingDetails',
				'shippingRate'        => array(
					'@type'    => 'MonetaryAmount',
					'value'    => $offer['postage'],
					'currency' => $offer['currency'],
				),
				'shippingDestination' => array(
					'@type'          => 'DefinedRegion',
					'addressCountry' => $offer['ships_to'],
				),
				'deliveryTime'        => $delivery,
			),
		);

		$graph[] = array(
			'@type'           => array( 'Product', 'Book' ),
			'@id'             => $ids['paperback'],
			'name'            => $book['name'],
			'image'           => array( (string) $book['image']['url'] ),
			'description'     => $book['description'],
			'sku'             => $offer['sku'],
			'isbn'            => $editions['paperback']['isbn'],
			'gtin13'          => preg_replace( '/\D+/', '', (string) $editions['paperback']['isbn'] ),
			'sameAs'          => $editions['paperback']['url'],
			'brand'           => array(
				'@type' => 'Brand',
				'name'  => $publisher['name'],
			),
			'category'        => 'Media > Books',
			'author'          => $ref( 'person' ),
			'publisher'       => $ref( 'org' ),
			'bookFormat'      => 'https://schema.org/Paperback',
			'inLanguage'      => $book['language'],
			'numberOfPages'   => $positive( $book['pages'] ),
			'datePublished'   => $book['release_date'],
			'exampleOfWork'   => $ref( 'book' ),
			'offers'          => $offer_node,
			'potentialAction' => array(
				'@type'  => 'BuyAction',
				'target' => $offer['buy_url'],
			),
		);

		if ( $post instanceof WP_Post ) {
			foreach ( bwfd_page_endorsements( $post ) as $endorsement ) {
				$graph[] = array(
					'@type'   => 'Quotation',
					'text'    => $endorsement['text'],
					'creator' => array(
						'@type'    => 'Person',
						'name'     => $endorsement['name'],
						'jobTitle' => $endorsement['role'],
					),
					'about'   => $ref( 'book' ),
				);
			}
		}
	}

	if ( $is_other_books ) {
		foreach ( (array) $author['other_books'] as $title ) {
			$graph[] = array(
				'@type'  => 'Book',
				'name'   => $title['name'] ?? '',
				'author' => $ref( 'person' ),
				'isbn'   => $title['isbn'] ?? '',
				'image'  => (string) ( $title['image']['url'] ?? '' ),
				'url'    => $title['url'] ?? '',
			);
		}
	}

	$graph = array_values( array_filter( array_map( 'bwfd_schema_clean', $graph ) ) );

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
 * shortlink, no RSD/WLW manifests, no comment feed links.
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
	// Comment feeds: the site has no comments. The posts feed link stays.
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	add_filter( 'feed_links_show_comments_feed', '__return_false' );
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

/* -------------------------------------------------------------------------
 * Crawl controls
 * ---------------------------------------------------------------------- */

/**
 * Core sitemaps answer with a 404 status while the site has no published
 * posts: the main query behind a sitemap request finds nothing, WordPress
 * marks the request not found, and the XML is then sent under that status.
 * Sitemap requests never need the not-found check, so skip it for them.
 *
 * @param bool|mixed $preempt Non-false short-circuits handle_404().
 * @return bool|mixed
 */
function bwfd_seo_sitemap_status( $preempt ) {
	global $wp_query;

	$sitemap    = (string) get_query_var( 'sitemap' );
	$stylesheet = (string) get_query_var( 'sitemap-stylesheet' );
	if ( '' === $sitemap && '' === $stylesheet ) {
		return $preempt;
	}

	$server = wp_sitemaps_get_server();
	$known  = '' !== $stylesheet || 'index' === $sitemap || $server->registry->get_provider( $sitemap );
	if ( $known && $server->sitemaps_enabled() ) {
		return true;
	}

	// An unknown sitemap name would otherwise render the homepage at that URL.
	$wp_query->set_404();
	status_header( 404 );
	nocache_headers();
	return true;
}
add_filter( 'pre_handle_404', 'bwfd_seo_sitemap_status' );

/**
 * No users sitemap: author archives redirect home (below), so listing them
 * would only advertise usernames.
 *
 * @param WP_Sitemaps_Provider|false $provider Provider.
 * @param string                     $name     Provider name.
 * @return WP_Sitemaps_Provider|false
 */
function bwfd_seo_sitemap_providers( $provider, string $name ) {
	return 'users' === $name ? false : $provider;
}
add_filter( 'wp_sitemaps_add_provider', 'bwfd_seo_sitemap_providers', 10, 2 );

/**
 * Author archives carry nothing but the admin username in their URL and
 * title. Send them (and ?author=N lookups) to the homepage.
 */
function bwfd_seo_redirect_author_archives(): void {
	if ( is_author() ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'bwfd_seo_redirect_author_archives', 0 );

/**
 * Keep thin or duplicate views out of the index: search results, date
 * archives, attachment pages, not-found pages, and term archives that have
 * nothing in them yet. Links on those pages are still followed.
 *
 * @param array<string, bool|string> $robots Robots directives.
 * @return array<string, bool|string>
 */
function bwfd_seo_robots( array $robots ): array {
	global $wp_query;

	$empty_archive = ( is_category() || is_tag() || is_tax() ) && 0 === (int) $wp_query->post_count;

	if ( is_search() || is_date() || is_author() || is_attachment() || is_404() || $empty_archive ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
		unset( $robots['index'], $robots['nofollow'] );
	}
	return $robots;
}
add_filter( 'wp_robots', 'bwfd_seo_robots' );

/* -------------------------------------------------------------------------
 * Search titles
 * ---------------------------------------------------------------------- */

/**
 * `bwfd_seo_title` post meta: a hand-written <title> for search results.
 * Edited in the "Search appearance" panel (src/admin/seo-panel.js). The
 * meta description comes from the Excerpt panel, see bwfd_seo_description().
 */
function bwfd_seo_register_title_meta(): void {
	foreach ( array( 'page', 'post' ) as $post_type ) {
		register_post_meta(
			$post_type,
			'bwfd_seo_title',
			array(
				'type'              => 'string',
				'single'            => true,
				'default'           => '',
				'description'       => __( 'Title shown in search results. Replaces the whole title tag.', 'bwfd' ),
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => static fn(): bool => current_user_can( 'edit_posts' ),
				'show_in_rest'      => true,
			)
		);
	}
}
add_action( 'init', 'bwfd_seo_register_title_meta' );

/**
 * Use the search title, when one is set, as the whole document title.
 *
 * @param array<string, string> $parts Title parts.
 * @return array<string, string>
 */
function bwfd_seo_document_title( array $parts ): array {
	if ( is_singular() ) {
		$custom = trim( (string) get_post_meta( get_queried_object_id(), 'bwfd_seo_title', true ) );
		if ( '' !== $custom ) {
			return array( 'title' => $custom );
		}
	}
	return $parts;
}
add_filter( 'document_title_parts', 'bwfd_seo_document_title' );

/**
 * The "Search appearance" document panel in the post editor.
 */
function bwfd_seo_editor_assets(): void {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || ! in_array( (string) $screen->post_type, array( 'page', 'post' ), true ) ) {
		return;
	}
	$asset_file = BWFD_DIR . '/build/admin/seo-panel.asset.php';
	if ( ! file_exists( $asset_file ) ) {
		return;
	}
	$asset = require $asset_file;

	wp_enqueue_script(
		'bwfd-seo-panel',
		BWFD_URI . '/build/admin/seo-panel.js',
		$asset['dependencies'],
		$asset['version'],
		true
	);
	wp_add_inline_script(
		'bwfd-seo-panel',
		'window.bwfdSeoPanel = ' . wp_json_encode(
			array(
				'siteName'  => get_bloginfo( 'name' ),
				'separator' => apply_filters( 'document_title_separator', '-' ),
			)
		) . ';',
		'before'
	);
}
add_action( 'enqueue_block_editor_assets', 'bwfd_seo_editor_assets' );

/* -------------------------------------------------------------------------
 * Response headers
 * ---------------------------------------------------------------------- */

/**
 * Baseline security headers on front-end responses. HSTS is left to
 * Cloudflare, where it can be enabled and rolled back without a deploy.
 */
function bwfd_seo_security_headers(): void {
	if ( headers_sent() ) {
		return;
	}
	header( 'X-Content-Type-Options: nosniff' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()' );
	if ( ! is_embed() ) {
		header( 'X-Frame-Options: SAMEORIGIN' );
	}
}
add_action( 'send_headers', 'bwfd_seo_security_headers' );
