import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	Button,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';

// Populated by inc/blocks.php from the PHP icon library so both sides stay in sync.
const ICONS = window.bwfdIcons || {};

function Svg( { slug, size } ) {
	const icon = ICONS[ slug ];
	if ( ! icon ) {
		return null;
	}
	const strokeProps = icon.filled
		? { fill: 'currentColor' }
		: { fill: 'none', stroke: 'currentColor', strokeWidth: 1.5, strokeLinecap: 'round', strokeLinejoin: 'round' };
	return (
		<svg
			width={ size }
			height={ size }
			viewBox="0 0 24 24"
			aria-hidden="true"
			focusable="false"
			{ ...strokeProps }
			dangerouslySetInnerHTML={ { __html: icon.paths } }
		/>
	);
}

export default function Edit( { attributes, setAttributes } ) {
	const { icon, variant, size } = attributes;
	const blockProps = useBlockProps( { className: `bwfd-icon--${ variant }` } );
	const drawSize = variant === 'circle' ? Math.round( size * 0.88 ) : size;

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Icon', 'bwfd' ) }>
					<div className="bwfd-icon-picker" role="listbox" aria-label={ __( 'Choose an icon', 'bwfd' ) }>
						{ Object.keys( ICONS ).map( ( slug ) => (
							<Button
								key={ slug }
								className={ slug === icon ? 'is-selected' : '' }
								label={ ICONS[ slug ].label }
								showTooltip
								isPressed={ slug === icon }
								onClick={ () => setAttributes( { icon: slug } ) }
							>
								<Svg slug={ slug } size={ 22 } />
							</Button>
						) ) }
					</div>
					<ToggleGroupControl
						label={ __( 'Style', 'bwfd' ) }
						value={ variant }
						isBlock
						onChange={ ( value ) => setAttributes( { variant: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					>
						<ToggleGroupControlOption value="plain" label={ __( 'Plain', 'bwfd' ) } />
						<ToggleGroupControlOption value="circle" label={ __( 'Apricot circle', 'bwfd' ) } />
					</ToggleGroupControl>
					<RangeControl
						label={ __( 'Size (px)', 'bwfd' ) }
						value={ size }
						min={ 16 }
						max={ 96 }
						onChange={ ( value ) => setAttributes( { size: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
				</PanelBody>
			</InspectorControls>
			<span { ...blockProps } style={ { ...blockProps.style, '--bwfd-icon-size': `${ size }px` } }>
				<Svg slug={ icon } size={ variant === 'circle' ? Math.round( drawSize * 0.55 ) : drawSize } />
			</span>
		</>
	);
}
