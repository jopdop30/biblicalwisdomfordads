import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { decodeEntities } from '@wordpress/html-entities';
import { MediaUpload } from '@wordpress/media-utils';
import {
	Button,
	Card,
	CardBody,
	CardHeader,
	CheckboxControl,
	SelectControl,
	ToggleControl,
	TextControl,
	TextareaControl,
} from '@wordpress/components';
import { closeSmall, plus } from '@wordpress/icons';

const controlProps = { __nextHasNoMarginBottom: true, __next40pxDefaultSize: true };

export function Section( { title, description, children } ) {
	return (
		<Card className="bwfd-schema__section">
			<CardHeader>
				<div>
					<h2 className="bwfd-schema__section-title">{ title }</h2>
					{ description && <p className="bwfd-schema__section-description">{ description }</p> }
				</div>
			</CardHeader>
			<CardBody>{ children }</CardBody>
		</Card>
	);
}

export function Grid( { children } ) {
	return <div className="bwfd-schema-grid">{ children }</div>;
}

export function Full( { children } ) {
	return <div className="bwfd-schema-grid__full">{ children }</div>;
}

export function Text( { label, value, onChange, help, type = 'text' } ) {
	return (
		<TextControl
			{ ...controlProps }
			type={ type }
			label={ label }
			help={ help }
			value={ value ?? '' }
			onChange={ onChange }
		/>
	);
}

export function Number( { label, value, onChange, help, optional = false } ) {
	const shown = optional && ! value ? '' : value ?? 0;
	return (
		<TextControl
			{ ...controlProps }
			type="number"
			min={ 0 }
			label={ label }
			help={ help }
			placeholder={ optional ? __( 'Not set', 'bwfd' ) : undefined }
			value={ shown }
			onChange={ ( next ) => onChange( parseInt( next, 10 ) || 0 ) }
		/>
	);
}

export function Toggle( { label, checked, onChange, help } ) {
	return (
		<ToggleControl
			__nextHasNoMarginBottom
			label={ label }
			help={ help }
			checked={ !! checked }
			onChange={ onChange }
		/>
	);
}

export function Select( { label, value, onChange, options, help } ) {
	return (
		<SelectControl
			{ ...controlProps }
			label={ label }
			help={ help }
			value={ value ?? '' }
			options={ options }
			onChange={ onChange }
		/>
	);
}

export function LongText( { label, value, onChange, help, rows = 4 } ) {
	return (
		<TextareaControl
			__nextHasNoMarginBottom
			label={ label }
			help={ help }
			rows={ rows }
			value={ value ?? '' }
			onChange={ onChange }
		/>
	);
}

/**
 * Image chosen from the Media Library (fills URL and dimensions) or entered
 * as a URL for files that live in the theme.
 */
export function Image( { label, value, onChange, help } ) {
	const image = value ?? { id: 0, url: '', width: 0, height: 0 };
	const update = ( patch ) => onChange( { ...image, ...patch } );

	return (
		<div className="bwfd-schema-image">
			<div className="bwfd-schema-image__thumb" aria-hidden="true">
				{ image.url && <img src={ image.url } alt="" /> }
			</div>
			<div className="bwfd-schema-image__fields">
				<Text
					label={ label }
					help={ help }
					value={ image.url }
					onChange={ ( url ) => update( { id: 0, url } ) }
				/>
				<div className="bwfd-schema-image__row">
					<Number label={ __( 'Width', 'bwfd' ) } value={ image.width } onChange={ ( width ) => update( { width } ) } />
					<Number label={ __( 'Height', 'bwfd' ) } value={ image.height } onChange={ ( height ) => update( { height } ) } />
					<MediaUpload
						allowedTypes={ [ 'image' ] }
						value={ image.id || undefined }
						onSelect={ ( media ) =>
							onChange( {
								id: media.id ?? 0,
								url: media.url ?? '',
								width: media.width ?? 0,
								height: media.height ?? 0,
							} )
						}
						render={ ( { open } ) => (
							<Button variant="secondary" onClick={ open } __next40pxDefaultSize>
								{ __( 'Media Library', 'bwfd' ) }
							</Button>
						) }
					/>
					{ image.url && (
						<Button
							variant="tertiary"
							isDestructive
							onClick={ () => onChange( { id: 0, url: '', width: 0, height: 0 } ) }
							__next40pxDefaultSize
						>
							{ __( 'Remove', 'bwfd' ) }
						</Button>
					) }
				</div>
			</div>
		</div>
	);
}

/**
 * Editable list of URLs.
 */
