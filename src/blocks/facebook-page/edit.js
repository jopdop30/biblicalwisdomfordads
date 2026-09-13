import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	RangeControl,
	ToggleControl,
	SelectControl,
	Disabled,
	Notice,
} from '@wordpress/components';
import { pluginSrc } from './embed';

const TABS = [
	{ label: __( 'Timeline', 'bwfd' ), value: 'timeline' },
	{ label: __( 'Timeline and events', 'bwfd' ), value: 'timeline,events' },
	{ label: __( 'Timeline, events and messages', 'bwfd' ), value: 'timeline,events,messages' },
	{ label: __( 'Header only', 'bwfd' ), value: '' },
];

export default function Edit( { attributes, setAttributes } ) {
	const { url, height, tabs, smallHeader, hideCover, showFacepile, title } = attributes;
	const blockProps = useBlockProps( { className: 'bwfd-facebook-page' } );
	const isFacebookUrl = /^https:\/\/(www\.)?facebook\.com\/.+/i.test( url );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Facebook page', 'bwfd' ) }>
					<TextControl
						label={ __( 'Page URL', 'bwfd' ) }
						value={ url }
						onChange={ ( value ) => setAttributes( { url: value } ) }
						type="url"
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<TextControl
						label={ __( 'Accessible title', 'bwfd' ) }
						value={ title }
						onChange={ ( value ) => setAttributes( { title: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<SelectControl
						label={ __( 'Tabs', 'bwfd' ) }
						value={ tabs }
						options={ TABS }
						onChange={ ( value ) => setAttributes( { tabs: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<RangeControl
						label={ __( 'Height (px)', 'bwfd' ) }
						value={ height }
						min={ 130 }
						max={ 1200 }
						step={ 10 }
						onChange={ ( value ) => setAttributes( { height: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<ToggleControl
						label={ __( 'Small header', 'bwfd' ) }
						checked={ smallHeader }
						onChange={ ( value ) => setAttributes( { smallHeader: value } ) }
						__nextHasNoMarginBottom
					/>
					<ToggleControl
						label={ __( 'Hide cover photo', 'bwfd' ) }
						checked={ hideCover }
						onChange={ ( value ) => setAttributes( { hideCover: value } ) }
						__nextHasNoMarginBottom
					/>
					<ToggleControl
						label={ __( 'Show friends’ faces', 'bwfd' ) }
						checked={ showFacepile }
						onChange={ ( value ) => setAttributes( { showFacepile: value } ) }
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				{ isFacebookUrl ? (
					<Disabled>
						<iframe
							src={ pluginSrc( attributes ) }
							title={ title }
							height={ height }
							style={ { border: 'none', overflow: 'hidden', width: '100%' } }
							scrolling="no"
							allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"
						/>
					</Disabled>
				) : (
					<Notice status="warning" isDismissible={ false }>
						{ __( 'Enter a public Facebook page URL in the block settings.', 'bwfd' ) }
					</Notice>
				) }
			</div>
		</>
	);
}
