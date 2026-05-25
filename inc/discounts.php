<?php
/**
 * First-purchase 5% off — code WELCOME5.
 * From v2 Appendix A.4 — verbatim.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Reject WELCOME5 if user (logged in) has any prior order,
 * or (guest) if email has any prior order.
 */
add_filter( 'woocommerce_coupon_is_valid', function( $valid, $coupon ) {
	if ( strtolower( $coupon->get_code() ) !== 'welcome5' ) {
		return $valid;
	}

	// Logged-in user check
	$user_id = get_current_user_id();
	if ( $user_id ) {
		$orders = wc_get_orders( [
			'customer_id' => $user_id,
			'status'      => [ 'wc-completed', 'wc-processing', 'wc-on-hold' ],
			'limit'       => 1,
			'return'      => 'ids',
		] );
		if ( ! empty( $orders ) ) {
			throw new Exception( __( 'Coupon WELCOME5 is for your first order only.', 'herbalpearls' ) );
		}
		return $valid;
	}

	// Guest email check (only at checkout when email is known)
	$email = WC()->customer ? WC()->customer->get_billing_email() : '';
	if ( $email ) {
		$orders = wc_get_orders( [
			'billing_email' => $email,
			'status'        => [ 'wc-completed', 'wc-processing', 'wc-on-hold' ],
			'limit'         => 1,
			'return'        => 'ids',
		] );
		if ( ! empty( $orders ) ) {
			throw new Exception( __( 'Coupon WELCOME5 is for first-time customers only.', 'herbalpearls' ) );
		}
	}
	return $valid;
}, 10, 2 );

/**
 * Auto-create WELCOME5 coupon on theme activation if missing.
 */
add_action( 'after_switch_theme', function() {
	if ( wc_get_coupon_id_by_code( 'welcome5' ) ) {
		return;
	}

	$coupon = new WC_Coupon();
	$coupon->set_code( 'WELCOME5' );
	$coupon->set_discount_type( 'percent' );
	$coupon->set_amount( 5 );
	$coupon->set_description( __( 'First-purchase 5% off — auto-created by Herbal Pearls theme.', 'herbalpearls' ) );
	$coupon->set_individual_use( true );
	$coupon->set_usage_limit_per_user( 1 );
	$coupon->set_minimum_amount( 199 );
	$coupon->save();
} );
