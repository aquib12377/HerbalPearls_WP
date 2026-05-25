<?php
/**
 * Search results template.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<header class="hp-section hp-section--sm">
	<div class="hp-container hp-text-center">
		<h1>
			<?php
			printf(
				/* translators: %s: search query */
				esc_html__( 'Search results for "%s"', 'herbalpearls' ),
				esc_html( get_search_query() )
			);
			?>
		</h1>
		<div class="hp-mt-1">
			<?php get_search_form(); ?>
		</div>
	</div>
</header>

<section class="hp-section hp-surface-page">
	<div class="hp-container">
		<?php if ( have_posts() ) : ?>
			<div class="hp-grid-3">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article <?php post_class( 'hp-card' ); ?>>
						<h3>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h3>
						<div class="hp-mt-1" style="font-size: 0.9375rem;">
							<?php the_excerpt(); ?>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'No results found. Try a different search term.', 'herbalpearls' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
