<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_icon_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_icon_theme_setup' );
	function prohost_sc_icon_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_icon_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_sc_icon_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_icon id="unique_id" style='round|square' icon='' color="" bg_color="" size="" weight=""]
*/

if (!function_exists('prohost_sc_icon')) {	
	function prohost_sc_icon($atts, $content=null){	
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"icon" => "",
			"color" => "",
			"bg_color" => "",
			"bg_shape" => "",
			"font_size" => "",
			"font_weight" => "",
			"align" => "",
			"link" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . prohost_get_css_position_as_classes($top, $right, $bottom, $left);
		$css2 = ($font_weight != '' && !prohost_is_inherit_option($font_weight) ? 'font-weight:'. esc_attr($font_weight).';' : '')
			. ($font_size != '' ? 'font-size:' . esc_attr(prohost_prepare_css_value($font_size)) . '; line-height: ' . (!$bg_shape || prohost_param_is_inherit($bg_shape) ? '1' : '1.2') . 'em;' : '')
			. ($color != '' ? 'color:'.esc_attr($color).';' : '')
			. ($bg_color != '' ? 'background-color:'.esc_attr($bg_color).';border-color:'.esc_attr($bg_color).';' : '')
		;
		$output = $icon!='' 
			? ($link ? '<a href="'.esc_url($link).'"' : '<span') . ($id ? ' id="'.esc_attr($id).'"' : '')
				. ' class="sc_icon '.esc_attr($icon)
					. ($bg_shape && !prohost_param_is_inherit($bg_shape) ? ' sc_icon_shape_'.esc_attr($bg_shape) : '')
					. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
					. (!empty($class) ? ' '.esc_attr($class) : '')
				.'"'
				.($css || $css2 ? ' style="'.($class ? 'display:block;' : '') . ($css) . ($css2) . '"' : '')
				.'>'
				.($link ? '</a>' : '</span>')
			: '';
		return apply_filters('prohost_shortcode_output', $output, 'trx_icon', $atts, $content);
	}
	prohost_require_shortcode('trx_icon', 'prohost_sc_icon');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_icon_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_icon_reg_shortcodes');
	function prohost_sc_icon_reg_shortcodes() {
	
		prohost_sc_map("trx_icon", array(
			"title" => esc_html__("Icon", "prohost"),
			"desc" => wp_kses_data( __("Insert icon", "prohost") ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"icon" => array(
					"title" => esc_html__('Icon',  'prohost'),
					"desc" => wp_kses_data( __('Select font icon from the Fontello icons set',  'prohost') ),
					"value" => "",
					"type" => "icons",
					"options" => prohost_get_sc_param('icons')
				),
				"color" => array(
					"title" => esc_html__("Icon's color", "prohost"),
					"desc" => wp_kses_data( __("Icon's color", "prohost") ),
					"dependency" => array(
						'icon' => array('not_empty')
					),
					"value" => "",
					"type" => "color"
				),
				"bg_shape" => array(
					"title" => esc_html__("Background shape", "prohost"),
					"desc" => wp_kses_data( __("Shape of the icon background", "prohost") ),
					"dependency" => array(
						'icon' => array('not_empty')
					),
					"value" => "none",
					"type" => "radio",
					"options" => array(
						'none' => esc_html__('None', 'prohost'),
						'round' => esc_html__('Round', 'prohost'),
						'square' => esc_html__('Square', 'prohost')
					)
				),
				"bg_color" => array(
					"title" => esc_html__("Icon's background color", "prohost"),
					"desc" => wp_kses_data( __("Icon's background color", "prohost") ),
					"dependency" => array(
						'icon' => array('not_empty'),
						'background' => array('round','square')
					),
					"value" => "",
					"type" => "color"
				),
				"font_size" => array(
					"title" => esc_html__("Font size", "prohost"),
					"desc" => wp_kses_data( __("Icon's font size", "prohost") ),
					"dependency" => array(
						'icon' => array('not_empty')
					),
					"value" => "",
					"type" => "spinner",
					"min" => 8,
					"max" => 240
				),
				"font_weight" => array(
					"title" => esc_html__("Font weight", "prohost"),
					"desc" => wp_kses_data( __("Icon font weight", "prohost") ),
					"dependency" => array(
						'icon' => array('not_empty')
					),
					"value" => "",
					"type" => "select",
					"size" => "medium",
					"options" => array(
						'100' => esc_html__('Thin (100)', 'prohost'),
						'300' => esc_html__('Light (300)', 'prohost'),
						'400' => esc_html__('Normal (400)', 'prohost'),
						'700' => esc_html__('Bold (700)', 'prohost')
					)
				),
				"align" => array(
					"title" => esc_html__("Alignment", "prohost"),
					"desc" => wp_kses_data( __("Icon text alignment", "prohost") ),
					"dependency" => array(
						'icon' => array('not_empty')
					),
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => prohost_get_sc_param('align')
				), 
				"link" => array(
					"title" => esc_html__("Link URL", "prohost"),
					"desc" => wp_kses_data( __("Link URL from this icon (if not empty)", "prohost") ),
					"value" => "",
					"type" => "text"
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
if ( !function_exists( 'prohost_sc_icon_reg_shortcodes_vc' ) ) {
	//add_action('prohost_action_shortcodes_list_vc', 'prohost_sc_icon_reg_shortcodes_vc');
	function prohost_sc_icon_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_icon",
			"name" => esc_html__("Icon", "prohost"),
			"description" => wp_kses_data( __("Insert the icon", "prohost") ),
			"category" => esc_html__('Content', 'prohost'),
			'icon' => 'icon_trx_icon',
			"class" => "trx_sc_single trx_sc_icon",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Icon", "prohost"),
					"description" => wp_kses_data( __("Select icon class from Fontello icons set", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => prohost_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Text color", "prohost"),
					"description" => wp_kses_data( __("Icon's color", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_color",
					"heading" => esc_html__("Background color", "prohost"),
					"description" => wp_kses_data( __("Background color for the icon", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_shape",
					"heading" => esc_html__("Background shape", "prohost"),
					"description" => wp_kses_data( __("Shape of the icon background", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
						esc_html__('None', 'prohost') => 'none',
						esc_html__('Round', 'prohost') => 'round',
						esc_html__('Square', 'prohost') => 'square'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "font_size",
					"heading" => esc_html__("Font size", "prohost"),
					"description" => wp_kses_data( __("Icon's font size", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "font_weight",
					"heading" => esc_html__("Font weight", "prohost"),
					"description" => wp_kses_data( __("Icon's font weight", "prohost") ),
					"class" => "",
					"value" => array(
						esc_html__('Default', 'prohost') => 'inherit',
						esc_html__('Thin (100)', 'prohost') => '100',
						esc_html__('Light (300)', 'prohost') => '300',
						esc_html__('Normal (400)', 'prohost') => '400',
						esc_html__('Bold (700)', 'prohost') => '700'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Icon's alignment", "prohost"),
					"description" => wp_kses_data( __("Align icon to left, center or right", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(prohost_get_sc_param('align')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Link URL", "prohost"),
					"description" => wp_kses_data( __("Link URL from this icon (if not empty)", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				prohost_get_vc_param('id'),
				prohost_get_vc_param('class'),
				prohost_get_vc_param('css'),
				prohost_get_vc_param('margin_top'),
				prohost_get_vc_param('margin_bottom'),
				prohost_get_vc_param('margin_left'),
				prohost_get_vc_param('margin_right')
			),
		) );
		
		class WPBakeryShortCode_Trx_Icon extends PROHOST_VC_ShortCodeSingle {}
	}
}
?>