<?php
/**
 * Content types for ongoing writing: News (announcements and press
 * releases) and Insights (short reflections from the author).
 *
 * They are separate post types rather than categories of the core post so
 * each has its own clean archive (/news/, /insights/), its own single and
 * archive templates with the right call to action, its own admin menu, and
 * its own Article subtype in the structured data. Core posts, categories
 * and date archives stay unused.
 *
 * @package bwfd
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * The content types and the facts the templates and SEO need about each.
 *
 * `schema` is the schema.org Article subtype for the single view and
 * `section` the articleSection value. The band that closes each single view
 * is the Book band block in the template (news points to the book, insights
 * to the purchase page).
 *
 * @return array<string, array<string, string>>
 */
function bwfd_content_types(): array {
	return array(
		'bwfd_news'    => array(
			'slug'        => 'news',
			'singular'    => __( 'News item', 'bwfd' ),
			'plural'      => __( 'News', 'bwfd' ),
			'description' => __( 'Announcements and press releases about Biblical Wisdom for Dads, the book by Stephen Parker.', 'bwfd' ),
			'schema'      => 'NewsArticle',
			'section'     => 'News',
			'icon'        => 'dashicons-megaphone',
		),
		'bwfd_insight' => array(
			'slug'        => 'insights',
			'singular'    => __( 'Insight', 'bwfd' ),
			'plural'      => __( 'Insights', 'bwfd' ),
			'description' => __( 'Short reflections from Stephen Parker on fatherhood and the wisdom found in Scripture.', 'bwfd' ),
			'schema'      => 'BlogPosting',
			'section'     => 'Insights',
			'icon'        => 'dashicons-lightbulb',
		),
	);
}

/**
 * Register both post types.
 */
function bwfd_register_content_types(): void {
	$position = 5;
	foreach ( bwfd_content_types() as $post_type => $type ) {
		$singular = $type['singular'];
		$plural   = $type['plural'];

		register_post_type(
			$post_type,
			array(
				'labels'              => array(
					'name'                     => $plural,
					'singular_name'            => $singular,
					'menu_name'                => $plural,
					'name_admin_bar'           => $singular,
					'all_items'                => sprintf( __( 'All %s', 'bwfd' ), $plural ),
					'add_new'                  => __( 'Add new', 'bwfd' ),
					'add_new_item'             => sprintf( __( 'Add new %s', 'bwfd' ), strtolower( $singular ) ),
					'edit_item'                => sprintf( __( 'Edit %s', 'bwfd' ), strtolower( $singular ) ),
					'new_item'                 => sprintf( __( 'New %s', 'bwfd' ), strtolower( $singular ) ),
					'view_item'                => sprintf( __( 'View %s', 'bwfd' ), strtolower( $singular ) ),
					'view_items'               => sprintf( __( 'View %s', 'bwfd' ), strtolower( $plural ) ),
					'search_items'             => sprintf( __( 'Search %s', 'bwfd' ), strtolower( $plural ) ),
					'not_found'                => sprintf( __( 'No %s found.', 'bwfd' ), strtolower( $plural ) ),
					'not_found_in_trash'       => sprintf( __( 'No %s found in the bin.', 'bwfd' ), strtolower( $plural ) ),
					'archives'                 => $plural,
					'attributes'               => sprintf( __( '%s attributes', 'bwfd' ), $singular ),
					'insert_into_item'         => sprintf( __( 'Insert into %s', 'bwfd' ), strtolower( $singular ) ),
					'uploaded_to_this_item'    => sprintf( __( 'Uploaded to this %s', 'bwfd' ), strtolower( $singular ) ),
					'featured_image'           => __( 'Featured image', 'bwfd' ),
					'set_featured_image'       => __( 'Set featured image', 'bwfd' ),
					'remove_featured_image'    => __( 'Remove featured image', 'bwfd' ),
					'use_featured_image'       => __( 'Use as featured image', 'bwfd' ),
					'filter_items_list'        => sprintf( __( 'Filter %s', 'bwfd' ), strtolower( $plural ) ),
					'items_list_navigation'    => sprintf( __( '%s navigation', 'bwfd' ), $plural ),
					'items_list'               => $plural,
					'item_published'           => sprintf( __( '%s published.', 'bwfd' ), $singular ),
					'item_published_privately' => sprintf( __( '%s published privately.', 'bwfd' ), $singular ),
					'item_reverted_to_draft'   => sprintf( __( '%s reverted to draft.', 'bwfd' ), $singular ),
					'item_scheduled'           => sprintf( __( '%s scheduled.', 'bwfd' ), $singular ),
					'item_updated'             => sprintf( __( '%s updated.', 'bwfd' ), $singular ),
					'item_link'                => sprintf( __( '%s link', 'bwfd' ), $singular ),
					'item_link_description'    => sprintf( __( 'A link to a %s.', 'bwfd' ), strtolower( $singular ) ),
				),
				// Shown as the archive intro in search snippets (get_the_archive_description()).
				'description'         => $type['description'],
				'public'              => true,
				'show_in_rest'        => true,
				'menu_position'       => $position++,
				'menu_icon'           => $type['icon'],
				'has_archive'         => $type['slug'],
				'rewrite'             => array(
					'slug'       => $type['slug'],
					'with_front' => false,
				),
				'query_var'           => true,
				'supports'            => array(
					'title',
					// Edit inside the single template so the call to action is visible.
					'editor'  => array( 'default-mode' => 'template-locked' ),
					'excerpt',
					'thumbnail',
					'revisions',
					// Needed for post meta (search title, featured flag) to reach the REST API and the editor.
					'custom-fields',
				),
				'template'            => array(
					array(
						'core/paragraph',
						array( 'placeholder' => __( 'Start writing…', 'bwfd' ) ),
					),
				),
				'exclude_from_search' => false,
			)
		);
	}
}
add_action( 'init', 'bwfd_register_content_types' );

