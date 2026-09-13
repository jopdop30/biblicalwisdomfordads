# Biblical Wisdom for Dads – WordPress block theme

Block theme (full site editing) for [biblicalwisdomfordads.au](https://www.biblicalwisdomfordads.au), built from the "Biblical Wisdom for Dads v3" Claude Design file. Requires WordPress 6.7+ (developed against 7.1) and PHP 8.0+.

## What is in the theme

| Path | Purpose |
| --- | --- |
| `theme.json` | Design tokens: palette, gradients, shadows, bundled fonts (Bitter, Source Sans 3), fluid font sizes, spacing scale, global element styles, template parts and custom templates. |
| `style.css` | Texture surfaces, button/table/group block styles, header and footer, layout helpers. Loaded on the front end and in the editor. |
| `templates/` | `page` (default, no title – the designed pages carry their own heading), `page-with-title`, `page-plain` (white sheet), `single`, `index`, `search`, `404`. |
| `parts/` | `header` and `footer`, each rendering a PHP pattern so links can use `home_url()`. |
| `patterns/` | Eight complete page patterns (category **BWFD pages**) and reusable sections (category **BWFD sections**): navy call-to-action band, endorsement sets, key information reveals, bulk order cards. |
| `src/blocks/` | Custom block sources (see below). Built to `build/` with `@wordpress/scripts`. |
| `inc/` | Block registration, block style variations, pattern categories, SVG icon library. |
| `bin/import-pages.php` | WP-CLI script that creates/updates the eight pages from the patterns, imports the images into the Media Library, sets the front page and writes the primary navigation. |
| `assets/` | Imagery from the design as right-sized WebP (covers 800–900px wide, textures 1000–1600px), favicon set, and the two variable fonts (latin subsets). |

## Custom blocks

All blocks live under the **Biblical Wisdom for Dads** inserter category.

| Block | Notes |
| --- | --- |
| Section heading | Heading with the apricot rule. Level, size preset, alignment, rule toggle, text colour. |
| Card | Textured card with accent top border. Surface (marble, white, apricot, navy), accent (blue, apricot, none), padding, min-height, optional shadow. Holds headings, text, buttons, icon, badge, image. |
| Card grid | Responsive grid of cards: auto-fit by minimum width, fixed columns, or centred wrapping rows. |
| Framed image | Cover/portrait with a soft shadow, optional apricot offset panel, ratio crop and max width. |
| Badge | Small uppercase status pill ("Coming soon", "URL TBA"). |
| Icon | One of thirteen line icons, plain or in an apricot circle. Rendered server-side from `inc/icons.php`. |
| Facebook page feed | Facebook Page Plugin embed (timeline by default) for facebook.com/biblicalwisdomfordads. Page URL, tabs, height, header and cover options in the sidebar; sized to its container. |
| Hero | Full-width hero on the silhouette artwork with radial, linear or solid navy wash (and a reversed, pale-behind-text option). |
| Endorsements carousel | Interactivity API carousel: autoplay with progress bar, pause, previous/next, dots or "n of N" counter, keyboard arrows, hover pause, reduced-motion aware. Child block: Endorsement. |
| Reveal panel | Interactivity API disclosure ("Key information" / "Hide key information") wrapping any blocks. |

Block styles registered for core blocks: Group (Navy texture, Apricot texture, Marble), Button (Apricot, Text link, Large call to action, Compact, Social), Table (Key information, Pricing).

## Development

```bash
cd wp-content/themes/bwfd
nvm use 22
npm install
npm run build      # production build to build/ (includes blocks-manifest.php)
npm run start      # watch mode
```

The build uses `wp-scripts` with `--experimental-modules` so the Interactivity API `view.js` files ship as script modules, and `--blocks-manifest` so blocks register through `wp_register_block_types_from_metadata_collection()`.

`build/` and `node_modules/` are git-ignored. Run `npm run build` after cloning, or commit `build/` for deployments without Node.

## Provisioning a site

With the theme active:

```bash
wp eval-file wp-content/themes/bwfd/bin/import-pages.php
```

Pass page slugs to update only those pages (`... import-pages.php home`). The script is idempotent. It creates or updates Home, About the book, About the author, Purchase, Small Group Guide, Other books, Enjoyed? and Churches & retail from the page patterns (nested patterns inlined so every page is literal, editable content), imports the design imagery into the Media Library, sets Home as the static front page, writes the primary navigation menu and drafts the default Sample Page.

## Editing

* Every page is ordinary block content: open it in the editor and change text, links, images or cards directly.
* To start a new page from a design layout, add a page and pick a layout from the **BWFD pages** patterns in the "Choose a pattern" modal, or insert a **BWFD sections** pattern anywhere.
* Colours, fonts, spacing and button styles are managed in **Appearance → Editor → Styles** via `theme.json`.
* Header and footer are template parts; the menu is a normal Navigation block menu ("Primary navigation"). The "Buy the book" item is styled as a button through the `bwfd-nav-button` CSS class on that link.
* The Facebook feed card on the home page embeds the page timeline; change the page URL or tabs in the block sidebar.
* Purchase and media-kit links marked "URL TBA" / `#` are placeholders awaiting final URLs.

## SEO and performance

`inc/seo.php` adds what core leaves out, without touching content:

* `<meta name="description">` from the page **Excerpt** (pages gain an Excerpt panel), falling back to the first substantial paragraphs of the page, then the tagline.
* Open Graph and Twitter card tags; the share image is the featured image, else the first image in the content, else the book cover.
* JSON-LD: Organization, WebSite and WebPage on every page, the Book (with ISBNs and formats) on Home and About the book, and the author Person on About the author. Facts live in `bwfd_book_data()` and can be changed with the `bwfd_book_data` filter.
* Emoji script, generator tag, shortlink and RSD/WLW links removed; Facebook preconnect only on pages with the feed; the hero cover is preloaded on the front page.
* Uploaded JPEG/PNG images get WebP sub-sizes (`image_editor_output_format`).
* Core provides the rest: title tag, canonical, robots, XML sitemap at `/wp-sitemap.xml`.

Framed image blocks output `width`/`height` so the browser reserves space and can lazy-load below-the-fold images.

## Fonts

Bitter and Source Sans 3 are bundled as variable woff2 files (latin subset, SIL Open Font License) and declared in `theme.json`, so no requests go to Google Fonts.
