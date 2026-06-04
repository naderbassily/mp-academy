<?php
/**
 * MP Academy functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * @package MP_Academy
 */

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

if ( ! defined( 'MP_ACADEMY_VERSION' ) ) {
  define( 'MP_ACADEMY_VERSION', '1.0.10' );
}

/**
 * Read a GitHub token for private repository update checks.
 *
 * Prefer defining MP_ACADEMY_GITHUB_TOKEN in wp-config.php. Environment
 * variables are also supported for hosts that manage secrets outside PHP files.
 *
 * @return string
 */
function mp_academy_get_github_update_token() {
  if ( defined( 'MP_ACADEMY_GITHUB_TOKEN' ) && MP_ACADEMY_GITHUB_TOKEN ) {
    return (string) MP_ACADEMY_GITHUB_TOKEN;
  }

  $token = getenv( 'MP_ACADEMY_GITHUB_TOKEN' );

  return $token ? (string) $token : '';
}

/**
 * Initialize GitHub-based theme updates.
 *
 * Plugin Update Checker reads the local theme version from style.css and compares
 * it with GitHub releases, tags, or the configured stable branch.
 */
function mp_academy_init_github_theme_updater() {
  $puc_loader = get_template_directory() . '/inc/plugin-update-checker/plugin-update-checker.php';

  if ( ! file_exists( $puc_loader ) ) {
    return;
  }

  require_once $puc_loader;

  if ( ! class_exists( PucFactory::class ) ) {
    return;
  }

  $update_checker = PucFactory::buildUpdateChecker(
    'https://github.com/naderbassily/mp-academy/',
    __FILE__,
    'mp-academy'
  );

  $update_checker->setBranch( 'main' );

  /*
   * Optional private repository support:
   * Add a GitHub token with read-only repository access in wp-config.php or
   * as an environment variable. Never commit a real token to this repository.
   */
  $github_token = mp_academy_get_github_update_token();
  if ( '' !== $github_token ) {
    $update_checker->setAuthentication( $github_token );
  }
}
mp_academy_init_github_theme_updater();

/**
 * Build the current request URL for SSO referrer handoff.
 *
 * @return string
 */
function mp_academy_get_current_url() {
  $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';
  $host        = isset( $_SERVER['HTTP_HOST'] ) ? wp_unslash( $_SERVER['HTTP_HOST'] ) : wp_parse_url( home_url(), PHP_URL_HOST );
  $scheme      = is_ssl() ? 'https' : 'http';

  return $scheme . '://' . $host . $request_uri;
}

/**
 * Build the Malvern Panalytical registration URL with MP Academy attribution.
 *
 * @return string
 */
function mp_academy_get_register_url() {
  return add_query_arg(
    array(
      'referrer'     => mp_academy_get_current_url(),
      'utm_source'   => 'mp-academy',
      'utm_medium'   => 'header-register',
      'utm_campaign' => 'academy-sso',
    ),
    'https://www.malvernpanalytical.com/en/profile/register'
  );
}

/**
 * Return the theme-bundled SVG sprite URL.
 *
 * @return string
 */
function mp_academy_get_sprite_url() {
  return get_template_directory_uri() . '/assets/images/sprite.svg';
}

/**
 * Enqueue a theme stylesheet only when the file exists.
 *
 * @param string   $handle        Style handle.
 * @param string   $relative_path Theme-relative asset path.
 * @param string[] $deps          Optional dependencies.
 */
function mp_academy_enqueue_theme_style( $handle, $relative_path, $deps = array() ) {
  $full_path = get_template_directory() . $relative_path;

  if ( ! file_exists( $full_path ) ) {
    return;
  }

  wp_enqueue_style(
    $handle,
    get_template_directory_uri() . $relative_path,
    $deps,
    filemtime( $full_path )
  );
}

/**
 * Enqueue a theme script only when the file exists.
 *
 * @param string   $handle        Script handle.
 * @param string   $relative_path Theme-relative asset path.
 * @param string[] $deps          Optional dependencies.
 */
