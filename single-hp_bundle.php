<?php
/**
 * Bundle landing page template — rebuilt with Tailwind utilities atop the
 * legacy .hp-* component library. Visual rhythm now matches front-page.php:
 * hero with script accent + serif h1, consistent .hp-section-h section
 * headers, shared .hp-trust-strip, .hp-section padding scale, surface
 * alternation via .hp-surface-*.
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

	<!-- Breadcrumb -->
	<div class="hp-container pt-4">
		<nav class="hp-crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'herbalpearls' ); ?>">
			<a href="<?php echo esc_url( home_url() ); ?>"><?php esc_html_e( 'Home', 'herbalpearls' ); ?></a>
			<span class="hp-crumbs__sep" aria-hidden="true">/</span>
			<a href="<?php echo esc_url( home_url( '/bundles/' ) ); ?>"><?php esc_html_e( 'Bundles', 'herbalpearls' ); ?></a>
			<span class="hp-crumbs__sep" aria-hidden="true">/</span>
			<span class="hp-crumbs__current"><?php the_title(); ?></span>
		</nav>
	</div>

	<!-- Hero — matches front-page rhythm: script accent + serif h1 -->
	<section class="hp-section hp-surface-1 text-center" aria-labelledby="hp-bundle-title">
		<div class="hp-container max-w-3xl">
			<span class="hp-tw-eyebrow mb-3">
				<?php esc_html_e( 'Bundle', 'herbalpearls' ); ?> &middot;
				<?php printf( esc_html__( 'Save %d%%', 'herbalpearls' ), $discount ); ?>
			</span>
			<h1 id="hp-bundle-title" class="hp-script mb-2 mt-2"><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="hp-subtitle mx-auto max-w-xl">
					<?php echo esc_html( get_the_excerpt() ); ?>
				</p>
			<?php endif; ?>
		</div>
	</section>

	<!-- Two-column layout: image + form -->
	<section class="hp-section hp-surface-page">
		<div class="hp-container">
			<div class="grid gap-8 lg:gap-12 md:grid-cols-2 items-start">

				<!-- Sticky image column -->
				<div class="md:sticky md:top-24">
					<div class="rounded-lg overflow-hidden border border-hp-line-1 bg-hp-bg-pure aspect-square">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php
							the_post_thumbnail(
								'hp-product-card',
								[
									'class'   => 'w-full h-full object-cover',
									'alt'     => esc_attr( get_the_title() ),
									'loading' => 'eager',
									'fetchpriority' => 'high',
								]
							);
							?>
						<?php endif; ?>
					</div>

					<?php if ( $total_mrp > 0 ) : ?>
						<div class="mt-5 p-5 rounded-lg border-2 border-hp-gold-500 bg-hp-bg-pure text-center shadow-hp-sm">
							<div class="text-[13px] font-medium uppercase tracking-wider text-hp-text-3 mb-2">
								<?php esc_html_e( 'Bundle Price', 'herbalpearls' ); ?>
							</div>
							<div class="flex items-baseline justify-center gap-3 mb-1">
								<span class="text-lg text-hp-text-4 line-through">
									₹<?php echo esc_html( number_format( $total_mrp, 0 ) ); ?>
								</span>
								<span class="font-serif text-3xl font-bold text-hp-text-1 leading-none">
									₹<?php echo esc_html( number_format( $discounted_price, 0 ) ); ?>
								</span>
							</div>
							<div class="text-sm font-semibold text-hp-coral">
								<?php printf( esc_html__( 'You save ₹%s', 'herbalpearls' ), number_format( $save_amount, 0 ) ); ?>
							</div>
						</div>
					<?php endif; ?>

					<!-- Reuse the homepage trust strip component instead of re-implementing -->
					<div class="mt-6">
						<?php echo do_shortcode( '[hp_trust_strip]' ); ?>
					</div>
				</div>

				<!-- Form column -->
				<div>
					<div class="hp-section-h text-left mb-6">
						<span class="hp-section-h__accent text-left">
							<?php esc_html_e( 'what you get', 'herbalpearls' ); ?>
						</span>
						<h2 class="hp-section-h__title text-left text-[clamp(1.5rem,3vw,2rem)]">
							<?php esc_html_e( "What's Included", 'herbalpearls' ); ?>
						</h2>
					</div>

					<form id="hp-bundle-form" data-bundle-id="<?php echo esc_attr( $bundle_id ); ?>" class="flex flex-col gap-3">
						<?php foreach ( $valid_items as $item ) :
							$product   = $item['product'];
							$item_mrp  = $item['price'];
							$item_sale = $item_mrp * ( 1 - $discount / 100 );
							?>
							<div class="flex gap-4 p-4 bg-hp-bg-pure border border-hp-line-1 rounded-md items-center xs:flex-col xs:items-stretch sm:flex-row sm:items-center">
								<div class="w-[72px] h-[72px] flex-shrink-0 rounded-md overflow-hidden bg-hp-bg-page">
									<?php
									$thumb_id = $product->get_image_id();
									$thumb_alt = $thumb_id ? get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) : '';
									if ( empty( $thumb_alt ) ) {
										$thumb_alt = $product->get_name();
									}
									echo $product->get_image(
										'thumbnail',
										[
											'class'   => 'w-full h-full object-cover',
											'alt'     => esc_attr( $thumb_alt ),
											'loading' => 'lazy',
										]
									); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									?>
								</div>

								<div class="flex-1 min-w-0">
									<div class="flex items-start justify-between gap-2">
										<h3 class="font-serif text-[15px] font-semibold text-hp-text-1 m-0 leading-tight">
											<?php echo esc_html( $product->get_name() ); ?>
										</h3>
										<span class="text-[11px] font-medium text-hp-text-4 whitespace-nowrap">
											<?php printf( esc_html__( 'Worth ₹%s', 'herbalpearls' ), number_format( $item_mrp, 0 ) ); ?>
										</span>
									</div>
									<div class="flex items-baseline gap-2 mt-1 mb-2">
										<span class="text-[13px] text-hp-text-4 line-through">
											₹<?php echo esc_html( number_format( $item_mrp, 0 ) ); ?>
										</span>
										<span class="text-[15px] font-semibold text-hp-gold-700">
											₹<?php echo esc_html( number_format( $item_sale, 0 ) ); ?>
										</span>
									</div>

									<?php if ( $product->is_type( 'variable' ) ) : ?>
										<label class="block text-[13px] font-medium text-hp-text-3 mb-1">
											<?php esc_html_e( 'Choose size:', 'herbalpearls' ); ?>
										</label>
										<select
											name="selections[<?php echo esc_attr( $product->get_id() ); ?>]"
											required
											class="hp-select w-full max-w-[280px]"
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

						<button type="submit" class="hp-btn hp-btn--primary hp-btn--lg hp-btn--block mt-4">
							<?php printf( esc_html__( 'Add Bundle to Cart — ₹%s', 'herbalpearls' ), number_format( $discounted_price, 0 ) ); ?>
						</button>
						<p class="hp-bundle-msg text-center text-sm min-h-[1.25rem] mt-2" aria-live="polite"></p>
					</form>
				</div>
			</div>

			<?php $bundle_content = get_the_content();
			if ( $bundle_content ) : ?>
				<div class="max-w-prose-hp mx-auto pt-10 mt-12 border-t border-hp-line-1 prose prose-sm">
					<?php echo wp_kses_post( wpautop( $bundle_content ) ); ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<?php /* Why This Bundle Works */ ?>
	<?php if ( ! empty( $rationale ) ) : ?>
		<section class="hp-section hp-surface-1" aria-labelledby="hp-bundle-why-title">
			<div class="hp-container">
				<div class="hp-section-h">
					<span class="hp-section-h__accent"><?php esc_html_e( 'the synergy', 'herbalpearls' ); ?></span>
					<h2 id="hp-bundle-why-title" class="hp-section-h__title"><?php esc_html_e( 'Why This Bundle Works', 'herbalpearls' ); ?></h2>
				</div>
				<div class="max-w-prose-hp mx-auto text-base leading-relaxed text-hp-text-2">
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
				<div class="hp-tw-steps prose max-w-none">
					<?php echo wp_kses_post( $protocol ); ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php /* FAQ accordion */ ?>
	<?php if ( ! empty( $faqs ) ) : ?>
		<section class="hp-section hp-surface-2" aria-labelledby="hp-bundle-faq-title">
			<div class="hp-container max-w-prose-hp">
				<div class="hp-section-h">
					<span class="hp-section-h__accent"><?php esc_html_e( 'good to know', 'herbalpearls' ); ?></span>
					<h2 id="hp-bundle-faq-title" class="hp-section-h__title"><?php esc_html_e( 'Frequently Asked Questions', 'herbalpearls' ); ?></h2>
				</div>
				<div class="hp-accordion bg-hp-bg-pure rounded-lg border border-hp-line-1 px-5" data-accordion>
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
					<span class="hp-section-h__accent"><?php esc_html_e( 'more curated combos', 'herbalpearls' ); ?></span>
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
									<?php echo get_the_post_thumbnail( $bundle_post, 'hp-product-card', [ 'alt' => esc_attr( $bundle_post->post_title ), 'loading' => 'lazy' ] ); ?>
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
											$bp_alt = $bp->get_name();
											?>
											<div class="hp-bundle-card__child-thumb">
												<?php echo $bp->get_image( 'thumbnail', [ 'alt' => esc_attr( $bp_alt ), 'loading' => 'lazy' ] ); // phpcs:ignore ?>
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
	<aside class="hp-tw-disclaimer hp-container">
		<h4><?php esc_html_e( 'Disclaimer', 'herbalpearls' ); ?></h4>
		<p><?php esc_html_e( 'Statements and product information have not been evaluated by any regulatory authority unless specified. Herbal Pearls products are intended for cosmetic use only and are not a substitute for medical advice, diagnosis, or treatment. Individual results may vary. Patch-test before first use.', 'herbalpearls' ); ?></p>
	</aside>

	<?php
endwhile;

get_footer();
