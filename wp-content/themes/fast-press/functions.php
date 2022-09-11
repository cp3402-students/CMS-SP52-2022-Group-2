<?php 
// Do not allow direct access to the file.
if( ! defined( 'ABSPATH' ) ) {
    exit;
}
if ( ! function_exists( 'fast_press_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 */
	function fast_press_setup() {
		/*
		 * Make theme available for translation.
		 */
		load_theme_textdomain( 'fast-press', get_template_directory() . '/languages' );
		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );
		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );
		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 */
		add_theme_support( 'post-thumbnails' );
		/*
		 * WooCommerce Support
		 */		
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
		/*
		 * Gutenberg Support
		 */			
		add_theme_support( 'align-wide' );
		add_theme_support( 'disable-custom-font-sizes');
		add_theme_support( 'disable-custom-colors' );
		add_theme_support( 'wp-block-styles' );		
		add_theme_support( 'responsive-embeds' );
		// This theme uses wp_nav_menu() in one location.
		add_theme_support( 'nav-menus' );
		register_nav_menu('primary', esc_html__( 'Primary', 'fast-press' ) );
		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );
		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'fast_press_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );
		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );
		/**
		 * Add support for core custom logo.
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 53,
			'width'       => 140,
			'flex-width'  => true,
			'flex-height' => false,
		) );
		register_default_headers( array(
			'img1' => array(
				'url'           => get_template_directory_uri() . '/images/header.webp',
				'thumbnail_url' => get_template_directory_uri() . '/images/header.webp',
				'description'   => esc_html__( 'Default Image 1', 'fast-press' )
			)
		));		
	}
endif;
add_action( 'after_setup_theme', 'fast_press_setup' );
/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 */
function fast_press_content_width() {
	// This variable is intended to be overruled from themes.
	$GLOBALS['content_width'] = apply_filters( 'fast_press_content_width', 640 );
}
add_action( 'after_setup_theme', 'fast_press_content_width', 0 );
/**
 * Register widget area.
 */
function fast_press_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'fast-press' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'fast-press' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer 1', 'fast-press' ),
		'id'            => 'footer-1',
		'description'   => '',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer 2', 'fast-press' ),
		'id'            => 'footer-2',
		'description'   => '',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer 3', 'fast-press' ),
		'id'            => 'footer-3',
		'description'   => '',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer 4', 'fast-press' ),
		'id'            => 'footer-4',
		'description'   => '',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'fast_press_widgets_init' );
/**
 * Enqueue scripts and styles.
 */
function fast_press_scripts() {	
	wp_enqueue_script( 'jquery');	
	wp_enqueue_script( 'jquery-ui-accordion' );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-tabs' );
	wp_enqueue_style( 'fast-style-css', get_stylesheet_uri() );
	wp_enqueue_style( 'dashicons' );
	wp_enqueue_style( 'fast-Sans-font', '//fonts.googleapis.com/css?family=Open+Sans:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i' );
	wp_enqueue_style( 'fast-Roboto-font', '//fonts.googleapis.com/css?family=Nanum+Pen+Script|Roboto' );
	wp_enqueue_style( 'fast-animate-css', get_template_directory_uri() . '/css/animate.css' );
	wp_enqueue_script( 'fast-search-top-js', get_template_directory_uri() . '/js/search-top.js', array(), '', false );	
	wp_enqueue_style( 'fast-Robotos-font', '//fonts.googleapis.com/css?family=Nanum+Pen+Script|Robotos' );
	wp_enqueue_style( 'fast-font-awesome', get_template_directory_uri() . '/css/font-awesome.css', array(), '4.7.0'  );
	wp_enqueue_style( 'fast-font-woo-css', get_template_directory_uri() . '/include/woocommerce/woo-css.css', array(), '4.7.0'  );
	wp_enqueue_script( 'fast-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '', true );
	wp_enqueue_script( 'fast-mobile-menu', get_template_directory_uri() . '/js/mobile-menu.js', array(), '', false );
	wp_enqueue_script( 'fast-viewportchecker', get_template_directory_uri() . '/js/viewportchecker.js', array(), '', true );
	wp_enqueue_script( 'fast-top', get_template_directory_uri() . '/js/to-top.js', array(), '', true );
	wp_enqueue_script( 'fast-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '', true );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'fast_press_scripts' );
/**
 * Includes
 */
require_once get_template_directory() . '/include/letters/anime.php';
require_once get_template_directory() . '/include/content-customizer.php';
require_once get_template_directory() . '/include/custom-header.php';
require_once get_template_directory() . '/include/template-tags.php';
require_once get_template_directory() . '/include/customizer.php';
require_once get_template_directory() . '/include/header-top.php';
require_once get_template_directory() . '/include/back-to-top-button.php';
require_once get_template_directory() . '/include/read-more-button.php';
require_once get_template_directory() . '/include/animations/animations.php';
require_once get_template_directory() . '/include/customize-pro/class-customize.php';
require_once get_template_directory() . '/include/pro/pro.php';
if( class_exists( 'WooCommerce' ) ) {
    require_once get_template_directory() . '/include/woocommerce/cart.php';
	require_once get_template_directory() . '/include/plugins/class-tgm-plugin-activation.php';	
    require_once get_template_directory() . '/include/plugins/tgm-plugin-activation.php';	
}
/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require_once get_template_directory() . '/include/jetpack.php';
}
/**
 * Adds custom classes to the array of body classes.
 */
function fast_press_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}
	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}
	return $classes;
}
add_filter( 'body_class', 'fast_press_body_classes' );
function fast_press_sidebar_position() {
	if ( ( is_active_sidebar('sidebar-1') ) ) { 
		wp_enqueue_style( 'fast-sidebar', get_template_directory_uri() . '/layouts/left-sidebar.css' );
	}
}
add_action( 'wp_enqueue_scripts', 'fast_press_sidebar_position' );
/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function fast_press_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'fast_press_pingback_header' );
/**
 * Header Image Position
 */
