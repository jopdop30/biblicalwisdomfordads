# Biblical Wisdom for Dads – WordPress block theme

Block theme (full site editing) for [biblicalwisdomfordads.au](https://www.biblicalwisdomfordads.au), built from the "Biblical Wisdom for Dads v3" Claude Design file. Requires WordPress 6.7+ (developed against 7.1) and PHP 8.0+.

## What is in the theme

| Path | Purpose |
| --- | --- |
| `theme.json` | Design tokens: palette, gradients, shadows, bundled fonts (Bitter, Source Sans 3), fluid font sizes, spacing scale, global element styles, template parts and custom templates. |
| `style.css` | Texture surfaces, button/table/group block styles, header and footer, layout helpers. Loaded on the front end and in the editor. |
| `templates/` | `page` (default, no title – the designed pages carry their own heading), `page-with-title`, `page-plain` (white sheet), `single`, `index`, `search`, `404`, the News and Insights views (`single-bwfd_news`, `archive-bwfd_news`, `single-bwfd_insight`, `archive-bwfd_insight`, `taxonomy-bwfd_topic`) and the chapter pages (`single-bwfd_chapter`, `archive-bwfd_chapter`). |
| `parts/` | `header` and `footer`, each rendering a PHP pattern so links can use `home_url()`. |
| `patterns/` | Ten complete page patterns (category **BWFD pages**) and reusable sections (category **BWFD sections**): navy call-to-action band, endorsement sets, key information reveals, bulk order cards, the latest-news list, the home page's insight strip and the post byline. |
| `src/blocks/` | Custom block sources (see below). Built to `build/` with `@wordpress/scripts`. |
| `src/admin/` | The Settings → Structured data app, the editor’s “Search appearance” panel, the “Before you publish” checklist for News and Insights, and the status-panel fields for the two types (the Insights “Feature on the Insights page” toggle and the News “Dateline” city) (`@wordpress/components` + `@wordpress/core-data`). Built to `build/admin/`. |
| `inc/` | Block registration, block style variations, pattern categories, the News and Insights post types, Topics, Chapters, Scripture and book-image helpers, SVG icon library, SEO output, performance trims, and the Structured data settings page. |
| `bin/import-chapters.php` | WP-CLI script that creates or updates the chapter pages from a text list (`22. Compassion` or `22 \| Compassion \| summary` per line), matched by chapter number. |
| `bin/make-social-images.php` | Renders the default share images (`social-default.jpg` and the `article-*.jpg` aspect-ratio set) from the cover and the navy texture with GD. Plain PHP, no WordPress needed. |
| `bin/import-pages.php` | WP-CLI script that creates/updates the ten pages from the patterns, imports the images into the Media Library, sets the front page, the privacy policy page and writes the primary navigation. |
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
| Scripture | A Bible verse with its reference: apricot rule and quote-mark disc, serif italic verse, uppercase reference linked to the passage on Bible Gateway (translation optional; link can be turned off; filter `bwfd_scripture_link`). Server-rendered; the first Scripture block in an Insight also feeds the Featured insight block and the article's `citation` in the structured data. |
| Book card | Cover, title, one line and a buy button drawn from Settings → Structured data, so the price and cover stay current. Blue or apricot accent, optional price on the button, secondary link, sticky beside an article. The News item and Insight templates place one next to the body. |
| Chapter number | “Chapter 22”, “22” or “Chapter 22 of 40” from a chapter's Order field, for the chapter templates and lists. |
| Book band | The full-width navy closing band: cover, heading, one line and the book's buttons. The words, links and cover size are block settings (editable in the Site Editor template); `{book}`, `{chapters}`, `{price}` and `{author}` are filled in from Settings → Structured data, so the price stays current. Closes news items, insights, topic pages and chapter pages. |
| Featured insight | The Insight flagged **Feature on the Insights page**, else the most recent (or a chosen ID), as a two-panel feature: its Scripture verse on apricot beside date, title, excerpt and author on navy. Falls back to the featured image, then to the navy panel alone. Renders nothing on page two onwards. |

Block styles registered for core blocks: Group (Navy texture, Apricot texture, Marble), Button (Apricot, Text link, Large call to action, Compact, Social), List (Numbered reasons: the apricot counter discs from the Launch page), Table (Key information, Pricing, Event details).

## News and Insights

Two post types for ongoing writing, registered in `inc/content-types.php`, so the site has fresh, indexable content beyond the ten designed pages:

| | News | Insights |
| --- | --- | --- |
| For | Announcements and press releases | Short reflections from Stephen |
| Admin menu | **News** | **Insights** |
| URLs | `/news/` and `/news/{slug}/` | `/insights/` and `/insights/{slug}/` |
| Templates | `archive-bwfd_news`: navy "Press room" band, then ruled rows of date, title, excerpt and optional 16:9 image. `single-bwfd_news`: "← All news" back link, "News · date" kicker, headline, optional image, the body beside a sticky Book card (blue accent, release-aware line, *Launch event* link). | `archive-bwfd_insight`: kicker and heading, the Featured insight block (latest item with its verse), then a card grid of the rest. `single-bwfd_insight`: "← All insights", kicker, headline, portrait byline, optional image, the body beside a sticky Book card (apricot accent, *Buy the book · $price*). |
| Closing band | Book band block: cover, the book's title, one line, *Learn more about the book* | Book band block: cover, "Want more like this?", *Buy the book · $price* and an outline *About the book* (the price comes from Settings → Structured data) |
| Structured data | `NewsArticle` | `BlogPosting`, with the first Scripture block as `citation` |

They are separate post types rather than categories of the core post so each has a clean archive URL, its own templates with the right call to action, its own admin menu and its own Article subtype. Neither type is in the header menu yet; both are linked from the footer.

The built-in **Posts** type is retired (`bwfd_retire_posts_args()` and the functions after it in `inc/content-types.php`): WordPress cannot unregister a built-in type, so it is made non-public along with categories and tags. That removes the Posts menu, the "New post" entries, the Quick Draft and Activity dashboard widgets, post and category URLs (they 404), and the post and category sitemaps; the site feed at `/feed/` lists News and Insights. Comments are switched off the same way (`bwfd_disable_comments()` and the functions after it): no post type supports them, nothing is open, and the Comments menu, admin bar bubble, dashboard widget, Discussion settings and pingback header are gone.

Each item supports a title, content, **Excerpt** (the meta description and the archive teaser), featured image and revisions, and opens in the editor inside its template so the call to action is visible while writing. The **Search appearance** panel is available on both. Feeds exist at `/news/feed/` and `/insights/feed/`, and both types are in the core sitemap.

The Insights archive shows one item in the Featured insight block and leaves it out of the list beneath (`bwfd_insights_archive_query()`), so it appears once; with a single item published the list is empty and a "more reflections are on the way" card shows. Which item is featured is chosen with the **Feature on the Insights page** toggle in the editor's status panel (the spot where core posts have "Stick to the top of the blog"). Core's sticky flag only exists for the built-in post type, so this is the theme's own: post meta `bwfd_featured`, registered in `inc/content-types.php`, with an editor toggle in `src/admin/editor-panels.js`. One Insight is featured at a time (flagging another clears the previous one), the list table shows "Featured" beside it, and with none flagged the most recent Insight is featured (`bwfd_featured_insight()`). Start each Insight with a **Scripture** block so the feature has a verse to quote. The archive's `CollectionPage` list includes the featured item.

News items open press-release style: the single view prepends **“Brisbane, 19 September 2026.”** in bold to the first paragraph, from the post date and the publisher city under Settings → Structured data → Publisher (`bwfd_news_dateline_render()`), and the `NewsArticle` carries it as `dateline`. A news item can set another city, or a dash to leave it out, in the **Dateline** field of the editor's status panel (meta `bwfd_dateline`). Nothing is added when the paragraph already opens with the city.

Each single view ends with a **More news** / **More insights** section: the three most recent other items (the Query Loop's *Exclude current* setting leaves out the item being read; the section hides itself when there is nothing else). The archives' title tags and search descriptions, the chapters index's, and the patterns for topic pages (`{topic}`, `{author}`, `{book}`) are edited under **Settings → Structured data → News and Insights archives** (`archives` in the `bwfd_schema` option; blank falls back to the section name and its standard description).

**Every word is editable on the site.** Template text (kickers, intros, back links, empty-state notes, section headings) is block content in the Site Editor; the bands are Book band blocks with their words as settings; the byline, book card and featured panel read names, prices and the cover from Settings → Structured data; the checklist and admin labels are the only strings that live in code. The footer is still the theme's pattern, as it was before this work: change its links in `patterns/footer.php`, or detach the pattern in the Site Editor to edit it there.

The **Latest news** section pattern (`bwfd/news-latest`, category BWFD sections) shows the three most recent news items as a ruled list of date and title with a link to `/news/`. The **From the Insights** pattern (`bwfd/insight-latest`) puts the Featured insight block with a heading and an *All insights* link on the home page, above the social media section, so the newest reflection is linked from the site's strongest page. On a site whose home page predates this release, insert the pattern in the editor (or re-run `import-pages.php home`, which replaces the page content from the pattern). It is part of the Churches & retail page pattern, under the media kit. On a site whose Churches & retail page was created before this release, insert the pattern on that page in the editor (or re-run `import-pages.php churches-and-retail`, which replaces the page content from the pattern).

### Topics

Insights are categorised with **Topics** (`bwfd_topic`, `inc/topics.php`): a checklist in the editor, an admin column, and public archives at `/insights/topic/{slug}/` rendered by `taxonomy-bwfd_topic.html` (topic name, description, the topic pills, the card grid, pagination, the buy band). Each insight shows its topics as the kicker above its headline (“DISCIPLINE · COMPASSION”, linked to the topic pages), and the Insights page and every topic page carry a pill row of all topics (a Tag Cloud block styled as pills). Topic pages get a canonical link, a title and description from the patterns under Settings → Structured data → Archives (the term's own description wins when it has one), a `CollectionPage` with breadcrumbs and an item list, and a place in the sitemap; empty ones are `noindex`. Topic names go into each `BlogPosting`'s `keywords`. Add topics under Insights → Topics; give each a description, since it becomes the page's intro and search description.

### Chapters

The book's chapters live as their own post type (`bwfd_chapter`, `inc/chapters.php`, menu **Chapters**): the chapter number in the **Order** field, the title, a block-editor summary (the template opens with a Scripture block for the chapter's key verse) and an optional excerpt (the teaser on the index). Slugs carry the number, so a chapter lives at `/chapters/22-compassion/`, and `/chapters/` lists all of them in order (`archive-bwfd_chapter.html`). A chapter page (`single-bwfd_chapter.html`) shows “Chapter 22 of 40”, the summary beside a Book card, **Insights on this chapter**, and a Book band reading “Read the whole chapter”. Its title tag is “Chapter 22: Compassion – Site”, and its structured data is a `Chapter` (position, `isPartOf` the Book, the key verse as `citation`) with breadcrumbs, so searches for “Biblical Wisdom for Dads chapter 22” have a page to land on. The chapter count used in copy is **Settings → Structured data → Book → Chapters**.

Insights link to chapters through a shadow taxonomy of the same name: saving a chapter creates or updates one term for it (name “Chapter 22: Compassion”, ordered by number in the editor's **Chapters** checklist), deleting the chapter removes it, and the terms cannot be edited by hand. An insight's chapters appear as “In the book: Chapter 22: Compassion” above its footer row (term links go to the chapter pages), are listed as `mentions` in its structured data, and the chapter page queries its insights through the term (`bwfd_chapter_insights_query()`; the Query Loop's post-template carries the `bwfd-chapter-insights` class). Load the table of contents with `bin/import-chapters.php`; the footer links to *Chapter by chapter*.

### Before you publish

News items and Insights carry a **Before you publish** panel in the editor's document sidebar (`src/admin/seo-checklist.js`). It scores the item live against the editorial checks and gives every unfinished line a button that opens the right panel or selects the right block; the unfinished lines repeat in the publish confirmation. Checks marked *needed* are the ones that matter most; the rest are recommendations.

| Check | Why | The button |
| --- | --- | --- |
| Headline 20 to 70 characters | Long enough to say something, short enough for results | Selects the title |
| Excerpt, one or two full sentences, 70 to 158 characters (*needed*) | Becomes the search description, the archive teaser and the featured panel text | Opens the Excerpt panel and focuses it |
| At least 150 words (news) or 300 (insights) | Very short pages rarely rank | Selects the first content block |
| Featured image, landscape | The picture shown when the link is shared; without one, shares fall back to the portrait cover | Opens the Featured image panel |
| Featured image alt text | Accessibility and image search | Opens the Featured image panel |
| Insights: opens with a Scripture block with verse and reference (*needed*) | Feeds the featured panel and the article's `citation` | Selects the block, or inserts one at the top |
| Insights: verse reference in the headline, excerpt or search title | People search for the reference | Opens the Search appearance panel |
| Insights: at least one topic (*needed*) | Gives the piece a topic page and a theme for search | Opens the Topics panel |
| Insights: linked to the chapter(s) it draws on | Lists it on the chapter page, names the chapter under the piece | Opens the Chapters panel |
| Search-result title fits in 60 characters | Otherwise Google cuts it | Opens the Search appearance panel |
| Links to another page on the site | Crawl paths and a next step for readers | Selects the first content block |
| Subheadings once the piece passes 400 words | Scannable | Selects the first content block |

Thresholds and which types get the panel are set in `bwfd_seo_checklist_rules()` in `inc/seo.php` (filter `bwfd_seo_checklist_rules`).

### Before anything is published

Every listing is built to sit empty. The News, Insights and Chapters archives and any topic page show a short note ("No news has been published yet", "More reflections are on the way", "The chapter guide is on its way") and are `noindex, follow` until they have an item; the Churches & retail news list shows its own note; the home page's *From the Insights* strip and the topic pill rows hide themselves until there is something to show; the "More news" / "More insights" sections under an item hide when there is nothing else. The sitemap index and the site feed answer normally with nothing in them. Chapters left in draft keep their entry in the Insights **Chapters** checklist; an insight linked to a draft chapter names it without a link until the chapter is published.

Rewrite rules for the two archives are flushed once per rule-set version (`bwfd_content_types_flush_rewrites()`, option `bwfd_rewrite_version`) on the first request after a deploy, so no visit to Settings → Permalinks is needed. Bump the version string in that function whenever the slugs change.

## Development

```bash
cd wp-content/themes/bwfd
nvm use 22
npm install
npm run build      # blocks and the settings app (build/ and build/admin/)
npm run start      # watch mode for blocks
npm run start:admin  # watch mode for the settings app
```

`build:blocks` empties `build/` first, which removes `build/admin/`; `npm run build` runs both steps in the right order. After a `start` session, run `npm run build:admin` again.

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

Pass page slugs to update only those pages (`... import-pages.php home`). The script is idempotent. A partial run leaves the menu as editors have it but adds any missing item for the pages it imported, in the designed position: `... import-pages.php launch` creates the Launch event page and puts “Launch event” after “About the book” in the primary menu.

WordPress caches the list of pattern files in a site transient unless `WP_DEBUG` is on, so after adding a file under `patterns/` on a site without debug mode run `wp eval 'wp_get_theme()->delete_pattern_cache();'` before importing, otherwise the import warns that the pattern is not registered. It creates or updates Home, About the book, About the author, Purchase, Small Group Guide, Other books, Enjoyed?, Churches & retail, Launch event and Privacy policy from the page patterns (nested patterns inlined so every page is literal, editable content), imports the design imagery into the Media Library, sets Home as the static front page, writes the primary navigation menu and drafts the default Sample Page.

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
2. Activate the theme, then run `wp eval-file wp-content/themes/bwfd/bin/import-pages.php`. This creates the ten pages, imports the imagery into the Media Library, sets the front page, writes the primary menu, sets the site title, tagline, site icon and pretty permalinks. Without WP-CLI: create each page and insert its **BWFD pages** pattern, then set the front page under Settings → Reading and pick the menu in the header's Navigation block.
3. Check Settings → Reading → "Discourage search engines" is off, and Settings → General has the right site URL.

**B. Full site migration**

Any WordPress migration (Local's export, a migration plugin, or a database plus `wp-content/uploads` copy with a search-replace of the domain) brings the pages, menu, media, site icon and any edits made in the editor. Bring the theme folder with `build/` as in step 1.

**Server requirements**: PHP 8.0+, WordPress 6.8+ (built on 7.1), and an image library with WebP support (GD or Imagick) for WebP upload sub-sizes.

**Not in the theme**: page Excerpts written for meta descriptions, template or style changes made in the Site Editor (stored in the database), values saved under Settings → Structured data, and the placeholder purchase/download URLs once you fill them in. All of these travel with a full migration, or need re-entering after a fresh install.

## Editing

* Every page is ordinary block content: open it in the editor and change text, links, images or cards directly.
* To start a new page from a design layout, add a page and pick a layout from the **BWFD pages** patterns in the "Choose a pattern" modal, or insert a **BWFD sections** pattern anywhere.
* Colours, fonts, spacing and button styles are managed in **Appearance → Editor → Styles** via `theme.json`.
* Header and footer are template parts; the menu is a normal Navigation block menu ("Primary navigation"). The "Buy the book" item is styled as a button through the `bwfd-nav-button` CSS class on that link.
* The Facebook feed card on the home page embeds the page timeline; change the page URL or tabs in the block sidebar.
* Purchase and media-kit links marked "URL TBA" / `#` are placeholders awaiting final URLs.
* The Launch event page (`patterns/page-launch.php`, `/launch/`) is built from core blocks with theme classes: the numbered reasons are an ordered List block with the `bwfd-numbered-list` class, the “on the night” pills are paragraphs with the `bwfd-pill` class inside a flex Group, the date card is a Group with the `bwfd-event-card` class, and the details use the Table block's *Event details* style. The TryBooking and Facebook links appear in the pattern as ordinary link URLs. The importer sets the book cover as the page's featured image so social shares show the cover rather than the wordmark.
* The Privacy policy page (`patterns/page-privacy-policy.php`, template *Page with title*, linked from the footer) describes what the site does today: email contact, Square checkout, server and Cloudflare logs, the Facebook page embed, Cloudflare Web Analytics (the beacon is injected by Cloudflare at the edge for browser user agents, so plain `curl` does not show it) and the Search Console connection through Site Kit. Update it and its "Last updated" date when a form, newsletter, comments or another third-party service is added.

## SEO and performance

`inc/seo.php` adds what core leaves out, without touching content:

* `<meta name="description">` from the page **Excerpt** (pages gain an Excerpt panel), falling back to the first substantial paragraphs of the page, then the tagline.
* Open Graph and Twitter card tags with image alt text; the share image is the featured image, else the first image in the content, else the landscape default `assets/images/social-default.jpg` (the cover on the navy texture at 1200×630, rendered by `bin/make-social-images.php`, which also writes the 16:9, 4:3 and 1:1 `article-*.jpg` set the Article structured data offers when an item has no featured image; re-run it after changing the cover). `article:author` points at the About the author page. The News and Insights archives, which core leaves without one, get a canonical link.
* JSON-LD: Organization, WebSite and WebPage on every page. News items, Insights and their archives add a `BreadcrumbList` (Home › News › Title) referenced from the page node. News items and Insights add a `NewsArticle` or `BlogPosting` (headline, description, image, dates, word count, `articleSection`, the author Person, the publisher Organization and the Book as its subject) with the WebPage's `mainEntity` pointing at it; their archives are a `CollectionPage` whose `mainEntity` is an `ItemList` of the items on that page. On the book pages (About the book, Purchase and any others chosen in the settings, by default Home and Churches & retail): the Book as a work with its paperback, eBook and audiobook editions (ISBN-13 each), the paperback doubling as a Google `Product` (co-typed `Product` + `Book`, `gtin13`, brand, and the direct-purchase Offer with price, postage and availability that switches from PreOrder to InStock on the release date), the author Person, and a `Quotation` per Endorsement block on the page (quote, speaker with role, `about` the Book; not reviews, because Google requires a star rating on each review and the endorsements have none). About the author is a `ProfilePage` whose `mainEntity` is the Person. Other books carries Book entries for the earlier titles. The Launch event page (matched by the slug `launch`; filter `bwfd_launch_event` to change the facts or switch it off) carries an `Event` with the venue, times, free-ticket Offer and the author as organiser and performer. Empty fields are dropped from the output.
* Emoji script, generator tag, shortlink and RSD/WLW links removed; Facebook preconnect only on pages with the feed.
* Uploaded JPEG/PNG images get WebP sub-sizes (`image_editor_output_format`).
* Core provides the rest: title tag, canonical, robots, XML sitemap at `/wp-sitemap.xml`.

### Structured data settings

Every fact in the JSON-LD (book, editions with their store links, offer, shipping and returns, publisher, author, other books, and which pages carry what) is edited under **Settings → Structured data**. The return policy (window, who pays postage, refund type, free returns for faulty items, link to the policy page) is emitted on the Offer and, as the site's standard policy, on the publisher Organization. Fields for facts not yet known (store links such as Amazon, eBook and audiobook release dates, narrator and running time, price validity, handling and transit times) sit blank and are left out of the output until filled. The page is a small React app built with `@wordpress/components`, so it looks and saves like the rest of the admin: fields are grouped into cards that mirror the graph, images can be chosen from the Media Library, and a preview panel shows the JSON-LD any page currently sends, with a link to Google's Rich Results Test. Endorsements are read from the Endorsement blocks on each page and need no entry. The preview's page list also offers the News and Insights archives and their three most recent items.

The data is one option, `bwfd_schema`, exposed on the REST settings endpoint with a full JSON schema (`inc/schema-settings.php`). Defaults live in `bwfd_schema_defaults()` (filter `bwfd_schema_defaults`) and are merged under the saved values, so a field added in a later release starts with a sensible value and **Reset to defaults** in the page recovers the shipped facts. `bwfd_schema_data()` returns the merged data (filter `bwfd_schema_data` for request-time changes). Page choices default to the pages the provisioning script creates, matched by slug. The option travels with a database migration; on a fresh install the defaults apply until someone saves the page.

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

## Analytics events

Site Kit prints the Google tag. `inc/analytics.php` adds `assets/js/analytics-events.js` (plain JavaScript, no build step), which sends the events below through the global `gtag()` and does nothing when that function is absent. Events are worked out from the markup the editor produces, so a new Square link, retailer or social button needs no code: the purchase option and price are read from the card heading the button sits in, the retailer and network from the link's domain.

| Event | Fires on | Own parameters |
| --- | --- | --- |
| `begin_checkout` | A `square.link` or `squareup.com` link | `purchase_option` (`direct_delivery`, `local_pickup`, `bulk_20_plus`), `currency`, `value`, `items` |
| `retailer_click` | Amazon, Koorong, Booktopia, Running Forever Press | `retailer`, `book_title` |
| `contact_click` | A `mailto:` or `tel:` link (including Cloudflare's protected form) | |
| `rsvp_click` | A TryBooking link | |
| `social_click` | Facebook, Instagram, LinkedIn, YouTube, Goodreads | `social_network` |
| `cta_click` | An internal link styled as a button (`wp-element-button`) | `link_url` |
| `panel_open` | A Reveal panel opened | `panel_label` |
| `content_read` | The end of a chapter, insight or news article scrolls into view | `content_type`, `chapter_number` |

Every link event also carries `link_text`, `link_context` (the nearest card or section heading, or `Header`/`Footer`) and `link_location` (`hero`, `card`, `book_card`, `book_band`, `article`, `header`, `footer`, `content`). Register the parameters you want to report on as event-scoped custom dimensions in Analytics (Admin → Custom definitions), and mark `begin_checkout`, `contact_click`, `rsvp_click` and `retailer_click` as key events. Outbound clicks in general, file downloads, scroll depth and site search come from GA4 enhanced measurement, not from this script. Filter `bwfd_analytics_events` to `false` to leave the script out.

## Fonts

Bitter and Source Sans 3 are bundled as variable woff2 files (SIL Open Font License) and declared in `theme.json`, so no requests go to Google Fonts. The files are subset with `pyftsubset` to ASCII, Latin-1, Œ/œ, typographic punctuation (dashes, curly quotes, ellipsis, bullets), the euro, trade mark, minus and the fi/fl ligatures, which is about 15% smaller than Google's latin subset. Rename the files when replacing them: they are cached for a year.

## Search engines

`inc/seo.php` handles everything an SEO plugin would, without one:

- **Title and description.** The `<title>` is WordPress’s default (page title – site name) unless the page has a *Search title* in the editor’s **Search appearance** panel, which then replaces the whole tag. The meta description is the page **Excerpt**; without one, the first substantial paragraphs are used and cut at a sentence end where possible.
- **Open Graph, Twitter cards, JSON-LD.** Facts come from Settings → Structured data. The book cover defaults to the media-library copy (slug `bwfd-cover`) when one exists.
- **Crawl controls.** Sitemap requests always answer 200 (core returns 404 while there are no posts). The users sitemap is off and author archives redirect to the homepage. Search results, date archives, attachment pages, not-found pages and empty term or post type archives are `noindex, follow`.
- **Facebook feed.** The Facebook page block reserves the embed’s height and inserts the Page Plugin iframe only when the block comes within a screen of the viewport, so the homepage does not download the embed on load. Facebook draws the plugin at the `width` in the iframe URL (180–500px) regardless of the iframe’s size, so the script measures the block and puts that width in the URL, and reloads the plugin after a resize that changes the width by 24px or more. A `<noscript>` copy (at 500px) keeps it working without JavaScript.
- **Headers.** Front-end responses carry `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy` and `X-Frame-Options`. Anonymous, cookie-less page requests also send `Cache-Control: public, s-maxage=600`, which Cloudflare honours once a cache rule makes HTML eligible (see `inc/performance.php`).

