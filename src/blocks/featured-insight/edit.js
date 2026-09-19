import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { PanelBody, TextControl, __experimentalNumberControl as NumberControl } from '@wordpress/components';
import metadata from './block.json';

/**
 * Featured insight editor: rendered by PHP from the latest Insight (or a
 * chosen post ID); only the kicker and the override are edited here.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { postId, kicker, moreLabel } = attributes;
	const blockProps = useBlockProps( { className: 'bwfd-featured-insight-editor' } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Featured insight', 'bwfd' ) }>
					<TextControl
						label={ __( 'Kicker', 'bwfd' ) }
						value={ kicker }
						onChange={ ( value ) => setAttributes( { kicker: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<TextControl
						label={ __( 'Link label', 'bwfd' ) }
						value={ moreLabel }
						onChange={ ( value ) => setAttributes( { moreLabel: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<NumberControl
						label={ __( 'Insight ID (0 = most recent)', 'bwfd' ) }
						value={ postId }
						min={ 0 }
						onChange={ ( value ) => setAttributes( { postId: parseInt( value, 10 ) || 0 } ) }
						__next40pxDefaultSize
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<ServerSideRender
					block={ metadata.name }
					attributes={ attributes }
					EmptyResponsePlaceholder={ () => (
						<p className="bwfd-featured-insight-editor__empty">
							{ __( 'Featured insight: shows the most recent published Insight here. Nothing is published yet, or this is a later archive page.', 'bwfd' ) }
						</p>
					) }
				/>
			</div>
		</>
	);
}
