/**
 * Herbal Pearls — Main JS
 * Mobile drawer, header scroll, accordion, quantity stepper.
 * Vanilla JS, no jQuery.
 *
 * @package HerbalPearls
 */

(function(){
  'use strict';

  /* ─────────────── Header scroll effect ─────────────── */
  const header = document.querySelector('.hp-header');
  if(header){
    const onScroll = () => header.classList.toggle('is-scrolled', window.scrollY > 10);
    window.addEventListener('scroll', onScroll, {passive:true});
    onScroll();
  }

  /* ─────────────── Mobile drawer ─────────────── */
  const drawer = document.getElementById('mobile-menu');
  const overlay = document.getElementById('mobile-menu-overlay');
  const toggleBtns = document.querySelectorAll('.hp-menu-toggle');
  const closeBtns = document.querySelectorAll('.hp-menu-close');

  function openDrawer(){
    if(!drawer) return;
    drawer.classList.add('is-open');
    drawer.setAttribute('aria-hidden','false');
    if(overlay){ overlay.classList.add('is-open'); overlay.setAttribute('aria-hidden','false'); }
    document.body.style.overflow = 'hidden';
  }

  function closeDrawer(){
    if(!drawer) return;
    drawer.classList.remove('is-open');
    drawer.setAttribute('aria-hidden','true');
    if(overlay){ overlay.classList.remove('is-open'); overlay.setAttribute('aria-hidden','true'); }
    document.body.style.overflow = '';
  }

  toggleBtns.forEach(function(btn){ btn.addEventListener('click', openDrawer); });
  closeBtns.forEach(function(btn){ btn.addEventListener('click', closeDrawer); });
  if(overlay) overlay.addEventListener('click', closeDrawer);
  document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeDrawer(); });

  /* ─────────────── Cart count live update (WooCommerce fragments) ─────────────── */
  document.body.addEventListener('wc_fragments_refreshed', function(){
    const countEls = document.querySelectorAll('[data-cart-count]');
    countEls.forEach(function(el){
      const newCount = el.textContent.trim();
      if(newCount==='0') el.style.display='none'; else el.style.display='';
    });
  });

  /* ─────────────── Accordion ─────────────── */
  document.querySelectorAll('[data-accordion] .hp-accordion__trigger').forEach(function(trigger){
    trigger.addEventListener('click', function(){
      const item = trigger.closest('.hp-accordion__item');
      if(!item) return;

      const isOpen = item.classList.contains('is-open');

      const parent = item.parentNode;
      parent.querySelectorAll('.hp-accordion__item.is-open').forEach(function(el){
        el.classList.remove('is-open');
        const t = el.querySelector('.hp-accordion__trigger');
        if(t) t.setAttribute('aria-expanded','false');
      });

      if(!isOpen){
        item.classList.add('is-open');
        trigger.setAttribute('aria-expanded','true');
      }
    });
  });

  /* ─────────────── Smooth scroll for in-page anchors ─────────────── */
  document.querySelectorAll('a[href^="#"]').forEach(function(anchor){
    anchor.addEventListener('click', function(e){
      const href = anchor.getAttribute('href');
      if(!href || href==='#') return;
      const target = document.querySelector(href);
      if(!target) return;
      e.preventDefault();
      target.scrollIntoView({behavior:'smooth', block:'start'});
    });
  });

  /* ─────────────── Quantity stepper ─────────────── */
  document.querySelectorAll('.hp-qty').forEach(function(stepper){
    const input = stepper.querySelector('.hp-qty__input');
    const minusBtn = stepper.querySelector('[data-action="minus"]');
    const plusBtn = stepper.querySelector('[data-action="plus"]');

    if(!input) return;

    if(minusBtn) minusBtn.addEventListener('click', function(){
      const val = parseInt(input.value, 10) || 1;
      const min = parseInt(input.getAttribute('min'), 10) || 1;
      if(val > min){
        input.value = val - 1;
        input.dispatchEvent(new Event('change', {bubbles:true}));
      }
    });

    if(plusBtn) plusBtn.addEventListener('click', function(){
      const val = parseInt(input.value, 10) || 1;
      const max = parseInt(input.getAttribute('max'), 10) || Infinity;
      if(val < max){
        input.value = val + 1;
        input.dispatchEvent(new Event('change', {bubbles:true}));
      }
    });
  });

  /* ─────────────── Variation pills (PDP) ─────────────── */
  document.querySelectorAll('.hp-var-pill').forEach(function(pill){
    pill.addEventListener('click', function(){
      const group = pill.parentNode;
      group.querySelectorAll('.hp-var-pill').forEach(function(p){
        p.classList.remove('is-active');
        p.setAttribute('aria-checked','false');
      });
      pill.classList.add('is-active');
      pill.setAttribute('aria-checked','true');

      const select = group.parentNode.querySelector('select');
      if(select){
        select.value = pill.dataset.value;
        select.dispatchEvent(new Event('change', {bubbles:true}));
      }
    });
  });

  /* ─────────────── Sticky mobile CTA — show/hide on scroll ─────────────── */
  const stickyCTA = document.querySelector('[data-sticky-cta]');
  const stickyATC = document.querySelector('[data-sticky-atc]');
  const desktopForm = document.querySelector('.hp-pdp__info form.cart');

  if(stickyCTA && stickyATC && desktopForm){
    let ticking = false;

    const updateStickyBar = function(){
      const scrollY = window.scrollY;
      const docHeight = document.documentElement.scrollHeight;
      const winHeight = window.innerHeight;
      const atBottom = scrollY + winHeight >= docHeight - 100;

      if(scrollY > 600 && !atBottom){
        stickyCTA.removeAttribute('aria-hidden');
      } else {
        stickyCTA.setAttribute('aria-hidden','true');
      }
      ticking = false;
    };

    stickyATC.addEventListener('click', function(){
      const desktopBtn = desktopForm.querySelector('button[type="submit"]');
      if(desktopBtn){ desktopBtn.click(); } else { desktopForm.submit(); }
    });

    window.addEventListener('scroll', function(){
      if(!ticking){
        window.requestAnimationFrame(updateStickyBar);
        ticking = true;
      }
    }, {passive:true});

    updateStickyBar();
  }

})();
