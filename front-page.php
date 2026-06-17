<?php if(!defined('ABSPATH'))exit;
get_header(); ?>

<!-- HERO — split layout -->
<section class="hp-hero-split" aria-label="<?php esc_attr_e('Featured','herbalpearls'); ?>">
  <div class="hp-hero-split__inner">
    <div class="hp-hero-split__copy" style="animation:hpFade .9s ease both;">
      <div class="hp-eyebrow" style="margin-bottom:26px;"><?php esc_html_e('Ayurvedic Apothecary · Est. 2024','herbalpearls'); ?></div>
      <h1 class="hp-hero-split__title"><?php esc_html_e('Botanicals,','herbalpearls'); ?><br><?php esc_html_e('perfected by','herbalpearls'); ?><br><em style="font-style:italic;color:var(--hp-green-soft);"><?php esc_html_e('ritual.','herbalpearls'); ?></em></h1>
      <p class="hp-hero-split__lead"><?php esc_html_e('Small-batch skincare, hair and wellness formulas — pressed from proven natural ingredients and crafted to actually work. Healthy &amp; organic, by design.','herbalpearls'); ?></p>
      <div class="hp-hero-split__cta">
        <a href="<?php echo esc_url(function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : '#'); ?>" class="hp-btn hp-btn--primary"><?php esc_html_e('Shop the Collection','herbalpearls'); ?></a>
        <a href="#ingredients" class="hp-link"><?php esc_html_e('Our Ingredients →','herbalpearls'); ?></a>
      </div>
    </div>
    <div class="hp-hero-split__media" style="animation:hpFadeIn 1.2s ease both;">
      <div class="hp-frame hp-hero-split__frame">
        <div class="hp-hero-split__img-wrap">
          <?php
          $hero_id = absint(get_theme_mod('hp_hero_image_id_1',0));
          $hero_url = get_theme_mod('hp_hero_image_url_1','');
          if($hero_id): echo wp_get_attachment_image($hero_id,'large',false,['class'=>'hp-hero-split__img','alt'=>get_bloginfo('name'),'fetchpriority'=>'high']);
          elseif($hero_url): ?>
            <img class="hp-hero-split__img" src="<?php echo esc_url($hero_url); ?>" alt="<?php bloginfo('name'); ?>" fetchpriority="high">
          <?php else: ?>
            <div class="hp-hero-split__img hp-hero-split__img--placeholder"></div>
          <?php endif; ?>
        </div>
      </div>
      <div class="hp-hero-split__seal" aria-hidden="true">
        <span class="hp-hero-split__seal-num">100%</span>
        <span class="hp-hero-split__seal-label"><?php esc_html_e('Natural','herbalpearls'); ?></span>
      </div>
    </div>
  </div>
</section>

<!-- TRUST MARQUEE -->
<section class="hp-marquee-band" aria-label="<?php esc_attr_e('Our values','herbalpearls'); ?>">
  <div class="hp-marquee-track" aria-hidden="true">
    <div class="hp-marquee-inner">
      <span class="hp-marquee-item"><?php esc_html_e('100% Natural','herbalpearls'); ?></span><span class="hp-marquee-tick">&#10022;</span>
      <span class="hp-marquee-item"><?php esc_html_e('Paraben Free','herbalpearls'); ?></span><span class="hp-marquee-tick">&#10022;</span>
      <span class="hp-marquee-item"><?php esc_html_e('Cruelty Free','herbalpearls'); ?></span><span class="hp-marquee-tick">&#10022;</span>
      <span class="hp-marquee-item"><?php esc_html_e('Pan-India Shipping','herbalpearls'); ?></span><span class="hp-marquee-tick">&#10022;</span>
    </div>
    <div class="hp-marquee-inner" aria-hidden="true">
      <span class="hp-marquee-item"><?php esc_html_e('100% Natural','herbalpearls'); ?></span><span class="hp-marquee-tick">&#10022;</span>
      <span class="hp-marquee-item"><?php esc_html_e('Paraben Free','herbalpearls'); ?></span><span class="hp-marquee-tick">&#10022;</span>
      <span class="hp-marquee-item"><?php esc_html_e('Cruelty Free','herbalpearls'); ?></span><span class="hp-marquee-tick">&#10022;</span>
      <span class="hp-marquee-item"><?php esc_html_e('Pan-India Shipping','herbalpearls'); ?></span><span class="hp-marquee-tick">&#10022;</span>
    </div>
  </div>
