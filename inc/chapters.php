<?php
/**
 * Chapters of the book.
 *
 * Each chapter is a post of the `bwfd_chapter` type: the chapter number in
 * the Order field (menu_order), the title, a block-editor summary and an
 * optional excerpt, at /chapters/{number}-{slug}/, with /chapters/ listing
 * them in order. A chapter page carries Chapter structured data, the
 * insights on that chapter and a buy prompt, so "Biblical Wisdom for Dads
 * chapter 22" has a page to land on.
 *
 * Insights are linked to chapters through a shadow taxonomy, also named
 * `bwfd_chapter`: one term per chapter post, created and kept in step by
 * the save hooks below, never edited by hand. That gives the Insight editor
 * the standard "Chapters" checklist, ordered by chapter number, and lets
 * the chapter page query its insights. Term links point at the chapter
 * page; the taxonomy has no archives of its own.
 *
 * @package bwfd
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

const BWFD_CHAPTER_TYPE = 'bwfd_chapter';
const BWFD_CHAPTER_TAX  = 'bwfd_chapter';

/**
 * Register the post type and its shadow taxonomy.
 */
function bwfd_register_chapters(): void {
	register_post_type(
		BWFD_CHAPTER_TYPE,
		array(
			'labels'              => array(
				'name'                  => __( 'Chapters', 'bwfd' ),
				'singular_name'         => __( 'Chapter', 'bwfd' ),
				'menu_name'             => __( 'Chapters', 'bwfd' ),
				'name_admin_bar'        => __( 'Chapter', 'bwfd' ),
				'all_items'             => __( 'All chapters', 'bwfd' ),
				'add_new'               => __( 'Add new', 'bwfd' ),
				'add_new_item'          => __( 'Add new chapter', 'bwfd' ),
				'edit_item'             => __( 'Edit chapter', 'bwfd' ),
				'new_item'              => __( 'New chapter', 'bwfd' ),
				'view_item'             => __( 'View chapter', 'bwfd' ),
				'view_items'            => __( 'View chapters', 'bwfd' ),
				'search_items'          => __( 'Search chapters', 'bwfd' ),
				'not_found'             => __( 'No chapters found.', 'bwfd' ),
				'not_found_in_trash'    => __( 'No chapters found in the bin.', 'bwfd' ),
				'archives'              => __( 'Chapters', 'bwfd' ),
				'attributes'            => __( 'Chapter number', 'bwfd' ),
				'featured_image'        => __( 'Featured image', 'bwfd' ),
				'set_featured_image'    => __( 'Set featured image', 'bwfd' ),
				'remove_featured_image' => __( 'Remove featured image', 'bwfd' ),
				'use_featured_image'    => __( 'Use as featured image', 'bwfd' ),
				'item_published'        => __( 'Chapter published.', 'bwfd' ),
				'item_updated'          => __( 'Chapter updated.', 'bwfd' ),
				'item_link'             => __( 'Chapter link', 'bwfd' ),
				'item_link_description' => __( 'A link to a chapter.', 'bwfd' ),
			),
			'description'         => __( 'Biblical Wisdom for Dads, chapter by chapter: what each of the 40 short chapters covers, with the insights that draw on it.', 'bwfd' ),
			'public'              => true,
			'show_in_rest'        => true,
			'menu_position'       => 7,
			'menu_icon'           => 'dashicons-book-alt',
			'has_archive'         => 'chapters',
			'rewrite'             => array(
				'slug'       => 'chapters',
				'with_front' => false,
			),
			'supports'            => array(
				'title',
				'editor'  => array( 'default-mode' => 'template-locked' ),
				'excerpt',
				'thumbnail',
				'revisions',
				'custom-fields',
				// The Order field holds the chapter number.
				'page-attributes',
			),
			'template'            => array(
				array( 'bwfd/scripture', array() ),
				array( 'core/paragraph', array( 'placeholder' => __( 'What this chapter is about, in a paragraph or two…', 'bwfd' ) ) ),
			),
			'exclude_from_search' => false,
		)
	);

	register_taxonomy(
		BWFD_CHAPTER_TAX,
		array( 'bwfd_insight' ),
		array(
			'labels'            => array(
				'name'          => __( 'Chapters', 'bwfd' ),
				'singular_name' => __( 'Chapter', 'bwfd' ),
				'search_items'  => __( 'Search chapters', 'bwfd' ),
				'not_found'     => __( 'No chapters yet. Add them under Chapters.', 'bwfd' ),
				'no_terms'      => __( 'No chapters', 'bwfd' ),
			),
			'description'       => __( 'The chapters an insight draws on. Managed from the Chapters menu; one entry per chapter.', 'bwfd' ),
			// Not public (no archives, not in sitemaps or menus), but the
			// editor's checklist needs show_ui and the Post Terms block needs
			// publicly_queryable; a stray term URL redirects to the chapter page.
			'public'            => false,
			'publicly_queryable' => true,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_in_menu'      => false,
			'show_in_rest'      => true,
			// The post type already owns /wp/v2/bwfd_chapter; the terms need their own route.
			'rest_base'         => 'insight-chapters',
			'show_admin_column' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud'     => false,
			'show_in_quick_edit' => false,
			'rewrite'           => false,
			'query_var'         => false,
			'capabilities'      => array(
				'manage_terms' => 'do_not_allow',
				'edit_terms'   => 'do_not_allow',
				'delete_terms' => 'do_not_allow',
				'assign_terms' => 'edit_posts',
			),
		)
	);

	register_term_meta( BWFD_CHAPTER_TAX, 'bwfd_chapter_post', array( 'type' => 'integer', 'single' => true, 'default' => 0, 'show_in_rest' => true ) );
	register_term_meta( BWFD_CHAPTER_TAX, 'bwfd_chapter_number', array( 'type' => 'integer', 'single' => true, 'default' => 0, 'show_in_rest' => true ) );
}
add_action( 'init', 'bwfd_register_chapters', 9 );

