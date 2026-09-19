<?php
/**
 * Book card – cover, title, a line of text and the buy button, from the
 * structured data settings so the price and cover stay current.
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
$bwfd_offer  = $bwfd_data['offer'];
$bwfd_pages  = $bwfd_data['pages'];
$bwfd_price  = trim( (string) $bwfd_offer['price'] );
$bwfd_accent = ( $attributes['accent'] ?? 'blue' ) === 'apricot' ? 'apricot' : 'blue';

$bwfd_page_link = static function ( $page_id, string $fallback ): string {
	$link = $page_id ? get_permalink( (int) $page_id ) : false;
	return is_string( $link ) ? $link : $fallback;
};
$bwfd_buy_page  = $bwfd_page_link( $bwfd_pages['purchase'], (string) $bwfd_offer['buy_url'] );
$bwfd_book_page = $bwfd_page_link( $bwfd_pages['about_book'], home_url( '/' ) );

// Text: the saved copy, else a line built from the release date and price.
$bwfd_text = trim( (string) ( $attributes['text'] ?? '' ) );
if ( '' === $bwfd_text ) {
	$bwfd_release = (string) $bwfd_book['release_date'];
	if ( '' !== $bwfd_release && $bwfd_release > current_time( 'Y-m-d' ) ) {
		$bwfd_text = sprintf(
			/* translators: 1: release date, 2: price. */
			__( 'Out %1$s. RRP $%2$s.', 'bwfd' ),
			wp_date( 'j F', strtotime( $bwfd_release ) ),
			$bwfd_price
		);
	} else {
		/* translators: %s: price. */
		$bwfd_text = sprintf( __( 'Paperback $%s plus postage, direct from the publisher.', 'bwfd' ), $bwfd_price );
	}
}

$bwfd_button = trim( (string) ( $attributes['buttonLabel'] ?? '' ) ) ?: __( 'Buy the book', 'bwfd' );
if ( ! empty( $attributes['showPrice'] ) && '' !== $bwfd_price ) {
	$bwfd_button .= ' &middot; $' . esc_html( $bwfd_price );
} else {
	$bwfd_button = esc_html( $bwfd_button );
}
$bwfd_button_url = trim( (string) ( $attributes['buttonUrl'] ?? '' ) ) ?: $bwfd_buy_page;
$bwfd_link_label = trim( (string) ( $attributes['linkLabel'] ?? '' ) ) ?: __( 'About the book', 'bwfd' );
$bwfd_link_url   = trim( (string) ( $attributes['linkUrl'] ?? '' ) ) ?: $bwfd_book_page;

$bwfd_classes = array( 'bwfd-book-card', 'bwfd-book-card--' . $bwfd_accent );
if ( ! isset( $attributes['sticky'] ) || $attributes['sticky'] ) {
	$bwfd_classes[] = 'bwfd-book-card--sticky';
}
$bwfd_wrapper = get_block_wrapper_attributes( array( 'class' => implode( ' ', $bwfd_classes ) ) );
?>
<aside <?php echo $bwfd_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?>>
	<?php echo bwfd_cover_image( 118, 'bwfd-book-card__cover' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped values. ?>
	<p class="bwfd-book-card__title"><?php echo esc_html( $bwfd_book['name'] ); ?></p>
	<p class="bwfd-book-card__text"><?php echo esc_html( $bwfd_text ); ?></p>
	<a class="bwfd-book-card__button wp-element-button" href="<?php echo esc_url( $bwfd_button_url ); ?>"><?php echo $bwfd_button; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?></a>
	<?php if ( '' !== $bwfd_link_url ) : ?>
	<a class="bwfd-book-card__link" href="<?php echo esc_url( $bwfd_link_url ); ?>"><?php echo esc_html( $bwfd_link_label ); ?></a>
	<?php endif; ?>
</aside>
