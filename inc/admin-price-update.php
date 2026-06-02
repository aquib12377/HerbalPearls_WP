<?php
/**
 * One-time bulk price update tool.
 *
 * Adds a "Update Prices" page under WooCommerce that matches the
 * 2026 Herbal Pearls price list onto existing products by name +
 * variation size attribute. Shows a preview (matched vs unmatched,
 * current vs new), then applies the change in one POST.
 *
 * Safe to re-run: it only updates `_regular_price` and `_sale_price`
 * on products and variations that match; nothing else is touched.
 * Cap: `manage_woocommerce` only.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The price list — Brand · Product name · Size · MRP · Selling price.
 * MRP becomes the regular price, Selling becomes the sale price.
 * Match is case-insensitive on title and size attribute value.
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
 * Find a WC_Product (parent) by title — exact match preferred, LIKE fallback.
 *
 * @return WC_Product|null
 */
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

/**
 * Find the variation whose size attribute matches $size_label.
 *
 * @return WC_Product_Variation|null
 */
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

/**
 * Resolve one price row to either a variation or simple product.
 *
 * @return array{0: ?WC_Product, 1: string, 2: string}  [target, status, message]
 */
function hp_resolve_price_row( array $row ) {
	$parent = hp_find_product_by_title( $row['product'] );
	if ( ! $parent ) {
		return [ null, 'missing-product', sprintf( 'No product titled "%s"', $row['product'] ) ];
	}
	if ( $parent->is_type( 'variable' ) ) {
		$variation = hp_find_variation_by_size( $parent, $row['size'] );
		if ( ! $variation ) {
			return [ null, 'missing-variation', sprintf( 'No "%s" variation on "%s"', $row['size'], $parent->get_name() ) ];
		}
		return [ $variation, 'ok', '' ];
	}
	return [ $parent, 'ok', '' ];
}

/**
 * Apply MRP + sale price to a product (parent or variation).
 */
function hp_apply_prices( WC_Product $target, $mrp, $sale ) {
	$target->set_regular_price( (string) $mrp );
	$target->set_sale_price( $sale < $mrp ? (string) $sale : '' );
	$target->set_date_on_sale_from( null );
	$target->set_date_on_sale_to( null );
	$target->save();
}

/**
 * Register the admin page.
 */
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
		foreach ( hp_price_list() as $row ) {
			[ $target, $status, $message ] = hp_resolve_price_row( $row );
			if ( 'ok' !== $status ) {
				$errors[] = $message;
				continue;
			}
			hp_apply_prices( $target, $row['mrp'], $row['sale'] );
			$applied[] = sprintf( '%s · %s → MRP ₹%d / Sale ₹%d', $row['product'], $row['size'], $row['mrp'], $row['sale'] );
		}

		// Bust WC transients so frontend prices refresh.
		if ( function_exists( 'wc_delete_product_transients' ) ) {
			foreach ( hp_price_list() as $row ) {
				$p = hp_find_product_by_title( $row['product'] );
				if ( $p ) {
					wc_delete_product_transients( $p->get_id() );
				}
			}
		}
	}

	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Update Prices', 'herbalpearls' ); ?></h1>
		<p>
			<?php esc_html_e( 'Applies the 2026 price list to existing products by matching on product title + variation size. Re-runnable: only the regular price and sale price are touched.', 'herbalpearls' ); ?>
		</p>

		<?php if ( ! empty( $applied ) ) : ?>
			<div class="notice notice-success">
				<p><strong><?php printf( esc_html( _n( 'Updated %d product.', 'Updated %d products:', count( $applied ), 'herbalpearls' ) ), count( $applied ) ); ?></strong></p>
				<ul style="margin:0 0 0 1.5em;list-style:disc;">
					<?php foreach ( $applied as $line ) : ?>
						<li><?php echo esc_html( $line ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $errors ) ) : ?>
			<div class="notice notice-warning">
				<p><strong><?php esc_html_e( 'Skipped (not found):', 'herbalpearls' ); ?></strong></p>
				<ul style="margin:0 0 0 1.5em;list-style:disc;">
					<?php foreach ( $errors as $line ) : ?>
						<li><?php echo esc_html( $line ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<table class="widefat striped" style="margin-top:1rem;">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Product', 'herbalpearls' ); ?></th>
					<th><?php esc_html_e( 'Size', 'herbalpearls' ); ?></th>
					<th><?php esc_html_e( 'Current MRP', 'herbalpearls' ); ?></th>
					<th><?php esc_html_e( 'Current Sale', 'herbalpearls' ); ?></th>
					<th><?php esc_html_e( 'New MRP', 'herbalpearls' ); ?></th>
					<th><?php esc_html_e( 'New Sale', 'herbalpearls' ); ?></th>
					<th><?php esc_html_e( 'Status', 'herbalpearls' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( hp_price_list() as $row ) :
					[ $target, $status, $message ] = hp_resolve_price_row( $row );
					$current_mrp  = $target ? $target->get_regular_price() : '';
					$current_sale = $target ? $target->get_sale_price() : '';
					$status_label = 'ok' === $status ? '✓ matched' : '⚠ ' . $message;
					$status_color = 'ok' === $status ? '#1e8a1e' : '#c45a00';
					?>
					<tr>
						<td><?php echo esc_html( $row['product'] ); ?></td>
						<td><?php echo esc_html( $row['size'] ); ?></td>
						<td><?php echo $current_mrp !== '' ? '₹' . esc_html( $current_mrp ) : '—'; ?></td>
						<td><?php echo $current_sale !== '' ? '₹' . esc_html( $current_sale ) : '—'; ?></td>
						<td><strong>₹<?php echo esc_html( $row['mrp'] ); ?></strong></td>
						<td><strong>₹<?php echo esc_html( $row['sale'] ); ?></strong></td>
						<td style="color:<?php echo esc_attr( $status_color ); ?>;"><?php echo esc_html( $status_label ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<form method="post" style="margin-top:1.5rem;">
			<?php wp_nonce_field( 'hp_apply_prices' ); ?>
			<button type="submit" name="hp_apply_prices" value="1" class="button button-primary button-large">
				<?php esc_html_e( 'Apply Prices to Matched Products', 'herbalpearls' ); ?>
			</button>
			<p class="description" style="margin-top:0.5rem;">
				<?php esc_html_e( 'Only rows marked ✓ matched will be updated. Unmatched rows are skipped — fix the product title or variation size in WooCommerce, then re-run.', 'herbalpearls' ); ?>
			</p>
		</form>
	</div>
	<?php
}