/**
 * Total chapters, from Settings → Structured data (default 40).
 */
function bwfd_chapter_total(): int {
	return max( 0, (int) ( bwfd_schema_data()['book']['chapters'] ?? 0 ) );
}

/**
 * Chapter number of a chapter post (its Order), or 0.
 */
function bwfd_chapter_number( WP_Post $post ): int {
	return BWFD_CHAPTER_TYPE === $post->post_type ? max( 0, (int) $post->menu_order ) : 0;
}

/**
 * "Chapter 22", "22" or "Chapter 22 of 40".
 */
function bwfd_chapter_label( WP_Post $post, string $format = 'label' ): string {
	$number = bwfd_chapter_number( $post );
	if ( $number <= 0 ) {
		return '';
	}
	switch ( $format ) {
		case 'number':
			return (string) $number;
		case 'label-of':
			$total = bwfd_chapter_total();
			return $total > 0
				/* translators: 1: chapter number, 2: total chapters */
				? sprintf( __( 'Chapter %1$d of %2$d', 'bwfd' ), $number, $total )
				/* translators: %d: chapter number */
				: sprintf( __( 'Chapter %d', 'bwfd' ), $number );
		default:
			/* translators: %d: chapter number */
			return sprintf( __( 'Chapter %d', 'bwfd' ), $number );
	}
}

/**
 * Slugs carry the number: /chapters/22-compassion/. Applied when the slug
 * does not already start with the chapter's number.
 *
 * @param array<string, mixed> $data    Post data about to be saved.
 * @param array<string, mixed> $postarr Raw post array.
 * @return array<string, mixed>
 */
function bwfd_chapter_slug( array $data, array $postarr ): array {
	if ( BWFD_CHAPTER_TYPE !== ( $data['post_type'] ?? '' ) || in_array( $data['post_status'] ?? '', array( 'auto-draft', 'trash' ), true ) ) {
		return $data;
	}
	$number = (int) ( $data['menu_order'] ?? 0 );
	if ( $number <= 0 ) {
		return $data;
	}
	$base = sanitize_title( (string) ( $data['post_name'] ?: $data['post_title'] ) );
	$base = (string) preg_replace( '/^\d+-/', '', $base );
	if ( '' === $base ) {
		return $data;
	}
	$slug = $number . '-' . $base;
	if ( $slug !== $data['post_name'] ) {
		$data['post_name'] = wp_unique_post_slug( $slug, (int) ( $postarr['ID'] ?? 0 ), (string) $data['post_status'], BWFD_CHAPTER_TYPE, (int) ( $data['post_parent'] ?? 0 ) );
	}
	return $data;
}
add_filter( 'wp_insert_post_data', 'bwfd_chapter_slug', 10, 2 );

