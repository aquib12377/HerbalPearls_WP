<?php
/**
 * Cart page template.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<header class="hp-blog-header">
	<div class="hp-container">
		<h1><?php esc_html_e( 'Your Cart', 'herbalpearls' ); ?></h1>
		<?php woocommerce_breadcrumb(); ?>
	</div>
</header>

<section class="hp-section hp-surface-page">
	<div class="hp-container">
		<div class="hp-cart-layout">
			<div class="hp-cart-main">
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;
				?>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