export function UrlList( { label, items, onChange, help, addLabel } ) {
	const list = items ?? [];
	return (
		<div className="bwfd-schema-list">
			<span className="bwfd-schema-list__label">{ label }</span>
			{ help && <p className="bwfd-schema-list__help">{ help }</p> }
			{ list.map( ( url, index ) => (
				<div className="bwfd-schema-list__row" key={ index }>
					<TextControl
						{ ...controlProps }
						type="url"
						label={ __( 'URL', 'bwfd' ) }
						hideLabelFromVision
						value={ url }
						onChange={ ( next ) => onChange( list.map( ( item, i ) => ( i === index ? next : item ) ) ) }
					/>
					<Button
						icon={ closeSmall }
						label={ __( 'Remove', 'bwfd' ) }
						onClick={ () => onChange( list.filter( ( _, i ) => i !== index ) ) }
						__next40pxDefaultSize
					/>
				</div>
			) ) }
			<Button variant="secondary" icon={ plus } onClick={ () => onChange( [ ...list, '' ] ) } __next40pxDefaultSize>
				{ addLabel ?? __( 'Add link', 'bwfd' ) }
			</Button>
		</div>
	);
}

/**
 * Published pages, for the page pickers and the preview.
 */
export function usePages() {
	return useSelect( ( select ) => {
		const query = {
			per_page: 100,
			status: 'publish',
			orderby: 'title',
			order: 'asc',
			_fields: 'id,title,link',
		};
		const records = select( coreStore ).getEntityRecords( 'postType', 'page', query );
		return ( records ?? [] ).map( ( page ) => ( {
			id: page.id,
			title: decodeEntities( page.title?.rendered ?? '' ) || __( '(no title)', 'bwfd' ),
			link: page.link,
		} ) );
	}, [] );
}

export function PageSelect( { label, value, onChange, pages, help } ) {
	const options = [
		{ value: 0, label: __( '— None —', 'bwfd' ) },
		...pages.map( ( page ) => ( { value: page.id, label: page.title } ) ),
	];
	return (
		<SelectControl
			{ ...controlProps }
			label={ label }
			help={ help }
			value={ value ?? 0 }
			options={ options }
			onChange={ ( next ) => onChange( parseInt( next, 10 ) || 0 ) }
		/>
	);
}

export function PageChecklist( { label, value, onChange, pages, exclude = [], help } ) {
	const selected = value ?? [];
	const candidates = pages.filter( ( page ) => ! exclude.includes( page.id ) );
	return (
		<div className="bwfd-schema-list">
			<span className="bwfd-schema-list__label">{ label }</span>
			{ help && <p className="bwfd-schema-list__help">{ help }</p> }
			<div className="bwfd-schema-checklist">
				{ candidates.map( ( page ) => (
					<CheckboxControl
						__nextHasNoMarginBottom
						key={ page.id }
						label={ page.title }
						checked={ selected.includes( page.id ) }
						onChange={ ( checked ) =>
							onChange(
								checked
									? [ ...selected, page.id ]
									: selected.filter( ( id ) => id !== page.id )
							)
						}
					/>
				) ) }
			</div>
		</div>
	);
}

/**
 * The author's other titles.
 */
export function OtherBooks( { items, onChange } ) {
	const list = items ?? [];
	const updateItem = ( index, patch ) =>
		onChange( list.map( ( item, i ) => ( i === index ? { ...item, ...patch } : item ) ) );

	return (
		<div className="bwfd-schema-books">
			{ list.map( ( book, index ) => (
				<div className="bwfd-schema-book" key={ index }>
					<div className="bwfd-schema-book__fields">
						<Text
							label={ __( 'Title', 'bwfd' ) }
							value={ book.name }
							onChange={ ( name ) => updateItem( index, { name } ) }
						/>
						<Text
							type="url"
							label={ __( 'Purchase link', 'bwfd' ) }
							value={ book.url }
							onChange={ ( url ) => updateItem( index, { url } ) }
						/>
						<Text
							label={ __( 'ISBN', 'bwfd' ) }
							value={ book.isbn }
							onChange={ ( isbn ) => updateItem( index, { isbn } ) }
							help={ __( 'Optional. ISBN-13 of the edition linked above.', 'bwfd' ) }
						/>
						<Image
							label={ __( 'Cover URL', 'bwfd' ) }
							value={ book.image }
							onChange={ ( image ) => updateItem( index, { image } ) }
						/>
					</div>
					<Button
						icon={ closeSmall }
						label={ __( 'Remove book', 'bwfd' ) }
						onClick={ () => onChange( list.filter( ( _, i ) => i !== index ) ) }
						__next40pxDefaultSize
					/>
				</div>
			) ) }
			<Button
				variant="secondary"
				icon={ plus }
				onClick={ () =>
					onChange( [ ...list, { name: '', isbn: '', url: '', image: { id: 0, url: '', width: 0, height: 0 } } ] )
				}
				__next40pxDefaultSize
			>
				{ __( 'Add book', 'bwfd' ) }
			</Button>
		</div>
	);
}
