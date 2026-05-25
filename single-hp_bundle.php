<?php
/**
 * Bundle landing page template.
 * Per v3 design system + redesign brief §5.3.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	$bundle_id = get_the_ID();
	$items     = get_post_meta( $bundle_id, '_hp_bundle_items', true ) ?: [];
	$discount  = absint( get_post_meta( $bundle_id, '_hp_bundle_discount_pct', true ) ?: 10 );

	// Calculate total MRP and discounted price
	$total_mrp   = 0;
	$total_sale  = 0;
	$valid_items = [];

	foreach ( $items as $item ) {
		$pid = absint( $item['product_id'] ?? 0 );
		$product = wc_get_product( $pid );
		if ( ! $product || 'publish' !== $product->get_status() ) {
			continue;
		}
		$price = (float) $product->get_regular_price();
		$valid_items[] = [
			'product' => $product,
			'qty'     => absint( $item['qty'] ?? 1 ),
			'price'   => $price,
		];
		$total_mrp  += $price * absint( $item['qty'] ?? 1 );
		$total_sale += ( $price * ( 1 - $discount / 100 ) ) * absint( $item['qty'] ?? 1 );
	}
	$discounted_price = $total_mrp * ( 1 - ( $discount / 100 ) );
	$save_amount      = $total_mrp - $discounted_price;

	// Meta sections
	$rationale = get_post_meta( $bundle_id, '_hp_bundle_rationale', true );
	$protocol  = get_post_meta( $bundle_id, '_hp_bundle_protocol', true );
	$faqs      = get_post_meta( $bundle_id, '_hp_bundle_faqs', true );
	if ( ! is_array( $faqs ) ) {
		$faqs = [];
	}
	?>

	<!-- Breadcrumb row -->
	<div class="hp-container">
		<nav class="hp-crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'herbalpearls' ); ?>">
			<a href="<?php echo esc_url( home_url() ); ?>"><?php esc_html_e( 'Home', 'herbalpearls' ); ?></a>
			<span class="hp-crumbs__sep" aria-hidden="true">/</span>
			<a href="<?php echo esc_url( home_url( '/bundles/' ) ); ?>"><?php esc_html_e( 'Bundles', 'herbalpearls' ); ?></a>
			<span class="hp-crumbs__sep" aria-hidden="true">/</span>
			<span class="hp-crumbs__current"><?php the_title(); ?></span>
		</nav>
	</div>

	<!-- Hero -->
	<section class="hp-bundle-hero">
		<div class="hp-container">
			<div class="hp-bundle-hero__eyebrow">
				<span class="hp-badge hp-badge--bestseller"><?php esc_html_e( 'BUNDLE', 'herbalpearls' ); ?> &middot; <?php printf( esc_html__( 'SAVE %d%%', 'herbalpearls' ), $discount ); ?></span>
			</div>
			<h1><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="hp-bundle-hero__subtitle"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<!-- Two-column layout: image + form -->
	<section class="hp-section">
		<div class="hp-container">
			<div class="hp-bundle-layout">
				<div class="hp-bundle-layout__image">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'hp-product-card', [ 'class' => 'hp-bundle-main-img' ] ); ?>
					<?php endif; ?>

					<?php if ( $total_mrp > 0 ) : ?>
						<div class="hp-bundle-price-card">
							<div class="hp-bundle-price-card__label"><?php esc_html_e( 'Bundle Price', 'herbalpearls' ); ?></div>
							<div class="hp-bundle-price-card__amount">
								<span class="hp-bundle-price-card__mrp">₹<?php echo esc_html( number_format( $total_mrp, 0 ) ); ?></span>
								<span class="hp-bundle-price-card__sale">₹<?php echo esc_html( number_format( $discounted_price, 0 ) ); ?></span>
							</div>
							<div class="hp-bundle-price-card__save">
								<?php printf( esc_html__( 'You save ₹%s', 'herbalpearls' ), number_format( $save_amount, 0 ) ); ?>
							</div>
						</div>
					<?php endif; ?>

					<!-- Trust strip -->
					<div class="hp-bundle-trust hp-mt-1">
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
				</div>

				<div class="hp-bundle-layout__form">
					<h2><?php esc_html_e( 'What\'s Included', 'herbalpearls' ); ?></h2>

					<form id="hp-bundle-form" data-bundle-id="<?php echo esc_attr( $bundle_id ); ?>">
						<?php foreach ( $valid_items as $item ) :
							$product = $item['product'];
							$item_mrp  = $item['price'];
							$item_sale = $item_mrp * ( 1 - $discount / 100 );
							?>
							<div class="hp-bundle-item">
								<div class="hp-bundle-item__image">
									<?php echo $product->get_image( 'thumbnail' ); // phpcs:ignore ?>
								</div>
								<div class="hp-bundle-item__info">
									<h3 class="hp-bundle-item__name"><?php echo esc_html( $product->get_name() ); ?></h3>
									<div class="hp-bundle-item__price">
										<span class="hp-bundle-item__mrp">₹<?php echo esc_html( number_format( $item_mrp, 0 ) ); ?></span>
										<span class="hp-bundle-item__sale">₹<?php echo esc_html( number_format( $item_sale, 0 ) ); ?></span>
										<span class="hp-bundle-item__worth"><?php printf( esc_html__( 'Worth ₹%s', 'herbalpearls' ), number_format( $item_mrp, 0 ) ); ?></span>
									</div>

									<?php if ( $product->is_type( 'variable' ) ) : ?>
										<label class="hp-bundle-item__label"><?php esc_html_e( 'Choose size:', 'herbalpearls' ); ?></label>
										<select
											name="selections[<?php echo esc_attr( $product->get_id() ); ?>]"
											required
											class="hp-select"
										>
											<option value="">&mdash; <?php esc_html_e( 'Select', 'herbalpearls' ); ?> &mdash;</option>
											<?php foreach ( $product->get_available_variations() as $variation ) :
												$size = '';
												foreach ( $variation['attributes'] as $attr ) {
													$size = $attr;
													break;
												}
												?>
												<option value="<?php echo esc_attr( $variation['variation_id'] ); ?>">
													<?php echo esc_html( $size ); ?> — ₹<?php echo esc_html( number_format( $variation['display_price'], 0 ) ); ?>
												</option>
											<?php endforeach; ?>
										</select>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>

						<button type="submit" class="hp-btn hp-btn--primary hp-btn--lg hp-btn--block hp-mt-2">
							<?php printf( esc_html__( 'Add Bundle to Cart — ₹%s', 'herbalpearls' ), number_format( $discounted_price, 0 ) ); ?>
						</button>
						<p class="hp-bundle-msg" aria-live="polite"></p>
					</form>
				</div>
			</div>

			<?php if ( $bundle_content = get_the_content() ) : ?>
				<div class="hp-bundle-description hp-mt-3">
					<?php echo wp_kses_post( wpautop( $bundle_content ) ); ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<?php /* Why This Bundle Works */ ?>
	<?php if ( ! empty( $rationale ) ) : ?>
		<section class="hp-section hp-surface-pure" aria-labelledby="hp-bundle-why-title">
			<div class="hp-container">
				<div class="hp-section-h">
					<span class="hp-section-h__accent"><?php esc_html_e( 'the synergy', 'herbalpearls' ); ?></span>
					<h2 id="hp-bundle-why-title" class="hp-section-h__title"><?php esc_html_e( 'Why This Bundle Works', 'herbalpearls' ); ?></h2>
				</div>
				<div class="hp-bundle-rationale">
					<?php echo wp_kses_post( wpautop( $rationale ) ); ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php /* How to Use Together */ ?>
	<?php if ( ! empty( $protocol ) ) : ?>
		<section class="hp-section hp-surface-page" aria-labelledby="hp-bundle-how-title">
			<div class="hp-container">
				<div class="hp-section-h">
					<span class="hp-section-h__accent"><?php esc_html_e( 'your regimen', 'herbalpearls' ); ?></span>
					<h2 id="hp-bundle-how-title" class="hp-section-h__title"><?php esc_html_e( 'How to Use Together', 'herbalpearls' ); ?></h2>
				</div>
				<div class="hp-steps-grid">
					<?php echo wp_kses_post( $protocol ); ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php /* FAQ accordion */ ?>
	<?php if ( ! empty( $faqs ) ) : ?>
		<section class="hp-section hp-surface-1" aria-labelledby="hp-bundle-faq-title">
			<div class="hp-container">
				<div class="hp-section-h">
					<h2 id="hp-bundle-faq-title" class="hp-section-h__title"><?php esc_html_e( 'Frequently Asked Questions', 'herbalpearls' ); ?></h2>
				</div>
				<div class="hp-accordion" data-accordion>
					<?php foreach ( $faqs as $faq ) : ?>
						<div class="hp-accordion__item">
							<button type="button" class="hp-accordion__trigger" aria-expanded="false" data-accordion-trigger>
								<span><?php echo esc_html( $faq['question'] ); ?></span>
								<svg class="hp-accordion__icon" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
									<path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" fill="none"/>
								</svg>
							</button>
							<div class="hp-accordion__panel">
								<div class="hp-accordion__content">
									<?php echo wp_kses_post( wpautop( $faq['answer'] ) ); ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php /* Cross-sell: other bundles */ ?>
	<?php
	$other_bundles = get_posts( [
		'post_type'      => 'hp_bundle',
		'posts_per_page' => 3,
		'post_status'    => 'publish',
		'post__not_in'   => [ $bundle_id ],
		'orderby'        => 'rand',
	] );
	if ( ! empty( $other_bundles ) ) :
		?>
		<section class="hp-section hp-surface-page" aria-labelledby="hp-bundle-cross-title">
			<div class="hp-container">
				<div class="hp-section-h">
					<h2 id="hp-bundle-cross-title" class="hp-section-h__title"><?php esc_html_e( 'Other Bundles You Might Like', 'herbalpearls' ); ?></h2>
				</div>
				<div class="hp-grid-3">
					<?php foreach ( $other_bundles as $bundle_post ) :
						$b_discount = absint( get_post_meta( $bundle_post->ID, '_hp_bundle_discount_pct', true ) ?: 10 );
						$b_items    = get_post_meta( $bundle_post->ID, '_hp_bundle_items', true ) ?: [];
						?>
						<div class="hp-bundle-card">
							<div class="hp-bundle-card__image">
								<a href="<?php echo esc_url( get_permalink( $bundle_post ) ); ?>">
									<?php echo get_the_post_thumbnail( $bundle_post, 'hp-product-card' ); ?>
								</a>
								<span class="hp-bundle-card__ribbon"><?php printf( esc_html__( 'Save %d%%', 'herbalpearls' ), $b_discount ); ?></span>
							</div>
							<div class="hp-bundle-card__body">
								<h3 class="hp-bundle-card__title">
									<a href="<?php echo esc_url( get_permalink( $bundle_post ) ); ?>"><?php echo esc_html( $bundle_post->post_title ); ?></a>
								</h3>
								<?php if ( ! empty( $b_items ) ) : ?>
									<div class="hp-bundle-card__children">
										<?php
										$shown = 0;
										foreach ( $b_items as $bi ) :
											$bp = wc_get_product( absint( $bi['product_id'] ?? 0 ) );
											if ( ! $bp || $shown >= 3 ) {
												continue;
											}
											$shown++;
											?>
											<div class="hp-bundle-card__child-thumb">
												<?php echo $bp->get_image( 'thumbnail' ); // phpcs:ignore ?>
											</div>
										<?php endforeach; ?>
										<?php $remaining = count( $b_items ) - 3;
										if ( $remaining > 0 ) : ?>
											<span class="hp-bundle-card__child-more">+<?php echo absint( $remaining ); ?></span>
										<?php endif; ?>
									</div>
								<?php endif; ?>
								<div class="hp-bundle-card__price">
									<a href="<?php echo esc_url( get_permalink( $bundle_post ) ); ?>" class="hp-btn hp-btn--ghost hp-btn--sm"><?php esc_html_e( 'View Bundle', 'herbalpearls' ); ?> &rarr;</a>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php /* Disclaimer */ ?>
	<aside class="hp-disclaimer hp-mt-3">
		<h4><?php esc_html_e( 'Disclaimer', 'herbalpearls' ); ?></h4>
		<p><?php esc_html_e( 'Statements and product information have not been evaluated by any regulatory authority unless specified. Herbal Pearls products are intended for cosmetic use only and are not a substitute for medical advice, diagnosis, or treatment. Individual results may vary. Patch-test before first use.', 'herbalpearls' ); ?></p>
	</aside>

	<?php
endwhile;

get_footer();
