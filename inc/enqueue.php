<?php
/**
 * Script and style enqueuing.
 * Cascade: tokens → base → components → woocommerce → page-specific
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', function() {
	$v = HP_VERSION; // Use filemtime() in dev for cache-busting

	/* ── Base cascade (all pages) ── */
	wp_enqueue_style( 'hp-tokens',     HP_URI . '/assets/css/tokens.css',     [], $v );
	wp_enqueue_style( 'hp-base',       HP_URI . '/assets/css/base.css',       [ 'hp-tokens' ], $v );
	wp_enqueue_style( 'hp-components', HP_URI . '/assets/css/components.css', [ 'hp-base' ], $v );
	wp_enqueue_style( 'hp-warm',       HP_URI . '/assets/css/warm-theme.css', [ 'hp-components' ], $v );

	/* ── WooCommerce (shop-aware pages) ── */
	if ( is_woocommerce() || is_cart() || is_checkout() ) {
		wp_enqueue_style( 'hp-woo', HP_URI . '/assets/css/woocommerce.css', [ 'hp-warm' ], $v );
	}

	/* ── Page-specific CSS (loaded after woocommerce.css so they override) ── */

	// Bundle CPT single + archive
	if ( is_singular( 'hp_bundle' ) || is_post_type_archive( 'hp_bundle' ) ) {
		wp_enqueue_style( 'hp-bundle', HP_URI . '/assets/css/bundle.css', [ 'hp-woo' ], $v );
	}

	// Shop archive / product category
	if ( is_shop() || is_product_category() || is_product_tag() ) {
		wp_enqueue_style( 'hp-shop', HP_URI . '/assets/css/pages/shop.css', [ 'hp-woo' ], $v );
	}

	// Single product
	if ( is_product() ) {
		wp_enqueue_style( 'hp-pdp', HP_URI . '/assets/css/pages/pdp.css', [ 'hp-woo' ], $v );
	}

	// Cart page
	if ( is_cart() ) {
		wp_enqueue_style( 'hp-cart', HP_URI . '/assets/css/pages/cart.css', [ 'hp-woo' ], $v );
	}

	// Checkout
	if ( is_checkout() ) {
		wp_enqueue_style( 'hp-checkout', HP_URI . '/assets/css/checkout.css', [ 'hp-woo' ], $v );
	}

	/* ── JavaScript ── */
	wp_enqueue_script( 'hp-main', HP_URI . '/assets/js/main.js', [], $v, true );

	if ( is_product() ) {
		wp_enqueue_script( 'hp-gallery', HP_URI . '/assets/js/product-gallery.js', [], $v, true );
	}

	if ( is_singular( 'hp_bundle' ) ) {
		wp_enqueue_script( 'hp-bundle', HP_URI . '/assets/js/bundle.js', [ 'jquery' ], $v, true );
		wp_localize_script( 'hp-bundle', 'HP_BUNDLE', [
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'hp_add_bundle' ),
		] );
	}

	if ( is_cart() ) {
		wp_enqueue_script( 'hp-cart-drawer', HP_URI . '/assets/js/mini-cart.js', [], $v, true );
	}

	if ( is_checkout() ) {
		wp_enqueue_script( 'hp-checkout', HP_URI . '/assets/js/checkout.js', [], $v, true );
	}
} );

/* ── Strip WooCommerce default CSS globally ── */
add_action( 'wp_enqueue_scripts', function() {
	wp_dequeue_style( 'woocommerce-general' );
	wp_dequeue_style( 'woocommerce-layout' );
	wp_dequeue_style( 'woocommerce-smallscreen' );
	wp_dequeue_style( 'wc-blocks-style' );

	if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() && ! is_account_page() ) {
		wp_dequeue_script( 'wc-cart-fragments' );
		wp_dequeue_script( 'wc-add-to-cart' );
		wp_dequeue_script( 'woocommerce' );
	}
}, 99 );

/* ── Defer non-critical JS ── */
add_filter( 'script_loader_tag', function( $tag, $handle ) {
	if ( is_admin() ) {
		return $tag;
	}

	if ( false !== strpos( $tag, 'defer' ) || false !== strpos( $tag, 'async' ) ) {
		return $tag;
	}

	$no_defer = [ 'jquery-core', 'jquery-migrate' ];
	if ( in_array( $handle, $no_defer, true ) ) {
		return $tag;
	}

	if ( 0 === strpos( $handle, 'wp-' ) ) {
		return $tag;
	}

	$tag = str_replace( ' src=', ' defer src=', $tag );
	return $tag;
}, 10, 2 );
