<?php
/**
 * MP Academy functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * @package MP_Academy
 */

if ( ! defined( 'MP_ACADEMY_VERSION' ) ) {
  define( 'MP_ACADEMY_VERSION', '1.0.0' );
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
  }

  if ( is_front_page() ) {
    mp_academy_enqueue_theme_style( 'mp-academy-home', '/assets/css/home.css', array( 'mp-inter-font' ) );
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
  if ( ! is_singular( 'sfwd-topic' ) ) {
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
