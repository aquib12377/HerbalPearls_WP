/**
 * Herbal Pearls — Shop Filters
 * Mobile bottom-sheet trigger + desktop sidebar toggle. Vanilla JS.
 *
 * @package HerbalPearls
 */

(function () {
	'use strict';

	const filterToggle = document.querySelector('[data-toggle="shop-filters"]');
	const filterSidebar = document.getElementById('shop-filters');
	const filterClose = document.querySelector('[data-close-filters]');

	if (!filterToggle || !filterSidebar) return;

	// Show toggle button on mobile
	function checkMobile() {
		const isMobile = window.innerWidth < 768;
		filterToggle.style.display = isMobile ? 'flex' : 'none';
		filterSidebar.classList.toggle('is-mobile', isMobile);
	}

	checkMobile();
	window.addEventListener('resize', checkMobile);

	// Open filters
	filterToggle.addEventListener('click', () => {
		filterSidebar.classList.add('is-open');

		// Create overlay on mobile
		if (window.innerWidth < 768) {
			const overlay = document.createElement('div');
			overlay.className = 'hp-bottomsheet__overlay is-open';
			overlay.setAttribute('id', 'filter-overlay');
			overlay.addEventListener('click', closeFilters);
			document.body.appendChild(overlay);
			document.body.style.overflow = 'hidden';
		}
	});

	// Close filters
	filterClose?.addEventListener('click', closeFilters);

	function closeFilters() {
		filterSidebar.classList.remove('is-open');
		const overlay = document.getElementById('filter-overlay');
		if (overlay) {
			overlay.remove();
		}
		document.body.style.overflow = '';
	}

})();
