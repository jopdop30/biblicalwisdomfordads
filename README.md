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

`node_modules/` is git-ignored. `build/` **is committed** so the theme deploys to hosts without Node (cPanel). Run `npm run build` before committing any change under `src/`.

## Provisioning a site

With the theme active:

```bash
wp eval-file wp-content/themes/bwfd/bin/import-pages.php
```

To fix heading levels on pages edited on the live site (PageSpeed's accessibility check flags an h3 directly under an h1), run:

```bash
wp eval-file wp-content/themes/bwfd/bin/fix-heading-levels.php
```

It walks each published page's blocks in order and promotes any heading that skips a level (h1 then h3 becomes h1 then h2), updating both the block attributes and the markup. Pass slugs to limit it to those pages. It is idempotent and prints every change.

Pass page slugs to update only those pages (`... import-pages.php home`). The script is idempotent. It creates or updates Home, About the book, About the author, Purchase, Small Group Guide, Other books, Enjoyed? and Churches & retail from the page patterns (nested patterns inlined so every page is literal, editable content), imports the design imagery into the Media Library, sets Home as the static front page, writes the primary navigation menu and drafts the default Sample Page.

## Deploying with cPanel Git Version Control

The repository includes `.cpanel.yml`, so cPanel can deploy the theme straight into WordPress:

1. Push this repository to a remote (GitHub, Bitbucket or GitLab; a private repo is fine).
2. In cPanel open **Git™ Version Control → Create**, turn on *Clone a Repository*, paste the clone URL, set the repository path to something outside the web root such as `/home/runningf/repositories/bwfd`, and create it. For a private repo use the SSH clone URL and add the key from cPanel's **SSH Access → Manage SSH Keys** as a deploy key on the remote.
3. Edit `DEPLOYPATH` in `.cpanel.yml` to the site's theme folder (see the comment in the file), commit and push.
4. To release: push to the remote, then in cPanel Git Version Control choose **Manage → Pull or Deploy → Update from Remote**, then **Deploy HEAD Commit**. cPanel copies the files into the theme folder; WordPress picks them up immediately.

The alternative without a remote is to push directly to the cPanel repository over SSH (`git remote add cpanel ssh://runningf@server:/home/runningf/repositories/bwfd`); deploying on push happens automatically when `.cpanel.yml` is present.

Content changes (pages, media, settings) are not part of the theme repository and are made on the live site.

## Deploying to another site

The theme folder holds the design, blocks, templates and patterns, but the site's *content* lives in the WordPress database and uploads folder. Two ways to move it:

**A. Fresh install (recommended for launch)**

1. Copy `wp-content/themes/bwfd` to the new site, or clone the repository. If you clone, run `npm install && npm run build` (Node 22) so `build/` exists; it is git-ignored. Alternatively copy the folder with `build/` included and skip Node on the server.
2. Activate the theme, then run `wp eval-file wp-content/themes/bwfd/bin/import-pages.php`. This creates the eight pages, imports the imagery into the Media Library, sets the front page, writes the primary menu, sets the site title, tagline, site icon and pretty permalinks. Without WP-CLI: create each page and insert its **BWFD pages** pattern, then set the front page under Settings → Reading and pick the menu in the header's Navigation block.
3. Check Settings → Reading → "Discourage search engines" is off, and Settings → General has the right site URL.

**B. Full site migration**

Any WordPress migration (Local's export, a migration plugin, or a database plus `wp-content/uploads` copy with a search-replace of the domain) brings the pages, menu, media, site icon and any edits made in the editor. Bring the theme folder with `build/` as in step 1.

**Server requirements**: PHP 8.0+, WordPress 6.8+ (built on 7.1), and an image library with WebP support (GD or Imagick) for WebP upload sub-sizes.

**Not in the theme**: page Excerpts written for meta descriptions, template or style changes made in the Site Editor (stored in the database), and the placeholder purchase/download URLs once you fill them in. All of these travel with a full migration, or need re-entering after a fresh install.

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
* JSON-LD: Organization, WebSite and WebPage on every page. On Home, About the book, Purchase and Churches & retail: the Book as a work with its paperback, eBook and audiobook editions (ISBN-13 each), the paperback doubling as a Google `Product` (co-typed `Product` + `Book`, `gtin13`, brand, and the direct-purchase Offer with AUD price, postage to Australia and availability that switches from PreOrder to InStock on the launch date), and the author Person. About the author is a `ProfilePage` whose `mainEntity` is the Person (portrait, bio, role, email, profile links). Other books carries Book entries for the earlier titles. Facts live in `bwfd_book_data()` and `bwfd_author_data()` and can be changed with the `bwfd_book_data` and `bwfd_author_data` filters. Endorsements are not emitted as reviews because Google requires a star rating on each review and the endorsements have none.
* Emoji script, generator tag, shortlink and RSD/WLW links removed; Facebook preconnect only on pages with the feed.
* Uploaded JPEG/PNG images get WebP sub-sizes (`image_editor_output_format`).
* Core provides the rest: title tag, canonical, robots, XML sitemap at `/wp-sitemap.xml`.

`inc/performance.php` targets what PageSpeed measures on the designed pages:

* **Hero preloads.** On any page that opens with a Hero block, the silhouette background (the Largest Contentful Paint element, otherwise only discoverable from an inline style) and the cover image are preloaded from `<head>`. The cover preload carries the same `imagesrcset`/`imagesizes` as the `<img>`, so the browser downloads one candidate once.
* **Phone-sized hero artwork.** When the hero's artwork is in the Media Library, the block's inline `background-image` is swapped for two custom properties and the stylesheet uses the smallest same-ratio sub-size of at least 640px (normally `medium_large`) below 640px, where the artwork sits under an almost opaque navy wash. The preloads carry matching `media` attributes so a phone fetches only the small file.
* **Lighter WebP.** Generated WebP sub-sizes use quality 75 rather than core's 86 (`wp_editor_set_quality`), roughly a third smaller at these display sizes. Run `wp media regenerate` on an existing site to re-encode earlier uploads.
* **Trimmed global styles.** Core still prints the default colour, gradient, font-size and spacing presets that theme.json switches off; `wp_theme_json_data_default` drops them since nothing in the theme uses them.
* **Font fallbacks without layout shift.** Metric-matched `Source Sans 3 Fallback` and `Bitter Fallback` faces (`size-adjust` and ascent/descent overrides against Arial and Times New Roman) sit after the web fonts in theme.json's font stacks, so the swap from system text to the web font does not move anything.
* **Textures only where painted.** The texture custom properties are declared on the selectors that use them rather than on `:root`: Chrome fetches `url()` values in custom properties as soon as they are computed, so `:root` cost every page four texture downloads whether or not it used them. Textures the page does paint are preloaded.
* **Responsive framed images.** Framed image blocks whose file is in the Media Library get `srcset`/`sizes` at render time (the block saves a plain `<img>`, so core's `wp-image-{id}` filter does not apply). Inside the hero the `sizes` mirror the hero's 220px phone and 260px tablet caps. A 440px sub-size (`bwfd-framed-sm`) is registered for high-density phones; after activating the theme on an existing site run `wp media regenerate --only-missing` so earlier uploads gain it.
* **Inlined CSS.** `style.css` is inlined with the block styles core already inlines (`styles_inline_size_limit` raised to 80 KB), removing the render-blocking stylesheet requests. The page HTML grows by roughly 6 KB compressed in exchange.
* **Compositor-friendly carousel.** The endorsements progress bar animates `transform: scaleX()` rather than `width`.
* **Cache headers.** The theme's own `.htaccess` gives fonts and imagery under `assets/` a one-year immutable cache lifetime. Uploads and core assets keep the server's default; set the browser cache TTL for those in the host or Cloudflare if wanted. Rename a theme image when it changes (and purge the CDN) rather than editing it in place.

Framed image blocks output `width`/`height` so the browser reserves space and can lazy-load below-the-fold images.

## Fonts

Bitter and Source Sans 3 are bundled as variable woff2 files (SIL Open Font License) and declared in `theme.json`, so no requests go to Google Fonts. The files are subset with `pyftsubset` to ASCII, Latin-1, Œ/œ, typographic punctuation (dashes, curly quotes, ellipsis, bullets), the euro, trade mark, minus and the fi/fl ligatures, which is about 15% smaller than Google's latin subset. Rename the files when replacing them: they are cached for a year.
