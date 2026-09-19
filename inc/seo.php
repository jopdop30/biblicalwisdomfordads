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
	} elseif ( is_tax( BWFD_TOPIC_TAX ) && get_queried_object() instanceof WP_Term ) {
		$text = bwfd_topic_archive_description( get_queried_object() );
	} elseif ( is_archive() ) {
		$key  = is_post_type_archive() ? bwfd_archive_settings_key( (string) get_query_var( 'post_type' ) ) : '';
		$text = '' !== $key ? (string) ( bwfd_schema_data()['archives'][ $key ]['description'] ?? '' ) : '';
		if ( '' === trim( $text ) ) {
			$text = wp_strip_all_tags( get_the_archive_description() );
		}
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
 * The default share image: the cover on the navy texture at 1200×630,
 * rendered by bin/make-social-images.php. Landscape, because Facebook,
 * LinkedIn and X crop a portrait cover badly. Filter `bwfd_seo_default_image`.
 *
 * @return array{url:string,width:int,height:int,alt:string}
 */
function bwfd_seo_default_image(): array {
	return (array) apply_filters(
		'bwfd_seo_default_image',
		array(
			'url'    => BWFD_URI . '/assets/images/social-default.jpg',
			'width'  => 1200,
			'height' => 630,
			'alt'    => (string) bwfd_schema_data()['book']['name'],
		)
	);
}

/**
 * The default article images in the three aspect ratios Google's Article
 * guidance recommends, for items without a featured image.
 *
 * @return string[]
 */
function bwfd_seo_default_article_images(): array {
	return (array) apply_filters(
		'bwfd_seo_default_article_images',
		array(
			BWFD_URI . '/assets/images/article-16x9.jpg',
			BWFD_URI . '/assets/images/article-4x3.jpg',
			BWFD_URI . '/assets/images/article-1x1.jpg',
		)
	);
}

/**
 * Social share image: featured image, else the first image in the content,
 * else the landscape default.
 *
 * @return array{url:string,width:int,height:int,alt:string}
 */
function bwfd_seo_image(): array {
	if ( is_singular() && has_post_thumbnail() ) {
		$id  = get_post_thumbnail_id();
		$src = wp_get_attachment_image_src( $id, 'large' );
		if ( $src ) {
			return array(
				'url'    => $src[0],
				'width'  => (int) $src[1],
				'height' => (int) $src[2],
				'alt'    => trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) ) ?: get_the_title(),
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
			$attrs = array( 'url' => $m[1], 'width' => 0, 'height' => 0, 'alt' => '' );
			if ( preg_match( '#width="(\d+)"#', $m[0], $w ) && preg_match( '#height="(\d+)"#', $m[0], $h ) ) {
				$attrs['width']  = (int) $w[1];
				$attrs['height'] = (int) $h[1];
			}
			if ( preg_match( '#alt="([^"]*)"#', $m[0], $a ) ) {
				$attrs['alt'] = html_entity_decode( $a[1], ENT_QUOTES, 'UTF-8' );
			}
			return $attrs;
		}
	}

	return bwfd_seo_default_image();
}

/**
 * Canonical-style URL of the current request: the permalink for singular
 * views, the archive link for a post type archive, otherwise the request
 * path with the site's trailing-slash convention.
 */
function bwfd_seo_current_url(): string {
	if ( is_singular() ) {
		return (string) get_permalink();
	}
	if ( is_post_type_archive() && ! is_paged() ) {
		$link = get_post_type_archive_link( (string) get_query_var( 'post_type' ) );
		if ( is_string( $link ) ) {
			return $link;
		}
	}
	if ( is_tax() && ! is_paged() && get_queried_object() instanceof WP_Term ) {
		$link = get_term_link( get_queried_object() );
		if ( is_string( $link ) ) {
			return $link;
		}
	}
	$request = (string) $GLOBALS['wp']->request;
	return '' === $request ? home_url( '/' ) : home_url( user_trailingslashit( $request ) );
}

/**
 * The article subtype of the queried post (NewsArticle, BlogPosting,
 * Article), or null when the request is not a single article.
 */
