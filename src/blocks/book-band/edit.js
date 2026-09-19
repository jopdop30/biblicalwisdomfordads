import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import {
	PanelBody,
	TextControl,
	TextareaControl,
	ToggleControl,
	RangeControl,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';
import metadata from './block.json';

/**
 * Book band editor: rendered by PHP so the price, cover and chapter count
 * are live; the words and links are edited here. Blank fields fall back
 * to sensible copy for the chosen action.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { primary, heading, text, primaryLabel, primaryUrl, secondaryLabel, secondaryUrl, showCover, coverWidth } = attributes;
	const blockProps = useBlockProps( { className: 'bwfd-book-band-editor' } );
	const isBuy = primary === 'buy';

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Book band', 'bwfd' ) }>
					<ToggleGroupControl
						label={ __( 'Main button', 'bwfd' ) }
						value={ primary }
						isBlock
						onChange={ ( value ) => setAttributes( { primary: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					>
						<ToggleGroupControlOption value="buy" label={ __( 'Buy the book', 'bwfd' ) } />
						<ToggleGroupControlOption value="book" label={ __( 'About the book', 'bwfd' ) } />
					</ToggleGroupControl>
					<TextControl
						label={ __( 'Heading', 'bwfd' ) }
						value={ heading }
						placeholder={ isBuy ? __( 'Want more like this?', 'bwfd' ) : __( 'The book title', 'bwfd' ) }
						onChange={ ( value ) => setAttributes( { heading: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<TextareaControl
						label={ __( 'Line', 'bwfd' ) }
						value={ text }
						onChange={ ( value ) => setAttributes( { text: value } ) }
						help={ __( 'You can write {book}, {chapters}, {price} or {author}; they are filled in from Settings → Structured data.', 'bwfd' ) }
						rows={ 3 }
						__nextHasNoMarginBottom
					/>
					<TextControl
						label={ __( 'Main button label', 'bwfd' ) }
						value={ primaryLabel }
						placeholder={ isBuy ? __( 'Buy the book · {price}', 'bwfd' ) : __( 'Learn more about the book', 'bwfd' ) }
						onChange={ ( value ) => setAttributes( { primaryLabel: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<TextControl
						label={ __( 'Main button link', 'bwfd' ) }
						value={ primaryUrl }
						placeholder={ isBuy ? __( 'Purchase page', 'bwfd' ) : __( 'About the book page', 'bwfd' ) }
						onChange={ ( value ) => setAttributes( { primaryUrl: value } ) }
						type="url"
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<TextControl
						label={ __( 'Second button label', 'bwfd' ) }
						value={ secondaryLabel }
						placeholder={ __( 'Blank for no second button', 'bwfd' ) }
						onChange={ ( value ) => setAttributes( { secondaryLabel: value } ) }
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<TextControl
						label={ __( 'Second button link', 'bwfd' ) }
						value={ secondaryUrl }
						placeholder={ __( 'About the book page', 'bwfd' ) }
						onChange={ ( value ) => setAttributes( { secondaryUrl: value } ) }
						type="url"
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<ToggleControl
						label={ __( 'Show the cover', 'bwfd' ) }
						checked={ showCover }
						onChange={ ( value ) => setAttributes( { showCover: value } ) }
						__nextHasNoMarginBottom
					/>
					{ showCover && (
						<RangeControl
							label={ __( 'Cover width (px)', 'bwfd' ) }
							value={ coverWidth }
							min={ 90 }
							max={ 200 }
							onChange={ ( value ) => setAttributes( { coverWidth: value } ) }
							__nextHasNoMarginBottom
							__next40pxDefaultSize
						/>
					) }
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<ServerSideRender block={ metadata.name } attributes={ attributes } />
			</div>
		</>
	);
}
