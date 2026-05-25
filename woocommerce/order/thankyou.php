<?php
/**
 * Order received / Thank You page.
 * Gold halo behind checkmark, 3-step timeline, cross-sell.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$order = wc_get_order( $order_id );

if ( ! $order ) {
	return;
}
?>

<div class="woocommerce-order-received">
	<div class="hp-order-confirm-icon" aria-hidden="true">
		<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
			<polyline points="20 6 9 17 4 12" />
		</svg>
	</div>

	<p class="woocommerce-thankyou-order-received">
		<?php
		printf(
			/* translators: %s: order number */
			esc_html__( 'Thank you! Your order #%s has been placed.', 'herbalpearls' ),
			esc_html( $order->get_order_number() )
		);
		?>
	</p>

	<p style="font-size: 0.9375rem; color: var(--hp-text-2);">
		<?php esc_html_e( 'We will send you a confirmation email shortly.', 'herbalpearls' ); ?>
	</p>

	<!-- 3-step timeline -->
	<div class="hp-flex hp-flex--center hp-mt-2" style="gap: 2rem; flex-wrap: wrap;">
		<div class="hp-text-center">
			<div style="width: 40px; height: 40px; margin: 0 auto 0.5rem; border-radius: 50%; background: var(--hp-gold-600); color: var(--hp-text-on-gold); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem;">1</div>
			<span style="font-size: 0.8125rem; color: var(--hp-text-1); font-weight: 600;"><?php esc_html_e( 'Order Confirmed', 'herbalpearls' ); ?></span>
		</div>
		<span style="color: var(--hp-text-4); font-size: 1.5rem;">&rarr;</span>
		<div class="hp-text-center">
			<div style="width: 40px; height: 40px; margin: 0 auto 0.5rem; border-radius: 50%; background: var(--hp-bg-2); color: var(--hp-text-2); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem;">2</div>
			<span style="font-size: 0.8125rem; color: var(--hp-text-2);"><?php esc_html_e( 'Processing', 'herbalpearls' ); ?></span>
		</div>
		<span style="color: var(--hp-text-4); font-size: 1.5rem;">&rarr;</span>
		<div class="hp-text-center">
			<div style="width: 40px; height: 40px; margin: 0 auto 0.5rem; border-radius: 50%; background: var(--hp-bg-2); color: var(--hp-text-2); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem;">3</div>
			<span style="font-size: 0.8125rem; color: var(--hp-text-2);"><?php esc_html_e( 'Shipped', 'herbalpearls' ); ?></span>
		</div>
	</div>

	<!-- Order details -->
	<ul class="woocommerce-order-overview hp-mt-2">
		<li>
			<?php esc_html_e( 'Order number:', 'herbalpearls' ); ?>
			<strong><?php echo esc_html( $order->get_order_number() ); ?></strong>
		</li>
		<li>
			<?php esc_html_e( 'Date:', 'herbalpearls' ); ?>
			<strong><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></strong>
		</li>
		<li>
			<?php esc_html_e( 'Total:', 'herbalpearls' ); ?>
			<strong><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></strong>
		</li>
		<?php if ( $order->get_payment_method_title() ) : ?>
			<li>
				<?php esc_html_e( 'Payment:', 'herbalpearls' ); ?>
				<strong><?php echo esc_html( $order->get_payment_method_title() ); ?></strong>
			</li>
		<?php endif; ?>
	</ul>

	<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
</div>

<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>
