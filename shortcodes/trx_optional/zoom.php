<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_zoom_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_zoom_theme_setup' );
	function prohost_sc_zoom_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_zoom_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_sc_zoom_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_zoom id="unique_id" border="none|light|dark"]
*/

if (!function_exists('prohost_sc_zoom')) {	
	function prohost_sc_zoom($atts, $content=null){	
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"effect" => "zoom",
			"src" => "",
			"url" => "",
			"over" => "",
			"align" => "",
			"bg_image" => "",
			"bg_top" => '',
			"bg_bottom" => '',
			"bg_left" => '',
			"bg_right" => '',
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"width" => "",
			"height" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
	
		prohost_enqueue_script( 'prohost-elevate-zoom-script', prohost_get_file_url('js/jquery.elevateZoom-3.0.4.js'), array(), null, true );
	
		$class .= ($class ? ' ' : '') . prohost_get_css_position_as_classes($top, $right, $bottom, $left);
		$css_dim = prohost_get_css_dimensions_from_values($width, $height);
		$css_bg = prohost_get_css_paddings_from_values($bg_top, $bg_right, $bg_bottom, $bg_left);
		$width  = prohost_prepare_css_value($width);
		$height = prohost_prepare_css_value($height);
		if (empty($id)) $id = 'sc_zoom_'.str_replace('.', '', mt_rand());
		$src = $src!='' ? $src : $url;
		if ($src > 0) {
			$attach = wp_get_attachment_image_src( $src, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$src = $attach[0];
		}
		if ($over > 0) {
			$attach = wp_get_attachment_image_src( $over, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$over = $attach[0];
		}
		if ($effect=='lens' && ((int) $width > 0 && prohost_substr($width, -2, 2)=='px') || ((int) $height > 0 && prohost_substr($height, -2, 2)=='px')) {
			if ($src)
				$src = prohost_get_resized_image_url($src, (int) $width > 0 && prohost_substr($width, -2, 2)=='px' ? (int) $width : null, (int) $height > 0 && prohost_substr($height, -2, 2)=='px' ? (int) $height : null);
			if ($over)
				$over = prohost_get_resized_image_url($over, (int) $width > 0 && prohost_substr($width, -2, 2)=='px' ? (int) $width : null, (int) $height > 0 && prohost_substr($height, -2, 2)=='px' ? (int) $height : null);
		}
		if ($bg_image > 0) {
			$attach = wp_get_attachment_image_src( $bg_image, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$bg_image = $attach[0];
		}
		if ($bg_image) {
			$css_bg .= $css . 'background-image: url('.esc_url($bg_image).');';
			$css = $css_dim;
		} else {
			$css .= $css_dim;
		}
		$output = empty($src) 
				? '' 
				: (
					(!empty($bg_image) 
						? '<div class="sc_zoom_wrap'
								. (!empty($class) ? ' '.esc_attr($class) : '')
								. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
								. '"'
							. (!prohost_param_is_off($animation) ? ' data-animation="'.esc_attr(prohost_get_animation_classes($animation)).'"' : '')
							. ($css_bg!='' ? ' style="'.esc_attr($css_bg).'"' : '') 
							. '>' 
						: '')
					.'<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
						. ' class="sc_zoom' 
								. (empty($bg_image) && !empty($class) ? ' '.esc_attr($class) : '') 
								. (empty($bg_image) && $align && $align!='none' ? ' align'.esc_attr($align) : '')
								. '"'
							. (empty($bg_image) && !prohost_param_is_off($animation) ? ' data-animation="'.esc_attr(prohost_get_animation_classes($animation)).'"' : '')
							. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
							. '>'
							. '<img src="'.esc_url($src).'"' . ($css_dim!='' ? ' style="'.esc_attr($css_dim).'"' : '') . ' data-zoom-image="'.esc_url($over).'" alt="" />'
					. '</div>'
					. (!empty($bg_image) 
						? '</div>' 
						: '')
				);
		return apply_filters('prohost_shortcode_output', $output, 'trx_zoom', $atts, $content);
	}
	prohost_require_shortcode('trx_zoom', 'prohost_sc_zoom');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_zoom_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_zoom_reg_shortcodes');
	function prohost_sc_zoom_reg_shortcodes() {
	
		prohost_sc_map("trx_zoom", array(
			"title" => esc_html__("Zoom", "prohost"),
			"desc" => wp_kses_data( __("Insert the image with zoom/lens effect", "prohost") ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"effect" => array(
					"title" => esc_html__("Effect", "prohost"),
					"desc" => wp_kses_data( __("Select effect to display overlapping image", "prohost") ),
					"value" => "lens",
					"size" => "medium",
					"type" => "switch",
					"options" => array(
						"lens" => esc_html__('Lens', 'prohost'),
						"zoom" => esc_html__('Zoom', 'prohost')
					)
				),
				"url" => array(
					"title" => esc_html__("Main image", "prohost"),
					"desc" => wp_kses_data( __("Select or upload main image", "prohost") ),
					"readonly" => false,
					"value" => "",
					"type" => "media"
				),
				"over" => array(
					"title" => esc_html__("Overlaping image", "prohost"),
					"desc" => wp_kses_data( __("Select or upload overlaping image", "prohost") ),
					"readonly" => false,
					"value" => "",
					"type" => "media"
				),
				"align" => array(
					"title" => esc_html__("Float zoom", "prohost"),
					"desc" => wp_kses_data( __("Float zoom to left or right side", "prohost") ),
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => prohost_get_sc_param('float')
				), 
				"bg_image" => array(
					"title" => esc_html__("Background image", "prohost"),
					"desc" => wp_kses_data( __("Select or upload image or write URL from other site for zoom block background. Attention! If you use background image - specify paddings below from background margins to zoom block in percents!", "prohost") ),
					"divider" => true,
					"readonly" => false,
					"value" => "",
					"type" => "media"
				),
				"bg_top" => array(
					"title" => esc_html__("Top offset", "prohost"),
					"desc" => wp_kses_data( __("Top offset (padding) inside background image to zoom block (in percent). For example: 3%", "prohost") ),
					"dependency" => array(
						'bg_image' => array('not_empty')
					),
					"value" => "",
					"type" => "text"
				),
				"bg_bottom" => array(
					"title" => esc_html__("Bottom offset", "prohost"),
					"desc" => wp_kses_data( __("Bottom offset (padding) inside background image to zoom block (in percent). For example: 3%", "prohost") ),
					"dependency" => array(
						'bg_image' => array('not_empty')
					),
					"value" => "",
					"type" => "text"
				),
				"bg_left" => array(
					"title" => esc_html__("Left offset", "prohost"),
					"desc" => wp_kses_data( __("Left offset (padding) inside background image to zoom block (in percent). For example: 20%", "prohost") ),
					"dependency" => array(
						'bg_image' => array('not_empty')
					),
					"value" => "",
					"type" => "text"
				),
				"bg_right" => array(
					"title" => esc_html__("Right offset", "prohost"),
					"desc" => wp_kses_data( __("Right offset (padding) inside background image to zoom block (in percent). For example: 12%", "prohost") ),
					"dependency" => array(
						'bg_image' => array('not_empty')
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
if ( !function_exists( 'prohost_sc_zoom_reg_shortcodes_vc' ) ) {
	//add_action('prohost_action_shortcodes_list_vc', 'prohost_sc_zoom_reg_shortcodes_vc');
	function prohost_sc_zoom_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_zoom",
			"name" => esc_html__("Zoom", "prohost"),
			"description" => wp_kses_data( __("Insert the image with zoom/lens effect", "prohost") ),
			"category" => esc_html__('Content', 'prohost'),
			'icon' => 'icon_trx_zoom',
			"class" => "trx_sc_single trx_sc_zoom",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "effect",
					"heading" => esc_html__("Effect", "prohost"),
					"description" => wp_kses_data( __("Select effect to display overlapping image", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"std" => "zoom",
					"value" => array(
						esc_html__('Lens', 'prohost') => 'lens',
						esc_html__('Zoom', 'prohost') => 'zoom'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "url",
					"heading" => esc_html__("Main image", "prohost"),
					"description" => wp_kses_data( __("Select or upload main image", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "over",
					"heading" => esc_html__("Overlaping image", "prohost"),
					"description" => wp_kses_data( __("Select or upload overlaping image", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", "prohost"),
					"description" => wp_kses_data( __("Float zoom to left or right side", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(prohost_get_sc_param('float')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "bg_image",
					"heading" => esc_html__("Background image", "prohost"),
					"description" => wp_kses_data( __("Select or upload image or write URL from other site for zoom background. Attention! If you use background image - specify paddings below from background margins to video block in percents!", "prohost") ),
					"group" => esc_html__('Background', 'prohost'),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "bg_top",
					"heading" => esc_html__("Top offset", "prohost"),
					"description" => wp_kses_data( __("Top offset (padding) from background image to zoom block (in percent). For example: 3%", "prohost") ),
					"group" => esc_html__('Background', 'prohost'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "bg_bottom",
					"heading" => esc_html__("Bottom offset", "prohost"),
					"description" => wp_kses_data( __("Bottom offset (padding) from background image to zoom block (in percent). For example: 3%", "prohost") ),
					"group" => esc_html__('Background', 'prohost'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "bg_left",
					"heading" => esc_html__("Left offset", "prohost"),
					"description" => wp_kses_data( __("Left offset (padding) from background image to zoom block (in percent). For example: 20%", "prohost") ),
					"group" => esc_html__('Background', 'prohost'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "bg_right",
					"heading" => esc_html__("Right offset", "prohost"),
					"description" => wp_kses_data( __("Right offset (padding) from background image to zoom block (in percent). For example: 12%", "prohost") ),
					"group" => esc_html__('Background', 'prohost'),
					"class" => "",
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
			)
		) );
		
		class WPBakeryShortCode_Trx_Zoom extends PROHOST_VC_ShortCodeSingle {}
	}
}
?>