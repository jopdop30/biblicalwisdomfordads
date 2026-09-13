export function gridProps( { columns, minWidth, gap, centered, maxWidth } ) {
	const classes = [
		'bwfd-card-grid',
		columns > 0 ? 'bwfd-card-grid--fixed' : 'bwfd-card-grid--auto',
		centered ? 'bwfd-card-grid--centered' : '',
	]
		.filter( Boolean )
		.join( ' ' );

	const style = {
		'--bwfd-grid-min': `${ minWidth }px`,
		'--bwfd-grid-gap': `${ gap }px`,
	};
	if ( columns > 0 ) {
		style[ '--bwfd-grid-cols' ] = String( columns );
	}
	if ( maxWidth ) {
		style.maxWidth = maxWidth;
	}
	return { className: classes, style };
}
