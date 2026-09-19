/**
 * "Before you publish" checklist for News items and Insights.
 *
 * A document sidebar panel that scores the item live against the editorial
 * SEO checks (headline, excerpt, image, length, Scripture, links…) and
 * gives every unfinished line a button that opens the right panel or
 * selects the right block. The unfinished lines repeat in the pre-publish
 * confirmation. Rules come from window.bwfdSeoChecklist (inc/seo.php).
 */
import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel, PluginPrePublishPanel } from '@wordpress/editor';
import { useSelect, useDispatch, select as selectStore, dispatch as dispatchStore } from '@wordpress/data';
import { useEntityProp, store as coreStore } from '@wordpress/core-data';
import { createBlock } from '@wordpress/blocks';
import { Button } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';
import { decodeEntities } from '@wordpress/html-entities';
import './seo-checklist.scss';

const config = window.bwfdSeoChecklist || {};
const rules = config.rules || {};
const SITE_SUFFIX = ` ${ config.separator || '-' } ${ config.siteName || '' }`;

/* -------------------------------------------------------------------------
 * Navigation helpers: take the writer to the field or block in question.
 * ---------------------------------------------------------------------- */

function openDocumentSidebar() {
	const editPost = dispatchStore( 'core/edit-post' );
	if ( editPost && editPost.openGeneralSidebar ) {
		editPost.openGeneralSidebar( 'edit-post/document' );
		return;
	}
	const iface = dispatchStore( 'core/interface' );
	if ( iface && iface.enableComplementaryArea ) {
		iface.enableComplementaryArea( 'core', 'edit-post/document' );
	}
}

function closePublishSidebar() {
	for ( const name of [ 'core/editor', 'core/edit-post' ] ) {
		const store = dispatchStore( name );
		if ( store && store.closePublishSidebar ) {
			store.closePublishSidebar();
			return;
		}
	}
}

function focusLater( selector ) {
	window.setTimeout( () => {
		const el = document.querySelector( selector );
		if ( el ) {
			el.scrollIntoView( { block: 'center', behavior: 'smooth' } );
			if ( typeof el.focus === 'function' ) {
				el.focus();
			}
		}
	}, 300 );
}

function openPanel( panelName, focusSelector ) {
	closePublishSidebar();
	openDocumentSidebar();
	const editor = selectStore( 'core/editor' );
	if ( editor.isEditorPanelEnabled( panelName ) && ! editor.isEditorPanelOpened( panelName ) ) {
		dispatchStore( 'core/editor' ).toggleEditorPanelOpened( panelName );
	}
	if ( focusSelector ) {
		focusLater( focusSelector );
	}
}

/**
 * Excerpt (WordPress 6.6+): an "Edit excerpt" dropdown in the Summary
 * panel that opens a popover holding the textarea.
 */
function openExcerpt() {
	openPanel( 'post-excerpt' );
	window.setTimeout( () => {
		const textarea = document.querySelector( '.editor-post-excerpt__textarea textarea, textarea.editor-post-excerpt__textarea' );
		if ( textarea ) {
			textarea.scrollIntoView( { block: 'center', behavior: 'smooth' } );
			textarea.focus();
			return;
		}
		const toggle = document.querySelector( '.editor-post-excerpt__dropdown button' );
		if ( toggle ) {
			toggle.scrollIntoView( { block: 'center', behavior: 'smooth' } );
			toggle.click();
			focusLater( '.components-popover textarea, .editor-post-excerpt__textarea textarea' );
		}
	}, 350 );
}

/**
 * Featured image: with none set, open the Media Library straight away;
 * with one set, show the preview (alt text is edited in the library).
 */
function openFeaturedImage( hasImage ) {
	openPanel( 'featured-image' );
	window.setTimeout( () => {
		const toggle = document.querySelector( '.editor-post-featured-image__toggle' );
		const preview = document.querySelector( '.editor-post-featured-image__preview' );
		const target = hasImage ? preview || toggle : toggle;
		if ( ! target ) {
			return;
		}
		target.scrollIntoView( { block: 'center', behavior: 'smooth' } );
		if ( hasImage ) {
			target.focus();
		} else {
			target.click();
		}
	}, 350 );
}

