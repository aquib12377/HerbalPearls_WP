<?php
/**
 * AJAX handlers — mini-cart fragments, pincode lookup REST route.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Mini-cart fragment refresh via WooCommerce cart fragments.
 */
add_filter( 'woocommerce_add_to_cart_fragments', function( $fragments ) {
	ob_start();
	?>
	<span class="hp-header__cart-count" data-cart-count>
		<?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?>
	</span>
	<?php
	$fragments['[data-cart-count]'] = ob_get_clean();

	return $fragments;
} );

/**
 * Register REST route for pincode / delivery zone lookup.
 * GET /wp-json/hp/v1/pincode/{code}
 * Cached via transient for 30 days.
 */
add_action( 'rest_api_init', function() {
	register_rest_route( 'hp/v1', '/pincode/(?P<code>\d{6})', [
		'methods'             => 'GET',
		'callback'            => 'hp_pincode_lookup',
		'permission_callback' => '__return_true',
	] );
} );

function hp_pincode_lookup( $request ) {
	$code = $request->get_param( 'code' );

	$transient_key = 'hp_pincode_' . $code;
	$cached        = get_transient( $transient_key );

	if ( false !== $cached ) {
		return rest_ensure_response( $cached );
	}

	// Static Indian pincode zone mapping — extend as needed
	$zone_map = [
		'delivery'       => true,
		'estimated_days' => '5-7',
		'cod_available'  => true,
		'city'           => '',
		'state'          => '',
	];

	// All Indian pincodes 110000-999999 default to deliverable
	// Mark specific non-serviceable codes here
	$non_serviceable = [];

	if ( in_array( $code, $non_serviceable, true ) ) {
		$zone_map['delivery']      = false;
		$zone_map['cod_available'] = false;
	}

	set_transient( $transient_key, $zone_map, 30 * DAY_IN_SECONDS );

	return rest_ensure_response( $zone_map );
}
