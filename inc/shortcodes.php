<?php
/**
 * Shortcodes — [hp_*] family.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ─────────────── [hp_bundle id="X"] — embed a bundle in posts ─────────────── */
add_shortcode( 'hp_bundle', function( $atts ) {
	$atts = shortcode_atts( [ 'id' => 0 ], $atts, 'hp_bundle' );
	$id   = absint( $atts['id'] );

	if ( ! $id || get_post_type( $id ) !== 'hp_bundle' || get_post_status( $id ) !== 'publish' ) {
		return '';
	}

	ob_start();

	$bundle_id = $id;
	$items     = get_post_meta( $bundle_id, '_hp_bundle_items', true ) ?: [];
	$discount  = get_post_meta( $bundle_id, '_hp_bundle_discount_pct', true ) ?: 10;

	if ( empty( $items ) ) {
		ob_end_clean();
		return '';
	}
	?>
	<div class="hp-bundle-embed">
		<h3><?php echo esc_html( get_the_title( $bundle_id ) ); ?></h3>
		<p class="hp-bundle-subtitle">
			<?php
			printf(
				/* translators: %d: discount percentage */
				esc_html__( 'Save %d%% on this curated combo.', 'herbalpearls' ),
				absint( $discount )
			);
			?>
		</p>
		<a href="<?php echo esc_url( get_permalink( $bundle_id ) ); ?>" class="hp-btn hp-btn--primary">
			<?php esc_html_e( 'View Bundle', 'herbalpearls' ); ?>
		</a>
	</div>
	<?php

	return ob_get_clean();
} );

/* ─────────────── [hp_bestsellers count="4"] ─────────────── */
add_shortcode( 'hp_bestsellers', function( $atts ) {
	$atts  = shortcode_atts( [ 'count' => 4 ], $atts, 'hp_bestsellers' );
	$count = absint( $atts['count'] );

	$products = wc_get_products( [
		'limit'   => $count,
		'status'  => 'publish',
		'orderby' => 'total_sales',
		'order'   => 'DESC',
	] );

	if ( empty( $products ) ) {
		return '';
	}

	ob_start();
	echo '<div class="hp-grid-' . absint( $count ) . '">';
	foreach ( $products as $product ) {
		wc_get_template_part( 'content', 'product', [ 'product' => $product ] );
	}
	echo '</div>';
	return ob_get_clean();
} );

/* ─────────────── [hp_bundle_promo count="3"] ─────────────── */
add_shortcode( 'hp_bundle_promo', function( $atts ) {
	$atts  = shortcode_atts( [ 'count' => 3 ], $atts, 'hp_bundle_promo' );
	$count = absint( $atts['count'] );

	$bundles = get_posts( [
		'post_type'      => 'hp_bundle',
		'posts_per_page' => $count,
		'post_status'    => 'publish',
	] );

	if ( empty( $bundles ) ) {
		return '<p>' . esc_html__( 'Bundles coming soon.', 'herbalpearls' ) . '</p>';
	}

	ob_start();
	echo '<div class="hp-grid-3">';
	foreach ( $bundles as $bundle ) {
		$discount = get_post_meta( $bundle->ID, '_hp_bundle_discount_pct', true ) ?: 10;
		?>
		<div class="hp-card hp-card--hover hp-text-center">
			<?php if ( has_post_thumbnail( $bundle ) ) : ?>
				<div class="hp-mb-1">
					<?php echo get_the_post_thumbnail( $bundle, 'hp-product-card' ); ?>
				</div>
			<?php endif; ?>
			<h3><?php echo esc_html( $bundle->post_title ); ?></h3>
			<span class="hp-badge hp-badge--bestseller hp-mt-1">
				<?php
				printf(
					/* translators: %d: discount percentage */
					esc_html__( 'Save %d%%', 'herbalpearls' ),
					absint( $discount )
				);
				?>
			</span>
			<div class="hp-mt-1">
				<a href="<?php echo esc_url( get_permalink( $bundle ) ); ?>" class="hp-btn hp-btn--secondary hp-btn--sm">
					<?php esc_html_e( 'View Bundle', 'herbalpearls' ); ?>
				</a>
			</div>
		</div>
		<?php
	}
	echo '</div>';
	return ob_get_clean();
} );

