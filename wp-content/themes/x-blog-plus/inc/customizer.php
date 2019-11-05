<?php
/**
 * eyepress Theme Customizer
 *
 * @package eyepress
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */



function x_blog_plus_customize_register( $wp_customize ) {

    $wp_customize->add_setting('xblog_plus_top_address', array(
        'default'        => esc_html__('Welcome','x-blog-plus'),
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('xblog_plus_top_address_control', array(
        'label'      => __('Top bar text', 'x-blog-plus'),
        'description'     => __('Enter top bar text here.', 'x-blog-plus'),
        'section'    => 'x_blog_options',
        'settings'   => 'xblog_plus_top_address',
        'type'       => 'text',
    ));
    $wp_customize->add_setting('xblog_plus_grid_height', array(
        'default'        => 750,
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
        'priority'       => 10,
    ));
    $wp_customize->add_control('xblog_plus_grid_height_control', array(
        'label'      => __('Set grid minimum height', 'x-blog-plus'),
        'description'     => __('You can reduce or increase the grid minimum height..', 'x-blog-plus'),
        'section'    => 'x_blog_options',
        'settings'   => 'xblog_plus_grid_height',
        'type'       => 'text',

    ));


}
add_action( 'customize_register', 'x_blog_plus_customize_register',99 );

