/**
 * Immutable deep set: setPath( obj, [ 'book', 'name' ], 'x' ).
 */
export function setPath( object, path, value ) {
	if ( path.length === 0 ) {
		return value;
	}
	const [ head, ...rest ] = path;
	const current = object ?? ( typeof head === 'number' ? [] : {} );
	const next = Array.isArray( current ) ? [ ...current ] : { ...current };
	next[ head ] = setPath( current[ head ], rest, value );
	return next;
}

export function getPath( object, path, fallback ) {
	let node = object;
	for ( const key of path ) {
		if ( node === null || node === undefined ) {
			return fallback;
		}
		node = node[ key ];
	}
	return node === undefined ? fallback : node;
}
