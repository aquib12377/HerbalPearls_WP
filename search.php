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

<header class="hp-blog-header">
	<div class="hp-container">
		<h1>
			<?php
			printf(
				/* translators: %s: search query */
				esc_html__( 'Search results for "%s"', 'herbalpearls' ),
				esc_html( get_search_query() )
			);
			?>
		</h1>
		<div class="hp-mt-1" style="max-width: 480px; margin-inline: auto;">
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
						<div class="hp-mt-1">
							<?php the_excerpt(); ?>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<div class="hp-empty-state hp-card hp-surface-pure">
				<p><?php esc_html_e( 'No results found. Try a different search term.', 'herbalpearls' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
