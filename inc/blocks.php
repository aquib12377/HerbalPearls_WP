<?php
/**
 * Custom Gutenberg blocks — category registration, block skeletons.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register custom block category "Herbal Pearls".
 */
add_filter( 'block_categories_all', function( $categories, $block_editor_context ) {
	if ( ! ( $block_editor_context instanceof WP_Block_Editor_Context ) ) {
		return $categories;
	}
	if ( empty( $block_editor_context->post ) ) {
		return $categories;
	}

	return array_merge( $categories, [
		[
			'slug'  => 'herbalpearls',
			'title' => __( 'Herbal Pearls', 'herbalpearls' ),
			'icon'  => null,
		],
	] );
}, 10, 2 );

/**
 * Register blocks.
 * Blocks to be built post-launch:
 *   - hp/trust-strip
 *   - hp/product-card
 *   - hp/bundle-promo
 *   - hp/ingredient-chip
 *   - hp/review-card
 *   - hp/category-triptych
 */
add_action( 'init', function() {
	// Block registration placeholder — register blocks here as they are built
	// Example:
	// register_block_type( HP_DIR . '/assets/blocks/trust-strip' );
} );
