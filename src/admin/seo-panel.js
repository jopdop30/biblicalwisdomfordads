/**
 * "Search appearance" panel in the post editor.
 *
 * Edits the `bwfd_seo_title` meta (the whole <title> for search results)
 * and shows how long the excerpt is, since inc/seo.php uses the Excerpt as
 * the meta description.
 */
import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { TextControl } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';
import { decodeEntities } from '@wordpress/html-entities';

const META_KEY = 'bwfd_seo_title';
const TITLE_MAX = 60;
const DESCRIPTION_MAX = 158;

function lengthNote( length, max ) {
	if ( length === 0 ) {
		return __( 'empty', 'bwfd' );
	}
	return length > max
		? sprintf( __( '%1$d characters, over the %2$d shown in results', 'bwfd' ), length, max )
		: sprintf( __( '%1$d of about %2$d characters', 'bwfd' ), length, max );
}

function Panel() {
	const { postType, postId } = useSelect(
		( select ) => ( {
			postType: select( 'core/editor' ).getCurrentPostType(),
			postId: select( 'core/editor' ).getCurrentPostId(),
		} ),
		[]
	);
	const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta', postId );
	const [ title ] = useEntityProp( 'postType', postType, 'title', postId );
	const [ excerpt ] = useEntityProp( 'postType', postType, 'excerpt', postId );

	if ( ! meta || ! ( META_KEY in meta ) ) {
		return null;
	}

	const config = window.bwfdSeoPanel || {};
	const separator = config.separator || '-';
	const fallback = `${ decodeEntities( title || '' ) } ${ separator } ${ config.siteName || '' }`.trim();
	const searchTitle = meta[ META_KEY ] || '';
	const effective = searchTitle || fallback;
	const excerptText = ( excerpt || '' ).replace( /<[^>]+>/g, '' ).trim();

	return (
		<PluginDocumentSettingPanel name="bwfd-search-appearance" title={ __( 'Search appearance', 'bwfd' ) }>
			<TextControl
				label={ __( 'Search title', 'bwfd' ) }
				value={ searchTitle }
				placeholder={ fallback }
				onChange={ ( value ) => setMeta( { ...meta, [ META_KEY ]: value } ) }
				help={ sprintf(
					/* translators: 1: length note, 2: the title that will be used. */
					__( '%1$s. Used as the whole title tag; blank uses “%2$s”.', 'bwfd' ),
					lengthNote( effective.length, TITLE_MAX ),
					fallback
				) }
				__nextHasNoMarginBottom
				__next40pxDefaultSize
			/>
			<p className="components-base-control__help" style={ { marginTop: '12px' } }>
				{ sprintf(
					/* translators: %s: length note. */
					__( 'Search description: the Excerpt panel (%s). Aim for one or two full sentences.', 'bwfd' ),
					lengthNote( excerptText.length, DESCRIPTION_MAX )
				) }
			</p>
		</PluginDocumentSettingPanel>
	);
}

registerPlugin( 'bwfd-search-appearance', { render: Panel } );
