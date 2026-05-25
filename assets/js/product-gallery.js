/**
 * Herbal Pearls — Product Gallery
 * Thumbnail switcher + click-to-zoom. Vanilla JS.
 *
 * @package HerbalPearls
 */

(function () {
	'use strict';

	const gallery = document.querySelector('.woocommerce-product-gallery');
	if (!gallery) return;

	const mainImage = gallery.querySelector('.woocommerce-product-gallery__image img');
	const thumbs = gallery.querySelectorAll('.flex-control-thumbs li img');

	if (!mainImage) return;

	// Thumbnail click → swap main image
	thumbs.forEach((thumb) => {
		thumb.addEventListener('click', (e) => {
			e.preventDefault();
			const fullSrc = thumb.getAttribute('data-src') || thumb.src.replace(/-\d+x\d+/, '');
			mainImage.src = fullSrc;
			mainImage.setAttribute('srcset', '');

			thumbs.forEach((t) => t.classList.remove('flex-active'));
			thumb.classList.add('flex-active');
		});
	});

	// Click main image → lightbox zoom
	mainImage.addEventListener('click', () => {
		const overlay = document.createElement('div');
		overlay.className = 'hp-gallery-zoom';
		overlay.setAttribute('role', 'dialog');
		overlay.setAttribute('aria-label', 'Image zoom');
		overlay.innerHTML = `
			<button type="button" class="hp-gallery-zoom__close" aria-label="Close zoom">&times;</button>
			<img src="${mainImage.src}" alt="${mainImage.alt || ''}">
		`;

		overlay.addEventListener('click', (e) => {
			if (e.target === overlay || e.target.classList.contains('hp-gallery-zoom__close')) {
				overlay.remove();
				document.body.style.overflow = '';
			}
		});

		document.addEventListener('keydown', function closeOnEsc(e) {
			if (e.key === 'Escape') {
				overlay.remove();
				document.body.style.overflow = '';
				document.removeEventListener('keydown', closeOnEsc);
			}
		});

		document.body.style.overflow = 'hidden';
		document.body.appendChild(overlay);
	});

})();
