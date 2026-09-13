import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	BlockControls,
	InspectorControls,
	AlignmentControl,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	ToggleControl,
	ToolbarGroup,
	ToolbarDropdownMenu,
} from '@wordpress/components';
import { headingLevel1, headingLevel2, headingLevel3, headingLevel4 } from '@wordpress/icons';

const LEVEL_ICONS = { 1: headingLevel1, 2: headingLevel2, 3: headingLevel3, 4: headingLevel4 };

const SIZE_OPTIONS = [
	{ label: __( 'Hero', 'bwfd' ), value: 'hero' },
	{ label: __( 'Page heading', 'bwfd' ), value: '4xl' },
	{ label: __( 'Feature heading', 'bwfd' ), value: '3xl' },
	{ label: __( 'Section heading', 'bwfd' ), value: '2xl' },
	{ label: __( 'Card title', 'bwfd' ), value: 'xl' },
];

export default function Edit( { attributes, setAttributes } ) {
	const { content, level, size, showRule, textAlign } = attributes;
	const TagName = `h${ level }`;
	const blockProps = useBlockProps( {
		className: `bwfd-section-heading has-text-align-${ textAlign }`,
	} );

	return (
		<>
			<BlockControls group="block">
				<ToolbarGroup>
					<ToolbarDropdownMenu
						icon={ LEVEL_ICONS[ level ] }
						label={ __( 'Heading level', 'bwfd' ) }
						controls={ [ 1, 2, 3, 4 ].map( ( l ) => ( {
							title: `H${ l }`,
							icon: LEVEL_ICONS[ l ],
							isActive: l === level,
							onClick: () => setAttributes( { level: l } ),
						} ) ) }
					/>
				</ToolbarGroup>
				<AlignmentControl
					value={ textAlign }
					onChange={ ( value ) => setAttributes( { textAlign: value || 'left' } ) }
				/>
			</BlockControls>
			<InspectorControls>
				<PanelBody title={ __( 'Heading settings', 'bwfd' ) }>
					<SelectControl
						label={ __( 'Size', 'bwfd' ) }
						value={ size }
						options={ SIZE_OPTIONS }
						onChange={ ( value ) => setAttributes( { size: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<ToggleControl
						label={ __( 'Show apricot rule', 'bwfd' ) }
						checked={ showRule }
						onChange={ ( value ) => setAttributes( { showRule: value } ) }
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<RichText
					tagName={ TagName }
					className={ `bwfd-section-heading__title has-${ size }-font-size` }
					value={ content }
					onChange={ ( value ) => setAttributes( { content: value } ) }
					placeholder={ __( 'Section heading…', 'bwfd' ) }
					allowedFormats={ [ 'core/italic', 'core/text-color' ] }
				/>
				{ showRule && <span className="bwfd-section-heading__rule" aria-hidden="true" /> }
			</div>
		</>
	);
}
