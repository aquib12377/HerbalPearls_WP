<?php
/**
 * Custom bundle system for Herbal Pearls.
 * Replaces WooCommerce Product Bundles plugin.
 * From v2 Appendix A.1 — verbatim.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ─────────────────────────── 1. Register Bundle CPT ─────────────────────────── */
add_action( 'init', function() {
	register_post_type( 'hp_bundle', [
		'labels' => [
			'name'               => __( 'Bundles', 'herbalpearls' ),
			'singular_name'      => __( 'Bundle', 'herbalpearls' ),
			'add_new_item'       => __( 'Add New Bundle', 'herbalpearls' ),
			'edit_item'          => __( 'Edit Bundle', 'herbalpearls' ),
			'new_item'           => __( 'New Bundle', 'herbalpearls' ),
			'view_item'          => __( 'View Bundle', 'herbalpearls' ),
			'search_items'       => __( 'Search Bundles', 'herbalpearls' ),
			'menu_name'          => __( 'Bundles', 'herbalpearls' ),
		],
		'public'              => true,
		'show_in_menu'        => true,
		'show_in_rest'        => true,
		'has_archive'         => 'bundles',
		'rewrite'             => [ 'slug' => 'bundles' ],
		'menu_icon'           => 'dashicons-products',
		'menu_position'       => 20,
		'supports'            => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
		'capability_type'     => 'post',
	] );
} );

/* ─────────────────────────── 2. Bundle Meta Boxes ─────────────────────────── */
add_action( 'add_meta_boxes', function() {
	add_meta_box( 'hp_bundle_items',    __( 'Bundle Items', 'herbalpearls' ),     'hp_render_bundle_items_mb',    'hp_bundle', 'normal', 'high' );
	add_meta_box( 'hp_bundle_pricing',  __( 'Bundle Pricing', 'herbalpearls' ),   'hp_render_bundle_pricing_mb',  'hp_bundle', 'side',   'default' );
} );

function hp_render_bundle_items_mb( $post ) {
	wp_nonce_field( 'hp_bundle_save', 'hp_bundle_nonce' );
	$items = get_post_meta( $post->ID, '_hp_bundle_items', true );
	if ( ! is_array( $items ) ) {
		$items = [];
	}

	$products = wc_get_products( [
		'limit'   => -1,
		'status'  => 'publish',
		'orderby' => 'title',
		'order'   => 'ASC',
	] );
	?>
	<div id="hp-bundle-items">
		<?php foreach ( $items as $i => $item ) : ?>
		<div class="hp-bundle-item-row" style="display:flex;gap:8px;margin-bottom:8px;align-items:center;">
			<select name="hp_bundle_items[<?php echo (int) $i; ?>][product_id]" style="flex:2;">
				<option value="">&mdash; <?php esc_html_e( 'Select product', 'herbalpearls' ); ?> &mdash;</option>
				<?php foreach ( $products as $p ) : ?>
				<option value="<?php echo esc_attr( $p->get_id() ); ?>" <?php selected( $item['product_id'] ?? '', $p->get_id() ); ?>>
					<?php echo esc_html( $p->get_name() ); ?>
				</option>
				<?php endforeach; ?>
			</select>
			<input type="number" name="hp_bundle_items[<?php echo (int) $i; ?>][qty]"
				   value="<?php echo esc_attr( $item['qty'] ?? 1 ); ?>"
				   min="1" style="flex:1;" placeholder="<?php esc_attr_e( 'Qty', 'herbalpearls' ); ?>">
			<button type="button" class="button hp-remove-item">&times;</button>
		</div>
		<?php endforeach; ?>
	</div>
	<button type="button" class="button button-secondary" id="hp-add-item">+ <?php esc_html_e( 'Add product to bundle', 'herbalpearls' ); ?></button>
	<p class="description"><?php esc_html_e( 'Customer picks the variation (size) on the bundle page. Quantity above is units of that product.', 'herbalpearls' ); ?></p>
	<script>
	jQuery(function($){
		var products = <?php echo wp_json_encode(
			array_map( function( $p ) {
				return [ 'id' => $p->get_id(), 'name' => $p->get_name() ];
			}, $products )
		); ?>;

		$('#hp-add-item').on('click', function(){
			var i = $('.hp-bundle-item-row').length;
			var opts = '<option value="">&mdash; Select product &mdash;</option>';
			$.each(products, function(_, p){
				opts += '<option value="'+p.id+'">'+p.name+'</option>';
			});
			$('#hp-bundle-items').append(
				'<div class="hp-bundle-item-row" style="display:flex;gap:8px;margin-bottom:8px;align-items:center;">' +
				'<select name="hp_bundle_items['+i+'][product_id]" style="flex:2;">'+opts+'</select>' +
				'<input type="number" name="hp_bundle_items['+i+'][qty]" value="1" min="1" style="flex:1;" placeholder="Qty">' +
				'<button type="button" class="button hp-remove-item">&times;</button>' +
				'</div>'
			);
		});
		$(document).on('click', '.hp-remove-item', function(){
			$(this).closest('.hp-bundle-item-row').remove();
		});
	});
	</script>
	<?php
}

