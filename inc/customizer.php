<?php
/**
 * MP Academy Theme Customizer
 *
 * @package MP_Academy
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function mp_academy_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '.site-title a',
				'render_callback' => 'mp_academy_customize_partial_blogname',
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'blogdescription',
			array(
				'selector'        => '.site-description',
				'render_callback' => 'mp_academy_customize_partial_blogdescription',
			)
		);
	}
}
add_action( 'customize_register', 'mp_academy_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function mp_academy_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function mp_academy_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function mp_academy_customize_preview_js() {
	$asset_path = get_template_directory() . '/assets/js/customizer.js';

	if ( ! file_exists( $asset_path ) ) {
		return;
	}

	wp_enqueue_script(
		'mp-academy-customizer',
		get_template_directory_uri() . '/assets/js/customizer.js',
		array( 'customize-preview' ),
		filemtime( $asset_path ),
		true
	);
}
add_action( 'customize_preview_init', 'mp_academy_customize_preview_js' );
