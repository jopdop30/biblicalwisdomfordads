import { __ } from '@wordpress/i18n';
import { useBlockProps, useInnerBlocksProps, InspectorControls, RichText } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';

const TEMPLATE = [
	[
		'core/table',
		{
			className: 'is-style-bwfd-key-info',
			hasFixedLayout: false,
			body: [
				{ cells: [ { content: 'Title', tag: 'td' }, { content: 'Biblical Wisdom for Dads', tag: 'td' } ] },
				{ cells: [ { content: 'Author', tag: 'td' }, { content: 'Stephen Parker', tag: 'td' } ] },
			],
		},
	],
];

export default function Edit( { attributes, setAttributes } ) {
	const { openLabel, closeLabel, defaultOpen } = attributes;
	const blockProps = useBlockProps( { className: 'bwfd-reveal is-editing' } );
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'bwfd-reveal__panel' },
		{ template: TEMPLATE }
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Reveal settings', 'bwfd' ) }>
					<TextControl
						label={ __( 'Label when closed', 'bwfd' ) }
						value={ openLabel }
						onChange={ ( value ) => setAttributes( { openLabel: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<TextControl
						label={ __( 'Label when open', 'bwfd' ) }
						value={ closeLabel }
						onChange={ ( value ) => setAttributes( { closeLabel: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<ToggleControl
						label={ __( 'Open by default', 'bwfd' ) }
						checked={ defaultOpen }
						onChange={ ( value ) => setAttributes( { defaultOpen: value } ) }
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<RichText
					tagName="span"
					className="bwfd-reveal__toggle"
					value={ openLabel }
					onChange={ ( value ) => setAttributes( { openLabel: value } ) }
					allowedFormats={ [] }
					withoutInteractiveFormatting
					placeholder={ __( 'Toggle label…', 'bwfd' ) }
				/>
				<div { ...innerBlocksProps } />
			</div>
		</>
	);
}