function fast_presstop_menu () {
	if( has_header_image()
	and ( ( !is_front_page() and ( !get_theme_mod( 'header_image_show' ) == "all" ) and get_theme_mod( 'header_image_show' ) )
	or (!is_front_page() and  get_theme_mod( 'header_image_show' ) == "home"  ) ) 
	or ( !has_header_image()) ) {
		$style1 = ".grid-top {position: relative;} .woo-log-s .dashicons, .s-search-top .dashicons-search, .main-navigation ul li a, body .cart-contents .my-cart:before, body .s-search-top-mobile .dashicons-search {  color: #222; } .woo_log:hover, .main-navigation ul li a:hover { color: #333;}" ; } else { $style1 =""; }
		wp_add_inline_style( 'fast-style-css', $style1);
	}
add_action( 'wp_enqueue_scripts', 'fast_presstop_menu' );
/**
 * Header Image Animation
 */
function fast_press_header_image_zoom () { 
	if (!get_theme_mod('fast_press_header_zoom')) { 
	?>
		<style>
@-webkit-keyframes header-image {
  0% {
    -webkit-transform: scale(1) translateY(0);
            transform: scale(1) translateY(0);
    -webkit-transform-origin: 50% 16%;
            transform-origin: 50% 16%;
  }
  100% {
    -webkit-transform: scale(1.25) translateY(-15px);
            transform: scale(1.25) translateY(-15px);
    -webkit-transform-origin: top;
            transform-origin: top;
  }
}
@keyframes header-image {
  0% {
    -webkit-transform: scale(1) translateY(0);
            transform: scale(1) translateY(0);
    -webkit-transform-origin: 50% 16%;
            transform-origin: 50% 16%;
  }
  100% {
    -webkit-transform: scale(1.25) translateY(-15px);
            transform: scale(1.25) translateY(-15px);
    -webkit-transform-origin: top;
            transform-origin: top;
  }
}
	</style>
	<?php
	}
}
add_action('wp_head','fast_press_header_image_zoom');
/**
 * Header Image - Zoom Animation Speed
 */
function fast_press_heade_image_zoom_speed () { ?>
	-webkit-animation: header-image 
	<?php 
	if (get_theme_mod('header_zoom_speed')) { 
		echo esc_attr(get_theme_mod('header_zoom_speed')); 
	} else 
		echo "20";
	?>s ease-out both; 
	animation: header-image
	<?php
	if (get_theme_mod('header_zoom_speed')) {
		echo esc_attr(get_theme_mod('header_zoom_speed')); 
	} else
		echo "20";
	?>s ease-out 0s 1 normal both running;
<?php	
}
/**
 * Activate Search Icon
 */
add_action( 'wp_enqueue_scripts', 'fast_press_search_icon' );	
function fast_press_search_icon() {
    $var_search = esc_attr(get_theme_mod( 'fast_press_header_search' ) );
    if( $var_search ) { $style = ".s-search-top {display: none;}";} else {$style ="";}
    wp_add_inline_style( 'fast-style-css', 
		$style
	);
}
/**
 * Search Top
 */
