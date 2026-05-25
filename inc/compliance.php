<?php
/**
 * Inject regulatory disclaimers on PDPs and blog posts.
 * From v2 Appendix A.5 — verbatim.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'woocommerce_after_single_product_summary', 'hp_render_disclaimer', 50 );
add_filter( 'the_content', function( $content ) {
	if ( is_singular( 'post' ) ) {
		ob_start();
		hp_render_disclaimer();
		$content .= ob_get_clean();
	}
	return $content;
}, 99 );

function hp_render_disclaimer() {
	$is_ingestible = false;
	if ( is_singular( 'product' ) ) {
		$product = wc_get_product( get_the_ID() );
		if ( $product ) {
			$cats = wp_get_post_terms( $product->get_id(), 'product_cat', [ 'fields' => 'slugs' ] );
			$is_ingestible = in_array( 'wellness', $cats, true );
		}
	}
	?>
	<aside class="hp-disclaimer">
		<h4><?php esc_html_e( 'Disclaimer', 'herbalpearls' ); ?></h4>
		<p><?php esc_html_e( 'Statements and product information have not been evaluated by any regulatory authority unless specified. Herbal Pearls products are intended for cosmetic use only and are not a substitute for medical advice, diagnosis, or treatment. Individual results may vary. Patch-test before first use.', 'herbalpearls' ); ?></p>
		<?php if ( $is_ingestible ) : ?>
		<p><strong><?php esc_html_e( 'For ingestible products:', 'herbalpearls' ); ?></strong> <?php esc_html_e( 'Keep out of reach of children. Not recommended for those under 18, pregnant, or nursing. Discontinue use if any adverse reaction occurs. Consult a qualified practitioner before use.', 'herbalpearls' ); ?></p>
		<?php endif; ?>
	</aside>
	<?php
}
