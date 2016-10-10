<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_infobox_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_infobox_theme_setup' );
	function prohost_sc_infobox_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_infobox_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_sc_infobox_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_infobox id="unique_id" style="regular|info|success|error|result" static="0|1"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_infobox]
*/

if (!function_exists('prohost_sc_infobox')) {	
	function prohost_sc_infobox($atts, $content=null){	
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "regular",
			"closeable" => "no",
			"icon" => "",
			"color" => "",
			"bg_color" => "",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . prohost_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= ($color !== '' ? 'color:' . esc_attr($color) .';' : '')
			. ($bg_color !== '' ? 'background-color:' . esc_attr($bg_color) .';' : '');
		if (empty($icon)) {
			if ($icon=='none')
				$icon = '';
			else if ($style=='regular')
				$icon = 'icon-cog';
			else if ($style=='success')
				$icon = 'icon-check';
			else if ($style=='error')
				$icon = 'icon-attention';
			else if ($style=='info')
				$icon = 'icon-info';
		}
		$content = do_shortcode($content);
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_infobox sc_infobox_style_' . esc_attr($style) 
					. (prohost_param_is_on($closeable) ? ' sc_infobox_closeable' : '') 
					. (!empty($class) ? ' '.esc_attr($class) : '') 
					. ($icon!='' && !prohost_param_is_inherit($icon) ? ' sc_infobox_iconed '. esc_attr($icon) : '') 
					. '"'
				. (!prohost_param_is_off($animation) ? ' data-animation="'.esc_attr(prohost_get_animation_classes($animation)).'"' : '')
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. '>'
				. trim($content)
				. '</div>';
		return apply_filters('prohost_shortcode_output', $output, 'trx_infobox', $atts, $content);
	}
	prohost_require_shortcode('trx_infobox', 'prohost_sc_infobox');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_infobox_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_infobox_reg_shortcodes');
	function prohost_sc_infobox_reg_shortcodes() {
	
		prohost_sc_map("trx_infobox", array(
			"title" => esc_html__("Infobox", "prohost"),
			"desc" => wp_kses_data( __("Insert infobox into your post (page)", "prohost") ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"style" => array(
					"title" => esc_html__("Style", "prohost"),
					"desc" => wp_kses_data( __("Infobox style", "prohost") ),
					"value" => "regular",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => array(
						'regular' => esc_html__('Regular', 'prohost'),
						'info' => esc_html__('Info', 'prohost'),
						'success' => esc_html__('Success', 'prohost'),
						'error' => esc_html__('Error', 'prohost')
					)
				),
				"closeable" => array(
					"title" => esc_html__("Closeable box", "prohost"),
					"desc" => wp_kses_data( __("Create closeable box (with close button)", "prohost") ),
					"value" => "no",
					"type" => "switch",
					"options" => prohost_get_sc_param('yes_no')
				),
				"icon" => array(
					"title" => esc_html__("Custom icon",  'prohost'),
					"desc" => wp_kses_data( __('Select icon for the infobox from Fontello icons set. If empty - use default icon',  'prohost') ),
					"value" => "",
					"type" => "icons",
					"options" => prohost_get_sc_param('icons')
				),
				"color" => array(
					"title" => esc_html__("Text color", "prohost"),
					"desc" => wp_kses_data( __("Any color for text and headers", "prohost") ),
					"value" => "",
					"type" => "color"
				),
				"bg_color" => array(
					"title" => esc_html__("Background color", "prohost"),
					"desc" => wp_kses_data( __("Any background color for this infobox", "prohost") ),
					"value" => "",
					"type" => "color"
				),
				"_content_" => array(
					"title" => esc_html__("Infobox content", "prohost"),
					"desc" => wp_kses_data( __("Content for infobox", "prohost") ),
					"divider" => true,
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
				),
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
if ( !function_exists( 'prohost_sc_infobox_reg_shortcodes_vc' ) ) {
	//add_action('prohost_action_shortcodes_list_vc', 'prohost_sc_infobox_reg_shortcodes_vc');
	function prohost_sc_infobox_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_infobox",
			"name" => esc_html__("Infobox", "prohost"),
			"description" => wp_kses_data( __("Box with info or error message", "prohost") ),
			"category" => esc_html__('Content', 'prohost'),
			'icon' => 'icon_trx_infobox',
			"class" => "trx_sc_container trx_sc_infobox",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "style",
					"heading" => esc_html__("Style", "prohost"),
					"description" => wp_kses_data( __("Infobox style", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
							esc_html__('Regular', 'prohost') => 'regular',
							esc_html__('Info', 'prohost') => 'info',
							esc_html__('Success', 'prohost') => 'success',
							esc_html__('Error', 'prohost') => 'error',
							esc_html__('Result', 'prohost') => 'result'
						),
					"type" => "dropdown"
				),
				array(
					"param_name" => "closeable",
					"heading" => esc_html__("Closeable", "prohost"),
					"description" => wp_kses_data( __("Create closeable box (with close button)", "prohost") ),
					"class" => "",
					"value" => array(esc_html__('Close button', 'prohost') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Custom icon", "prohost"),
					"description" => wp_kses_data( __("Select icon for the infobox from Fontello icons set. If empty - use default icon", "prohost") ),
					"class" => "",
					"value" => prohost_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Text color", "prohost"),
					"description" => wp_kses_data( __("Any color for the text and headers", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_color",
					"heading" => esc_html__("Background color", "prohost"),
					"description" => wp_kses_data( __("Any background color for this infobox", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				/*
				array(
					"param_name" => "content",
					"heading" => esc_html__("Message text", "prohost"),
					"description" => wp_kses_data( __("Message for the infobox", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "textarea_html"
				),
				*/
				prohost_get_vc_param('id'),
				prohost_get_vc_param('class'),
				prohost_get_vc_param('animation'),
				prohost_get_vc_param('css'),
				prohost_get_vc_param('margin_top'),
				prohost_get_vc_param('margin_bottom'),
				prohost_get_vc_param('margin_left'),
				prohost_get_vc_param('margin_right')
			),
			'js_view' => 'VcTrxTextContainerView'
		) );
		
		class WPBakeryShortCode_Trx_Infobox extends PROHOST_VC_ShortCodeContainer {}
	}
}
?>