export function cardClassName( { accent, surface, padding, lift } ) {
	return [
		'bwfd-card',
		`bwfd-card--accent-${ accent }`,
		`bwfd-card--${ surface }`,
		`bwfd-card--pad-${ padding }`,
		lift ? 'bwfd-card--lift' : '',
	]
		.filter( Boolean )
		.join( ' ' );
}

/**
 * Inline style for the card wrapper: optional min-height and the block gap
 * chosen in the editor (the card manages its own flex gap, so the layout
 * support does not output it).
 */
export function cardStyle( { minHeight, style } ) {
	const out = {};
	if ( minHeight ) {
		out.minHeight = minHeight;
	}
	const gap = style?.spacing?.blockGap;
	if ( typeof gap === 'string' && gap ) {
		out.gap = gap.startsWith( 'var:preset|spacing|' )
			? `var(--wp--preset--spacing--${ gap.split( '|' ).pop() })`
			: gap;
	}
	return Object.keys( out ).length ? out : undefined;
}
