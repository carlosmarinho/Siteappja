<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_countdown_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_countdown_theme_setup' );
	function prohost_sc_countdown_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_countdown_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_sc_countdown_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

//[trx_countdown date="" time=""]

if (!function_exists('prohost_sc_countdown')) {	
	function prohost_sc_countdown($atts, $content = null) {
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"date" => "",
			"time" => "",
			"style" => "1",
			"align" => "center",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => "",
			"width" => "",
			"height" => ""
		), $atts)));
		if (empty($id)) $id = "sc_countdown_".str_replace('.', '', mt_rand());
		$class .= ($class ? ' ' : '') . prohost_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= prohost_get_css_dimensions_from_values($width, $height);
		if (empty($interval)) $interval = 1;
		prohost_enqueue_script( 'prohost-jquery-plugin-script', prohost_get_file_url('js/countdown/jquery.plugin.js'), array('jquery'), null, true );	
		prohost_enqueue_script( 'prohost-countdown-script', prohost_get_file_url('js/countdown/jquery.countdown.js'), array('jquery'), null, true );	
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
			. ' class="sc_countdown sc_countdown_style_' . esc_attr(max(1, min(2, $style))) . (!empty($align) && $align!='none' ? ' align'.esc_attr($align) : '') . (!empty($class) ? ' '.esc_attr($class) : '') .'"'
			. ($css ? ' style="'.esc_attr($css).'"' : '')
			. ' data-date="'.esc_attr(empty($date) ? date('Y-m-d') : $date).'"'
			. ' data-time="'.esc_attr(empty($time) ? '00:00:00' : $time).'"'
			. (!prohost_param_is_off($animation) ? ' data-animation="'.esc_attr(prohost_get_animation_classes($animation)).'"' : '')
			. '>'
				. ($align=='center' ? '<div class="sc_countdown_inner">' : '')
				. '<div class="sc_countdown_item sc_countdown_days">'
					. '<span class="sc_countdown_digits"><span></span><span></span><span></span></span>'
					. '<span class="sc_countdown_label">'.esc_html__('Days', 'prohost').'</span>'
				. '</div>'
				. '<div class="sc_countdown_separator">:</div>'
				. '<div class="sc_countdown_item sc_countdown_hours">'
					. '<span class="sc_countdown_digits"><span></span><span></span></span>'
					. '<span class="sc_countdown_label">'.esc_html__('Hours', 'prohost').'</span>'
				. '</div>'
				. '<div class="sc_countdown_separator">:</div>'
				. '<div class="sc_countdown_item sc_countdown_minutes">'
					. '<span class="sc_countdown_digits"><span></span><span></span></span>'
					. '<span class="sc_countdown_label">'.esc_html__('Minutes', 'prohost').'</span>'
				. '</div>'
				. '<div class="sc_countdown_separator">:</div>'
				. '<div class="sc_countdown_item sc_countdown_seconds">'
					. '<span class="sc_countdown_digits"><span></span><span></span></span>'
					. '<span class="sc_countdown_label">'.esc_html__('Seconds', 'prohost').'</span>'
				. '</div>'
				. '<div class="sc_countdown_placeholder hide"></div>'
				. ($align=='center' ? '</div>' : '')
			. '</div>';
		return apply_filters('prohost_shortcode_output', $output, 'trx_countdown', $atts, $content);
	}
	prohost_require_shortcode("trx_countdown", "prohost_sc_countdown");
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_countdown_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_countdown_reg_shortcodes');
	function prohost_sc_countdown_reg_shortcodes() {
	
		prohost_sc_map("trx_countdown", array(
			"title" => esc_html__("Countdown", "prohost"),
			"desc" => wp_kses_data( __("Insert countdown object", "prohost") ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"date" => array(
					"title" => esc_html__("Date", "prohost"),
					"desc" => wp_kses_data( __("Upcoming date (format: yyyy-mm-dd)", "prohost") ),
					"value" => "",
					"format" => "yy-mm-dd",
					"type" => "date"
				),
				"time" => array(
					"title" => esc_html__("Time", "prohost"),
					"desc" => wp_kses_data( __("Upcoming time (format: HH:mm:ss)", "prohost") ),
					"value" => "",
					"type" => "text"
				),
				"style" => array(
					"title" => esc_html__("Style", "prohost"),
					"desc" => wp_kses_data( __("Countdown style", "prohost") ),
					"value" => "1",
					"type" => "checklist",
					"options" => prohost_get_list_styles(1, 2)
				),
				"align" => array(
					"title" => esc_html__("Alignment", "prohost"),
					"desc" => wp_kses_data( __("Align counter to left, center or right", "prohost") ),
					"divider" => true,
					"value" => "none",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => prohost_get_sc_param('align')
				), 
				"width" => prohost_shortcodes_width(),
				"height" => prohost_shortcodes_height(),
				"top" => prohost_get_sc_param('top'),
				"bottom" => prohost_get_sc_param('bottom'),
				"left" => prohost_get_sc_param('left'),
				"right" => prohost_get_sc_param('right'),
				"id" => prohost_get_sc_param('id'),
				"class" => prohost_get_sc_param('class'),
				"animation" => prohost_get_sc_param('animation'),
				"css" => prohost_get_sc_param('css')
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_countdown_reg_shortcodes_vc' ) ) {
	//add_action('prohost_action_shortcodes_list_vc', 'prohost_sc_countdown_reg_shortcodes_vc');
	function prohost_sc_countdown_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_countdown",
			"name" => esc_html__("Countdown", "prohost"),
			"description" => wp_kses_data( __("Insert countdown object", "prohost") ),
			"category" => esc_html__('Content', 'prohost'),
			'icon' => 'icon_trx_countdown',
			"class" => "trx_sc_single trx_sc_countdown",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "date",
					"heading" => esc_html__("Date", "prohost"),
					"description" => wp_kses_data( __("Upcoming date (format: yyyy-mm-dd)", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "time",
					"heading" => esc_html__("Time", "prohost"),
					"description" => wp_kses_data( __("Upcoming time (format: HH:mm:ss)", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "style",
					"heading" => esc_html__("Style", "prohost"),
					"description" => wp_kses_data( __("Countdown style", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(prohost_get_list_styles(1, 2)),
					"type" => "dropdown"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", "prohost"),
					"description" => wp_kses_data( __("Align counter to left, center or right", "prohost") ),
					"class" => "",
					"value" => array_flip(prohost_get_sc_param('align')),
					"type" => "dropdown"
				),
				prohost_get_vc_param('id'),
				prohost_get_vc_param('class'),
				prohost_get_vc_param('animation'),
				prohost_get_vc_param('css'),
				prohost_vc_width(),
				prohost_vc_height(),
				prohost_get_vc_param('margin_top'),
				prohost_get_vc_param('margin_bottom'),
				prohost_get_vc_param('margin_left'),
				prohost_get_vc_param('margin_right')
			)
		) );
		
		class WPBakeryShortCode_Trx_Countdown extends PROHOST_VC_ShortCodeSingle {}
	}
}
?>