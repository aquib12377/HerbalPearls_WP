<?php if(!defined('ABSPATH'))exit; ?>
</main>

<footer class="hp-footer" role="contentinfo">
  <div class="hp-footer__inner">
    <!-- Brand col -->
    <div class="hp-footer__col hp-footer__col--brand">
      <?php if(has_custom_logo()): ?>
        <div class="hp-footer__logo"><?php the_custom_logo(); ?></div>
      <?php else: ?>
        <div class="hp-footer__logo"><span class="hp-footer__brand-name"><?php bloginfo('name'); ?></span></div>
      <?php endif; ?>
      <p class="hp-footer__tagline"><?php esc_html_e('Healthy &amp; organic herbal care that works — formulated with proven natural ingredients, made in small batches in India.','herbalpearls'); ?></p>
      <a href="https://wa.me/910000000000" target="_blank" rel="noopener noreferrer" class="hp-btn hp-btn--whatsapp"><?php esc_html_e('Order on WhatsApp →','herbalpearls'); ?></a>
    </div>
    <!-- Shop col -->
    <div class="hp-footer__col">
      <h4 class="hp-footer__heading"><?php esc_html_e('Shop','herbalpearls'); ?></h4>
      <?php if(has_nav_menu('footer-1')): wp_nav_menu(['theme_location'=>'footer-1','container'=>false,'fallback_cb'=>false,'depth'=>1,'menu_class'=>'hp-footer__links']); else: ?>
        <ul class="hp-footer__links">
          <?php if(function_exists('wc_get_page_id')): ?>
          <li><a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>"><?php esc_html_e('All Products','herbalpearls'); ?></a></li>
          <?php endif; ?>
          <li><a href="#"><?php esc_html_e('Skin','herbalpearls'); ?></a></li>
          <li><a href="#"><?php esc_html_e('Hair','herbalpearls'); ?></a></li>
          <li><a href="#"><?php esc_html_e('Wellness','herbalpearls'); ?></a></li>
        </ul>
      <?php endif; ?>
    </div>
    <!-- Help col -->
    <div class="hp-footer__col">
      <h4 class="hp-footer__heading"><?php esc_html_e('Help','herbalpearls'); ?></h4>
      <?php if(has_nav_menu('footer-2')): wp_nav_menu(['theme_location'=>'footer-2','container'=>false,'fallback_cb'=>false,'depth'=>1,'menu_class'=>'hp-footer__links']); else: ?>
        <ul class="hp-footer__links">
          <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('contact'))); ?>"><?php esc_html_e('Contact','herbalpearls'); ?></a></li>
          <li><a href="#"><?php esc_html_e('Shipping Policy','herbalpearls'); ?></a></li>
          <li><a href="#"><?php esc_html_e('Refund &amp; Returns','herbalpearls'); ?></a></li>
          <li><a href="#"><?php esc_html_e('Track Order','herbalpearls'); ?></a></li>
        </ul>
      <?php endif; ?>
    </div>
    <!-- Follow col -->
    <div class="hp-footer__col">
      <h4 class="hp-footer__heading"><?php esc_html_e('Follow','herbalpearls'); ?></h4>
      <ul class="hp-footer__links">
        <li><a href="#" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Instagram','herbalpearls'); ?></a></li>
        <li><a href="#" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Facebook','herbalpearls'); ?></a></li>
        <li><a href="#" target="_blank" rel="noopener noreferrer"><?php esc_html_e('YouTube','herbalpearls'); ?></a></li>
      </ul>
    </div>
  </div>
  <div class="hp-footer__bottom">
    <div class="hp-footer__bottom-inner">
      <span>&copy; <?php echo esc_html(gmdate('Y')); ?> <?php bloginfo('name'); ?>. <?php esc_html_e('All rights reserved.','herbalpearls'); ?></span>
      <span><?php esc_html_e('Healthy &amp; Organic','herbalpearls'); ?></span>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
