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
        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/mp-logo-white.svg' ); ?>" alt="Home" width="162" />
      </a>
      <ul class="c-footer__social">
        <li>
          <a href="https://facebook.com/MalvernPanalytical">
            <img src="https://dam.malvernpanalytical.com/57f7ad6e-6556-4d1d-8180-ae9700a67b31/MP%20Facebook%20icon_Original%20file.svg" alt="Facebook" class="c-icon">
          </a>
        </li>
        <li>
          <a href="https://twitter.com/newsfrom_MP">
            <img src="https://dam.malvernpanalytical.com/cdd1f8a0-06d7-4f62-8e6f-b0f200a9e9f3/x-twitter-logo_Original%20file.svg" alt="Twitter" class="c-icon">
          </a>
        </li>
        <li>
          <a href="https://www.malvernpanalytical.com/en/learn/knowledge-center?size=n_20_n&amp;filters%5B0%5D%5Bfield%5D=mp_documenttype_title&amp;filters%5B0%5D%5Bvalues%5D%5B0%5D=Insights&amp;filters%5B0%5D%5Btype%5D=any&amp;sort-field=date&amp;sort-direction=desc">
            <img src="https://dam.malvernpanalytical.com/ebf7b7af-4177-45e2-8388-ae9700a67b6a/MP%20RSS%20blog%20feed%20icon_Original%20file.svg" alt="Blog" class="c-icon">
          </a>
        </li>
        <li>
          <a href="https://www.instagram.com/malvernpanalytical">
            <img src="https://dam.malvernpanalytical.com/41fc66b5-da41-443b-b537-ae9700a67a4c/MP%20instagram%20icon_Original%20file.svg" alt="Instagram" class="c-icon">
          </a>
        </li>
        <li>
          <a href="https://www.linkedin.com/company/malvernpanalytical">
            <img src="https://dam.malvernpanalytical.com/2c4d1560-6d70-401c-802a-ae9700a679e8/MP%20Linkedin%20icon_Original%20file.svg" alt="LinkedIn" class="c-icon">
          </a>
        </li>
        <li>
          <a href="https://www.youtube.com/c/MalvernPanalytical">
            <img src="https://dam.malvernpanalytical.com/fbab6bba-3efb-4f23-86a9-ae9700a67bf2/MP%20Youtube%20icon_Original%20file.svg" alt="Youtube" class="c-icon">
          </a>
        </li>
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

<button type="button" class="mp c-back-to-top" aria-label="<?php esc_attr_e( 'Back to top', 'mp-academy' ); ?>">
  <svg class="mp c-icon c-icon--chevron-up c-back-to-top__icon">
    <use xlink:href="<?php echo esc_url( mp_academy_get_sprite_url() ); ?>#chevron-up"></use>
  </svg>
</button>

<?php wp_footer(); ?>

</body>
</html>
