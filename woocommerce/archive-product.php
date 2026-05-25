<?php
/**
 * Shop archive template.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

/**
 * Hook: woocommerce_before_main_content.
 */
do_action( 'woocommerce_before_main_content' );
?>

<header class="woocommerce-products-header hp-mb-2">
	<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
		<h1 class="woocommerce-products-header__title"><?php woocommerce_page_title(); ?></h1>
	<?php endif; ?>

	<?php
	do_action( 'woocommerce_archive_description' );
	?>
</header>

<div class="hp-shop-layout">
	<?php
	// Filter sidebar
	if ( is_active_sidebar( 'shop-filters' ) || has_action( 'hp_shop_filters' ) ) :
		?>
		<aside class="hp-shop-sidebar" id="shop-filters">
			<div class="hp-shop-sidebar__header hp-flex hp-flex--between">
				<h4><?php esc_html_e( 'Filters', 'herbalpearls' ); ?></h4>
				<button type="button" class="hp-btn hp-btn--ghost hp-btn--sm" data-close-filters>
					<?php esc_html_e( 'Close', 'herbalpearls' ); ?>
				</button>
			</div>
			<?php do_action( 'hp_shop_filters' ); ?>
			<?php dynamic_sidebar( 'shop-filters' ); ?>
		</aside>
	<?php endif; ?>

	<div class="hp-shop-main">
		<div class="hp-flex hp-flex--between hp-shop-toolbar hp-mb-1">
			<?php
			woocommerce_result_count();
			woocommerce_catalog_ordering();
			?>
			<button type="button" class="hp-btn hp-btn--secondary hp-btn--sm hp-filter-toggle" data-toggle="shop-filters" style="display:none;">
				<?php esc_html_e( 'Filters', 'herbalpearls' ); ?>
			</button>
		</div>

		<?php
		if ( woocommerce_product_loop() ) {
			do_action( 'woocommerce_before_shop_loop' );
			woocommerce_product_loop_start();

			if ( wc_get_loop_prop( 'total' ) ) {
				while ( have_posts() ) {
					the_post();
					do_action( 'woocommerce_shop_loop' );
					wc_get_template_part( 'content', 'product' );
				}
			}

			woocommerce_product_loop_end();
			do_action( 'woocommerce_after_shop_loop' );
		} else {
			do_action( 'woocommerce_no_products_found' );
		}
		?>
	</div>
</div>

<?php
do_action( 'woocommerce_after_main_content' );

get_footer();
