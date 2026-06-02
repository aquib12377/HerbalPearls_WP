/**
 * Herbal Pearls — Main JS
 * Mobile menu toggle, accordion, sticky header, smooth scroll.
 * Vanilla JS, no jQuery.
 *
 * @package HerbalPearls
 */

(function () {
	'use strict';

	/* ─────────────── Mobile menu toggle ─────────────── */
	const menuToggle = document.querySelector('[data-toggle="mobile-menu"]');
	const mobileMenu = document.getElementById('mobile-menu');
	const mobileOverlay = document.getElementById('mobile-menu-overlay');

	if (menuToggle && mobileMenu) {
		menuToggle.addEventListener('click', () => {
			const isOpen = mobileMenu.classList.toggle('is-open');
			menuToggle.setAttribute('aria-expanded', isOpen);

			if (mobileOverlay) {
				mobileOverlay.classList.toggle('is-open', isOpen);
				mobileOverlay.setAttribute('aria-hidden', !isOpen);
			}
			mobileMenu.setAttribute('aria-hidden', !isOpen);
			document.body.style.overflow = isOpen ? 'hidden' : '';
		});

		if (mobileOverlay) {
			mobileOverlay.addEventListener('click', closeMobileMenu);
		}
	}

	function closeMobileMenu() {
		if (mobileMenu) {
			mobileMenu.classList.remove('is-open');
			mobileMenu.setAttribute('aria-hidden', 'true');
		}
		if (mobileOverlay) {
			mobileOverlay.classList.remove('is-open');
			mobileOverlay.setAttribute('aria-hidden', 'true');
		}
		if (menuToggle) {
			menuToggle.setAttribute('aria-expanded', 'false');
		}
		document.body.style.overflow = '';
	}

	/* ─────────────── Accordion ─────────────── */
	document.querySelectorAll('[data-accordion] .hp-accordion__trigger').forEach((trigger) => {
		trigger.addEventListener('click', () => {
			const item = trigger.closest('.hp-accordion__item');
			if (!item) return;

			const isOpen = item.classList.contains('is-open');

			// Close all siblings
			const parent = item.parentNode;
			parent.querySelectorAll('.hp-accordion__item.is-open').forEach((el) => {
				el.classList.remove('is-open');
				el.querySelector('.hp-accordion__trigger')?.setAttribute('aria-expanded', 'false');
			});

			// Toggle current
			if (!isOpen) {
				item.classList.add('is-open');
				trigger.setAttribute('aria-expanded', 'true');
			} else {
				item.classList.remove('is-open');
				trigger.setAttribute('aria-expanded', 'false');
			}
		});
	});

	/* ─────────────── Sticky header on scroll ─────────────── */
	const header = document.getElementById('masthead');
	let lastScrollY = 0;

	function onScroll() {
		const scrollY = window.scrollY;
		if (header) {
			header.classList.toggle('is-scrolled', scrollY > 20);
		}
		lastScrollY = scrollY;
	}

	window.addEventListener('scroll', onScroll, { passive: true });

	/* ─────────────── Smooth scroll for in-page anchors ─────────────── */
	document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
		anchor.addEventListener('click', (e) => {
			const href = anchor.getAttribute('href');
			if (!href || href === '#') return;

			const target = document.querySelector(href);
			if (!target) return;

			e.preventDefault();
			target.scrollIntoView({ behavior: 'smooth', block: 'start' });
		});
	});

	/* ─────────────── Hero slider auto-rotation ─────────────── */
	const heroSlider = document.querySelector('[data-hero-slider]');
	if (heroSlider) {
		const slides = heroSlider.querySelectorAll('.hp-hero__slide');
		const reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		let current = 0;

		if (slides.length > 1 && !reduceMotion) {
			setInterval(() => {
				slides[current].setAttribute('hidden', '');
				current = (current + 1) % slides.length;
				slides[current].removeAttribute('hidden');
			}, 5000);
		}
	}

	/* ─────────────── Quantity stepper ─────────────── */
	document.querySelectorAll('.hp-qty').forEach((stepper) => {
		const input = stepper.querySelector('.hp-qty__input');
		const minusBtn = stepper.querySelector('[data-action="minus"]');
		const plusBtn = stepper.querySelector('[data-action="plus"]');

		if (!input) return;

		minusBtn?.addEventListener('click', () => {
			const val = parseInt(input.value, 10) || 1;
			const min = parseInt(input.getAttribute('min'), 10) || 1;
			if (val > min) {
				input.value = val - 1;
				input.dispatchEvent(new Event('change', { bubbles: true }));
			}
		});

		plusBtn?.addEventListener('click', () => {
			const val = parseInt(input.value, 10) || 1;
			const max = parseInt(input.getAttribute('max'), 10) || Infinity;
			if (val < max) {
				input.value = val + 1;
				input.dispatchEvent(new Event('change', { bubbles: true }));
			}
		});
	});

	/* ─────────────── Variation pills (PDP) ───────────────
	 * Handled inline in woocommerce/single-product/add-to-cart/variable.php
	 * (self-contained: the template does its own variation lookup so the
	 * form is safe to submit even if WC's add-to-cart-variation JS hasn't
	 * initialized). Keeping this block empty preserves the surrounding
	 * IIFE structure. */
		/* Sticky mobile CTA — show/hide on scroll */
		const stickyCTA = document.querySelector('[data-sticky-cta]');
		const stickyATC = document.querySelector('[data-sticky-atc]');
		const desktopForm = document.querySelector('.hp-pdp__info form.cart');

		if (stickyCTA && stickyATC && desktopForm) {
			let lastScrollY = 0;
			let ticking = false;

			const updateStickyBar = () => {
				const scrollY = window.scrollY;
				const docHeight = document.documentElement.scrollHeight;
				const winHeight = window.innerHeight;
				const atBottom = scrollY + winHeight >= docHeight - 100;

				// Show bar after scrolling past the desktop CTA (roughly 600px from top)
				if (scrollY > 600 && !atBottom) {
					stickyCTA.removeAttribute('aria-hidden');
				} else if (atBottom) {
					stickyCTA.setAttribute('aria-hidden', 'true');
				} else {
					stickyCTA.setAttribute('aria-hidden', 'true');
				}

				ticking = false;
			};

			// Click on sticky CTA triggers the desktop form submit
			stickyATC.addEventListener('click', () => {
				const desktopBtn = desktopForm.querySelector('button[type="submit"]');
				if (desktopBtn) {
					desktopBtn.click();
				} else {
					desktopForm.submit();
				}
			});

			window.addEventListener('scroll', () => {
				lastScrollY = window.scrollY;
				if (!ticking) {
					window.requestAnimationFrame(updateStickyBar);
					ticking = true;
				}
			}, { passive: true });

			updateStickyBar();
		}

})();
