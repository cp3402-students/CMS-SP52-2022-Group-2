<?php
// Do not allow direct access to the file.
if( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Header Top
 *
 */
 add_action( 'customize_register', 'fast_press_header_top_customize_register' );
function fast_press_header_top_customize_register( $wp_customize ) {
/***********************************************************************************
 * Back to top button Options
 ***********************************************************************************/
		$wp_customize->add_section( 'header_top' , array(
			'title'       => __( 'Header Top', 'fast-press' ),
			'priority'   => 2,
		) );
		$wp_customize->add_setting( 'activate_header_top', array (
			'sanitize_callback' => 'fast_press_sanitize_checkbox',
		) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'activate_header_top', array(
			'priority'    => 1,
			'label'    => __( 'Deactivate Header Top', 'fast-press' ),
			'section'  => 'header_top',
			'settings' => 'activate_header_top',
			'type' => 'checkbox',
		) ) );

 	    $wp_customize->add_setting( 'header_email', array (
		    'default' => 'email@seosthemes.com',	
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'header_email', array(
			'label'    => __( 'E-mail', 'fast-press' ),
			'priority'    => 3,
			'section'  => 'header_top',
			'settings' => 'header_email',
			'type' => 'text',
		) ) );
 	    $wp_customize->add_setting( 'header_address', array (
			'default' => 'Str. 368',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'header_address', array(
			'label'    => __( 'Address', 'fast-press' ),
			'priority'    => 3,
			'section'  => 'header_top',
			'settings' => 'header_address',
			'type' => 'text',
		) ) );
 	    $wp_customize->add_setting( 'header_phone', array (
			'default' => '+01234567890',		
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'header_phone', array(
			'label'    => __( 'Phone', 'fast-press' ),
			'priority'    => 3,
			'section'  => 'header_top',
			'settings' => 'header_phone',
			'type' => 'text',
		) ) );
}		
	
	
function fast_press_header () {
?>
<header class="site-header" itemscope="itemscope" itemtype="http://schema.org/WPHeader">
		<?php if ( !get_theme_mod( 'activate_header_top' ) ) { ?>
		<div class="header-top">
			<div id="top-contacts" class="before-header">
						<?php if (get_theme_mod('header_email', 'email@seosthemes.com')) { ?>
							<div class="h-email" itemprop="email"><a href="mailto:<?php echo esc_html( get_theme_mod( 'header_email', 'email@seosthemes.com') ); ?>"><span class="dashicons dashicons-email-alt"> </span> <?php echo esc_html( get_theme_mod( 'header_email', 'email@seosthemes.com') ); ?></a></div>
						<?php } ?>
						<?php if (get_theme_mod('header_address','Str. 368')) { ?>
							<div class="h-address" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"><span class="dashicons dashicons-location"></span><?php echo esc_html( get_theme_mod( 'header_address','Str. 368') ); ?></div>
						<?php } ?>
						<?php if (get_theme_mod('header_phone','+01234567890')) { ?>
							<div class="h-phone" itemprop="telephone"><a href="tel:<?php echo esc_html( get_theme_mod( 'header_phone','+01234567890') ); ?>"><span class="dashicons dashicons-phone"> </span> <?php echo esc_html( get_theme_mod( 'header_phone','+01234567890') ); ?></a></div>
						<?php } ?>				
			</div>
		</div>
		<?php
		}	?>

<div id="grid-top" class="grid-top">
	<!-- Site Navigation  -->
	<button id="s-button-menu" class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><img alt="mobile" src="<?php echo esc_url(get_template_directory_uri() ) . '/images/mobile.jpg'; ?>"/></button>
	<div class="mobile-cont">
		<div class="mobile-logo" itemprop="logo" itemscope="itemscope" itemtype="http://schema.org/Brand">
				<?php the_custom_logo(); ?>
		</div>
	</div>
	<div class="mobile-setiles">
	    <?php
		do_action( 'fast_press_header_search_top_mobile' );
		do_action( 'fast_press_header_woo_cart' );
		do_action( 'fast_press_header_my_account' );
		?>
	</div>	
	<nav id="site-navigation" class="main-navigation">
		<div class="header-right" itemprop="logo" itemscope="itemscope" itemtype="http://schema.org/Brand">
				<?php the_custom_logo(); ?>
		</div>	
		<button class="menu-toggle"><?php esc_html_e( 'Menu', 'fast-press' ); ?></button>
		<?php wp_nav_menu( array( 
		'theme_location' => 'primary',
		'menu_id' => 'primary-menu',
		'items_wrap' => '
		<ul id="%1$s" class="%2$s">%3$s 
			'.do_action( 'fast_press_header_search_top' )
			.do_action( 'fast_press_header_woo_cart' ).'
		</ul>'		
		) ); ?>
	</nav><!-- #site-navigation -->
</div>
	<!-- Header Image  -->
	<div class="all-header">
	    <div class="s-shadow"></div>
	    <div class="s-hidden">
		<?php if (get_theme_mod( 'header_image_position' ) == 'default' ) { ?>
		<img id="masthead" style="<?php fast_press_heade_image_zoom_speed (); ?>" class="header-image" src='<?php echo esc_url(get_template_directory_uri() ) . '/images/header.webp'; ?>' alt="<?php esc_attr_e( 'header image','fast-press' ); ?>"/>	
		<?php } ?>
		<?php if (get_theme_mod( 'header_image_position' ) == 'real' ) { ?>
		<img id="masthead" style="<?php fast_press_heade_image_zoom_speed (); ?>" class="header-image" src='<?php if ( !is_home() and has_post_thumbnail() and get_post_meta( get_the_ID(), 'fast_press_value_header_image', true ) ) { the_post_thumbnail_url(); } else { header_image(); } ?>' alt="<?php esc_attr_e( 'header image','fast-press' ); ?>"/>	
		<?php } else { ?>
		<div id="masthead" class="header-image" style="<?php fast_press_heade_image_zoom_speed (); ?> background-image: url( '<?php if (  !is_home() and has_post_thumbnail() and get_post_meta( get_the_ID(), 'fast_press_value_header_image', true ) ) { the_post_thumbnail_url(); } else { header_image(); } ?>' );"></div>
		<?php } ?>
</div>
		<div class="site-branding">
		<?php if ( display_header_text() == true ) { ?>
			<div class="ml15">
			<?php
			if ( is_front_page() && is_home() ) :
				?>
					<h1 class="site-title" itemscope itemtype="http://schema.org/Brand"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><span class="word"><?php bloginfo( 'name' ); ?></span></a></h1>
					<?php
				else :
					?>
					<p class="site-title" itemscope itemtype="http://schema.org/Brand"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><span class="word"><?php bloginfo( 'name' ); ?></span></a></p>
					<?php
				endif;
				$fast_press_description = esc_html(get_bloginfo( 'description', 'display' ) );
				if ( $fast_press_description || is_customize_preview() ) :
					?>    
					<p class="site-description" itemprop="headline">
						<span class="word"><?php echo esc_html($fast_press_description); ?></span>
					</p>
				<?php endif; ?>	
			</div>
		<?php } ?>	
		</div>
		<!-- .site-branding -->
	</div>
</header>
<?php }