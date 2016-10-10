<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_image_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_image_theme_setup' );
	function prohost_sc_image_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_image_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_sc_image_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_image id="unique_id" src="image_url" width="width_in_pixels" height="height_in_pixels" title="image's_title" align="left|right"]
*/

if (!function_exists('prohost_sc_image')) {	
	function prohost_sc_image($atts, $content=null){	
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"align" => "",
			"shape" => "square",
			"src" => "",
			"url" => "",
			"icon" => "",
			"link" => "",
            "popup" => "no",
            // Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => "",
			"width" => "",
			"height" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . prohost_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= prohost_get_css_dimensions_from_values($width, $height);
		$src = $src!='' ? $src : $url;
		if ($src > 0) {
			$attach = wp_get_attachment_image_src( $src, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$src = $attach[0];
		}
		if (!empty($width) || !empty($height)) {
			$w = !empty($width) && strlen(intval($width)) == strlen($width) ? $width : null;
			$h = !empty($height) && strlen(intval($height)) == strlen($height) ? $height : null;
			if ($w || $h) $src = prohost_get_resized_image_url($src, $w, $h);
		}
        if (prohost_param_is_on($popup) && trim($link)) {
            prohost_enqueue_popup('magnific');
        } elseif(trim($link)) {
            prohost_enqueue_popup();
        };
        $output = empty($src) ? '' : ('<figure' . ($id ? ' id="'.esc_attr($id).'"' : '')
			. ' class="sc_image ' . ($align && $align!='none' ? ' align' . esc_attr($align) : '') . (!empty($shape) ? ' sc_image_shape_'.esc_attr($shape) : '') . (!empty($class) ? ' '.esc_attr($class) : '') . '"'
			. (!prohost_param_is_off($animation) ? ' data-animation="'.esc_attr(prohost_get_animation_classes($animation)).'"' : '')
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
			. '>'
            . (trim($link) ? '<a href="'.esc_url($link).'" class="' . (prohost_param_is_on($popup) ? ' sc_popup_link' : '') . '">' : '')
            . '<img src="'.esc_url($src).'" alt="" />'
            . (trim($link) ? (trim($title) || trim($icon) ? '<div class="figcaption"><span'.($icon ? ' class="'.esc_attr($icon).'"' : '').'></span> ' . ($title) . '</div>' : '') : '')
            . (trim($link) ? '</a>' : '')
            . ( trim($title) || trim($icon) ? '<figcaption><span'.($icon ? ' class="'.esc_attr($icon).'"' : '').'></span> ' . ($title) . '</figcaption>' : '')
			. '</figure>');
		return apply_filters('prohost_shortcode_output', $output, 'trx_image', $atts, $content);
	}
	prohost_require_shortcode('trx_image', 'prohost_sc_image');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_image_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_image_reg_shortcodes');
	function prohost_sc_image_reg_shortcodes() {
	
		prohost_sc_map("trx_image", array(
			"title" => esc_html__("Image", "prohost"),
			"desc" => wp_kses_data( __("Insert image into your post (page)", "prohost") ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"url" => array(
					"title" => esc_html__("URL for image file", "prohost"),
					"desc" => wp_kses_data( __("Select or upload image or write URL from other site", "prohost") ),
					"readonly" => false,
					"value" => "",
					"type" => "media",
					"before" => array(
						'sizes' => true		// If you want allow user select thumb size for image. Otherwise, thumb size is ignored - image fullsize used
					)
				),
				"title" => array(
					"title" => esc_html__("Title", "prohost"),
					"desc" => wp_kses_data( __("Image title (if need)", "prohost") ),
					"value" => "",
					"type" => "text"
				),
				"icon" => array(
					"title" => esc_html__("Icon before title",  'prohost'),
					"desc" => wp_kses_data( __('Select icon for the title from Fontello icons set',  'prohost') ),
					"value" => "",
					"type" => "icons",
					"options" => prohost_get_sc_param('icons')
				),
				"align" => array(
					"title" => esc_html__("Float image", "prohost"),
					"desc" => wp_kses_data( __("Float image to left or right side", "prohost") ),
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => prohost_get_sc_param('float')
				), 
				"shape" => array(
					"title" => esc_html__("Image Shape", "prohost"),
					"desc" => wp_kses_data( __("Shape of the image: square (rectangle) or round", "prohost") ),
					"value" => "square",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => array(
						"square" => esc_html__('Square', 'prohost'),
						"round" => esc_html__('Round', 'prohost')
					)
				), 
				"link" => array(
					"title" => esc_html__("Link", "prohost"),
					"desc" => wp_kses_data( __("The link URL from the image", "prohost") ),
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
if ( !function_exists( 'prohost_sc_image_reg_shortcodes_vc' ) ) {
	//add_action('prohost_action_shortcodes_list_vc', 'prohost_sc_image_reg_shortcodes_vc');
	function prohost_sc_image_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_image",
			"name" => esc_html__("Image", "prohost"),
			"description" => wp_kses_data( __("Insert image", "prohost") ),
			"category" => esc_html__('Content', 'prohost'),
			'icon' => 'icon_trx_image',
			"class" => "trx_sc_single trx_sc_image",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "url",
					"heading" => esc_html__("Select image", "prohost"),
					"description" => wp_kses_data( __("Select image from library", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Image alignment", "prohost"),
					"description" => wp_kses_data( __("Align image to left or right side", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(prohost_get_sc_param('float')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "shape",
					"heading" => esc_html__("Image shape", "prohost"),
					"description" => wp_kses_data( __("Shape of the image: square or round", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
						esc_html__('Square', 'prohost') => 'square',
						esc_html__('Round', 'prohost') => 'round'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", "prohost"),
					"description" => wp_kses_data( __("Image's title", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Title's icon", "prohost"),
					"description" => wp_kses_data( __("Select icon for the title from Fontello icons set", "prohost") ),
					"class" => "",
					"value" => prohost_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Link", "prohost"),
					"description" => wp_kses_data( __("The link URL from the image", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
                array(
                    "param_name" => "popup",
                    "heading" => esc_html__("Open link in popup", "prohost"),
                    "description" => wp_kses_data( __("Open link target in popup window", "prohost") ),
                    "class" => "",
                    "value" => array(esc_html__('Open in popup', 'prohost') => 'yes'),
                    "type" => "checkbox"
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
		
		class WPBakeryShortCode_Trx_Image extends PROHOST_VC_ShortCodeSingle {}
	}
}
?>