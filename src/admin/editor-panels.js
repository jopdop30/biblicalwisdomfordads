/**
 * Small per-type fields in the editor's status panel, the spot where core
 * shows "Stick to the top of the blog" for posts:
 *
 * - Insights: "Feature on the Insights page" (meta `bwfd_featured`). The
 *   Featured insight block shows the flagged Insight, else the most recent.
 * - News items: "Dateline" (meta `bwfd_dateline`). The city that opens the
 *   first paragraph with the date; blank uses the publisher city from
 *   Settings → Structured data, a dash leaves the dateline out.
 * - Chapters: "Chapter number", the post's Order (menu_order), which drives
 *   the slug, the "Chapter 22" labels and the order of the chapter list.
 *
 * The metas are registered in inc/content-types.php; chapters in inc/chapters.php.
 */
import { registerPlugin } from '@wordpress/plugins';
import { PluginPostStatusInfo } from '@wordpress/editor';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { ToggleControl, TextControl, __experimentalNumberControl as NumberControl } from '@wordpress/components';
import { useDispatch } from '@wordpress/data';
import { __, sprintf } from '@wordpress/i18n';

const FEATURED_KEY = 'bwfd_featured';
const DATELINE_KEY = 'bwfd_dateline';
const config = window.bwfdEditorPanels || {};

function usePostMeta() {
	const { postType, postId } = useSelect(
		( select ) => ( {
			postType: select( 'core/editor' ).getCurrentPostType(),
			postId: select( 'core/editor' ).getCurrentPostId(),
		} ),
		[]
	);
	const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta', postId );
	return { postType, meta, setMeta };
}

function FeaturedToggle() {
	const { postType, meta, setMeta } = usePostMeta();
	if ( postType !== 'bwfd_insight' || ! meta || ! ( FEATURED_KEY in meta ) ) {
		return null;
	}
	return (
		<PluginPostStatusInfo className="bwfd-featured-toggle">
			<ToggleControl
				label={ __( 'Feature on the Insights page', 'bwfd' ) }
				checked={ !! meta[ FEATURED_KEY ] }
				onChange={ ( value ) => setMeta( { ...meta, [ FEATURED_KEY ]: value } ) }
				help={ __( 'Shown in the panel at the top of Insights. One insight is featured at a time; with none chosen the most recent is shown.', 'bwfd' ) }
				__nextHasNoMarginBottom
			/>
		</PluginPostStatusInfo>
	);
}

function DatelineField() {
	const { postType, meta, setMeta } = usePostMeta();
	if ( postType !== 'bwfd_news' || ! meta || ! ( DATELINE_KEY in meta ) ) {
		return null;
	}
	const city = config.city || '';
	return (
		<PluginPostStatusInfo className="bwfd-dateline-field">
			<TextControl
				label={ __( 'Dateline', 'bwfd' ) }
				value={ meta[ DATELINE_KEY ] || '' }
				placeholder={ city }
				onChange={ ( value ) => setMeta( { ...meta, [ DATELINE_KEY ]: value } ) }
				help={ sprintf(
					/* translators: %s: default city */
					__( 'Opens the first paragraph with the city and date, press-release style. Blank uses “%s”; a dash (-) leaves it out.', 'bwfd' ),
					city
				) }
				__nextHasNoMarginBottom
				__next40pxDefaultSize
			/>
		</PluginPostStatusInfo>
	);
}

function ChapterNumberField() {
	const { postType, menuOrder } = useSelect(
		( select ) => ( {
			postType: select( 'core/editor' ).getCurrentPostType(),
			menuOrder: select( 'core/editor' ).getEditedPostAttribute( 'menu_order' ),
		} ),
		[]
	);
	const { editPost } = useDispatch( 'core/editor' );
	if ( postType !== 'bwfd_chapter' ) {
		return null;
	}
	const total = config.chapters || 0;
	return (
		<PluginPostStatusInfo className="bwfd-chapter-number-field">
			<NumberControl
				label={ __( 'Chapter number', 'bwfd' ) }
				value={ menuOrder || '' }
				min={ 1 }
				max={ total || undefined }
				onChange={ ( value ) => editPost( { menu_order: parseInt( value, 10 ) || 0 } ) }
				help={ total
					? sprintf( /* translators: %d: total chapters */ __( '1 to %d. Sets the “Chapter 22” label, the web address and the order in the chapter list.', 'bwfd' ), total )
					: __( 'Sets the “Chapter 22” label, the web address and the order in the chapter list.', 'bwfd' ) }
				__next40pxDefaultSize
			/>
		</PluginPostStatusInfo>
	);
}

registerPlugin( 'bwfd-editor-panels', {
	render: () => (
		<>
			<FeaturedToggle />
			<DatelineField />
			<ChapterNumberField />
		</>
	),
} );
