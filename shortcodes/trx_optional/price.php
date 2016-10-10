<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_price_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_price_theme_setup' );
	function prohost_sc_price_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_price_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_sc_price_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_price id="unique_id" currency="$" money="29.99" period="monthly"]
*/

if (!function_exists('prohost_sc_price')) {	
	function prohost_sc_price($atts, $content=null){	
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"money" => "",
			"currency" => "$",
			"period" => "",
			"align" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$output = '';
		if (!empty($money)) {
			$class .= ($class ? ' ' : '') . prohost_get_css_position_as_classes($top, $right, $bottom, $left);
			$m = explode('.', str_replace(',', '.', $money));
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
					. ' class="sc_price'
					. (!empty($class) ? ' '.esc_attr($class) : '')
					. ($align && $align!='none' ? ' align'.esc_attr($align) : '')
                    . (!empty($currency) ? ' with_currency' : '')
					. '"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
					. '>'
				. '<span class="sc_price_money">'
                    . (!empty($currency) ? '<span class="sc_price_currency">'.($currency).'</span>' : '')
                    . (!empty($m[0]) ? ($m[0]) : '')
				    . (!empty($m[1]) ? '.'.($m[1]) : '')
                .'</span>'
				. (!empty($period) ? '<span class="sc_price_period">'.($period).'</span>' : (!empty($m[1]) ? '<span class="sc_price_period_empty"></span>' : ''))
				. '</div>';
		}
		return apply_filters('prohost_shortcode_output', $output, 'trx_price', $atts, $content);
	}
	prohost_require_shortcode('trx_price', 'prohost_sc_price');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_price_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_price_reg_shortcodes');
	function prohost_sc_price_reg_shortcodes() {
	
		prohost_sc_map("trx_price", array(
			"title" => esc_html__("Price", "prohost"),
			"desc" => wp_kses_data( __("Insert price with decoration", "prohost") ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"money" => array(
					"title" => esc_html__("Money", "prohost"),
					"desc" => wp_kses_data( __("Money value (dot or comma separated)", "prohost") ),
					"value" => "",
					"type" => "text"
				),
				"currency" => array(
					"title" => esc_html__("Currency", "prohost"),
					"desc" => wp_kses_data( __("Currency character", "prohost") ),
					"value" => "$",
					"type" => "text"
				),
				"period" => array(
					"title" => esc_html__("Period", "prohost"),
					"desc" => wp_kses_data( __("Period text (if need). For example: monthly, daily, etc.", "prohost") ),
					"value" => "",
					"type" => "text"
				),
				"align" => array(
					"title" => esc_html__("Alignment", "prohost"),
					"desc" => wp_kses_data( __("Align price to left or right side", "prohost") ),
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => prohost_get_sc_param('float')
				), 
				"top" => prohost_get_sc_param('top'),
				"bottom" => prohost_get_sc_param('bottom'),
				"left" => prohost_get_sc_param('left'),
				"right" => prohost_get_sc_param('right'),
				"id" => prohost_get_sc_param('id'),
				"class" => prohost_get_sc_param('class'),
				"css" => prohost_get_sc_param('css')
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_price_reg_shortcodes_vc' ) ) {
	//add_action('prohost_action_shortcodes_list_vc', 'prohost_sc_price_reg_shortcodes_vc');
	function prohost_sc_price_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_price",
			"name" => esc_html__("Price", "prohost"),
			"description" => wp_kses_data( __("Insert price with decoration", "prohost") ),
			"category" => esc_html__('Content', 'prohost'),
			'icon' => 'icon_trx_price',
			"class" => "trx_sc_single trx_sc_price",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "money",
					"heading" => esc_html__("Money", "prohost"),
					"description" => wp_kses_data( __("Money value (dot or comma separated)", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "currency",
					"heading" => esc_html__("Currency symbol", "prohost"),
					"description" => wp_kses_data( __("Currency character", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "$",
					"type" => "textfield"
				),
				array(
					"param_name" => "period",
					"heading" => esc_html__("Period", "prohost"),
					"description" => wp_kses_data( __("Period text (if need). For example: monthly, daily, etc.", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", "prohost"),
					"description" => wp_kses_data( __("Align price to left or right side", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(prohost_get_sc_param('float')),
					"type" => "dropdown"
				),
				prohost_get_vc_param('id'),
				prohost_get_vc_param('class'),
				prohost_get_vc_param('css'),
				prohost_get_vc_param('margin_top'),
				prohost_get_vc_param('margin_bottom'),
				prohost_get_vc_param('margin_left'),
				prohost_get_vc_param('margin_right')
			)
		) );
		
		class WPBakeryShortCode_Trx_Price extends PROHOST_VC_ShortCodeSingle {}
	}
}
?>