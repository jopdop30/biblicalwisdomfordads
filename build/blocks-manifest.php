<?php
// This file is generated. Do not modify it manually.
return array(
	'badge' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'bwfd/badge',
		'version' => '1.0.0',
		'title' => 'Badge',
		'category' => 'bwfd',
		'icon' => 'tag',
		'description' => 'Small uppercase status label, such as “Coming soon” or “URL TBA”.',
		'keywords' => array(
			'badge',
			'label',
			'status',
			'pill'
		),
		'textdomain' => 'bwfd',
		'attributes' => array(
			'content' => array(
				'type' => 'rich-text',
				'source' => 'rich-text',
				'selector' => 'span'
			)
		),
		'supports' => array(
			'html' => false,
			'color' => array(
				'text' => true,
				'background' => true,
				'link' => false
			),
			'typography' => array(
				'fontSize' => true
			)
		),
		'example' => array(
			'attributes' => array(
				'content' => 'Coming soon'
			)
		),
		'editorScript' => 'file:./index.js',
		'style' => 'file:./style-index.css'
	),
	'card' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'bwfd/card',
		'version' => '1.0.0',
		'title' => 'Card',
		'category' => 'bwfd',
		'icon' => 'index-card',
		'description' => 'A textured card with an accent top border. Holds a heading, text and a link, badge or button.',
		'keywords' => array(
			'card',
			'panel',
			'box'
		),
		'textdomain' => 'bwfd',
		'attributes' => array(
			'accent' => array(
				'type' => 'string',
				'default' => 'blue'
			),
			'surface' => array(
				'type' => 'string',
				'default' => 'marble'
			),
			'padding' => array(
				'type' => 'string',
				'default' => 'regular'
			),
			'minHeight' => array(
				'type' => 'string',
				'default' => ''
			),
			'lift' => array(
				'type' => 'boolean',
				'default' => false
			)
		),
		'supports' => array(
			'anchor' => true,
			'html' => false,
			'align' => false,
			'spacing' => array(
				'blockGap' => true
			)
		),
		'example' => array(
			'attributes' => array(
				'accent' => 'blue',
				'surface' => 'marble'
			),
			'innerBlocks' => array(
				array(
					'name' => 'core/heading',
					'attributes' => array(
						'level' => 3,
						'content' => 'Buy the book',
						'fontSize' => 'xl'
					)
				),
				array(
					'name' => 'core/paragraph',
					'attributes' => array(
						'content' => 'Available in retail stores, through Amazon, or as a direct purchase.',
						'fontSize' => 'base'
					)
				)
			)
		),
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css'
	),
	'card-grid' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'bwfd/card-grid',
		'version' => '1.0.0',
		'title' => 'Card grid',
		'category' => 'bwfd',
		'icon' => 'grid-view',
		'description' => 'Responsive grid of cards. Auto-fits columns to the available width, or fixes a column count.',
		'keywords' => array(
			'grid',
			'cards',
			'columns'
		),
		'textdomain' => 'bwfd',
		'attributes' => array(
			'columns' => array(
				'type' => 'number',
				'default' => 0
			),
			'minWidth' => array(
				'type' => 'number',
				'default' => 268
			),
			'gap' => array(
				'type' => 'number',
				'default' => 22
			),
			'centered' => array(
				'type' => 'boolean',
				'default' => false
			),
			'maxWidth' => array(
				'type' => 'string',
				'default' => ''
			)
		),
		'allowedBlocks' => array(
			'bwfd/card',
			'core/group',
			'core/image'
		),
		'supports' => array(
			'anchor' => true,
			'html' => false,
			'align' => array(
				'wide',
				'full'
			)
		),
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css'
	),
	'endorsement' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'bwfd/endorsement',
		'version' => '1.0.0',
		'title' => 'Endorsement',
		'category' => 'bwfd',
		'parent' => array(
			'bwfd/endorsements'
		),
		'icon' => 'testimonial',
		'description' => 'One endorsement: the quote and who said it.',
		'textdomain' => 'bwfd',
		'attributes' => array(
			'quote' => array(
				'type' => 'rich-text',
				'source' => 'rich-text',
				'selector' => '.bwfd-endorsement__quote'
			),
			'attribution' => array(
				'type' => 'rich-text',
				'source' => 'rich-text',
				'selector' => '.bwfd-endorsement__who'
			)
		),
		'supports' => array(
			'html' => false,
			'reusable' => false
		),
		'editorScript' => 'file:./index.js',
		'style' => 'file:./style-index.css'
	),
	'endorsements' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'bwfd/endorsements',
		'version' => '1.0.0',
		'title' => 'Endorsements carousel',
		'category' => 'bwfd',
		'icon' => 'format-quote',
		'description' => 'Rotating endorsement quotes on the navy texture, with previous/next, pause and a progress bar. Powered by the Interactivity API.',
		'keywords' => array(
			'endorsements',
			'testimonials',
			'quotes',
			'carousel',
			'slider'
		),
		'textdomain' => 'bwfd',
		'attributes' => array(
			'interval' => array(
				'type' => 'number',
				'default' => 8000
			),
			'autoplay' => array(
				'type' => 'boolean',
				'default' => true
			),
			'navigation' => array(
				'type' => 'string',
				'default' => 'dots'
			),
			'minHeight' => array(
				'type' => 'string',
				'default' => '300px'
			)
		),
		'allowedBlocks' => array(
			'bwfd/endorsement'
		),
		'supports' => array(
			'anchor' => true,
			'html' => false,
			'align' => array(
				'wide',
				'full'
			),
			'interactivity' => true
		),
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php',
		'viewScriptModule' => 'file:./view.js'
	),
	'facebook-page' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'bwfd/facebook-page',
		'version' => '1.1.0',
		'title' => 'Facebook page feed',
		'category' => 'bwfd',
		'icon' => 'facebook',
		'description' => 'Embeds a Facebook Page timeline using Facebook’s Page Plugin. Visitors load it with a click, so the page itself sends nothing to Facebook. Sized to its container (Facebook caps the width at 500px).',
		'keywords' => array(
			'facebook',
			'feed',
			'social',
			'embed'
		),
		'textdomain' => 'bwfd',
		'attributes' => array(
			'url' => array(
				'type' => 'string',
				'default' => 'https://www.facebook.com/biblicalwisdomfordads'
			),
			'height' => array(
				'type' => 'number',
				'default' => 600
			),
			'tabs' => array(
				'type' => 'string',
				'default' => 'timeline'
			),
			'smallHeader' => array(
				'type' => 'boolean',
				'default' => false
			),
			'hideCover' => array(
				'type' => 'boolean',
				'default' => false
			),
			'showFacepile' => array(
				'type' => 'boolean',
				'default' => true
			),
			'title' => array(
				'type' => 'string',
				'default' => 'Biblical Wisdom for Dads on Facebook'
			)
		),
		'supports' => array(
			'anchor' => true,
			'html' => false,
			'align' => array(
				'center',
				'wide'
			)
		),
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php',
		'viewScriptModule' => 'file:./view.js'
	),
	'framed-image' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'bwfd/framed-image',
		'version' => '1.0.0',
		'title' => 'Framed image',
		'category' => 'bwfd',
		'icon' => 'format-image',
		'description' => 'Book cover or portrait with a soft shadow and an optional apricot panel behind it.',
		'keywords' => array(
			'image',
			'cover',
			'portrait',
			'book'
		),
		'textdomain' => 'bwfd',
		'attributes' => array(
			'id' => array(
				'type' => 'number'
			),
			'url' => array(
				'type' => 'string',
				'source' => 'attribute',
				'selector' => 'img',
				'attribute' => 'src'
			),
			'alt' => array(
				'type' => 'string',
				'source' => 'attribute',
				'selector' => 'img',
				'attribute' => 'alt',
				'default' => ''
			),
			'width' => array(
				'type' => 'number'
			),
			'height' => array(
				'type' => 'number'
			),
			'panel' => array(
				'type' => 'boolean',
				'default' => false
			),
			'shadow' => array(
				'type' => 'string',
				'default' => 'cover'
			),
			'maxWidth' => array(
				'type' => 'string',
				'default' => '360px'
			),
			'aspectRatio' => array(
				'type' => 'string',
				'default' => ''
			),
			'align' => array(
				'type' => 'string',
				'default' => ''
			)
		),
		'supports' => array(
			'anchor' => true,
			'html' => false,
			'align' => array(
				'left',
				'center',
				'right'
			)
		),
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css'
	),
	'hero' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'bwfd/hero',
		'version' => '1.0.0',
		'title' => 'Hero',
		'category' => 'bwfd',
		'icon' => 'cover-image',
		'description' => 'Full-width hero on the navy silhouette artwork with a choice of colour washes. Holds the cover image and headline.',
		'keywords' => array(
			'hero',
			'banner',
			'cover'
		),
		'textdomain' => 'bwfd',
		'attributes' => array(
			'align' => array(
				'type' => 'string',
				'default' => 'full'
			),
			'wash' => array(
				'type' => 'string',
				'default' => 'radial'
			),
			'reverse' => array(
				'type' => 'boolean',
				'default' => false
			),
			'backgroundId' => array(
				'type' => 'number'
			),
			'backgroundUrl' => array(
				'type' => 'string',
				'default' => ''
			)
		),
		'supports' => array(
			'anchor' => true,
			'html' => false,
			'align' => array(
				'full'
			)
		),
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css'
	),
	'icon' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'bwfd/icon',
		'version' => '1.0.0',
		'title' => 'Icon',
		'category' => 'bwfd',
		'icon' => 'star-empty',
		'description' => 'One of the theme\'s line icons, plain or in an apricot circle.',
		'keywords' => array(
			'icon',
			'symbol',
			'svg'
		),
		'textdomain' => 'bwfd',
		'attributes' => array(
			'icon' => array(
				'type' => 'string',
				'default' => 'compass'
			),
			'variant' => array(
				'type' => 'string',
				'default' => 'plain'
			),
			'size' => array(
				'type' => 'number',
				'default' => 34
			)
		),
		'supports' => array(
			'html' => false,
			'color' => array(
				'text' => true,
				'background' => false,
				'link' => false
			)
		),
		'example' => array(
			'attributes' => array(
				'icon' => 'book',
				'variant' => 'circle'
			)
		),
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php'
	),
	'reveal' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'bwfd/reveal',
		'version' => '1.0.0',
		'title' => 'Reveal panel',
		'category' => 'bwfd',
		'icon' => 'arrow-down-alt2',
		'description' => 'A text link that reveals extra content, such as key book information. Powered by the Interactivity API.',
		'keywords' => array(
			'toggle',
			'details',
			'reveal',
			'expand',
			'accordion'
		),
		'textdomain' => 'bwfd',
		'attributes' => array(
			'openLabel' => array(
				'type' => 'string',
				'default' => 'Key information'
			),
			'closeLabel' => array(
				'type' => 'string',
				'default' => 'Hide key information'
			),
			'defaultOpen' => array(
				'type' => 'boolean',
				'default' => false
			)
		),
		'supports' => array(
			'anchor' => true,
			'html' => false,
			'interactivity' => true
		),
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php',
		'viewScriptModule' => 'file:./view.js'
	),
	'section-heading' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'bwfd/section-heading',
		'version' => '1.0.0',
		'title' => 'Section heading',
		'category' => 'bwfd',
		'icon' => 'heading',
		'description' => 'A heading with the apricot rule beneath it. Used for page titles and section titles.',
		'keywords' => array(
			'heading',
			'title',
			'rule'
		),
		'textdomain' => 'bwfd',
		'attributes' => array(
			'content' => array(
				'type' => 'rich-text',
				'source' => 'rich-text',
				'selector' => '.bwfd-section-heading__title'
			),
			'level' => array(
				'type' => 'number',
				'default' => 2
			),
			'size' => array(
				'type' => 'string',
				'default' => 'xxl'
			),
			'showRule' => array(
				'type' => 'boolean',
				'default' => true
			),
			'textAlign' => array(
				'type' => 'string',
				'default' => 'left'
			)
		),
		'supports' => array(
			'anchor' => true,
			'html' => false,
			'color' => array(
				'text' => true,
				'background' => false,
				'link' => false
			),
			'spacing' => array(
				'margin' => true
			)
		),
		'example' => array(
			'attributes' => array(
				'content' => 'About the book',
				'level' => 1,
				'size' => 'display'
			)
		),
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css'
	)
);