/**
 * The Insights archive opens with the Featured insight block, which shows
 * the featured (else most recent) item, so the list beneath it leaves that
 * item out. With one item published the list is empty and the template's
 * "more on the way" note shows instead.
 */
function bwfd_insights_archive_query( WP_Query $query ): void {
	if ( is_admin() || ! $query->is_main_query() || $query->is_feed() || ! $query->is_post_type_archive( 'bwfd_insight' ) ) {
		return;
	}
	$featured = bwfd_featured_insight();
	if ( $featured instanceof WP_Post ) {
		$query->set( 'post__not_in', array( (int) $featured->ID ) );
	}
}
add_action( 'pre_get_posts', 'bwfd_insights_archive_query' );

/**
 * Settings key for a content type's archive (Settings → Structured data →
 * Archives), or '' for other types.
 */
function bwfd_archive_settings_key( string $post_type ): string {
	if ( defined( 'BWFD_CHAPTER_TYPE' ) && BWFD_CHAPTER_TYPE === $post_type ) {
		return 'chapters';
	}
	return bwfd_content_types()[ $post_type ]['slug'] ?? '';
}

/* -------------------------------------------------------------------------
 * Featured insight ("sticky") flag
 *
 * Core's sticky posts exist only for the built-in post type, so Insights
 * carry their own: the `bwfd_featured` meta, switched from a "Feature on
 * the Insights page" toggle in the editor's status panel
 * (src/admin/editor-panels.js). One Insight is featured at a time; marking
 * another clears the previous one. bwfd_featured_insight() reads it.
 * ---------------------------------------------------------------------- */

const BWFD_FEATURED_META = 'bwfd_featured';

/**
 * Register the flag for the REST API and the editor.
 */
function bwfd_register_featured_meta(): void {
	register_post_meta(
		'bwfd_insight',
		BWFD_FEATURED_META,
		array(
			'type'              => 'boolean',
			'single'            => true,
			'default'           => false,
			'description'       => __( 'Shown in the featured panel at the top of the Insights page.', 'bwfd' ),
			'sanitize_callback' => 'rest_sanitize_boolean',
			'auth_callback'     => static fn(): bool => current_user_can( 'edit_posts' ),
			'show_in_rest'      => true,
		)
	);
}
add_action( 'init', 'bwfd_register_featured_meta', 11 );

/**
 * Keep the flag on one Insight: when it is set, clear it everywhere else.
 * Runs after the meta is written, so it covers the editor, WP-CLI and code.
 *
 * @param int    $meta_id    Meta row ID.
 * @param int    $object_id  Post ID.
 * @param string $meta_key   Meta key.
 * @param mixed  $meta_value New value.
 */
