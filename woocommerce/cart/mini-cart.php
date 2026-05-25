<?php
/**
 * Mini-cart drawer.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="mini-cart-drawer" class="hp-mini-cart" aria-hidden="true" role="dialog" aria-label="<?php esc_attr_e( 'Shopping cart', 'herbalpearls' ); ?>">
	<div class="hp-mini-cart__header">
		<h3 class="hp-mini-cart__title">
			<?php esc_html_e( 'Your Cart', 'herbalpearls' ); ?>
		</h3>
		<button
			type="button"
			class="hp-mini-cart__close"
			aria-label="<?php esc_attr_e( 'Close cart', 'herbalpearls' ); ?>"
			data-close="mini-cart"
		>
			<svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
				<line x1="18" y1="6" x2="6" y2="18" stroke="currentColor" stroke-width="2"/>
				<line x1="6" y1="6" x2="18" y2="18" stroke="currentColor" stroke-width="2"/>
			</svg>
		</button>
	</div>

	<div class="hp-mini-cart__body">
		<?php if ( WC()->cart->is_empty() ) : ?>
			<div class="hp-mini-cart__empty">
				<p><?php esc_html_e( 'Your cart is empty.', 'herbalpearls' ); ?></p>
				<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="hp-btn hp-btn--primary hp-btn--sm hp-mt-1">
					<?php esc_html_e( 'Start Shopping', 'herbalpearls' ); ?>
				</a>
			</div>
		<?php else : ?>
			<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
				$_product = $cart_item['data'];
				if ( ! $_product->exists() ) {
					continue;
				}
				?>
				<div class="hp-mini-cart__item">
					<div class="hp-mini-cart__item-image">
						<?php echo $_product->get_image( 'thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<div class="hp-mini-cart__item-info">
						<div class="hp-mini-cart__item-name">
							<?php echo esc_html( $_product->get_name() ); ?>
						</div>
						<div class="hp-mini-cart__item-price">
							<?php echo wp_kses_post( WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ) ); ?>
						</div>
						<?php if ( $cart_item['quantity'] > 1 ) : ?>
							<span style="font-size:0.75rem;color:var(--hp-text-3);">
								<?php echo esc_html( 'Qty: ' . $cart_item['quantity'] ); ?>
							</span>
						<?php endif; ?>
					</div>
					<button
						type="button"
						class="hp-mini-cart__item-remove"
						data-remove-item="<?php echo esc_attr( $cart_item_key ); ?>"
						aria-label="<?php esc_attr_e( 'Remove item', 'herbalpearls' ); ?>"
					>
						<svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true">
							<line x1="18" y1="6" x2="6" y2="18" stroke="currentColor" stroke-width="2"/>
							<line x1="6" y1="6" x2="18" y2="18" stroke="currentColor" stroke-width="2"/>
						</svg>
					</button>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>

	<?php if ( ! WC()->cart->is_empty() ) : ?>
		<div class="hp-mini-cart__footer">
			<div class="hp-mini-cart__subtotal">
				<span><?php esc_html_e( 'Subtotal', 'herbalpearls' ); ?></span>
				<span><?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?></span>
			</div>
			<div class="hp-mini-cart__actions">
				<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="hp-btn hp-btn--secondary hp-btn--sm">
					<?php esc_html_e( 'View Cart', 'herbalpearls' ); ?>
				</a>
				<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="hp-btn hp-btn--primary hp-btn--sm">
					<?php esc_html_e( 'Checkout', 'herbalpearls' ); ?>
				</a>
			</div>
		</div>
	<?php endif; ?>
</div>
