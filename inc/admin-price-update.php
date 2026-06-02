<?php
/**
 * One-time bulk price + variation migration tool.
 *
 * Adds a "Update Prices" page under WooCommerce that:
 *   1. Maps brand names in the 2026 price list onto WC products by
 *      title (with a configurable alias map for renamed products).
 *   2. For brand groups with multiple sizes, converts the existing
 *      simple product to variable and creates each size as a
 *      variation with its own regular_price + sale_price.
 *   3. For brand groups with a single size, updates the simple
 *      product's price in place.
 *   4. Re-runnable: updates existing variations by size; only adds
 *      ones that don't exist yet.
 *
 * Cap: `manage_woocommerce` only. Nonce-protected.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ─────────────── Data ─────────────── */

/**
 * The price list. MRP becomes regular_price; selling becomes sale_price.
 * `size` is matched against a "Size" attribute value (case-insensitive).
 */
function hp_price_list() {
	return [
		// Weight Loss Powder
		[ 'product' => 'Weight Loss Powder',     'size' => '100gm', 'mrp' => 499,  'sale' => 349 ],
		[ 'product' => 'Weight Loss Powder',     'size' => '500gm', 'mrp' => 1850, 'sale' => 1049 ],
		[ 'product' => 'Weight Loss Powder',     'size' => '1kg',   'mrp' => 2249, 'sale' => 1749 ],
		// Hair Oil
		[ 'product' => 'Hair Oil',               'size' => '100ml', 'mrp' => 499,  'sale' => 349 ],
		[ 'product' => 'Hair Oil',               'size' => '200ml', 'mrp' => 600,  'sale' => 499 ],
		[ 'product' => 'Hair Oil',               'size' => '500ml', 'mrp' => 999,  'sale' => 849 ],
		// Skin Whitening Cream
		[ 'product' => 'Skin Whitening Cream',   'size' => '15gm',  'mrp' => 249,  'sale' => 199 ],
		[ 'product' => 'Skin Whitening Cream',   'size' => '25gm',  'mrp' => 349,  'sale' => 299 ],
		[ 'product' => 'Skin Whitening Cream',   'size' => '100gm', 'mrp' => 499,  'sale' => 449 ],
		// Skin De Tan Cream
		[ 'product' => 'Skin De Tan Cream',      'size' => '15gm',  'mrp' => 249,  'sale' => 199 ],
		[ 'product' => 'Skin De Tan Cream',      'size' => '25gm',  'mrp' => 299,  'sale' => 249 ],
		[ 'product' => 'Skin De Tan Cream',      'size' => '100gm', 'mrp' => 550,  'sale' => 399 ],
		// Whitening De Tan Scrub
		[ 'product' => 'Whitening De Tan Scrub', 'size' => '15gm',  'mrp' => 249,  'sale' => 199 ],
		[ 'product' => 'Whitening De Tan Scrub', 'size' => '25gm',  'mrp' => 349,  'sale' => 249 ],
		[ 'product' => 'Whitening De Tan Scrub', 'size' => '100gm', 'mrp' => 549,  'sale' => 399 ],
		// Testerone Powder (single SKU)
		[ 'product' => 'Testerone Powder',       'size' => '250gm', 'mrp' => 950,  'sale' => 549 ],
		// Whitening Serum
		[ 'product' => 'whitening serum',        'size' => '10ml',  'mrp' => 299,  'sale' => 249 ],
		[ 'product' => 'whitening serum',        'size' => '15ml',  'mrp' => 399,  'sale' => 349 ],
		[ 'product' => 'whitening serum',        'size' => '30ml',  'mrp' => 499,  'sale' => 399 ],
		// Peeling Solution Serum
		[ 'product' => 'peeling soultion serum', 'size' => '10ml',  'mrp' => 399,  'sale' => 249 ],
		[ 'product' => 'peeling soultion serum', 'size' => '15ml',  'mrp' => 449,  'sale' => 349 ],
		[ 'product' => 'peeling soultion serum', 'size' => '30ml',  'mrp' => 599,  'sale' => 449 ],
	];
}

/**
 * Alias map — brand name in the price list → product title prefix in WC.
 * Used when the WC product was renamed to a marketing-friendly title.
 * LIKE-matched, so e.g. "Herbal Brightening Serum" finds
 * "Herbal Brightening Serum 30ml".
 */
function hp_brand_to_wc_title_map() {
	return [
		'whitening serum'        => 'Herbal Brightening Serum',
		'peeling soultion serum' => 'Herbal Peeling Solution',
		'Testerone Powder'       => "Men's Vitality Powder",
		'Skin De Tan Cream'      => 'Herbal De-Tan Cream',
	];
}

