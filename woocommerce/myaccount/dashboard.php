<?php
/**
 * My Account dashboard — custom welcome, recent orders.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user = wp_get_current_user();
?>

<div class="hp-account-dashboard">
	<div class="hp-mb-2">
		<h2>
			<?php
			printf(
				/* translators: %s: user display name */
				esc_html__( 'Hello, %s', 'herbalpearls' ),
				esc_html( $current_user->display_name )
			);
			?>
		</h2>
		<p style="font-size: 0.9375rem;">
			<?php esc_html_e( 'From your account dashboard you can view recent orders, manage your addresses, and edit your account details.', 'herbalpearls' ); ?>
		</p>
	</div>

	<div class="hp-grid-2 hp-mb-2">
		<?php
		$recent_orders = wc_get_orders( [
			'customer_id' => get_current_user_id(),
			'limit'       => 5,
			'orderby'     => 'date',
			'order'       => 'DESC',
		] );
		?>
		<div class="hp-card">
			<h4><?php esc_html_e( 'Recent Orders', 'herbalpearls' ); ?></h4>
			<?php if ( empty( $recent_orders ) ) : ?>
				<p style="font-size: 0.9375rem;">
					<?php esc_html_e( 'No orders yet.', 'herbalpearls' ); ?>
				</p>
				<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="hp-btn hp-btn--primary hp-btn--sm hp-mt-1">
					<?php esc_html_e( 'Start Shopping', 'herbalpearls' ); ?>
				</a>
			<?php else : ?>
				<table style="width:100%;font-size:0.875rem;border-collapse:collapse;">
					<thead>
						<tr>
							<th style="text-align:left;padding:0.5rem 0;border-bottom:1px solid var(--hp-line-1);"><?php esc_html_e( 'Order', 'herbalpearls' ); ?></th>
							<th style="text-align:left;padding:0.5rem 0;border-bottom:1px solid var(--hp-line-1);"><?php esc_html_e( 'Date', 'herbalpearls' ); ?></th>
							<th style="text-align:left;padding:0.5rem 0;border-bottom:1px solid var(--hp-line-1);"><?php esc_html_e( 'Status', 'herbalpearls' ); ?></th>
							<th style="text-align:right;padding:0.5rem 0;border-bottom:1px solid var(--hp-line-1);"><?php esc_html_e( 'Total', 'herbalpearls' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $recent_orders as $order ) : ?>
							<tr>
								<td style="padding:0.5rem 0;border-bottom:1px solid var(--hp-line-1);">
									<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
										#<?php echo esc_html( $order->get_order_number() ); ?>
									</a>
								</td>
								<td style="padding:0.5rem 0;border-bottom:1px solid var(--hp-line-1);">
									<?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?>
								</td>
								<td style="padding:0.5rem 0;border-bottom:1px solid var(--hp-line-1);">
									<?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
								</td>
								<td style="text-align:right;padding:0.5rem 0;border-bottom:1px solid var(--hp-line-1);">
									<?php echo wp_kses_post( $order->get_formatted_order_total() ); ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>

		<div class="hp-card">
			<h4><?php esc_html_e( 'Account Details', 'herbalpearls' ); ?></h4>
			<p style="font-size: 0.9375rem;"><?php echo esc_html( $current_user->user_email ); ?></p>
			<div class="hp-mt-1 hp-flex" style="gap:0.5rem;flex-wrap:wrap;">
				<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-account' ) ); ?>" class="hp-btn hp-btn--secondary hp-btn--sm">
					<?php esc_html_e( 'Edit Account', 'herbalpearls' ); ?>
				</a>
				<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>" class="hp-btn hp-btn--secondary hp-btn--sm">
					<?php esc_html_e( 'Addresses', 'herbalpearls' ); ?>
				</a>
			</div>
		</div>
	</div>
</div>
