/**
 * Facebook page feed – load the Page Plugin iframe on request.
 *
 * render.php prints a placeholder with the iframe details on the block
 * wrapper (data-src, data-title, data-height). The first hover or focus on
 * the button warms the Facebook connections; a click swaps the iframe in.
 */
const ROOT = '.bwfd-facebook-page';
const ORIGINS = [ 'https://www.facebook.com', 'https://static.xx.fbcdn.net' ];

let warmed = false;

function preconnect() {
	if ( warmed ) {
		return;
	}
	warmed = true;
	for ( const href of ORIGINS ) {
		const link = document.createElement( 'link' );
		link.rel = 'preconnect';
		link.href = href;
		document.head.appendChild( link );
	}
}

function load( root ) {
	const { src, title, height } = root.dataset;
	const placeholder = root.querySelector( '.bwfd-facebook-page__placeholder' );
	if ( ! src || ! placeholder ) {
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
	root.replaceChild( iframe, placeholder );
	root.classList.add( 'bwfd-facebook-page--loaded' );
	iframe.focus();
}

document.addEventListener( 'click', ( event ) => {
	const button = event.target.closest( '.bwfd-facebook-page__load' );
	if ( button ) {
		load( button.closest( ROOT ) );
	}
} );

for ( const type of [ 'pointerover', 'focusin', 'touchstart' ] ) {
	document.addEventListener(
		type,
		( event ) => {
			if ( event.target.closest( '.bwfd-facebook-page__load' ) ) {
				preconnect();
			}
		},
		{ passive: true }
	);
}
