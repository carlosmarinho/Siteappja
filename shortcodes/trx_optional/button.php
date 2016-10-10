<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_button_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_button_theme_setup' );
	function prohost_sc_button_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_button_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_sc_button_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_button id="unique_id" type="square|round" fullsize="0|1" style="global|light|dark" size="mini|medium|big|huge|banner" icon="icon-name" link='#' target='']Button caption[/trx_button]
*/

if (!function_exists('prohost_sc_button')) {	
	function prohost_sc_button($atts, $content=null){	
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"type" => "square",
			"style" => "filled",
			"size" => "small",
			"icon" => "",
			"color" => "",
			"bg_color" => "",
			"link" => "",
			"target" => "",
			"align" => "",
			"rel" => "",
			"popup" => "no",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"width" => "",
			"height" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . prohost_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= prohost_get_css_dimensions_from_values($width, $height)
			. ($color !== '' ? 'color:' . esc_attr($color) .';' : '')
			. ($bg_color !== '' ? 'background-color:' . esc_attr($bg_color) . '; border-color:'. esc_attr($bg_color) .';' : '');
		if (prohost_param_is_on($popup)) prohost_enqueue_popup('magnific');
		$output = '<a href="' . (empty($link) ? '#' : $link) . '"'
			. (!empty($target) ? ' target="'.esc_attr($target).'"' : '')
			. (!empty($rel) ? ' rel="'.esc_attr($rel).'"' : '')
			. (!prohost_param_is_off($animation) ? ' data-animation="'.esc_attr(prohost_get_animation_classes($animation)).'"' : '')
			. ' class="sc_button sc_button_' . esc_attr($type) 
					. ' sc_button_style_' . esc_attr($style) 
					. ' sc_button_size_' . esc_attr($size)
					. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
					. (!empty($class) ? ' '.esc_attr($class) : '')
					. ($icon!='' ? '  sc_button_iconed '. esc_attr($icon) : '') 
					. (prohost_param_is_on($popup) ? ' sc_popup_link' : '') 
					. '"'
			. ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
			. '>'
			. do_shortcode($content)
			. '</a>';
		return apply_filters('prohost_shortcode_output', $output, 'trx_button', $atts, $content);
	}
	prohost_require_shortcode('trx_button', 'prohost_sc_button');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_button_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_button_reg_shortcodes');
	function prohost_sc_button_reg_shortcodes() {
	
		prohost_sc_map("trx_button", array(
			"title" => esc_html__("Button", "prohost"),
			"desc" => wp_kses_data( __("Button with link", "prohost") ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"_content_" => array(
					"title" => esc_html__("Caption", "prohost"),
					"desc" => wp_kses_data( __("Button caption", "prohost") ),
					"value" => "",
					"type" => "text"
				),
				"type" => array(
					"title" => esc_html__("Button's shape", "prohost"),
					"desc" => wp_kses_data( __("Select button's shape", "prohost") ),
					"value" => "square",
					"size" => "medium",
					"options" => array(
						'square' => esc_html__('Square', 'prohost'),
						'round' => esc_html__('Round', 'prohost')
					),
					"type" => "switch"
				), 
				"style" => array(
					"title" => esc_html__("Button's style", "prohost"),
					"desc" => wp_kses_data( __("Select button's style", "prohost") ),
					"value" => "default",
					"dir" => "horizontal",
					"options" => array(
						'filled' => esc_html__('Filled style 1', 'prohost'),
						'filled_2' => esc_html__('Filled style 2', 'prohost'),
						'border' => esc_html__('Border', 'prohost')
					),
					"type" => "checklist"
				), 
				"size" => array(
					"title" => esc_html__("Button's size", "prohost"),
					"desc" => wp_kses_data( __("Select button's size", "prohost") ),
					"value" => "small",
					"dir" => "horizontal",
					"options" => array(
						'small' => esc_html__('Small', 'prohost'),
						'medium' => esc_html__('Medium', 'prohost'),
						'large' => esc_html__('Large', 'prohost')
					),
					"type" => "checklist"
				), 
				"icon" => array(
					"title" => esc_html__("Button's icon",  'prohost'),
					"desc" => wp_kses_data( __('Select icon for the title from Fontello icons set',  'prohost') ),
					"value" => "",
					"type" => "icons",
					"options" => prohost_get_sc_param('icons')
				),
				"color" => array(
					"title" => esc_html__("Button's text color", "prohost"),
					"desc" => wp_kses_data( __("Any color for button's caption", "prohost") ),
					"std" => "",
					"value" => "",
					"type" => "color"
				),
				"bg_color" => array(
					"title" => esc_html__("Button's backcolor", "prohost"),
					"desc" => wp_kses_data( __("Any color for button's background", "prohost") ),
					"value" => "",
					"type" => "color"
				),
				"align" => array(
					"title" => esc_html__("Button's alignment", "prohost"),
					"desc" => wp_kses_data( __("Align button to left, center or right", "prohost") ),
					"value" => "none",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => prohost_get_sc_param('align')
				), 
				"link" => array(
					"title" => esc_html__("Link URL", "prohost"),
					"desc" => wp_kses_data( __("URL for link on button click", "prohost") ),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
				"target" => array(
					"title" => esc_html__("Link target", "prohost"),
					"desc" => wp_kses_data( __("Target for link on button click", "prohost") ),
					"dependency" => array(
						'link' => array('not_empty')
					),
					"value" => "",
					"type" => "text"
				),
				"popup" => array(
					"title" => esc_html__("Open link in popup", "prohost"),
					"desc" => wp_kses_data( __("Open link target in popup window", "prohost") ),
					"dependency" => array(
						'link' => array('not_empty')
					),
					"value" => "no",
					"type" => "switch",
					"options" => prohost_get_sc_param('yes_no')
				), 
				"rel" => array(
					"title" => esc_html__("Rel attribute", "prohost"),
					"desc" => wp_kses_data( __("Rel attribute for button's link (if need)", "prohost") ),
					"dependency" => array(
						'link' => array('not_empty')
					),
					"value" => "",
					"type" => "text"
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
if ( !function_exists( 'prohost_sc_button_reg_shortcodes_vc' ) ) {
	//add_action('prohost_action_shortcodes_list_vc', 'prohost_sc_button_reg_shortcodes_vc');
	function prohost_sc_button_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_button",
			"name" => esc_html__("Button", "prohost"),
			"description" => wp_kses_data( __("Button with link", "prohost") ),
			"category" => esc_html__('Content', 'prohost'),
			'icon' => 'icon_trx_button',
			"class" => "trx_sc_single trx_sc_button",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "content",
					"heading" => esc_html__("Caption", "prohost"),
					"description" => wp_kses_data( __("Button caption", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "type",
					"heading" => esc_html__("Button's shape", "prohost"),
					"description" => wp_kses_data( __("Select button's shape", "prohost") ),
					"class" => "",
					"value" => array(
						esc_html__('Square', 'prohost') => 'square',
						esc_html__('Round', 'prohost') => 'round'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "style",
					"heading" => esc_html__("Button's style", "prohost"),
					"description" => wp_kses_data( __("Select button's style", "prohost") ),
					"class" => "",
					"value" => array(
						esc_html__('Filled style 1', 'prohost') => 'filled',
						esc_html__('Filled style 2', 'prohost') => 'filled_2',
						esc_html__('Border', 'prohost') => 'border'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "size",
					"heading" => esc_html__("Button's size", "prohost"),
					"description" => wp_kses_data( __("Select button's size", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
						esc_html__('Small', 'prohost') => 'small',
						esc_html__('Medium', 'prohost') => 'medium',
						esc_html__('Large', 'prohost') => 'large'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Button's icon", "prohost"),
					"description" => wp_kses_data( __("Select icon for the title from Fontello icons set", "prohost") ),
					"class" => "",
					"value" => prohost_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Button's text color", "prohost"),
					"description" => wp_kses_data( __("Any color for button's caption", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_color",
					"heading" => esc_html__("Button's backcolor", "prohost"),
					"description" => wp_kses_data( __("Any color for button's background", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Button's alignment", "prohost"),
					"description" => wp_kses_data( __("Align button to left, center or right", "prohost") ),
					"class" => "",
					"value" => array_flip(prohost_get_sc_param('align')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Link URL", "prohost"),
					"description" => wp_kses_data( __("URL for the link on button click", "prohost") ),
					"class" => "",
					"group" => esc_html__('Link', 'prohost'),
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "target",
					"heading" => esc_html__("Link target", "prohost"),
					"description" => wp_kses_data( __("Target for the link on button click", "prohost") ),
					"class" => "",
					"group" => esc_html__('Link', 'prohost'),
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "popup",
					"heading" => esc_html__("Open link in popup", "prohost"),
					"description" => wp_kses_data( __("Open link target in popup window", "prohost") ),
					"class" => "",
					"group" => esc_html__('Link', 'prohost'),
					"value" => array(esc_html__('Open in popup', 'prohost') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "rel",
					"heading" => esc_html__("Rel attribute", "prohost"),
					"description" => wp_kses_data( __("Rel attribute for the button's link (if need", "prohost") ),
					"class" => "",
					"group" => esc_html__('Link', 'prohost'),
					"value" => "",
					"type" => "textfield"
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
			),
			'js_view' => 'VcTrxTextView'
		) );
		
		class WPBakeryShortCode_Trx_Button extends PROHOST_VC_ShortCodeSingle {}
	}
}
?>