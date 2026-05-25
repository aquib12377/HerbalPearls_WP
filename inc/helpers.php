<?php
/**
 * Utility functions.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Format amount in INR with ₹ symbol.
 *
 * @param float $amount
 * @return string
 */
function hp_inr( $amount ) {
	return '₹' . number_format( (float) $amount, 0, '.', ',' );
}

/**
 * Escape a string for safe HTML output. Wrapper for wp_kses_post
 * with a narrower allowlist for inline elements.
 *
 * @param string $html
 * @return string
 */
function hp_safe_html( $html ) {
	$allowed = [
		'strong' => [],
		'em'     => [],
		'span'   => [ 'class' => [] ],
		'a'      => [ 'href' => [], 'class' => [], 'rel' => [], 'target' => [] ],
		'br'     => [],
	];

	return wp_kses( $html, $allowed );
}

/**
 * Get the brand logo URL, with fallback.
 *
 * @return string
 */
function hp_get_brand_logo_url() {
	$logo_id = get_theme_mod( 'custom_logo' );

	if ( $logo_id ) {
		$url = wp_get_attachment_image_url( $logo_id, 'full' );
		if ( $url ) {
			return $url;
		}
	}

	return HP_URI . '/assets/images/logo-512.png';
}

/**
 * Output breadcrumb HTML for use in templates.
 * Wraps hp_build_breadcrumb_trail() from seo-schema.php.
 *
 * @return void
 */
function hp_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}

	if ( ! function_exists( 'hp_build_breadcrumb_trail' ) ) {
		return;
	}

	$items = hp_build_breadcrumb_trail();

	if ( count( $items ) < 2 ) {
		return;
	}

	echo '<nav class="hp-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'herbalpearls' ) . '">';
	$last = count( $items ) - 1;
	foreach ( $items as $i => $item ) {
		if ( $i === $last ) {
			echo '<span aria-current="page">' . esc_html( $item['name'] ) . '</span>';
		} else {
			echo '<a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['name'] ) . '</a>';
			echo ' <span class="hp-breadcrumbs__sep">/</span> ';
		}
	}
	echo '</nav>';
}
