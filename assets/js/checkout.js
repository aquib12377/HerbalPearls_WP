/**
 * Herbal Pearls — Checkout JS
 * Pincode lookup via REST API, validation, sticky mobile total.
 * Vanilla JS.
 *
 * @package HerbalPearls
 */

(function () {
	'use strict';

	/* ─────────────── Pincode lookup ─────────────── */
	const billingPostcode = document.getElementById('billing_postcode');
	const shippingPostcode = document.getElementById('shipping_postcode');

	function setupPincodeLookup(input) {
		if (!input) return;

		let feedback = input.parentNode.querySelector('.hp-pincode-feedback');
		if (!feedback) {
			feedback = document.createElement('span');
			feedback.className = 'hp-pincode-feedback';
			feedback.style.cssText = 'display:block;font-size:0.8125rem;margin-top:0.375rem;';
			input.parentNode.appendChild(feedback);
		}

		let timeout;
		input.addEventListener('input', () => {
			clearTimeout(timeout);
			const code = input.value.trim();

			if (code.length !== 6 || !/^\d{6}$/.test(code)) {
				feedback.textContent = '';
				feedback.className = 'hp-pincode-feedback';
				return;
			}

			feedback.textContent = 'Checking…';
			feedback.className = 'hp-pincode-feedback';
			feedback.style.color = 'var(--hp-text-3)';

			timeout = setTimeout(() => {
				fetch('/wp-json/hp/v1/pincode/' + code)
					.then((res) => {
						if (!res.ok) throw new Error('Network error');
						return res.json();
					})
					.then((data) => {
						if (data.delivery) {
							feedback.textContent = 'Delivery available (' + data.estimated_days + ' days)' + (data.cod_available ? ' · COD available' : '');
							feedback.className = 'hp-pincode-feedback';
							feedback.style.color = 'var(--hp-success)';
						} else {
							feedback.textContent = 'Delivery not available in your area.';
							feedback.className = 'hp-pincode-feedback';
							feedback.style.color = 'var(--hp-coral)';
						}
					})
					.catch(() => {
						feedback.textContent = 'Could not verify. Please try again.';
						feedback.className = 'hp-pincode-feedback';
						feedback.style.color = 'var(--hp-coral)';
					});
			}, 500);
		});
	}

	setupPincodeLookup(billingPostcode);
	setupPincodeLookup(shippingPostcode);

	/* ─────────────── Sticky mobile order total ─────────────── */
	const checkoutForm = document.querySelector('form.checkout');
	const orderReview = document.getElementById('order_review');

	if (checkoutForm && orderReview && window.innerWidth < 768) {
		const stickyBar = document.createElement('div');
		const totalEl = orderReview.querySelector('.order-total .woocommerce-Price-amount');
		const totalText = totalEl ? totalEl.textContent : '';

		stickyBar.className = 'hp-checkout-sticky-total';
		stickyBar.innerHTML = `
			<span class="hp-checkout-sticky-total__label">Total</span>
			<span class="hp-checkout-sticky-total__amount">${totalText}</span>
		`;

		document.body.appendChild(stickyBar);
	}

})();
