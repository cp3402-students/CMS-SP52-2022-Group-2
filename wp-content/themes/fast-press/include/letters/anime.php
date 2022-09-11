<?php if( ! defined( 'ABSPATH' ) ) exit;
/*******************************
* Enqueue scripts and styles.
********************************/
function fast_press_anima_scripts() {
	if( !get_theme_mod('fast_press_header_animation') ) {
		wp_enqueue_style( 'fast-anima-css', get_template_directory_uri() . '/include/letters/anime.css');
		wp_enqueue_script( 'fast-anima-js', get_template_directory_uri() . '/include/letters/anime.js', array( 'jquery' ), true);
		wp_enqueue_script( 'fast-anima-custom-js', get_template_directory_uri() . '/include/letters/anime-custom.js', array( 'jquery' ), '7638488', true);
	}
}
add_action( 'wp_enqueue_scripts', 'fast_press_anima_scripts' );