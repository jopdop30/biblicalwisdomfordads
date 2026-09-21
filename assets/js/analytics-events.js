/**
 * Google Analytics custom events.
 *
 * Site Kit prints the Google tag and defines the global gtag() in <head>;
 * this file only adds events for the actions that matter on a book site.
 * Nothing here runs when gtag() is absent (Site Kit off, or a logged-in
 * user Site Kit excludes), and every event is derived from the markup
 * the editor produces, so no data attributes are needed on links.
 *
 * Events (all parameters also carry page_location etc. automatically):
 *   begin_checkout  a Square checkout link          purchase_option, value, items
 *   retailer_click  Amazon or another retailer      retailer, book_title
 *   contact_click   a mailto: link                  (context params only)
 *   rsvp_click      a TryBooking link               (context params only)
 *   social_click    Facebook / Instagram / LinkedIn social_network
 *   cta_click       an internal button-styled link  link_url
 *   panel_open      a Reveal panel opened           panel_label
 *   content_read    end of a chapter/insight/news   content_type, chapter_number
 *
 * Every link event carries link_text, link_context (nearest card or
 * section heading) and link_location (hero, card, book_band, footer …).
 * Outbound clicks in general, file downloads, scroll depth and site
 * search are left to GA4 enhanced measurement.
 */
