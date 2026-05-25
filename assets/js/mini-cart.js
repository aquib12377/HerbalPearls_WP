/**
 * Herbal Pearls — Mini Cart
 * AJAX add-to-cart drawer using WC fragments. Vanilla JS.
 *
 * @package HerbalPearls
 */

(function () {
	'use strict';

	const cartToggle = document.querySelector('[data-toggle="mini-cart"]');
	const cartDrawer = document.getElementById('mini-cart-drawer');
	const cartOverlay = document.getElementById('mini-cart-overlay');

	if (!cartDrawer) return;

	function openCart() {
		cartDrawer.classList.add('is-open');
		cartDrawer.setAttribute('aria-hidden', 'false');
		if (cartOverlay) {
			cartOverlay.classList.add('is-open');
			cartOverlay.setAttribute('aria-hidden', 'false');
		}
		document.body.style.overflow = 'hidden';
	}

	function closeCart() {
		cartDrawer.classList.remove('is-open');
		cartDrawer.setAttribute('aria-hidden', 'true');
		if (cartOverlay) {
			cartOverlay.classList.remove('is-open');
			cartOverlay.setAttribute('aria-hidden', 'true');
		}
		document.body.style.overflow = '';
	}

	if (cartToggle) {
		cartToggle.addEventListener('click', openCart);
	}

	if (cartOverlay) {
		cartOverlay.addEventListener('click', closeCart);
	}

	// Close button inside drawer
	cartDrawer.querySelector('[data-close="mini-cart"]')?.addEventListener('click', closeCart);

	// Close on Escape
	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape' && cartDrawer.classList.contains('is-open')) {
			closeCart();
		}
	});

	// Listen for WC fragments refresh (cart count update)
	document.body.addEventListener('wc_fragments_refreshed', () => {
		const count = document.querySelector('[data-cart-count]');
		if (count && parseInt(count.textContent, 10) > 0) {
			openCart();
		}
	});

	// Remove item
	cartDrawer.addEventListener('click', (e) => {
		const removeBtn = e.target.closest('[data-remove-item]');
		if (!removeBtn) return;

		const cartItemKey = removeBtn.dataset.removeItem;
		if (!cartItemKey) return;

		fetch(wc_cart_fragments_params?.ajax_url || '/wp-admin/admin-ajax.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: new URLSearchParams({
				action: 'custom_remove_from_cart',
				cart_item_key: cartItemKey,
				_ajax_nonce: wc_cart_fragments_params?.nonce || ''
			})
		}).then(() => {
			// WC will fire fragments refresh automatically
		});
	});

})();
