/**
 * Facebook page feed – insert the Page Plugin iframe as the block nears
 * the viewport.
 *
 * render.php reserves the iframe's height and puts its details on the
 * wrapper (data-src, data-title, data-height). When the block comes within
 * a screen of view the iframe replaces the reserved box, with the plugin
 * width in its URL set to the width the block has; the <noscript> copy
 * covers browsers without JavaScript.
 */
const ROOT = '.bwfd-facebook-page';
const MARGIN = '100% 0px';

// Facebook renders the plugin at the `width` in the iframe URL (180–500px)
// and ignores the iframe's own size, so the URL has to carry the width the
// block actually has, otherwise the right edge is clipped.
const MIN_WIDTH = 180;
const MAX_WIDTH = 500;

function widthFor( root ) {
	return Math.max( MIN_WIDTH, Math.min( MAX_WIDTH, Math.round( root.clientWidth ) || MAX_WIDTH ) );
}

function srcFor( root, width ) {
	const url = new URL( root.dataset.src, window.location.href );
	url.searchParams.set( 'width', String( width ) );
	return url.toString();
}

function insert( root ) {
	const { src, title, height } = root.dataset;
	const frame = root.querySelector( '.bwfd-facebook-page__frame' );
	if ( ! src || ! frame || root.querySelector( 'iframe' ) ) {
		return;
	}
	const width = widthFor( root );
	const iframe = document.createElement( 'iframe' );
	iframe.src = srcFor( root, width );
	iframe.title = title || '';
	iframe.width = String( width );
	iframe.height = height || '600';
	iframe.style.cssText = 'border:none;overflow:hidden';
	iframe.setAttribute( 'scrolling', 'no' );
	iframe.setAttribute( 'allowfullscreen', 'true' );
	iframe.setAttribute( 'allow', 'autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share' );
	root.dataset.width = String( width );
	frame.replaceWith( iframe );
}

// After a resize that changes the available width (a phone rotating, a
// window dragged narrower) reload the plugin at the new width. Small
// changes are absorbed by the iframe's max-width rather than a reload.
function refit( root ) {
	const iframe = root.querySelector( 'iframe' );
	if ( ! iframe ) {
		return;
	}
	const width = widthFor( root );
	if ( Math.abs( width - Number( root.dataset.width || 0 ) ) < 24 ) {
		return;
	}
	root.dataset.width = String( width );
	iframe.width = String( width );
	iframe.src = srcFor( root, width );
}

const roots = document.querySelectorAll( ROOT );

if ( ! ( 'IntersectionObserver' in window ) ) {
	roots.forEach( insert );
} else {
	const observer = new IntersectionObserver(
		( entries ) => {
			for ( const entry of entries ) {
				if ( entry.isIntersecting ) {
					insert( entry.target );
					observer.unobserve( entry.target );
				}
			}
		},
		{ rootMargin: MARGIN }
	);
	roots.forEach( ( root ) => observer.observe( root ) );
}

let resizeTimer = 0;
window.addEventListener( 'resize', () => {
	window.clearTimeout( resizeTimer );
	resizeTimer = window.setTimeout( () => roots.forEach( refit ), 250 );
} );