( function () {
	'use strict';

	var MAIN_TITLE = 'Biblical Wisdom for Dads';
	var MAX = 100; // GA4 caps parameter values at 100 characters.

	function send( name, params ) {
		if ( typeof window.gtag !== 'function' ) {
			return;
		}
		window.gtag( 'event', name, params );
	}

	function clip( text ) {
		text = ( text || '' ).replace( /\s+/g, ' ' ).trim();
		return text.length > MAX ? text.slice( 0, MAX - 1 ) + '…' : text;
	}

	function textOf( el ) {
		if ( ! el ) {
			return '';
		}
		var img = el.querySelector && el.querySelector( 'img[alt]' );
		var text = el.textContent;
		if ( ! text.trim() && img ) {
			text = img.getAttribute( 'alt' );
		}
		return clip( text );
	}

	function headingIn( el ) {
		var h = el && el.querySelector( 'h1, h2, h3' );
		return h ? clip( h.textContent ) : '';
	}

	function contextFor( a ) {
		// Header and footer links belong to the whole site, not to the page
		// heading they happen to sit under.
		if ( a.closest( 'header' ) ) {
			return 'Header';
		}
		if ( a.closest( 'footer' ) ) {
			return 'Footer';
		}
		var card = a.closest( '.bwfd-card, .bwfd-book-card, .bwfd-book-band, .bwfd-featured-insight' );
		var text = headingIn( card );
		if ( text ) {
			return text;
		}
		var section = a.closest( '.bwfd-section, .bwfd-hero, .wp-block-group.alignfull, article' );
		text = headingIn( section );
		if ( text ) {
			return text;
		}
		return headingIn( document );
	}

	function locationFor( a ) {
		if ( a.closest( 'header' ) ) {
			return 'header';
		}
		if ( a.closest( 'footer' ) ) {
			return 'footer';
		}
		if ( a.closest( '.bwfd-hero' ) ) {
			return 'hero';
		}
		if ( a.closest( '.bwfd-book-band' ) ) {
			return 'book_band';
		}
		if ( a.closest( '.bwfd-book-card' ) ) {
			return 'book_card';
		}
		if ( a.closest( '.bwfd-card' ) ) {
			return 'card';
		}
		if ( a.closest( '.bwfd-article__body' ) ) {
			return 'article';
		}
		return 'content';
	}

	function baseParams( a ) {
		return {
			link_text: textOf( a ),
			link_context: contextFor( a ),
			link_location: locationFor( a ),
		};
	}

	function hostMatches( host, domain ) {
		return host === domain || host.slice( -( domain.length + 1 ) ) === '.' + domain;
	}

	/**
	 * Purchase option and price, read from the card heading the button sits
	 * in ("Bulk Orders (20+) $15.99 free postage", "Local Pickup $24.99",
	 * "Delivery $24.99 +$9.99 postage"), so new Square links need no code.
	 */
	function checkoutParams( a, params ) {
		var heading = params.link_context.toLowerCase();
		var option = 'other';
		if ( heading.indexOf( 'bulk' ) !== -1 ) {
			option = 'bulk_20_plus';
		} else if ( heading.indexOf( 'pickup' ) !== -1 || heading.indexOf( 'pick up' ) !== -1 ) {
			option = 'local_pickup';
		} else if ( heading.indexOf( 'deliver' ) !== -1 || heading.indexOf( 'direct' ) !== -1 || heading.indexOf( 'post' ) !== -1 ) {
			option = 'direct_delivery';
		}
		var priceMatch = /\$\s?(\d+(?:\.\d{1,2})?)/.exec( params.link_context );
		var price = priceMatch ? parseFloat( priceMatch[ 1 ] ) : 0;
		var quantity = option === 'bulk_20_plus' ? 20 : 1;
		var value = Math.round( price * quantity * 100 ) / 100;

		params.purchase_option = option;
		params.currency = 'AUD';
		params.value = value;
		params.items = [
			{
				item_id: 'bwfd-paperback',
				item_name: MAIN_TITLE,
				item_variant: option,
				price: price,
				quantity: quantity,
			},
		];
		return params;
	}

	var RETAILERS = [
		[ 'amazon.com', 'amazon' ],
		[ 'amazon.com.au', 'amazon' ],
		[ 'amzn.to', 'amazon' ],
		[ 'amzn.asia', 'amazon' ],
		[ 'koorong.com', 'koorong' ],
		[ 'booktopia.com.au', 'booktopia' ],
		[ 'runningforever.au', 'running_forever_press' ],
	];

	var SOCIAL = [
		[ 'facebook.com', 'facebook' ],
		[ 'fb.com', 'facebook' ],
		[ 'instagram.com', 'instagram' ],
		[ 'linkedin.com', 'linkedin' ],
		[ 'youtube.com', 'youtube' ],
		[ 'goodreads.com', 'goodreads' ],
	];

	function lookup( table, host ) {
		for ( var i = 0; i < table.length; i++ ) {
			if ( hostMatches( host, table[ i ][ 0 ] ) ) {
				return table[ i ][ 1 ];
			}
		}
		return '';
	}

	function trackLink( a ) {
		var href = a.getAttribute( 'href' ) || '';
		if ( ! href || href.charAt( 0 ) === '#' || href.indexOf( 'javascript:' ) === 0 ) {
			return;
		}
		var params = baseParams( a );
		var url;
		try {
			url = new URL( a.href, window.location.href );
		} catch ( e ) {
			return;
		}

		// Cloudflare rewrites mailto links to /cdn-cgi/l/email-protection and
		// decodes them back on load; either form is a contact click.
		if ( url.protocol === 'mailto:' || url.pathname.indexOf( '/cdn-cgi/l/email-protection' ) === 0 ) {
			send( 'contact_click', params );
			return;
		}
		if ( url.protocol === 'tel:' ) {
			send( 'contact_click', params );
			return;
		}

		var host = url.hostname.toLowerCase();

		if ( hostMatches( host, 'square.link' ) || hostMatches( host, 'squareup.com' ) || hostMatches( host, 'square.site' ) ) {
			send( 'begin_checkout', checkoutParams( a, params ) );
			return;
		}

		var retailer = lookup( RETAILERS, host );
		if ( retailer ) {
			params.retailer = retailer;
			// On the Other books page the card heading is the book's title; on the
			// Purchase page it names the retailer, so the main book is meant.
			params.book_title = /amazon|ebook|paperback|print|purchase|buy/i.test( params.link_context ) ? MAIN_TITLE : params.link_context;
			send( 'retailer_click', params );
			return;
		}

		if ( hostMatches( host, 'trybooking.com' ) ) {
			send( 'rsvp_click', params );
			return;
		}

		var network = lookup( SOCIAL, host );
		if ( network ) {
			params.social_network = network;
			send( 'social_click', params );
			return;
		}

		if ( host === window.location.hostname.toLowerCase() && a.classList.contains( 'wp-element-button' ) ) {
			params.link_url = clip( url.pathname );
			send( 'cta_click', params );
		}
	}

	function trackReveal( button ) {
		if ( button.getAttribute( 'aria-expanded' ) === 'false' ) {
			send( 'panel_open', { panel_label: clip( button.textContent ) } );
		}
	}

	function onClick( event ) {
		if ( event.type === 'auxclick' && event.button !== 1 ) {
			return;
		}
		var target = event.target;
		if ( ! target || ! target.closest ) {
			return;
		}
		var a = target.closest( 'a[href]' );
		if ( a ) {
			trackLink( a );
			return;
		}
		var toggle = target.closest( '.bwfd-reveal__toggle' );
		if ( toggle && event.type === 'click' ) {
			trackReveal( toggle );
		}
	}

	// Capture phase: runs before the Interactivity API flips aria-expanded
	// and before any handler that might stop propagation.
	document.addEventListener( 'click', onClick, true );
	document.addEventListener( 'auxclick', onClick, true );

	/**
	 * content_read: the bottom of a chapter, insight or news article has
	 * come into view. Enhanced measurement's 90% scroll includes the
	 * related-insights and buy band below, so it says less than this.
	 */
	function trackContentRead() {
		var types = { 'single-bwfd_chapter': 'chapter', 'single-bwfd_insight': 'insight', 'single-bwfd_news': 'news' };
		var type = '';
		for ( var cls in types ) {
			if ( document.body.classList.contains( cls ) ) {
				type = types[ cls ];
			}
		}
		var body = document.querySelector( '.bwfd-article__body' );
		if ( ! type || ! body || ! ( 'IntersectionObserver' in window ) ) {
			return;
		}
		var sentinel = body.lastElementChild || body;
		var params = { content_type: type };
		if ( type === 'chapter' ) {
			var kicker = document.querySelector( '.bwfd-article__kicker' );
			var m = kicker && /(\d+)/.exec( kicker.textContent );
			if ( m ) {
				params.chapter_number = parseInt( m[ 1 ], 10 );
			}
		}
		var observer = new IntersectionObserver( function ( entries ) {
			for ( var i = 0; i < entries.length; i++ ) {
				if ( entries[ i ].isIntersecting ) {
					send( 'content_read', params );
					observer.disconnect();
					return;
				}
			}
		} );
		observer.observe( sentinel );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', trackContentRead );
	} else {
		trackContentRead();
	}
} )();
