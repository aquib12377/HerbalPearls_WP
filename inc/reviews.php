<?php
/**
 * Reviews integration — Customer Reviews for WooCommerce (CusRev).
 *
 * Replaces Judge.me (no longer supported). CusRev is free, uses native
 * WordPress comments, and adds verified buyer badges + review reminders.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Style CusRev verified badge to match our design system.
 */
add_action( 'wp_head', function() {
	if ( ! is_singular( 'product' ) ) {
		return;
	}
	?>
	<style>
		/* CusRev verified badge — match our mint */
		.cr-review .ivole-verified-badge,
		.ivole-verified-badge {
			color: var(--hp-mint) !important;
			font-size: 0.75rem !important;
		}

		/* CusRev star ratings — match our gold */
		.cr-review .crstar-rating,
		.cr-review .cr-star-rating,
		.ivole-star-rating,
		.cr-all-reviews-shortcode .star-rating {
			color: var(--hp-gold-500) !important;
		}

		/* CusRev review form */
		.cr-review-form,
		.ivole-review-form {
			background: var(--hp-bg-pure) !important;
			border: 1px solid var(--hp-line-1) !important;
			border-radius: 12px !important;
			padding: 1.5rem !important;
		}

		.cr-review-form input[type="submit"],
		.ivole-review-form input[type="submit"] {
			background: var(--hp-gold-600) !important;
			color: var(--hp-text-on-gold) !important;
			border: none !important;
			border-radius: 8px !important;
			padding: 0.625rem 1.5rem !important;
			font-weight: 600 !important;
			cursor: pointer !important;
		}
	</style>
	<?php
}, 99 );
