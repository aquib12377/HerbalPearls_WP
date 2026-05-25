<?php
/**
 * Product card in shop loop.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! $product instanceof WC_Product ) {
	return;
}

$sale_pct = 0;
if ( $product->is_type( 'variable' ) ) {
	$regular = $product->get_variation_regular_price( 'min' );
	$sale    = $product->get_variation_sale_price( 'min' );
	if ( $regular && $sale && $regular > $sale ) {
		$sale_pct = round( ( ( $regular - $sale ) / $regular ) * 100 );
	}
} elseif ( $product->is_on_sale() ) {
	$regular = $product->get_regular_price();
	$sale    = $product->get_sale_price();
	if ( $regular && $sale && $regular > $sale ) {
		$sale_pct = round( ( ( $regular - $sale ) / $regular ) * 100 );
	}
}
?>
<li <?php wc_product_class( 'hp-product-card', $product ); ?>>
	<div class="hp-product-card__image">
		<a href="<?php the_permalink(); ?>">
			<?php echo $product->get_image( 'hp-product-card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — WooCommerce escapes ?>
		</a>
		<?php if ( $sale_pct > 0 ) : ?>
			<div class="hp-product-card__badge">
				<span class="hp-badge hp-badge--sale">-<?php echo absint( $sale_pct ); ?>%</span>
			</div>
		<?php endif; ?>
	</div>

	<div class="hp-product-card__body">
		<h2 class="hp-product-card__title">
			<a href="<?php the_permalink(); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
		</h2>

		<?php if ( $product->get_average_rating() > 0 ) : ?>
			<div class="hp-product-card__rating">
				<?php for ( $s = 1; $s <= 5; $s++ ) : ?>
					<span aria-hidden="true"><?php echo ( $s <= round( $product->get_average_rating() ) ) ? '★' : '☆'; ?></span>
				<?php endfor; ?>
				<span>(<?php echo absint( $product->get_review_count() ); ?>)</span>
			</div>
		<?php endif; ?>

		<div class="hp-product-card__price">
			<?php echo $product->get_price_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — WooCommerce escapes ?>
			<?php if ( $sale_pct > 0 ) : ?>
				<span class="hp-save-badge" style="display:block;margin-top:4px;">
					<?php
					printf(
						/* translators: %d: percentage off */
						esc_html__( 'You save %d%%', 'herbalpearls' ),
						absint( $sale_pct )
					);
					?>
				</span>
			<?php endif; ?>
		</div>
	</div>

	<div class="hp-product-card__actions">
		<a href="<?php the_permalink(); ?>" class="hp-btn hp-btn--primary hp-btn--sm hp-btn--block">
			<?php esc_html_e( 'View Product', 'herbalpearls' ); ?>
		</a>
	</div>
</li>
