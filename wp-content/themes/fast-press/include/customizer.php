<?php
// Do not allow direct access to the file.
if(  ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Theme Customizer
 *
 */
add_action( 'customize_register', 'fast_press_customize_register' );
function fast_press_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial( 'blogname', array(
			'selector'        => '.site-title a',
			'render_callback' => 'fast_press_customize_partial_blogname',
		) );
		$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
			'selector'        => '.site-description',
			'render_callback' => 'fast_press_customize_partial_blogdescription',
		) );
	}
  	    $wp_customize->add_setting( 'background_color', array (
			'sanitize_callback' => 'sanitize_text_field',
		) );
 		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'background_color', array(
			'label'    => __( 'Background Color ', 'fast-press' ),
			'section'  => 'fast-press',
			'settings' => 'background_color',
		) ) );
/**
 * Sanitize Functions
 */
	function fast_press_sanitize_checkbox( $input ) {
		if ( $input ) {
			return 1;
		}
		return 0;
	}
	function fast_press_header_sanitize_position( $input ) {
		$valid = array(
			'center' => esc_attr__( 'center center', 'fast-press' ),
			'real' => esc_attr__( 'Real Size (Deactivate the image height.)', 'fast-press' ),
		);
		if ( array_key_exists( $input, $valid ) ) {
			return $input;
		} else {
			return '';
		}
	}
	function fast_press_header_sanitize_show( $input ) {
		$valid = array(
				'all' => esc_attr__( 'All Pages', 'fast-press' ),
				'home' => esc_attr__( 'Home Page', 'fast-press' ),
		);
		if ( array_key_exists( $input, $valid ) ) {
			return $input;
		} else {
			return '';
		}
	}	
/**
 * Header Image
 */	
   	    $wp_customize->add_setting( 'body_background', array (
			'sanitize_callback' => 'sanitize_hex_color',
		) );
 		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'body_background', array(
			'label'    => __( 'Body Background Color', 'fast-press' ),
			'priority' => 14,
			'section'  => 'colors',
			'settings' => 'body_background',
		) ) );
 		$wp_customize->add_setting( 'header_image_show', array (
			'sanitize_callback' => 'fast_press_header_sanitize_show',
		) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'header_image_show', array(
			'label'    => __( 'Activate Header Image', 'fast-press' ),
			'section'  => 'header_image',		
			'settings' => 'header_image_show',
			'type'     =>  'select',
			'priority'    => 1,			
            'choices'  => array(
				'all' => esc_attr__( 'All Pages', 'fast-press' ),
				'home' => esc_attr__( 'Home Page', 'fast-press' ),
            ),
			'default'  => 'all'	
		) ) );
		$wp_customize->add_setting( 'header_image_height', array (
			'sanitize_callback' => 'absint',
		) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'header_image_height', array(
			'section'  => 'header_image',
			'priority'    => 1,
			'settings' => 'header_image_height',
			'label'       => __( 'Image Height', 'fast-press' ),			
			'type'     =>  'number',
			'input_attrs'     => array(
				'min'  => 200,
				'max'  => 1000,
				'step' => 1,
			),
		) ) );
		$wp_customize->add_setting( 'header_image_position', array (
			'sanitize_callback' => 'fast_press_header_sanitize_position',
		) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'header_image_position', array(
			'label'    => __( 'Image Position', 'fast-press' ),
			'section'  => 'header_image',		
			'settings' => 'header_image_position',
			'type'     =>  'select',
			'priority'    => 2,			
            'choices'  => array(
				'center' => esc_attr__( 'Background Cover (center center)', 'fast-press' ),
				'real' => esc_attr__( 'Real Size (Deactivate the image height.)', 'fast-press' ),
            ),
			'default'  => 'real'	
		) ) );
		$wp_customize->add_setting( 'fast_press_header_shadow', array (
            'default' => '',		
			'sanitize_callback' => 'fast_press_sanitize_checkbox',
		) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'fast_press_header_shadow', array(
			'label'    => __( 'Dectivate Image Shadow:', 'fast-press' ),
			'section'  => 'header_image',
			'priority'    => 3,				
			'settings' => 'fast_press_header_shadow',
			'type' => 'checkbox',
		) ) );
		$wp_customize->add_setting( 'fast_press_header_zoom', array (
            'default' => '',		
			'sanitize_callback' => 'fast_press_sanitize_checkbox',
		) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'fast_press_header_zoom', array(
			'label'    => __( 'Dectivate Image Zoom:', 'fast-press' ),
			'section'  => 'header_image',
			'priority'    => 3,				
			'settings' => 'fast_press_header_zoom',
			'type' => 'checkbox',
		) ) );
		$wp_customize->add_setting( 'fast_press_header_animation', array (
            'default' => '',		
			'sanitize_callback' => 'fast_press_sanitize_checkbox',
		) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'fast_press_header_animation', array(
			'label'    => __( 'Dectivate Text Animation:', 'fast-press' ),
			'section'  => 'header_image',
			'priority'    => 3,				
			'settings' => 'fast_press_header_animation',
			'type' => 'checkbox',
		) ) );
		$wp_customize->add_setting( 'fast_press_header_search', array (
            'default' => '',		
			'sanitize_callback' => 'fast_press_sanitize_checkbox',
		) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'fast_press_header_search', array(
			'label'    => __( 'Deactivate Search Icon:', 'fast-press' ),
			'section'  => 'header_image',
			'priority'    => 3,				
			'settings' => 'fast_press_header_search',
			'type' => 'checkbox',
		) ) );
		if( class_exists( 'WooCommerce' ) ) {
			$wp_customize->add_setting( 'fast_press_header_cart', array (
				'default' => '',		
				'sanitize_callback' => 'fast_press_sanitize_checkbox',
			) );
			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'fast_press_header_cart', array(
				'label'    => __( 'Deactivate WooCommerce Cart:', 'fast-press' ),
				'section'  => 'header_image',
				'priority'    => 3,				
				'settings' => 'fast_press_header_cart',
				'type' => 'checkbox',
			) ) );	
		}			
}
/**
 * Custom Font Size Styles
 */ 	
