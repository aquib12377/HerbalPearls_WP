<?php
/**
 * PDP below-fold sections — hook-based injection.
 * Hooked into woocommerce_after_single_product_summary with explicit priorities.
 *
 * Priority order: 5 (trust strip) → 10 (why love it) → 20 (ingredient story)
 * → 30 (how to use) → 40 (reviews) → 50 (related) → 60 (disclaimer)
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Section: Why You'll Love It (3-column benefit cards)
 * Priority: 10
 */
add_action( 'woocommerce_after_single_product_summary', function() {
	global $product;
	$product_id = $product->get_id();

	// Build benefits from product meta + hardcoded fallbacks
	$benefits = [];

	// Try meta field first
	$saved_benefits = get_post_meta( $product_id, '_hp_benefits', true );
	if ( ! empty( $saved_benefits ) && is_array( $saved_benefits ) ) {
		$benefits = $saved_benefits;
	} else {
		// Fallback: derive from ingredients + product category
		$ingredients = hp_get_product_ingredients( $product_id );
		if ( ! empty( $ingredients ) ) {
			foreach ( array_slice( $ingredients, 0, 3 ) as $ing ) {
				$benefits[] = [
					'icon'        => 'leaf',
					'headline'    => $ing['name'],
					'description' => $ing['benefit'],
				];
			}
		}
	}

	if ( empty( $benefits ) ) {
		return;
	}

	// Limit to 3 cards
	$benefits = array_slice( $benefits, 0, 3 );
	?>
	<section class="hp-section hp-surface-1" aria-labelledby="hp-why-title">
		<div class="hp-container">
			<div class="hp-section-h">
				<span class="hp-section-h__accent"><?php esc_html_e( 'the difference', 'herbalpearls' ); ?></span>
				<h2 id="hp-why-title" class="hp-section-h__title"><?php esc_html_e( 'Why You\'ll Love It', 'herbalpearls' ); ?></h2>
			</div>
			<div class="hp-grid-3">
				<?php foreach ( $benefits as $benefit ) : ?>
					<div class="hp-benefit-card">
						<div class="hp-benefit-card__icon">
							<svg width="28" height="28" viewBox="0 0 24 24" aria-hidden="true">
								<path d="M12 2C8 6 2 10 2 14c0 5.5 4.5 8 10 8s10-2.5 10-8c0-4-6-8-10-12z" fill="none" stroke="var(--hp-gold-600)" stroke-width="1.5"/>
							</svg>
						</div>
						<h3><?php echo esc_html( $benefit['headline'] ); ?></h3>
						<p><?php echo esc_html( $benefit['description'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}, 10 );

/**
 * Section: Hero Ingredient Story — image left, copy right
 * Priority: 20
 */
add_action( 'woocommerce_after_single_product_summary', function() {
	global $product;
	$product_id = $product->get_id();

	// Try dedicated meta first, fall back to first ingredient
	$hero_ingredient = get_post_meta( $product_id, '_hp_hero_ingredient', true );
	$ingredients     = hp_get_product_ingredients( $product_id );

	if ( ! empty( $hero_ingredient ) && is_array( $hero_ingredient ) ) {
		$name    = $hero_ingredient['name'] ?? '';
		$headline = $hero_ingredient['headline'] ?? $name;
		$copy    = $hero_ingredient['copy'] ?? '';
		$img_id  = absint( $hero_ingredient['image_id'] ?? 0 );
	} elseif ( ! empty( $ingredients ) ) {
		$first   = $ingredients[0];
		$name    = $first['name'];
		$headline = sprintf( __( 'The Power of %s', 'herbalpearls' ), $name );
		$copy    = $first['benefit'];
		$img_id  = 0;
	} else {
		return;
	}

	if ( empty( $name ) ) {
		return;
	}

	// Skip if no image and no copy
	if ( ! $img_id && empty( $copy ) ) {
		return;
	}
	?>
	<section class="hp-section hp-surface-pure" aria-labelledby="hp-ingredient-title">
		<div class="hp-container">
			<div class="hp-ingredient-story">
				<?php if ( $img_id ) : ?>
					<div class="hp-ingredient-story__image">
						<?php echo wp_get_attachment_image( $img_id, 'hp-product-card', false, [ 'loading' => 'lazy' ] ); ?>
					</div>
				<?php endif; ?>
				<div class="hp-ingredient-story__copy">
					<span class="hp-ingredient-story__name"><?php echo esc_html( $name ); ?></span>
					<h2 id="hp-ingredient-title"><?php echo esc_html( $headline ); ?></h2>
					<p><?php echo esc_html( $copy ); ?></p>
				</div>
			</div>
		</div>
	</section>
	<?php
}, 20 );

/**
 * Section: How to Use — numbered steps grid
 * Priority: 30
 */
add_action( 'woocommerce_after_single_product_summary', function() {
	global $product;
	$product_id = $product->get_id();

	$how_to = get_post_meta( $product_id, '_hp_how_to_short', true );
	if ( ! $how_to ) {
		return;
	}

	// Strip <ol> wrapper to count <li> items
	$steps_html = wp_kses_post( $how_to );
	?>
	<section class="hp-section hp-surface-page" aria-labelledby="hp-how-title">
		<div class="hp-container">
			<div class="hp-section-h">
				<span class="hp-section-h__accent"><?php esc_html_e( 'your routine', 'herbalpearls' ); ?></span>
				<h2 id="hp-how-title" class="hp-section-h__title"><?php esc_html_e( 'How to Use', 'herbalpearls' ); ?></h2>
			</div>
			<div class="hp-steps-grid">
				<?php echo $steps_html; ?>
			</div>
		</div>
	</section>
	<?php
}, 30 );

/**
 * Section: Reviews
 * Priority: 40
 */
add_action( 'woocommerce_after_single_product_summary', function() {
	if ( ! function_exists( 'wc_review_ratings_enabled' ) || ! wc_review_ratings_enabled() ) {
		return;
	}
	global $product;

	$reviews = get_comments( [
		'post_id' => $product->get_id(),
		'status'  => 'approve',
		'number'  => 4,
		'orderby' => 'comment_date_gmt',
		'order'   => 'DESC',
		'meta_query' => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			[ 'key' => 'rating', 'compare' => 'EXISTS' ],
		],
	] );

	if ( empty( $reviews ) ) {
		return;
	}

	$rating_count = $product->get_review_count();
	$average      = $product->get_average_rating();
	?>
	<section class="hp-section hp-surface-2" aria-labelledby="hp-reviews-title">
		<div class="hp-container">
			<div class="hp-section-h">
				<h2 id="hp-reviews-title" class="hp-section-h__title"><?php esc_html_e( 'What Our Customers Say', 'herbalpearls' ); ?></h2>
			</div>

			<div class="hp-reviews-summary hp-text-center hp-mb-2">
				<div class="hp-reviews-summary__stars" aria-label="<?php printf( esc_attr__( 'Rated %s out of 5', 'herbalpearls' ), esc_attr( $average ) ); ?>">
					<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
						<span aria-hidden="true"><?php echo ( $i <= round( $average ) ) ? '★' : '☆'; ?></span>
					<?php endfor; ?>
				</div>
				<p class="hp-reviews-summary__count">
					<?php printf( esc_html__( 'Based on %d reviews', 'herbalpearls' ), absint( $rating_count ) ); ?>
				</p>
			</div>

			<div class="hp-grid-2">
				<?php
				$alt = false;
				foreach ( $reviews as $review ) :
					$rating   = (int) get_comment_meta( $review->comment_ID, 'rating', true );
					$verified = get_comment_meta( $review->comment_ID, 'verified', true );
					?>
					<div class="hp-review-card <?php echo $alt ? 'hp-review-card--alt' : ''; ?>">
						<div class="hp-review-card__stars" aria-label="<?php printf( esc_attr__( '%d out of 5 stars', 'herbalpearls' ), $rating ); ?>">
							<?php for ( $s = 1; $s <= 5; $s++ ) : ?>
								<span aria-hidden="true"><?php echo ( $s <= $rating ) ? '★' : '☆'; ?></span>
							<?php endfor; ?>
						</div>
						<div class="hp-review-card__body">
							<?php echo esc_html( wp_trim_words( $review->comment_content, 30 ) ); ?>
						</div>
						<div class="hp-review-card__author">
							<?php echo esc_html( $review->comment_author ); ?>
							<?php if ( $verified ) : ?>
								<span class="hp-review-card__verified">✓ <?php esc_html_e( 'Verified', 'herbalpearls' ); ?></span>
							<?php endif; ?>
						</div>
						<div class="hp-review-card__date">
							<?php echo esc_html( mysql2date( get_option( 'date_format' ), $review->comment_date ) ); ?>
						</div>
					</div>
					<?php $alt = ! $alt; ?>
				<?php endforeach; ?>
			</div>

			<div class="hp-text-center hp-mt-2">
				<a href="#review_form_wrapper" class="hp-btn hp-btn--secondary">
					<?php esc_html_e( 'Write a Review', 'herbalpearls' ); ?>
				</a>
			</div>
		</div>
	</section>
	<?php
}, 40 );
