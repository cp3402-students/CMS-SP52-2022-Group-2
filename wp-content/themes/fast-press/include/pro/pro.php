<?php if( ! defined( 'ABSPATH' ) ) exit;
	
	function fast_presshow_to_scripts() {
		wp_enqueue_style( 'how-to-use', get_template_directory_uri() . '/include/pro/pro.css' );
	}
	
	add_action( 'admin_enqueue_scripts', 'fast_presshow_to_scripts' );	
	
	// create custom plugin settings menu
	add_action('admin_menu', 'fast_press_create_menu');
	
	function fast_press_create_menu() {
		
		//create new top-level menu
		global $fast_press_settings_page;
		
		$fast_press_settings_page = add_theme_page('Fest Press Theme', 'Fest Press Theme', 'edit_theme_options',  'fast-press-unique-identifier', 'fast_press_settings_page');
		
		//call register settings function
		add_action( 'admin_init', 'register_mysettings' );
	}
	
	function register_mysettings() {
		//register our settings
		register_setting( 'seos-settings-group', 'adsense' );
	}
	
	function fast_press_settings_page() {	
	$path_img = get_template_directory_uri()."/include/pro/"; ?>
	<div id="cont-pro">
		<h1><?php esc_html_e('Fast Press WordPress Theme', 'fast-press'); ?></h1>	
		<div class="pro-links">	
		<p><?php esc_html_e('We create free themes and have helped thousands of users to build their sites. You can also support us using the Fast Press Pro theme with many new features and extensions.', 'fast-press'); ?></p>
			<a class="button button-primary" target="_blank" href="https://seosthemes.info/fast-press-wordpress-theme/"><?php esc_html_e('Theme Demo', 'fast-press'); ?></a>
			<a style="background: #A83625;" class="reds button button-primary" target="_blank" href="https://seosthemes.com/fast-press-wordpress-theme/"><?php esc_html_e('Upgrade to PRO', 'fast-press'); ?></a>
		</div>	
		<table id="table-colors" class="free-wp-theme">
			<tbody>
				<tr>
					<th><?php esc_html_e('Fast Press Wordpress Theme', 'fast-press'); ?></th>
					<th><?php esc_html_e('Free WP Theme','fast-press'); ?></th>
					<th><?php esc_html_e('Premium WP Theme','fast-press'); ?></th>
				</tr>
				<tr>
					<td><strong><?php esc_html_e('One Click Demo Import', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>NO.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>				
				<tr class="s-white">
					<td><strong><?php esc_html_e('Sidebar Position', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>NO.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e('Blog Page', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>NO.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>
				<tr class="s-white">
					<td><strong><?php esc_html_e('Camera Slider', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>NO.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>
				
				<tr>
					<td><strong><?php esc_html_e('Slick Slider', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>NO.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>
				<tr class="s-white">
					<td><strong><?php esc_html_e('Cube Slider', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>NO.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e('Title Position', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>NO.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>
				<tr class="s-white">
					<td><strong><?php esc_html_e('Post Options', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>NO.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e('WooCommerce My Account Icon', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>NO.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>
				<tr class="s-white">
					<td><strong><?php esc_html_e('Multiple Gallery', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>NO.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e('Animations of all elements', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>NO.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>
				<tr class="s-white">
					<td><strong><?php esc_html_e('Header Options', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e('Hide Single Page Title', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>NO.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>
				<tr class="s-white">
					<td><strong><?php esc_html_e('Featured Image', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e('WooCommerce Product Zoom', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>
				<tr class="s-white">
					<td><strong><?php esc_html_e('WooCommerce Cart Options', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e('WooCommerce Pagination', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>NO.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>
				
				
				<tr class="s-white">
					<td><strong><?php esc_html_e('All Google Fonts', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>NO.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e('Shortcode', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>NO.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>
				<tr class="s-white">
					<td><strong><?php esc_html_e('Color of All Elements', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>NO.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e('Full Width Page', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>
				
				
				<tr class="s-white">
					<td><strong><?php esc_html_e('More Social Media Icons Header', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>NO.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e('Custom Footer Copyright', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>NO.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>
				<tr class="s-white">
					<td><strong><?php esc_html_e('Microdata', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e('Translation Ready', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>
				<tr class="s-white">
					<td><strong><?php esc_html_e('Header Logo', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e('Vote Options', 'fast-press'); ?></strong></td>
					<td><img src="<?php echo $path_img; ?>NO.ico" alt="free-wp-theme" /></td>
					<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
				</tr>
				<tr>
					
					
					<tr class="s-white">
						<td><strong><?php esc_html_e('Header Image', 'fast-press'); ?></strong></td>
						<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
						<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e('Background Image', 'fast-press'); ?></strong></td>
						<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
						<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
					</tr>
					<tr class="s-white">
						<td><strong><?php esc_html_e('404 Page Template', 'fast-press'); ?></strong></td>
						<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
						<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e('Footer Widgets', 'fast-press'); ?></strong></td>
						<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
						<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
					</tr>
					<tr class="s-white">
						<td><strong><?php esc_html_e('WooCommerce Plugin Support', 'fast-press'); ?></strong></td>
						<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
						<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e('Back to top button', 'fast-press'); ?></strong></td>
						<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
						<td><img src="<?php echo $path_img; ?>YES.ico" alt="free-wp-theme" /></td>
					</tr>
					<tr>
						
						<td><a class="button button-primary" target="_blank" href="https://seosthemes.info/fast-press-wordpress-theme/"><?php esc_html_e('Theme Demo', 'fast-press'); ?></a></td>
						<td> </td>
						<td style=" text-align:center;"><a style="background: #A83625;" class="reds button button-primary" target="_blank" href="https://seosthemes.com/fast-press-wordpress-theme/"><?php esc_html_e('Upgrade to PRO', 'fast-press'); ?></a></td>
					</tr>					
				</tbody>
			</table>
		</div>
		<?php	
		}		