<?php
/**
 * MP Academy functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * @package MP_Academy
 */

if ( ! defined( '_S_VERSION' ) ) {
  define( '_S_VERSION', '1.0.0' );
}

/**
 * Theme setup
 */
function mp_academy_setup() {
  // Make theme available for translation.
  load_theme_textdomain( 'mp-academy', get_template_directory() . '/languages' );

  // Add default posts and comments RSS feed links to head.
  add_theme_support( 'automatic-feed-links' );

  // Let WordPress manage the document title.
  add_theme_support( 'title-tag' );

  // Enable support for Post Thumbnails on posts and pages.
  add_theme_support( 'post-thumbnails' );

  // Register navigation menus.
  register_nav_menus( array(
    'menu-1'         => esc_html__( 'Primary', 'mp-academy' ),
    'footer-popular' => esc_html__( 'Footer - Popular Links', 'mp-academy' ),
    'footer-support' => esc_html__( 'Footer - Support and Services', 'mp-academy' ),
    'footer-company' => esc_html__( 'Footer - Company Profile', 'mp-academy' ),
    'footer-legal'   => esc_html__( 'Footer - Legal Information', 'mp-academy' ),
  ) );

  // HTML5 support.
  add_theme_support( 'html5', array(
    'search-form',
    'comment-form',
    'comment-list',
    'gallery',
    'caption',
    'style',
    'script',
  ) );

  // Custom background.
  add_theme_support( 'custom-background', apply_filters( 'mp_academy_custom_background_args', array(
    'default-color' => 'ffffff',
    'default-image' => '',
  ) ) );

  // Selective refresh for widgets.
  add_theme_support( 'customize-selective-refresh-widgets' );
}
add_action( 'after_setup_theme', 'mp_academy_setup' );

/**
 * Set the content width.
 */
function mp_academy_content_width() {
  $GLOBALS['content_width'] = apply_filters( 'mp_academy_content_width', 640 );
}
add_action( 'after_setup_theme', 'mp_academy_content_width', 0 );

/**
 * Register widget area.
 */
function mp_academy_widgets_init() {
  register_sidebar( array(
    'name'          => esc_html__( 'Sidebar', 'mp-academy' ),
    'id'            => 'sidebar-1',
    'description'   => esc_html__( 'Add widgets here.', 'mp-academy' ),
    'before_widget' => '<section id="%1$s" class="widget %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<h2 class="widget-title">',
    'after_title'   => '</h2>',
  ) );
}
add_action( 'widgets_init', 'mp_academy_widgets_init' );

/**
 * Enqueue theme scripts and styles (Single unified function)
 */
function mp_academy_scripts() {

  // ---------- Base styles ----------
  wp_enqueue_style(
    'mp-academy-style',
    get_stylesheet_uri(),
    [],
    _S_VERSION
  );
  wp_style_add_data( 'mp-academy-style', 'rtl', 'replace' );

  // ---------- Fonts ----------
  wp_enqueue_style(
    'mp-inter-font',
    get_template_directory_uri() . '/assets/css/inter-font.css',
    [],
    null
  );

  // ---------- Franklin or global design system ----------
  // Uncomment if Franklin has a CDN or compiled file
  // wp_enqueue_style('franklin', 'https://unpkg.com/mp-design-system@latest/dist/build/scss/main.css', [], null);

  // ---------- Page-specific ----------
  if ( is_front_page() || is_page_template('templates/template-academy-home.php') ) {
    $path = get_template_directory() . '/assets/css/home.css';
    wp_enqueue_style(
      'mp-academy-home',
      get_template_directory_uri() . '/assets/css/home.css',
      ['mp-inter-font'],
      file_exists($path) ? filemtime($path) : _S_VERSION
    );
  }

  // ---------- Custom modules (add all here) ----------
  $custom_styles = [
    'my-courses-grid' => '/assets/css/my-courses-grid.css',
    'my-courses'      => '/assets/css/my-courses.css',
    // Add more CSS files easily here later
  ];

  foreach ( $custom_styles as $handle => $relative_path ) {
    $full_path = get_template_directory() . $relative_path;
    if ( file_exists( $full_path ) ) {
      wp_enqueue_style(
        'mp-' . $handle,
        get_template_directory_uri() . $relative_path,
        ['mp-inter-font'],
        filemtime( $full_path )
      );
    }
  }

  // ---------- JS ----------
  wp_enqueue_script(
    'mp-academy-navigation',
    get_template_directory_uri() . '/js/navigation.js',
    [],
    _S_VERSION,
    true
  );

  // ---------- Comments ----------
  if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
    wp_enqueue_script( 'comment-reply' );
  }
}
add_action( 'wp_enqueue_scripts', 'mp_academy_scripts' );

/**
 * Include additional files.
 */
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/customizer.php';
if ( defined( 'JETPACK__VERSION' ) ) {
  require get_template_directory() . '/inc/jetpack.php';
}
// Single Course CSS (LearnDash)
add_action('wp_enqueue_scripts', function () {
  // Adjust path if your CSS lives elsewhere
  $css = get_stylesheet_directory_uri() . '/assets/css/single-course.css';
  wp_enqueue_style('mp-single-course', $css, [], null);
});

/**
 * Minimal Franklin menu walker (placeholder hooks, extend when needed).
 */
class Franklin_Menu_Walker extends Walker_Nav_Menu {
  public function start_lvl( &$output, $depth = 0, $args = null ) {}
  public function end_lvl( &$output, $depth = 0, $args = null ) {}
}

/**
 * Allow SVG uploads.
 */
function mp_allow_svg_uploads( $mimes ) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter( 'upload_mimes', 'mp_allow_svg_uploads' );

/**
 * (Optional) Honor LearnDash course category in search results.
 */
function mp_academy_search_ld_category( $query ) {
  if ( is_admin() || ! $query->is_main_query() ) return;

  if ( $query->is_search() && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'sfwd-courses' ) {
    $taxes = [ 'ld_course_category', 'course_category' ];
    foreach ( $taxes as $tax ) {
      if ( taxonomy_exists( $tax ) && ! empty( $_GET[ $tax ] ) ) {
        $query->set( 'tax_query', [
          [
            'taxonomy' => $tax,
            'field'    => 'slug',
            'terms'    => sanitize_text_field( wp_unslash( $_GET[ $tax ] ) ),
          ],
        ] );
        break;
      }
    }
  }
}
add_action( 'pre_get_posts', 'mp_academy_search_ld_category' );

/**
 * Force WordPress authentication cookies to expire after 30 minutes
 */
add_filter( 'auth_cookie_expiration', function( $seconds, $user_id, $remember ) {
    return 30 * 60; // 30 minutes
}, 10, 3 );
