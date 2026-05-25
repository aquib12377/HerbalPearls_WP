<?php
/**
 * Variable product add-to-cart — size pill selector.
 * Replaces default dropdown with pill UI.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! $product instanceof WC_Product_Variable ) {
	return;
}

$variations = $product->get_available_variations();
if ( empty( $variations ) ) {
	return;
}

// Group by attribute (size)
$attribute_name = '';
foreach ( $product->get_variation_attributes() as $attr_name => $options ) {
	$attribute_name = $attr_name;
	break;
}

if ( '' === $attribute_name ) {
	// Fallback to default dropdown
	wc_get_template( 'single-product/add-to-cart/variation.php' );
	return;
}

$attribute_slug = sanitize_title( $attribute_name );
wp_enqueue_script( 'wc-add-to-cart-variation' );
?>

<form class="variations_form cart" method="post"
	  enctype="multipart/form-data"
	  data-product_id="<?php echo absint( $product->get_id() ); ?>"
	  data-product_variations="<?php echo esc_attr( wp_json_encode( $variations ) ); ?>">

	<div class="variations">
		<div class="label">
			<?php
			printf(
				/* translators: %s: attribute name */
				esc_html__( 'Choose %s', 'herbalpearls' ),
				esc_html( wc_attribute_label( $attribute_name ) )
			);
			?>
		</div>
		<div class="value hp-pill-group" data-attribute_name="<?php echo esc_attr( $attribute_slug ); ?>">
			<?php
			foreach ( $product->get_variation_attributes()[ $attribute_name ] as $option ) :
				$enabled = false;
				foreach ( $variations as $variation ) {
					if (
						isset( $variation['attributes'][ 'attribute_' . $attribute_slug ] )
						&& $variation['attributes'][ 'attribute_' . $attribute_slug ] === $option
						&& $variation['is_in_stock']
					) {
						$enabled = true;
						break;
					}
				}
				?>
				<button
					type="button"
					class="hp-var-pill <?php echo $enabled ? '' : 'is-disabled'; ?>"
					data-value="<?php echo esc_attr( $option ); ?>"
					<?php echo $enabled ? '' : 'disabled'; ?>
					role="radio"
					aria-checked="false"
				>
					<?php echo esc_html( $option ); ?>
				</button>
			<?php endforeach; ?>
		</div>

		<select
			id="<?php echo esc_attr( $attribute_slug ); ?>"
			name="attribute_<?php echo esc_attr( $attribute_slug ); ?>"
			class="hp-input"
			style="display:none;"
		>
			<option value=""><?php esc_html_e( 'Choose an option', 'herbalpearls' ); ?></option>
			<?php foreach ( $product->get_variation_attributes()[ $attribute_name ] as $option ) : ?>
				<option value="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( $option ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>

	<div class="single_variation_wrap">
		<div class="woocommerce-variation single_variation" style="display:none;"></div>

		<div class="woocommerce-variation-add-to-cart variations_button">
			<div class="hp-flex" style="gap: 0.75rem; align-items: center;">
				<div class="hp-qty">
					<button type="button" class="hp-qty__btn" data-action="minus" aria-label="<?php esc_attr_e( 'Decrease quantity', 'herbalpearls' ); ?>">&minus;</button>
					<input
						type="number"
						name="quantity"
						class="hp-qty__input"
						value="1"
						min="1"
						max=""
						aria-label="<?php esc_attr_e( 'Quantity', 'herbalpearls' ); ?>"
					>
					<button type="button" class="hp-qty__btn" data-action="plus" aria-label="<?php esc_attr_e( 'Increase quantity', 'herbalpearls' ); ?>">+</button>
				</div>

				<button
					type="submit"
					class="hp-btn hp-btn--primary hp-btn--lg single_add_to_cart_button"
					style="flex:1;"
				>
					<?php esc_html_e( 'Add to Cart', 'herbalpearls' ); ?>
				</button>
			</div>

			<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>">
			<input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>">
			<input type="hidden" name="variation_id" class="variation_id" value="0">
		</div>
	</div>
</form>
