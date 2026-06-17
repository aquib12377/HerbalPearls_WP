<?php if(!defined('ABSPATH'))exit; ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,500&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
<?php wp_head(); ?>
</head>
<body <?php body_class('hp-store'); ?>>
<?php wp_body_open(); ?>
<a class="hp-skip-link screen-reader-text" href="#main"><?php esc_html_e('Skip to content','herbalpearls'); ?></a>

<!-- ANNOUNCEMENT BAR -->
<div class="hp-announcement" role="complementary">
  <?php esc_html_e('FREE shipping above ₹499 · First order 5% OFF — code WELCOME5','herbalpearls'); ?>
</div>

<!-- HEADER -->
<header id="masthead" class="hp-header" role="banner">
  <div class="hp-header__inner">
    <!-- Left: burger + desktop nav -->
    <div style="display:flex;align-items:center;gap:26px;">
      <button type="button" class="hp-burger hp-menu-toggle" aria-label="<?php esc_attr_e('Open menu','herbalpearls'); ?>" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
      <nav class="hp-nav" aria-label="<?php esc_attr_e('Primary navigation','herbalpearls'); ?>">
        <?php if(has_nav_menu('primary')): ?>
          <?php wp_nav_menu(['theme_location'=>'primary','container'=>false,'fallback_cb'=>false,'depth'=>1,'menu_class'=>'hp-nav__list']); ?>
        <?php else: ?>
          <ul class="hp-nav__list">
            <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home','herbalpearls'); ?></a></li>
            <li><a href="<?php echo esc_url(function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : '#'); ?>"><?php esc_html_e('Shop','herbalpearls'); ?></a></li>
            <li><a href="#"><?php esc_html_e('Journal','herbalpearls'); ?></a></li>
            <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('contact'))); ?>"><?php esc_html_e('Contact','herbalpearls'); ?></a></li>
          </ul>
        <?php endif; ?>
      </nav>
    </div>
    <!-- Center: logo -->
    <div class="hp-header__logo">
      <a href="<?php echo esc_url(home_url('/')); ?>" rel="home" aria-label="<?php bloginfo('name'); ?>">
        <?php if(has_custom_logo()): the_custom_logo();
        else: ?>
          <span class="hp-header__site-title"><?php bloginfo('name'); ?></span>
        <?php endif; ?>
      </a>
    </div>
    <!-- Right: account + cart -->
    <div class="hp-header__actions">
      <a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>" class="hp-header__action-link" aria-label="<?php esc_attr_e('Account','herbalpearls'); ?>"><?php esc_html_e('Account','herbalpearls'); ?></a>
      <?php if(function_exists('WC')): ?>
        <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="hp-header__action-link hp-header__cart-link" aria-label="<?php esc_attr_e('Cart','herbalpearls'); ?>">
          <?php esc_html_e('Cart','herbalpearls'); ?>
          <span class="hp-header__cart-count" data-cart-count><?php echo esc_html(WC()->cart->get_cart_contents_count()); ?></span>
        </a>
      <?php endif; ?>
    </div>
  </div>
</header>

<!-- MOBILE NAV DRAWER -->
<div class="hp-drawer" id="mobile-menu" aria-hidden="true">
  <div class="hp-drawer__panel">
    <div class="hp-drawer__top">
      <?php if(has_custom_logo()): the_custom_logo();
      else: ?>
        <span class="hp-header__site-title"><?php bloginfo('name'); ?></span>
      <?php endif; ?>
      <button type="button" class="hp-drawer__close hp-menu-close" aria-label="<?php esc_attr_e('Close menu','herbalpearls'); ?>">&#215;</button>
    </div>
    <nav>
      <ul class="hp-drawer__nav">
        <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home','herbalpearls'); ?></a></li>
        <?php if(function_exists('wc_get_page_id')): ?>
        <li><a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>"><?php esc_html_e('Shop','herbalpearls'); ?></a></li>
        <?php endif; ?>
        <li><a href="#"><?php esc_html_e('Journal','herbalpearls'); ?></a></li>
        <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('contact'))); ?>"><?php esc_html_e('Contact','herbalpearls'); ?></a></li>
        <?php if(function_exists('wc_get_cart_url')): ?>
        <li><a href="<?php echo esc_url(wc_get_cart_url()); ?>" style="display:flex;align-items:center;gap:10px;"><?php esc_html_e('Cart','herbalpearls'); ?> <?php if(function_exists('WC')): ?><span class="hp-header__cart-count"><?php echo esc_html(WC()->cart->get_cart_contents_count()); ?></span><?php endif; ?></a></li>
        <?php endif; ?>
      </ul>
    </nav>
    <a href="https://wa.me/910000000000" target="_blank" rel="noopener noreferrer" class="hp-btn hp-btn--whatsapp" style="margin-top:auto;"><?php esc_html_e('Order on WhatsApp →','herbalpearls'); ?></a>
  </div>
</div>
<div class="hp-drawer__overlay" id="mobile-menu-overlay" aria-hidden="true"></div>

<main id="main">