function bwfd_featured_meta_exclusive( $meta_id, $object_id, $meta_key, $meta_value ): void {
	if ( BWFD_FEATURED_META !== $meta_key || ! rest_sanitize_boolean( $meta_value ) || 'bwfd_insight' !== get_post_type( (int) $object_id ) ) {
		return;
	}
	$others = get_posts(
		array(
			'post_type'      => 'bwfd_insight',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'post__not_in'   => array( (int) $object_id ),
			'meta_key'       => BWFD_FEATURED_META, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => '1', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'no_found_rows'  => true,
		)
	);
	foreach ( $others as $other_id ) {
		delete_post_meta( (int) $other_id, BWFD_FEATURED_META );
	}
}
add_action( 'added_post_meta', 'bwfd_featured_meta_exclusive', 10, 4 );
add_action( 'updated_post_meta', 'bwfd_featured_meta_exclusive', 10, 4 );

/**
 * "Featured" beside the title in the Insights list, like core's "Sticky".
 *
 * @param string[] $states Post states.
 * @param WP_Post  $post   Post.
 * @return string[]
 */
function bwfd_featured_post_state( array $states, WP_Post $post ): array {
	if ( 'bwfd_insight' === $post->post_type && get_post_meta( $post->ID, BWFD_FEATURED_META, true ) ) {
		$states['bwfd_featured'] = __( 'Featured', 'bwfd' );
	}
	return $states;
}
add_filter( 'display_post_states', 'bwfd_featured_post_state', 10, 2 );

/* -------------------------------------------------------------------------
 * News datelines
 *
 * A press release opens with where and when: "Brisbane, 19 September
 * 2026." Rather than have the writer type it, the single view prepends it
 * to the first paragraph from the post date and the publisher city
 * (Settings → Structured data), and the NewsArticle carries it as
 * `dateline`. A news item can name another city, or a dash to leave it out,
 * in the editor's status panel (src/admin/editor-panels.js).
 * ---------------------------------------------------------------------- */

const BWFD_DATELINE_META = 'bwfd_dateline';

function bwfd_register_dateline_meta(): void {
	register_post_meta(
		'bwfd_news',
		BWFD_DATELINE_META,
		array(
			'type'              => 'string',
			'single'            => true,
			'default'           => '',
			'description'       => __( 'City for the dateline; blank uses the publisher city, a dash leaves it out.', 'bwfd' ),
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => static fn(): bool => current_user_can( 'edit_posts' ),
			'show_in_rest'      => true,
		)
	);
}
add_action( 'init', 'bwfd_register_dateline_meta', 11 );

/**
 * The dateline for a news item ("Brisbane, 19 September 2026"), or '' when
 * switched off for it. Filter `bwfd_news_dateline`.
 */
function bwfd_news_dateline( WP_Post $post ): string {
	$city = trim( (string) get_post_meta( $post->ID, BWFD_DATELINE_META, true ) );
	if ( '' === $city ) {
		$city = trim( (string) ( bwfd_schema_data()['publisher']['locality'] ?? '' ) );
	}
	$dateline = ( '' === $city || '-' === $city ) ? '' : sprintf( '%s, %s', $city, get_the_date( 'j F Y', $post ) );
	return (string) apply_filters( 'bwfd_news_dateline', $dateline, $post );
}

/**
 * Prepend the dateline to the first paragraph of a news item's content on
 * its own page. Skipped when the paragraph already opens with the city.
 *
 * @param string $content Rendered post-content block.
 * @return string
 */
function bwfd_news_dateline_render( string $content ): string {
	if ( ! is_singular( 'bwfd_news' ) || ! in_the_loop() ) {
		return $content;
	}
	$post = get_post();
	if ( ! $post instanceof WP_Post || 'bwfd_news' !== $post->post_type ) {
		return $content;
	}
	$dateline = bwfd_news_dateline( $post );
	if ( '' === $dateline ) {
		return $content;
	}
	$city = explode( ',', $dateline, 2 )[0];
	if ( preg_match( '#<p\b[^>]*>(?:\s*<(?:strong|b)[^>]*>)?\s*' . preg_quote( $city, '#' ) . ',#i', $content ) ) {
		return $content;
	}
	$lead  = '<strong class="bwfd-dateline">' . esc_html( $dateline ) . '.</strong> ';
	$count = 0;
	$out   = preg_replace( '#(<p\b[^>]*>)#i', '$1' . $lead, $content, 1, $count );
	if ( 0 === $count ) {
		return '<p><strong class="bwfd-dateline">' . esc_html( $dateline ) . '.</strong></p>' . $content;
	}
	return (string) $out;
}
add_filter( 'render_block_core/post-content', 'bwfd_news_dateline_render' );

