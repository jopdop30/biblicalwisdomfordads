<?php
/**
 * Google Analytics custom events.
 *
 * Site Kit prints the Google tag; assets/js/analytics-events.js adds the
 * events that matter on a book site (checkout, retailer, contact, RSVP,
 * social and call-to-action clicks, panel opens, articles read to the end).
 * The script is plain JavaScript with no build step and does nothing when
 * gtag() is not on the page.
 *
 * @package bwfd
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue the events script on the front end.
 *
 * Filter `bwfd_analytics_events` to false to leave it out.
 */
function bwfd_analytics_enqueue(): void {
	if ( is_admin() || ! apply_filters( 'bwfd_analytics_events', true ) ) {
		return;
	}
	wp_enqueue_script(
		'bwfd-analytics-events',
		BWFD_URI . '/assets/js/analytics-events.js',
		array(),
		BWFD_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'bwfd_analytics_enqueue' );