function bwfd_seo_article_type(): ?string {
	$post = is_singular() ? get_queried_object() : null;
	return $post instanceof WP_Post ? bwfd_article_type( $post ) : null;
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
	$url         = bwfd_seo_current_url();
	$image       = bwfd_seo_image();
	$is_article  = null !== bwfd_seo_article_type();
	$type        = $is_article && ! is_front_page() ? 'article' : 'website';
	$locale      = str_replace( '-', '_', get_bloginfo( 'language' ) );

	if ( is_post_type_archive( array_merge( array_keys( bwfd_content_types() ), array( BWFD_CHAPTER_TYPE ) ) ) || is_tax( BWFD_TOPIC_TAX ) ) {
		// Core prints a canonical link on single items only.
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );
	}

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
		array( 'property', 'og:image:alt', (string) ( $image['alt'] ?? '' ) ),
		array( 'name', 'twitter:image:alt', (string) ( $image['alt'] ?? '' ) ),
	);
	if ( $image['width'] && $image['height'] ) {
		$tags[] = array( 'property', 'og:image:width', (string) $image['width'] );
		$tags[] = array( 'property', 'og:image:height', (string) $image['height'] );
	}
	if ( $is_article ) {
		$author_page = (int) bwfd_schema_data()['pages']['about_author'];
		$author_link = $author_page ? get_permalink( $author_page ) : false;
		$tags[] = array( 'property', 'article:published_time', get_the_date( DATE_W3C ) );
		$tags[] = array( 'property', 'article:modified_time', get_the_modified_date( DATE_W3C ) );
		// Open Graph wants a profile URL here, not a name.
		$tags[] = array( 'property', 'article:author', is_string( $author_link ) ? $author_link : home_url( '/' ) );
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
 * Facts for the book launch, used for the Event node on the launch page
 * (matched by its slug). Filter `bwfd_launch_event` to change them, or
 * return null to switch the node off.
 *
 * @return array<string, string>|null
 */
function bwfd_launch_event( WP_Post $post ): ?array {
	$event = null;
	if ( 'launch' === $post->post_name ) {
		$event = array(
			'name'        => 'Biblical Wisdom for Dads book launch',
			'description' => 'The launch of Biblical Wisdom for Dads by Stephen Parker: interviews, games, stories from the book, hope for exhausted dads, help for small groups, and copies at the launch special of $19.99. Free to attend; RSVP by 20 October.',
			'start'       => '2026-10-27T19:00:00+10:00',
			'end'         => '2026-10-27T20:15:00+10:00',
			'venue'       => 'Springwood Church of Christ',
			'street'      => '178 Springwood Road',
			'locality'    => 'Springwood',
			'region'      => 'QLD',
			'postcode'    => '4127',
			'country'     => 'AU',
			'rsvp_url'    => 'https://www.trybooking.com/DQBEO',
			'rsvp_by'     => '2026-10-20T23:59:59+10:00',
			'price'       => '0',
			'currency'    => 'AUD',
		);
	}
	return apply_filters( 'bwfd_launch_event', $event, $post );
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
 * Launch event: an Event with venue, times, the free-ticket Offer and the
 * author as organiser and performer.
 * News items and Insights (inc/content-types.php): a NewsArticle or
 * BlogPosting with the author Person, the publisher and the Book as its
 * subject; core posts get a plain Article. Their archives are a
 * CollectionPage listing the items on that page.
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
	$event   = $post instanceof WP_Post ? bwfd_launch_event( $post ) : null;
	$article = $post instanceof WP_Post ? bwfd_article_type( $post ) : null;
	$types   = bwfd_content_types();
	$listing = is_post_type_archive( array_keys( $types ) ) ? (string) get_query_var( 'post_type' ) : '';
	$topic   = is_tax( BWFD_TOPIC_TAX ) && get_queried_object() instanceof WP_Term ? get_queried_object() : null;
	$is_chapter        = $post instanceof WP_Post && BWFD_CHAPTER_TYPE === $post->post_type;
	$is_chapter_index  = is_post_type_archive( BWFD_CHAPTER_TYPE );

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
		$page = array(
			'@type'              => $is_author_page ? 'ProfilePage' : 'WebPage',
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
		} elseif ( $event ) {
			$page['mainEntity'] = array( '@id' => get_permalink() . '#event' );
		} elseif ( $article ) {
			$page['mainEntity'] = array( '@id' => get_permalink() . '#article' );
		}

		if ( $is_chapter ) {
			$page['mainEntity']  = array( '@id' => get_permalink() . '#chapter' );
			$page['breadcrumb']  = array( '@id' => get_permalink() . '#breadcrumb' );
			$graph[]             = bwfd_breadcrumb_list(
				get_permalink() . '#breadcrumb',
				array(
					array( __( 'Home', 'bwfd' ), $home ),
					array( __( 'Chapter by chapter', 'bwfd' ), (string) get_post_type_archive_link( BWFD_CHAPTER_TYPE ) ),
					array( trim( bwfd_chapter_label( $post ) . ': ' . html_entity_decode( get_the_title( $post ), ENT_QUOTES, 'UTF-8' ), ': ' ), '' ),
				)
			);
		}

		if ( $article && isset( $types[ $post->post_type ] ) ) {
			$page['breadcrumb'] = array( '@id' => get_permalink() . '#breadcrumb' );
			$graph[]            = bwfd_breadcrumb_list(
				get_permalink() . '#breadcrumb',
				array(
					array( __( 'Home', 'bwfd' ), $home ),
					array( $types[ $post->post_type ]['plural'], (string) get_post_type_archive_link( $post->post_type ) ),
					array( html_entity_decode( get_the_title( $post ), ENT_QUOTES, 'UTF-8' ), '' ),
				)
			);
		}

		$graph[] = $page;
	}

	if ( $topic || $is_chapter_index ) {
		// Topic archive or the chapter index: a collection with its own breadcrumb trail.
		$items    = array();
		$position = 0;
		foreach ( (array) $GLOBALS['wp_query']->posts as $listed ) {
			if ( ! $listed instanceof WP_Post ) {
				continue;
			}
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => ++$position,
				'url'      => get_permalink( $listed ),
				'name'     => ( $is_chapter_index ? bwfd_chapter_label( $listed ) . ': ' : '' ) . html_entity_decode( get_the_title( $listed ), ENT_QUOTES, 'UTF-8' ),
			);
		}
		$url    = bwfd_seo_current_url();
		$crumbs = $topic
			? array(
				array( __( 'Home', 'bwfd' ), $home ),
				array( $types['bwfd_insight']['plural'], (string) get_post_type_archive_link( 'bwfd_insight' ) ),
				array( html_entity_decode( $topic->name, ENT_QUOTES, 'UTF-8' ), is_paged() ? (string) get_term_link( $topic ) : '' ),
			)
			: array(
				array( __( 'Home', 'bwfd' ), $home ),
				array( __( 'Chapter by chapter', 'bwfd' ), '' ),
			);
		$graph[] = bwfd_breadcrumb_list( $url . '#breadcrumb', $crumbs );
		$graph[] = array(
			'@type'       => 'CollectionPage',
			'@id'         => $url . '#webpage',
			'url'         => $url,
			'name'        => html_entity_decode( wp_get_document_title(), ENT_QUOTES, 'UTF-8' ),
			'description' => bwfd_seo_description(),
			'isPartOf'    => $ref( 'site' ),
			'breadcrumb'  => array( '@id' => $url . '#breadcrumb' ),
			'inLanguage'  => get_bloginfo( 'language' ),
			'about'       => array(
				'@type' => 'Book',
				'@id'   => $ids['book'],
				'name'  => $book['name'],
				'url'   => $book_url,
			),
			'mainEntity'  => $items ? array(
				'@type'           => 'ItemList',
				'itemListOrder'   => $is_chapter_index ? 'https://schema.org/ItemListOrderAscending' : 'https://schema.org/ItemListOrderDescending',
				'numberOfItems'   => count( $items ),
				'itemListElement' => $items,
			) : null,
		);
	}

	if ( '' !== $listing ) {
		// News or Insights archive: the page as a collection of the items shown.
		$items    = array();
		$position = 0;
		$listed_posts = (array) $GLOBALS['wp_query']->posts;
		// The Insights archive shows the latest item in its featured block, outside the query.
		if ( 'bwfd_insight' === $listing && ! is_paged() && bwfd_featured_insight() instanceof WP_Post ) {
			array_unshift( $listed_posts, bwfd_featured_insight() );
		}
		foreach ( $listed_posts as $listed ) {
			if ( ! $listed instanceof WP_Post ) {
				continue;
			}
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => ++$position,
				'url'      => get_permalink( $listed ),
				'name'     => html_entity_decode( get_the_title( $listed ), ENT_QUOTES, 'UTF-8' ),
			);
		}
		$url     = bwfd_seo_current_url();
		$graph[] = bwfd_breadcrumb_list(
			$url . '#breadcrumb',
			array(
				array( __( 'Home', 'bwfd' ), $home ),
				array( $types[ $listing ]['plural'], is_paged() ? (string) get_post_type_archive_link( $listing ) : '' ),
			)
		);
		$graph[] = array(
			'@type'       => 'CollectionPage',
			'@id'         => $url . '#webpage',
			'url'         => $url,
			'name'        => html_entity_decode( wp_get_document_title(), ENT_QUOTES, 'UTF-8' ),
			'description' => bwfd_seo_description(),
			'isPartOf'    => $ref( 'site' ),
			'breadcrumb'  => array( '@id' => $url . '#breadcrumb' ),
			'inLanguage'  => get_bloginfo( 'language' ),
			'about'       => array(
				'@type'  => 'Book',
				'@id'    => $ids['book'],
				'name'   => $book['name'],
				'url'    => $book_url,
			),
			'mainEntity'  => $items ? array(
				'@type'           => 'ItemList',
				'itemListOrder'   => 'https://schema.org/ItemListOrderDescending',
				'numberOfItems'   => count( $items ),
				'itemListElement' => $items,
			) : null,
		);
	}

	if ( $is_book_page || $is_author_page || $is_other_books || $event || $article || $is_chapter ) {
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

		// Google reports a partial deliveryTime as a missing field, so emit it
		// only when both handling and transit are known.
		$delivery = null;
		if ( $positive( $offer['handling_max'] ) && $positive( $offer['transit_max'] ) ) {
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

	if ( $is_chapter && $post instanceof WP_Post ) {
		$verse = bwfd_post_scripture( $post );
		$graph[] = array(
			'@type'            => 'Chapter',
			'@id'              => get_permalink() . '#chapter',
			'name'             => html_entity_decode( get_the_title( $post ), ENT_QUOTES, 'UTF-8' ),
			'alternativeHeadline' => bwfd_chapter_label( $post ) ?: null,
			'position'         => bwfd_chapter_number( $post ) ?: null,
			'url'              => get_permalink(),
			'description'      => bwfd_seo_description(),
			'mainEntityOfPage' => array( '@id' => get_permalink() . '#webpage' ),
			'inLanguage'       => $book['language'],
			'author'           => $ref( 'person' ),
			'publisher'        => $ref( 'org' ),
			'image'            => has_post_thumbnail( $post ) ? array( bwfd_seo_image()['url'] ) : array( (string) $book['image']['url'] ),
			'isPartOf'         => array(
				'@type'         => 'Book',
				'@id'           => $ids['book'],
				'name'          => $book['name'],
				'author'        => $ref( 'person' ),
				'url'           => $book_url,
				'isbn'          => $editions['paperback']['isbn'],
				'numberOfPages' => $positive( $book['pages'] ),
			),
			'citation'         => $verse ? array(
				'@type' => 'CreativeWork',
				'name'  => $verse['reference'] ?: null,
				'text'  => $verse['text'],
			) : null,
		);
	}

	if ( $article && $post instanceof WP_Post ) {
		$plain = trim( wp_strip_all_tags( strip_shortcodes( (string) $post->post_content ) ) );
		$image = bwfd_seo_image();
		// Google asks for the image in several aspect ratios; the defaults
		// come in three, a featured image is offered as uploaded.
		$article_images = has_post_thumbnail( $post ) ? array( $image['url'] ) : bwfd_seo_default_article_images();

		$graph[] = array(
			'@type'            => $article,
			'@id'              => get_permalink() . '#article',
			'headline'         => html_entity_decode( get_the_title( $post ), ENT_QUOTES, 'UTF-8' ),
			'description'      => bwfd_seo_description(),
			'url'              => get_permalink(),
			'mainEntityOfPage' => array( '@id' => get_permalink() . '#webpage' ),
			'isPartOf'         => $ref( 'site' ),
			'image'            => $article_images,
			'datePublished'    => get_the_date( DATE_W3C, $post ),
			'dateModified'     => get_the_modified_date( DATE_W3C, $post ),
			'author'           => $ref( 'person' ),
			'publisher'        => $ref( 'org' ),
			'inLanguage'       => get_bloginfo( 'language' ),
			'articleSection'   => $types[ $post->post_type ]['section'] ?? null,
			'dateline'         => 'NewsArticle' === $article ? bwfd_news_dateline( $post ) : null,
			'keywords'         => 'BlogPosting' === $article ? ( bwfd_post_topic_names( $post ) ?: null ) : null,
			'mentions'         => 'BlogPosting' === $article ? array_values( array_map(
				static fn( WP_Post $chapter ): array => array(
					'@type'    => 'Chapter',
					'@id'      => get_permalink( $chapter ) . '#chapter',
					'name'     => html_entity_decode( get_the_title( $chapter ), ENT_QUOTES, 'UTF-8' ),
					'position' => bwfd_chapter_number( $chapter ) ?: null,
					'url'      => get_permalink( $chapter ),
					'isPartOf' => array( '@type' => 'Book', '@id' => $ids['book'], 'name' => $book['name'] ),
				),
				bwfd_post_chapters( $post )
			) ) ?: null : null,
			'wordCount'        => '' !== $plain ? str_word_count( $plain ) : null,
			'isAccessibleForFree' => true,
			'citation'         => ( $verse = bwfd_post_scripture( $post ) ) ? array(
				'@type' => 'CreativeWork',
				'name'  => $verse['reference'] ?: null,
				'text'  => $verse['text'],
			) : null,
			'about'            => array(
				'@type'  => 'Book',
				'@id'    => $ids['book'],
				'name'   => $book['name'],
				'author' => $ref( 'person' ),
				'url'    => $book_url,
				'isbn'   => $editions['paperback']['isbn'],
			),
		);
	}

	if ( $event ) {
		$graph[] = array(
			'@type'               => 'Event',
			'@id'                 => get_permalink() . '#event',
			'name'                => $event['name'],
			'description'         => $event['description'],
			'url'                 => get_permalink(),
			'image'               => (string) $book['image']['url'],
			'startDate'           => $event['start'],
			'endDate'             => $event['end'],
			'eventStatus'         => 'https://schema.org/EventScheduled',
			'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
			'location'            => array(
				'@type'   => 'Place',
				'name'    => $event['venue'],
				'address' => array(
					'@type'           => 'PostalAddress',
					'streetAddress'   => $event['street'],
					'addressLocality' => $event['locality'],
					'addressRegion'   => $event['region'],
					'postalCode'      => $event['postcode'],
					'addressCountry'  => $event['country'],
				),
			),
			'organizer'           => $ref( 'person' ),
			'performer'           => $ref( 'person' ),
			'about'               => array(
				'@type'  => 'Book',
				'name'   => $book['name'],
				'author' => $ref( 'person' ),
				'url'    => $book_url,
			),
			'offers'              => array(
				'@type'         => 'Offer',
				'name'          => 'Free ticket',
				'url'           => $event['rsvp_url'],
				'price'         => $event['price'],
				'priceCurrency' => $event['currency'],
				'availability'  => 'https://schema.org/InStock',
				'validFrom'     => $post instanceof WP_Post ? get_the_date( DATE_W3C, $post ) : null,
				'validThrough'  => $event['rsvp_by'],
			),
		);
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
 * A BreadcrumbList node. The last item may have no URL (the current page).
 *
 * @param string                               $id    Node @id.
 * @param array<int, array{0:string,1:string}> $items Name and URL pairs, in order.
 * @return array<string, mixed>
 */
function bwfd_breadcrumb_list( string $id, array $items ): array {
	$list = array();
	foreach ( array_values( $items ) as $index => list( $name, $url ) ) {
		$list[] = array(
			'@type'    => 'ListItem',
			'position' => $index + 1,
			'name'     => $name,
			'item'     => '' !== $url ? $url : null,
		);
	}
	return array(
		'@type'           => 'BreadcrumbList',
		'@id'             => $id,
		'itemListElement' => $list,
	);
}

/**
 * Title tag for the News and Insights archives from Settings → Structured
 * data → Archives (blank keeps core's "News – Site"). Page two onwards
 * gets the page number.
 *
 * @param array<string, string> $parts Title parts.
 * @return array<string, string>
 */
function bwfd_seo_archive_title( array $parts ): array {
	if ( ! is_post_type_archive() ) {
		return $parts;
	}
	$key    = bwfd_archive_settings_key( (string) get_query_var( 'post_type' ) );
	$custom = '' !== $key ? trim( (string) ( bwfd_schema_data()['archives'][ $key ]['title'] ?? '' ) ) : '';
	if ( '' === $custom ) {
		return $parts;
	}
	$out = array( 'title' => $custom );
	if ( is_paged() ) {
		/* translators: %d: page number */
		$out['page'] = sprintf( __( 'Page %d', 'bwfd' ), (int) get_query_var( 'paged' ) );
	}
	return $out;
}
add_filter( 'document_title_parts', 'bwfd_seo_archive_title', 9 );

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
	if ( bwfd_seo_generating_site_icon() ) {
		return $formats;
	}
	$formats['image/jpeg'] = 'image/webp';
	$formats['image/png']  = 'image/webp';
	return $formats;
}
add_filter( 'image_editor_output_format', 'bwfd_seo_webp_uploads' );

/**
 * Track whether the sub-sizes being generated belong to the site icon.
 *
 * @param bool|null $set New state, or null to read the current state.
 * @return bool
 */
function bwfd_seo_generating_site_icon( ?bool $set = null ): bool {
	static $generating = false;
	if ( null !== $set ) {
		$generating = $set;
	}
	return $generating;
}

/**
 * Keep the site icon as PNG with its favicon sizes (32, 180, 192, 270) when
 * its metadata is regenerated. Core only adds those sizes while the icon is
 * first chosen, so a later `wp media regenerate` would otherwise drop them
 * and convert the icon to WebP.
 *
 * @param array $sizes         Sub-sizes about to be generated.
 * @param array $image_meta    Image metadata.
 * @param int   $attachment_id Attachment being processed.
 * @return array
 */
function bwfd_seo_site_icon_sizes( array $sizes, array $image_meta, int $attachment_id ): array {
	if ( 'site-icon' === get_post_meta( $attachment_id, '_wp_attachment_context', true ) ) {
		bwfd_seo_generating_site_icon( true );
		$sizes = ( new WP_Site_Icon() )->additional_sizes( $sizes );
	}
	return $sizes;
}
add_filter( 'intermediate_image_sizes_advanced', 'bwfd_seo_site_icon_sizes', 10, 3 );

/**
 * Reset the site icon flag once an attachment's metadata is complete.
 *
 * @param array $metadata Attachment metadata.
 * @return array
 */
function bwfd_seo_site_icon_done( array $metadata ): array {
	bwfd_seo_generating_site_icon( false );
	return $metadata;
}
add_filter( 'wp_generate_attachment_metadata', 'bwfd_seo_site_icon_done', 5 );

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
 * archives, attachment pages, not-found pages, and term or post type
 * archives that have nothing in them yet. Links on those pages are still
 * followed.
 *
 * @param array<string, bool|string> $robots Robots directives.
 * @return array<string, bool|string>
 */
function bwfd_seo_robots( array $robots ): array {
	global $wp_query;

	$empty_archive = ( is_category() || is_tag() || is_tax() || is_post_type_archive() ) && 0 === (int) $wp_query->post_count;

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
	foreach ( bwfd_seo_post_types() as $post_type ) {
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
	if ( ! $screen || ! in_array( (string) $screen->post_type, bwfd_seo_post_types(), true ) ) {
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

	// The "Before you publish" checklist, on the article types only.
	$rules = bwfd_seo_checklist_rules();
	$file  = BWFD_DIR . '/build/admin/seo-checklist.asset.php';
	if ( isset( $rules[ (string) $screen->post_type ] ) && file_exists( $file ) ) {
		$checklist = require $file;
		wp_enqueue_script( 'bwfd-seo-checklist', BWFD_URI . '/build/admin/seo-checklist.js', $checklist['dependencies'], $checklist['version'], true );
		if ( file_exists( BWFD_DIR . '/build/admin/seo-checklist.css' ) ) {
			wp_enqueue_style( 'bwfd-seo-checklist', BWFD_URI . '/build/admin/seo-checklist.css', array( 'wp-components' ), $checklist['version'] );
		}
		wp_add_inline_script(
			'bwfd-seo-checklist',
			'window.bwfdSeoChecklist = ' . wp_json_encode(
				array(
					'siteName'  => get_bloginfo( 'name' ),
					'separator' => apply_filters( 'document_title_separator', '-' ),
					'homeUrl'   => home_url( '/' ),
					'rules'     => $rules,
				)
			) . ';',
			'before'
		);
	}
}
add_action( 'enqueue_block_editor_assets', 'bwfd_seo_editor_assets' );

/**
 * Per-type rules for the editor's "Before you publish" checklist
 * (src/admin/seo-checklist.js). Filter `bwfd_seo_checklist_rules` to tune
 * the thresholds or add a type; a type with no entry gets no checklist.
 *
 * @return array<string, array<string, mixed>>
 */
function bwfd_seo_checklist_rules(): array {
	return (array) apply_filters(
		'bwfd_seo_checklist_rules',
		array(
			'bwfd_news'    => array(
				'minWords'      => 150,
				'imageRequired' => false,
				'scripture'     => false,
				'headingsFrom'  => 400,
			),
			'bwfd_insight' => array(
				'minWords'      => 300,
				'imageRequired' => false,
				'scripture'     => true,
				'topics'        => true,
				'headingsFrom'  => 400,
			),
		)
	);
}

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
