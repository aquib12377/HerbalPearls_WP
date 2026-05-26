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
	if ( ! is_front_page() && ! is_product() && ! is_shop() && ! is_singular( 'hp_bundle' ) ) {
		return;
	}
	$file = HP_DIR . '/assets/css/critical.css';
	if ( ! file_exists( $file ) ) {
		return;
	}
	echo '<style id="hp-critical">' . file_get_contents( $file ) . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — raw CSS from project file
}, 5 );

/* Inject @font-face declarations — prefer WOFF2 over TTF when both exist.
 * Once the user converts the .ttf files to subset .woff2, no code change is needed.
 */
add_action( 'wp_head', function() {
	$faces = [
		'cormorant-garamond-400'  => [ 'family' => 'Cormorant Garamond', 'weight' => '400', 'style' => 'normal' ],
		'cormorant-garamond-600'  => [ 'family' => 'Cormorant Garamond', 'weight' => '600', 'style' => 'normal' ],
		'cormorant-garamond-400i' => [ 'family' => 'Cormorant Garamond', 'weight' => '400', 'style' => 'italic' ],
		'inter-400'               => [ 'family' => 'Inter',              'weight' => '400', 'style' => 'normal' ],
		'inter-600'               => [ 'family' => 'Inter',              'weight' => '600', 'style' => 'normal' ],
		'allura-400'              => [ 'family' => 'Allura',             'weight' => '400', 'style' => 'normal' ],
	];

	$format_map = [ 'woff2' => 'woff2', 'woff' => 'woff', 'ttf' => 'truetype' ];

	$css = '';
	foreach ( $faces as $stem => $meta ) {
		$sources = [];
		foreach ( $format_map as $ext => $format ) {
			$file_path = HP_DIR . '/assets/fonts/' . $stem . '.' . $ext;
			if ( file_exists( $file_path ) ) {
				$sources[] = sprintf(
					"url('%s/assets/fonts/%s.%s') format('%s')",
					HP_URI,
					$stem,
					$ext,
					$format
				);
			}
		}
		if ( empty( $sources ) ) {
			continue;
		}
		$css .= sprintf(
			"@font-face{font-family:'%s';src:%s;font-weight:%s;font-style:%s;font-display:swap;}\n",
			$meta['family'],
			implode( ',', $sources ),
			$meta['weight'],
			$meta['style']
		);
	}

	if ( '' !== $css ) {
		echo '<style id="hp-fonts">' . $css . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — generated CSS
	}
}, 1 );

/* Preload hero fonts + LCP image — only emit if files exist.
 * Prefer .woff2 over .ttf when both exist (smaller, modern browser support).
 */
add_action( 'wp_head', function() {
	// Each entry: [ filename, mime-type ]. The first available variant per
	// family wins so we never preload both a .ttf and a .woff2 for the same face.
	$preload_candidates = [
		'cormorant-garamond-600' => [ 'woff2', 'ttf' ],
		'inter-400'              => [ 'woff2', 'ttf' ],
	];
	$mime = [
		'woff2' => 'font/woff2',
		'ttf'   => 'font/ttf',
	];

	foreach ( $preload_candidates as $stem => $exts ) {
		foreach ( $exts as $ext ) {
			$filename  = $stem . '.' . $ext;
			$file_path = HP_DIR . '/assets/fonts/' . $filename;
			if ( file_exists( $file_path ) ) {
				echo '<link rel="preload" href="' . esc_url( HP_URI . '/assets/fonts/' . $filename ) . '" as="font" type="' . esc_attr( $mime[ $ext ] ) . '" crossorigin>' . "\n";
				break; // only one preload per family
			}
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
