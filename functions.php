<?php
/**
 * Herbal Pearls Theme bootstrap.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HP_VERSION', '1.0.0' );
define( 'HP_DIR', get_stylesheet_directory() );
define( 'HP_URI', get_stylesheet_directory_uri() );

require HP_DIR . '/inc/setup.php';
require HP_DIR . '/inc/enqueue.php';
require HP_DIR . '/inc/nav.php';
require HP_DIR . '/inc/woocommerce.php';
require HP_DIR . '/inc/meta-boxes.php';
require HP_DIR . '/inc/blocks.php';
require HP_DIR . '/inc/shortcodes.php';
require HP_DIR . '/inc/ajax.php';
require HP_DIR . '/inc/admin.php';
require HP_DIR . '/inc/admin-price-update.php';
require HP_DIR . '/inc/seo-schema.php';
require HP_DIR . '/inc/seo-meta.php';
require HP_DIR . '/inc/bundles.php';
require HP_DIR . '/inc/discounts.php';
require HP_DIR . '/inc/reviews.php';
require HP_DIR . '/inc/compliance.php';
require HP_DIR . '/inc/perf.php';
require HP_DIR . '/inc/pdp-sections.php';
require HP_DIR . '/inc/helpers.php';
