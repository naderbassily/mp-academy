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
 * Enqueue theme scripts and styles
 * CONSOLIDATED: All asset enqueuing in one place
 */
function mp_academy_scripts() {

  // ---------- Base Styles ----------
  // Single Video page (CPT: video)
  if ( is_singular('video') ) {
    $path = get_template_directory() . '/assets/css/single-video.css';
    if ( file_exists( $path ) ) {
      wp_enqueue_style(
        'mp-single-video',
        get_template_directory_uri() . '/assets/css/single-video.css',
        ['mp-inter-font'],
        filemtime( $path )
      );
    }
  }

  // Videos Library page (template)
  if ( is_page_template('page-videos-library.php') ) {
    $path = get_template_directory() . '/assets/css/videos-library.css';
    if ( file_exists( $path ) ) {
      wp_enqueue_style(
        'mp-videos-library',
        get_template_directory_uri() . '/assets/css/videos-library.css',
        ['mp-inter-font'],
        filemtime( $path )
      );
    }
  }

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
    filemtime( get_template_directory() . '/assets/css/inter-font.css' )
  );

  // ---------- Page-Specific Styles ----------
  
  // Home page
  if ( is_front_page() || is_page_template('templates/template-academy-home.php') ) {
    $path = get_template_directory() . '/assets/css/home.css';
    if ( file_exists( $path ) ) {
      wp_enqueue_style(
        'mp-academy-home',
        get_template_directory_uri() . '/assets/css/home.css',
        ['mp-inter-font'],
        filemtime( $path )
      );
    }
  }


  // All Courses archive page
  if ( is_post_type_archive( 'sfwd-courses' ) ) {
    // CSS
    $css_path = get_template_directory() . '/assets/css/all-courses.css';
    if ( file_exists( $css_path ) ) {
      wp_enqueue_style(
        'mp-all-courses',
        get_template_directory_uri() . '/assets/css/all-courses.css',
        ['mp-inter-font'],
        filemtime( $css_path )
      );
    }

    // JS
    $js_path = get_template_directory() . '/assets/js/all-courses.js';
    if ( file_exists( $js_path ) ) {
      wp_enqueue_script(
        'mp-all-courses',
        get_template_directory_uri() . '/assets/js/all-courses.js',
        [],
        filemtime( $js_path ),
        true
      );
    }
  }

  // Single Topic / Lesson
  if ( is_singular( 'sfwd-topic' ) || is_singular( 'sfwd-lessons' ) ) {
    $topic_css = get_template_directory() . '/assets/css/single-topic.css';
    if ( file_exists( $topic_css ) ) {
      wp_enqueue_style( 'mp-single-topic', get_template_directory_uri() . '/assets/css/single-topic.css', ['mp-inter-font'], filemtime( $topic_css ) );
    }

    $topic_js = get_template_directory() . '/assets/js/single-topic.js';
    if ( file_exists( $topic_js ) ) {
      wp_enqueue_script( 'mp-single-topic-js', get_template_directory_uri() . '/assets/js/single-topic.js', ['jquery'], filemtime( $topic_js ), true );
    }
  }

  // Category icons (Home + All Courses)
  if ( is_front_page() || is_post_type_archive( 'sfwd-courses' ) ) {
    $path = get_template_directory() . '/assets/css/categories.css';
    if ( file_exists( $path ) ) {
      wp_enqueue_style(
        'mp-categories',
        get_template_directory_uri() . '/assets/css/categories.css',
        ['mp-inter-font'],
        filemtime( $path )
      );
    }
  }

  // ---------- Global Component Styles ----------
  
  // My Courses components (used on multiple pages)
  $component_styles = [
    'my-courses-grid' => '/assets/css/my-courses-grid.css',
    'my-courses'      => '/assets/css/my-courses.css',
  ];

  foreach ( $component_styles as $handle => $relative_path ) {
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

  // ---------- JavaScript ----------
  
  // Navigation (FIXED PATH)
  $nav_path = get_template_directory() . '/assets/js/navigation.js';
  if ( file_exists( $nav_path ) ) {
    wp_enqueue_script(
      'mp-academy-navigation',
      get_template_directory_uri() . '/assets/js/navigation.js',
      [],
      filemtime( $nav_path ),
      true
    );
  }

  // Comments (only if needed)
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
add_filter( 'learndash_course_completion_url', 'mp_academy_course_completion_url', 10, 3 );

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
add_action( 'pre_get_posts', 'mp_academy_search_ld_category' );

// Force LearnDash to enqueue video scripts on topic pages
add_action('wp_enqueue_scripts', function() {
    if (is_singular('sfwd-topic')) {
        wp_enqueue_script('learndash-video');
        wp_enqueue_style('learndash-video');
        
        // Also enqueue the main LearnDash script
        if (file_exists(LEARNDASH_LMS_PLUGIN_DIR . 'assets/js/learndash.js')) {
            wp_enqueue_script('learndash');
        }
    }
}, 999);

