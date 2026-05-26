<?php
/**
 * Theme setup — theme support, menus, image sizes, performance tweaks.
 * From v2 §4.3 — verbatim.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', function() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'gallery', 'caption', 'script', 'style' ] );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus( [
		'primary'   => __( 'Primary', 'herbalpearls' ),
		'footer-1'  => __( 'Footer Shop', 'herbalpearls' ),
		'footer-2'  => __( 'Footer Help', 'herbalpearls' ),
		'footer-3'  => __( 'Footer About', 'herbalpearls' ),
	] );

	add_image_size( 'hp-product-card',  600,  600, true );
	add_image_size( 'hp-product-zoom', 1400, 1400, true );
	add_image_size( 'hp-banner',       1920,  720, true );
	add_image_size( 'hp-blog-card',     800,  500, true );
} );

// Disable WP heartbeat on front-end, throttle in admin
add_action( 'init', function() {
	if ( ! is_admin() ) {
		wp_deregister_script( 'heartbeat' );
	}
} );

add_filter( 'heartbeat_settings', function( $s ) {
	$s['interval'] = 60;
	return $s;
} );

// Limit post revisions
if ( ! defined( 'WP_POST_REVISIONS' ) ) {
	define( 'WP_POST_REVISIONS', 5 );
}

// Disable XML-RPC unless explicitly needed
add_filter( 'xmlrpc_enabled', '__return_false' );

// Customizer — Hero banners with per-slide data.
// Uses plain URL text fields for images (no class dependencies — works on any host).
add_action( 'customize_register', function( $wp_customize ) {
	$wp_customize->add_section( 'hp_hero', [
		'title'    => __( 'Hero Banners', 'herbalpearls' ),
		'priority' => 30,
	] );

	for ( $i = 1; $i <= 3; $i++ ) {
		// Banner image — attachment ID (preferred, enables srcset/sizes)
		$wp_customize->add_setting( "hp_hero_image_id_$i", [
			'default'           => 0,
			'sanitize_callback' => 'absint',
		] );
		$wp_customize->add_control(
			new WP_Customize_Media_Control( $wp_customize, "hp_hero_image_id_$i", [
				'label'     => sprintf( __( 'Slide %d — Banner Image (recommended)', 'herbalpearls' ), $i ),
				'section'   => 'hp_hero',
				'mime_type' => 'image',
			] )
		);

		// Legacy fallback — banner image URL (used only if no attachment ID set)
		$wp_customize->add_setting( "hp_hero_image_url_$i", [
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		] );
		$wp_customize->add_control( "hp_hero_image_url_$i", [
			'label'       => sprintf( __( 'Slide %d — Image URL (fallback)', 'herbalpearls' ), $i ),
			'description' => __( 'Only used if no media image is selected above. Prefer the media picker for responsive images.', 'herbalpearls' ),
			'section'     => 'hp_hero',
			'type'        => 'url',
		] );

		// Headline
		$wp_customize->add_setting( "hp_hero_headline_$i", [
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		] );
		$wp_customize->add_control( "hp_hero_headline_$i", [
			'label'   => sprintf( __( 'Slide %d — Headline', 'herbalpearls' ), $i ),
			'section' => 'hp_hero',
			'type'    => 'text',
		] );

		// Subtitle
		$wp_customize->add_setting( "hp_hero_subtitle_$i", [
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		] );
		$wp_customize->add_control( "hp_hero_subtitle_$i", [
			'label'   => sprintf( __( 'Slide %d — Subtitle', 'herbalpearls' ), $i ),
			'section' => 'hp_hero',
			'type'    => 'textarea',
		] );

		// CTA text
		$wp_customize->add_setting( "hp_hero_cta_text_$i", [
			'default'           => __( 'Shop Now', 'herbalpearls' ),
			'sanitize_callback' => 'sanitize_text_field',
		] );
		$wp_customize->add_control( "hp_hero_cta_text_$i", [
			'label'   => sprintf( __( 'Slide %d — Button Text', 'herbalpearls' ), $i ),
			'section' => 'hp_hero',
			'type'    => 'text',
		] );

		// CTA URL
		$wp_customize->add_setting( "hp_hero_cta_url_$i", [
			'default'           => get_permalink( wc_get_page_id( 'shop' ) ),
			'sanitize_callback' => 'esc_url_raw',
		] );
		$wp_customize->add_control( "hp_hero_cta_url_$i", [
			'label'   => sprintf( __( 'Slide %d — Button Link', 'herbalpearls' ), $i ),
			'section' => 'hp_hero',
			'type'    => 'url',
		] );
	}
} );