</section>

<!-- CATEGORIES -->
<section class="hp-section hp-categories" aria-label="<?php esc_attr_e('Shop by category','herbalpearls'); ?>">
  <div class="hp-container">
    <div class="hp-section-head hp-text-center" style="margin-bottom:48px;">
      <div class="hp-eyebrow" style="margin-bottom:16px;"><?php esc_html_e('Explore','herbalpearls'); ?></div>
      <h2 class="hp-display hp-h2"><?php esc_html_e('Shop by Category','herbalpearls'); ?></h2>
    </div>
    <div class="hp-grid-3">
      <?php
      $cats = [
        'skin'     => ['label' => __('Skin','herbalpearls'),     'blurb' => __('10 products','herbalpearls')],
        'hair'     => ['label' => __('Hair','herbalpearls'),     'blurb' => __('6 products','herbalpearls')],
        'wellness' => ['label' => __('Wellness','herbalpearls'), 'blurb' => __('4 products','herbalpearls')],
      ];
      foreach($cats as $slug => $cat):
        $term = get_term_by('slug',$slug,'product_cat');
        $url  = ($term && !is_wp_error($term)) ? get_term_link($term) : (function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : '#');
        $img_id = $term ? get_term_meta($term->term_id,'thumbnail_id',true) : 0;
      ?>
      <a href="<?php echo esc_url($url); ?>" class="hp-cat-card hp-card">
        <div class="hp-card__media" style="height:420px;">
          <?php if($img_id): echo wp_get_attachment_image($img_id,'large',false,['class'=>'hp-card__img','style'=>'height:420px;','alt'=>$cat['label']]);
          else: ?><div class="hp-card__img hp-card__img--placeholder" style="height:420px;background:var(--hp-cream-card);"></div><?php endif; ?>
          <div class="hp-cat-card__overlay"></div>
          <div class="hp-cat-card__caption">
            <h3 class="hp-cat-card__title"><?php echo esc_html($cat['label']); ?></h3>
            <div class="hp-cat-card__blurb"><?php echo esc_html($cat['blurb']); ?> &rarr;</div>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- BESTSELLERS -->