function hp_render_bundle_pricing_mb( $post ) {
	$discount = get_post_meta( $post->ID, '_hp_bundle_discount_pct', true );
	if ( '' === $discount ) {
		$discount = 10;
	}
	?>
	<p>
		<label><strong><?php esc_html_e( 'Bundle Discount %', 'herbalpearls' ); ?></strong></label><br>
		<input type="number" name="hp_bundle_discount_pct"
			   value="<?php echo esc_attr( $discount ); ?>"
			   min="0" max="50" step="1" style="width:80px;"> %
	</p>
	<p class="description"><?php esc_html_e( 'Applied to the sum of selected items at cart calculation time.', 'herbalpearls' ); ?></p>
	<?php
}

/* ─────────────────────────── 3. Save Bundle Meta ─────────────────────────── */
add_action( 'save_post_hp_bundle', function( $post_id ) {
	if ( ! isset( $_POST['hp_bundle_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['hp_bundle_nonce'], 'hp_bundle_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Items
	$items = [];
	if ( isset( $_POST['hp_bundle_items'] ) && is_array( $_POST['hp_bundle_items'] ) ) {
		foreach ( wp_unslash( $_POST['hp_bundle_items'] ) as $row ) {
			$pid = absint( $row['product_id'] ?? 0 );
			$qty = max( 1, absint( $row['qty'] ?? 1 ) );
			if ( $pid > 0 ) {
				$items[] = [ 'product_id' => $pid, 'qty' => $qty ];
			}
		}
	}
	update_post_meta( $post_id, '_hp_bundle_items', $items );

	// Discount
	$discount = isset( $_POST['hp_bundle_discount_pct'] )
		? max( 0, min( 50, absint( $_POST['hp_bundle_discount_pct'] ) ) )
		: 10;
	update_post_meta( $post_id, '_hp_bundle_discount_pct', $discount );
} );

/* ─────────────────────────── 4. AJAX: Add Bundle to Cart ─────────────────────────── */
add_action( 'wp_ajax_hp_add_bundle_to_cart',        'hp_handle_add_bundle' );
add_action( 'wp_ajax_nopriv_hp_add_bundle_to_cart', 'hp_handle_add_bundle' );

function hp_handle_add_bundle() {
	check_ajax_referer( 'hp_add_bundle', 'nonce' );

	$bundle_id = absint( $_POST['bundle_id'] ?? 0 );
	if ( ! $bundle_id || get_post_type( $bundle_id ) !== 'hp_bundle' ) {
		wp_send_json_error( [ 'message' => __( 'Invalid bundle.', 'herbalpearls' ) ], 400 );
	}

	$items = get_post_meta( $bundle_id, '_hp_bundle_items', true );
	if ( empty( $items ) ) {
		wp_send_json_error( [ 'message' => __( 'Bundle has no items.', 'herbalpearls' ) ], 400 );
	}

	$selections = $_POST['selections'] ?? [];

	$bundle_token = wp_generate_uuid4();

	foreach ( $items as $item ) {
		$product_id   = $item['product_id'];
		$qty          = $item['qty'];
		$variation_id = absint( $selections[ $product_id ] ?? 0 );

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			continue;
		}

		$variation_attrs = [];
		if ( $variation_id && $product->is_type( 'variable' ) ) {
			$variation = wc_get_product( $variation_id );
			if ( $variation ) {
				$variation_attrs = $variation->get_variation_attributes();
			}
		} elseif ( $product->is_type( 'variable' ) ) {
			wp_send_json_error( [
				'message' => sprintf(
					/* translators: %s: product name */
					__( 'Please select a size for %s', 'herbalpearls' ),
					$product->get_name()
				),
			], 400 );
		}

		$cart_item_data = [
			'hp_bundle_id'    => $bundle_id,
			'hp_bundle_token' => $bundle_token,
		];

		$added = WC()->cart->add_to_cart(
			$product_id,
			$qty,
			$variation_id,
			$variation_attrs,
			$cart_item_data
		);

		if ( ! $added ) {
			wp_send_json_error( [
				'message' => sprintf(
					/* translators: %s: product name */
					__( 'Could not add %s to cart.', 'herbalpearls' ),
					$product->get_name()
				),
			], 500 );
		}
	}

	wp_send_json_success( [
		'cart_count' => WC()->cart->get_cart_contents_count(),
		'cart_url'   => wc_get_cart_url(),
	] );
}

/* ─────────────────────────── 5. Apply Bundle Discount on Cart Calc ─────────────────────────── */
add_action( 'woocommerce_before_calculate_totals', function( $cart ) {
	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}

	$discount_cache = [];

	foreach ( $cart->get_cart() as $cart_item ) {
		if ( empty( $cart_item['hp_bundle_id'] ) ) {
			continue;
		}

		$bundle_id = $cart_item['hp_bundle_id'];
		if ( ! isset( $discount_cache[ $bundle_id ] ) ) {
			$pct = (int) get_post_meta( $bundle_id, '_hp_bundle_discount_pct', true );
			$discount_cache[ $bundle_id ] = max( 0, min( 50, $pct ) ) / 100;
		}
		$factor = 1 - $discount_cache[ $bundle_id ];
		$original = $cart_item['data']->get_price();
		$cart_item['data']->set_price( $original * $factor );
	}
}, 20, 1 );

/* ─────────────────────────── 6. Display Bundle Label in Cart ─────────────────────────── */
add_filter( 'woocommerce_get_item_data', function( $item_data, $cart_item ) {
	if ( empty( $cart_item['hp_bundle_id'] ) ) {
		return $item_data;
	}
	$bundle = get_post( $cart_item['hp_bundle_id'] );
	if ( ! $bundle ) {
		return $item_data;
	}

	$item_data[] = [
		'key'     => __( 'Bundle', 'herbalpearls' ),
		'value'   => $bundle->post_title,
		'display' => $bundle->post_title . ' (10% off)',
	];
	return $item_data;
}, 10, 2 );

/* ─────────────────────────── 7. Persist Bundle Meta on Order ─────────────────────────── */
add_action( 'woocommerce_checkout_create_order_line_item', function( $item, $cart_item_key, $values, $order ) {
	if ( ! empty( $values['hp_bundle_id'] ) ) {
		$bundle = get_post( $values['hp_bundle_id'] );
		if ( $bundle ) {
			$item->add_meta_data( __( 'Bundle', 'herbalpearls' ), $bundle->post_title, true );
			$item->add_meta_data( '_hp_bundle_id', $values['hp_bundle_id'], true );
			$item->add_meta_data( '_hp_bundle_token', $values['hp_bundle_token'], true );
		}
	}
}, 10, 4 );

/* ─────────────────────────── 8. Additional Bundle Meta Boxes ─────────────────────────── */
add_action( 'add_meta_boxes', function() {
	add_meta_box( 'hp_bundle_rationale', __( 'Why This Bundle Works', 'herbalpearls' ), 'hp_render_bundle_rationale_mb', 'hp_bundle', 'normal', 'default' );
	add_meta_box( 'hp_bundle_protocol',  __( 'How to Use Together', 'herbalpearls' ),  'hp_render_bundle_protocol_mb',  'hp_bundle', 'normal', 'default' );
	add_meta_box( 'hp_bundle_faqs_mb',    __( 'Bundle FAQs', 'herbalpearls' ),          'hp_render_bundle_faqs_mb',      'hp_bundle', 'normal', 'default' );
} );

function hp_render_bundle_rationale_mb( $post ) {
	$val = get_post_meta( $post->ID, '_hp_bundle_rationale', true );
	wp_editor( $val, 'hp_bundle_rationale', [
		'textarea_name' => 'hp_bundle_rationale',
		'media_buttons' => false,
		'textarea_rows' => 6,
		'teeny'         => true,
	] );
}

function hp_render_bundle_protocol_mb( $post ) {
	$val = get_post_meta( $post->ID, '_hp_bundle_protocol', true );
	wp_editor( $val, 'hp_bundle_protocol', [
		'textarea_name' => 'hp_bundle_protocol',
		'media_buttons' => false,
		'textarea_rows' => 6,
		'teeny'         => true,
	] );
}

function hp_render_bundle_faqs_mb( $post ) {
	$faqs = get_post_meta( $post->ID, '_hp_bundle_faqs', true );
	if ( ! is_array( $faqs ) ) {
		$faqs = [];
	}
	?>
	<div id="hp-bundle-faq-wrap">
		<?php foreach ( $faqs as $i => $faq ) : ?>
			<div class="hp-bundle-faq-row" style="margin-bottom:12px;border:1px solid #ddd;padding:8px;border-radius:4px;">
				<input type="text" name="hp_bundle_faqs[<?php echo (int) $i; ?>][question]"
					   value="<?php echo esc_attr( $faq['question'] ?? '' ); ?>"
					   placeholder="<?php esc_attr_e( 'Question', 'herbalpearls' ); ?>" style="width:100%;margin-bottom:4px;">
				<textarea name="hp_bundle_faqs[<?php echo (int) $i; ?>][answer]" rows="3"
						  placeholder="<?php esc_attr_e( 'Answer', 'herbalpearls' ); ?>" style="width:100%;"><?php echo esc_textarea( $faq['answer'] ?? '' ); ?></textarea>
				<button type="button" class="button button-small hp-bundle-faq-remove"><?php esc_html_e( 'Remove', 'herbalpearls' ); ?></button>
			</div>
		<?php endforeach; ?>
	</div>
	<button type="button" class="button" id="hp-bundle-faq-add">+ <?php esc_html_e( 'Add FAQ', 'herbalpearls' ); ?></button>
	<script>
	jQuery(function($){
		$('#hp-bundle-faq-add').on('click', function(){
			var i = $('#hp-bundle-faq-wrap .hp-bundle-faq-row').length;
			$.post(ajaxurl, { action: 'hp_get_bundle_faq_row', i: i }, function(html){
				$('#hp-bundle-faq-wrap').append(html);
			});
		});
		$(document).on('click', '.hp-bundle-faq-remove', function(){
			$(this).closest('.hp-bundle-faq-row').remove();
		});
	});
	</script>
	<?php
}

add_action( 'wp_ajax_hp_get_bundle_faq_row', function() {
	$i = absint( $_POST['i'] ?? 0 );
	?>
	<div class="hp-bundle-faq-row" style="margin-bottom:12px;border:1px solid #ddd;padding:8px;border-radius:4px;">
		<input type="text" name="hp_bundle_faqs[<?php echo (int) $i; ?>][question]"
			   value="" placeholder="<?php esc_attr_e( 'Question', 'herbalpearls' ); ?>" style="width:100%;margin-bottom:4px;">
		<textarea name="hp_bundle_faqs[<?php echo (int) $i; ?>][answer]" rows="3"
				  placeholder="<?php esc_attr_e( 'Answer', 'herbalpearls' ); ?>" style="width:100%;"></textarea>
		<button type="button" class="button button-small hp-bundle-faq-remove"><?php esc_html_e( 'Remove', 'herbalpearls' ); ?></button>
	</div>
	<?php
	wp_die();
} );

/* Extend save handler to include new fields */
add_action( 'save_post_hp_bundle', function( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['hp_bundle_rationale'] ) ) {
		update_post_meta( $post_id, '_hp_bundle_rationale', wp_kses_post( wp_unslash( $_POST['hp_bundle_rationale'] ) ) );
	}

	if ( isset( $_POST['hp_bundle_protocol'] ) ) {
		update_post_meta( $post_id, '_hp_bundle_protocol', wp_kses_post( wp_unslash( $_POST['hp_bundle_protocol'] ) ) );
	}

	if ( isset( $_POST['hp_bundle_faqs'] ) && is_array( $_POST['hp_bundle_faqs'] ) ) {
		$faqs = [];
		foreach ( wp_unslash( $_POST['hp_bundle_faqs'] ) as $faq ) {
			$q = sanitize_text_field( $faq['question'] ?? '' );
			$a = wp_kses_post( $faq['answer'] ?? '' );
			if ( '' !== $q && '' !== $a ) {
				$faqs[] = [ 'question' => $q, 'answer' => $a ];
			}
		}
		update_post_meta( $post_id, '_hp_bundle_faqs', $faqs );
	}
}, 20 );
