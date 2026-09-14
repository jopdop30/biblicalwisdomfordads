<?php
/**
 * Structured data settings.
 *
 * All facts used by the JSON-LD in inc/seo.php live in one option,
 * `bwfd_schema`, exposed over the REST settings endpoint and edited on
 * Settings → Structured data by a small @wordpress/components app
 * (src/admin/schema-settings.js, built to build/admin/). Defaults stay in
 * code and are merged underneath whatever is saved, so a field added later
 * starts with a sensible value. Read the merged data with bwfd_schema_data().
 *
 * @package bwfd
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

const BWFD_SCHEMA_OPTION = 'bwfd_schema';

/**
 * ID of a published page by slug, or 0.
 */
function bwfd_schema_page_id( string $slug ): int {
	$page = get_page_by_path( $slug );
	return $page instanceof WP_Post ? (int) $page->ID : 0;
}

/**
 * Image record for a theme asset.
 *
 * @return array{id:int,url:string,width:int,height:int}
 */
function bwfd_schema_theme_image( string $file, int $width, int $height ): array {
	return array(
		'id'     => 0,
		'url'    => BWFD_URI . '/assets/images/' . $file,
		'width'  => $width,
		'height' => $height,
	);
}

/**
 * Default facts. Filter `bwfd_schema_defaults` to change them in code.
 *
 * @return array<string, mixed>
 */
function bwfd_schema_defaults(): array {
	static $defaults = null;
	if ( null !== $defaults ) {
		return $defaults;
	}

	$defaults = apply_filters(
		'bwfd_schema_defaults',
		array(
			'book'      => array(
				'name'           => 'Biblical Wisdom for Dads',
				'description'    => 'In 40 short, practical chapters, dads discover how our Heavenly Father sets the ultimate example in compassion, strength, discipline, instruction and more.',
				'foreword'       => 'Richard Blackaby',
				'genre'          => 'Christian living',
				'audience'       => 'Christian fathers',
				'language'       => 'en',
				'pages'          => 142,
				'release_date'   => '2026-10-27',
				'image'          => bwfd_schema_theme_image( 'bwfd-cover.webp', 800, 1215 ),
				'editions'       => array(
					'paperback' => array(
						'isbn' => '978-1-7635863-3-8',
						'url'  => '',
					),
					'ebook'     => array(
						'isbn'         => '978-1-7635863-4-5',
						'url'          => '',
						'release_date' => '',
					),
					'audiobook' => array(
						'isbn'         => '978-1-7635863-5-2',
						'url'          => '',
						'release_date' => '',
						'narrator'     => '',
						'minutes'      => 0,
					),
				),
			),
			'offer'     => array(
				'sku'      => 'BWFD-PB',
				'price'    => '24.99',
				'currency' => 'AUD',
				'postage'  => '9.99',
				'ships_to' => 'AU',
				'buy_url'  => 'https://square.link/u/dQKD6ORW',
				// Optional; blank until known.
				'price_valid_until' => '',
				'handling_min'      => 0,
				'handling_max'      => 0,
				'transit_min'       => 0,
				'transit_max'       => 0,
				'return_days'       => 0,
				'return_fees'       => '',
				'return_postage'    => '',
			),
			'publisher' => array(
				'name'    => 'Running Forever Press',
				'same_as' => array(
					'https://www.facebook.com/biblicalwisdomfordads',
					'https://www.instagram.com/biblicalwisdomfordads',
				),
			),
			'author'    => array(
				'name'        => 'Stephen Parker',
				'job_title'   => 'Author and Associate Professor',
				'employer'    => 'Australian College of Ministries',
				'email'       => 'stephen@biblicalwisdomfordads.au',
				'description' => 'Stephen Parker loves being a dad, and is fascinated by the wisdom found in the pages of Scripture. He is a husband, father, runner and loved child of God, with one wonderful wife and four delightful daughters. With 30 years of ministry experience, he is currently an Associate Professor at the Australian College of Ministries.',
				'image'       => bwfd_schema_theme_image( 'stephen-headshot.webp', 900, 1350 ),
				'same_as'     => array(
					'https://www.linkedin.com/in/stephendparkeraus',
					'https://www.facebook.com/Stephendparker',
				),
				'other_books' => array(
					array(
						'name'  => 'There is No Finish: The Backyard Ultra Story',
						'isbn'  => '',
						'url'   => 'https://www.amazon.com/dp/B0DD42GD27',
						'image' => bwfd_schema_theme_image( 'no-finish-front.webp', 700, 1054 ),
					),
					array(
						'name'  => 'The Heart of an Elder',
						'isbn'  => '',
						'url'   => 'https://amzn.to/3VhFBf1',
						'image' => bwfd_schema_theme_image( 'heart-elder-front.webp', 700, 1067 ),
					),
				),
			),
			'pages'     => array(
				'about_book'       => bwfd_schema_page_id( 'about-the-book' ),
				'purchase'         => bwfd_schema_page_id( 'purchase' ),
				'about_author'     => bwfd_schema_page_id( 'about-the-author' ),
				'other_books'      => bwfd_schema_page_id( 'other-books' ),
				'extra_book_pages' => array_values(
					array_filter(
						array(
							(int) get_option( 'page_on_front' ),
							bwfd_schema_page_id( 'churches-and-retail' ),
						)
					)
				),
			),
		)
	);

	return $defaults;
}

