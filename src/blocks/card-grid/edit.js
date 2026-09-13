import { __ } from '@wordpress/i18n';
import { useBlockProps, useInnerBlocksProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	ToggleControl,
	__experimentalUnitControl as UnitControl,
} from '@wordpress/components';
import { gridProps } from './props';

const TEMPLATE = [
	[ 'bwfd/card' ],
	[ 'bwfd/card' ],
	[ 'bwfd/card' ],
];

export default function Edit( { attributes, setAttributes } ) {
	const { columns, minWidth, gap, centered, maxWidth } = attributes;
	const blockProps = useBlockProps( gridProps( attributes ) );
	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template: TEMPLATE,
		orientation: 'horizontal',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Grid settings', 'bwfd' ) }>
					<RangeControl
						label={ __( 'Columns (0 = automatic)', 'bwfd' ) }
						value={ columns }
						min={ 0 }
						max={ 4 }
						onChange={ ( value ) => setAttributes( { columns: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<RangeControl
						label={ __( 'Minimum card width (px)', 'bwfd' ) }
						value={ minWidth }
						min={ 180 }
						max={ 480 }
						step={ 4 }
						onChange={ ( value ) => setAttributes( { minWidth: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<RangeControl
						label={ __( 'Gap (px)', 'bwfd' ) }
						value={ gap }
						min={ 0 }
						max={ 60 }
						onChange={ ( value ) => setAttributes( { gap: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<ToggleControl
						label={ __( 'Centre incomplete rows', 'bwfd' ) }
						help={ __( 'Cards wrap and centre, at most three per row.', 'bwfd' ) }
						checked={ centered }
						onChange={ ( value ) => setAttributes( { centered: value } ) }
						__nextHasNoMarginBottom
					/>
					<UnitControl
						label={ __( 'Maximum width', 'bwfd' ) }
						value={ maxWidth }
						onChange={ ( value ) => setAttributes( { maxWidth: value || '' } ) }
						units={ [ { value: 'px', label: 'px' }, { value: 'em', label: 'em' }, { value: '%', label: '%' } ] }
						__next40pxDefaultSize
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...innerBlocksProps } />
		</>
	);
}
