<?php
/**
 * Site footer.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

</main><!-- #main -->

<footer class="hp-footer" role="contentinfo">
	<div class="hp-footer__inner">
		<div class="hp-footer__grid">
			<div class="hp-footer__col">
				<div class="hp-footer__logo hp-mb-1">
					<?php if ( has_custom_logo() ) : ?>
						<?php the_custom_logo(); ?>
					<?php else : ?>
						<span class="hp-footer__site-title">
							<?php bloginfo( 'name' ); ?>
						</span>
					<?php endif; ?>
				</div>
				<p class="hp-footer__tagline" style="font-size: 0.9375rem;">
					<?php esc_html_e( 'Herbal skincare that works. Formulated with proven natural ingredients, backed by science.', 'herbalpearls' ); ?>
				</p>
				<div class="hp-footer__social">
					<?php
					$social = [
						'instagram' => '#',
						'facebook'  => '#',
						'youtube'   => '#',
					];
					foreach ( $social as $platform => $url ) :
						?>
						<a href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( ucfirst( $platform ) ); ?>" target="_blank" rel="noopener noreferrer">
							<span aria-hidden="true"><?php echo esc_html( strtoupper( $platform[0] ) ); ?></span>
						</a>
					<?php endforeach; ?>
				</div>
			</div>

			<?php if ( has_nav_menu( 'footer-1' ) ) : ?>
				<div class="hp-footer__col">
					<h4 class="hp-footer__heading"><?php esc_html_e( 'Shop', 'herbalpearls' ); ?></h4>
					<?php
					wp_nav_menu( [
						'theme_location' => 'footer-1',
						'container'      => false,
						'fallback_cb'    => false,
						'depth'          => 1,
					] );
					?>
				</div>
			<?php endif; ?>

			<?php if ( has_nav_menu( 'footer-2' ) ) : ?>
				<div class="hp-footer__col">
					<h4 class="hp-footer__heading"><?php esc_html_e( 'Help', 'herbalpearls' ); ?></h4>
					<?php
					wp_nav_menu( [
						'theme_location' => 'footer-2',
						'container'      => false,
						'fallback_cb'    => false,
						'depth'          => 1,
					] );
					?>
				</div>
			<?php endif; ?>

			<?php if ( has_nav_menu( 'footer-3' ) ) : ?>
				<div class="hp-footer__col">
					<h4 class="hp-footer__heading"><?php esc_html_e( 'About', 'herbalpearls' ); ?></h4>
					<?php
					wp_nav_menu( [
						'theme_location' => 'footer-3',
						'container'      => false,
						'fallback_cb'    => false,
						'depth'          => 1,
					] );
					?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<div class="hp-footer__bottom">
		<p>
			&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>.
			<?php esc_html_e( 'All rights reserved.', 'herbalpearls' ); ?>
		</p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
