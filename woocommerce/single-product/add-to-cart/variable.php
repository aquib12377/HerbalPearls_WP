<?php
/**
 * Variable product add-to-cart — pill selector with self-contained
 * variation lookup. We resolve `variation_id` from the parent's
 * `data-product_variations` JSON ourselves, so the form is safe to
 * submit regardless of whether WC's add-to-cart-variation JS has
 * initialized (cache / load-order / jQuery issues that previously
 * left variation_id=0 → "no product has been selected" error).
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

// First (and only — for now) attribute
$attribute_name = '';
foreach ( $product->get_variation_attributes() as $attr_name => $options ) {
	$attribute_name = $attr_name;
	break;
}

if ( '' === $attribute_name ) {
	wc_get_template( 'single-product/add-to-cart/variation.php' );
	return;
}

$attribute_slug   = sanitize_title( $attribute_name );
$attribute_label  = wc_attribute_label( $attribute_name );
$attribute_key    = 'attribute_' . $attribute_slug;
$attribute_values = $product->get_variation_attributes()[ $attribute_name ];

// Still enqueue WC's variation script for compatibility (price html,
// gallery swap on variation change). Our inline JS sets variation_id
// independently so the form is safe to submit even if WC's JS is slow.
wp_enqueue_script( 'wc-add-to-cart-variation' );
?>

<form class="variations_form cart hp-var-form" method="post"
	  enctype="multipart/form-data"
	  data-product_id="<?php echo absint( $product->get_id() ); ?>"
	  data-product_variations="<?php echo esc_attr( wp_json_encode( $variations ) ); ?>">

	<div class="variations hp-var-form__variations">
		<div class="hp-var-form__label">
			<?php
			/* translators: %s: attribute label (e.g. "Size") */
			printf( esc_html__( 'Choose %s', 'herbalpearls' ), esc_html( $attribute_label ) );
			?>
		</div>

		<div class="hp-pill-group hp-var-form__pills" role="radiogroup" aria-label="<?php echo esc_attr( $attribute_label ); ?>" data-attribute_name="<?php echo esc_attr( $attribute_slug ); ?>">
			<?php foreach ( $attribute_values as $option ) :
				$enabled        = false;
				$matched_label  = '';
				foreach ( $variations as $variation ) {
					if (
						isset( $variation['attributes'][ $attribute_key ] )
						&& $variation['attributes'][ $attribute_key ] === $option
					) {
						$enabled = $variation['is_in_stock'];
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
			name="<?php echo esc_attr( $attribute_key ); ?>"
			class="hp-var-form__select"
			aria-hidden="true"
			tabindex="-1"
		>
			<option value=""><?php esc_html_e( 'Choose an option', 'herbalpearls' ); ?></option>
			<?php foreach ( $attribute_values as $option ) : ?>
				<option value="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( $option ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>

	<!-- Live price / stock readout for the selected variation -->
	<div class="hp-var-form__readout single_variation woocommerce-variation" aria-live="polite"></div>

	<div class="woocommerce-variation-add-to-cart variations_button hp-var-form__actions">
		<div class="hp-qty">
			<button type="button" class="hp-qty__btn" data-action="minus" aria-label="<?php esc_attr_e( 'Decrease quantity', 'herbalpearls' ); ?>">&minus;</button>
			<input
				type="number"
				name="quantity"
				class="hp-qty__input"
				value="1"
				min="1"
				step="1"
				aria-label="<?php esc_attr_e( 'Quantity', 'herbalpearls' ); ?>"
			>
			<button type="button" class="hp-qty__btn" data-action="plus" aria-label="<?php esc_attr_e( 'Increase quantity', 'herbalpearls' ); ?>">+</button>
		</div>

		<button
			type="submit"
			class="hp-btn hp-btn--primary hp-btn--lg single_add_to_cart_button hp-var-form__submit"
			disabled
		>
			<span class="hp-var-form__submit-label"><?php esc_html_e( 'Select an option', 'herbalpearls' ); ?></span>
		</button>

		<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>">
		<input type="hidden" name="product_id"  value="<?php echo absint( $product->get_id() ); ?>">
		<input type="hidden" name="variation_id" class="variation_id" value="0">
	</div>
</form>

<script>
(function () {
	'use strict';
	var forms = document.querySelectorAll('.hp-var-form');
	if (!forms.length) return;

	forms.forEach(function (form) {
		var pills        = form.querySelectorAll('.hp-var-pill');
		var select       = form.querySelector('.hp-var-form__select');
		var variationId  = form.querySelector('.variation_id');
		var submit       = form.querySelector('.hp-var-form__submit');
		var submitLabel  = form.querySelector('.hp-var-form__submit-label');
		var readout      = form.querySelector('.hp-var-form__readout');
		var qtyInput     = form.querySelector('input[name="quantity"]');
		var addText      = <?php echo wp_json_encode( __( 'Add to Cart', 'herbalpearls' ) ); ?>;
		var oosText      = <?php echo wp_json_encode( __( 'Out of stock', 'herbalpearls' ) ); ?>;
		var pickText     = <?php echo wp_json_encode( __( 'Select an option', 'herbalpearls' ) ); ?>;
		var attrKey      = select ? select.name : 'attribute_<?php echo esc_js( $attribute_slug ); ?>';

		var variations = [];
		try {
			variations = JSON.parse(form.getAttribute('data-product_variations') || '[]');
		} catch (e) {}

		function pickVariation(value) {
			pills.forEach(function (p) {
				var on = p.dataset.value === value;
				p.classList.toggle('is-active', on);
				p.setAttribute('aria-checked', on ? 'true' : 'false');
			});

			if (select) {
				select.value = value;
			}

			var match = null;
			for (var i = 0; i < variations.length; i++) {
				if (variations[i].attributes && variations[i].attributes[attrKey] === value) {
					match = variations[i];
					break;
				}
			}

			if (!match) {
				variationId.value = '0';
				submit.disabled = true;
				submitLabel.textContent = pickText;
				readout.style.display = 'none';
				readout.innerHTML = '';
				return;
			}

			variationId.value = match.variation_id;

			if (match.is_in_stock && match.is_purchasable !== false) {
				submit.disabled = false;
				submitLabel.textContent = addText;
			} else {
				submit.disabled = true;
				submitLabel.textContent = oosText;
			}

			if (qtyInput && match.max_qty && match.max_qty > 0) {
				qtyInput.max = match.max_qty;
			}

			var html = '';
			if (match.price_html) {
				html += '<div class="hp-var-form__price">' + match.price_html + '</div>';
			}
			if (match.availability_html) {
				html += '<div class="hp-var-form__stock">' + match.availability_html + '</div>';
			}
			readout.innerHTML = html;
			readout.style.display = html ? 'block' : 'none';

			// Let WC's variation JS pick up the change too (gallery, etc.)
			if (select && window.jQuery) {
				window.jQuery(select).trigger('change');
			}
		}

		pills.forEach(function (pill) {
			pill.addEventListener('click', function () {
				if (pill.disabled) return;
				pickVariation(pill.dataset.value);
			});
		});

		// Belt-and-braces: if WC's JS updates variation_id to a known
		// good value (e.g. from URL ?attribute_size=…), reflect it
		// in our UI.
		if (select) {
			select.addEventListener('change', function () {
				if (select.value) pickVariation(select.value);
			});
		}
	});
})();
</script>
