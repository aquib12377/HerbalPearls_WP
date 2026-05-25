<?php
/**
 * Related products — 4-card carousel.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$related = wc_get_related_products( get_the_ID(), 4 );

if ( empty( $related ) ) {
	return;
}
?>

<section class="hp-section hp-surface-page">
	<div class="hp-container">
		<h2 class="hp-text-center hp-mb-2">
			<?php esc_html_e( 'You May Also Like', 'herbalpearls' ); ?>
		</h2>
		<div class="hp-grid-4" data-related-carousel>
			<?php
			foreach ( $related as $related_id ) :
				$related_product = wc_get_product( $related_id );
				if ( ! $related_product ) {
					continue;
				}

				$sale_pct = 0;
				if ( $related_product->is_on_sale() ) {
					$regular = (float) $related_product->get_regular_price();
					$sale    = (float) $related_product->get_sale_price();
					if ( $regular > 0 ) {
						$sale_pct = round( ( ( $regular - $sale ) / $regular ) * 100 );
					}
				}
				?>
				<div class="hp-product-card">
					<div class="hp-product-card__image">
						<a href="<?php echo esc_url( get_permalink( $related_id ) ); ?>">
							<?php echo $related_product->get_image( 'hp-product-card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
						<?php if ( $sale_pct > 0 ) : ?>
							<div class="hp-product-card__badge">
								<span class="hp-badge hp-badge--sale">-<?php echo absint( $sale_pct ); ?>%</span>
							</div>
						<?php endif; ?>
					</div>
					<div class="hp-product-card__body">
						<h3 class="hp-product-card__title">
							<a href="<?php echo esc_url( get_permalink( $related_id ) ); ?>">
								<?php echo esc_html( $related_product->get_name() ); ?>
							</a>
						</h3>
						<div class="hp-product-card__price">
							<?php echo $related_product->get_price_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
