<?php
/**
 * Blog posts page template (used when a static page is set as "Posts page").
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<!-- Blog Page Header -->
<header class="hp-blog-header">
	<div class="hp-container">
		<h1><?php esc_html_e( 'Our Blog', 'herbalpearls' ); ?></h1>
		<p class="hp-subtitle">
			<?php esc_html_e( 'Skincare wisdom, ingredient deep-dives, and practical routines for healthy, glowing skin.', 'herbalpearls' ); ?>
		</p>
	</div>
</header>

<!-- Blog Posts Grid -->
<section class="hp-section hp-surface-page">
	<div class="hp-container">
		<?php if ( have_posts() ) : ?>
			<div class="hp-blog-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					$categories = get_the_category();
					?>
					<article <?php post_class( 'hp-blog-card' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="hp-blog-card__image">
								<a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
									<?php the_post_thumbnail( 'hp-blog-card' ); ?>
								</a>
							</div>
						<?php endif; ?>
						<div class="hp-blog-card__body">
							<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
								<div class="hp-blog-card__category">
									<a href="<?php echo esc_url( get_category_link( $categories[0] ) ); ?>">
										<?php echo esc_html( $categories[0]->name ); ?>
									</a>
								</div>
							<?php endif; ?>
							<h2 class="hp-blog-card__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>
							<div class="hp-blog-card__excerpt">
								<?php the_excerpt(); ?>
							</div>
							<div class="hp-blog-card__footer">
								<span class="hp-blog-card__date">
									<?php echo esc_html( get_the_date() ); ?>
								</span>
								<a href="<?php the_permalink(); ?>" class="hp-btn hp-btn--ghost hp-btn--sm">
									<?php esc_html_e( 'Read More', 'herbalpearls' ); ?> &rarr;
								</a>
							</div>
						</div>
					</article>
				<?php endwhile; ?>
			</div>

			<?php the_posts_pagination( [
				'mid_size'  => 2,
				'prev_text' => '&larr; ' . __( 'Previous', 'herbalpearls' ),
				'next_text' => __( 'Next', 'herbalpearls' ) . ' &rarr;',
			] ); ?>

		<?php else : ?>
			<div class="hp-text-center hp-surface-pure hp-card" style="max-width:480px;margin-inline:auto;">
				<p><?php esc_html_e( 'No posts yet. Check back soon for skincare tips and guides.', 'herbalpearls' ); ?></p>
				<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="hp-btn hp-btn--primary hp-mt-1">
					<?php esc_html_e( 'Shop Now', 'herbalpearls' ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
