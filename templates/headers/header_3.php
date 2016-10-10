<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'prohost_template_header_3_theme_setup' ) ) {
	add_action( 'prohost_action_before_init_theme', 'prohost_template_header_3_theme_setup', 1 );
	function prohost_template_header_3_theme_setup() {
		prohost_add_template(array(
			'layout' => 'header_3',
			'mode'   => 'header',
			'title'  => esc_html__('Header 3', 'prohost'),
			'icon'   => prohost_get_file_url('templates/headers/images/3.jpg')
			));
	}
}

// Template output
if ( !function_exists( 'prohost_template_header_3_output' ) ) {
	function prohost_template_header_3_output($post_options, $post_data) {

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

		<header class="top_panel_wrap top_panel_style_3 scheme_<?php echo esc_attr($post_options['scheme']);?><?php echo (($contact_phone = trim(prohost_get_custom_option('contact_phone'))) != '' ? '' : ' without_phone'); ?>">
			<div class="top_panel_wrap_inner top_panel_inner_style_3 top_panel_position_<?php echo esc_attr(prohost_get_custom_option('top_panel_position')); ?>">
			
			<?php if (prohost_get_custom_option('show_top_panel_top')=='yes') { ?>
				<div class="top_panel_top">
					<div class="content_wrap clearfix">
						<?php
						prohost_template_set_args('top-panel-top', array(
							'top_panel_top_components' => array('contact_email', 'login', 'currency')//('contact_info', 'search', 'login', 'socials', 'currency', 'bookmarks')
						));
						get_template_part(prohost_get_file_slug('templates/headers/_parts/top-panel-top.php'));
						?>
					</div>
				</div>
			<?php } ?>

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
					</div>
                    <?php if ((prohost_get_custom_option('show_search') == 'yes') || (($contact_phone = trim(prohost_get_custom_option('contact_phone'))) != ''))  { ?>
                        <div class="search">
                            <div class="menu_search_delimiter"></div>
                            <?php if (prohost_get_custom_option('show_search') == 'yes')
                                echo trim(prohost_sc_search(array('class' => "top_panel_icon", 'state' => "closed")));
                            ?>
                            <?php
                            if (($contact_phone = trim(prohost_get_custom_option('contact_phone'))) != '') { ?>
                                <div class="phone_header_3">
                                    <div class="top_panel_middle_contact_area">
                                        <span class="icon-phone"></span>
                                        <?php if (($text_before_phone = trim(prohost_get_custom_option('text_before_phone'))) != '') { ?>
                                                <?php echo force_balance_tags($text_before_phone);?>
                                        <?php } ?>
                                        <span class="text_phone">
                                            <?php echo force_balance_tags($contact_phone); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
			</div>

			</div>
		</header>

		<?php
	}
}
?>