function fast_press_search_top () {
		echo '<div class="s-search-top">
				<i onclick="fastSearch()" id="search-top-ico" class="dashicons dashicons-search"></i>
				<div id="big-search" style="display:none;">
					<form method="get" class="search-form" action="'. esc_url( home_url( '/' ) ).'">
						<div style="position: relative;">
						<button class="button-primary button-search"><span class="screen-reader-text">'. _x( 'Search for:', 'label', 'fast-press' ).'</span></button>
							<span class="screen-reader-text">'._x( 'Search for:', 'label', 'fast-press' ).'</span>
							<div class="s-search-show">
								<input id="s-search-field"  type="search" class="search-field"
								placeholder="'. esc_attr_x( 'Search ...', 'placeholder', 'fast-press' ).'"
								value="'. get_search_query().'" name="s"
								title="'. esc_attr_x( 'Search for:', 'label', 'fast-press' ).'" />
								<input type="submit" id="stss" class="search-submit" value="'. esc_attr_x( 'Search', 'submit button', 'fast-press' ).'" />
								<div onclick="fastCloseSearch()" id="s-close">X</div>
							</div>	
						</div>
					</form>
				</div>	
		</div>';
}
add_action( 'fast_press_header_search_top', 'fast_press_search_top' );
function fast_press_search_top_mobile () {
		echo '<div class="s-search-top-mobile">
				<i onclick="fastSearchMobile()" id="search-top-ico-mobile" class="dashicons dashicons-search"></i>
				<div id="big-search-mobile" style="display:none;">
					<form method="get" class="search-form-mobile" action="'. esc_url( home_url( '/' ) ).'">
						<div style="position: relative;">
						<button class="button-primary-mobile button-search-mobile"><span class="screen-reader-text">'. _x( 'Search for:', 'label', 'fast-press' ).'</span></button>
							<span class="screen-reader-text">'._x( 'Search for:', 'label', 'fast-press' ).'</span>
							<div class="s-search-show-mobile">
								<input id="s-search-field-mobile"  type="search" class="search-field-mobile"
								placeholder="'. esc_attr_x( 'Search ...', 'placeholder', 'fast-press' ).'"
								value="'. get_search_query().'" name="s"
								title="'. esc_attr_x( 'Search for:', 'label', 'fast-press' ).'" />
								<input type="submit" id="stss-mobile" class="search-submit-mobile" value="'. esc_attr_x( 'Search', 'submit button', 'fast-press' ).'" />
								<div onclick="fastCloseSearchMobile()" id="s-close-mobile">X</div>
							</div>	
						</div>
					</form>
				</div>	
		</div>';
}
add_action( 'fast_press_header_search_top_mobile', 'fast_press_search_top_mobile' );
/*********************************************************************************************************
* Customizer Styles
**********************************************************************************************************/
function fast_press_customize_checkbox_styles( $input ) { ?>
	<style>
		/**
		 * Checkbox Toggle UI
		 */
		#customize-theme-controls input[type="checkbox"] {
			-webkit-appearance: none;
			-moz-appearance: none;
			appearance: none;
			-webkit-tap-highlight-color: transparent;
			width: auto;
			height: auto;
			vertical-align: middle;
			position: relative;
			border: 0;
			outline: 0;
			cursor: pointer;
			background: none;
			box-shadow: none;
		}
		#customize-theme-controls input[type="checkbox"]:focus {
			box-shadow: none;
		}
		#customize-theme-controls input[type="checkbox"]:after {
			content: '';
			font-size: 8px;
			font-weight: 400;
			line-height: 18px;
			text-indent: -14px;
			color: #ffffff;
			width: 36px;
			height: 18px;
			display: inline-block;
			background-color: #a7aaad;
			border-radius: 72px;
			box-shadow: 0 0 12px rgb(0 0 0 / 15%) inset;
		}
		#customize-theme-controls input[type="checkbox"]:before {
			content: '';
			width: 14px;
			height: 14px;
			display: block;
			position: absolute;
			top: 2px;
			left: 2px;
			margin: 0;
			border-radius: 50%;
			background-color: #ffffff;
		}
		#customize-theme-controls input[type="checkbox"]:checked:before {
			left: 20px;
			margin: 0;
			background-color: #ffffff;
		}
		#customize-theme-controls input[type="checkbox"],
		#customize-theme-controls input[type="checkbox"]:before,
		#customize-theme-controls input[type="checkbox"]:after,
		#customize-theme-controls input[type="checkbox"]:checked:before,
		#customize-theme-controls input[type="checkbox"]:checked:after {
			transition: ease .15s;
		}
		#customize-theme-controls input[type="checkbox"]:checked:after {
			content: 'ON';
			background-color: #2271b1;
		}
		#_customize-input-fast_press_shortcode_top_news {
			display: none;
		}
		.customize-range {
			width: 100%;
		}
		.seos-range-value {
			color: #50575e;
			font-family: Impact;
			font-size: 17px;
		}
	</style>
	<?php }
add_action( 'customize_controls_print_styles', 'fast_press_customize_checkbox_styles');

function fast_press_admin_notice(){
    global $pagenow;
    if ( $pagenow == 'options-general.php' ) {
	    echo '
		<div class="notice notice-info is-dismissible">
             <p><b>Fast Press - <a href="themes.php?page=fast-press-unique-identifier">Free VS Pro version.</a> </b><br><br><a href="themes.php?page=fast-press-unique-identifier" class="button button-primary">More Info</a></p>
        </div>';
    }
}
add_action('admin_notices', 'fast_press_admin_notice');