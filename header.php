<?php
/**
 * Site header.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="hp-skip-link screen-reader-text" href="#main">
	<?php esc_html_e( 'Skip to content', 'herbalpearls' ); ?>
</a>

<?php if ( has_action( 'hp_announcement_bar' ) || is_front_page() ) : ?>
	<div class="hp-announcement" role="complementary" aria-label="<?php esc_attr_e( 'Announcement', 'herbalpearls' ); ?>">
		<?php do_action( 'hp_announcement_bar' ); ?>
		<?php if ( is_front_page() && ! has_action( 'hp_announcement_bar' ) ) : ?>
			<?php esc_html_e( 'FREE shipping above ₹499 · First order 5% OFF — code WELCOME5', 'herbalpearls' ); ?>
		<?php endif; ?>
	</div>
<?php endif; ?>

<header id="masthead" class="hp-header" role="banner">
	<div class="hp-header__inner">
		<div class="hp-header__logo">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php bloginfo( 'name' ); ?>">
				<?php if ( has_custom_logo() ) : ?>
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<span class="hp-header__site-title">
						<?php bloginfo( 'name' ); ?>
					</span>
				<?php endif; ?>
			</a>
		</div>

		<nav class="hp-header__nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'herbalpearls' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu( [
					'theme_location' => 'primary',
					'container'      => false,
					'fallback_cb'    => false,
					'depth'          => 3,
					'walker'         => class_exists( 'HP_Nav_Walker' ) ? new HP_Nav_Walker() : null,
				] );
			}
			?>
		</nav>

		<div class="hp-header__actions">
			<button
				type="button"
				class="hp-header__icon hp-menu-toggle"
				aria-label="<?php esc_attr_e( 'Menu', 'herbalpearls' ); ?>"
				aria-expanded="false"
				data-toggle="mobile-menu"
			>
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
					<line x1="3" y1="6" x2="21" y2="6" />
					<line x1="3" y1="12" x2="21" y2="12" />
					<line x1="3" y1="18" x2="21" y2="18" />
				</svg>
			</button>

			<a href="<?php echo esc_url( get_search_link() ); ?>" class="hp-header__icon" aria-label="<?php esc_attr_e( 'Search', 'herbalpearls' ); ?>">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
					<circle cx="11" cy="11" r="8" />
					<line x1="21" y1="21" x2="16.65" y2="16.65" />
				</svg>
			</a>

			<a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>" class="hp-header__icon" aria-label="<?php esc_attr_e( 'My Account', 'herbalpearls' ); ?>">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
					<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
					<circle cx="12" cy="7" r="4" />
				</svg>
			</a>

			<?php if ( function_exists( 'WC' ) ) : ?>
				<button
					type="button"
					class="hp-header__icon hp-header__cart"
					aria-label="<?php esc_attr_e( 'Cart', 'herbalpearls' ); ?>"
					data-toggle="mini-cart"
				>
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
						<circle cx="9" cy="21" r="1" />
						<circle cx="20" cy="21" r="1" />
						<path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
					</svg>
					<span class="hp-header__cart-count" data-cart-count>
						<?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?>
					</span>
				</button>
			<?php endif; ?>
		</div>
	</div>
</header>

<?php
// Mobile nav overlay
if ( has_nav_menu( 'primary' ) ) : ?>
	<div id="mobile-menu" class="hp-bottomsheet" aria-hidden="true">
		<div class="hp-bottomsheet__handle"></div>
		<nav aria-label="<?php esc_attr_e( 'Mobile navigation', 'herbalpearls' ); ?>">
			<?php
			wp_nav_menu( [
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'hp-mobile-nav',
				'fallback_cb'    => false,
				'depth'          => 2,
			] );
			?>
		</nav>
	</div>
	<div id="mobile-menu-overlay" class="hp-bottomsheet__overlay" aria-hidden="true"></div>
<?php endif; ?>

<?php if ( function_exists( 'WC' ) ) : ?>
	<?php get_template_part( 'woocommerce/cart/mini-cart' ); ?>
	<div id="mini-cart-overlay" class="hp-mini-cart-overlay" aria-hidden="true"></div>
<?php endif; ?>

<main id="main">