/**
 * The status-panel fields (featured toggle, dateline) on the two types.
 */
function bwfd_editor_panels_assets(): void {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || ! in_array( (string) $screen->post_type, array_merge( array_keys( bwfd_content_types() ), array( BWFD_CHAPTER_TYPE ) ), true ) ) {
		return;
	}
	$asset_file = BWFD_DIR . '/build/admin/editor-panels.asset.php';
	if ( ! file_exists( $asset_file ) ) {
		return;
	}
	$asset = require $asset_file;
	wp_enqueue_script( 'bwfd-editor-panels', BWFD_URI . '/build/admin/editor-panels.js', $asset['dependencies'], $asset['version'], true );
	wp_add_inline_script(
		'bwfd-editor-panels',
		'window.bwfdEditorPanels = ' . wp_json_encode(
			array(
				'city'     => (string) ( bwfd_schema_data()['publisher']['locality'] ?? '' ),
				'chapters' => bwfd_chapter_total(),
			)
		) . ';',
		'before'
	);
}
add_action( 'enqueue_block_editor_assets', 'bwfd_editor_panels_assets' );

/**
 * Article subtype for a post, or null for pages and anything else.
 */
function bwfd_article_type( WP_Post $post ): ?string {
	return bwfd_content_types()[ $post->post_type ]['schema'] ?? null;
}

/**
 * Post types the SEO tooling (search title meta, editor panel) applies to.
 *
 * @return string[]
 */
function bwfd_seo_post_types(): array {
	return array_merge( array( 'page' ), array_keys( bwfd_content_types() ) );
}

/* -------------------------------------------------------------------------
 * The built-in post type is retired
 *
 * News and Insights carry all ongoing writing, so core's Posts (with its
 * categories, tags and date archives) would only be an empty second blog.
 * WordPress does not allow a built-in type to be unregistered, so it is
 * switched off everywhere it shows: no admin menu or "New post", no
 * front-end URLs, not in search, sitemaps or the REST-driven editor lists
 * that go by "public". The site feed at /feed/ carries News and Insights.
 * ---------------------------------------------------------------------- */

/**
 * Turn the built-in post type and its taxonomies non-public.
 *
 * @param array<string, mixed> $args Registration arguments.
 * @param string               $name Post type or taxonomy name.
 * @return array<string, mixed>
 */
function bwfd_retire_posts_args( array $args, string $name ): array {
	if ( 'post' === $name ) {
		$args['public']              = false;
		$args['publicly_queryable']  = false;
		$args['exclude_from_search'] = true;
		$args['show_ui']             = false;
		$args['show_in_menu']        = false;
		$args['show_in_admin_bar']   = false;
		$args['show_in_nav_menus']   = false;
		$args['has_archive']         = false;
		$args['rewrite']             = false;
	}
	return $args;
}
add_filter( 'register_post_type_args', 'bwfd_retire_posts_args', 10, 2 );

/**
 * Categories and tags belong to posts alone; retire them with it.
 *
 * @param array<string, mixed> $args Registration arguments.
 * @param string               $name Taxonomy name.
 * @return array<string, mixed>
 */
function bwfd_retire_post_taxonomies_args( array $args, string $name ): array {
	if ( in_array( $name, array( 'category', 'post_tag' ), true ) ) {
		$args['public']             = false;
		$args['publicly_queryable'] = false;
		$args['show_ui']            = false;
		$args['show_in_nav_menus']  = false;
		$args['show_tagcloud']      = false;
		$args['show_in_quick_edit'] = false;
		$args['show_admin_column']  = false;
		$args['rewrite']            = false;
		$args['query_var']          = false;
	}
	return $args;
}
add_filter( 'register_taxonomy_args', 'bwfd_retire_post_taxonomies_args', 10, 2 );