/* -------------------------------------------------------------------------
 * Shadow taxonomy: one term per chapter post.
 * ---------------------------------------------------------------------- */

/**
 * The term standing for a chapter post, or 0.
 */
function bwfd_chapter_term_id( int $post_id ): int {
	$terms = get_terms(
		array(
			'taxonomy'   => BWFD_CHAPTER_TAX,
			'hide_empty' => false,
			'number'     => 1,
			'fields'     => 'ids',
			'meta_key'   => 'bwfd_chapter_post', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value' => $post_id, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	return is_array( $terms ) && $terms ? (int) $terms[0] : 0;
}

/**
 * The chapter post a term stands for, or null.
 */
function bwfd_chapter_term_post( WP_Term $term ): ?WP_Post {
	$post = get_post( (int) get_term_meta( $term->term_id, 'bwfd_chapter_post', true ) );
	return $post instanceof WP_Post && BWFD_CHAPTER_TYPE === $post->post_type ? $post : null;
}

/**
 * Create or update the term for a chapter post.
 */
function bwfd_chapter_sync_term( int $post_id, WP_Post $post ): void {
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) || in_array( $post->post_status, array( 'auto-draft', 'trash' ), true ) ) {
		return;
	}
	$number = bwfd_chapter_number( $post );
	$title  = html_entity_decode( $post->post_title, ENT_QUOTES, 'UTF-8' );
	/* translators: 1: chapter number, 2: chapter title */
	$name = $number > 0 ? sprintf( __( 'Chapter %1$d: %2$s', 'bwfd' ), $number, $title ) : $title;
	$slug = $post->post_name ?: sanitize_title( $title );
	if ( '' === trim( $name ) ) {
		return;
	}

	$term_id = bwfd_chapter_term_id( $post_id );
	if ( $term_id ) {
		wp_update_term( $term_id, BWFD_CHAPTER_TAX, array( 'name' => $name, 'slug' => $slug ) );
	} else {
		$result = wp_insert_term( $name, BWFD_CHAPTER_TAX, array( 'slug' => $slug ) );
		if ( is_wp_error( $result ) ) {
			$existing = (int) ( $result->get_error_data( 'term_exists' ) ?: 0 );
			if ( ! $existing ) {
				return;
			}
			$term_id = $existing;
			wp_update_term( $term_id, BWFD_CHAPTER_TAX, array( 'name' => $name ) );
		} else {
			$term_id = (int) $result['term_id'];
		}
	}
	update_term_meta( $term_id, 'bwfd_chapter_post', $post_id );
	update_term_meta( $term_id, 'bwfd_chapter_number', $number );
}
add_action( 'save_post_' . BWFD_CHAPTER_TYPE, 'bwfd_chapter_sync_term', 20, 2 );

/**
 * Remove the term when its chapter is deleted for good.
 */
function bwfd_chapter_delete_term( int $post_id ): void {
	if ( BWFD_CHAPTER_TYPE !== get_post_type( $post_id ) ) {
		return;
	}
	$term_id = bwfd_chapter_term_id( $post_id );
	if ( $term_id ) {
		wp_delete_term( $term_id, BWFD_CHAPTER_TAX );
	}
}
add_action( 'before_delete_post', 'bwfd_chapter_delete_term' );

/**
 * Term links go to the chapter page (the taxonomy has no archives).
 *
 * @param string  $link     Term link.
 * @param WP_Term $term     Term.
 * @param string  $taxonomy Taxonomy.
 */
function bwfd_chapter_term_link( string $link, WP_Term $term, string $taxonomy ): string {
	if ( BWFD_CHAPTER_TAX !== $taxonomy ) {
		return $link;
	}
	$post = bwfd_chapter_term_post( $term );
	if ( $post && 'publish' === $post->post_status ) {
		return (string) get_permalink( $post );
	}
	return (string) get_post_type_archive_link( BWFD_CHAPTER_TYPE );
}
add_filter( 'term_link', 'bwfd_chapter_term_link', 10, 3 );

/**
 * A chapter term reached as an archive (only possible through the generic
 * taxonomy query variables) goes to its chapter page.
 */
