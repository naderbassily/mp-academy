<?php
/**
 * The header for MP Academy theme
 * @package MP_Academy
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Franklin Design System -->
<link rel="stylesheet" href="https://unpkg.com/mp-design-system@latest/dist/build/scss/main.css" />
<script src="https://unpkg.com/mp-design-system@latest/dist/build/js/app.js" defer></script>
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'mp-academy' ); ?></a>


<header class="mp c-header" id="header">
  <div class="c-header__wrap u-wrap">

    <!-- Logo -->
    <a class="c-header__logo" href="<?php echo esc_url(home_url('/')); ?>" rel="home" aria-label="Homepage">
  <?php
    $svg_path = get_template_directory() . '/assets/images/malvernpanalyticallogo.svg';
    if (file_exists($svg_path)) {
        echo file_get_contents($svg_path);
    } else {
        echo '<img src="' . get_template_directory_uri() . '/assets/images/logo-fallback.png" alt="Logo">';
    }
  ?>
</a>


    <!-- Mobile Nav Icon (Open) -->
    <a class="c-navicon" href="#nav" aria-label="Open Navigation">
      <i></i><i></i><i></i>
    </a>

    <!-- Main Navigation -->
    <div class="c-header__primary" id="nav">

      <!-- Mobile Nav Icon (Close) -->
      <a class="c-navicon" href="#header" aria-label="Close Navigation">
        <i></i><i></i><i></i>
      </a>

      <!-- Corporate Nav (Static Items) -->
      <nav class="c-header__corporate" aria-label="Corporate">
        <ul>
          <li>
            <div class="c-input__wrap">
              <label class="u-hidden" for="Language">Language</label>
              <select class="c-input c-input--select" id="Language">
                <option>Deutsch</option>
                <option selected>English</option>
                <option>Español</option>
                <option>Français</option>
                <option>Português</option>
                <option>Pусский</option>
                <option>한국어</option>
                <option>日本語</option>
                <option>简体中文</option>
              </select>
            </div>
          </li>
          <li><a href="#">About us</a></li>
          <li><a href="#">Careers</a></li>
          <li><a href="#">Blog</a></li>
          <li><a href="#">Contact us</a></li>
        </ul>
      </nav>

      <!-- Search -->
      <form class="c-header__search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
        <label for="site-search" class="u-hidden">Search</label>
        <div class="u-flex u-bg-petrol-step-3">
          <input id="site-search" type="search" name="s" placeholder="Search" class="c-input c-input--alt c-input--with-button">
          <button type="submit" class="c-button--reset u-blue">
            <svg role="img" aria-hidden="true" focusable="false" class="mp c-icon c-icon--search">
              <use xlink:href="/static/svg/sprite.svg#search"></use>
            </svg>
          </button>
        </div>
      </form>

      <!-- Site Nav (Dynamic WordPress Menu) -->
      <nav class="c-header__site" aria-label="Site">
        <?php
        wp_nav_menu(array(
          'theme_location' => 'menu-1',
          'menu_class'     => '',
          'container'      => false,
          'items_wrap'     => '<ul>%3$s</ul>',
          'walker'         => new Franklin_Menu_Walker()
        ));
        ?>
      </nav>

    </div><!-- /c-header__primary -->
  </div><!-- /c-header__wrap -->
</header>
