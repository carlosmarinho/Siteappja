<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_googlemap_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_googlemap_theme_setup' );
	function prohost_sc_googlemap_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_googlemap_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_sc_googlemap_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

//[trx_googlemap id="unique_id" width="width_in_pixels_or_percent" height="height_in_pixels"]
//	[trx_googlemap_marker address="your_address"]
//[/trx_googlemap]

if (!function_exists('prohost_sc_googlemap')) {	
	function prohost_sc_googlemap($atts, $content = null) {
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"zoom" => 16,
			"style" => 'default',
			"scheme" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"width" => "100%",
			"height" => "400",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . prohost_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= prohost_get_css_dimensions_from_values($width, $height);
		if (empty($id)) $id = 'sc_googlemap_'.str_replace('.', '', mt_rand());
		if (empty($style)) $style = prohost_get_custom_option('googlemap_style');
        $api_key = prohost_get_theme_option('api_google');
        prohost_enqueue_script( 'googlemap', prohost_get_protocol().'://maps.google.com/maps/api/js'.($api_key ? '?key='.$api_key : ''), array(), null, true );
		prohost_enqueue_script( 'prohost-googlemap-script', prohost_get_file_url('js/core.googlemap.js'), array(), null, true );
		prohost_storage_set('sc_googlemap_markers', array());
		$content = do_shortcode($content);
		$output = '';
		$markers = prohost_storage_get('sc_googlemap_markers');
		if (count($markers) == 0) {
			$markers[] = array(
				'title' => prohost_get_custom_option('googlemap_title'),
				'description' => prohost_strmacros(prohost_get_custom_option('googlemap_description')),
				'latlng' => prohost_get_custom_option('googlemap_latlng'),
				'address' => prohost_get_custom_option('googlemap_address'),
				'point' => prohost_get_custom_option('googlemap_marker')
			);
		}
		$output .= 
			($content ? '<div id="'.esc_attr($id).'_wrap" class="sc_googlemap_wrap'
					. ($scheme && !prohost_param_is_off($scheme) && !prohost_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '') 
					. '">' : '')
			. '<div id="'.esc_attr($id).'"'
				. ' class="sc_googlemap'. (!empty($class) ? ' '.esc_attr($class) : '').'"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
				. (!prohost_param_is_off($animation) ? ' data-animation="'.esc_attr(prohost_get_animation_classes($animation)).'"' : '')
				. ' data-zoom="'.esc_attr($zoom).'"'
				. ' data-style="'.esc_attr($style).'"'
				. '>';
		$cnt = 0;
		foreach ($markers as $marker) {
			$cnt++;
			if (empty($marker['id'])) $marker['id'] = $id.'_'.intval($cnt);
			$output .= '<div id="'.esc_attr($marker['id']).'" class="sc_googlemap_marker"'
				. ' data-title="'.esc_attr($marker['title']).'"'
				. ' data-description="'.esc_attr(prohost_strmacros($marker['description'])).'"'
				. ' data-address="'.esc_attr($marker['address']).'"'
				. ' data-latlng="'.esc_attr($marker['latlng']).'"'
				. ' data-point="'.esc_attr($marker['point']).'"'
				. '></div>';
		}
		$output .= '</div>'
			. ($content ? '<div class="sc_googlemap_content">' . trim($content) . '</div></div>' : '');
			
		return apply_filters('prohost_shortcode_output', $output, 'trx_googlemap', $atts, $content);
	}
	prohost_require_shortcode("trx_googlemap", "prohost_sc_googlemap");
}


