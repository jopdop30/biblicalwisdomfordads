<?php
/**
 * Render the default share images: the book cover on the navy texture,
 * with the brand stripe along the foot. Written to assets/images/ as JPEG
 * (social scrapers are unreliable with WebP).
 *
 *   php wp-content/themes/bwfd/bin/make-social-images.php
 *
 * Outputs social-default.jpg (1200×630, Open Graph / Twitter) and the three
 * aspect ratios Google's Article guidance recommends: article-16x9.jpg,
 * article-4x3.jpg and article-1x1.jpg. Re-run after replacing the cover or
 * the texture, then rename or bump references (the assets are cached for a
 * year).
 *
 * @package bwfd
 */

$bwfd_dir = dirname( __DIR__ ) . '/assets/images/';
$bwfd_out = array(
	'social-default.jpg' => array( 1200, 630 ),
	'article-16x9.jpg'   => array( 1200, 675 ),
	'article-4x3.jpg'    => array( 1200, 900 ),
	'article-1x1.jpg'    => array( 1200, 1200 ),
);

$bwfd_texture = imagecreatefromwebp( $bwfd_dir . 'tex-navy.webp' );
$bwfd_cover   = imagecreatefromwebp( $bwfd_dir . 'bwfd-cover.webp' );
if ( ! $bwfd_texture || ! $bwfd_cover ) {
	fwrite( STDERR, "Could not read the texture or cover.\n" );
	exit( 1 );
}

foreach ( $bwfd_out as $bwfd_file => list( $bwfd_w, $bwfd_h ) ) {
	$bwfd_img = imagecreatetruecolor( $bwfd_w, $bwfd_h );
	imagealphablending( $bwfd_img, true );

	// Texture, scaled to cover the canvas and centred.
	$bwfd_tw = imagesx( $bwfd_texture );
	$bwfd_th = imagesy( $bwfd_texture );
	$bwfd_scale = max( $bwfd_w / $bwfd_tw, $bwfd_h / $bwfd_th );
	$bwfd_sw = (int) round( $bwfd_w / $bwfd_scale );
	$bwfd_sh = (int) round( $bwfd_h / $bwfd_scale );
	imagecopyresampled( $bwfd_img, $bwfd_texture, 0, 0, (int) ( ( $bwfd_tw - $bwfd_sw ) / 2 ), (int) ( ( $bwfd_th - $bwfd_sh ) / 2 ), $bwfd_w, $bwfd_h, $bwfd_sw, $bwfd_sh );

	// Cover at 76% of the height, centred, over a stepped shadow.
	$bwfd_ch = (int) round( $bwfd_h * 0.76 );
	$bwfd_cw = (int) round( $bwfd_ch * imagesx( $bwfd_cover ) / imagesy( $bwfd_cover ) );
	$bwfd_cx = (int) round( ( $bwfd_w - $bwfd_cw ) / 2 );
	$bwfd_cy = (int) round( ( $bwfd_h - $bwfd_ch ) / 2 ) - (int) round( $bwfd_h * 0.01 );
	for ( $bwfd_i = 18; $bwfd_i >= 1; $bwfd_i-- ) {
		$bwfd_alpha  = (int) ( 127 - ( 22 * ( 1 - $bwfd_i / 18 ) ) - 6 );
		$bwfd_shadow = imagecolorallocatealpha( $bwfd_img, 0, 12, 24, max( 0, min( 127, $bwfd_alpha ) ) );
		imagefilledrectangle( $bwfd_img, $bwfd_cx - $bwfd_i, $bwfd_cy + 14 - $bwfd_i + 12, $bwfd_cx + $bwfd_cw + $bwfd_i, $bwfd_cy + $bwfd_ch + 14 + $bwfd_i, $bwfd_shadow );
	}
	imagecopyresampled( $bwfd_img, $bwfd_cover, $bwfd_cx, $bwfd_cy, 0, 0, $bwfd_cw, $bwfd_ch, imagesx( $bwfd_cover ), imagesy( $bwfd_cover ) );

	// Brand stripe along the foot: apricot, blue, gold.
	$bwfd_stripe = max( 8, (int) round( $bwfd_h * 0.013 ) );
	$bwfd_third  = (int) ( $bwfd_w / 3 );
	$bwfd_colours = array(
		imagecolorallocate( $bwfd_img, 0xF8, 0xBB, 0x7C ),
		imagecolorallocate( $bwfd_img, 0x3B, 0x89, 0xBA ),
		imagecolorallocate( $bwfd_img, 0xFF, 0xD3, 0x88 ),
	);
	foreach ( $bwfd_colours as $bwfd_n => $bwfd_colour ) {
		imagefilledrectangle( $bwfd_img, $bwfd_n * $bwfd_third, $bwfd_h - $bwfd_stripe, ( $bwfd_n + 1 ) * $bwfd_third + 1, $bwfd_h, $bwfd_colour );
	}

	imagejpeg( $bwfd_img, $bwfd_dir . $bwfd_file, 86 );
	imagedestroy( $bwfd_img );
	echo $bwfd_file, ' ', $bwfd_w, 'x', $bwfd_h, ' ', round( filesize( $bwfd_dir . $bwfd_file ) / 1024 ), " KB\n";
}