<section class="hp-section hp-bestsellers" aria-label="<?php esc_attr_e('Bestsellers','herbalpearls'); ?>">
  <div class="hp-container">
    <div class="hp-section-head" style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:44px;">
      <div>
        <div class="hp-eyebrow" style="margin-bottom:14px;"><?php esc_html_e('Most Loved','herbalpearls'); ?></div>
        <h2 class="hp-display hp-h2"><?php esc_html_e('Bestsellers','herbalpearls'); ?></h2>
      </div>
      <a href="<?php echo esc_url(function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : '#'); ?>" class="hp-link"><?php esc_html_e('View All →','herbalpearls'); ?></a>
    </div>
    <?php
    $args = [
      'post_type'      => 'product',
      'posts_per_page' => 3,
      'meta_key'       => 'total_sales',
      'orderby'        => 'meta_value_num',
      'order'          => 'DESC',
    ];
    $products = new WP_Query($args);
    if($products->have_posts()):
    ?>
    <div class="hp-grid-3">
      <?php while($products->have_posts()): $products->the_post();
        global $product;
        if(!$product) $product = wc_get_product(get_the_ID());
      ?>
      <div class="hp-card">
        <div class="hp-card__media">
          <a href="<?php the_permalink(); ?>">
            <?php if(has_post_thumbnail()): the_post_thumbnail('woocommerce_single',['class'=>'hp-card__img','alt'=>get_the_title()]);
            else: ?><div class="hp-card__img hp-card__img--placeholder"></div><?php endif; ?>
          </a>
          <?php $pcats = get_the_terms(get_the_ID(),'product_cat'); if($pcats && !is_wp_error($pcats)): ?>
            <span class="hp-card__tag"><?php echo esc_html($pcats[0]->name); ?></span>
          <?php endif; ?>
        </div>
        <div class="hp-card__body">
          <h3 class="hp-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
          <p class="hp-card__desc"><?php echo wp_kses_post(wp_trim_words(get_the_excerpt(),14)); ?></p>
          <div class="hp-card__pricing">
            <?php if($product): ?>
              <span class="hp-price"><?php echo wp_kses_post($product->get_price_html()); ?></span>
            <?php endif; ?>
          </div>
          <?php
          if($product && $product->is_purchasable() && $product->is_in_stock()):
            woocommerce_template_loop_add_to_cart();
          else: ?>
            <a href="<?php the_permalink(); ?>" class="hp-btn hp-btn--ghost hp-btn--block"><?php esc_html_e('View Product','herbalpearls'); ?></a>
          <?php endif; ?>
        </div>
      </div>
      <?php endwhile; wp_reset_postdata(); ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- COMBO BAND (dark green) -->
<section class="hp-combo-band" aria-label="<?php esc_attr_e('Curated combos','herbalpearls'); ?>">
  <div class="hp-combo-band__inner">
    <div class="hp-combo-band__copy">
      <div class="hp-eyebrow" style="color:var(--hp-brass-light);margin-bottom:20px;"><?php esc_html_e('Save More','herbalpearls'); ?></div>
      <h2 class="hp-display hp-combo-band__title"><?php esc_html_e('Curated combos,','herbalpearls'); ?><br><?php esc_html_e('10% lighter on the wallet.','herbalpearls'); ?></h2>
      <p class="hp-combo-band__lead"><?php esc_html_e('Hand-picked rituals that work better together. Pair our De-Tan Cream with the Weight Loss Booster and save on the set.','herbalpearls'); ?></p>
      <a href="<?php echo esc_url(function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : '#'); ?>" class="hp-btn hp-btn--brass"><?php esc_html_e('Shop Combos','herbalpearls'); ?></a>
    </div>
    <?php
    $combo_args = ['post_type'=>'product','posts_per_page'=>2,'meta_key'=>'total_sales','orderby'=>'meta_value_num','order'=>'DESC'];
    $combo_prods = new WP_Query($combo_args);
    if($combo_prods->have_posts()):
    ?>
    <div class="hp-combo-band__imgs">
      <?php $cidx=0; while($combo_prods->have_posts()): $combo_prods->the_post(); ?>
      <div class="hp-combo-band__img-wrap" style="transform:translateY(<?php echo $cidx===0?'-16px':'16px'; ?>);">
        <?php if(has_post_thumbnail()): the_post_thumbnail('woocommerce_single',['class'=>'hp-combo-band__img','alt'=>get_the_title()]);
        else: ?><div class="hp-combo-band__img hp-combo-band__img--placeholder"></div><?php endif; ?>
      </div>
      <?php $cidx++; endwhile; wp_reset_postdata(); ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- INGREDIENTS -->
