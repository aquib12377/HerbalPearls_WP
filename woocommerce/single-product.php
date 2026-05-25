<?php
/**
 * Single Product — full PDP layout.
 * Gallery on white card, info panel, accordion, below-fold sections.
 *
 * Version: WC 10.7.0 · Customized: yes · Updated: 2026-05-11
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	global $product;

	if ( ! $product instanceof WC_Product ) {
		$product = wc_get_product( get_the_ID() );
	}

	if ( ! $product ) {
		echo '<div class="hp-container hp-section"><p>' . esc_html__( 'Product not found.', 'herbalpearls' ) . '</p></div>';
		get_footer();
		return;
	}

	$product_id = $product->get_id();

	// Calculate savings for display
	$regular_price = (float) $product->get_regular_price();
	$sale_price    = (float) $product->get_sale_price();
	$has_sale      = $product->is_on_sale() && $regular_price > 0;
	$save_amount   = $has_sale ? $regular_price - $sale_price : 0;
	$save_pct      = $has_sale ? round( ( $save_amount / $regular_price ) * 100 ) : 0;
	?>

	<!-- Breadcrumb row -->
	<div class="hp-container">
		<nav class="hp-crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'herbalpearls' ); ?>">
			<?php
			$crumbs = [
				[ 'url' => home_url(), 'label' => __( 'Home', 'herbalpearls' ) ],
				[ 'url' => get_permalink( wc_get_page_id( 'shop' ) ), 'label' => __( 'Shop', 'herbalpearls' ) ],
			];

			$terms = get_the_terms( $product_id, 'product_cat' );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$crumbs[] = [ 'url' => get_term_link( $terms[0] ), 'label' => $terms[0]->name ];
			}

			$last = count( $crumbs ) - 1;
			foreach ( $crumbs as $i => $crumb ) :
				?>
				<a href="<?php echo esc_url( $crumb['url'] ); ?>"><?php echo esc_html( $crumb['label'] ); ?></a>
				<span class="hp-crumbs__sep" aria-hidden="true">/</span>
			<?php endforeach; ?>
			<span class="hp-crumbs__current"><?php the_title(); ?></span>
		</nav>
	</div>

	<div class="hp-pdp">
		<div class="hp-pdp__gallery">
			<?php
			/**
			 * Hook: woocommerce_before_single_product_summary.
			 * Outputs the product gallery.
			 */
			do_action( 'woocommerce_before_single_product_summary' );
			?>
		</div>

		<div class="hp-pdp__info">
			<?php
			// Category text (small caps above title)
			$categories = get_the_terms( $product_id, 'product_cat' );
			if ( $categories && ! is_wp_error( $categories ) ) {
				echo '<div class="hp-pdp__category">' . esc_html( $categories[0]->name ) . '</div>';
			}

			// Title
			the_title( '<h1 class="hp-pdp__title">', '</h1>' );

			// Rating
			if ( wc_review_ratings_enabled() ) {
				$rating_count = $product->get_review_count();
				$average      = $product->get_average_rating();
				if ( $rating_count > 0 ) {
					echo '<div class="hp-pdp__rating">';
					echo '<span class="hp-pdp__stars" aria-label="' . esc_attr( sprintf( __( 'Rated %s out of 5', 'herbalpearls' ), $average ) ) . '">';
					for ( $i = 1; $i <= 5; $i++ ) {
						echo ( $i <= round( $average ) ) ? '★' : '☆';
					}
					echo '</span>';
					echo '<a href="#hp-reviews" class="hp-pdp__rating-count">(' . absint( $rating_count ) . ' ' . esc_html__( 'reviews', 'herbalpearls' ) . ')</a>';
					echo '</div>';
				}
			}

			// Price with save badge
			wc_get_template( 'single-product/price.php' );

			if ( $has_sale && $save_amount > 0 ) :
				?>
				<p class="hp-save-badge">
					<?php
					printf(
						/* translators: 1: amount saved, 2: percentage saved */
						esc_html__( 'You save %1$s (%2$d%%)', 'herbalpearls' ),
						wp_strip_all_tags( wc_price( $save_amount ) ),
						absint( $save_pct )
					);
					?>
				</p>
			<?php endif; ?>

			<p class="hp-tax-note" style="font-size: 0.8125rem; color: var(--hp-text-4);">
				<?php esc_html_e( 'Inclusive of all taxes.', 'herbalpearls' ); ?>
			</p>

			<?php
			// Short description
			$short_desc = $product->get_short_description();
			if ( $short_desc ) {
				echo '<div class="hp-pdp__desc">' . wp_kses_post( wpautop( $short_desc ) ) . '</div>';
			}

			// Ingredient chips in summary
			$ingredients = hp_get_product_ingredients( $product_id );
			if ( ! empty( $ingredients ) ) :
				?>
				<div class="hp-pdp__chips">
					<?php
					$visible = array_slice( $ingredients, 0, 3 );
					$hidden  = array_slice( $ingredients, 3 );
					foreach ( $visible as $ing ) :
						?>
						<span class="hp-ingredient-chip">
							<svg class="hp-ingredient-chip__icon" width="14" height="14" viewBox="0 0 24 24" aria-hidden="true">
								<path d="M12 2C8 6 2 10 2 14c0 5.5 4.5 8 10 8s10-2.5 10-8c0-4-6-8-10-12z" fill="none" stroke="currentColor" stroke-width="1.5"/>
							</svg>
							<?php echo esc_html( $ing['name'] ); ?>
						</span>
					<?php endforeach; ?>
					<?php if ( ! empty( $hidden ) ) : ?>
						<span class="hp-ingredient-chip hp-ingredient-chip--more">
							+<?php echo absint( count( $hidden ) ); ?> <?php esc_html_e( 'more', 'herbalpearls' ); ?>
						</span>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php
			// Add to cart form
			woocommerce_template_single_add_to_cart();

			// Trust strip
			?>
			<div class="hp-pdp__trust">
				<div class="hp-trust-item">
					<svg class="hp-trust-item__icon" width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3 17 5 13 9 13 15s-3 5-5 5c-.63 0-1.05-.05-1.63-.2" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
					<span><?php esc_html_e( '100% Natural', 'herbalpearls' ); ?></span>
				</div>
				<div class="hp-trust-item">
					<svg class="hp-trust-item__icon" width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
					<span><?php esc_html_e( 'Paraben Free', 'herbalpearls' ); ?></span>
				</div>
				<div class="hp-trust-item">
					<svg class="hp-trust-item__icon" width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12c0 5-4 9-9 9s-9-4-9-9 4-9 9-9" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
					<span><?php esc_html_e( 'Cruelty Free', 'herbalpearls' ); ?></span>
				</div>
			</div>

			<?php
			// Mini icon row — shipping / COD / returns
			?>
			<div class="hp-pdp__perks">
				<div class="hp-pdp__perk">
					<svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true"><rect x="1" y="3" width="15" height="13" rx="2" fill="none" stroke="currentColor" stroke-width="1.5"/><path d="M16 8h5l2 4-2 4h-5" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
					<span><?php esc_html_e( 'Free shipping above ₹499', 'herbalpearls' ); ?></span>
				</div>
				<div class="hp-pdp__perk">
					<svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2" fill="none" stroke="currentColor" stroke-width="1.5"/><line x1="2" y1="10" x2="22" y2="10" stroke="currentColor" stroke-width="1.5"/></svg>
					<span><?php esc_html_e( 'COD available', 'herbalpearls' ); ?></span>
				</div>
				<div class="hp-pdp__perk">
					<svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 12l2 2 4-4" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
					<span><?php esc_html_e( '7-day easy returns', 'herbalpearls' ); ?></span>
				</div>
			</div>

			<?php
			// Meta — marketplace links + blog link
			wc_get_template( 'single-product/meta.php' );
			?>
		</div>
	</div>

	<?php
	// Accordion — Full Details + Shipping & Returns only
	// How to Use, Ingredients, FAQs are now below-fold sections via pdp-sections.php
	$long_desc = $product->get_description();
	$has_accordion = $long_desc;
	if ( $has_accordion ) :
		?>
		<div class="hp-accordion hp-mt-3 hp-container" data-accordion>
			<?php if ( $long_desc ) : ?>
				<div class="hp-accordion__item">
					<button type="button" class="hp-accordion__trigger" aria-expanded="false" data-accordion-trigger>
						<span><?php esc_html_e( 'Full Details', 'herbalpearls' ); ?></span>
						<svg class="hp-accordion__icon" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
							<path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" fill="none"/>
						</svg>
					</button>
					<div class="hp-accordion__panel">
						<div class="hp-accordion__content">
							<?php echo wp_kses_post( wpautop( $long_desc ) ); ?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<div class="hp-accordion__item">
				<button type="button" class="hp-accordion__trigger" aria-expanded="false" data-accordion-trigger>
					<span><?php esc_html_e( 'Shipping & Returns', 'herbalpearls' ); ?></span>
					<svg class="hp-accordion__icon" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
						<path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" fill="none"/>
					</svg>
				</button>
				<div class="hp-accordion__panel">
					<div class="hp-accordion__content">
						<p><?php esc_html_e( 'Free shipping on orders above ₹499. Delivery within 5-7 business days across India. Easy returns within 7 days of delivery — products must be unopened and in original packaging.', 'herbalpearls' ); ?></p>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<?php
	// Below-fold sections — injected via hooks in inc/pdp-sections.php
	// Priority: 10 (Why You'll Love It) → 30 (How to Use) → 40 (Reviews)
	do_action( 'woocommerce_after_single_product_summary' );

	// Related products section
	wc_get_template( 'single-product/related.php' );

	// Compliance disclaimer
	?>
	<aside class="hp-disclaimer hp-mt-3">
		<h4><?php esc_html_e( 'Disclaimer', 'herbalpearls' ); ?></h4>
		<p><?php esc_html_e( 'Statements and product information have not been evaluated by any regulatory authority unless specified. Herbal Pearls products are intended for cosmetic use only and are not a substitute for medical advice, diagnosis, or treatment. Individual results may vary. Patch-test before first use.', 'herbalpearls' ); ?></p>
	</aside>

	<?php
	// Sticky mobile CTA
	if ( $product->is_in_stock() ) :
		?>
		<div class="hp-sticky-bar" aria-hidden="true" data-sticky-cta>
			<div class="hp-sticky-bar__price">
				<?php if ( $has_sale ) : ?>
					<del style="font-size:0.75rem;color:var(--hp-text-4);margin-right:0.5rem;">₹<?php echo esc_html( number_format( $regular_price, 0 ) ); ?></del>
				<?php endif; ?>
				₹<?php echo esc_html( number_format( $has_sale ? $sale_price : $regular_price, 0 ) ); ?>
			</div>
			<?php
			woocommerce_template_single_add_to_cart(); // Re-use the same form — hidden on mobile, overridden by sticky
			?>
			<button type="button" class="hp-btn hp-btn--primary hp-sticky-bar__atc" data-sticky-atc>
				<?php esc_html_e( 'Add to Cart', 'herbalpearls' ); ?>
			</button>
		</div>
	<?php endif; ?>
	<?php
endwhile;

get_footer();