function bwfd_chapter_term_redirect(): void {
	if ( ! is_tax( BWFD_CHAPTER_TAX ) ) {
		return;
	}
	$term = get_queried_object();
	$post = $term instanceof WP_Term ? bwfd_chapter_term_post( $term ) : null;
	$to   = $post && 'publish' === $post->post_status ? get_permalink( $post ) : get_post_type_archive_link( BWFD_CHAPTER_TYPE );
	wp_safe_redirect( is_string( $to ) ? $to : home_url( '/' ), 301 );
	exit;
}
add_action( 'template_redirect', 'bwfd_chapter_term_redirect', 0 );

/**
 * The editor's Chapters checklist and any term list come back in chapter
 * order rather than alphabetical.
 *
 * @param array<string, mixed> $args Term query arguments.
 * @return array<string, mixed>
 */
function bwfd_chapter_terms_order( array $args ): array {
	$args['meta_key'] = 'bwfd_chapter_number'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
	$args['orderby']  = 'meta_value_num';
	$args['order']    = 'ASC';
	return $args;
}
add_filter( 'rest_' . BWFD_CHAPTER_TAX . '_query', 'bwfd_chapter_terms_order' );

/**
 * Chapters an insight draws on, in chapter order: the chapter posts.
 *
 * @return WP_Post[]
 */
function bwfd_post_chapters( WP_Post $post ): array {
	$terms = get_the_terms( $post, BWFD_CHAPTER_TAX );
	if ( ! is_array( $terms ) ) {
		return array();
	}
	$chapters = array();
	foreach ( $terms as $term ) {
		$chapter = bwfd_chapter_term_post( $term );
		if ( $chapter && 'publish' === $chapter->post_status ) {
			$chapters[] = $chapter;
		}
	}
	usort( $chapters, static fn( WP_Post $a, WP_Post $b ): int => $a->menu_order <=> $b->menu_order );
	return $chapters;
}

/* -------------------------------------------------------------------------
 * Queries
 * ---------------------------------------------------------------------- */

/**
 * The chapters archive lists every chapter in order on one page.
 */
function bwfd_chapters_archive_query( WP_Query $query ): void {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( BWFD_CHAPTER_TYPE ) ) {
		return;
	}
	$query->set( 'orderby', 'menu_order title' );
	$query->set( 'order', 'ASC' );
	$query->set( 'posts_per_page', 100 );
}
add_action( 'pre_get_posts', 'bwfd_chapters_archive_query' );

/**
 * On a chapter page, a Query Loop whose post-template carries the
 * `bwfd-chapter-insights` class lists the insights on that chapter.
 *
 * @param array<string, mixed> $query Query arguments.
 * @param WP_Block             $block The post-template block.
 * @return array<string, mixed>
 */
function bwfd_chapter_insights_query( array $query, WP_Block $block ): array {
	$class = (string) ( $block->parsed_block['attrs']['className'] ?? '' );
	if ( false === strpos( $class, 'bwfd-chapter-insights' ) || ! is_singular( BWFD_CHAPTER_TYPE ) ) {
		return $query;
	}
	$term_id = bwfd_chapter_term_id( get_queried_object_id() );
	if ( ! $term_id ) {
		$query['post__in'] = array( 0 );
		return $query;
	}
	$query['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		array(
			'taxonomy' => BWFD_CHAPTER_TAX,
			'field'    => 'term_id',
			'terms'    => array( $term_id ),
		),
	);
	return $query;
}
add_filter( 'query_loop_block_query_vars', 'bwfd_chapter_insights_query', 10, 2 );

/**
 * Title tag: "Chapter 22: Compassion – Site"; the archive gets a fuller
 * title. Filter `bwfd_chapter_document_title`.
 *
 * @param array<string, string> $parts Title parts.
 * @return array<string, string>
 */
function bwfd_chapter_document_title( array $parts ): array {
	if ( is_singular( BWFD_CHAPTER_TYPE ) ) {
		$post  = get_queried_object();
		$label = $post instanceof WP_Post ? bwfd_chapter_label( $post ) : '';
		if ( '' !== $label ) {
			$parts['title'] = $label . ': ' . $parts['title'];
		}
	}
	// The chapters index takes its title and description from Settings →
	// Structured data → Archives, like the News and Insights archives.
	return apply_filters( 'bwfd_chapter_document_title', $parts );
}
add_filter( 'document_title_parts', 'bwfd_chapter_document_title', 9 );
