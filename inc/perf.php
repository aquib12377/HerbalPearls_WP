<?php
/**
 * Performance helpers — critical CSS, font-face, preload, lazy, misc.
 * Updated: uses .ttf fonts with format('truetype').
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Inline critical CSS on key page types */
add_action( 'wp_head', function() {
	if ( is_admin() ) {
		return;
	}
	if ( ! is_front_page() && ! is_product() && ! is_shop() ) {
		return;
	}
	$file = HP_DIR . '/assets/css/critical.css';
	if ( ! file_exists( $file ) ) {
		return;
	}
	echo '<style id="hp-critical">' . file_get_contents( $file ) . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — raw CSS from project file
}, 5 );

/* Inject @font-face declarations — only for .ttf fonts that exist on disk */
add_action( 'wp_head', function() {
	$fonts = [
		'cormorant-garamond-400.ttf'  => [ 'family' => 'Cormorant Garamond', 'weight' => '400', 'style' => 'normal' ],
		'cormorant-garamond-600.ttf'  => [ 'family' => 'Cormorant Garamond', 'weight' => '600', 'style' => 'normal' ],
		'cormorant-garamond-400i.ttf' => [ 'family' => 'Cormorant Garamond', 'weight' => '400', 'style' => 'italic' ],
		'inter-400.ttf'               => [ 'family' => 'Inter',              'weight' => '400', 'style' => 'normal' ],
		'inter-600.ttf'               => [ 'family' => 'Inter',              'weight' => '600', 'style' => 'normal' ],
		'allura-400.ttf'              => [ 'family' => 'Allura',             'weight' => '400', 'style' => 'normal' ],
	];

	$css = '';
	foreach ( $fonts as $filename => $meta ) {
		$file_path = HP_DIR . '/assets/fonts/' . $filename;
		if ( file_exists( $file_path ) ) {
			$css .= sprintf(
				"@font-face{font-family:'%s';src:url('%s/assets/fonts/%s') format('truetype');font-weight:%s;font-style:%s;font-display:swap;}\n",
				$meta['family'],
				HP_URI,
				$filename,
				$meta['weight'],
				$meta['style']
			);
		}
	}

	if ( '' !== $css ) {
		echo '<style id="hp-fonts">' . $css . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — generated CSS
	}
}, 1 );

/* Preload hero fonts + LCP image — only emit if files exist */
add_action( 'wp_head', function() {
	$fonts = [
		'cormorant-garamond-600.ttf' => 'font/ttf',
		'inter-400.ttf'              => 'font/ttf',
	];

	foreach ( $fonts as $filename => $type ) {
		$file_path = HP_DIR . '/assets/fonts/' . $filename;
		if ( file_exists( $file_path ) ) {
			echo '<link rel="preload" href="' . esc_url( HP_URI . '/assets/fonts/' . $filename ) . '" as="font" type="' . esc_attr( $type ) . '" crossorigin>' . "\n";
		}
	}

	if ( is_front_page() ) {
		$hero_path = HP_DIR . '/assets/images/hero-banner.webp';
		if ( file_exists( $hero_path ) ) {
			echo '<link rel="preload" as="image" href="' . esc_url( HP_URI . '/assets/images/hero-banner.webp' ) . '" fetchpriority="high">' . "\n";
		}
	}
}, 1 );

/* Disable emoji scripts */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

/* Remove RSD + wlwmanifest links */
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_generator' );

/* Lazy-load iframes */
add_filter( 'the_content', function( $content ) {
	return preg_replace( '/<iframe(?![^>]*\bloading=)/', '<iframe loading="lazy"', $content );
} );
