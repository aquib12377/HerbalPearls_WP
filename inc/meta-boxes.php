<?php
/**
 * Custom meta boxes for Herbal Pearls products.
 * Replaces ACF Pro features.
 * From v2 Appendix A.3 — verbatim.
 *
 * @package HerbalPearls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ─────────────── Register meta boxes ─────────────── */
add_action( 'add_meta_boxes', function() {
	add_meta_box( 'hp_marketplace',  __( 'Marketplace Links', 'herbalpearls' ),          'hp_mb_marketplace',  'product', 'side',   'default' );
	add_meta_box( 'hp_blog_link',    __( 'Detailed Guide Blog Post', 'herbalpearls' ),   'hp_mb_blog_link',    'product', 'side',   'default' );
	add_meta_box( 'hp_how_to',       __( 'How to Use (PDP short)', 'herbalpearls' ),     'hp_mb_how_to',       'product', 'normal', 'default' );
	add_meta_box( 'hp_ingredients',  __( 'Key Ingredients', 'herbalpearls' ),            'hp_mb_ingredients',  'product', 'normal', 'default' );
	add_meta_box( 'hp_faqs',         __( 'Product FAQs', 'herbalpearls' ),               'hp_mb_faqs',         'product', 'normal', 'default' );
} );

/* ─────────────── Marketplace ─────────────── */
function hp_mb_marketplace( $post ) {
	wp_nonce_field( 'hp_meta_save', 'hp_meta_nonce' );
	$amazon   = get_post_meta( $post->ID, '_hp_amazon_url', true );
	$flipkart = get_post_meta( $post->ID, '_hp_flipkart_url', true );
	?>
	<p>
		<label for="hp_amazon_url"><strong><?php esc_html_e( 'Amazon URL', 'herbalpearls' ); ?></strong></label>
		<input type="url" id="hp_amazon_url" name="hp_amazon_url"
			   value="<?php echo esc_attr( $amazon ); ?>"
			   placeholder="https://amazon.in/..." style="width:100%;">
	</p>
	<p>
		<label for="hp_flipkart_url"><strong><?php esc_html_e( 'Flipkart URL', 'herbalpearls' ); ?></strong></label>
		<input type="url" id="hp_flipkart_url" name="hp_flipkart_url"
			   value="<?php echo esc_attr( $flipkart ); ?>"
			   placeholder="https://flipkart.com/..." style="width:100%;">
	</p>
	<p class="description"><?php esc_html_e( 'UTM tags appended automatically on click.', 'herbalpearls' ); ?></p>
	<?php
}

