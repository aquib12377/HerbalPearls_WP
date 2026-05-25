<?php
/**
 * Archive template.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<header class="hp-section hp-section--sm hp-text-center">
	<div class="hp-container">
		<?php the_archive_title( '<h1>', '</h1>' ); ?>
		<?php the_archive_description( '<p class="hp-subtitle hp-mt-1">', '</p>' ); ?>
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
					<article <?php post_class( 'hp-blog-card' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="hp-blog-card__image">
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( 'hp-blog-card' ); ?>
								</a>
							</div>
						<?php endif; ?>
						<div class="hp-blog-card__body">
							<?php
							$cats = get_the_category();
							if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) :
								?>
								<div class="hp-blog-card__category">
									<?php echo esc_html( $cats[0]->name ); ?>
								</div>
							<?php endif; ?>
							<h2 class="hp-blog-card__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>
							<div class="hp-blog-card__excerpt">
								<?php the_excerpt(); ?>
							</div>
							<div class="hp-blog-card__meta">
								<?php echo esc_html( get_the_date() ); ?>
							</div>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'No posts found.', 'herbalpearls' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
