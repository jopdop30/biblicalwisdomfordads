import { __ } from '@wordpress/i18n';
import { useBlockProps, useInnerBlocksProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	ToggleControl,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
	__experimentalUnitControl as UnitControl,
} from '@wordpress/components';

const TEMPLATE = [ [ 'bwfd/endorsement' ], [ 'bwfd/endorsement' ], [ 'bwfd/endorsement' ] ];

export default function Edit( { attributes, setAttributes } ) {
	const { interval, autoplay, navigation, minHeight } = attributes;
	const blockProps = useBlockProps( {
		className: `bwfd-endorsements bwfd-endorsements--nav-${ navigation } is-editing`,
	} );
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'bwfd-endorsements__slides' },
		{ template: TEMPLATE, allowedBlocks: [ 'bwfd/endorsement' ] }
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Carousel settings', 'bwfd' ) }>
					<ToggleControl
						label={ __( 'Rotate automatically', 'bwfd' ) }
						help={ __( 'Pauses on hover and honours reduced-motion preferences.', 'bwfd' ) }
						checked={ autoplay }
						onChange={ ( value ) => setAttributes( { autoplay: value } ) }
						__nextHasNoMarginBottom
					/>
					<RangeControl
						label={ __( 'Seconds per quote', 'bwfd' ) }
						value={ interval / 1000 }
						min={ 3 }
						max={ 20 }
						disabled={ ! autoplay }
						onChange={ ( value ) => setAttributes( { interval: value * 1000 } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<ToggleGroupControl
						label={ __( 'Position indicator', 'bwfd' ) }
						value={ navigation }
						isBlock
						onChange={ ( value ) => setAttributes( { navigation: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					>
						<ToggleGroupControlOption value="dots" label={ __( 'Dots', 'bwfd' ) } />
						<ToggleGroupControlOption value="count" label={ __( '“1 of 9”', 'bwfd' ) } />
					</ToggleGroupControl>
					<UnitControl
						label={ __( 'Minimum height', 'bwfd' ) }
						value={ minHeight }
						onChange={ ( value ) => setAttributes( { minHeight: value || '' } ) }
						units={ [ { value: 'px', label: 'px' } ] }
						__next40pxDefaultSize
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<div className="bwfd-endorsements__frame" aria-hidden="true" />
				<div className="bwfd-endorsements__inner">
					<p className="bwfd-endorsements__editor-note">
						{ __( 'Endorsements – every quote is shown here for editing; visitors see one at a time.', 'bwfd' ) }
					</p>
					<div { ...innerBlocksProps } />
				</div>
			</div>
		</>
	);
}