/* ─────────────── Blog post link ─────────────── */
function hp_mb_blog_link( $post ) {
	$blog_id = get_post_meta( $post->ID, '_hp_blog_link_id', true );
	$posts = get_posts( [ 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC' ] );
	?>
	<select name="hp_blog_link_id" style="width:100%;">
		<option value="">— <?php esc_html_e( 'None', 'herbalpearls' ); ?> —</option>
		<?php foreach ( $posts as $p ) : ?>
			<option value="<?php echo esc_attr( $p->ID ); ?>" <?php selected( $blog_id, $p->ID ); ?>>
				<?php echo esc_html( $p->post_title ); ?>
			</option>
		<?php endforeach; ?>
	</select>
	<p class="description"><?php esc_html_e( 'PDP "Read full guide" link.', 'herbalpearls' ); ?></p>
	<?php
}

/* ─────────────── How to use (short) ─────────────── */
function hp_mb_how_to( $post ) {
	$val = get_post_meta( $post->ID, '_hp_how_to_short', true );
	wp_editor( $val, 'hp_how_to_short', [
		'textarea_name' => 'hp_how_to_short',
		'media_buttons' => false,
		'textarea_rows' => 5,
		'teeny'         => true,
	] );
	?>
	<p class="description"><?php esc_html_e( '3 steps max. Long version goes in the blog post selected above.', 'herbalpearls' ); ?></p>
	<?php
}

/* ─────────────── Ingredients (repeater) ─────────────── */
function hp_mb_ingredients( $post ) {
	$items = get_post_meta( $post->ID, '_hp_ingredients', true );
	if ( ! is_array( $items ) ) {
		$items = [];
	}
	?>
	<div id="hp-ing-wrap">
		<?php foreach ( $items as $i => $ing ) : ?>
			<?php hp_render_ing_row( $i, $ing ); ?>
		<?php endforeach; ?>
	</div>
	<button type="button" class="button" id="hp-ing-add">+ <?php esc_html_e( 'Add ingredient', 'herbalpearls' ); ?></button>
	<script>
	jQuery(function($){
		$('#hp-ing-add').on('click', function(){
			var i = $('#hp-ing-wrap .hp-ing-row').length;
			$.post(ajaxurl, { action: 'hp_get_ing_row', i: i }, function(html){
				$('#hp-ing-wrap').append(html);
			});
		});
		$(document).on('click', '.hp-ing-remove', function(){
			$(this).closest('.hp-ing-row').remove();
		});
	});
	</script>
	<?php
}

function hp_render_ing_row( $i, $ing = [] ) {
	?>
	<div class="hp-ing-row" style="display:grid;grid-template-columns:1fr 1fr 32px;gap:8px;margin-bottom:8px;">
		<input type="text" name="hp_ingredients[<?php echo (int) $i; ?>][name]"
			   value="<?php echo esc_attr( $ing['name'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Name (e.g., Niacinamide)', 'herbalpearls' ); ?>">
		<input type="text" name="hp_ingredients[<?php echo (int) $i; ?>][benefit]"
			   value="<?php echo esc_attr( $ing['benefit'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Benefit (one line)', 'herbalpearls' ); ?>">
		<button type="button" class="button hp-ing-remove">&times;</button>
	</div>
	<?php
}

add_action( 'wp_ajax_hp_get_ing_row', function() {
	$i = absint( $_POST['i'] ?? 0 );
	hp_render_ing_row( $i );
	wp_die();
} );

/* ─────────────── FAQs (repeater) ─────────────── */
function hp_mb_faqs( $post ) {
	$faqs = get_post_meta( $post->ID, '_hp_faqs', true );
	if ( ! is_array( $faqs ) ) {
		$faqs = [];
	}
	?>
	<div id="hp-faq-wrap">
		<?php foreach ( $faqs as $i => $faq ) : ?>
			<?php hp_render_faq_row( $i, $faq ); ?>
		<?php endforeach; ?>
	</div>
	<button type="button" class="button" id="hp-faq-add">+ <?php esc_html_e( 'Add FAQ', 'herbalpearls' ); ?></button>
	<script>
	jQuery(function($){
		$('#hp-faq-add').on('click', function(){
			var i = $('#hp-faq-wrap .hp-faq-row').length;
			$.post(ajaxurl, { action: 'hp_get_faq_row', i: i }, function(html){
				$('#hp-faq-wrap').append(html);
			});
		});
		$(document).on('click', '.hp-faq-remove', function(){
			$(this).closest('.hp-faq-row').remove();
		});
	});
	</script>
	<p class="description"><?php esc_html_e( 'Auto-generates FAQ schema on the PDP.', 'herbalpearls' ); ?></p>
	<?php
}

function hp_render_faq_row( $i, $faq = [] ) {
	?>
	<div class="hp-faq-row" style="margin-bottom:12px;border:1px solid #ddd;padding:8px;border-radius:4px;">
		<input type="text" name="hp_faqs[<?php echo (int) $i; ?>][question]"
			   value="<?php echo esc_attr( $faq['question'] ?? '' ); ?>"
			   placeholder="<?php esc_attr_e( 'Question', 'herbalpearls' ); ?>" style="width:100%;margin-bottom:4px;">
		<textarea name="hp_faqs[<?php echo (int) $i; ?>][answer]" rows="3"
				  placeholder="<?php esc_attr_e( 'Answer', 'herbalpearls' ); ?>" style="width:100%;"><?php echo esc_textarea( $faq['answer'] ?? '' ); ?></textarea>
		<button type="button" class="button button-small hp-faq-remove"><?php esc_html_e( 'Remove', 'herbalpearls' ); ?></button>
	</div>
	<?php
}

add_action( 'wp_ajax_hp_get_faq_row', function() {
	$i = absint( $_POST['i'] ?? 0 );
	hp_render_faq_row( $i );
	wp_die();
} );

/* ─────────────── Save handler ─────────────── */
add_action( 'save_post_product', function( $post_id ) {
	if ( ! isset( $_POST['hp_meta_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['hp_meta_nonce'], 'hp_meta_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Simple fields
	$simple = [
		'_hp_amazon_url'    => 'esc_url_raw',
		'_hp_flipkart_url'  => 'esc_url_raw',
		'_hp_blog_link_id'  => 'absint',
		'_hp_how_to_short'  => 'wp_kses_post',
	];
	foreach ( $simple as $meta => $sanitizer ) {
		$key = ltrim( $meta, '_' );
		if ( isset( $_POST[ $key ] ) ) {
			$clean = call_user_func( $sanitizer, wp_unslash( $_POST[ $key ] ) );
			update_post_meta( $post_id, $meta, $clean );
		}
	}

	// Ingredients
	$ings = [];
	if ( isset( $_POST['hp_ingredients'] ) && is_array( $_POST['hp_ingredients'] ) ) {
		foreach ( wp_unslash( $_POST['hp_ingredients'] ) as $ing ) {
			$name    = sanitize_text_field( $ing['name'] ?? '' );
			$benefit = sanitize_text_field( $ing['benefit'] ?? '' );
			if ( '' === $name ) {
				continue;
			}
			$ings[] = [ 'name' => $name, 'benefit' => $benefit ];
		}
	}
	update_post_meta( $post_id, '_hp_ingredients', $ings );

	// FAQs
	$faqs = [];
	if ( isset( $_POST['hp_faqs'] ) && is_array( $_POST['hp_faqs'] ) ) {
		foreach ( wp_unslash( $_POST['hp_faqs'] ) as $faq ) {
			$q = sanitize_text_field( $faq['question'] ?? '' );
			$a = wp_kses_post( $faq['answer'] ?? '' );
			if ( '' === $q || '' === $a ) {
				continue;
			}
			$faqs[] = [ 'question' => $q, 'answer' => $a ];
		}
	}
	update_post_meta( $post_id, '_hp_faqs', $faqs );
} );

/* ─────────────── Template helpers ─────────────── */
function hp_get_marketplace_links( $product_id ) {
	$a = get_post_meta( $product_id, '_hp_amazon_url',   true );
	$f = get_post_meta( $product_id, '_hp_flipkart_url', true );

	if ( $a ) {
		$a .= ( false === strpos( $a, '?' ) ? '?' : '&' ) . 'utm_source=website&utm_medium=marketplace&utm_campaign=amazon_pdp';
	}
	if ( $f ) {
		$f .= ( false === strpos( $f, '?' ) ? '?' : '&' ) . 'utm_source=website&utm_medium=marketplace&utm_campaign=flipkart_pdp';
	}

	return [ 'amazon' => $a, 'flipkart' => $f ];
}

function hp_get_product_faqs( $product_id ) {
	$faqs = get_post_meta( $product_id, '_hp_faqs', true );
	return is_array( $faqs ) ? $faqs : [];
}

function hp_get_product_ingredients( $product_id ) {
	$ings = get_post_meta( $product_id, '_hp_ingredients', true );
	return is_array( $ings ) ? $ings : [];
}
