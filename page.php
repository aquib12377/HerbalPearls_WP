<?php
/**
 * Page template.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'hp-section' ); ?>>
		<div class="hp-container">
			<?php if ( ! is_front_page() ) : ?>
				<h1 class="hp-mb-2"><?php the_title(); ?></h1>
			<?php endif; ?>
			<div class="hp-post-content">
				<?php the_content(); ?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