/* ─────────────── [hp_reviews_carousel count="3"] ─────────────── */
add_shortcode( 'hp_reviews_carousel', function( $atts ) {
	$atts  = shortcode_atts( [ 'count' => 3 ], $atts, 'hp_reviews_carousel' );
	$count = absint( $atts['count'] );

	$reviews = get_comments( [
		'post_type' => 'product',
		'status'    => 'approve',
		'number'    => $count,
		'orderby'   => 'comment_date_gmt',
		'order'     => 'DESC',
		'meta_query' => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			[
				'key'     => 'rating',
				'compare' => 'EXISTS',
			],
		],
	] );

	if ( empty( $reviews ) ) {
		return '<p class="hp-text-center">' . esc_html__( 'Reviews coming soon.', 'herbalpearls' ) . '</p>';
	}

	ob_start();
	echo '<div class="hp-grid-3">';
	foreach ( $reviews as $review ) {
		$rating   = (int) get_comment_meta( $review->comment_ID, 'rating', true );
		$verified = get_comment_meta( $review->comment_ID, 'verified', true );
		?>
		<div class="hp-review-card hp-review-card--alt">
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
		</div>
		<?php
	}
	echo '</div>';
	return ob_get_clean();
} );

/* ─────────────── [hp_blog_highlight count="3"] ─────────────── */
add_shortcode( 'hp_blog_highlight', function( $atts ) {
	$atts  = shortcode_atts( [ 'count' => 3 ], $atts, 'hp_blog_highlight' );
	$count = absint( $atts['count'] );

	$posts = get_posts( [
		'post_type'      => 'post',
		'posts_per_page' => $count,
		'post_status'    => 'publish',
	] );

	if ( empty( $posts ) ) {
		return '<p class="hp-text-center">' . esc_html__( 'Articles coming soon.', 'herbalpearls' ) . '</p>';
	}

	ob_start();
	echo '<div class="hp-grid-3">';
	foreach ( $posts as $post_item ) {
		$cats = get_the_category( $post_item->ID );
		?>
		<article class="hp-blog-card">
			<?php if ( has_post_thumbnail( $post_item ) ) : ?>
				<div class="hp-blog-card__image">
					<a href="<?php echo esc_url( get_permalink( $post_item ) ); ?>">
						<?php echo get_the_post_thumbnail( $post_item, 'hp-blog-card' ); ?>
					</a>
				</div>
			<?php endif; ?>
			<div class="hp-blog-card__body">
				<?php if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) : ?>
					<div class="hp-blog-card__category"><?php echo esc_html( $cats[0]->name ); ?></div>
				<?php endif; ?>
				<h3 class="hp-blog-card__title">
					<a href="<?php echo esc_url( get_permalink( $post_item ) ); ?>">
						<?php echo esc_html( $post_item->post_title ); ?>
					</a>
				</h3>
				<div class="hp-blog-card__meta">
					<?php echo esc_html( get_the_date( '', $post_item ) ); ?>
				</div>
			</div>
		</article>
		<?php
	}
	echo '</div>';
	return ob_get_clean();
} );

/* ─────────────── [hp_current_year] ─────────────── */
add_shortcode( 'hp_current_year', function() {
	return esc_html( gmdate( 'Y' ) );
} );

/* ─────────────── [hp_trust_strip] ─────────────── */
add_shortcode( 'hp_trust_strip', function() {
	ob_start();
	?>
	<div class="hp-trust-strip" aria-label="<?php esc_attr_e( 'What we stand for', 'herbalpearls' ); ?>">
		<div class="hp-trust-strip__inner">
			<div class="hp-trust-item">
				<svg class="hp-trust-item__icon" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3 17 5 13 9 13 15s-3 5-5 5c-.63 0-1.05-.05-1.63-.2" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
				<span><?php esc_html_e( '100% Natural', 'herbalpearls' ); ?></span>
			</div>
			<div class="hp-trust-item">
				<svg class="hp-trust-item__icon" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="1.5"/><line x1="12" y1="7" x2="12" y2="7" stroke="currentColor" stroke-width="5" stroke-linecap="round"/></svg>
				<span><?php esc_html_e( 'Paraben Free', 'herbalpearls' ); ?></span>
			</div>
			<div class="hp-trust-item">
				<svg class="hp-trust-item__icon" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12c0 5-4 9-9 9s-9-4-9-9 4-9 9-9" fill="none" stroke="currentColor" stroke-width="1.5"/><circle cx="9" cy="8.5" r="1.5" fill="currentColor" stroke="none"/><path d="M15 12l-3-1.5V7" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
				<span><?php esc_html_e( 'Cruelty Free', 'herbalpearls' ); ?></span>
			</div>
			<div class="hp-trust-item">
				<svg class="hp-trust-item__icon" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><rect x="1" y="3" width="15" height="13" rx="2" fill="none" stroke="currentColor" stroke-width="1.5"/><path d="M16 8h5l2 4-2 4h-5" fill="none" stroke="currentColor" stroke-width="1.5"/><circle cx="5.5" cy="18.5" r="2.5" fill="none" stroke="currentColor" stroke-width="1.5"/><circle cx="16.5" cy="18.5" r="2.5" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
				<span><?php esc_html_e( 'Pan-India Shipping', 'herbalpearls' ); ?></span>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
} );
