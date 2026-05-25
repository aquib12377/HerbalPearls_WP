<?php
/**
 * Homepage template — v3 §6.1 rhythm with v2 §5.1 structure.
 *
 * Section order and bg alternation per v3 §6.1:
 *   [1] Announcement bar — in header.php
 *   [2] Header              — in header.php
 *   [3] Hero banner         — full bleed, uploaded banners
 *   [4] Trust strip         — bg --hp-bg-1
 *   [5] Category triptych   — bg --hp-bg-page, white cards
 *   [6] Bestsellers         — bg --hp-bg-2 (deeper champagne band)
 *   [7] Bundle promo        — bg --hp-bg-page, gold-bordered cards
 *   [8] Ingredient story    — bg alternating: bg-1 → bg-pure → bg-1
 *   [9] Reviews             — bg --hp-bg-2, white review cards
 *   [10] Blog highlight     — bg --hp-bg-page
 *   [11] Newsletter         — bg --hp-bg-3 sandstone
 *   [12] Footer             — in footer.php
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<!-- [3] Hero Banner -->
<?php
// Build enabled slides from Customizer data.
// Image is a plain URL (no class dependencies — works on every host).
$hero_slides = [];
for ( $i = 1; $i <= 3; $i++ ) {
	$img_url = get_theme_mod( "hp_hero_image_url_$i", '' );
	if ( $img_url ) {
		$hero_slides[ $i ] = [
			'img_url'   => $img_url,
			'headline'  => get_theme_mod( "hp_hero_headline_$i", '' ),
			'subtitle'  => get_theme_mod( "hp_hero_subtitle_$i", '' ),
			'cta_text'  => get_theme_mod( "hp_hero_cta_text_$i", __( 'Shop Now', 'herbalpearls' ) ),
			'cta_url'   => get_theme_mod( "hp_hero_cta_url_$i", get_permalink( wc_get_page_id( 'shop' ) ) ),
		];
	}
}
$has_slides = ! empty( $hero_slides );
?>
<section class="hp-hero" aria-label="<?php esc_attr_e( 'Featured', 'herbalpearls' ); ?>">
	<div class="hp-hero__slider" data-hero-slider>
		<?php if ( $has_slides ) : ?>
			<?php $idx = 0; foreach ( $hero_slides as $slide ) : ?>
				<div class="hp-hero__slide" <?php echo ( 0 === $idx ) ? '' : 'hidden'; ?>>
					<?php if ( $slide['img_url'] ) : ?>
						<img
							src="<?php echo esc_url( $slide['img_url'] ); ?>"
							alt=""
							width="1920"
							height="720"
							fetchpriority="<?php echo ( 0 === $idx ) ? 'high' : 'low'; ?>"
						>
					<?php endif; ?>
					<div class="hp-hero__content">
						<?php if ( $slide['headline'] ) : ?>
							<h1 class="hp-script hp-mb-1"><?php echo esc_html( $slide['headline'] ); ?></h1>
						<?php endif; ?>
						<?php if ( $slide['subtitle'] ) : ?>
							<p class="hp-subtitle"><?php echo esc_html( $slide['subtitle'] ); ?></p>
						<?php endif; ?>
						<?php if ( $slide['cta_text'] && $slide['cta_url'] ) : ?>
							<div class="hp-mt-2">
								<a href="<?php echo esc_url( $slide['cta_url'] ); ?>" class="hp-btn hp-btn--primary hp-btn--lg">
									<?php echo esc_html( $slide['cta_text'] ); ?>
								</a>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<?php $idx++; endforeach; ?>
		<?php else : ?>
			<div class="hp-hero__slide">
				<div class="hp-hero__content">
					<h1 class="hp-script hp-mb-1">
						<?php esc_html_e( "Nature's Touch, Visible Glow", 'herbalpearls' ); ?>
					</h1>
					<p class="hp-subtitle">
						<?php esc_html_e( 'Herbal skincare backed by science. Real ingredients, real results.', 'herbalpearls' ); ?>
					</p>
					<div class="hp-mt-2">
						<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="hp-btn hp-btn--primary hp-btn--lg">
							<?php esc_html_e( 'Shop Now', 'herbalpearls' ); ?>
						</a>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>

<!-- [4] Trust Strip — bg-1 -->
<section class="hp-trust-strip" aria-label="<?php esc_attr_e( 'What we stand for', 'herbalpearls' ); ?>">
	<div class="hp-trust-strip__inner">
		<div class="hp-trust-item">
			<svg class="hp-trust-item__icon" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true">
				<path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3 17 5 13 9 13 15s-3 5-5 5c-.63 0-1.05-.05-1.63-.2" fill="none" stroke="currentColor" stroke-width="1.5"/>
			</svg>
			<span><?php esc_html_e( '100% Natural', 'herbalpearls' ); ?></span>
		</div>
		<div class="hp-trust-item">
			<svg class="hp-trust-item__icon" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true">
				<circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="1.5"/>
				<line x1="12" y1="7" x2="12" y2="7" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
				<line x1="12" y1="17" x2="12" y2="17" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
			</svg>
			<span><?php esc_html_e( 'Paraben Free', 'herbalpearls' ); ?></span>
		</div>
		<div class="hp-trust-item">
			<svg class="hp-trust-item__icon" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true">
				<path d="M21 12c0 5-4 9-9 9s-9-4-9-9 4-9 9-9" fill="none" stroke="currentColor" stroke-width="1.5"/>
				<circle cx="9" cy="8.5" r="1.5" fill="currentColor" stroke="none"/>
				<path d="M15 12l-3-1.5V7" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
			<span><?php esc_html_e( 'Cruelty Free', 'herbalpearls' ); ?></span>
		</div>
		<div class="hp-trust-item">
			<svg class="hp-trust-item__icon" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true">
				<rect x="1" y="3" width="15" height="13" rx="2" fill="none" stroke="currentColor" stroke-width="1.5"/>
				<path d="M16 8h5l2 4-2 4h-5" fill="none" stroke="currentColor" stroke-width="1.5"/>
				<circle cx="5.5" cy="18.5" r="2.5" fill="none" stroke="currentColor" stroke-width="1.5"/>
				<circle cx="16.5" cy="18.5" r="2.5" fill="none" stroke="currentColor" stroke-width="1.5"/>
			</svg>
			<span><?php esc_html_e( 'Pan-India Shipping', 'herbalpearls' ); ?></span>
		</div>
	</div>
</section>

<!-- [5] Category Triptych — bg-page -->
<section class="hp-section">
	<div class="hp-container">
		<h2 class="hp-text-center hp-mb-2">
			<?php esc_html_e( 'Shop by Category', 'herbalpearls' ); ?>
		</h2>
		<div class="hp-grid-3">
			<?php
			$categories = [ 'skin' => 'Skin', 'hair' => 'Hair', 'wellness' => 'Wellness' ];
			foreach ( $categories as $slug => $label ) :
				$term = get_term_by( 'slug', $slug, 'product_cat' );
				$url  = $term && ! is_wp_error( $term ) ? get_term_link( $term ) : '#';
				$img_id = $term ? get_term_meta( $term->term_id, 'thumbnail_id', true ) : 0;
				?>
				<a href="<?php echo esc_url( $url ); ?>" class="hp-card hp-card--hover hp-text-center hp-surface-pure">
					<?php if ( $img_id ) : ?>
						<?php echo wp_get_attachment_image( $img_id, 'hp-product-card', false, [ 'style' => 'border-radius:8px;margin-bottom:1rem;' ] ); ?>
					<?php endif; ?>
					<h3><?php echo esc_html( $label ); ?></h3>
					<span class="hp-btn hp-btn--ghost hp-mt-1">
						<?php esc_html_e( 'Explore', 'herbalpearls' ); ?> &rarr;
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- [6] Bestsellers — bg-2 (deeper champagne band) -->
<section class="hp-section hp-surface-2">
	<div class="hp-container">
		<h2 class="hp-text-center hp-mb-2">
			<?php esc_html_e( 'Bestsellers', 'herbalpearls' ); ?>
		</h2>
		<?php echo do_shortcode( '[hp_bestsellers count="4"]' ); ?>
	</div>
</section>

<!-- [7] Bundle Promo — bg-page -->
<section class="hp-section hp-surface-page">
	<div class="hp-container">
		<div class="hp-text-center hp-mb-2">
			<h2><?php esc_html_e( 'Save 10% on Curated Combos', 'herbalpearls' ); ?></h2>
			<p class="hp-subtitle hp-mt-1">
				<?php esc_html_e( 'Hand-picked bundles for every routine.', 'herbalpearls' ); ?>
			</p>
		</div>
		<?php echo do_shortcode( '[hp_bundle_promo count="3"]' ); ?>
	</div>
</section>

<!-- [8] Ingredient Story — alternating bg-1 → bg-pure → bg-1 -->
<section class="hp-section hp-surface-1">
	<div class="hp-container">
		<div class="hp-text-center hp-mb-2">
			<h2><?php esc_html_e( 'Ingredients That Work', 'herbalpearls' ); ?></h2>
			<p class="hp-subtitle hp-mt-1">
				<?php esc_html_e( 'Each ingredient is chosen for a reason.', 'herbalpearls' ); ?>
			</p>
		</div>
		<div class="hp-grid-2">
			<div class="hp-card hp-surface-pure hp-text-center">
				<div class="hp-ingredient-chip hp-mb-1">
					<svg class="hp-ingredient-chip__icon" width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
						<path d="M12 2C8 6 2 10 2 14c0 5.5 4.5 8 10 8s10-2.5 10-8c0-4-6-8-10-12z" fill="none" stroke="currentColor" stroke-width="1.5"/>
					</svg>
					<span><?php esc_html_e( 'Rosemary', 'herbalpearls' ); ?></span>
				</div>
				<p><?php esc_html_e( 'Stimulates hair follicles and improves scalp circulation for visibly thicker, healthier hair.', 'herbalpearls' ); ?></p>
			</div>
			<div class="hp-card hp-surface-pure hp-text-center">
				<div class="hp-ingredient-chip hp-mb-1">
					<svg class="hp-ingredient-chip__icon" width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
						<path d="M12 2C8 6 2 10 2 14c0 5.5 4.5 8 10 8s10-2.5 10-8c0-4-6-8-10-12z" fill="none" stroke="currentColor" stroke-width="1.5"/>
					</svg>
					<span><?php esc_html_e( 'Niacinamide', 'herbalpearls' ); ?></span>
				</div>
				<p><?php esc_html_e( 'Brightens skin tone, minimizes pores, and strengthens the skin barrier — the multi-tasker every routine needs.', 'herbalpearls' ); ?></p>
			</div>
			<div class="hp-card hp-surface-pure hp-text-center">
				<div class="hp-ingredient-chip hp-mb-1">
					<svg class="hp-ingredient-chip__icon" width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
						<path d="M12 2C8 6 2 10 2 14c0 5.5 4.5 8 10 8s10-2.5 10-8c0-4-6-8-10-12z" fill="none" stroke="currentColor" stroke-width="1.5"/>
					</svg>
					<span><?php esc_html_e( 'Kojic Acid', 'herbalpearls' ); ?></span>
				</div>
				<p><?php esc_html_e( 'A natural alternative for reducing dark spots and hyperpigmentation, derived from fermented rice.', 'herbalpearls' ); ?></p>
			</div>
			<div class="hp-card hp-surface-pure hp-text-center">
				<div class="hp-ingredient-chip hp-mb-1">
					<svg class="hp-ingredient-chip__icon" width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
						<path d="M12 2C8 6 2 10 2 14c0 5.5 4.5 8 10 8s10-2.5 10-8c0-4-6-8-10-12z" fill="none" stroke="currentColor" stroke-width="1.5"/>
					</svg>
					<span><?php esc_html_e( 'Aloe Vera', 'herbalpearls' ); ?></span>
				</div>
				<p><?php esc_html_e( 'Deeply hydrates, soothes inflammation, and accelerates skin healing — the foundation of our formulas.', 'herbalpearls' ); ?></p>
			</div>
		</div>
	</div>
</section>

<!-- [9] Reviews — bg-2 -->
<section class="hp-section hp-surface-2">
	<div class="hp-container">
		<h2 class="hp-text-center hp-mb-2">
			<?php esc_html_e( 'What Our Customers Say', 'herbalpearls' ); ?>
		</h2>
		<?php echo do_shortcode( '[hp_reviews_carousel count="3"]' ); ?>
	</div>
</section>

<!-- [10] Blog Highlight — bg-page -->
<section class="hp-section hp-surface-page">
	<div class="hp-container">
		<h2 class="hp-text-center hp-mb-2">
			<?php esc_html_e( 'From Our Blog', 'herbalpearls' ); ?>
		</h2>
		<?php echo do_shortcode( '[hp_blog_highlight count="3"]' ); ?>
		<div class="hp-text-center hp-mt-2">
			<a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>" class="hp-btn hp-btn--secondary">
				<?php esc_html_e( 'Read All Articles', 'herbalpearls' ); ?>
			</a>
		</div>
	</div>
</section>

<!-- [11] Newsletter — bg-3 sandstone -->
<section class="hp-section hp-surface-3">
	<div class="hp-container hp-text-center">
		<h2><?php esc_html_e( 'Get 5% Off Your First Order', 'herbalpearls' ); ?></h2>
		<p class="hp-subtitle hp-mt-1">
			<?php esc_html_e( 'Skincare tips, new launches, and exclusive offers — no spam, ever.', 'herbalpearls' ); ?>
		</p>
		<form class="hp-newsletter-form hp-mt-2" method="post" action="#" style="max-width: 480px; margin-inline: auto;">
			<div style="display: flex; gap: 0.5rem;">
				<input
					type="email"
					name="email"
					class="hp-input"
					placeholder="<?php esc_attr_e( 'Your email address', 'herbalpearls' ); ?>"
					required
					aria-label="<?php esc_attr_e( 'Email address', 'herbalpearls' ); ?>"
				>
				<button type="submit" class="hp-btn hp-btn--primary">
					<?php esc_html_e( 'Subscribe', 'herbalpearls' ); ?>
				</button>
			</div>
		</form>
	</div>
</section>

<?php
get_footer();
