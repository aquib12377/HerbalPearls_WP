<?php
/**
 * 404 template.
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
		<h1><?php esc_html_e( 'Page not found', 'herbalpearls' ); ?></h1>
		<p class="hp-subtitle">
			<?php esc_html_e( 'The page you are looking for does not exist or has been moved.', 'herbalpearls' ); ?>
		</p>
	</div>
</header>

<section class="hp-section hp-surface-page hp-text-center">
	<div class="hp-container">
		<div>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hp-btn hp-btn--primary">
				<?php esc_html_e( 'Return Home', 'herbalpearls' ); ?>
			</a>
		</div>
		<div class="hp-mt-2">
			<?php get_search_form(); ?>
		</div>
	</div>
</section>

<?php
get_footer();
