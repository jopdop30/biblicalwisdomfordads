export function heroProps( { wash, reverse, backgroundUrl } ) {
	return {
		className: [ 'bwfd-hero', `bwfd-hero--${ wash }`, reverse ? 'bwfd-hero--reverse' : '' ]
			.filter( Boolean )
			.join( ' ' ),
		style: backgroundUrl ? { backgroundImage: `url(${ backgroundUrl })` } : undefined,
	};
}
