export function frameClassName( { panel, shadow } ) {
	return [ 'bwfd-framed-image', panel ? 'bwfd-framed-image--panel' : '', `bwfd-framed-image--shadow-${ shadow }` ]
		.filter( Boolean )
		.join( ' ' );
}

export function imageStyle( aspectRatio ) {
	return aspectRatio ? { aspectRatio, objectFit: 'cover' } : undefined;
}