/**
 * Focus a sidebar field by its label text once its panel is open.
 */
function focusField( panelName, labelText ) {
	openPanel( panelName );
	window.setTimeout( () => {
		const label = [ ...document.querySelectorAll( '.interface-complementary-area label, .editor-sidebar label' ) ].find( ( l ) => l.textContent.trim() === labelText );
		const field = label && label.htmlFor ? document.getElementById( label.htmlFor ) : null;
		if ( field ) {
			field.scrollIntoView( { block: 'center', behavior: 'smooth' } );
			field.focus();
		}
	}, 350 );
}

function blocksNamed( name ) {
	const be = selectStore( 'core/block-editor' );
	return be.getClientIdsWithDescendants().filter( ( id ) => be.getBlockName( id ) === name );
}

function selectBlock( clientId ) {
	closePublishSidebar();
	dispatchStore( 'core/block-editor' ).selectBlock( clientId );
	window.setTimeout( () => {
		const canvas = document.querySelector( 'iframe[name="editor-canvas"]' );
		const doc = canvas && canvas.contentDocument ? canvas.contentDocument : document;
		const el = doc.querySelector( `[data-block="${ clientId }"]` );
		if ( el ) {
			el.scrollIntoView( { block: 'center', behavior: 'smooth' } );
		}
	}, 200 );
}

function focusTitle() {
	const ids = blocksNamed( 'core/post-title' );
	if ( ids.length ) {
		selectBlock( ids[ 0 ] );
		return;
	}
	closePublishSidebar();
	focusLater( '.editor-post-title, .editor-post-title__input' );
}

function contentRoot() {
	const ids = blocksNamed( 'core/post-content' );
	return ids.length ? ids[ 0 ] : '';
}

function focusContent() {
	const be = selectStore( 'core/block-editor' );
	const root = contentRoot();
	const order = be.getBlockOrder( root );
	if ( order.length ) {
		selectBlock( order[ 0 ] );
	}
}

function focusScripture() {
	const ids = blocksNamed( 'bwfd/scripture' );
	if ( ids.length ) {
		selectBlock( ids[ 0 ] );
		return;
	}
	const block = createBlock( 'bwfd/scripture' );
	dispatchStore( 'core/block-editor' ).insertBlock( block, 0, contentRoot() || undefined, true );
	closePublishSidebar();
}

/* -------------------------------------------------------------------------
 * The checks.
 * ---------------------------------------------------------------------- */

const strip = ( html ) => decodeEntities( ( html || '' ).replace( /<!--[\s\S]*?-->/g, ' ' ).replace( /<[^>]+>/g, ' ' ) ).replace( /\s+/g, ' ' ).trim();
const words = ( text ) => ( text ? text.split( /\s+/ ).filter( Boolean ).length : 0 );

