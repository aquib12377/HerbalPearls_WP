<?php
/**
 * Product tabs — custom tab set with meta box data.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
$product_id = $product->get_id();

$tabs = [];

// How to Use
$how_to = get_post_meta( $product_id, '_hp_how_to_short', true );
if ( $how_to ) {
	$tabs['how_to'] = [
		'title'    => __( 'How to Use', 'herbalpearls' ),
		'content'  => wp_kses_post( $how_to ),
	];
}

// Key Ingredients
$ingredients = hp_get_product_ingredients( $product_id );
if ( ! empty( $ingredients ) ) {
	ob_start();
	echo '<ul class="hp-ingredient-list">';
	foreach ( $ingredients as $ing ) {
		?>
		<li class="hp-ingredient-chip" style="margin-bottom: 0.5rem;">
			<svg class="hp-ingredient-chip__icon" width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
				<path d="M12 2C8 6 2 10 2 14c0 5.5 4.5 8 10 8s10-2.5 10-8c0-4-6-8-10-12z" fill="none" stroke="currentColor" stroke-width="1.5"/>
			</svg>
			<strong><?php echo esc_html( $ing['name'] ); ?></strong>
			<?php if ( ! empty( $ing['benefit'] ) ) : ?>
				&mdash; <?php echo esc_html( $ing['benefit'] ); ?>
			<?php endif; ?>
		</li>
		<?php
	}
	echo '</ul>';
	$tabs['ingredients'] = [
		'title'   => __( 'Key Ingredients', 'herbalpearls' ),
		'content' => ob_get_clean(),
	];
}

// Full Ingredients (from product description)
$long_desc = $product->get_description();
if ( $long_desc ) {
	$tabs['full_details'] = [
		'title'   => __( 'Full Details', 'herbalpearls' ),
		'content' => wp_kses_post( wpautop( $long_desc ) ),
	];
}

// FAQs from meta box
$faqs = hp_get_product_faqs( $product_id );
if ( ! empty( $faqs ) ) {
	ob_start();
	echo '<div class="hp-accordion">';
	foreach ( $faqs as $faq ) {
		?>
		<div class="hp-accordion__item">
			<button type="button" class="hp-accordion__trigger" aria-expanded="false">
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
		<?php
	}
	echo '</div>';
	$tabs['faqs'] = [
		'title'   => __( 'FAQs', 'herbalpearls' ),
		'content' => ob_get_clean(),
	];
}

// Shipping & Returns (static)
$tabs['shipping'] = [
	'title'   => __( 'Shipping & Returns', 'herbalpearls' ),
	'content' => '<p>' . esc_html__( 'Free shipping on orders above ₹499. Delivery within 5-7 business days across India. Easy returns within 7 days of delivery — products must be unopened and in original packaging.', 'herbalpearls' ) . '</p>',
];

// Render tabs
if ( ! empty( $tabs ) ) :
	?>
	<div class="hp-accordion hp-mt-3" data-accordion>
		<?php foreach ( $tabs as $key => $tab ) : ?>
			<div class="hp-accordion__item">
				<button
					type="button"
					class="hp-accordion__trigger"
					aria-expanded="false"
					data-accordion-trigger
				>
					<span><?php echo esc_html( $tab['title'] ); ?></span>
					<svg class="hp-accordion__icon" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
						<path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" fill="none"/>
					</svg>
				</button>
				<div class="hp-accordion__panel">
					<div class="hp-accordion__content">
						<?php echo wp_kses_post( $tab['content'] ); ?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
