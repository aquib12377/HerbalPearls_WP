<?php
/**
 * Search form template.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<form role="search" method="get" class="hp-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="hp-search-field">
		<?php esc_html_e( 'Search for:', 'herbalpearls' ); ?>
	</label>
	<div class="hp-search-form__inner">
		<input
			type="search"
			id="hp-search-field"
			class="hp-input"
			name="s"
			placeholder="<?php esc_attr_e( 'Search products & articles…', 'herbalpearls' ); ?>"
			value="<?php echo get_search_query(); ?>"
		>
		<button type="submit" class="hp-btn hp-btn--primary hp-btn--sm">
			<?php esc_html_e( 'Search', 'herbalpearls' ); ?>
		</button>
	</div>
</form>