/**
 * Field specification: a JSON schema with two private hints, `kind`
 * (`url` or `multiline`) for sanitising and `title` for the REST docs.
 * bwfd_schema_rest_schema() strips the hints.
 *
 * @return array<string, mixed>
 */
function bwfd_schema_spec(): array {
	$text  = static fn( string $title, string $kind = '' ): array => array_filter( array( 'type' => 'string', 'title' => $title, 'kind' => $kind ) );
	$int   = static fn( string $title ): array => array( 'type' => 'integer', 'title' => $title, 'minimum' => 0 );
	$image = static fn( string $title ): array => array(
		'type'       => 'object',
		'title'      => $title,
		'properties' => array(
			'id'     => $int( 'Attachment ID' ),
			'url'    => $text( 'URL', 'url' ),
			'width'  => $int( 'Width' ),
			'height' => $int( 'Height' ),
		),
	);
	$urls  = static fn( string $title ): array => array( 'type' => 'array', 'title' => $title, 'items' => $text( 'URL', 'url' ) );

	return array(
		'type'       => 'object',
		'properties' => array(
			'book'      => array(
				'type'       => 'object',
				'properties' => array(
					'name'           => $text( 'Title' ),
					'description'    => $text( 'Description', 'multiline' ),
					'foreword'       => $text( 'Foreword by' ),
					'genre'          => $text( 'Genre' ),
					'audience'       => $text( 'Audience' ),
					'language'       => $text( 'Language' ),
					'pages'          => $int( 'Pages' ),
					'release_date'   => $text( 'Release date' ),
					'image'          => $image( 'Cover' ),
					'editions'       => array(
						'type'       => 'object',
						'title'      => 'Editions',
						'properties' => array(
							'paperback' => array(
								'type'       => 'object',
								'properties' => array(
									'isbn' => $text( 'ISBN' ),
									'url'  => $text( 'Store link', 'url' ),
								),
							),
							'ebook'     => array(
								'type'       => 'object',
								'properties' => array(
									'isbn'         => $text( 'ISBN' ),
									'url'          => $text( 'Store link', 'url' ),
									'release_date' => $text( 'Release date' ),
								),
							),
							'audiobook' => array(
								'type'       => 'object',
								'properties' => array(
									'isbn'         => $text( 'ISBN' ),
									'url'          => $text( 'Store link', 'url' ),
									'release_date' => $text( 'Release date' ),
									'narrator'     => $text( 'Narrator' ),
									'minutes'      => $int( 'Running time in minutes' ),
								),
							),
						),
					),
				),
			),
			'offer'     => array(
				'type'       => 'object',
				'properties' => array(
					'sku'      => $text( 'SKU' ),
					'price'    => $text( 'Price' ),
					'currency' => $text( 'Currency' ),
					'postage'  => $text( 'Postage' ),
					'ships_to' => $text( 'Ships to' ),
					'buy_url'  => $text( 'Checkout link', 'url' ),
					'price_valid_until' => $text( 'Price valid until' ),
					'handling_min'      => $int( 'Handling time, minimum days' ),
					'handling_max'      => $int( 'Handling time, maximum days' ),
					'transit_min'       => $int( 'Transit time, minimum days' ),
					'transit_max'       => $int( 'Transit time, maximum days' ),
					'return_days'       => $int( 'Return window in days' ),
					'return_fees'       => array( 'type' => 'string', 'title' => 'Return postage', 'enum' => array( '', 'free', 'customer' ) ),
					'return_postage'    => $text( 'Return postage cost' ),
				),
			),
			'publisher' => array(
				'type'       => 'object',
				'properties' => array(
					'name'    => $text( 'Name' ),
					'same_as' => $urls( 'Profiles' ),
				),
			),
			'author'    => array(
				'type'       => 'object',
				'properties' => array(
					'name'        => $text( 'Name' ),
					'job_title'   => $text( 'Role' ),
					'employer'    => $text( 'Organisation' ),
					'email'       => $text( 'Email' ),
					'description' => $text( 'Biography', 'multiline' ),
					'image'       => $image( 'Portrait' ),
					'same_as'     => $urls( 'Profiles' ),
					'other_books' => array(
						'type'  => 'array',
						'title' => 'Other books',
						'items' => array(
							'type'       => 'object',
							'properties' => array(
								'name'  => $text( 'Title' ),
								'isbn'  => $text( 'ISBN' ),
								'url'   => $text( 'Link', 'url' ),
								'image' => $image( 'Cover' ),
							),
						),
					),
				),
			),
			'pages'     => array(
				'type'       => 'object',
				'properties' => array(
					'about_book'       => $int( 'About the book page' ),
					'purchase'         => $int( 'Purchase page' ),
					'about_author'     => $int( 'About the author page' ),
					'other_books'      => $int( 'Other books page' ),
					'extra_book_pages' => array( 'type' => 'array', 'title' => 'Other pages carrying the book', 'items' => $int( 'Page ID' ) ),
				),
			),
		),
	);
}

