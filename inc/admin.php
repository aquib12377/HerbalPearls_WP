<?php
/**
 * Admin UI tweaks — menu cleanup, dashboard widget.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Clean up admin menu for shop managers.
 */
add_action( 'admin_menu', function() {
	// Remove core meta boxes that we replace
	remove_meta_box( 'postcustom', 'product', 'normal' );
}, 99 );

/**
 * Add dashboard widget showing today's order count.
 */
add_action( 'wp_dashboard_setup', function() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}

	wp_add_dashboard_widget(
		'hp_today_orders',
		__( 'Herbal Pearls — Orders Today', 'herbalpearls' ),
		'hp_dashboard_today_orders'
	);
} );

function hp_dashboard_today_orders() {
	if ( ! function_exists( 'wc_get_orders' ) ) {
		echo '<p>' . esc_html__( 'WooCommerce is not active.', 'herbalpearls' ) . '</p>';
		return;
	}

	$today_start = gmdate( 'Y-m-d 00:00:00' );
	$today_end   = gmdate( 'Y-m-d 23:59:59' );

	$orders = wc_get_orders( [
		'date_created' => $today_start . '...' . $today_end,
		'status'       => [ 'wc-completed', 'wc-processing', 'wc-on-hold' ],
		'limit'        => -1,
		'return'       => 'ids',
	] );

	$count = count( $orders );
	$total = 0;

	if ( $count > 0 ) {
		foreach ( $orders as $order_id ) {
			$order = wc_get_order( $order_id );
			if ( $order ) {
				$total += (float) $order->get_total();
			}
		}
	}

	echo '<div style="padding: 12px 0;">';
	echo '<p style="font-size: 2rem; font-weight: 700; margin: 0 0 4px;">' . absint( $count ) . '</p>';
	echo '<p style="color: #666; margin: 0;">' . esc_html__( 'Orders', 'herbalpearls' ) . '</p>';
	echo '<p style="font-size: 1.25rem; font-weight: 600; margin: 12px 0 0;">' . esc_html( hp_inr( $total ) ) . '</p>';
	echo '<p style="color: #666; margin: 0;">' . esc_html__( 'Total Revenue', 'herbalpearls' ) . '</p>';
	echo '</div>';

	echo '<p><a href="' . esc_url( admin_url( 'edit.php?post_type=shop_order' ) ) . '" class="button">' . esc_html__( 'View All Orders', 'herbalpearls' ) . '</a></p>';
}
