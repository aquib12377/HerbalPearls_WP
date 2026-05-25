<?php
/**
 * Single blog post template.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<article <?php post_class( 'hp-section' ); ?>>
	<div class="hp-container" style="max-width: 800px;">
		<header class="hp-mb-2 hp-text-center">
			<?php
			$categories = get_the_category();
			if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) :
				?>
				<span class="hp-badge hp-badge--new hp-mb-1">
					<?php echo esc_html( $categories[0]->name ); ?>
				</span>
			<?php endif; ?>

			<h1><?php the_title(); ?></h1>

			<div class="hp-flex hp-flex--center hp-mt-1" style="gap: 1rem; font-size: 0.875rem; color: var(--hp-text-4);">
				<span><?php echo esc_html( get_the_author_meta( 'display_name' ) ); ?></span>
				<span>&middot;</span>
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
					<?php echo esc_html( get_the_date() ); ?>
				</time>
			</div>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="hp-mb-2">
				<?php the_post_thumbnail( 'hp-blog-card', [ 'class' => 'hp-rounded' ] ); ?>
			</figure>
		<?php endif; ?>

		<div class="hp-post-content">
			<?php the_content(); ?>
		</div>

		<?php
		wp_link_pages( [
			'before' => '<nav class="hp-mt-2">' . esc_html__( 'Pages:', 'herbalpearls' ),
			'after'  => '</nav>',
		] );
		?>

		<footer class="hp-mt-2" style="padding-top: 2rem; border-top: 1px solid var(--hp-line-1);">
			<div class="hp-flex hp-flex--between">
				<div class="hp-flex" style="gap: 0.5rem;">
					<?php
					$tags = get_the_tags();
					if ( $tags && ! is_wp_error( $tags ) ) :
						foreach ( $tags as $tag ) :
							?>
							<a href="<?php echo esc_url( get_tag_link( $tag ) ); ?>" class="hp-pill">
								<?php echo esc_html( $tag->name ); ?>
							</a>
							<?php
						endforeach;
					endif;
					?>
				</div>
			</div>
		</footer>

		<?php
		// Author bio
		$author_id = get_the_author_meta( 'ID' );
		if ( get_the_author_meta( 'description', $author_id ) ) :
			?>
			<div class="hp-card hp-mt-2 hp-flex" style="align-items: flex-start; gap: 1.5rem;">
				<?php echo get_avatar( $author_id, 80, '', '', [ 'style' => 'border-radius: 50%;' ] ); ?>
				<div>
					<h4><?php echo esc_html( get_the_author_meta( 'display_name', $author_id ) ); ?></h4>
					<p style="font-size: 0.9375rem;">
						<?php echo esc_html( get_the_author_meta( 'description', $author_id ) ); ?>
					</p>
				</div>
			</div>
		<?php endif; ?>
	</div>
</article>

<?php
get_footer();
