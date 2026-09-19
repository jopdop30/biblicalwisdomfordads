<?php
/**
 * Topics: the categories for Insights.
 *
 * A hierarchical taxonomy (so the editor shows a checklist) with public
 * archives at /insights/topic/{slug}/, rendered by
 * templates/taxonomy-bwfd_topic.html. inc/seo.php gives the archives a
 * canonical link, a title, a CollectionPage and breadcrumbs, and adds the
 * topic names to each BlogPosting's keywords.
 *
 * @package bwfd
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

const BWFD_TOPIC_TAX = 'bwfd_topic';

/**
 * Register the taxonomy.
 */
function bwfd_register_topics(): void {
	register_taxonomy(
		BWFD_TOPIC_TAX,
		array( 'bwfd_insight' ),
		array(
			'labels'            => array(
				'name'                       => __( 'Topics', 'bwfd' ),
				'singular_name'              => __( 'Topic', 'bwfd' ),
				'menu_name'                  => __( 'Topics', 'bwfd' ),
				'all_items'                  => __( 'All topics', 'bwfd' ),
				'edit_item'                  => __( 'Edit topic', 'bwfd' ),
				'view_item'                  => __( 'View topic', 'bwfd' ),
				'update_item'                => __( 'Update topic', 'bwfd' ),
				'add_new_item'               => __( 'Add new topic', 'bwfd' ),
				'new_item_name'              => __( 'New topic name', 'bwfd' ),
				'parent_item'                => __( 'Parent topic', 'bwfd' ),
				'parent_item_colon'          => __( 'Parent topic:', 'bwfd' ),
				'search_items'               => __( 'Search topics', 'bwfd' ),
				'not_found'                  => __( 'No topics found.', 'bwfd' ),
				'no_terms'                   => __( 'No topics', 'bwfd' ),
				'items_list'                 => __( 'Topics list', 'bwfd' ),
				'items_list_navigation'      => __( 'Topics list navigation', 'bwfd' ),
				'back_to_items'              => __( '← Go to topics', 'bwfd' ),
				'item_link'                  => __( 'Topic link', 'bwfd' ),
				'item_link_description'      => __( 'A link to a topic.', 'bwfd' ),
			),
			'description'       => __( 'Themes the Insights return to: compassion, discipline, strength, instruction and so on. Each has its own page listing the insights on it.', 'bwfd' ),
			'public'            => true,
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
			'rewrite'           => array(
				'slug'         => 'insights/topic',
				'with_front'   => false,
				'hierarchical' => false,
			),
		)
	);
}
add_action( 'init', 'bwfd_register_topics', 9 );

/**
 * Topic names for a post, for keywords and the like.
 *
 * @return string[]
 */
function bwfd_post_topic_names( WP_Post $post ): array {
	$terms = get_the_terms( $post, BWFD_TOPIC_TAX );
	if ( ! is_array( $terms ) ) {
		return array();
	}
	return array_values( array_map( static fn( WP_Term $t ): string => html_entity_decode( $t->name, ENT_QUOTES, 'UTF-8' ), $terms ) );
}

/**
 * Title for a topic archive: "Insights on Compassion" (page two onwards
 * adds the page number). Filter `bwfd_topic_archive_title`.
 */
function bwfd_topic_archive_title( WP_Term $term ): string {
	$pattern = trim( (string) ( bwfd_schema_data()['archives']['topic']['title'] ?? '' ) ) ?: 'Insights on {topic}';
	return (string) apply_filters( 'bwfd_topic_archive_title', bwfd_topic_fill( $pattern, $term ), $term );
}

/**
 * {topic}, {author} and {book} in the settings' topic page patterns.
 */
function bwfd_topic_fill( string $pattern, WP_Term $term ): string {
	$data = bwfd_schema_data();
	return strtr(
		$pattern,
		array(
			'{topic}'  => html_entity_decode( $term->name, ENT_QUOTES, 'UTF-8' ),
			'{author}' => (string) $data['author']['name'],
			'{book}'   => (string) $data['book']['name'],
		)
	);
}

/**
 * Description for a topic archive: the term description, else a sentence
 * built from the topic name.
 */
function bwfd_topic_archive_description( WP_Term $term ): string {
	$text = trim( wp_strip_all_tags( (string) $term->description ) );
	if ( '' !== $text ) {
		return $text;
	}
	$pattern = trim( (string) ( bwfd_schema_data()['archives']['topic']['description'] ?? '' ) ) ?: 'Short reflections from {author} on {topic}: what Scripture shows dads about it, drawn from {book}.';
	return bwfd_topic_fill( $pattern, $term );
}

/**
 * In the topic pill row (a Tag Cloud block), mark the topic being viewed.
 */
function bwfd_topic_cloud_current( string $html ): string {
	if ( ! is_tax( BWFD_TOPIC_TAX ) || ! get_queried_object() instanceof WP_Term ) {
		return $html;
	}
	$link = get_term_link( get_queried_object() );
	if ( ! is_string( $link ) ) {
		return $html;
	}
	return str_replace( 'href="' . esc_url( $link ) . '"', 'href="' . esc_url( $link ) . '" aria-current="page"', $html );
}
add_filter( 'wp_tag_cloud', 'bwfd_topic_cloud_current' );

/**
 * Use the topic title as the title tag on topic archives.
 *
 * @param array<string, string> $parts Title parts.
 * @return array<string, string>
 */
function bwfd_topic_document_title( array $parts ): array {
	if ( is_tax( BWFD_TOPIC_TAX ) ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			$parts['title'] = bwfd_topic_archive_title( $term );
		}
	}
	return $parts;
}
add_filter( 'document_title_parts', 'bwfd_topic_document_title', 9 );
