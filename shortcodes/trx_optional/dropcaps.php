<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_dropcaps_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_dropcaps_theme_setup' );
	function prohost_sc_dropcaps_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_dropcaps_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_sc_dropcaps_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

//[trx_dropcaps id="unique_id" style="1-6"]paragraph text[/trx_dropcaps]

if (!function_exists('prohost_sc_dropcaps')) {	
	function prohost_sc_dropcaps($atts, $content=null){
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "1",
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
		$css .= prohost_get_css_dimensions_from_values($width, $height);
		$style = min(3, max(1, $style));
		$content = do_shortcode(str_replace(array('[vc_column_text]', '[/vc_column_text]'), array('', ''), $content));
		$output = prohost_substr($content, 0, 1) == '<' 
			? $content 
			: '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_dropcaps sc_dropcaps_style_' . esc_attr($style) . (!empty($class) ? ' '.esc_attr($class) : '') . '"'
				. ($css ? ' style="'.esc_attr($css).'"' : '')
				. (!prohost_param_is_off($animation) ? ' data-animation="'.esc_attr(prohost_get_animation_classes($animation)).'"' : '')
				. '>' 
					. '<span class="sc_dropcaps_item">' . trim(prohost_substr($content, 0, 1)) . '</span>' . trim(prohost_substr($content, 1))
			. '</div>';
		return apply_filters('prohost_shortcode_output', $output, 'trx_dropcaps', $atts, $content);
	}
	prohost_require_shortcode('trx_dropcaps', 'prohost_sc_dropcaps');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_dropcaps_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_dropcaps_reg_shortcodes');
	function prohost_sc_dropcaps_reg_shortcodes() {
	
		prohost_sc_map("trx_dropcaps", array(
			"title" => esc_html__("Dropcaps", "prohost"),
			"desc" => wp_kses_data( __("Make first letter as dropcaps", "prohost") ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"style" => array(
					"title" => esc_html__("Style", "prohost"),
					"desc" => wp_kses_data( __("Dropcaps style", "prohost") ),
					"value" => "1",
					"type" => "checklist",
					"options" => prohost_get_list_styles(1, 3)
				),
				"_content_" => array(
					"title" => esc_html__("Paragraph content", "prohost"),
					"desc" => wp_kses_data( __("Paragraph with dropcaps content", "prohost") ),
					"divider" => true,
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
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
if ( !function_exists( 'prohost_sc_dropcaps_reg_shortcodes_vc' ) ) {
	//add_action('prohost_action_shortcodes_list_vc', 'prohost_sc_dropcaps_reg_shortcodes_vc');
	function prohost_sc_dropcaps_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_dropcaps",
			"name" => esc_html__("Dropcaps", "prohost"),
			"description" => wp_kses_data( __("Make first letter of the text as dropcaps", "prohost") ),
			"category" => esc_html__('Content', 'prohost'),
			'icon' => 'icon_trx_dropcaps',
			"class" => "trx_sc_container trx_sc_dropcaps",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "style",
					"heading" => esc_html__("Style", "prohost"),
					"description" => wp_kses_data( __("Dropcaps style", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(prohost_get_list_styles(1, 3)),
					"type" => "dropdown"
				),
/*
				array(
					"param_name" => "content",
					"heading" => esc_html__("Paragraph text", "prohost"),
					"description" => wp_kses_data( __("Paragraph with dropcaps content", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "textarea_html"
				),
*/
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
		
		class WPBakeryShortCode_Trx_Dropcaps extends PROHOST_VC_ShortCodeContainer {}
	}
}
?>