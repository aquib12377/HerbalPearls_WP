<?php
/**
 * Single product price — MRP strikethrough + sell + "You save ₹X (Y%)" line.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! $product instanceof WC_Product ) {
	return;
}
?>
<div class="hp-price-block">
	<?php if ( $product->is_type( 'variable' ) ) : ?>
		<?php
		$min_regular = $product->get_variation_regular_price( 'min', true );
		$min_sale    = $product->get_variation_sale_price( 'min', true );
		$max_regular = $product->get_variation_regular_price( 'max', true );
		$max_sale    = $product->get_variation_sale_price( 'max', true );

		if ( $min_regular !== $min_sale ) :
			// On sale
			?>
			<p class="price">
				<del aria-label="<?php esc_attr_e( 'Regular price', 'herbalpearls' ); ?>">
					<?php echo wp_kses_post( hp_inr( (float) $min_regular ) ); ?>
				</del>
				<ins aria-label="<?php esc_attr_e( 'Sale price', 'herbalpearls' ); ?>">
					<?php echo wp_kses_post( hp_inr( (float) $min_sale ) ); ?>
				</ins>
				&ndash;
				<ins>
					<?php echo wp_kses_post( hp_inr( (float) $max_sale ) ); ?>
				</ins>
			</p>
			<?php
			$save_pct = round( ( ( $min_regular - $min_sale ) / $min_regular ) * 100 );
			$save_amt = $min_regular - $min_sale;
			?>
			<p class="hp-price-save">
				<?php
				printf(
					/* translators: 1: saved amount, 2: saved percentage */
					esc_html__( 'You save %1$s (%2$d%%)', 'herbalpearls' ),
					esc_html( hp_inr( $save_amt ) ),
					absint( $save_pct )
				);
				?>
			</p>
		<?php else : ?>
			<p class="price">
				<?php echo wp_kses_post( hp_inr( (float) $min_regular ) ); ?>
				<?php if ( $min_regular !== $max_regular ) : ?>
					&ndash; <?php echo wp_kses_post( hp_inr( (float) $max_regular ) ); ?>
				<?php endif; ?>
			</p>
		<?php endif; ?>
	<?php else : ?>
		<p class="price" style="font-size: 1.5rem;">
			<?php if ( $product->is_on_sale() ) : ?>
				<del aria-label="<?php esc_attr_e( 'Regular price', 'herbalpearls' ); ?>">
					<?php echo wp_kses_post( hp_inr( (float) $product->get_regular_price() ) ); ?>
				</del>
				<ins aria-label="<?php esc_attr_e( 'Sale price', 'herbalpearls' ); ?>">
					<?php echo wp_kses_post( hp_inr( (float) $product->get_sale_price() ) ); ?>
				</ins>
				<?php
				$save_pct = round( ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100 );
				$save_amt = $product->get_regular_price() - $product->get_sale_price();
				?>
				</p>
				<p class="hp-price-save">
				<?php
				printf(
					/* translators: 1: saved amount, 2: saved percentage */
					esc_html__( 'You save %1$s (%2$d%%)', 'herbalpearls' ),
					esc_html( hp_inr( $save_amt ) ),
					absint( $save_pct )
				);
				?>
			<?php else : ?>
				<?php echo wp_kses_post( hp_inr( (float) $product->get_price() ) ); ?>
			<?php endif; ?>
		</p>
	<?php endif; ?>

	<p class="hp-tax-note">
		<?php esc_html_e( 'Inclusive of all taxes.', 'herbalpearls' ); ?>
	</p>
</div>
