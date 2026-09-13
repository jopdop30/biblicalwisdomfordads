import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';
import {
	PanelBody,
	Button,
	ToggleControl,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';
import { heroProps } from './props';

const TEMPLATE = [
	[ 'bwfd/framed-image', { shadow: 'cover-dark', maxWidth: '360px' } ],
	[
		'core/group',
		{ layout: { type: 'flex', orientation: 'vertical' }, style: { spacing: { blockGap: '22px' } } },
		[
			[ 'core/paragraph', { className: 'bwfd-kicker', placeholder: __( 'Kicker line…', 'bwfd' ) } ],
			[ 'core/heading', { level: 1, fontSize: 'hero', placeholder: __( 'Headline…', 'bwfd' ) } ],
			[ 'core/paragraph', { fontSize: 'lg', placeholder: __( 'Supporting text…', 'bwfd' ) } ],
			[
				'core/buttons',
				{ layout: { type: 'flex', verticalAlignment: 'center' } },
				[
					[ 'core/button', { className: 'is-style-bwfd-apricot', text: __( 'Buy the book', 'bwfd' ) } ],
					[ 'core/button', { className: 'is-style-bwfd-text-link', text: __( 'About the book', 'bwfd' ) } ],
				],
			],
		],
	],
];

export default function Edit( { attributes, setAttributes } ) {
	const { wash, reverse, backgroundUrl, backgroundId } = attributes;
	const blockProps = useBlockProps( heroProps( attributes ) );
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'bwfd-hero__inner' },
		{ template: TEMPLATE, templateLock: false }
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Hero wash', 'bwfd' ) }>
					<ToggleGroupControl
						label={ __( 'Colour wash', 'bwfd' ) }
						value={ wash }
						isBlock
						onChange={ ( value ) => setAttributes( { wash: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					>
						<ToggleGroupControlOption value="solid" label={ __( 'Solid navy', 'bwfd' ) } />
						<ToggleGroupControlOption value="radial" label={ __( 'Radial', 'bwfd' ) } />
						<ToggleGroupControlOption value="linear" label={ __( 'Linear', 'bwfd' ) } />
					</ToggleGroupControl>
					<ToggleControl
						label={ __( 'Reverse (pale behind the text)', 'bwfd' ) }
						help={ __( 'Switches the headline to navy ink. Not used with solid navy.', 'bwfd' ) }
						checked={ reverse }
						disabled={ wash === 'solid' }
						onChange={ ( value ) => setAttributes( { reverse: value } ) }
						__nextHasNoMarginBottom
					/>
				</PanelBody>
				<PanelBody title={ __( 'Background artwork', 'bwfd' ) }>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={ ( media ) =>
								setAttributes( { backgroundId: media.id, backgroundUrl: media.url } )
							}
							allowedTypes={ [ 'image' ] }
							value={ backgroundId }
							render={ ( { open } ) => (
								<div className="bwfd-hero__media-control">
									{ backgroundUrl && <img src={ backgroundUrl } alt="" /> }
									<Button variant="secondary" onClick={ open }>
										{ backgroundUrl ? __( 'Replace artwork', 'bwfd' ) : __( 'Choose artwork', 'bwfd' ) }
									</Button>
									{ backgroundUrl && (
										<Button
											variant="tertiary"
											isDestructive
											onClick={ () => setAttributes( { backgroundId: undefined, backgroundUrl: '' } ) }
										>
											{ __( 'Remove', 'bwfd' ) }
										</Button>
									) }
								</div>
							) }
						/>
					</MediaUploadCheck>
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="bwfd-hero__overlay" aria-hidden="true" />
				<div { ...innerBlocksProps } />
			</section>
		</>
	);
}
