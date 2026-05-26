<?php
/**
 * WooCommerce integration — template overrides, hooks, wrappers.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Declare WooCommerce support (also in setup.php for safety)
add_action( 'after_setup_theme', function() {
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
} );

// Wrap WooCommerce content
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

add_action( 'woocommerce_before_main_content', function() {
	echo '<div class="hp-container hp-section"><div class="hp-woo-content">';
}, 10 );

add_action( 'woocommerce_after_main_content', function() {
	echo '</div></div>';
}, 10 );

// Remove WooCommerce sidebar — we use our own filter sidebar
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

// Remove default single-product summary hooks — our woocommerce/single-product.php
// renders everything manually. Prevents duplicate tabs, duplicate related products,
// and duplicate upsells from woocommerce_after_single_product_summary.
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

// Product cards per row
add_filter( 'woocommerce_loop_columns', function() {
	return 4;
} );

// Products per page
add_filter( 'loop_shop_per_page', function() {
	return 12;
} );

// Cross-sells count
add_filter( 'woocommerce_cross_sells_columns', function() {
	return 4;
} );

add_filter( 'woocommerce_cross_sells_total', function() {
	return 4;
} );

// Related products count
add_filter( 'woocommerce_output_related_products_args', function( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
} );

// Sale badge text
add_filter( 'woocommerce_sale_flash', function( $badge, $post, $product ) {
	if ( $product->is_type( 'variable' ) ) {
		$regular = $product->get_variation_regular_price( 'min' );
		$sale    = $product->get_variation_sale_price( 'min' );
		if ( $regular && $sale ) {
			$pct = round( ( ( $regular - $sale ) / $regular ) * 100 );
			return '<span class="hp-badge hp-badge--sale">-' . absint( $pct ) . '%</span>';
		}
	}
	return '<span class="hp-badge hp-badge--sale">' . esc_html__( 'Sale', 'herbalpearls' ) . '</span>';
}, 10, 3 );

// Enable gallery on single product
add_action( 'wp', function() {
	if ( is_product() ) {
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}
} );

/* Product image alt fallback — use the product title when the
 * attachment has no alt text set. Prevents empty alt="" on every
 * shop archive image. */
add_filter( 'wp_get_attachment_image_attributes', function( $attr, $attachment, $size ) {
	if ( ! empty( $attr['alt'] ) ) {
		return $attr;
	}
	if ( ! function_exists( 'wc_get_product' ) ) {
		return $attr;
	}
	$parent = $attachment ? $attachment->post_parent : 0;
	if ( $parent && 'product' === get_post_type( $parent ) ) {
		$product = wc_get_product( $parent );
		if ( $product ) {
			$attr['alt'] = $product->get_name();
		}
	}
	return $attr;
}, 10, 3 );