/**
 * The spec as a plain JSON schema for the REST settings endpoint.
 *
 * @param array<string, mixed>|null $spec Node to convert; the whole spec when null.
 * @return array<string, mixed>
 */
function bwfd_schema_rest_schema( ?array $spec = null ): array {
	$spec = $spec ?? bwfd_schema_spec();
	unset( $spec['kind'] );
	if ( isset( $spec['properties'] ) ) {
		foreach ( $spec['properties'] as $key => $child ) {
			$spec['properties'][ $key ] = bwfd_schema_rest_schema( $child );
		}
	}
	if ( isset( $spec['items'] ) ) {
		$spec['items'] = bwfd_schema_rest_schema( $spec['items'] );
	}
	return $spec;
}

/**
 * Sanitise a value against the spec (option sanitize_callback).
 *
 * @param mixed                     $value Value to sanitise.
 * @param array<string, mixed>|null $spec  Spec node; the whole spec when null.
 * @return mixed
 */
function bwfd_schema_sanitize( $value, ?array $spec = null ) {
	$spec = $spec ?? bwfd_schema_spec();

	switch ( $spec['type'] ) {
		case 'object':
			$value = is_array( $value ) ? $value : array();
			$out   = array();
			foreach ( $spec['properties'] as $key => $child ) {
				if ( array_key_exists( $key, $value ) ) {
					$out[ $key ] = bwfd_schema_sanitize( $value[ $key ], $child );
				}
			}
			return $out;

		case 'array':
			$value = is_array( $value ) ? array_values( $value ) : array();
			return array_map( static fn( $item ) => bwfd_schema_sanitize( $item, $spec['items'] ), $value );

		case 'integer':
			return absint( $value );

		default:
			$value = is_scalar( $value ) ? (string) $value : '';
			if ( isset( $spec['enum'] ) ) {
				return in_array( $value, $spec['enum'], true ) ? $value : '';
			}
			switch ( $spec['kind'] ?? '' ) {
				case 'url':
					return esc_url_raw( trim( $value ) );
				case 'multiline':
					return sanitize_textarea_field( $value );
				default:
					return sanitize_text_field( $value );
			}
	}
}

/**
 * Saved values over defaults, key by key. Lists (profiles, other books,
 * page IDs) are taken whole from the saved data.
 *
 * @param array<string, mixed> $defaults Defaults.
 * @param array<string, mixed> $saved    Saved option.
 * @return array<string, mixed>
 */
