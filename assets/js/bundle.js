/**
 * Herbal Pearls — Bundle Add-to-Cart
 * From v2 Appendix A.1 — verbatim.
 *
 * @package HerbalPearls
 */

/* globals jQuery, HP_BUNDLE */
(function($){
	'use strict';

	$(document).on('submit', '#hp-bundle-form', function(e){
		e.preventDefault();
		var $form = $(this);
		var $btn = $form.find('button[type=submit]');
		var $msg = $form.find('.hp-bundle-msg');

		var data = {
			action: 'hp_add_bundle_to_cart',
			nonce: HP_BUNDLE.nonce,
			bundle_id: $form.data('bundle-id'),
			selections: {}
		};
		$form.find('select[name^=selections]').each(function(){
			var pid = $(this).attr('name').match(/\[(\d+)\]/)[1];
			data.selections[pid] = $(this).val();
		});

		$btn.prop('disabled', true).text('Adding…');
		$msg.removeClass('error success').text('');

		$.post(HP_BUNDLE.ajaxurl, data)
			.done(function(res){
				if (res.success) {
					$msg.addClass('success').text('Added to cart!');
					window.location.href = res.data.cart_url;
				} else {
					$msg.addClass('error').text(res.data.message || 'Could not add bundle.');
					$btn.prop('disabled', false).text('Add bundle to cart');
				}
			})
			.fail(function(xhr){
				var m = (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) || 'Network error.';
				$msg.addClass('error').text(m);
				$btn.prop('disabled', false).text('Add bundle to cart');
			});
	});
})(jQuery);
