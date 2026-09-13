/**
 * Build the Facebook Page Plugin iframe URL. Mirrors render.php.
 */
export function pluginSrc( { url, height, tabs, smallHeader, hideCover, showFacepile } ) {
	const params = new URLSearchParams( {
		href: url,
		tabs,
		width: '500',
		height: String( height ),
		small_header: smallHeader ? 'true' : 'false',
		adapt_container_width: 'true',
		hide_cover: hideCover ? 'true' : 'false',
		show_facepile: showFacepile ? 'true' : 'false',
	} );
	return `https://www.facebook.com/plugins/page.php?${ params.toString() }`;
}
