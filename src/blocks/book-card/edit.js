import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import {
	PanelBody,
	TextControl,
	TextareaControl,
	ToggleControl,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';
import metadata from './block.json';

/**
 * Book card editor: the card is rendered by PHP from the structured data
 * settings, so the editor shows the server output and only the copy and
 * links are edited here.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { accent, text, buttonLabel, buttonUrl, showPrice, linkLabel, linkUrl, sticky } = attributes;
	const blockProps = useBlockProps( { className: 'bwfd-book-card-editor' } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Book card', 'bwfd' ) }>
					<ToggleGroupControl
						label={ __( 'Accent', 'bwfd' ) }
						value={ accent }
						isBlock
						onChange={ ( value ) => setAttributes( { accent: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					>
						<ToggleGroupControlOption value="blue" label={ __( 'Blue', 'bwfd' ) } />
						<ToggleGroupControlOption value="apricot" label={ __( 'Apricot', 'bwfd' ) } />
					</ToggleGroupControl>
					<TextareaControl
						label={ __( 'Text', 'bwfd' ) }
						value={ text }
						onChange={ ( value ) => setAttributes( { text: value } ) }
						help={ __( 'Blank uses the release date and price from Settings → Structured data.', 'bwfd' ) }
						rows={ 3 }
						__nextHasNoMarginBottom
					/>
					<ToggleControl
						label={ __( 'Show the price on the button', 'bwfd' ) }
						checked={ showPrice }
						onChange={ ( value ) => setAttributes( { showPrice: value } ) }
						__nextHasNoMarginBottom
					/>
					<TextControl
						label={ __( 'Button label', 'bwfd' ) }
						value={ buttonLabel }
						placeholder={ __( 'Buy the book', 'bwfd' ) }
						onChange={ ( value ) => setAttributes( { buttonLabel: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<TextControl
						label={ __( 'Button link', 'bwfd' ) }
						value={ buttonUrl }
						placeholder={ __( 'Purchase page', 'bwfd' ) }
						onChange={ ( value ) => setAttributes( { buttonUrl: value } ) }
						type="url"
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<TextControl
						label={ __( 'Secondary link label', 'bwfd' ) }
						value={ linkLabel }
						placeholder={ __( 'About the book', 'bwfd' ) }
						onChange={ ( value ) => setAttributes( { linkLabel: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<TextControl
						label={ __( 'Secondary link', 'bwfd' ) }
						value={ linkUrl }
						placeholder={ __( 'About the book page', 'bwfd' ) }
						onChange={ ( value ) => setAttributes( { linkUrl: value } ) }
						type="url"
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<ToggleControl
						label={ __( 'Stick beside the article while scrolling', 'bwfd' ) }
						checked={ sticky }
						onChange={ ( value ) => setAttributes( { sticky: value } ) }
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<ServerSideRender block={ metadata.name } attributes={ attributes } />
			</div>
		</>
	);
}
