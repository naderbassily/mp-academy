<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package MP_Academy
 */

?>

<footer class="mp c-footer" role="contentinfo">
  <nav class="c-footer__primary u-wrap" aria-label="Footer">
    <ul class="c-footer__sections">
      <li>
        <h4 class="c-h c-h--step--1 c-footer__subtitle">Popular links</h4>
        <?php
          wp_nav_menu(array(
            'theme_location' => 'footer-popular',
            'container'      => false,
            'items_wrap'     => '<ul>%3$s</ul>',
          ));
        ?>
      </li>
      <li>
        <h4 class="c-h c-h--step--1 c-footer__subtitle">Support and services</h4>
        <?php
          wp_nav_menu(array(
            'theme_location' => 'footer-support',
            'container'      => false,
            'items_wrap'     => '<ul>%3$s</ul>',
          ));
        ?>
      </li>
      <li>
        <h4 class="c-h c-h--step--1 c-footer__subtitle">Company profile</h4>
        <?php
          wp_nav_menu(array(
            'theme_location' => 'footer-company',
            'container'      => false,
            'items_wrap'     => '<ul>%3$s</ul>',
          ));
        ?>
      </li>
      <li>
        <h4 class="c-h c-h--step--1 c-footer__subtitle">Legal information</h4>
        <?php
          wp_nav_menu(array(
            'theme_location' => 'footer-legal',
            'container'      => false,
            'items_wrap'     => '<ul>%3$s</ul>',
          ));
        ?>
      </li>
    </ul>

    <div class="c-footer__identity">
      <a href="/" class="c-footer__logo">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/malvernpanalyticallogo.svg" alt="Home" width="162" />
      </a>
      <ul class="c-footer__social">
        <!-- Keep as-is for now, we can ACF or menu these later -->
        <li><a href="#"><svg class="mp c-icon c-icon--facebook"><use xlink:href="<?php echo esc_url( mp_academy_get_sprite_url() ); ?>#facebook"></use></svg></a></li>
        <li><a href="#"><svg class="mp c-icon c-icon--twitter"><use xlink:href="<?php echo esc_url( mp_academy_get_sprite_url() ); ?>#twitter"></use></svg></a></li>
        <li><a href="#"><svg class="mp c-icon c-icon--rss"><use xlink:href="<?php echo esc_url( mp_academy_get_sprite_url() ); ?>#rss"></use></svg></a></li>
        <li><a href="#"><svg class="mp c-icon c-icon--instagram"><use xlink:href="<?php echo esc_url( mp_academy_get_sprite_url() ); ?>#instagram"></use></svg></a></li>
        <li><a href="#"><svg class="mp c-icon c-icon--linkedin"><use xlink:href="<?php echo esc_url( mp_academy_get_sprite_url() ); ?>#linkedin"></use></svg></a></li>
        <li><a href="#"><svg class="mp c-icon c-icon--youtube"><use xlink:href="<?php echo esc_url( mp_academy_get_sprite_url() ); ?>#youtube"></use></svg></a></li>
      </ul>
    </div>
  </nav>

  <div class="c-footer__secondary">
    <div class="u-wrap">
      <ul class="c-footer__h-links">
        <li><a href="#">Website feedback</a></li>
        <li><a href="#">Site map</a></li>
        <li><a href="#">Cookie settings</a></li>
      </ul>
      <span>&copy; <?php echo date('Y'); ?> Malvern Panalytical Ltd is a <a href="https://www.spectris.com/">Spectris</a> company</span>
    </div>
  </div>
</footer>

<a href="#header" class="mp c-back-to-top" title="">
  <svg class="mp c-icon c-icon--chevron-up c-back-to-top__icon">
    <use xlink:href="<?php echo esc_url( mp_academy_get_sprite_url() ); ?>#chevron-up"></use>
  </svg>
</a>

<?php wp_footer(); ?>

</body>
</html>
