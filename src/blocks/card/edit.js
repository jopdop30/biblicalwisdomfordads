import { __ } from '@wordpress/i18n';
import { useBlockProps, useInnerBlocksProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
	__experimentalUnitControl as UnitControl,
} from '@wordpress/components';
import { cardClassName, cardStyle } from './classes';

const ALLOWED_BLOCKS = [
	'core/heading',
	'core/paragraph',
	'core/buttons',
	'core/list',
	'core/image',
	'core/html',
	'core/shortcode',
	'core/embed',
	'bwfd/icon',
	'bwfd/badge',
	'bwfd/framed-image',
	'bwfd/facebook-page',
];

const TEMPLATE = [
	[ 'core/heading', { level: 3, placeholder: __( 'Card title', 'bwfd' ), fontSize: 'xl' } ],
	[ 'core/paragraph', { placeholder: __( 'Card text…', 'bwfd' ), fontSize: 'base' } ],
	[
		'core/buttons',
		{},
		[ [ 'core/button', { className: 'is-style-bwfd-text-link', text: __( 'Read more', 'bwfd' ) } ] ],
	],
];

export default function Edit( { attributes, setAttributes } ) {
	const { accent, surface, padding, minHeight, lift } = attributes;
	const blockProps = useBlockProps( {
		className: cardClassName( attributes ),
		style: cardStyle( attributes ),
	} );
	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: ALLOWED_BLOCKS,
		template: TEMPLATE,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Card style', 'bwfd' ) }>
					<ToggleGroupControl
						label={ __( 'Surface', 'bwfd' ) }
						value={ surface }
						isBlock
						onChange={ ( value ) => setAttributes( { surface: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					>
						<ToggleGroupControlOption value="marble" label={ __( 'Marble', 'bwfd' ) } />
						<ToggleGroupControlOption value="white" label={ __( 'White', 'bwfd' ) } />
						<ToggleGroupControlOption value="apricot" label={ __( 'Apricot', 'bwfd' ) } />
						<ToggleGroupControlOption value="navy" label={ __( 'Navy', 'bwfd' ) } />
					</ToggleGroupControl>
					<ToggleGroupControl
						label={ __( 'Accent border', 'bwfd' ) }
						value={ accent }
						isBlock
						onChange={ ( value ) => setAttributes( { accent: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					>
						<ToggleGroupControlOption value="blue" label={ __( 'Blue', 'bwfd' ) } />
						<ToggleGroupControlOption value="apricot" label={ __( 'Apricot', 'bwfd' ) } />
						<ToggleGroupControlOption value="none" label={ __( 'None', 'bwfd' ) } />
					</ToggleGroupControl>
					<ToggleGroupControl
						label={ __( 'Padding', 'bwfd' ) }
						value={ padding }
						isBlock
						onChange={ ( value ) => setAttributes( { padding: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					>
						<ToggleGroupControlOption value="regular" label={ __( 'Regular', 'bwfd' ) } />
						<ToggleGroupControlOption value="compact" label={ __( 'Compact', 'bwfd' ) } />
					</ToggleGroupControl>
					<UnitControl
						label={ __( 'Minimum height', 'bwfd' ) }
						value={ minHeight }
						onChange={ ( value ) => setAttributes( { minHeight: value || '' } ) }
						units={ [ { value: 'px', label: 'px' }, { value: 'em', label: 'em' } ] }
						__next40pxDefaultSize
					/>
					<ToggleControl
						label={ __( 'Lift with a shadow', 'bwfd' ) }
						help={ __( 'Use when the card sits on a matching surface.', 'bwfd' ) }
						checked={ lift }
						onChange={ ( value ) => setAttributes( { lift: value } ) }
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...innerBlocksProps } />
		</>
	);
}
