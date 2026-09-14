import { __, sprintf } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import { Button, Notice, SelectControl, Spinner, ExternalLink } from '@wordpress/components';

/**
 * Fetches a page from the front end and shows the JSON-LD it carries.
 * Reflects saved settings, not unsaved edits.
 */
export default function Preview( { pages, initialPageId, savedAt } ) {
	const [ pageId, setPageId ] = useState( initialPageId || 0 );
	const [ state, setState ] = useState( { loading: false, json: '', error: '' } );

	useEffect( () => {
		if ( ! pageId && pages.length ) {
			setPageId( initialPageId || pages[ 0 ].id );
		}
	}, [ pages, initialPageId, pageId ] );

	const page = pages.find( ( p ) => p.id === pageId );

	const load = async () => {
		if ( ! page ) {
			return;
		}
		setState( { loading: true, json: '', error: '' } );
		try {
			const response = await fetch( page.link, { credentials: 'same-origin', cache: 'no-store' } );
			const html = await response.text();
			const match = html.match( /<script type="application\/ld\+json">([\s\S]*?)<\/script>/ );
			if ( ! match ) {
				throw new Error( __( 'No JSON-LD found on that page.', 'bwfd' ) );
			}
			setState( { loading: false, json: JSON.stringify( JSON.parse( match[ 1 ] ), null, 2 ), error: '' } );
		} catch ( error ) {
			setState( { loading: false, json: '', error: error.message } );
		}
	};

	// Reload after a save so the preview shows what was just written.
	useEffect( () => {
		if ( savedAt && state.json ) {
			load();
		}
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, [ savedAt ] );

	const testUrl = page ? `https://search.google.com/test/rich-results?url=${ encodeURIComponent( page.link ) }` : '';

	return (
		<div className="bwfd-schema-preview">
			<div className="bwfd-schema-preview__controls">
				<SelectControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ __( 'Page', 'bwfd' ) }
					value={ pageId }
					options={ pages.map( ( p ) => ( { value: p.id, label: p.title } ) ) }
					onChange={ ( next ) => setPageId( parseInt( next, 10 ) || 0 ) }
				/>
				<Button variant="secondary" onClick={ load } disabled={ ! page || state.loading } __next40pxDefaultSize>
					{ __( 'Show JSON-LD', 'bwfd' ) }
				</Button>
				{ testUrl && (
					<ExternalLink href={ testUrl }>{ __( 'Open in Rich Results Test', 'bwfd' ) }</ExternalLink>
				) }
			</div>
			<p className="description">
				{ __( 'Shows the structured data the page sends right now, from saved settings. The Rich Results Test needs the site to be reachable from the internet.', 'bwfd' ) }
			</p>
			{ state.loading && <Spinner /> }
			{ state.error && (
				<Notice status="error" isDismissible={ false }>
					{ sprintf( /* translators: %s: error message */ __( 'Could not load the page: %s', 'bwfd' ), state.error ) }
				</Notice>
			) }
			{ state.json && <pre>{ state.json }</pre> }
		</div>
	);
}