add_action( 'wp_enqueue_scripts', 'fast_press_header_position' );	
function fast_press_header_position() {
        $header_image_height = esc_attr(get_theme_mod( 'header_image_height' ) );
        $header_image_position = esc_attr(get_theme_mod( 'header_image_position' ) );
		if( $header_image_height and $header_image_position != 'real' ) { $header_height = ".header-image {height: {$header_image_height}px !important;}";} else {$header_height ="";}
		if( $header_image_position == 'center' ) { $header_position = ".header-image {background-position: center center !important;}";} else {$header_position ="";}
		if( $header_image_position == '50' ) { $header_position = ".header-image {background-position: 50% 50% !important;}";} else {$header_position ="";}
		if( $header_image_position == 'real' ) { $header_position = ".header-image {background-position: no !important; height: auto;} .site-branding {display:block;}";} else {$header_position ="";}
        wp_add_inline_style( 'fast-style-css', 
		$header_height.$header_position
		);
}
/**
 * Render the site title for the selective refresh partial.
 */
function fast_press_customize_partial_blogname() {
	bloginfo( 'name' );
}
/**
 * Render the site tagline for the selective refresh partial.
 */
function fast_press_customize_partial_blogdescription() {
	bloginfo( 'description' );
}
/**
 * Custom Font Size Styles
 */ 	
