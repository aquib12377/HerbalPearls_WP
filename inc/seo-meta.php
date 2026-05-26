<?php
/**
 * SEO meta — Open Graph, Twitter Card, meta description, canonical fallback.
 *
 * Defers to Yoast SEO and Rank Math when present so we never duplicate tags.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Detect whether a third-party SEO plugin is active so we step out of the way.
 *
 * @return bool
 */
function hp_seo_plugin_active() {
	return (
		defined( 'WPSEO_VERSION' )                           // Yoast SEO
		|| class_exists( 'RankMath' )                        // Rank Math
		|| defined( 'AIOSEO_VERSION' )                       // All in One SEO
		|| defined( 'THE_SEO_FRAMEWORK_PRESENT' )            // The SEO Framework
	);
}

/**
 * Build a description for the current request.
 *
 * @return string Plain text, no tags.
 */
function hp_seo_get_description() {
	$desc = '';

	if ( is_singular() ) {
		global $post;
		if ( has_excerpt( $post ) ) {
			$desc = wp_strip_all_tags( get_the_excerpt( $post ) );
		} elseif ( ! empty( $post->post_content ) ) {
			$desc = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 30, '…' );
		}

		if ( '' === $desc && function_exists( 'wc_get_product' ) && is_product() ) {
			$product = wc_get_product( $post->ID );
			if ( $product ) {
				$desc = wp_strip_all_tags( $product->get_short_description() ?: $product->get_description() );
				$desc = wp_trim_words( $desc, 30, '…' );
			}
		}
	} elseif ( is_category() || is_tag() || is_tax() || is_product_category() || is_product_tag() ) {
		$term = get_queried_object();
		if ( $term && ! empty( $term->description ) ) {
			$desc = wp_strip_all_tags( $term->description );
		}
	} elseif ( is_search() ) {
		/* translators: %s: search query */
		$desc = sprintf( __( 'Search results for "%s".', 'herbalpearls' ), get_search_query() );
	}

	if ( '' === $desc ) {
		$desc = get_bloginfo( 'description', 'display' );
	}

	return trim( $desc );
}

/**
 * Best image URL for the current request (OG / Twitter card preview).
 *
 * @return string
 */
function hp_seo_get_image() {
	if ( is_singular() && has_post_thumbnail() ) {
		$src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
		if ( $src ) {
			return $src[0];
		}
	}

	// Site-wide fallback: theme logo (if present) → site icon → empty.
	$logo = HP_DIR . '/assets/images/logo-512.png';
	if ( file_exists( $logo ) ) {
		return HP_URI . '/assets/images/logo-512.png';
	}

	$icon_id = get_option( 'site_icon' );
	if ( $icon_id ) {
		$icon = wp_get_attachment_image_src( $icon_id, 'full' );
		if ( $icon ) {
			return $icon[0];
		}
	}

	return '';
}

/**
 * Best URL for the current request.
 *
 * @return string
 */
function hp_seo_get_url() {
	if ( is_singular() ) {
		return get_permalink();
	}
	if ( is_category() || is_tag() || is_tax() ) {
		$link = get_term_link( get_queried_object() );
		return is_wp_error( $link ) ? home_url( add_query_arg( null, null ) ) : $link;
	}
	if ( is_post_type_archive() ) {
		return get_post_type_archive_link( get_post_type() );
	}
	if ( is_front_page() || is_home() ) {
		return home_url( '/' );
	}
	// Fallback — strip query params for canonical-ish URL.
	$path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) : '/';
	return home_url( $path );
}

/**
 * Page type for og:type.
 *
 * @return string
 */
function hp_seo_get_og_type() {
	if ( is_front_page() ) {
		return 'website';
	}
	if ( is_singular( 'post' ) ) {
		return 'article';
	}
	if ( function_exists( 'is_product' ) && is_product() ) {
		return 'product';
	}
	return 'website';
}

/* ─────────────── Emit description + OG + Twitter ─────────────── */
add_action( 'wp_head', function() {
	if ( hp_seo_plugin_active() ) {
		return; // Yoast / Rank Math handles this.
	}

	$desc  = hp_seo_get_description();
	$img   = hp_seo_get_image();
	$url   = hp_seo_get_url();
	$type  = hp_seo_get_og_type();
	$title = wp_get_document_title();
	$site  = get_bloginfo( 'name' );

	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
	}

	echo '<meta property="og:site_name" content="' . esc_attr( $site ) . '">' . "\n";
	echo '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $desc ) {
		echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	if ( $url ) {
		echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	}
	if ( $img ) {
		echo '<meta property="og:image" content="' . esc_url( $img ) . '">' . "\n";
	}
	echo '<meta property="og:locale" content="' . esc_attr( get_locale() ) . '">' . "\n";

	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $desc ) {
		echo '<meta name="twitter:description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	if ( $img ) {
		echo '<meta name="twitter:image" content="' . esc_url( $img ) . '">' . "\n";
	}
}, 2 );

/* ─────────────── Canonical fallback (search, paged archives) ─────────────── */
add_action( 'wp_head', function() {
	if ( hp_seo_plugin_active() ) {
		return;
	}
	// WordPress core already emits canonical on is_singular() — fill the gaps here.
	if ( is_singular() ) {
		return;
	}

	$canonical = '';
	if ( is_search() ) {
		$canonical = home_url( '/?s=' . rawurlencode( get_search_query() ) );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$link = get_term_link( get_queried_object() );
		if ( ! is_wp_error( $link ) ) {
			$canonical = $link;
		}
	} elseif ( is_post_type_archive() ) {
		$canonical = get_post_type_archive_link( get_post_type() );
	} elseif ( is_home() ) {
		$page_for_posts = get_option( 'page_for_posts' );
		$canonical      = $page_for_posts ? get_permalink( $page_for_posts ) : home_url( '/' );
	} elseif ( is_front_page() ) {
		$canonical = home_url( '/' );
	}

	if ( $canonical ) {
		// Append paged segment if present.
		$paged = get_query_var( 'paged' );
		if ( $paged > 1 ) {
			$canonical = trailingslashit( $canonical ) . 'page/' . absint( $paged ) . '/';
		}
		echo '<link rel="canonical" href="' . esc_url( $canonical ) . '">' . "\n";
	}
}, 3 );