/**
 * Dashboard widgets that only make sense with posts: Quick Draft and the
 * Activity feed. The post count in "At a glance" is hidden with a style.
 */
function bwfd_retire_posts_dashboard(): void {
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
}
add_action( 'wp_dashboard_setup', 'bwfd_retire_posts_dashboard' );

/**
 * Hide the posts count and the Posts link in the admin.
 */
function bwfd_retire_posts_admin_css(): void {
	echo '<style>#dashboard_right_now .post-count,#menu-posts{display:none}</style>';
}
add_action( 'admin_head', 'bwfd_retire_posts_admin_css' );

/**
 * The site feed (/feed/) lists News and Insights instead of posts. Date
 * and author feeds stay on posts and are empty.
 *
 * @param array<string, mixed> $vars Query variables from the request.
 * @return array<string, mixed>
 */
function bwfd_retire_posts_feed( array $vars ): array {
	if ( isset( $vars['feed'] ) && empty( $vars['post_type'] ) && count( array_diff_key( $vars, array_flip( array( 'feed', 'paged' ) ) ) ) === 0 ) {
		$vars['post_type'] = array_keys( bwfd_content_types() );
	}
	return $vars;
}
add_filter( 'request', 'bwfd_retire_posts_feed' );

/* -------------------------------------------------------------------------
 * Comments are off
 *
 * Nothing on the site takes comments, so the Comments menu, the admin bar
 * bubble, the dashboard widget and the discussion settings are noise.
 * ---------------------------------------------------------------------- */

/**
 * No post type supports comments or trackbacks, and nothing is open.
 */
function bwfd_disable_comments(): void {
	foreach ( get_post_types() as $post_type ) {
		remove_post_type_support( $post_type, 'comments' );
		remove_post_type_support( $post_type, 'trackbacks' );
	}
}
add_action( 'init', 'bwfd_disable_comments', 20 );
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
add_filter( 'comments_array', '__return_empty_array', 20 );

/**
 * Admin: no Comments menu or Discussion settings; the comments screen
 * sends the visitor back to the dashboard.
 */
function bwfd_disable_comments_admin_menu(): void {
	remove_menu_page( 'edit-comments.php' );
	remove_submenu_page( 'options-general.php', 'options-discussion.php' );
}
add_action( 'admin_menu', 'bwfd_disable_comments_admin_menu' );

function bwfd_disable_comments_admin_redirect(): void {
	global $pagenow;
	if ( 'edit-comments.php' === $pagenow ) {
		wp_safe_redirect( admin_url() );
		exit;
	}
}
add_action( 'admin_init', 'bwfd_disable_comments_admin_redirect' );

function bwfd_disable_comments_admin_bar( WP_Admin_Bar $bar ): void {
	$bar->remove_node( 'comments' );
}
add_action( 'admin_bar_menu', 'bwfd_disable_comments_admin_bar', 999 );

function bwfd_disable_comments_dashboard(): void {
	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
}
add_action( 'wp_dashboard_setup', 'bwfd_disable_comments_dashboard' );

/**
 * No pingback advertisement on responses.
 *
 * @param array<string, string> $headers Response headers.
 * @return array<string, string>
 */
function bwfd_disable_comments_headers( array $headers ): array {
	unset( $headers['X-Pingback'] );
	return $headers;
}
add_filter( 'wp_headers', 'bwfd_disable_comments_headers' );

/**
 * Flush rewrite rules once per rule set. The rules for /news/ and
 * /insights/ only exist after a flush, and the theme deploys by file copy,
 * so a version number keyed in an option does it on the first request after
 * a deploy rather than needing a visit to Settings → Permalinks.
 */
function bwfd_content_types_flush_rewrites(): void {
	$version = '2026-09-19.3';
	if ( get_option( 'bwfd_rewrite_version' ) !== $version ) {
		flush_rewrite_rules( false );
		update_option( 'bwfd_rewrite_version', $version );
	}
}
add_action( 'init', 'bwfd_content_types_flush_rewrites', 99 );
add_action( 'after_switch_theme', static function (): void {
	delete_option( 'bwfd_rewrite_version' );
} );
