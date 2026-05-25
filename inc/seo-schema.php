<?php
/**
 * Custom JSON-LD schema for Herbal Pearls.
 * Replaces Rank Math Pro features.
 * From v2 Appendix A.2 — verbatim.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ─────────────── Helper: emit JSON-LD ─────────────── */
function hp_emit_jsonld( $schema ) {
	if ( empty( $schema ) ) {
		return;
	}
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — JSON-LD schema output
}

/* ─────────────── Organization (homepage only) ─────────────── */
add_action( 'wp_head', function() {
	if ( ! is_front_page() ) {
		return;
	}
	hp_emit_jsonld( [
		'@context' => 'https://schema.org',
		'@type'    => 'Organization',
		'name'     => 'Herbal Pearls',
		'url'      => home_url( '/' ),
		'logo'     => HP_URI . '/assets/images/logo-512.png',
		'sameAs'   => array_filter( [
			'https://www.instagram.com/herbalpearls/',
			'https://www.facebook.com/herbalpearls/',
		] ),
		'contactPoint' => [
			'@type'             => 'ContactPoint',
			'telephone'         => '+91-XXXXXXXXXX',
			'contactType'       => 'customer service',
			'areaServed'        => 'IN',
			'availableLanguage' => [ 'en', 'hi' ],
		],
	] );
}, 30 );

/* ─────────────── WebSite + SearchAction (homepage) ─────────────── */
add_action( 'wp_head', function() {
	if ( ! is_front_page() ) {
		return;
	}
	hp_emit_jsonld( [
		'@context'        => 'https://schema.org',
		'@type'           => 'WebSite',
		'url'             => home_url( '/' ),
		'name'            => 'Herbal Pearls',
		'potentialAction' => [
			'@type'       => 'SearchAction',
			'target'      => home_url( '/?s={search_term_string}' ),
			'query-input' => 'required name=search_term_string',
		],
	] );
}, 31 );

/* ─────────────── Product (PDP) ─────────────── */
add_action( 'wp_head', function() {
	if ( ! is_singular( 'product' ) ) {
		return;
	}
	global $post;
	$product = wc_get_product( $post->ID );
	if ( ! $product ) {
		return;
	}

	$images = array_filter( [ wp_get_attachment_url( $product->get_image_id() ) ] );
	foreach ( $product->get_gallery_image_ids() as $gid ) {
		$url = wp_get_attachment_url( $gid );
		if ( $url ) {
			$images[] = $url;
		}
	}

	$schema = [
		'@context'    => 'https://schema.org',
		'@type'       => 'Product',
		'name'        => $product->get_name(),
		'description' => wp_strip_all_tags( $product->get_short_description() ?: $product->get_description() ),
		'image'       => array_values( $images ),
		'sku'         => $product->get_sku() ?: 'HP-' . $product->get_id(),
		'brand'       => [ '@type' => 'Brand', 'name' => 'Herbal Pearls' ],
		'category'    => wp_strip_all_tags( wc_get_product_category_list( $product->get_id(), ', ', '', '' ) ),
	];

	if ( $product->is_type( 'variable' ) ) {
		$schema['offers'] = [
			'@type'         => 'AggregateOffer',
			'priceCurrency' => 'INR',
			'lowPrice'      => $product->get_variation_price( 'min', true ),
			'highPrice'     => $product->get_variation_price( 'max', true ),
			'offerCount'    => count( $product->get_children() ),
			'availability'  => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
			'url'           => get_permalink( $product->get_id() ),
		];
	} else {
		$schema['offers'] = [
			'@type'         => 'Offer',
			'priceCurrency' => 'INR',
			'price'         => $product->get_price(),
			'availability'  => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
			'url'           => get_permalink( $product->get_id() ),
		];
	}

	if ( $product->get_review_count() > 0 ) {
		$schema['aggregateRating'] = [
			'@type'       => 'AggregateRating',
			'ratingValue' => (float) $product->get_average_rating(),
			'reviewCount' => (int) $product->get_review_count(),
			'bestRating'  => '5',
			'worstRating' => '1',
		];
	}

	hp_emit_jsonld( $schema );

	// Suppress Rank Math's competing Product schema to avoid duplication
	add_filter( 'rank_math/snippet/rich_snippet_product_entity', '__return_empty_array' );
}, 30 );

/* ─────────────── BreadcrumbList (everywhere except home) ─────────────── */
add_action( 'wp_head', function() {
	if ( is_front_page() ) {
		return;
	}

	$items = hp_build_breadcrumb_trail();
	if ( count( $items ) < 2 ) {
		return;
	}

	$list = [];
	foreach ( $items as $i => $item ) {
		$list[] = [
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => $item['name'],
			'item'     => $item['url'],
		];
	}
	hp_emit_jsonld( [
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $list,
	] );
}, 31 );

