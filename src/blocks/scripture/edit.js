import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';

/**
 * Scripture block editor: the verse and reference are typed in place; the
 * translation and the passage link live in the sidebar. render.php draws
 * the front end from the same attributes.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { text, reference, translation, linkReference } = attributes;
	const blockProps = useBlockProps( { className: 'bwfd-scripture' } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Scripture settings', 'bwfd' ) }>
					<TextControl
						label={ __( 'Translation', 'bwfd' ) }
						value={ translation }
						onChange={ ( value ) => setAttributes( { translation: value } ) }
						help={ __( 'Shown after the reference, e.g. NIV, ESV, CSB. Also picks the version on the linked passage.', 'bwfd' ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<ToggleControl
						label={ __( 'Link the reference to the passage', 'bwfd' ) }
						checked={ linkReference }
						onChange={ ( value ) => setAttributes( { linkReference: value } ) }
						help={ __( 'Opens the passage on Bible Gateway in a new tab.', 'bwfd' ) }
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>
			<figure { ...blockProps }>
				<span className="bwfd-scripture__mark" aria-hidden="true">&ldquo;</span>
				<blockquote className="bwfd-scripture__text">
					<RichText
						tagName="p"
						value={ text }
						onChange={ ( value ) => setAttributes( { text: value } ) }
						placeholder={ __( 'Type or paste the verse…', 'bwfd' ) }
						allowedFormats={ [ 'core/italic', 'core/bold' ] }
					/>
				</blockquote>
				<figcaption className="bwfd-scripture__reference">
					<RichText
						tagName="span"
						value={ reference }
						onChange={ ( value ) => setAttributes( { reference: value } ) }
						placeholder={ __( 'Reference, e.g. Psalm 103:13', 'bwfd' ) }
						allowedFormats={ [] }
						withoutInteractiveFormatting
					/>
					{ translation && <span className="bwfd-scripture__translation">{ translation }</span> }
				</figcaption>
			</figure>
		</>
	);
}
