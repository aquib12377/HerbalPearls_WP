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

while ( have_posts() ) :
	the_post();
	?>

	<header class="hp-blog-header">
		<div class="hp-container hp-container--narrow">
			<?php
			$categories = get_the_category();
			if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) :
				?>
				<span class="hp-badge hp-badge--new hp-mb-1">
					<?php echo esc_html( $categories[0]->name ); ?>
				</span>
			<?php endif; ?>

			<h1><?php the_title(); ?></h1>

			<div class="hp-post-meta hp-mt-1">
				<span><?php echo esc_html( get_the_author_meta( 'display_name' ) ); ?></span>
				<span aria-hidden="true">&middot;</span>
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
					<?php echo esc_html( get_the_date() ); ?>
				</time>
			</div>
		</div>
	</header>

	<section class="hp-section hp-surface-page">
		<article <?php post_class(); ?>>
			<div class="hp-container hp-container--narrow">
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

				<?php
				$tags = get_the_tags();
				if ( $tags && ! is_wp_error( $tags ) ) :
					?>
					<footer class="hp-post-footer">
						<div class="hp-post-tags">
							<?php foreach ( $tags as $tag ) : ?>
								<a href="<?php echo esc_url( get_tag_link( $tag ) ); ?>" class="hp-pill">
									<?php echo esc_html( $tag->name ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					</footer>
				<?php endif; ?>

				<?php
				$author_id = get_the_author_meta( 'ID' );
				if ( get_the_author_meta( 'description', $author_id ) ) :
					?>
					<aside class="hp-card hp-post-author">
						<div class="hp-post-author__avatar">
							<?php echo get_avatar( $author_id, 80 ); ?>
						</div>
						<div class="hp-post-author__body">
							<h4><?php echo esc_html( get_the_author_meta( 'display_name', $author_id ) ); ?></h4>
							<p><?php echo esc_html( get_the_author_meta( 'description', $author_id ) ); ?></p>
						</div>
					</aside>
				<?php endif; ?>
			</div>
		</article>
	</section>

	<?php
endwhile;

get_footer();