function buildChecks( state, typeRules ) {
	const { title, excerpt, content, meta, featuredId, media, homeUrl, postType, topics = [], chapters = [] } = state;
	const headline = strip( title );
	const excerptText = strip( excerpt );
	const body = strip( content );
	const wordCount = words( body );
	const searchTitle = ( meta && meta.bwfd_seo_title ) || '';
	const effectiveTitle = searchTitle || `${ headline }${ SITE_SUFFIX }`;
	const scripture = blocksNamed( 'bwfd/scripture' ).map( ( id ) => selectStore( 'core/block-editor' ).getBlockAttributes( id ) );
	const verse = scripture.find( ( a ) => a && a.text && a.text.trim() );
	const reference = verse && verse.reference ? verse.reference.trim() : '';
	const internalLinks = ( content.match( /href="([^"]+)"/g ) || [] ).filter( ( h ) => h.includes( `href="${ homeUrl }` ) || h.includes( 'href="/' ) ).length;
	const headings = ( content.match( /<!-- wp:heading/g ) || [] ).length;

	const checks = [
		{
			id: 'headline',
			required: true,
			done: headline.length >= 20 && headline.length <= 70,
			label: __( 'Headline between 20 and 70 characters', 'bwfd' ),
			detail: headline.length
				? sprintf( /* translators: %d: character count */ __( '%d characters now.', 'bwfd' ), headline.length )
				: __( 'No headline yet.', 'bwfd' ),
			action: __( 'Edit headline', 'bwfd' ),
			go: focusTitle,
		},
		{
			id: 'excerpt',
			required: true,
			done: excerptText.length >= 70 && excerptText.length <= 158 && /[.!?]["”’)]?$/.test( excerptText ),
			label: __( 'Excerpt of one or two full sentences (70 to 158 characters)', 'bwfd' ),
			detail: excerptText.length
				? sprintf( /* translators: %d: character count */ __( '%d characters now. It becomes the search description and the teaser on the archive.', 'bwfd' ), excerptText.length )
				: __( 'Empty. Search engines and the archive teaser use it.', 'bwfd' ),
			action: __( 'Write excerpt', 'bwfd' ),
			go: openExcerpt,
		},
		{
			id: 'length',
			required: false,
			done: wordCount >= ( typeRules.minWords || 150 ),
			label: sprintf( /* translators: %d: minimum word count */ __( 'At least %d words', 'bwfd' ), typeRules.minWords || 150 ),
			detail: sprintf( /* translators: %d: word count */ __( '%d words now. Very short pages rarely rank.', 'bwfd' ), wordCount ),
			action: __( 'Go to text', 'bwfd' ),
			go: focusContent,
		},
		{
			id: 'image',
			required: !! typeRules.imageRequired,
			done: !! featuredId,
			label: __( 'Featured image, landscape (at least 1200 pixels wide)', 'bwfd' ),
			detail: featuredId
				? __( 'Set. It is the picture shown when the link is shared.', 'bwfd' )
				: __( 'None. Shares fall back to the book cover, which crops badly on Facebook and LinkedIn.', 'bwfd' ),
			action: __( 'Set image', 'bwfd' ),
			go: () => openFeaturedImage( false ),
		},
	];

	if ( featuredId ) {
		checks.push( {
			id: 'alt',
			required: false,
			done: !! ( media && media.alt_text && media.alt_text.trim() ),
			label: __( 'Featured image has alt text', 'bwfd' ),
			detail: media && media.alt_text ? __( 'Set in the Media Library.', 'bwfd' ) : __( 'Add it in the Media Library: describe what the picture shows.', 'bwfd' ),
			action: __( 'Open image', 'bwfd' ),
			go: () => openFeaturedImage( true ),
		} );
	}

	if ( typeRules.scripture ) {
		checks.push( {
			id: 'scripture',
			required: true,
			done: !! ( verse && reference ),
			label: __( 'Opens with a Scripture block, verse and reference filled in', 'bwfd' ),
			detail: verse
				? ( reference ? sprintf( /* translators: %s: reference */ __( '%s. It feeds the featured panel on the Insights page.', 'bwfd' ), reference ) : __( 'The verse is in; the reference is missing.', 'bwfd' ) )
				: __( 'None yet. The button adds one at the top.', 'bwfd' ),
			action: verse ? __( 'Go to verse', 'bwfd' ) : __( 'Add Scripture', 'bwfd' ),
			go: focusScripture,
		} );
		checks.push( {
			id: 'reference-in-copy',
			required: false,
			done: !! reference && ( headline.toLowerCase().includes( reference.toLowerCase() ) || excerptText.toLowerCase().includes( reference.toLowerCase() ) || searchTitle.toLowerCase().includes( reference.toLowerCase() ) ),
			label: __( 'Verse reference in the headline, excerpt or search title', 'bwfd' ),
			detail: reference
				? sprintf( /* translators: %s: reference */ __( 'People search for “%s”. Naming it in the copy helps them find this.', 'bwfd' ), reference )
				: __( 'Add the Scripture block first.', 'bwfd' ),
			action: __( 'Search title', 'bwfd' ),
			go: () => focusField( 'bwfd-search-appearance/bwfd-search-appearance', __( 'Search title', 'bwfd' ) ),
		} );
	}

	if ( typeRules.topics ) {
		checks.push( {
			id: 'topics',
			required: true,
			done: topics.length >= 1,
			label: __( 'At least one topic', 'bwfd' ),
			detail: topics.length
				? sprintf( /* translators: %d: count */ __( '%d topic(s). Each topic has its own page listing the insights on it.', 'bwfd' ), topics.length )
				: __( 'None. Topics give the piece a home page on the site and a theme for search.', 'bwfd' ),
			action: __( 'Choose topics', 'bwfd' ),
			go: () => openPanel( 'taxonomy-panel-bwfd_topic', '.editor-post-taxonomies__hierarchical-terms-list' ),
		} );
		checks.push( {
			id: 'chapters',
			required: false,
			done: chapters.length >= 1,
			label: __( 'Linked to the chapter(s) it draws on', 'bwfd' ),
			detail: chapters.length
				? sprintf( /* translators: %d: count */ __( '%d chapter(s). The insight is listed on those chapter pages and names them in “In the book”.', 'bwfd' ), chapters.length )
				: __( 'None. Linking a chapter sends readers to the book and helps searches for that chapter.', 'bwfd' ),
			action: __( 'Choose chapters', 'bwfd' ),
			go: () => openPanel( 'taxonomy-panel-bwfd_chapter', '.editor-post-taxonomies__hierarchical-terms-list' ),
		} );
	}

	checks.push( {
		id: 'search-title',
		required: false,
		done: effectiveTitle.length <= 60,
		label: __( 'Title as shown in search results fits in 60 characters', 'bwfd' ),
		detail: sprintf( /* translators: 1: character count, 2: title */ __( '%1$d characters: “%2$s”. Set a shorter search title if the full one gets cut.', 'bwfd' ), effectiveTitle.length, effectiveTitle ),
		action: __( 'Search title', 'bwfd' ),
		go: () => focusField( 'bwfd-search-appearance/bwfd-search-appearance', __( 'Search title', 'bwfd' ) ),
	} );

	checks.push( {
		id: 'links',
		required: false,
		done: internalLinks >= 1,
		label: __( 'Links to another page on this site', 'bwfd' ),
		detail: internalLinks
			? sprintf( /* translators: %d: link count */ __( '%d internal link(s).', 'bwfd' ), internalLinks )
			: __( 'None. Link a phrase to the book page, the Small Group Guide or another piece.', 'bwfd' ),
		action: __( 'Go to text', 'bwfd' ),
		go: focusContent,
	} );

	if ( wordCount >= ( typeRules.headingsFrom || 400 ) ) {
		checks.push( {
			id: 'headings',
			required: false,
			done: headings >= 1,
			label: __( 'Longer piece broken up with subheadings', 'bwfd' ),
			detail: headings ? sprintf( /* translators: %d: heading count */ __( '%d heading(s).', 'bwfd' ), headings ) : __( 'Add a Heading block or two so readers can scan.', 'bwfd' ),
			action: __( 'Go to text', 'bwfd' ),
			go: focusContent,
		} );
	}

	return checks;
}

/* -------------------------------------------------------------------------
 * Components.
 * ---------------------------------------------------------------------- */

function useChecklist() {
	const { postType, postId, title, excerpt, content, featuredId, isSaving, topics, chapters } = useSelect( ( select ) => {
		const editor = select( 'core/editor' );
		return {
			postType: editor.getCurrentPostType(),
			postId: editor.getCurrentPostId(),
			title: editor.getEditedPostAttribute( 'title' ),
			excerpt: editor.getEditedPostAttribute( 'excerpt' ),
			content: editor.getEditedPostContent(),
			featuredId: editor.getEditedPostAttribute( 'featured_media' ),
			isSaving: editor.isSavingPost(),
			topics: editor.getEditedPostAttribute( 'bwfd_topic' ) || [],
			chapters: editor.getEditedPostAttribute( 'bwfd_chapter' ) || [],
		};
	}, [] );
	const media = useSelect( ( select ) => ( featuredId ? select( coreStore ).getMedia( featuredId ) : null ), [ featuredId ] );
	const [ meta ] = useEntityProp( 'postType', postType, 'meta', postId );
	const typeRules = rules[ postType ];
	if ( ! typeRules ) {
		return null;
	}
	const checks = buildChecks( { title, excerpt, content, meta, featuredId, media, homeUrl: config.homeUrl || '/', postType, topics, chapters }, typeRules );
	return { checks, isSaving };
}

function ChecklistItem( { check } ) {
	return (
		<li className={ `bwfd-checklist__item ${ check.done ? 'is-done' : 'is-todo' } ${ check.required ? 'is-required' : '' }` }>
			<span className="bwfd-checklist__mark" aria-hidden="true">{ check.done ? '✓' : '' }</span>
			<div className="bwfd-checklist__text">
				<span className="bwfd-checklist__label">
					{ check.label }
					{ ! check.done && check.required && <em className="bwfd-checklist__badge">{ __( 'needed', 'bwfd' ) }</em> }
				</span>
				<span className="bwfd-checklist__detail">{ check.detail }</span>
			</div>
			{ ! check.done && (
				<Button variant="secondary" size="small" onClick={ check.go }>
					{ check.action }
				</Button>
			) }
		</li>
	);
}

function Checklist( { checks, showDone } ) {
	const shown = showDone ? checks : checks.filter( ( c ) => ! c.done );
	if ( ! shown.length ) {
		return <p className="bwfd-checklist__allset">{ __( 'All set. Nothing left on the checklist.', 'bwfd' ) }</p>;
	}
	return (
		<ul className="bwfd-checklist">
			{ shown.map( ( check ) => <ChecklistItem key={ check.id } check={ check } /> ) }
		</ul>
	);
}

function Summary( { checks } ) {
	const done = checks.filter( ( c ) => c.done ).length;
	const missing = checks.filter( ( c ) => ! c.done && c.required ).length;
	return (
		<p className="bwfd-checklist__summary">
			<strong>{ sprintf( /* translators: 1: done count, 2: total */ __( '%1$d of %2$d done', 'bwfd' ), done, checks.length ) }</strong>
			{ missing > 0 && <span className="bwfd-checklist__summary-warn">{ sprintf( /* translators: %d: count */ __( '%d needed before publishing', 'bwfd' ), missing ) }</span> }
		</p>
	);
}

function SidebarPanel() {
	const data = useChecklist();
	if ( ! data ) {
		return null;
	}
	return (
		<PluginDocumentSettingPanel name="bwfd-seo-checklist" title={ __( 'Before you publish', 'bwfd' ) } className="bwfd-checklist-panel">
			<Summary checks={ data.checks } />
			<Checklist checks={ data.checks } showDone />
		</PluginDocumentSettingPanel>
	);
}

function PrePublishPanel() {
	const data = useChecklist();
	if ( ! data ) {
		return null;
	}
	const todo = data.checks.filter( ( c ) => ! c.done );
	return (
		<PluginPrePublishPanel title={ todo.length ? sprintf( /* translators: %d: count */ __( 'Checklist: %d to do', 'bwfd' ), todo.length ) : __( 'Checklist: all done', 'bwfd' ) } initialOpen className="bwfd-checklist-panel">
			<Checklist checks={ data.checks } showDone={ false } />
		</PluginPrePublishPanel>
	);
}

registerPlugin( 'bwfd-seo-checklist', {
	render: () => (
		<>
			<SidebarPanel />
			<PrePublishPanel />
		</>
	),
} );
