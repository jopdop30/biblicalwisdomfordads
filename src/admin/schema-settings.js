/**
 * Settings → Structured data.
 *
 * Edits the `bwfd_schema` option through the REST settings endpoint using
 * @wordpress/core-data, so it saves the same way the site editor saves the
 * site title. Rendered into #bwfd-schema-settings by inc/schema-settings.php.
 */
import { createRoot } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import App from './schema-settings/app';
import './schema-settings/editor.scss';

domReady( () => {
	const root = document.getElementById( 'bwfd-schema-settings' );
	if ( root ) {
		createRoot( root ).render( <App /> );
	}
} );