/* ─────────────── Lookup ─────────────── */

function hp_find_product_by_title( $title ) {
	$matches = get_posts( [
		'post_type'      => 'product',
		'post_status'    => [ 'publish', 'draft', 'private' ],
		'title'          => $title,
		'posts_per_page' => 1,
		'fields'         => 'ids',
	] );
	if ( ! empty( $matches ) ) {
		return wc_get_product( $matches[0] );
	}

	global $wpdb;
	$like = '%' . $wpdb->esc_like( trim( $title ) ) . '%';
	$id   = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'product' AND post_status IN ('publish','draft','private') AND post_title LIKE %s ORDER BY ID ASC LIMIT 1",
			$like
		)
	);
	return $id ? wc_get_product( $id ) : null;
}

function hp_find_variation_by_size( WC_Product $parent, $size_label ) {
	if ( ! $parent->is_type( 'variable' ) ) {
		return null;
	}
	$needle = strtolower( trim( $size_label ) );
	foreach ( $parent->get_children() as $vid ) {
		$variation = wc_get_product( $vid );
		if ( ! $variation ) {
			continue;
		}
		foreach ( $variation->get_attributes() as $attr_value ) {
			if ( strtolower( trim( $attr_value ) ) === $needle ) {
				return $variation;
			}
		}
	}
	return null;
}

/* ─────────────── Mutation ─────────────── */

function hp_apply_prices( WC_Product $target, $mrp, $sale ) {
	$target->set_regular_price( (string) $mrp );
	$target->set_sale_price( $sale < $mrp ? (string) $sale : '' );
	$target->set_date_on_sale_from( null );
	$target->set_date_on_sale_to( null );
	$target->save();
}

/**
 * Convert a simple product to variable and add Size variations for
 * each row supplied. Existing simple price is cleared (variations
 * carry their own); product type term is updated; local "Size"
 * attribute is registered for variation use.
 */
function hp_convert_simple_to_variable( WC_Product $simple, array $rows ) {
	$product_id = $simple->get_id();

	wp_set_object_terms( $product_id, 'variable', 'product_type' );

	$sizes = array_values( array_unique( array_column( $rows, 'size' ) ) );

	$attribute = new WC_Product_Attribute();
	$attribute->set_id( 0 ); // 0 = local (not a global taxonomy)
	$attribute->set_name( 'Size' );
	$attribute->set_options( $sizes );
	$attribute->set_visible( true );
	$attribute->set_variation( true );
	$attribute->set_position( 0 );

	$product = new WC_Product_Variable( $product_id );
	$product->set_attributes( [ $attribute ] );
	$product->set_regular_price( '' );
	$product->set_sale_price( '' );
	$product->save();

	foreach ( $rows as $row ) {
		hp_create_or_update_variation( $product, $row['size'], $row['mrp'], $row['sale'] );
	}

	WC_Product_Variable::sync( $product_id );
	wc_delete_product_transients( $product_id );
}

/**
 * Idempotently create a Size variation, or update its price if one
 * with that size already exists.
 *
 * @return WC_Product_Variation
 */
function hp_create_or_update_variation( WC_Product $parent, $size, $mrp, $sale ) {
	$existing = hp_find_variation_by_size( $parent, $size );
	if ( $existing ) {
		hp_apply_prices( $existing, $mrp, $sale );
		return $existing;
	}

	$variation = new WC_Product_Variation();
	$variation->set_parent_id( $parent->get_id() );
	$variation->set_attributes( [ 'size' => $size ] );
	$variation->set_regular_price( (string) $mrp );
	if ( $sale < $mrp ) {
		$variation->set_sale_price( (string) $sale );
	}
	$variation->set_status( 'publish' );
	$variation->set_manage_stock( false );
	$variation->set_stock_status( 'instock' );
	$variation->save();
	return $variation;
}

/* ─────────────── Planning (preview + apply share logic) ─────────────── */

/**
 * Group price rows by brand and resolve each group to a plan:
 *   [
 *     'brand'     => string,
 *     'wc_title'  => string,
 *     'product'   => WC_Product|null,
 *     'rows'      => array of price rows,
 *     'action'    => 'update-simple' | 'add-variation' | 'update-variation'
 *                  | 'convert-and-add' | 'missing-product',
 *     'messages'  => array of per-row strings for preview,
 *   ]
 */
