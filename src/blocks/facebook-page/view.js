/**
 * Facebook page feed – insert the Page Plugin iframe as the block nears
 * the viewport.
 *
 * render.php reserves the iframe's height and puts its details on the
 * wrapper (data-src, data-title, data-height). When the block comes within
 * a screen of view the iframe replaces the reserved box; the <noscript>
 * copy covers browsers without JavaScript.
 */
const ROOT = '.bwfd-facebook-page';
const MARGIN = '100% 0px';

function insert( root ) {
	const { src, title, height } = root.dataset;
	const frame = root.querySelector( '.bwfd-facebook-page__frame' );
	if ( ! src || ! frame || root.querySelector( 'iframe' ) ) {
		return;
	}
	const iframe = document.createElement( 'iframe' );
	iframe.src = src;
	iframe.title = title || '';
	iframe.height = height || '600';
	iframe.style.cssText = 'border:none;overflow:hidden';
	iframe.setAttribute( 'scrolling', 'no' );
	iframe.setAttribute( 'allowfullscreen', 'true' );
	iframe.setAttribute( 'allow', 'autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share' );
	frame.replaceWith( iframe );
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
