<?php
/**
 * Checkout form — single-page 3-section layout per v2 §5.6.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', WC()->checkout() );

if ( ! WC()->checkout()->is_registration_enabled() && WC()->checkout()->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout"
	  action="<?php echo esc_url( wc_get_checkout_url() ); ?>"
	  enctype="multipart/form-data">

	<div class="col2-set" id="customer_details">
		<div class="col-1">
			<div class="woocommerce-billing-fields">
				<h3><?php esc_html_e( 'Contact & Billing', 'herbalpearls' ); ?></h3>
				<?php do_action( 'woocommerce_checkout_billing' ); ?>
			</div>
		</div>

		<div class="col-2">
			<div class="woocommerce-shipping-fields">
				<h3><?php esc_html_e( 'Shipping Address', 'herbalpearls' ); ?></h3>
				<?php do_action( 'woocommerce_checkout_shipping' ); ?>
			</div>

			<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
		</div>
	</div>

	<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

	<div id="order_review" class="woocommerce-checkout-review-order">
		<h3><?php esc_html_e( 'Order Summary', 'herbalpearls' ); ?></h3>
		<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
		<?php do_action( 'woocommerce_checkout_order_review' ); ?>
		<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
	</div>

</form>

<?php do_action( 'woocommerce_after_checkout_form', WC()->checkout() ); ?>