function hp_plan_price_migration() {
	$rows     = hp_price_list();
	$name_map = hp_brand_to_wc_title_map();

	$groups = [];
	foreach ( $rows as $row ) {
		$groups[ $row['product'] ][] = $row;
	}

	$plans = [];

	foreach ( $groups as $brand => $brand_rows ) {
		$wc_title = $name_map[ $brand ] ?? $brand;
		$product  = hp_find_product_by_title( $wc_title );

		$plan = [
			'brand'    => $brand,
			'wc_title' => $wc_title,
			'product'  => $product,
			'rows'     => $brand_rows,
			'messages' => [],
		];

		if ( ! $product ) {
			$plan['action']     = 'missing-product';
			$plan['messages'][] = sprintf( 'No WC product matches "%s".', $wc_title );
			$plans[]            = $plan;
			continue;
		}

		$is_variable     = $product->is_type( 'variable' );
		$is_simple       = $product->is_type( 'simple' );
		$single_size     = count( $brand_rows ) === 1;

		if ( $single_size && $is_simple ) {
			$plan['action'] = 'update-simple';
			$row            = $brand_rows[0];
			$plan['messages'][] = sprintf(
				'%s · MRP ₹%d → Sale ₹%d (simple product, current size in title ignored)',
				$product->get_name(),
				$row['mrp'],
				$row['sale']
			);
		} elseif ( $is_simple ) {
			$plan['action'] = 'convert-and-add';
			$plan['messages'][] = sprintf(
				'Convert "%s" to variable; add %d Size variations:',
				$product->get_name(),
				count( $brand_rows )
			);
			foreach ( $brand_rows as $row ) {
				$plan['messages'][] = sprintf( '   • %s → MRP ₹%d / Sale ₹%d', $row['size'], $row['mrp'], $row['sale'] );
			}
		} elseif ( $is_variable ) {
			$plan['action'] = 'update-or-add-variations';
			foreach ( $brand_rows as $row ) {
				$existing = hp_find_variation_by_size( $product, $row['size'] );
				if ( $existing ) {
					$plan['messages'][] = sprintf( '   • %s (update) → MRP ₹%d / Sale ₹%d', $row['size'], $row['mrp'], $row['sale'] );
				} else {
					$plan['messages'][] = sprintf( '   • %s (NEW) → MRP ₹%d / Sale ₹%d', $row['size'], $row['mrp'], $row['sale'] );
				}
			}
		} else {
			$plan['action']     = 'skip';
			$plan['messages'][] = sprintf( 'Unsupported product type: %s', $product->get_type() );
		}

		$plans[] = $plan;
	}

	return $plans;
}

/* ─────────────── Apply ─────────────── */

function hp_apply_plan( array $plan ) {
	$applied = [];

	switch ( $plan['action'] ) {
		case 'update-simple':
			$row = $plan['rows'][0];
			hp_apply_prices( $plan['product'], $row['mrp'], $row['sale'] );
			$applied[] = sprintf( '%s → MRP ₹%d / Sale ₹%d', $plan['product']->get_name(), $row['mrp'], $row['sale'] );
			wc_delete_product_transients( $plan['product']->get_id() );
			break;

		case 'convert-and-add':
			hp_convert_simple_to_variable( $plan['product'], $plan['rows'] );
			$applied[] = sprintf(
				'Converted "%s" to variable + created %d Size variations',
				$plan['product']->get_name(),
				count( $plan['rows'] )
			);
			break;

		case 'update-or-add-variations':
			$parent = $plan['product'];
			foreach ( $plan['rows'] as $row ) {
				hp_create_or_update_variation( $parent, $row['size'], $row['mrp'], $row['sale'] );
				$applied[] = sprintf( '%s · %s → MRP ₹%d / Sale ₹%d', $parent->get_name(), $row['size'], $row['mrp'], $row['sale'] );
			}
			WC_Product_Variable::sync( $parent->get_id() );
			wc_delete_product_transients( $parent->get_id() );
			break;
	}

	return $applied;
}

/* ─────────────── Admin page ─────────────── */

add_action( 'admin_menu', function() {
	if ( ! function_exists( 'WC' ) ) {
		return;
	}
	add_submenu_page(
		'woocommerce',
		__( 'Update Prices', 'herbalpearls' ),
		__( 'Update Prices', 'herbalpearls' ),
		'manage_woocommerce',
		'hp-update-prices',
		'hp_render_price_update_page'
	);
} );

