<?php
/**
 * Book band – the navy closing band with cover, heading, line and buttons.
 * Words come from the block's attributes (blank falls back to copy for the
 * chosen action); price, cover, chapter count and page links from the
 * structured data settings.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner content (unused).
 * @var WP_Block $block      Block instance.
 *
 * @package bwfd
 */

declare( strict_types=1 );

$bwfd_data   = bwfd_schema_data();
$bwfd_book   = $bwfd_data['book'];
$bwfd_price  = trim( (string) $bwfd_data['offer']['price'] );
$bwfd_pages  = $bwfd_data['pages'];
$bwfd_is_buy = ( $attributes['primary'] ?? 'buy' ) !== 'book';

$bwfd_page_link = static function ( $page_id, string $fallback ): string {
	$link = $page_id ? get_permalink( (int) $page_id ) : false;
	return is_string( $link ) ? $link : $fallback;
};
$bwfd_buy_page  = $bwfd_page_link( $bwfd_pages['purchase'], (string) $bwfd_data['offer']['buy_url'] );
$bwfd_book_page = $bwfd_page_link( $bwfd_pages['about_book'], home_url( '/' ) );

// {book}, {chapters}, {price}, {author} in any of the texts.
$bwfd_fill = static function ( string $text ) use ( $bwfd_book, $bwfd_price, $bwfd_data ): string {
	$chapters = (int) ( $bwfd_book['chapters'] ?? 0 );
	return strtr(
		$text,
		array(
			'{book}'     => '<em>' . esc_html( (string) $bwfd_book['name'] ) . '</em>',
			'{chapters}' => $chapters > 0 ? (string) $chapters : '',
			'{price}'    => '' !== $bwfd_price ? '$' . esc_html( $bwfd_price ) : '',
			'{author}'   => esc_html( (string) $bwfd_data['author']['name'] ),
		)
	);
};
$bwfd_plain = static fn( string $html ): string => trim( wp_strip_all_tags( $html ) );

$bwfd_heading = trim( (string) ( $attributes['heading'] ?? '' ) );
if ( '' === $bwfd_heading ) {
	$bwfd_heading = $bwfd_is_buy ? __( 'Want more like this?', 'bwfd' ) : (string) $bwfd_book['name'];
}
$bwfd_text = trim( (string) ( $attributes['text'] ?? '' ) );
if ( '' === $bwfd_text ) {
	$bwfd_text = $bwfd_is_buy
		? __( 'There are {chapters} short chapters like this in {book}.', 'bwfd' )
		: __( 'Forty short chapters on what Scripture shows us about the Father we are learning to imitate.', 'bwfd' );
}
$bwfd_primary_label = trim( (string) ( $attributes['primaryLabel'] ?? '' ) );
if ( '' === $bwfd_primary_label ) {
	$bwfd_primary_label = $bwfd_is_buy
		? ( '' !== $bwfd_price ? __( 'Buy the book · {price}', 'bwfd' ) : __( 'Buy the book', 'bwfd' ) )
		: __( 'Learn more about the book', 'bwfd' );
}
$bwfd_primary_url    = trim( (string) ( $attributes['primaryUrl'] ?? '' ) ) ?: ( $bwfd_is_buy ? $bwfd_buy_page : $bwfd_book_page );
$bwfd_secondary      = trim( (string) ( $attributes['secondaryLabel'] ?? '' ) );
$bwfd_secondary_url  = trim( (string) ( $attributes['secondaryUrl'] ?? '' ) ) ?: $bwfd_book_page;
$bwfd_cover_width    = max( 60, min( 240, (int) ( $attributes['coverWidth'] ?? 140 ) ) );
$bwfd_show_cover     = ! isset( $attributes['showCover'] ) || $attributes['showCover'];

$bwfd_wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'is-style-bwfd-navy bwfd-book-band' . ( $bwfd_show_cover ? '' : ' bwfd-book-band--no-cover' ),
	)
);
?>
<div <?php echo $bwfd_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?>>
	<div class="bwfd-book-band__inner">
		<div class="bwfd-book-band__row">
			<?php if ( $bwfd_show_cover ) : ?>
				<?php echo bwfd_cover_image( $bwfd_cover_width, 'bwfd-book-band__cover' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped values. ?>
			<?php endif; ?>
			<div class="bwfd-book-band__text">
				<h2 class="bwfd-book-band__heading"><?php echo esc_html( $bwfd_plain( $bwfd_fill( $bwfd_heading ) ) ); ?></h2>
				<p class="bwfd-book-band__line"><?php echo wp_kses( $bwfd_fill( esc_html( $bwfd_text ) ), array( 'em' => array() ) ); ?></p>
			</div>
			<div class="bwfd-book-band__buttons">
				<a class="bwfd-book-band__button wp-element-button" href="<?php echo esc_url( $bwfd_primary_url ); ?>"><?php echo esc_html( $bwfd_plain( $bwfd_fill( $bwfd_primary_label ) ) ); ?></a>
				<?php if ( '' !== $bwfd_secondary ) : ?>
				<a class="bwfd-book-band__button bwfd-book-band__button--outline wp-element-button" href="<?php echo esc_url( $bwfd_secondary_url ); ?>"><?php echo esc_html( $bwfd_plain( $bwfd_fill( $bwfd_secondary ) ) ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
