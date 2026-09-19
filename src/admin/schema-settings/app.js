import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import { store as coreStore, useEntityProp } from '@wordpress/core-data';
import { Button, Notice, Spinner } from '@wordpress/components';
import {
	Section,
	Grid,
	Full,
	Text,
	Number,
	Select,
	Toggle,
	DayRange,
	LongText,
	Image,
	UrlList,
	PageSelect,
	PageChecklist,
	OtherBooks,
	usePages,
} from './fields';
import Preview from './preview';
import { setPath, getPath } from './util';

const config = window.bwfdSchemaSettings ?? { defaults: {}, homeUrl: '/' };

export default function App() {
	const [ data, setData ] = useEntityProp( 'root', 'site', 'bwfd_schema' );
	const { saveEditedEntityRecord } = useDispatch( coreStore );
	const { isSaving, hasEdits, hasLoaded, saveError } = useSelect( ( select ) => {
		const store = select( coreStore );
		return {
			isSaving: store.isSavingEntityRecord( 'root', 'site' ),
			hasEdits: store.hasEditsForEntityRecord( 'root', 'site' ),
			hasLoaded: store.hasFinishedResolution( 'getEntityRecord', [ 'root', 'site' ] ),
			saveError: store.getLastEntitySaveError( 'root', 'site' ),
		};
	}, [] );
	const pages = usePages();
	const [ notice, setNotice ] = useState( null );
	const [ savedAt, setSavedAt ] = useState( 0 );

	// Warn before leaving with unsaved changes, as the editors do.
	useEffect( () => {
		if ( ! hasEdits ) {
			return undefined;
		}
		const warn = ( event ) => {
			event.preventDefault();
			event.returnValue = '';
		};
		window.addEventListener( 'beforeunload', warn );
		return () => window.removeEventListener( 'beforeunload', warn );
	}, [ hasEdits ] );

	if ( ! hasLoaded || ! data ) {
		return (
			<div className="bwfd-schema__loading">
				<Spinner />
			</div>
		);
	}

	const get = ( path, fallback = '' ) => getPath( data, path, fallback );
	const set = ( path ) => ( value ) => setData( setPath( data, path, value ) );

	const save = async () => {
		setNotice( null );
		const saved = await saveEditedEntityRecord( 'root', 'site' );
		if ( saved ) {
			setNotice( { status: 'success', text: __( 'Structured data saved.', 'bwfd' ) } );
			setSavedAt( Date.now() );
		} else {
			setNotice( {
				status: 'error',
				text: saveError?.message || __( 'The settings could not be saved.', 'bwfd' ),
			} );
		}
	};

	const reset = () => {
		// eslint-disable-next-line no-alert
		if ( window.confirm( __( 'Replace every field with the theme defaults? Nothing is saved until you press Save.', 'bwfd' ) ) ) {
			setData( config.defaults );
		}
	};

	const pageIds = {
		aboutBook: get( [ 'pages', 'about_book' ], 0 ),
		purchase: get( [ 'pages', 'purchase' ], 0 ),
		aboutAuthor: get( [ 'pages', 'about_author' ], 0 ),
		otherBooks: get( [ 'pages', 'other_books' ], 0 ),
	};

	// Groups Google reports as "missing field" when half filled. Mirrors
	// the Search Console "Improve item appearance" checks.
	const offer = data.offer ?? {};
	const checks = [];
	if ( offer.return_url && ! ( offer.return_days > 0 ) ) {
		checks.push( __( 'Return window is blank. Google will report a missing returnPolicyCategory on the return policy.', 'bwfd' ) );
	}
	if ( ( offer.return_url || offer.return_days > 0 ) && ! offer.return_fees ) {
		checks.push( __( 'Return postage is not stated. Google will report a missing returnFees on the return policy.', 'bwfd' ) );
	}
	if ( offer.return_fees === 'fixed' && ! offer.return_postage ) {
		checks.push( __( 'A fixed return fee needs an amount.', 'bwfd' ) );
	}
	if ( ( offer.handling_max > 0 ) !== ( offer.transit_max > 0 ) ) {
		checks.push( __( 'Delivery time needs both handling and transit “To” values. Until both are set it is left out of the structured data.', 'bwfd' ) );
	}

	const saveButton = (
		<Button variant="primary" onClick={ save } isBusy={ isSaving } disabled={ isSaving || ! hasEdits } __next40pxDefaultSize>
			{ isSaving ? __( 'Saving…', 'bwfd' ) : __( 'Save', 'bwfd' ) }
		</Button>
	);

	return (
		<div className="bwfd-schema">
			<div className="bwfd-schema__header">
				<p className="bwfd-schema__intro">
					{ __( 'These facts feed the JSON-LD structured data on the site: the Book and its editions, the paperback Product and its offer, the publisher, and the author profile. Endorsements come from the Endorsement blocks on each page and need no entry here.', 'bwfd' ) }
				</p>
				<div className="bwfd-schema__actions">
					<Button variant="tertiary" onClick={ reset } __next40pxDefaultSize>
						{ __( 'Reset to defaults', 'bwfd' ) }
					</Button>
					{ saveButton }
				</div>
			</div>

			{ notice && (
				<Notice status={ notice.status } onRemove={ () => setNotice( null ) }>
					{ notice.text }
				</Notice>
			) }

			<Section
				title={ __( 'Book', 'bwfd' ) }
				description={ __( 'The work itself. Emitted as a Book on the book pages and repeated on the paperback Product.', 'bwfd' ) }
			>
				<Grid>
					<Text label={ __( 'Title', 'bwfd' ) } value={ get( [ 'book', 'name' ] ) } onChange={ set( [ 'book', 'name' ] ) } />
					<Text
						label={ __( 'Foreword by', 'bwfd' ) }
						value={ get( [ 'book', 'foreword' ] ) }
						onChange={ set( [ 'book', 'foreword' ] ) }
						help={ __( 'Listed as a contributor.', 'bwfd' ) }
					/>
					<Full>
						<LongText
							label={ __( 'Description', 'bwfd' ) }
							value={ get( [ 'book', 'description' ] ) }
							onChange={ set( [ 'book', 'description' ] ) }
							help={ __( 'One or two sentences. Used for the Book and the Product.', 'bwfd' ) }
						/>
					</Full>
					<Text label={ __( 'Genre', 'bwfd' ) } value={ get( [ 'book', 'genre' ] ) } onChange={ set( [ 'book', 'genre' ] ) } />
					<Text label={ __( 'Audience', 'bwfd' ) } value={ get( [ 'book', 'audience' ] ) } onChange={ set( [ 'book', 'audience' ] ) } />
					<Text
						label={ __( 'Language', 'bwfd' ) }
						value={ get( [ 'book', 'language' ] ) }
						onChange={ set( [ 'book', 'language' ] ) }
						help={ __( 'Two-letter code, for example en.', 'bwfd' ) }
					/>
					<Number label={ __( 'Pages', 'bwfd' ) } value={ get( [ 'book', 'pages' ], 0 ) } onChange={ set( [ 'book', 'pages' ] ) } />
					<Number label={ __( 'Chapters', 'bwfd' ) } value={ get( [ 'book', 'chapters' ], 0 ) } onChange={ set( [ 'book', 'chapters' ] ) } help={ __( 'Used in “Chapter 22 of 40” and the chapter pages’ call to action.', 'bwfd' ) } />
					<Text
						type="date"
						label={ __( 'Release date', 'bwfd' ) }
						value={ get( [ 'book', 'release_date' ] ) }
						onChange={ set( [ 'book', 'release_date' ] ) }
						help={ __( 'Before this date the offer is marked PreOrder; from this date, InStock.', 'bwfd' ) }
					/>
					<Full>
						<Image
							label={ __( 'Cover URL', 'bwfd' ) }
							value={ get( [ 'book', 'image' ], null ) }
							onChange={ set( [ 'book', 'image' ] ) }
							help={ __( 'Choose from the Media Library to fill the size automatically.', 'bwfd' ) }
						/>
					</Full>
				</Grid>
			</Section>

			<Section
				title={ __( 'Editions', 'bwfd' ) }
				description={ __( 'One column per format. ISBN-13 with or without hyphens; a store link is where that format is sold, for example its Amazon page. Blank fields are left out.', 'bwfd' ) }
			>
				<div className="bwfd-schema-editions">
					<div className="bwfd-schema-edition">
						<h3>{ __( 'Paperback', 'bwfd' ) }</h3>
						<Text label={ __( 'ISBN', 'bwfd' ) } value={ get( [ 'book', 'editions', 'paperback', 'isbn' ] ) } onChange={ set( [ 'book', 'editions', 'paperback', 'isbn' ] ) } />
						<Text type="url" label={ __( 'Store link', 'bwfd' ) } value={ get( [ 'book', 'editions', 'paperback', 'url' ] ) } onChange={ set( [ 'book', 'editions', 'paperback', 'url' ] ) } help={ __( 'Added alongside the direct offer; the Purchase page stays the main link.', 'bwfd' ) } />
					</div>
					<div className="bwfd-schema-edition">
						<h3>{ __( 'eBook', 'bwfd' ) }</h3>
						<Text label={ __( 'ISBN', 'bwfd' ) } value={ get( [ 'book', 'editions', 'ebook', 'isbn' ] ) } onChange={ set( [ 'book', 'editions', 'ebook', 'isbn' ] ) } />
						<Text type="url" label={ __( 'Store link', 'bwfd' ) } value={ get( [ 'book', 'editions', 'ebook', 'url' ] ) } onChange={ set( [ 'book', 'editions', 'ebook', 'url' ] ) } />
						<Text type="date" label={ __( 'Release date', 'bwfd' ) } value={ get( [ 'book', 'editions', 'ebook', 'release_date' ] ) } onChange={ set( [ 'book', 'editions', 'ebook', 'release_date' ] ) } help={ __( 'Only if it differs from the book’s release date.', 'bwfd' ) } />
					</div>
					<div className="bwfd-schema-edition">
						<h3>{ __( 'Audiobook', 'bwfd' ) }</h3>
						<Text label={ __( 'ISBN', 'bwfd' ) } value={ get( [ 'book', 'editions', 'audiobook', 'isbn' ] ) } onChange={ set( [ 'book', 'editions', 'audiobook', 'isbn' ] ) } />
						<Text type="url" label={ __( 'Store link', 'bwfd' ) } value={ get( [ 'book', 'editions', 'audiobook', 'url' ] ) } onChange={ set( [ 'book', 'editions', 'audiobook', 'url' ] ) } />
						<Text type="date" label={ __( 'Release date', 'bwfd' ) } value={ get( [ 'book', 'editions', 'audiobook', 'release_date' ] ) } onChange={ set( [ 'book', 'editions', 'audiobook', 'release_date' ] ) } />
						<Text label={ __( 'Narrator', 'bwfd' ) } value={ get( [ 'book', 'editions', 'audiobook', 'narrator' ] ) } onChange={ set( [ 'book', 'editions', 'audiobook', 'narrator' ] ) } />
						<Number optional label={ __( 'Running time (minutes)', 'bwfd' ) } value={ get( [ 'book', 'editions', 'audiobook', 'minutes' ], 0 ) } onChange={ set( [ 'book', 'editions', 'audiobook', 'minutes' ] ) } />
					</div>
				</div>
			</Section>

			<Section
				title={ __( 'Paperback offer', 'bwfd' ) }
				description={ __( 'The direct-purchase offer on the Product. Google shows price and availability from this.', 'bwfd' ) }
			>
				<Grid>
					<Text label={ __( 'Price', 'bwfd' ) } value={ get( [ 'offer', 'price' ] ) } onChange={ set( [ 'offer', 'price' ] ) } help={ __( 'Numbers only, for example 24.99.', 'bwfd' ) } />
					<Text label={ __( 'Currency', 'bwfd' ) } value={ get( [ 'offer', 'currency' ] ) } onChange={ set( [ 'offer', 'currency' ] ) } help={ __( 'Three-letter code, for example AUD.', 'bwfd' ) } />
					<Text label={ __( 'Postage', 'bwfd' ) } value={ get( [ 'offer', 'postage' ] ) } onChange={ set( [ 'offer', 'postage' ] ) } help={ __( 'Flat rate in the same currency. 0 for free postage.', 'bwfd' ) } />
					<Text label={ __( 'Ships to', 'bwfd' ) } value={ get( [ 'offer', 'ships_to' ] ) } onChange={ set( [ 'offer', 'ships_to' ] ) } help={ __( 'Two-letter country code, for example AU.', 'bwfd' ) } />
					<Text label={ __( 'SKU', 'bwfd' ) } value={ get( [ 'offer', 'sku' ] ) } onChange={ set( [ 'offer', 'sku' ] ) } help={ __( 'Your own product code. No spaces.', 'bwfd' ) } />
					<Text type="url" label={ __( 'Checkout link', 'bwfd' ) } value={ get( [ 'offer', 'buy_url' ] ) } onChange={ set( [ 'offer', 'buy_url' ] ) } help={ __( 'Where the Buy button goes, for example the Square checkout.', 'bwfd' ) } />
					<Text type="date" label={ __( 'Price valid until', 'bwfd' ) } value={ get( [ 'offer', 'price_valid_until' ] ) } onChange={ set( [ 'offer', 'price_valid_until' ] ) } help={ __( 'Optional. Set when the price is due to change, for example at the end of a launch discount.', 'bwfd' ) } />
				</Grid>
			</Section>

			<Section
				title={ __( 'Shipping and returns', 'bwfd' ) }
				description={ __( 'Delivery times and the return policy Google shows with merchant listings. The return policy is also attached to the publisher as the standard policy for the site. Leave a field blank to leave it out.', 'bwfd' ) }
			>
				{ checks.length > 0 && (
					<Notice status="warning" isDismissible={ false } className="bwfd-schema__checks">
						<ul>
							{ checks.map( ( text, index ) => (
								<li key={ index }>{ text }</li>
							) ) }
						</ul>
					</Notice>
				) }
				<Grid>
					<DayRange
						label={ __( 'Handling time (days)', 'bwfd' ) }
						from={ get( [ 'offer', 'handling_min' ], 0 ) }
						to={ get( [ 'offer', 'handling_max' ], 0 ) }
						onChangeFrom={ set( [ 'offer', 'handling_min' ] ) }
						onChangeTo={ set( [ 'offer', 'handling_max' ] ) }
						help={ __( 'From order to dispatch. Both handling and transit are needed for Google to use them.', 'bwfd' ) }
					/>
					<DayRange
						label={ __( 'Transit time (days)', 'bwfd' ) }
						from={ get( [ 'offer', 'transit_min' ], 0 ) }
						to={ get( [ 'offer', 'transit_max' ], 0 ) }
						onChangeFrom={ set( [ 'offer', 'transit_min' ] ) }
						onChangeTo={ set( [ 'offer', 'transit_max' ] ) }
						help={ __( 'Time in the post.', 'bwfd' ) }
					/>
					<Full>
						<Text type="url" label={ __( 'Return policy page', 'bwfd' ) } value={ get( [ 'offer', 'return_url' ] ) } onChange={ set( [ 'offer', 'return_url' ] ) } help={ __( 'Linked from the structured data so Google can show the policy.', 'bwfd' ) } />
					</Full>
					<Number optional label={ __( 'Return window (days)', 'bwfd' ) } value={ get( [ 'offer', 'return_days' ], 0 ) } onChange={ set( [ 'offer', 'return_days' ] ) } help={ __( 'For change-of-mind returns, counted from delivery. Blank means no return window is stated.', 'bwfd' ) } />
					<Select
						label={ __( 'Return postage', 'bwfd' ) }
						value={ get( [ 'offer', 'return_fees' ] ) }
						onChange={ set( [ 'offer', 'return_fees' ] ) }
						options={ [
							{ value: '', label: __( '— Not stated —', 'bwfd' ) },
							{ value: 'free', label: __( 'Free returns', 'bwfd' ) },
							{ value: 'customer', label: __( 'Customer pays the postage', 'bwfd' ) },
							{ value: 'fixed', label: __( 'Fixed return fee', 'bwfd' ) },
						] }
					/>
					{ get( [ 'offer', 'return_fees' ] ) === 'fixed' && (
						<Text label={ __( 'Fixed return fee', 'bwfd' ) } value={ get( [ 'offer', 'return_postage' ] ) } onChange={ set( [ 'offer', 'return_postage' ] ) } help={ __( 'In the offer currency. Google requires this for a fixed fee.', 'bwfd' ) } />
					) }
					<Select
						label={ __( 'Refund type', 'bwfd' ) }
						value={ get( [ 'offer', 'refund_type' ] ) }
						onChange={ set( [ 'offer', 'refund_type' ] ) }
						options={ [
							{ value: '', label: __( '— Not stated —', 'bwfd' ) },
							{ value: 'full', label: __( 'Refund', 'bwfd' ) },
							{ value: 'full_or_exchange', label: __( 'Refund or replacement', 'bwfd' ) },
							{ value: 'exchange', label: __( 'Replacement only', 'bwfd' ) },
							{ value: 'credit', label: __( 'Store credit', 'bwfd' ) },
						] }
					/>
					<Full>
						<Toggle
							label={ __( 'Damaged or faulty items are replaced or refunded free of charge', 'bwfd' ) }
							checked={ get( [ 'offer', 'return_defect_free' ], false ) }
							onChange={ set( [ 'offer', 'return_defect_free' ] ) }
							help={ __( 'Marks faulty-item returns as free while change-of-mind returns follow the postage setting above.', 'bwfd' ) }
						/>
					</Full>
				</Grid>
			</Section>

			<Section
				title={ __( 'Publisher', 'bwfd' ) }
				description={ __( 'The Organization behind the site and the brand on the Product. The logo is the Site Icon.', 'bwfd' ) }
			>
				<Grid>
					<Text label={ __( 'Name', 'bwfd' ) } value={ get( [ 'publisher', 'name' ] ) } onChange={ set( [ 'publisher', 'name' ] ) } />
					<Text label={ __( 'City', 'bwfd' ) } value={ get( [ 'publisher', 'locality' ], '' ) } onChange={ set( [ 'publisher', 'locality' ] ) } help={ __( 'Opens each news item’s dateline: “Brisbane, 19 September 2026.” A news item can set its own.', 'bwfd' ) } />
					<Full>
						<UrlList
							label={ __( 'Social profiles', 'bwfd' ) }
							items={ get( [ 'publisher', 'same_as' ], [] ) }
							onChange={ set( [ 'publisher', 'same_as' ] ) }
							help={ __( 'Facebook, Instagram and other official pages for the book or publisher.', 'bwfd' ) }
						/>
					</Full>
				</Grid>
			</Section>

			<Section
				title={ __( 'Author', 'bwfd' ) }
				description={ __( 'The Person on every book page, and the subject of the About the author profile page.', 'bwfd' ) }
			>
				<Grid>
					<Text label={ __( 'Name', 'bwfd' ) } value={ get( [ 'author', 'name' ] ) } onChange={ set( [ 'author', 'name' ] ) } />
					<Text type="email" label={ __( 'Email', 'bwfd' ) } value={ get( [ 'author', 'email' ] ) } onChange={ set( [ 'author', 'email' ] ) } />
					<Text label={ __( 'Role', 'bwfd' ) } value={ get( [ 'author', 'job_title' ] ) } onChange={ set( [ 'author', 'job_title' ] ) } />
					<Text label={ __( 'Organisation', 'bwfd' ) } value={ get( [ 'author', 'employer' ] ) } onChange={ set( [ 'author', 'employer' ] ) } help={ __( 'Where the author works.', 'bwfd' ) } />
					<Full>
						<LongText
							label={ __( 'Biography', 'bwfd' ) }
							value={ get( [ 'author', 'description' ] ) }
							onChange={ set( [ 'author', 'description' ] ) }
							help={ __( 'A short paragraph. The page itself carries the full biography.', 'bwfd' ) }
						/>
					</Full>
					<Full>
						<Image
							label={ __( 'Portrait URL', 'bwfd' ) }
							value={ get( [ 'author', 'image' ], null ) }
							onChange={ set( [ 'author', 'image' ] ) }
						/>
					</Full>
					<Full>
						<UrlList
							label={ __( 'Profile links', 'bwfd' ) }
							items={ get( [ 'author', 'same_as' ], [] ) }
							onChange={ set( [ 'author', 'same_as' ] ) }
							help={ __( 'The author’s own profiles: LinkedIn, Facebook, Amazon author page, Goodreads.', 'bwfd' ) }
						/>
					</Full>
				</Grid>
			</Section>

			<Section
				title={ __( 'Other books', 'bwfd' ) }
				description={ __( 'Earlier titles by the author, emitted as Books on the Other books page.', 'bwfd' ) }
			>
				<OtherBooks items={ get( [ 'author', 'other_books' ], [] ) } onChange={ set( [ 'author', 'other_books' ] ) } />
			</Section>

			<Section
				title={ __( 'News and Insights archives', 'bwfd' ) }
				description={ __( 'The title tag and search description of the two archive pages. Blank falls back to the section name and its standard description.', 'bwfd' ) }
			>
				<Grid>
					<Text label={ __( 'News archive title', 'bwfd' ) } value={ get( [ 'archives', 'news', 'title' ], '' ) } onChange={ set( [ 'archives', 'news', 'title' ] ) } help={ __( 'The whole title tag. Aim for under 60 characters.', 'bwfd' ) } />
					<Text label={ __( 'Insights archive title', 'bwfd' ) } value={ get( [ 'archives', 'insights', 'title' ], '' ) } onChange={ set( [ 'archives', 'insights', 'title' ] ) } help={ __( 'The whole title tag. Aim for under 60 characters.', 'bwfd' ) } />
					<LongText label={ __( 'News archive description', 'bwfd' ) } value={ get( [ 'archives', 'news', 'description' ], '' ) } onChange={ set( [ 'archives', 'news', 'description' ] ) } rows={ 3 } help={ __( 'One or two sentences, up to about 158 characters.', 'bwfd' ) } />
					<LongText label={ __( 'Insights archive description', 'bwfd' ) } value={ get( [ 'archives', 'insights', 'description' ], '' ) } onChange={ set( [ 'archives', 'insights', 'description' ] ) } rows={ 3 } help={ __( 'One or two sentences, up to about 158 characters.', 'bwfd' ) } />
					<Text label={ __( 'Chapters index title', 'bwfd' ) } value={ get( [ 'archives', 'chapters', 'title' ], '' ) } onChange={ set( [ 'archives', 'chapters', 'title' ] ) } help={ __( 'The whole title tag of /chapters/.', 'bwfd' ) } />
					<LongText label={ __( 'Chapters index description', 'bwfd' ) } value={ get( [ 'archives', 'chapters', 'description' ], '' ) } onChange={ set( [ 'archives', 'chapters', 'description' ] ) } rows={ 3 } />
					<Text label={ __( 'Topic page title', 'bwfd' ) } value={ get( [ 'archives', 'topic', 'title' ], '' ) } onChange={ set( [ 'archives', 'topic', 'title' ] ) } help={ __( 'Write {topic} where the topic name goes; the site name is added after it.', 'bwfd' ) } />
					<LongText label={ __( 'Topic page description', 'bwfd' ) } value={ get( [ 'archives', 'topic', 'description' ], '' ) } onChange={ set( [ 'archives', 'topic', 'description' ] ) } rows={ 3 } help={ __( 'Used when the topic has no description of its own. {topic}, {author} and {book} are filled in.', 'bwfd' ) } />
				</Grid>
			</Section>

			<Section
				title={ __( 'Pages', 'bwfd' ) }
				description={ __( 'Which pages carry which structured data. The About the book and Purchase pages also supply the Book and Offer URLs.', 'bwfd' ) }
			>
				<Grid>
					<PageSelect label={ __( 'About the book', 'bwfd' ) } pages={ pages } value={ pageIds.aboutBook } onChange={ set( [ 'pages', 'about_book' ] ) } help={ __( 'Carries the Book and Product; used as the Book URL.', 'bwfd' ) } />
					<PageSelect label={ __( 'Purchase', 'bwfd' ) } pages={ pages } value={ pageIds.purchase } onChange={ set( [ 'pages', 'purchase' ] ) } help={ __( 'Carries the Book and Product; used as the Offer URL.', 'bwfd' ) } />
					<PageSelect label={ __( 'About the author', 'bwfd' ) } pages={ pages } value={ pageIds.aboutAuthor } onChange={ set( [ 'pages', 'about_author' ] ) } help={ __( 'Marked up as a ProfilePage.', 'bwfd' ) } />
					<PageSelect label={ __( 'Other books', 'bwfd' ) } pages={ pages } value={ pageIds.otherBooks } onChange={ set( [ 'pages', 'other_books' ] ) } />
					<Full>
						<PageChecklist
							label={ __( 'Also carry the Book and Product on', 'bwfd' ) }
							pages={ pages }
							exclude={ [ pageIds.aboutBook, pageIds.purchase, pageIds.aboutAuthor, pageIds.otherBooks ].filter( Boolean ) }
							value={ get( [ 'pages', 'extra_book_pages' ], [] ) }
							onChange={ set( [ 'pages', 'extra_book_pages' ] ) }
							help={ __( 'Typically the home page and any trade or bulk-order page.', 'bwfd' ) }
						/>
					</Full>
				</Grid>
			</Section>

			<Section
				title={ __( 'Preview', 'bwfd' ) }
				description={ __( 'The JSON-LD a page sends, so you can check a change after saving.', 'bwfd' ) }
			>
				<Preview pages={ pages } extras={ config.previews ?? [] } initialPageId={ pageIds.aboutBook } savedAt={ savedAt } />
			</Section>

			<div className="bwfd-schema__footer">{ saveButton }</div>
		</div>
	);
}