function bwfd_schema_merge( array $defaults, array $saved ): array {
	foreach ( $defaults as $key => $default ) {
		if ( ! array_key_exists( $key, $saved ) ) {
			continue;
		}
		$value   = $saved[ $key ];
		$is_map  = is_array( $default ) && array() !== $default && array_keys( $default ) !== range( 0, count( $default ) - 1 );
		$defaults[ $key ] = ( $is_map && is_array( $value ) ) ? bwfd_schema_merge( $default, $value ) : $value;
	}
	return $defaults;
}

/**
 * The merged structured-data facts. Filter `bwfd_schema_data` for
 * request-time changes.
 *
 * @return array<string, mixed>
 */
function bwfd_schema_data(): array {
	$data = get_option( BWFD_SCHEMA_OPTION );
	return apply_filters( 'bwfd_schema_data', is_array( $data ) ? $data : bwfd_schema_defaults() );
}

/**
 * get_option() always returns the full, merged structure.
 */
function bwfd_schema_option_filter( $value ): array {
	return bwfd_schema_merge( bwfd_schema_defaults(), is_array( $value ) ? $value : array() );
}
add_filter( 'option_' . BWFD_SCHEMA_OPTION, 'bwfd_schema_option_filter' );
add_filter( 'default_option_' . BWFD_SCHEMA_OPTION, static fn() => bwfd_schema_defaults() );

/**
 * Register the option for the REST settings endpoint.
 */
function bwfd_schema_register(): void {
	register_setting(
		'bwfd_schema',
		BWFD_SCHEMA_OPTION,
		array(
			'type'              => 'object',
			'description'       => __( 'Facts used for the site’s structured data (JSON-LD).', 'bwfd' ),
			'sanitize_callback' => 'bwfd_schema_sanitize',
			'show_in_rest'      => array( 'schema' => bwfd_schema_rest_schema() ),
		)
	);
}
add_action( 'init', 'bwfd_schema_register' );

/**
 * Settings → Structured data.
 */
function bwfd_schema_admin_menu(): void {
	add_options_page(
		__( 'Structured data', 'bwfd' ),
		__( 'Structured data', 'bwfd' ),
		'manage_options',
		'bwfd-schema',
		'bwfd_schema_render_page'
	);
}
add_action( 'admin_menu', 'bwfd_schema_admin_menu' );

/**
 * Page shell; the app renders into #bwfd-schema-settings.
 */
function bwfd_schema_render_page(): void {
	echo '<div class="wrap bwfd-schema-wrap"><h1>' . esc_html__( 'Structured data', 'bwfd' ) . '</h1>';
	if ( ! file_exists( BWFD_DIR . '/build/admin/schema-settings.asset.php' ) ) {
		echo '<div class="notice notice-error"><p>' . esc_html__( 'The settings app has not been built. Run `npm run build` in the theme folder.', 'bwfd' ) . '</p></div>';
	}
	echo '<div id="bwfd-schema-settings"></div></div>';
}

/**
 * Scripts and styles for the settings page only.
 */
function bwfd_schema_admin_assets( string $hook ): void {
	if ( 'settings_page_bwfd-schema' !== $hook ) {
		return;
	}
	$asset_file = BWFD_DIR . '/build/admin/schema-settings.asset.php';
	if ( ! file_exists( $asset_file ) ) {
		return;
	}
	$asset = require $asset_file;

	wp_enqueue_media();
	wp_enqueue_style( 'wp-components' );
	wp_enqueue_script(
		'bwfd-schema-settings',
		BWFD_URI . '/build/admin/schema-settings.js',
		$asset['dependencies'],
		$asset['version'],
		true
	);
	if ( file_exists( BWFD_DIR . '/build/admin/schema-settings.css' ) ) {
		wp_enqueue_style(
			'bwfd-schema-settings',
			BWFD_URI . '/build/admin/schema-settings.css',
			array( 'wp-components' ),
			$asset['version']
		);
	}
	wp_add_inline_script(
		'bwfd-schema-settings',
		'window.bwfdSchemaSettings = ' . wp_json_encode(
			array(
				'defaults' => bwfd_schema_defaults(),
				'homeUrl'  => home_url( '/' ),
			)
		) . ';',
		'before'
	);
}
add_action( 'admin_enqueue_scripts', 'bwfd_schema_admin_assets' );
