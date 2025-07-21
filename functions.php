<?php
/**
 * MP Academy functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * @package MP_Academy
 */

// Theme version
if ( ! defined( '_S_VERSION' ) ) {
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Theme setup
 */
function mp_academy_setup() {
	// Translation support
	load_theme_textdomain( 'mp-academy', get_template_directory() . '/languages' );

	// RSS feed links
	add_theme_support( 'automatic-feed-links' );

	// Manage <title> tag via WordPress
	add_theme_support( 'title-tag' );

	// Featured images
	add_theme_support( 'post-thumbnails' );

	// Navigation menu
	register_nav_menus( array(
		'menu-1' => esc_html__( 'Primary', 'mp-academy' ),
	) );

	// HTML5 support
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );

	// Custom background support
	add_theme_support( 'custom-background', apply_filters(
		'mp_academy_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		)
	) );

	// Selective refresh for widgets
	add_theme_support( 'customize-selective-refresh-widgets' );
}
add_action( 'after_setup_theme', 'mp_academy_setup' );

/**
 * Set content width
 */
function mp_academy_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'mp_academy_content_width', 640 );
}
add_action( 'after_setup_theme', 'mp_academy_content_width', 0 );

/**
 * Register widget area
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
 * Enqueue theme scripts and styles
 */
function mp_academy_scripts() {
	wp_enqueue_style( 'mp-academy-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'mp-academy-style', 'rtl', 'replace' );

	wp_enqueue_script( 'mp-academy-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	  // Load Inter font
  wp_enqueue_style(
    'mp-inter-font',
    get_template_directory_uri() . '/assets/css/inter-font.css',
    array(),
    null
  );

  // Theme main styles
  wp_enqueue_style(
    'mp-academy-style',
    get_stylesheet_uri(),
    array('mp-inter-font'),
    _S_VERSION
  );

  wp_style_add_data('mp-academy-style', 'rtl', 'replace');

  wp_enqueue_script(
    'mp-academy-navigation',
    get_template_directory_uri() . '/js/navigation.js',
    array(),
    _S_VERSION,
    true
  );

  if (is_singular() && comments_open() && get_option('thread_comments')) {
    wp_enqueue_script('comment-reply');
  }
}


add_action( 'wp_enqueue_scripts', 'mp_academy_scripts' );

/**
 * Load template tags, functions, and customizer settings
 */
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/customizer.php';

/**
 * Jetpack compatibility
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Custom Walker for Franklin Design System Menu
 */
class Franklin_Menu_Walker extends Walker_Nav_Menu {
	function start_lvl( &$output, $depth = 0, $args = null ) {}
	function end_lvl( &$output, $depth = 0, $args = null ) {}
}

/**
 * Allow SVG uploads
 */
function mp_allow_svg_uploads( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter( 'upload_mimes', 'mp_allow_svg_uploads' );
// Register navigation menus - footer menus
 register_nav_menus( array(
	'menu-1'            => esc_html__( 'Primary', 'mp-academy' ),
	'footer-popular'    => esc_html__( 'Footer - Popular Links', 'mp-academy' ),
	'footer-support'    => esc_html__( 'Footer - Support and Services', 'mp-academy' ),
	'footer-company'    => esc_html__( 'Footer - Company Profile', 'mp-academy' ),
	'footer-legal'      => esc_html__( 'Footer - Legal Information', 'mp-academy' ),
) );