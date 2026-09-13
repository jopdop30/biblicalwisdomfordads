import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	BlockControls,
	InspectorControls,
	MediaPlaceholder,
	MediaReplaceFlow,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextareaControl,
	ToggleControl,
	__experimentalUnitControl as UnitControl,
} from '@wordpress/components';
import { frameClassName, imageStyle } from './props';

const SHADOWS = [
	{ label: __( 'Cover (soft navy)', 'bwfd' ), value: 'cover' },
	{ label: __( 'Cover on dark', 'bwfd' ), value: 'cover-dark' },
	{ label: __( 'Book', 'bwfd' ), value: 'book' },
	{ label: __( 'None', 'bwfd' ), value: 'none' },
];

const RATIOS = [
	{ label: __( 'Natural', 'bwfd' ), value: '' },
	{ label: '4:5 ' + __( 'portrait', 'bwfd' ), value: '4 / 5' },
	{ label: '3:4 ' + __( 'portrait', 'bwfd' ), value: '3 / 4' },
	{ label: '2:3 ' + __( 'book', 'bwfd' ), value: '2 / 3' },
	{ label: '1:1 ' + __( 'square', 'bwfd' ), value: '1 / 1' },
];

export default function Edit( { attributes, setAttributes } ) {
	const { id, url, alt, panel, shadow, maxWidth, aspectRatio, width, height } = attributes;
	const blockProps = useBlockProps( {
		className: frameClassName( attributes ),
		style: maxWidth ? { maxWidth } : undefined,
	} );

	const onSelect = ( media ) => {
		if ( ! media?.url ) {
			return;
		}
		const large = media.sizes?.large || media.media_details?.sizes?.large;
		const largeUrl = large?.url || large?.source_url;
		setAttributes( {
			id: media.id,
			url: largeUrl || media.url,
			alt: media.alt || alt || '',
			width: ( largeUrl ? large.width : media.width ) || undefined,
			height: ( largeUrl ? large.height : media.height ) || undefined,
		} );
	};

	return (
		<>
			{ url && (
				<BlockControls group="other">
					<MediaReplaceFlow
						mediaId={ id }
						mediaURL={ url }
						allowedTypes={ [ 'image' ] }
						accept="image/*"
						onSelect={ onSelect }
						name={ __( 'Replace', 'bwfd' ) }
					/>
				</BlockControls>
			) }
			<InspectorControls>
				<PanelBody title={ __( 'Frame', 'bwfd' ) }>
					<ToggleControl
						label={ __( 'Apricot panel behind', 'bwfd' ) }
						checked={ panel }
						onChange={ ( value ) => setAttributes( { panel: value } ) }
						__nextHasNoMarginBottom
					/>
					<SelectControl
						label={ __( 'Shadow', 'bwfd' ) }
						value={ shadow }
						options={ SHADOWS }
						onChange={ ( value ) => setAttributes( { shadow: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<SelectControl
						label={ __( 'Crop to ratio', 'bwfd' ) }
						value={ aspectRatio }
						options={ RATIOS }
						onChange={ ( value ) => setAttributes( { aspectRatio: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<UnitControl
						label={ __( 'Maximum width', 'bwfd' ) }
						value={ maxWidth }
						onChange={ ( value ) => setAttributes( { maxWidth: value || '' } ) }
						units={ [ { value: 'px', label: 'px' }, { value: '%', label: '%' } ] }
						__next40pxDefaultSize
					/>
					<TextareaControl
						label={ __( 'Alternative text', 'bwfd' ) }
						value={ alt }
						onChange={ ( value ) => setAttributes( { alt: value } ) }
						help={ __( 'Describe the image for screen readers.', 'bwfd' ) }
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>
			<figure { ...blockProps }>
				{ panel && url && <span className="bwfd-framed-image__panel" aria-hidden="true" /> }
				{ url ? (
					<img src={ url } alt={ alt } width={ width } height={ height } style={ imageStyle( aspectRatio ) } />
				) : (
					<MediaPlaceholder
						icon="format-image"
						labels={ { title: __( 'Framed image', 'bwfd' ) } }
						onSelect={ onSelect }
						accept="image/*"
						allowedTypes={ [ 'image' ] }
					/>
				) }
			</figure>
		</>
	);
}
