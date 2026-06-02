<?php
/**
 * Page template — used for static pages (Privacy Policy, Refund, Shipping, etc.).
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	// Front page is handled by front-page.php — guard just in case.
	if ( is_front_page() ) :
		?>
		<section class="hp-section">
			<div class="hp-container hp-post-content">
				<?php the_content(); ?>
			</div>
		</section>
		<?php
		continue;
	endif;
	?>

	<header class="hp-blog-header">
		<div class="hp-container">
			<h1><?php the_title(); ?></h1>
		</div>
	</header>

	<section class="hp-section hp-surface-page">
		<article <?php post_class(); ?>>
			<div class="hp-container hp-container--prose hp-post-content">
				<?php the_content(); ?>
			</div>
		</article>
	</section>

<?php
endwhile;

get_footer();
