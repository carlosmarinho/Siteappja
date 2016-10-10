<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_popup_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_popup_theme_setup' );
	function prohost_sc_popup_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_popup_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_sc_popup_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_popup id="unique_id" class="class_name" style="css_styles"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_popup]
*/

if (!function_exists('prohost_sc_popup')) {	
	function prohost_sc_popup($atts, $content=null){	
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
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
		prohost_enqueue_popup('magnific');
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_popup mfp-with-anim mfp-hide' . ($class ? ' '.esc_attr($class) : '') . '"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. '>' 
				. do_shortcode($content) 
				. '</div>';
		return apply_filters('prohost_shortcode_output', $output, 'trx_popup', $atts, $content);
	}
	prohost_require_shortcode('trx_popup', 'prohost_sc_popup');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_popup_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_popup_reg_shortcodes');
	function prohost_sc_popup_reg_shortcodes() {
	
		prohost_sc_map("trx_popup", array(
			"title" => esc_html__("Popup window", "prohost"),
			"desc" => wp_kses_data( __("Container for any html-block with desired class and style for popup window", "prohost") ),
			"decorate" => true,
			"container" => true,
			"params" => array(
				"_content_" => array(
					"title" => esc_html__("Container content", "prohost"),
					"desc" => wp_kses_data( __("Content for section container", "prohost") ),
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
				"css" => prohost_get_sc_param('css')
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_popup_reg_shortcodes_vc' ) ) {
	//add_action('prohost_action_shortcodes_list_vc', 'prohost_sc_popup_reg_shortcodes_vc');
	function prohost_sc_popup_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_popup",
			"name" => esc_html__("Popup window", "prohost"),
			"description" => wp_kses_data( __("Container for any html-block with desired class and style for popup window", "prohost") ),
			"category" => esc_html__('Content', 'prohost'),
			'icon' => 'icon_trx_popup',
			"class" => "trx_sc_collection trx_sc_popup",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => true,
			"params" => array(
				/*
				array(
					"param_name" => "content",
					"heading" => esc_html__("Container content", "prohost"),
					"description" => wp_kses_data( __("Content for popup container", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "textarea_html"
				),
				*/
				prohost_get_vc_param('id'),
				prohost_get_vc_param('class'),
				prohost_get_vc_param('css'),
				prohost_get_vc_param('margin_top'),
				prohost_get_vc_param('margin_bottom'),
				prohost_get_vc_param('margin_left'),
				prohost_get_vc_param('margin_right')
			)
		) );
		
		class WPBakeryShortCode_Trx_Popup extends PROHOST_VC_ShortCodeCollection {}
	}
}
?>