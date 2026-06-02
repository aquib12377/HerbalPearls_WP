# Herbal Pearls — Redesign Status & Gap Analysis

_Status report for the "Phlox shop-cosmetic" redesign brief, mapped against the
current state of the theme. Generated 2026-06-02._

## TL;DR

The headline redesign described in the brief has **already been implemented** in
this repo. The theme is a bespoke, token-driven WordPress + WooCommerce theme
(Tailwind build pipeline + hand-authored `.hp-*` CSS) — **not** the purple
Elementor site the brief assumes. The warm Ayurvedic palette is in place, every
homepage section from the brief exists, and the content cleanup the brief
demands (watch/warranty leftovers, "Lorem ipsum", demo credits) was already
done.

This session added **polish toward the Phlox reference** rather than rebuilding
what already works.

## Brief → current state

| Brief requirement | Status | Notes |
|---|---|---|
| §3 Ayurvedic palette, replace purple `#7e50d3` | ✅ Done | Warm champagne/gold/cream tokens in `assets/css/tokens.css`. No purple anywhere. (Note: brand uses gold `#A0793F` as primary + hibiscus `#B8403A` for sale, not the brief's green/gold — an intentional brand choice already shipped.) |
| §3 Typography (sans body + display headings) | ✅ Done | Inter (body) + Cormorant Garamond (display) + Allura (script accents). |
| §3 Pill/rounded buttons, soft cards, generous spacing | ✅ Done | `.hp-btn`, `.hp-card`, `.hp-section` (80px desktop padding). |
| §4.1 Announcement bar | ✅ Done | `header.php` — free-shipping/welcome promo. |
| §4.2 Sticky header (logo, nav, search, account, cart count) | ✅ Done | `.hp-header` sticky w/ `is-scrolled`; mini-cart drawer. |
| §4.3 Hero banner + slider + CTA | ✅ Done | Customizer-driven 3-slide hero. |
| §4.4 USP/trust strip, remove "watches/10yr warranty" | ✅ Done | 100% Natural / Paraben Free / Cruelty Free / Pan-India Shipping. No watch text exists. |
| §4.5 Category showcase | ✅ Improved this session | See below. |
| §4.6 Featured products w/ ₹ price + sale pill | ✅ Done | `[hp_bestsellers]` grid + `.hp-badge--sale` pill. |
| §4.7 Promotional banner band | ➖ Partial | Covered by the "Save 10% on Curated Combos" bundle promo section; no separate full-width CTA band. |
| §4.8 Best sellers **with tabs** | ➖ Gap | Bestsellers grid exists but has no All/Hair/Skin tab filter. |
| §4.9 Recent blog posts | ✅ Done | `[hp_blog_highlight count="3"]`. |
| §4.10 Testimonials | ✅ Done | `[hp_reviews_carousel]`. |
| §4.11 Newsletter subscribe | ✅ Done | Discount-offer subscribe block. |
| §4.12 Footer (logo, contact, links, social, © 2026) | ✅ Done | Auto-year copyright, no demo credits. |
| §6 Responsive / a11y / perf | ✅ Done | 4→2→1 grids, lazy-load, deferred JS, dequeued WC bloat, focus states, reduced-motion. |
| §7 Content cleanup | ✅ Done | No "Lorem ipsum", watches, or demo credits in the codebase. |

## Changes made this session (polish toward Phlox)

All changes are CSS + homepage markup only — **no** WooCommerce/cart/checkout
logic was touched.

1. **Consistent labeled section headers (homepage).**
   The homepage previously used plain centered `<h2>`s while the rest of the
   site (PDP, bundles) used the richer `.hp-section-h` pattern (script accent +
   serif title). All seven homepage section headers now use `.hp-section-h`,
   matching the Phlox reference's "eyebrow label + title" sections and unifying
   the site.
   _File: `front-page.php`._

2. **Image-forward category cards.**
   The category showcase was plain white stacked cards (image → title → "Explore"
   text link). It's now image-forward overlay cards with a bottom gradient,
   white title, uppercase "Shop Now →" CTA, and hover zoom + lift — matching the
   Phlox category blocks.
   _New component `.hp-cat-card` in `assets/css/components.css`; markup in
   `front-page.php`. Reuses the existing `hp-product-card` (600×600) image size —
   no media regeneration required._

3. **Premium product-card hover.**
   Product cards now lift (`translateY(-4px)`) with a larger soft shadow on hover
   instead of only a border/shadow change.
   _File: `assets/css/components.css` (`.hp-product-card`)._

### New/changed CSS classes (for the client to maintain)

- `.hp-cat-card`, `.hp-cat-card__img`, `.hp-cat-card__overlay`,
  `.hp-cat-card__content`, `.hp-cat-card__title`, `.hp-cat-card__cta`
  (new, in `components.css`).
- `.hp-product-card` hover — added `transform` lift + `--hp-shadow-lg`.

No global tokens, colors, fonts, or theme settings were changed.

## Remaining gaps (recommended next steps, not done this session)

These were intentionally left for a follow-up because they need product/taxonomy
decisions or are larger than a styling pass:

1. **Category set.** The brief lists Fertility / Weight Loss / Hair Care /
   Skin Care; the homepage currently shows Skin / Hair / Wellness (hardcoded in
   `front-page.php`). Aligning these needs the **real `product_cat` slugs** from
   the live site to avoid broken links.
2. **Tabbed Best Sellers** (§4.8) — All/Hair/Skin filter on the bestsellers grid.
3. **Dedicated full-width promo banner** (§4.7) — a standalone CTA band, distinct
   from the bundle promo.

## Caveats

- This environment has the theme files only — no WordPress runtime, database, or
  live site. Changes are verified by PHP lint and code review, **not** rendered
  output. Review on staging before going live.
- `assets/css/tailwind.css` is an empty build artifact; the polish here uses
  hand-authored `.hp-*` classes in `components.css` (enqueued directly), so no
  `npm run build:css` step is required for these changes to take effect.