if (!function_exists('prohost_sc_googlemap_marker')) {	
	function prohost_sc_googlemap_marker($atts, $content = null) {
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"address" => "",
			"latlng" => "",
			"point" => "",
			// Common params
			"id" => ""
		), $atts)));
		if (!empty($point)) {
			if ($point > 0) {
				$attach = wp_get_attachment_image_src( $point, 'full' );
				if (isset($attach[0]) && $attach[0]!='')
					$point = $attach[0];
			}
		}
		prohost_storage_set_array('sc_googlemap_markers', '', array(
			'id' => $id,
			'title' => $title,
			'description' => do_shortcode($content),
			'latlng' => $latlng,
			'address' => $address,
			'point' => $point ? $point : prohost_get_custom_option('googlemap_marker')
			)
		);
		return '';
	}
	prohost_require_shortcode("trx_googlemap_marker", "prohost_sc_googlemap_marker");
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_googlemap_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_googlemap_reg_shortcodes');
	function prohost_sc_googlemap_reg_shortcodes() {
	
		prohost_sc_map("trx_googlemap", array(
			"title" => esc_html__("Google map", "prohost"),
			"desc" => wp_kses_data( __("Insert Google map with specified markers", "prohost") ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"zoom" => array(
					"title" => esc_html__("Zoom", "prohost"),
					"desc" => wp_kses_data( __("Map zoom factor", "prohost") ),
					"divider" => true,
					"value" => 16,
					"min" => 1,
					"max" => 20,
					"type" => "spinner"
				),
				"style" => array(
					"title" => esc_html__("Map style", "prohost"),
					"desc" => wp_kses_data( __("Select map style", "prohost") ),
					"value" => "default",
					"type" => "checklist",
					"options" => prohost_get_sc_param('googlemap_styles')
				),
				"scheme" => array(
					"title" => esc_html__("Color scheme", "prohost"),
					"desc" => wp_kses_data( __("Select color scheme for this block", "prohost") ),
					"value" => "",
					"type" => "checklist",
					"options" => prohost_get_sc_param('schemes')
				),
				"width" => prohost_shortcodes_width('100%'),
				"height" => prohost_shortcodes_height(240),
				"top" => prohost_get_sc_param('top'),
				"bottom" => prohost_get_sc_param('bottom'),
				"left" => prohost_get_sc_param('left'),
				"right" => prohost_get_sc_param('right'),
				"id" => prohost_get_sc_param('id'),
				"class" => prohost_get_sc_param('class'),
				"animation" => prohost_get_sc_param('animation'),
				"css" => prohost_get_sc_param('css')
			),
			"children" => array(
				"name" => "trx_googlemap_marker",
				"title" => esc_html__("Google map marker", "prohost"),
				"desc" => wp_kses_data( __("Google map marker", "prohost") ),
				"decorate" => false,
				"container" => true,
				"params" => array(
					"address" => array(
						"title" => esc_html__("Address", "prohost"),
						"desc" => wp_kses_data( __("Address of this marker", "prohost") ),
						"value" => "",
						"type" => "text"
					),
					"latlng" => array(
						"title" => esc_html__("Latitude and Longitude", "prohost"),
						"desc" => wp_kses_data( __("Comma separated marker's coorditanes (instead Address)", "prohost") ),
						"value" => "",
						"type" => "text"
					),
					"point" => array(
						"title" => esc_html__("URL for marker image file", "prohost"),
						"desc" => wp_kses_data( __("Select or upload image or write URL from other site for this marker. If empty - use default marker", "prohost") ),
						"readonly" => false,
						"value" => "",
						"type" => "media"
					),
					"title" => array(
						"title" => esc_html__("Title", "prohost"),
						"desc" => wp_kses_data( __("Title for this marker", "prohost") ),
						"value" => "",
						"type" => "text"
					),
					"_content_" => array(
						"title" => esc_html__("Description", "prohost"),
						"desc" => wp_kses_data( __("Description for this marker", "prohost") ),
						"rows" => 4,
						"value" => "",
						"type" => "textarea"
					),
					"id" => prohost_get_sc_param('id')
				)
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_googlemap_reg_shortcodes_vc' ) ) {
	//add_action('prohost_action_shortcodes_list_vc', 'prohost_sc_googlemap_reg_shortcodes_vc');
	function prohost_sc_googlemap_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_googlemap",
			"name" => esc_html__("Google map", "prohost"),
			"description" => wp_kses_data( __("Insert Google map with desired address or coordinates", "prohost") ),
			"category" => esc_html__('Content', 'prohost'),
			'icon' => 'icon_trx_googlemap',
			"class" => "trx_sc_collection trx_sc_googlemap",
			"content_element" => true,
			"is_container" => true,
			"as_parent" => array('only' => 'trx_googlemap_marker,trx_form,trx_section,trx_block,trx_promo'),
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "zoom",
					"heading" => esc_html__("Zoom", "prohost"),
					"description" => wp_kses_data( __("Map zoom factor", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "16",
					"type" => "textfield"
				),
				array(
					"param_name" => "style",
					"heading" => esc_html__("Style", "prohost"),
					"description" => wp_kses_data( __("Map custom style", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(prohost_get_sc_param('googlemap_styles')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "scheme",
					"heading" => esc_html__("Color scheme", "prohost"),
					"description" => wp_kses_data( __("Select color scheme for this block", "prohost") ),
					"class" => "",
					"value" => array_flip(prohost_get_sc_param('schemes')),
					"type" => "dropdown"
				),
				prohost_get_vc_param('id'),
				prohost_get_vc_param('class'),
				prohost_get_vc_param('animation'),
				prohost_get_vc_param('css'),
				prohost_vc_width('100%'),
				prohost_vc_height(240),
				prohost_get_vc_param('margin_top'),
				prohost_get_vc_param('margin_bottom'),
				prohost_get_vc_param('margin_left'),
				prohost_get_vc_param('margin_right')
			)
		) );
		
		vc_map( array(
			"base" => "trx_googlemap_marker",
			"name" => esc_html__("Googlemap marker", "prohost"),
			"description" => wp_kses_data( __("Insert new marker into Google map", "prohost") ),
			"class" => "trx_sc_collection trx_sc_googlemap_marker",
			'icon' => 'icon_trx_googlemap_marker',
			//"allowed_container_element" => 'vc_row',
			"show_settings_on_create" => true,
			"content_element" => true,
			"is_container" => true,
			"as_child" => array('only' => 'trx_googlemap'), // Use only|except attributes to limit parent (separate multiple values with comma)
			"params" => array(
				array(
					"param_name" => "address",
					"heading" => esc_html__("Address", "prohost"),
					"description" => wp_kses_data( __("Address of this marker", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "latlng",
					"heading" => esc_html__("Latitude and Longitude", "prohost"),
					"description" => wp_kses_data( __("Comma separated marker's coorditanes (instead Address)", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", "prohost"),
					"description" => wp_kses_data( __("Title for this marker", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "point",
					"heading" => esc_html__("URL for marker image file", "prohost"),
					"description" => wp_kses_data( __("Select or upload image or write URL from other site for this marker. If empty - use default marker", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				prohost_get_vc_param('id')
			)
		) );
		
		class WPBakeryShortCode_Trx_Googlemap extends PROHOST_VC_ShortCodeCollection {}
		class WPBakeryShortCode_Trx_Googlemap_Marker extends PROHOST_VC_ShortCodeCollection {}
	}
}
?>