function hp_render_price_update_page() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'herbalpearls' ) );
	}

	$applied = [];
	$errors  = [];

	if ( isset( $_POST['hp_apply_prices'] ) && check_admin_referer( 'hp_apply_prices' ) ) {
		foreach ( hp_plan_price_migration() as $plan ) {
			if ( 'missing-product' === $plan['action'] ) {
				$errors[] = sprintf( '%s — %s', $plan['brand'], $plan['messages'][0] );
				continue;
			}
			$applied = array_merge( $applied, hp_apply_plan( $plan ) );
		}
	}

	$plans = hp_plan_price_migration();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Update Prices', 'herbalpearls' ); ?></h1>
		<p>
			<?php esc_html_e( 'Applies the 2026 price list to your WC products. Brand names in the price list are mapped to your renamed products by title prefix. Simple products with multiple sizes in the list are converted to variable; single-size groups update the simple product\'s price in place. Re-runnable safely.', 'herbalpearls' ); ?>
		</p>

		<?php if ( ! empty( $applied ) ) : ?>
			<div class="notice notice-success">
				<p><strong><?php printf( esc_html__( 'Done — %d update(s) applied:', 'herbalpearls' ), count( $applied ) ); ?></strong></p>
				<ul style="margin:0 0 0 1.5em;list-style:disc;">
					<?php foreach ( $applied as $line ) : ?>
						<li><?php echo esc_html( $line ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $errors ) ) : ?>
			<div class="notice notice-warning">
				<p><strong><?php esc_html_e( 'Skipped:', 'herbalpearls' ); ?></strong></p>
				<ul style="margin:0 0 0 1.5em;list-style:disc;">
					<?php foreach ( $errors as $line ) : ?>
						<li><?php echo esc_html( $line ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<h2><?php esc_html_e( 'Name mapping', 'herbalpearls' ); ?></h2>
		<table class="widefat striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Brand name in price list', 'herbalpearls' ); ?></th>
					<th><?php esc_html_e( 'Matches WC product title (prefix)', 'herbalpearls' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( hp_brand_to_wc_title_map() as $brand => $wc_title ) : ?>
					<tr>
						<td><?php echo esc_html( $brand ); ?></td>
						<td><?php echo esc_html( $wc_title ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<h2 style="margin-top:1.5rem;"><?php esc_html_e( 'Migration plan', 'herbalpearls' ); ?></h2>
		<table class="widefat striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Brand', 'herbalpearls' ); ?></th>
					<th><?php esc_html_e( 'WC product', 'herbalpearls' ); ?></th>
					<th><?php esc_html_e( 'Action', 'herbalpearls' ); ?></th>
					<th><?php esc_html_e( 'Details', 'herbalpearls' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $plans as $plan ) :
					$action_labels = [
						'update-simple'             => '✓ Update simple price',
						'convert-and-add'           => '⚙ Convert simple → variable + create variations',
						'update-or-add-variations'  => '✓ Update / add variations',
						'missing-product'           => '⚠ Product not found',
						'skip'                      => '⚠ Skipped',
					];
					$label = $action_labels[ $plan['action'] ] ?? $plan['action'];
					$color = in_array( $plan['action'], [ 'missing-product', 'skip' ], true ) ? '#c45a00' : '#1e8a1e';
					?>
					<tr>
						<td><?php echo esc_html( $plan['brand'] ); ?></td>
						<td>
							<?php if ( $plan['product'] ) : ?>
								<a href="<?php echo esc_url( get_edit_post_link( $plan['product']->get_id() ) ); ?>">
									<?php echo esc_html( $plan['product']->get_name() ); ?>
								</a>
								<br>
								<small><?php echo esc_html( $plan['product']->get_type() ); ?></small>
							<?php else : ?>
								<em><?php echo esc_html( $plan['wc_title'] ); ?></em>
							<?php endif; ?>
						</td>
						<td style="color:<?php echo esc_attr( $color ); ?>;"><?php echo esc_html( $label ); ?></td>
						<td>
							<ul style="margin:0;">
								<?php foreach ( $plan['messages'] as $msg ) : ?>
									<li style="margin:0;list-style:none;"><?php echo esc_html( $msg ); ?></li>
								<?php endforeach; ?>
							</ul>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<form method="post" style="margin-top:1.5rem;">
			<?php wp_nonce_field( 'hp_apply_prices' ); ?>
			<button type="submit" name="hp_apply_prices" value="1" class="button button-primary button-large">
				<?php esc_html_e( 'Apply Migration', 'herbalpearls' ); ?>
			</button>
			<p class="description" style="margin-top:0.5rem;">
				<?php esc_html_e( 'Rows marked ⚠ are skipped — fix the product title in WooCommerce or adjust hp_brand_to_wc_title_map() in inc/admin-price-update.php and re-run.', 'herbalpearls' ); ?>
			</p>
		</form>
	</div>
	<?php
}