function hp_build_breadcrumb_trail() {
	$crumbs = [ [ 'name' => __( 'Home', 'herbalpearls' ), 'url' => home_url( '/' ) ] ];

	if ( is_singular( 'product' ) ) {
		$cats = get_the_terms( get_the_ID(), 'product_cat' );
		if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {
			$cat = $cats[0];
			$crumbs[] = [ 'name' => $cat->name, 'url' => get_term_link( $cat ) ];
		}
		$crumbs[] = [ 'name' => get_the_title(), 'url' => get_permalink() ];
	} elseif ( is_product_category() ) {
		$term = get_queried_object();
		$crumbs[] = [ 'name' => __( 'Shop', 'herbalpearls' ), 'url' => get_permalink( wc_get_page_id( 'shop' ) ) ];
		$crumbs[] = [ 'name' => $term->name, 'url' => get_term_link( $term ) ];
	} elseif ( is_singular( 'post' ) ) {
		$cats = get_the_category();
		if ( ! empty( $cats ) ) {
			$crumbs[] = [ 'name' => $cats[0]->name, 'url' => get_category_link( $cats[0] ) ];
		}
		$crumbs[] = [ 'name' => get_the_title(), 'url' => get_permalink() ];
	} elseif ( is_singular( 'hp_bundle' ) ) {
		$crumbs[] = [ 'name' => __( 'Bundles', 'herbalpearls' ), 'url' => home_url( '/bundles/' ) ];
		$crumbs[] = [ 'name' => get_the_title(), 'url' => get_permalink() ];
	} elseif ( is_page() ) {
		$crumbs[] = [ 'name' => get_the_title(), 'url' => get_permalink() ];
	} elseif ( is_search() ) {
		$crumbs[] = [ 'name' => __( 'Search results', 'herbalpearls' ), 'url' => home_url( '/?s=' . get_search_query() ) ];
	} elseif ( is_404() ) {
		$crumbs[] = [ 'name' => __( 'Not found', 'herbalpearls' ), 'url' => home_url( '/' ) ];
	}
	return $crumbs;
}


/* Bundle Product schema */
add_action( 'wp_head', function() {
	if ( ! is_singular( 'hp_bundle' ) ) {
		return;
	}
	$bundle_id = get_the_ID();
	$items     = get_post_meta( $bundle_id, '_hp_bundle_items', true ) ?: [];
	$discount  = absint( get_post_meta( $bundle_id, '_hp_bundle_discount_pct', true ) ?: 10 );

	$total_mrp = 0;
	foreach ( $items as $item ) {
		$pid = absint( $item['product_id'] ?? 0 );
		$p   = wc_get_product( $pid );
		if ( $p ) {
			$total_mrp += (float) $p->get_regular_price() * absint( $item['qty'] ?? 1 );
		}
	}
	$discounted = $total_mrp * ( 1 - $discount / 100 );

	$image = has_post_thumbnail() ? [ get_the_post_thumbnail_url( null, 'full' ) ] : [];

	hp_emit_jsonld( [
		'@context'    => 'https://schema.org',
		'@type'       => 'Product',
		'name'        => get_the_title(),
		'description' => wp_strip_all_tags( get_the_excerpt() ?: '' ),
		'image'       => $image,
		'sku'         => 'HP-BNDL-' . $bundle_id,
		'brand'       => [ '@type' => 'Brand', 'name' => 'Herbal Pearls' ],
		'offers'      => [
			'@type'         => 'Offer',
			'priceCurrency' => 'INR',
			'price'         => (string) $discounted,
			'availability'  => 'https://schema.org/InStock',
			'url'           => get_permalink(),
		],
	] );
}, 30 );
	/* ─────────────── FAQ schema (PDP + blog if FAQs present) ─────────────── */
add_action( 'wp_head', function() {
	if ( ! is_singular( [ 'product', 'post', 'hp_bundle' ] ) ) {
		return;
	}

	$faqs = get_post_meta( get_the_ID(), '_hp_faqs', true );
	if ( empty( $faqs ) || ! is_array( $faqs ) ) {
		return;
	}

	$entities = [];
	foreach ( $faqs as $faq ) {
		if ( empty( $faq['question'] ) || empty( $faq['answer'] ) ) {
			continue;
		}
		$entities[] = [
			'@type'          => 'Question',
			'name'           => $faq['question'],
			'acceptedAnswer' => [
				'@type' => 'Answer',
				'text'  => wp_strip_all_tags( $faq['answer'] ),
			],
		];
	}
	if ( empty( $entities ) ) {
		return;
	}

	hp_emit_jsonld( [
		'@context'  => 'https://schema.org',
		'@type'     => 'FAQPage',
		'mainEntity' => $entities,
	] );
}, 32 );

/* ─────────────── Article (blog posts) ─────────────── */
add_action( 'wp_head', function() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}
	global $post;

	$featured = get_the_post_thumbnail_url( $post, 'full' );

	hp_emit_jsonld( [
		'@context'         => 'https://schema.org',
		'@type'            => 'Article',
		'headline'         => get_the_title(),
		'image'            => $featured ? [ $featured ] : [],
		'datePublished'    => get_the_date( 'c' ),
		'dateModified'     => get_the_modified_date( 'c' ),
		'author'           => [
			'@type' => 'Person',
			'name'  => get_the_author_meta( 'display_name', $post->post_author ),
			'url'   => get_author_posts_url( $post->post_author ),
		],
		'publisher'        => [
			'@type' => 'Organization',
			'name'  => 'Herbal Pearls',
			'logo'  => [
				'@type' => 'ImageObject',
				'url'   => HP_URI . '/assets/images/logo-512.png',
			],
		],
		'mainEntityOfPage' => get_permalink(),
	] );
}, 33 );
