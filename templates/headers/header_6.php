<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'prohost_template_header_6_theme_setup' ) ) {
	add_action( 'prohost_action_before_init_theme', 'prohost_template_header_6_theme_setup', 1 );
	function prohost_template_header_6_theme_setup() {
		prohost_add_template(array(
			'layout' => 'header_6',
			'mode'   => 'header',
			'title'  => esc_html__('Header 6', 'prohost'),
			'icon'   => prohost_get_file_url('templates/headers/images/6.jpg')
			));
	}
}

// Template output
if ( !function_exists( 'prohost_template_header_6_output' ) ) {
	function prohost_template_header_6_output($post_options, $post_data) {

		// WP custom header
		$header_css = '';
		if ($post_options['position'] != 'over') {
			$header_image = get_header_image();
			$header_css = $header_image!='' 
				? ' style="background-image: url('.esc_url($header_image).')"' 
				: '';
		}
		?>

		<div class="top_panel_fixed_wrap"></div>

		<header class="top_panel_wrap top_panel_style_6 scheme_<?php echo esc_attr($post_options['scheme']); ?>">
			<div class="top_panel_wrap_inner top_panel_inner_style_6 top_panel_position_<?php echo esc_attr(prohost_get_custom_option('top_panel_position')); ?>">

			<div class="top_panel_middle" <?php echo trim($header_css); ?>>
				<div class="content_wrap">
					<div class="contact_logo">
						<?php prohost_show_logo(true, true); ?>
					</div>
					<div class="menu_main_wrap">
						<a href="#" class="menu_main_responsive_button icon-menu"></a>
						<nav class="menu_main_nav_area">
							<?php
							$menu_main = prohost_get_nav_menu('menu_main');
							if (empty($menu_main)) $menu_main = prohost_get_nav_menu();
							echo trim($menu_main);
							?>
						</nav>
						<?php
						if (function_exists('prohost_exists_woocommerce') && prohost_exists_woocommerce() && (prohost_is_woocommerce_page() && prohost_get_custom_option('show_cart')=='shop' || prohost_get_custom_option('show_cart')=='always') && !(is_checkout() || is_cart() || defined('WOOCOMMERCE_CHECKOUT') || defined('WOOCOMMERCE_CART'))) { 
							?>
							<div class="menu_main_cart top_panel_icon">
								<?php get_template_part(prohost_get_file_slug('templates/headers/_parts/contact-info-cart.php')); ?>
							</div>
							<?php
						}
						if (prohost_get_custom_option('show_search')=='yes') 
							echo trim(prohost_sc_search(array('class'=>"top_panel_icon", 'state'=>"closed")));
						?>
					</div>
				</div>
			</div>

			</div>
		</header>

		<?php
	}
}
?>