function mp_academy_enqueue_theme_script( $handle, $relative_path, $deps = array() ) {
  $full_path = get_template_directory() . $relative_path;

  if ( ! file_exists( $full_path ) ) {
    return;
  }

  wp_enqueue_script(
    $handle,
    get_template_directory_uri() . $relative_path,
    $deps,
    filemtime( $full_path ),
    true
  );
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
    'corporate-menu' => esc_html__( 'Corporate', 'mp-academy' ),
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
 * Show the front-end admin bar to administrators only.
 *
 * @param bool $show Whether WordPress would show the admin bar.
 * @return bool
 */
function mp_academy_show_admin_bar_for_administrators( $show ) {
  if ( ! is_user_logged_in() ) {
    return false;
  }

  return current_user_can( 'administrator' );
}
add_filter( 'show_admin_bar', 'mp_academy_show_admin_bar_for_administrators' );

/**
 * Set the content width.
 */
function mp_academy_content_width() {
  $GLOBALS['content_width'] = apply_filters( 'mp_academy_content_width', 640 );
}
add_action( 'after_setup_theme', 'mp_academy_content_width', 0 );

/**
 * Enqueue theme scripts and styles
 * CONSOLIDATED: All asset enqueuing in one place
 */
function mp_academy_scripts() {
  wp_enqueue_style( 'mp-academy-style', get_stylesheet_uri(), array(), filemtime( get_stylesheet_directory() . '/style.css' ) );
  wp_style_add_data( 'mp-academy-style', 'rtl', 'replace' );

  mp_academy_enqueue_theme_style( 'mp-inter-font', '/assets/css/inter-font.css' );
  mp_academy_enqueue_theme_style( 'mp-progress-bar', '/assets/css/progress-bar.css' );
  mp_academy_enqueue_theme_style( 'mp-academy-header', '/assets/css/header.css' );
  mp_academy_enqueue_theme_style( 'mp-my-courses-grid', '/assets/css/my-courses-grid.css', array( 'mp-inter-font' ) );
  mp_academy_enqueue_theme_style( 'mp-my-courses', '/assets/css/my-courses.css', array( 'mp-inter-font', 'mp-progress-bar' ) );
  mp_academy_enqueue_theme_script( 'mp-academy-navigation', '/assets/js/navigation.js' );

  if ( is_singular( 'video' ) ) {
    mp_academy_enqueue_theme_style( 'mp-single-video', '/assets/css/single-video.css', array( 'mp-inter-font' ) );
  }

  if ( is_page_template( 'page-videos-library.php' ) ) {
    mp_academy_enqueue_theme_style( 'mp-videos-library', '/assets/css/videos-library.css', array( 'mp-inter-font' ) );
    mp_academy_enqueue_theme_script( 'mp-videos-library', '/assets/js/videos-library.js' );
  }

  if ( is_page_template( 'page-contact-support.php' ) ) {
    mp_academy_enqueue_theme_style( 'mp-contact-support', '/assets/css/contact-support.css', array( 'mp-inter-font' ) );
  }

  if ( is_front_page() ) {
    mp_academy_enqueue_theme_style( 'mp-academy-home', '/assets/css/home.css', array( 'mp-inter-font' ) );
    mp_academy_enqueue_theme_style( 'mp-videos-library', '/assets/css/videos-library.css', array( 'mp-inter-font' ) );
  }

  if ( is_404() ) {
    mp_academy_enqueue_theme_style( 'mp-404', '/assets/css/404.css', array( 'mp-inter-font' ) );
  }

  if ( is_search() ) {
    mp_academy_enqueue_theme_style( 'mp-search-results', '/assets/css/search-results.css', array( 'mp-inter-font' ) );
  }

  if ( is_front_page() || is_post_type_archive( 'sfwd-courses' ) ) {
    mp_academy_enqueue_theme_style( 'mp-categories', '/assets/css/categories.css', array( 'mp-inter-font' ) );
  }

  if ( is_post_type_archive( 'sfwd-courses' ) ) {
    mp_academy_enqueue_theme_style( 'mp-all-courses', '/assets/css/all-courses.css', array( 'mp-inter-font' ) );
    mp_academy_enqueue_theme_script( 'mp-all-courses', '/assets/js/all-courses.js' );
  }

  if ( is_singular( 'sfwd-courses' ) ) {
    mp_academy_enqueue_theme_style( 'mp-single-course', '/assets/css/single-course.css', array( 'mp-inter-font', 'mp-progress-bar' ) );
    mp_academy_enqueue_theme_script( 'mp-single-course-js', '/assets/js/single-course.js' );
  }

  if ( is_singular( 'sfwd-quiz' ) ) {
    mp_academy_enqueue_theme_style( 'mp-single-topic', '/assets/css/single-topic.css', array( 'mp-inter-font' ) );
    mp_academy_enqueue_theme_style( 'mp-single-quiz', '/assets/css/single-quiz.css', array( 'mp-inter-font', 'mp-single-topic' ) );
    mp_academy_enqueue_theme_script( 'mp-single-quiz-js', '/assets/js/single-quiz.js' );

    // Injected last in <head> — beats LearnDash Focus Mode !important overrides
    wp_add_inline_style( 'mp-single-quiz', '
      /* Restore Inter font — Focus Mode stylesheet overrides it globally */
      body.single-sfwd-quiz .site-main,
      body.single-sfwd-quiz .site-main h1,
      body.single-sfwd-quiz .site-main h2,
      body.single-sfwd-quiz .site-main h3,
      body.single-sfwd-quiz .site-main h4,
      body.single-sfwd-quiz .site-main p,
      body.single-sfwd-quiz .site-main a,
      body.single-sfwd-quiz .site-main li,
      body.single-sfwd-quiz .site-main span,
      body.single-sfwd-quiz .site-main input,
      body.single-sfwd-quiz .site-main button,
      body.single-sfwd-quiz .site-main label,
      body.single-sfwd-quiz .learndash-wrapper,
      body.single-sfwd-quiz .wpProQuiz_content,
      body.single-sfwd-quiz .learndash-wrapper * {
        font-family: "Inter", "InterVariable", sans-serif !important;
      }

      /* Start Quiz button — match topic navigation button geometry */
      body.single-sfwd-quiz input[name="startQuiz"],
      body.single-sfwd-quiz .wpProQuiz_button[name="startQuiz"],
      body.single-sfwd-quiz .wpProQuiz_text .wpProQuiz_button2,
      body.single-sfwd-quiz .wpProQuiz_startOnlyRegisteredUser .wpProQuiz_button2 {
        align-items: center !important;
        background: transparent !important;
        background-color: transparent !important;
        background-image: none !important;
        border: 2px solid #00b140 !important;
        border-radius: 0.5rem !important;
        box-sizing: border-box !important;
        color: #00b140 !important;
        display: inline-flex !important;
        font-size: 1rem !important;
        font-weight: 700 !important;
        height: 44px !important;
        justify-content: center !important;
        letter-spacing: 0 !important;
        line-height: 1.2 !important;
        min-width: 170px !important;
        padding: 0.625rem 1.1rem !important;
        box-shadow: none !important;
        outline: none !important;
        text-shadow: none !important;
        cursor: pointer !important;
        text-align: center !important;
        text-decoration: none !important;
        transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease !important;
      }
      body.single-sfwd-quiz input[name="startQuiz"]:hover,
      body.single-sfwd-quiz .wpProQuiz_button[name="startQuiz"]:hover,
      body.single-sfwd-quiz .wpProQuiz_text .wpProQuiz_button2:hover,
      body.single-sfwd-quiz .wpProQuiz_startOnlyRegisteredUser .wpProQuiz_button2:hover,
      body.single-sfwd-quiz input[name="startQuiz"]:focus,
      body.single-sfwd-quiz .wpProQuiz_button[name="startQuiz"]:focus,
      body.single-sfwd-quiz .wpProQuiz_text .wpProQuiz_button2:focus,
      body.single-sfwd-quiz .wpProQuiz_startOnlyRegisteredUser .wpProQuiz_button2:focus {
        background: #00b140 !important;
        background-color: #00b140 !important;
        border: none !important;
        border-color: transparent !important;
        box-shadow: none !important;
        color: #ffffff !important;
        height: 44px !important;
        min-width: 170px !important;
        padding: 0.625rem 1.1rem !important;
      }
    ' );
  }

  if ( is_singular( 'sfwd-topic' ) || is_singular( 'sfwd-lessons' ) ) {
    mp_academy_enqueue_theme_style( 'mp-single-topic', '/assets/css/single-topic.css', array( 'mp-inter-font' ) );
    mp_academy_enqueue_theme_script( 'mp-single-topic-js', '/assets/js/single-topic.js', array( 'jquery' ) );
  }

  if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
    wp_enqueue_script( 'comment-reply' );
  }
}
add_action( 'wp_enqueue_scripts', 'mp_academy_scripts' );

/**
 * Disable public author archives so they use the branded 404 template.
 *
 * @return void
 */
function mp_academy_disable_author_archives() {
  if ( ! is_author() ) {
    return;
  }

  global $wp_query;

  $wp_query->set_404();
  status_header( 404 );
  nocache_headers();
}
add_action( 'template_redirect', 'mp_academy_disable_author_archives' );

/**
 * Keep users on the current topic page after marking it complete.
 *
 * This lets the UI unlock the next-topic button without immediately redirecting.
 *
 * @param bool $redirect_immediately Whether LearnDash should redirect immediately.
 * @return bool
 */
function mp_academy_disable_topic_complete_redirect( $redirect_immediately ) {
  if ( ! is_singular( 'sfwd-topic' ) && ! is_singular( 'sfwd-lessons' ) ) {
    return $redirect_immediately;
  }

  if ( empty( $_POST['sfwd_mark_complete'] ) || empty( $_POST['post'] ) ) {
    return $redirect_immediately;
  }

  $post_id = absint( wp_unslash( $_POST['post'] ) );

  if ( $post_id > 0 && in_array( get_post_type( $post_id ), array( 'sfwd-topic', 'sfwd-lessons' ), true ) ) {
    return false;
  }

  return $redirect_immediately;
}
add_filter( 'learndash_step_completed_redirect_immediately', 'mp_academy_disable_topic_complete_redirect', 20 );

/**
 * Include additional files.
 */
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/menu-walker.php';




/**
 * Allow SVG uploads.
 */
function mp_allow_svg_uploads( $mimes ) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter( 'upload_mimes', 'mp_allow_svg_uploads' );

/**
 * Enable excerpts for LearnDash Topics
 */
function mp_enable_excerpts_for_topics() {
    add_post_type_support( 'sfwd-topic', 'excerpt' );
}
add_action( 'init', 'mp_enable_excerpts_for_topics' );

/**
 * Honor LearnDash course category in search results.
 */
function mp_academy_search_ld_category( $query ) {
  if ( is_admin() || ! $query->is_main_query() ) return;

  if ( $query->is_search() && empty( $_GET['post_type'] ) ) {
    $query->set( 'post_type', [ 'post', 'sfwd-courses', 'video' ] );
  }

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
/**
 * Append the completed course ID to LearnDash completion redirects.
 */
function mp_academy_course_completion_url( $url, $course_id, $page_id ) {
  if ( empty( $url ) || empty( $course_id ) || empty( $page_id ) ) {
    return $url;
  }

  return add_query_arg(
    array(
      'course_completed' => (int) $course_id,
    ),
    $url
  );
}
add_filter( 'learndash_course_completion_url', 'mp_academy_course_completion_url', 10, 3 );
add_action( 'pre_get_posts', 'mp_academy_search_ld_category' );

/**
 * Ensure LearnDash video assets are available on topic pages.
 */
function mp_academy_enqueue_learndash_video_assets() {
  if ( ! is_singular( 'sfwd-topic' ) && ! is_singular( 'sfwd-lessons' ) ) {
    return;
  }

  if ( wp_style_is( 'learndash-video', 'registered' ) ) {
    wp_enqueue_style( 'learndash-video' );
  }

  if ( wp_script_is( 'learndash-video', 'registered' ) ) {
    wp_enqueue_script( 'learndash-video' );
  }

  if ( wp_script_is( 'learndash', 'registered' ) ) {
    wp_enqueue_script( 'learndash' );
  }
}
add_action( 'wp_enqueue_scripts', 'mp_academy_enqueue_learndash_video_assets', 999 );
