<?php
/**
 * Inline SVG icon library shared by the icon block (PHP render) and the
 * editor (exposed as window.bwfdIcons). Keep in sync with src/blocks/icon/icons.js.
 *
 * @package bwfd
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Icon inner markup keyed by slug. All icons use a 24x24 viewBox and
 * inherit stroke/fill from CSS via currentColor.
 *
 * @return array<string, array{label:string, paths:string, filled?:bool}>
 */
function bwfd_icon_paths(): array {
	return array(
		'compass'   => array(
			'label' => __( 'Compass', 'bwfd' ),
			'paths' => '<circle cx="12" cy="12" r="9"/><path d="m15.5 8.5-2.2 5.3-5.3 2.2 2.2-5.3z"/>',
		),
		'book'      => array(
			'label' => __( 'Open book', 'bwfd' ),
			'paths' => '<path d="M12 6.5S10 4.5 3 5v13c7-.5 9 1.5 9 1.5s2-2 9-1.5V5c-7-.5-9 1.5-9 1.5Z"/><path d="M12 6.5v13"/>',
		),
		'chat'      => array(
			'label' => __( 'Speech bubble', 'bwfd' ),
			'paths' => '<path d="M21 12a8.5 8.5 0 0 1-12.3 7.6L3.5 21l1.4-5.2A8.5 8.5 0 1 1 21 12Z"/>',
		),
		'shield'    => array(
			'label' => __( 'Shield', 'bwfd' ),
			'paths' => '<path d="M12 3 5 6v5.5c0 4.6 3 7.4 7 9.5 4-2.1 7-4.9 7-9.5V6l-7-3Z"/>',
		),
		'group'     => array(
			'label' => __( 'Group', 'bwfd' ),
			'paths' => '<circle cx="9" cy="9" r="3.2"/><path d="M3.5 19a5.5 5.5 0 0 1 11 0"/><path d="M16 6.3a3.2 3.2 0 0 1 0 5.4"/><path d="M17.5 14.2A5.5 5.5 0 0 1 20.5 19"/>',
		),
		'gift'      => array(
			'label' => __( 'Gift', 'bwfd' ),
			'paths' => '<rect x="3.5" y="8.5" width="17" height="11.5" rx="1"/><path d="M3.5 12.5h17"/><path d="M12 8.5V20"/><path d="M12 8.5S10.5 4 8 4a2.2 2.2 0 0 0 0 4.5h4Z"/><path d="M12 8.5S13.5 4 16 4a2.2 2.2 0 0 1 0 4.5h-4Z"/>',
		),
		'star'      => array(
			'label' => __( 'Star', 'bwfd' ),
			'paths' => '<path d="M12 3.5l2.6 5.4 5.9.8-4.3 4.2 1 5.9-5.2-2.8-5.2 2.8 1-5.9L3.5 9.7l5.9-.8z"/>',
		),
		'camera'    => array(
			'label' => __( 'Camera', 'bwfd' ),
			'paths' => '<path d="M4 8.5h2.6l1.3-2h8.2l1.3 2H20a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-9a1 1 0 0 1 1-1Z"/><circle cx="12" cy="13.5" r="3.4"/>',
		),
		'heart'     => array(
			'label' => __( 'Heart', 'bwfd' ),
			'paths' => '<path d="M12 20s-7-4.4-7-9.5A4 4 0 0 1 12 8a4 4 0 0 1 7 2.5C19 15.6 12 20 12 20Z"/>',
		),
		'download'  => array(
			'label' => __( 'Download', 'bwfd' ),
			'paths' => '<path d="M12 4v11"/><path d="m7.5 10.5 4.5 4.5 4.5-4.5"/><path d="M4 19.5h16"/>',
		),
		'mail'      => array(
			'label' => __( 'Mail', 'bwfd' ),
			'paths' => '<rect x="3.5" y="5.5" width="17" height="13" rx="1.5"/><path d="m4 7 8 6 8-6"/>',
		),
		'facebook'  => array(
			'label'  => __( 'Facebook', 'bwfd' ),
			'filled' => true,
			'paths'  => '<path d="M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.4h-1.2c-1.2 0-1.6.8-1.6 1.6V12h2.7l-.4 2.9h-2.3v7A10 10 0 0 0 22 12Z"/>',
		),
		'instagram' => array(
			'label'  => __( 'Instagram', 'bwfd' ),
			'filled' => true,
			'paths'  => '<path d="M12 2c2.7 0 3 0 4.1.1 1 0 1.8.2 2.4.5.7.3 1.2.6 1.7 1.2.6.5.9 1 1.2 1.7.3.6.4 1.4.5 2.4 0 1.1.1 1.4.1 4.1s0 3-.1 4.1c0 1-.2 1.8-.5 2.4-.3.7-.6 1.2-1.2 1.7-.5.6-1 .9-1.7 1.2-.6.3-1.4.4-2.4.5-1.1 0-1.4.1-4.1.1s-3 0-4.1-.1c-1 0-1.8-.2-2.4-.5-.7-.3-1.2-.6-1.7-1.2-.6-.5-.9-1-1.2-1.7-.3-.6-.4-1.4-.5-2.4C2 15 2 14.7 2 12s0-3 .1-4.1c0-1 .2-1.8.5-2.4.3-.7.6-1.2 1.2-1.7.5-.6 1-.9 1.7-1.2.6-.3 1.4-.4 2.4-.5C9 2 9.3 2 12 2Zm0 5a5 5 0 1 0 0 10 5 5 0 0 0 0-10Zm0 8.2a3.2 3.2 0 1 1 0-6.4 3.2 3.2 0 0 1 0 6.4ZM18.4 6.8a1.2 1.2 0 1 1-2.4 0 1.2 1.2 0 0 1 2.4 0Z"/>',
		),
	);
}

/**
 * Render an icon as inline SVG.
 *
 * @param string $slug  Icon slug.
 * @param int    $size  Pixel size.
 * @param array  $attrs Extra attributes (class, aria-label).
 * @return string
 */
function bwfd_icon_svg( string $slug, int $size = 24, array $attrs = array() ): string {
	$icons = bwfd_icon_paths();
	if ( ! isset( $icons[ $slug ] ) ) {
		return '';
	}
	$icon   = $icons[ $slug ];
	$filled = ! empty( $icon['filled'] );

	$attributes = array_merge(
		array(
			'width'       => (string) $size,
			'height'      => (string) $size,
			'viewBox'     => '0 0 24 24',
			'aria-hidden' => 'true',
			'focusable'   => 'false',
		),
		$filled
			? array( 'fill' => 'currentColor' )
			: array(
				'fill'            => 'none',
				'stroke'          => 'currentColor',
				'stroke-width'    => '1.5',
				'stroke-linecap'  => 'round',
				'stroke-linejoin' => 'round',
			),
		$attrs
	);

	$attr_string = '';
	foreach ( $attributes as $key => $value ) {
		$attr_string .= sprintf( ' %s="%s"', esc_attr( $key ), esc_attr( $value ) );
	}

	return sprintf( '<svg%s>%s</svg>', $attr_string, $icon['paths'] );
}
