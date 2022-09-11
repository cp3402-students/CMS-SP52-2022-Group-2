<?php if( ! defined( 'ABSPATH' ) ) exit;
/**
 * Read More Button
 */
	function fast_press_excerpt_more( $more ) {
		if ( is_admin() ) {
			return $more;
		}
        return '<p class="link-more"><a class="myButt " href="'. esc_url(get_permalink( get_the_ID() ) ) . '">' . fast_press_return_read_more_text (). '</a></p>';
	}
	add_filter( 'excerpt_more', 'fast_press_excerpt_more' );	
	function fast_press_excerpt_length( $length ) {
			if ( is_admin() ) {
					return $length;
			}
			return 22;
	}
	add_filter( 'excerpt_length', 'fast_press_excerpt_length', 999 );
	function fast_press_return_read_more_text () {
		return __( 'Read More','fast-press');
	}