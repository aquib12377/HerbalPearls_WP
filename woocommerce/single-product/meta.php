<?php
/**
 * Product meta — suppress default category/tag display.
 * We render our own marketplace links + blog link instead.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

$links = hp_get_marketplace_links( $product->get_id() );
$has_marketplace = ! empty( $links['amazon'] ) || ! empty( $links['flipkart'] );
$blog_id = get_post_meta( $product->get_id(), '_hp_blog_link_id', true );
?>

<div class="product_meta">
	<?php if ( $has_marketplace ) : ?>
		<div class="hp-marketplace-buttons">
			<?php if ( ! empty( $links['amazon'] ) ) : ?>
				<a href="<?php echo esc_url( $links['amazon'] ); ?>"
				   class="hp-btn hp-btn--secondary hp-btn--sm"
				   target="_blank" rel="noopener nofollow sponsored">
					<?php esc_html_e( 'Buy on Amazon', 'herbalpearls' ); ?>
				</a>
			<?php endif; ?>
			<?php if ( ! empty( $links['flipkart'] ) ) : ?>
				<a href="<?php echo esc_url( $links['flipkart'] ); ?>"
				   class="hp-btn hp-btn--secondary hp-btn--sm"
				   target="_blank" rel="noopener nofollow sponsored">
					<?php esc_html_e( 'Buy on Flipkart', 'herbalpearls' ); ?>
				</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if ( $blog_id ) : ?>
		<div class="hp-mt-1">
			<a href="<?php echo esc_url( get_permalink( $blog_id ) ); ?>" class="hp-btn hp-btn--ghost hp-btn--sm">
				<?php esc_html_e( 'Read full guide →', 'herbalpearls' ); ?>
			</a>
		</div>
	<?php endif; ?>
</div>