<section class="hp-section hp-ingredients" id="ingredients" aria-label="<?php esc_attr_e('Key ingredients','herbalpearls'); ?>">
  <div class="hp-container">
    <div class="hp-section-head hp-text-center" style="margin-bottom:54px;">
      <div class="hp-eyebrow" style="margin-bottom:16px;"><?php esc_html_e('Pure &amp; Potent','herbalpearls'); ?></div>
      <h2 class="hp-display hp-h2"><?php esc_html_e('Ingredients That Work','herbalpearls'); ?></h2>
      <p class="hp-ingredients__lead"><?php esc_html_e('Every ingredient is chosen for a reason — never filler.','herbalpearls'); ?></p>
    </div>
    <div class="hp-ing-grid">
      <?php
      $ingredients = [
        ['no'=>1,'name'=>__('Rosemary','herbalpearls'),'desc'=>__('Stimulates hair follicles and improves scalp circulation for visibly thicker, healthier hair.','herbalpearls')],
        ['no'=>2,'name'=>__('Niacinamide','herbalpearls'),'desc'=>__('Brightens skin tone, minimizes pores, and strengthens the skin barrier — the multi-tasker every routine needs.','herbalpearls')],
        ['no'=>3,'name'=>__('Kojic Acid','herbalpearls'),'desc'=>__('A natural alternative for reducing dark spots and hyperpigmentation, derived from fermented rice.','herbalpearls')],
        ['no'=>4,'name'=>__('Aloe Vera','herbalpearls'),'desc'=>__('Deeply hydrates, soothes inflammation, and accelerates skin healing — the foundation of our formulas.','herbalpearls')],
      ];
      foreach($ingredients as $ing): ?>
      <div class="hp-ing-card">
        <div class="hp-ing-card__no">0<?php echo esc_html($ing['no']); ?></div>
        <h3 class="hp-ing-card__name"><?php echo esc_html($ing['name']); ?></h3>
        <p class="hp-ing-card__desc"><?php echo esc_html($ing['desc']); ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- PROMISE ICONS -->
<section class="hp-promise" aria-label="<?php esc_attr_e('Our promise','herbalpearls'); ?>">
  <div class="hp-container">
    <div class="hp-promise-grid">
      <?php
      $promises = [
        ['icon'=>'&#127807;','title'=>__('100% Natural','herbalpearls'),'sub'=>__('Every ingredient, botanically sourced','herbalpearls')],
        ['icon'=>'&#10022;','title'=>__('No Parabens','herbalpearls'),'sub'=>__('Free from harmful preservatives','herbalpearls')],
        ['icon'=>'&#9825;','title'=>__('Cruelty Free','herbalpearls'),'sub'=>__('Never tested on animals','herbalpearls')],
        ['icon'=>'&#9688;','title'=>__('Small Batch','herbalpearls'),'sub'=>__('Made fresh in India','herbalpearls')],
      ];
      foreach($promises as $pr): ?>
      <div class="hp-promise-item">
        <div class="hp-promise-item__icon"><?php echo $pr['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
        <div class="hp-promise-item__title"><?php echo esc_html($pr['title']); ?></div>
        <div class="hp-promise-item__sub"><?php echo esc_html($pr['sub']); ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- NEWSLETTER -->
<section class="hp-newsletter" aria-label="<?php esc_attr_e('Newsletter signup','herbalpearls'); ?>">
  <div class="hp-newsletter__inner">
    <div class="hp-eyebrow" style="margin-bottom:16px;"><?php esc_html_e('Stay in Touch','herbalpearls'); ?></div>
    <h2 class="hp-display" style="font-size:2.625rem;margin-bottom:14px;"><?php esc_html_e('Get 5% off your first order','herbalpearls'); ?></h2>
    <p class="hp-newsletter__lead"><?php esc_html_e('Skincare rituals, new launches, and quiet offers. No spam, ever.','herbalpearls'); ?></p>
    <form class="hp-newsletter__form" method="post" action="#">
      <?php wp_nonce_field('hp_newsletter','hp_newsletter_nonce'); ?>
      <input type="email" name="email" class="hp-newsletter__input" placeholder="<?php esc_attr_e('Your email address','herbalpearls'); ?>" required aria-label="<?php esc_attr_e('Email address','herbalpearls'); ?>">
      <button type="submit" class="hp-btn hp-btn--primary"><?php esc_html_e('Subscribe','herbalpearls'); ?></button>
    </form>
  </div>
</section>

<?php get_footer(); ?>
