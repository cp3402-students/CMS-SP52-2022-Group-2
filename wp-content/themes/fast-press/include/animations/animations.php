<?php if( ! defined( 'ABSPATH' ) ) exit;
/**
 * Enqueue scripts and styles.
 */
function fast_press_animations_scripts() {	
	wp_enqueue_style( 'fast-aos-css', get_template_directory_uri() . '/include/animations/aos.css' );
	wp_enqueue_script( 'fast-aos-js', get_template_directory_uri() . '/include/animations/aos.js', array(), '', true);
	wp_enqueue_script( 'fast-aos-options-js', get_template_directory_uri() . '/include/animations/aos-options.js', array(), '', true);
}
add_action( 'wp_enqueue_scripts', 'fast_press_animations_scripts' );
function  fast_press_animation ($animation) {
	if ( get_theme_mod( $animation ) != "none" and get_theme_mod( $animation ) != ""  )  {
		return "data-aos-delay='100'"." ".
		"data-aos-duration='500'"." ".
		"data-aos='".esc_html ( get_theme_mod( $animation ) )."'";
	}
	if ( get_theme_mod( $animation  ) == "" ) {
		return "data-aos-delay='100'"." ".
		"data-aos-duration='500'"." ".
		"data-aos='slide-up'";		
	}
}
function fast_press_animations() {
	$array = array(
				'' => esc_attr__( 'Default', 'fast-press' ),			
				'none' => esc_attr__( 'Deactivate Animation', 'fast-press' ),			
				'fade' => esc_attr__( 'fade', 'fast-press' ),
				'fade-up' => esc_attr__( 'fade-up', 'fast-press' ),
				'fade-down' => esc_attr__( 'fade-down', 'fast-press' ),
				'fade-left' => esc_attr__( 'fade-left', 'fast-press' ),
				'fade-right' => esc_attr__( 'fade-right', 'fast-press' ),
				'fade-up-right' => esc_attr__( 'fade-up-right', 'fast-press' ),
				'fade-up-left' => esc_attr__( 'fade-up-left', 'fast-press' ),
				'fade-down-right' => esc_attr__( 'fade-down-right', 'fast-press' ),
				'fade-down-left' => esc_attr__( 'fade-down-left', 'fast-press' ),
				'flip-up' => esc_attr__( 'flip-up', 'fast-press' ),
				'flip-down' => esc_attr__( 'flip-down', 'fast-press' ),
				'flip-left' => esc_attr__( 'flip-left', 'fast-press' ),
				'flip-right' => esc_attr__( 'flip-right', 'fast-press' ),
				'slide-up' => esc_attr__( 'slide-up', 'fast-press' ),
				'slide-down' => esc_attr__( 'slide-down', 'fast-press' ),
				'slide-left' => esc_attr__( 'slide-left', 'fast-press' ),
				'slide-right' => esc_attr__( 'slide-right', 'fast-press' ),
				'zoom-in' => esc_attr__( 'zoom-in', 'fast-press' ),
				'zoom-in-up' => esc_attr__( 'zoom-in-up', 'fast-press' ),
				'zoom-in-down' => esc_attr__( 'zoom-in-down', 'fast-press' ),
				'zoom-in-left' => esc_attr__( 'zoom-in-left', 'fast-press' ),
				'zoom-in-right' => esc_attr__( 'zoom-in-right', 'fast-press' ),
				'zoom-out' => esc_attr__( 'zoom-out', 'fast-press' ),
				'zoom-out-up' => esc_attr__( 'zoom-out-up', 'fast-press' ),
				'zoom-out-down' => esc_attr__( 'zoom-out-down', 'fast-press' ),
				'zoom-out-left' => esc_attr__( 'zoom-out-left', 'fast-press' ),
				'zoom-out-right' => esc_attr__( 'zoom-out-right', 'fast-press' ),
				);
	return $array;
}
		function fast_press_sanitize_animations( $input ) {
			$valid = fast_press_animations();
			if ( array_key_exists( $input, $valid ) ) {
				return $input;
			} else {
				return '';
			}
		}
function fast_press_animations_customize_register( $wp_customize ) {
		$wp_customize->add_panel( 'fast_press_panel_animations' , array(
			'title'       => __( 'Animations', 'fast-press' ),
			'priority'   => 1,
		) );
/************************************
* Animation Articles
************************************/
		$wp_customize->add_section( 'fast_press_animations_section_articles' , array(
			'title'       => __( 'Animation Articles', 'fast-press' ),
			'panel'       => 'fast_press_panel_animations',
			'priority'   => 64,
		) );
		$wp_customize->add_setting( 'fast_press_articles_animation', array (
			'sanitize_callback' => 'fast_press_sanitize_animations',
		) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'fast_press_articles_animation', array(
			'label'    => __( 'Animation Articles', 'fast-press' ),
			'section'  => 'fast_press_animations_section_articles',
			'settings' => 'fast_press_articles_animation',
			'type'     =>  'select',
            'choices'  => fast_press_animations(),		
		) ) );
}
add_action( 'customize_register', 'fast_press_animations_customize_register' );