function fast_press_customizing_styles() {
        $header_tagline_hide = esc_attr(get_theme_mod( 'header_tagline_hide' ) );
        $fast_press_top_menu_sub_font_size = esc_attr(get_theme_mod( 'fast_press_top_menu_sub_font_size' ) );
        $fast_press_top_menu_font_size = esc_attr(get_theme_mod( 'fast_press_top_menu_font_size' ) );
        $header_image_show = esc_attr(get_theme_mod( 'header_image_show' ) );
        $sidebar_position = esc_attr(get_theme_mod( 'sidebar_position' ) );
        $fast_press_menu_background_color = esc_attr(get_theme_mod( 'fast_press_menu_background_color' ) );
        $fast_press_menu_color = esc_attr(get_theme_mod( 'fast_press_menu_color' ) );
        $fast_press_menu_background_hover_color = esc_attr(get_theme_mod( 'fast_press_menu_background_hover_color' ) );
        $before_background_color = esc_attr(get_theme_mod( 'before_background_color' ) );
        $before_border_color = esc_attr(get_theme_mod( 'before_border_color' ) );
        $fast_press_link_color = esc_attr(get_theme_mod( 'fast_press_link_color' ) );
        $fast_press_link_hover_color = esc_attr(get_theme_mod( 'fast_press_link_hover_color' ) );
        $body_background = esc_attr(get_theme_mod( 'body_background' ) );
        $fast_press_header_shadow = esc_attr(get_theme_mod( 'fast_press_header_shadow' ) );
        $fast_press_header_search = esc_attr(get_theme_mod( 'fast_press_header_search' ) );
		
		
		
		
## Header Styles
		if( $fast_press_header_shadow) { $style28 = ".s-shadow { background-color: inherit !important;}";} else {$style28 ="";}
		if( $fast_press_header_search) { $search_hide = ".s-search-top-mobile { display: none !important;}";} else {$search_hide ="";}
		if( $header_tagline_hide) { $style9 = ".site-branding .site-description {display: none !important;}";} else {$style9 ="";}
		if( $fast_press_top_menu_sub_font_size) { $style10 = ".main-navigation ul ul li a {font-size: {$fast_press_top_menu_sub_font_size}px !important;}";} else {$style10 ="";}
		if( $fast_press_top_menu_font_size) { $style29 = ".main-navigation ul li a {font-size: {$fast_press_top_menu_font_size}px !important;}";} else {$style29 ="";}
		if( $before_background_color) { $style17 = ".before-header {background: {$before_background_color} !important;}";} else {$style17 ="";}
		if( $before_border_color) { $style19 = ".before-header {border-bottom: 1px solid {$before_border_color} !important;}";} else {$style19 ="";}
		if( ((!is_front_page() or !is_home() ) and (!has_post_thumbnail() or !get_post_meta( get_the_ID(), 'fast_press_value_header_image', true ) ) ) and $header_image_show == 'home' ) { $style11 = ".all-header{display: none !important;} .site-header {overflow: visible;}";} else {$style11 ="";}
		if( ((is_front_page() and !is_home() ) and (!has_post_thumbnail() or !get_post_meta( get_the_ID(), 'fast_press_value_header_image', true ) ) )and $header_image_show == 'home' ) { $style27 = " body .all-header{display: block !important;} body .site-header {overflow: hidden;}";} else {$style27 ="";}
		if (!has_header_image() ) { $style14 = ".site-branding, .all-header {display: none !important;} .grid-top {position: relative;} .site-header{overflow: inherit;}";} else {$style14 ="";}
		if( $body_background) { $body_background = "body {background: {$body_background} !important;}";} else {$body_background ="";}			
## Sidebar Styles
		if( $sidebar_position == 'no' ) { $style12 = "#content #secondary {display: none !important;}";} else {$style12 ="";}
## Menu Styles		
		if( $fast_press_menu_background_color) { $style15 = ".grid-top, .main-navigation ul ul, .slicknav_menu {background: {$fast_press_menu_background_color} !important; box-shadow: none !important;}";} else {$style15 ="";}
		if( $fast_press_menu_color) { $style16 = ".main-navigation ul li a, .main-navigation ul ul li a, .main-navigation ul ul li a:hover, .main-navigation ul ul li > a:after, .main-navigation ul ul ul li > a:after, .slicknav_nav a {color: {$fast_press_menu_color} !important; }";} else {$style16 ="";}
		if( $fast_press_menu_background_hover_color) { $style18 = ".main-navigation ul li a:hover, .slicknav_nav a:hover {background: {$fast_press_menu_background_hover_color} !important; box-shadow: none !important;}";} else {$style18 ="";}
## Colors Styles
		if( $fast_press_link_color) { $style22 = "a {color: {$fast_press_link_color};}";} else {$style22 ="";}
		if( $fast_press_link_hover_color ) { $style23 = "a:hover {color: {$fast_press_link_hover_color};}";} else {$style23 ="";}
        wp_add_inline_style( 'fast-style-css', 
		$style9.$style10.$style11.$style12.$style14.$style15.$style16.$style17.$style18.$style19.$style22.$style23.$style27.$style28.$style29.$body_background.$search_hide
		);
}
add_action( 'wp_enqueue_scripts', 'fast_press_customizing_styles' );