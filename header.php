<?php
/**
 * The header for MP Academy theme
 * @package MP_Academy
 */

$mp_academy_host = isset( $_SERVER['HTTP_HOST'] ) ? strtolower( (string) $_SERVER['HTTP_HOST'] ) : '';
$mp_academy_is_local_host = '' !== $mp_academy_host && (
	'localhost' === $mp_academy_host
	|| str_ends_with( $mp_academy_host, '.local' )
	|| str_ends_with( $mp_academy_host, '.test' )
);
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Franklin Design System - Specific Version -->
  <link rel="stylesheet" href="https://unpkg.com/mp-design-system@2.0.82/dist/build/scss/main.css" />
  <link rel="stylesheet" href="https://unpkg.com/mp-design-system@2.0.82/dist/build/scss/mp-www.css" />
  <script src="https://unpkg.com/mp-design-system@2.0.82/dist/build/js/app.js" defer></script>
  <?php if ( ! $mp_academy_is_local_host ) : ?>
    <!-- OneTrust Cookies Consent Notice start for academy.malvernpanalytical.com -->
    <script src="https://cdn.cookielaw.org/scripttemplates/otSDKStub.js" type="text/javascript" charset="UTF-8" data-domain-script="019e0327-d75d-7c5c-b506-5805599bb642"></script>
    <script type="text/javascript">
      function OptanonWrapper() {}
    </script>
    <!-- OneTrust Cookies Consent Notice end for academy.malvernpanalytical.com -->
  <?php endif; ?>

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'mp-academy' ); ?></a>


<header class="c-header" aria-label="Website header">

  <?php
    $is_logged_in = is_user_logged_in();
  ?>

  <!-- TOP BAR -->
  <div class="c-header__primary">
    <nav class="c-navigation c-navigation--corporate" aria-label="Corporate navigation">
      <ul class="c-navigation__list">
        <?php
          wp_nav_menu([
            'theme_location' => 'corporate-menu',
            'container'      => false,
            'items_wrap'     => '%3$s',
            'fallback_cb'    => '__return_empty_string',
            'walker'         => class_exists('Franklin_Menu_Walker') ? new Franklin_Menu_Walker() : '',
          ]);
        ?>
      </ul>
    </nav>

    <div class="c-header__group">
      <?php if (!$is_logged_in) : ?>
        <a class="c-button c-button--blue c-button--small" href="<?php echo esc_url('https://www.malvernpanalytical.com/en/support/login?referrer=' . urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])); ?>">Login</a>
        <a class="c-button c-button--outline-white c-button--small" href="<?php echo esc_url( mp_academy_get_register_url() ); ?>">Register</a>
      <?php else : ?>
        <a class="c-button c-button--outline-white c-button--small" href="<?php echo esc_url(home_url('/logout')); ?>">Logout</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- MAIN HEADER -->
  <div class="c-header__secondary">
    <a class="c-header__logo" href="<?php echo esc_url(home_url('/')); ?>" aria-label="Home">
      <?php
        $svg_path = get_template_directory() . '/assets/images/malvernpanalyticallogo.svg';
        if (file_exists($svg_path)) {
          echo file_get_contents($svg_path);
        } else {
          echo '<img src="' . esc_url(get_template_directory_uri() . '/assets/images/logo-fallback.png') . '" alt="Malvern Panalytical" width="216">';
        }
      ?>
    </a>

    <div class="c-header__group">
      <nav class="c-navigation c-navigation--website" aria-label="Website navigation">
        <ul class="c-navigation__list">
          <?php
            wp_nav_menu([
              'theme_location' => 'menu-1', // or 'menu-1' if you prefer
              'container'      => false,
              'items_wrap'     => '%3$s',
              'fallback_cb'    => '__return_empty_string',
              'walker'         => class_exists('Franklin_Menu_Walker') ? new Franklin_Menu_Walker() : '',
            ]);
          ?>
        </ul>
      </nav>

      <form class="c-header__search c-form c-form--search" action="<?php echo esc_url(home_url('/')); ?>" method="get">
        <div class="c-form__input-wrap">
          <label for="header-site-search" class="u-hidden">Search</label>
          <input id="header-site-search" placeholder="Search" type="search" name="s" class="c-input c-input--alt c-input--with-button">
          <button type="submit" aria-label="Search">
            <svg role="img" aria-hidden="true" focusable="false" class="mp c-icon c-icon--search">
              <use xlink:href="/static/svg/sprite.svg#search"></use>
            </svg>
          </button>
        </div>
      </form>

      <button class="c-navicon c-navicon--open" aria-label="Open navigation" aria-controls="overlay-menu" aria-expanded="false" type="button">
        <i aria-hidden="true"></i><i aria-hidden="true"></i><i aria-hidden="true"></i>
      </button>
    </div>
  </div>

  <!-- MOBILE OVERLAY MENU -->
  <div id="overlay-menu" class="c-header__overlay" aria-hidden="true" aria-modal="true">
    <button class="c-navicon c-navicon--close" aria-label="Close navigation" aria-controls="overlay-menu" type="button">
      <i aria-hidden="true"></i><i aria-hidden="true"></i><i aria-hidden="true"></i>
    </button>

    <nav class="c-navigation c-navigation--website" aria-label="Website navigation">
      <ul class="c-navigation__list">
        <?php
          wp_nav_menu([
            'theme_location' => 'menu-1',
            'container'      => false,
            'items_wrap'     => '%3$s',
            'fallback_cb'    => '__return_empty_string',
            'walker'         => class_exists('Franklin_Menu_Walker') ? new Franklin_Menu_Walker() : '',
          ]);
        ?>
      </ul>
    </nav>

    <nav class="c-navigation c-navigation--corporate" aria-label="Corporate navigation">
      <ul class="c-navigation__list">
        <?php
          wp_nav_menu([
            'theme_location' => 'corporate-menu',
            'container'      => false,
            'items_wrap'     => '%3$s',
            'fallback_cb'    => '__return_empty_string',
            'walker'         => class_exists('Franklin_Menu_Walker') ? new Franklin_Menu_Walker() : '',
          ]);
        ?>
      </ul>
    </nav>

    <form class="c-header__search c-form c-form--search" action="<?php echo esc_url(home_url('/')); ?>" method="get">
      <div class="c-form__input-wrap">
        <label for="header-overlay-site-search" class="u-hidden">Search</label>
        <input id="header-overlay-site-search" placeholder="Search" type="search" name="s" class="c-input c-input--alt c-input--with-button">
        <button type="submit" aria-label="Search">
          <svg role="img" aria-hidden="true" focusable="false" class="mp c-icon c-icon--search">
            <use xlink:href="/static/svg/sprite.svg#search"></use>
          </svg>
        </button>
      </div>
    </form>

    <div class="c-header__group">
      <?php if (!$is_logged_in) : ?>
        <a class="c-button c-button--blue c-button--small" href="<?php echo esc_url('https://www.malvernpanalytical.com/en/support/login?referrer=' . urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])); ?>">Login</a>
        <a class="c-button c-button--outline-white c-button--small" href="<?php echo esc_url( mp_academy_get_register_url() ); ?>">Register</a>
      <?php else : ?>
        <a class="c-button c-button--outline-white c-button--small" href="<?php echo esc_url(home_url('/logout')); ?>">Logout</a>
      <?php endif; ?>
    </div>
  </div>

</header>
