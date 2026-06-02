<?php
/**
 * Fallback template.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="hp-container hp-section">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class( 'hp-card hp-mb-1' ); ?>>
				<?php if ( has_post_thumbnail() ) : ?>
					<a href="<?php the_permalink(); ?>" class="hp-mb-1 hp-card-thumb-link">
						<?php the_post_thumbnail( 'hp-blog-card' ); ?>
					</a>
				<?php endif; ?>
				<h2>
					<a href="<?php the_permalink(); ?>">
						<?php the_title(); ?>
					</a>
				</h2>
				<div class="hp-mt-1">
					<?php the_excerpt(); ?>
				</div>
			</article>
			<?php
		endwhile;
		the_posts_pagination();
	else :
		?>
		<p><?php esc_html_e( 'No content found.', 'herbalpearls' ); ?></p>
	<?php endif; ?>
</div>

<?php
get_footer();
