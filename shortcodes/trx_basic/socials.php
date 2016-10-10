<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_socials_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_socials_theme_setup' );
	function prohost_sc_socials_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_socials_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_sc_socials_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_socials id="unique_id" size="small"]
	[trx_social_item name="facebook" url="profile url" icon="path for the icon"]
	[trx_social_item name="twitter" url="profile url"]
[/trx_socials]
*/

if (!function_exists('prohost_sc_socials')) {	
	function prohost_sc_socials($atts, $content=null){	
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"size" => "small",		// tiny | small | medium | large
			"shape" => "square",	// round | square
			"type" => prohost_get_theme_setting('socials_type'),	// icons | images
			"socials" => "",
			"custom" => "no",
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
		prohost_storage_set('sc_social_data', array(
			'icons' => false,
            'type' => $type
            )
        );
		if (!empty($socials)) {
			$allowed = explode('|', $socials);
			$list = array();
			for ($i=0; $i<count($allowed); $i++) {
				$s = explode('=', $allowed[$i]);
				if (!empty($s[1])) {
					$list[] = array(
						'icon'	=> $type=='images' ? prohost_get_socials_url($s[0]) : 'icon-'.trim($s[0]),
						'url'	=> $s[1]
						);
				}
			}
			if (count($list) > 0) prohost_storage_set_array('sc_social_data', 'icons', $list);
		} else if (prohost_param_is_off($custom))
			$content = do_shortcode($content);
		if (prohost_storage_get_array('sc_social_data', 'icons')===false) prohost_storage_set_array('sc_social_data', 'icons', prohost_get_custom_option('social_icons'));
		$output = prohost_prepare_socials(prohost_storage_get_array('sc_social_data', 'icons'));
		$output = $output
			? '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_socials sc_socials_type_' . esc_attr($type) . ' sc_socials_shape_' . esc_attr($shape) . ' sc_socials_size_' . esc_attr($size) . (!empty($class) ? ' '.esc_attr($class) : '') . '"' 
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
				. (!prohost_param_is_off($animation) ? ' data-animation="'.esc_attr(prohost_get_animation_classes($animation)).'"' : '')
				. '>' 
				. ($output)
				. '</div>'
			: '';
		return apply_filters('prohost_shortcode_output', $output, 'trx_socials', $atts, $content);
	}
	prohost_require_shortcode('trx_socials', 'prohost_sc_socials');
}


if (!function_exists('prohost_sc_social_item')) {	
	function prohost_sc_social_item($atts, $content=null){	
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"name" => "",
			"url" => "",
			"icon" => ""
		), $atts)));
		if (!empty($name) && empty($icon)) {
			$type = prohost_storage_get_array('sc_social_data', 'type');
			if ($type=='images') {
				if (file_exists(prohost_get_socials_dir($name.'.png')))
					$icon = prohost_get_socials_url($name.'.png');
			} else
				$icon = 'icon-'.esc_attr($name);
		}
		if (!empty($icon) && !empty($url)) {
			if (prohost_storage_get_array('sc_social_data', 'icons')===false) prohost_storage_set_array('sc_social_data', 'icons', array());
			prohost_storage_set_array2('sc_social_data', 'icons', '', array(
				'icon' => $icon,
				'url' => $url
				)
			);
		}
		return '';
	}
	prohost_require_shortcode('trx_social_item', 'prohost_sc_social_item');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_socials_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_socials_reg_shortcodes');
	function prohost_sc_socials_reg_shortcodes() {
	
		prohost_sc_map("trx_socials", array(
			"title" => esc_html__("Social icons", "prohost"),
			"desc" => wp_kses_data( __("List of social icons (with hovers)", "prohost") ),
			"decorate" => true,
			"container" => false,
			"params" => array(
				"type" => array(
					"title" => esc_html__("Icon's type", "prohost"),
					"desc" => wp_kses_data( __("Type of the icons - images or font icons", "prohost") ),
					"value" => prohost_get_theme_setting('socials_type'),
					"options" => array(
						'icons' => esc_html__('Icons', 'prohost'),
						'images' => esc_html__('Images', 'prohost')
					),
					"type" => "checklist"
				), 
				"size" => array(
					"title" => esc_html__("Icon's size", "prohost"),
					"desc" => wp_kses_data( __("Size of the icons", "prohost") ),
					"value" => "small",
					"options" => prohost_get_sc_param('sizes'),
					"type" => "checklist"
				), 
				"shape" => array(
					"title" => esc_html__("Icon's shape", "prohost"),
					"desc" => wp_kses_data( __("Shape of the icons", "prohost") ),
					"value" => "square",
					"options" => prohost_get_sc_param('shapes'),
					"type" => "checklist"
				), 
				"socials" => array(
					"title" => esc_html__("Manual socials list", "prohost"),
					"desc" => wp_kses_data( __("Custom list of social networks. For example: twitter=http://twitter.com/my_profile|facebook=http://facebook.com/my_profile. If empty - use socials from Theme options.", "prohost") ),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
				"custom" => array(
					"title" => esc_html__("Custom socials", "prohost"),
					"desc" => wp_kses_data( __("Make custom icons from inner shortcodes (prepare it on tabs)", "prohost") ),
					"divider" => true,
					"value" => "no",
					"options" => prohost_get_sc_param('yes_no'),
					"type" => "switch"
				),
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
				"name" => "trx_social_item",
				"title" => esc_html__("Custom social item", "prohost"),
				"desc" => wp_kses_data( __("Custom social item: name, profile url and icon url", "prohost") ),
				"decorate" => false,
				"container" => false,
				"params" => array(
					"name" => array(
						"title" => esc_html__("Social name", "prohost"),
						"desc" => wp_kses_data( __("Name (slug) of the social network (twitter, facebook, linkedin, etc.)", "prohost") ),
						"value" => "",
						"type" => "text"
					),
					"url" => array(
						"title" => esc_html__("Your profile URL", "prohost"),
						"desc" => wp_kses_data( __("URL of your profile in specified social network", "prohost") ),
						"value" => "",
						"type" => "text"
					),
					"icon" => array(
						"title" => esc_html__("URL (source) for icon file", "prohost"),
						"desc" => wp_kses_data( __("Select or upload image or write URL from other site for the current social icon", "prohost") ),
						"readonly" => false,
						"value" => "",
						"type" => "media"
					)
				)
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_socials_reg_shortcodes_vc' ) ) {
	//add_action('prohost_action_shortcodes_list_vc', 'prohost_sc_socials_reg_shortcodes_vc');
	function prohost_sc_socials_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_socials",
			"name" => esc_html__("Social icons", "prohost"),
			"description" => wp_kses_data( __("Custom social icons", "prohost") ),
			"category" => esc_html__('Content', 'prohost'),
			'icon' => 'icon_trx_socials',
			"class" => "trx_sc_collection trx_sc_socials",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => true,
			"as_parent" => array('only' => 'trx_social_item'),
			"params" => array_merge(array(
				array(
					"param_name" => "type",
					"heading" => esc_html__("Icon's type", "prohost"),
					"description" => wp_kses_data( __("Type of the icons - images or font icons", "prohost") ),
					"class" => "",
					"std" => prohost_get_theme_setting('socials_type'),
					"value" => array(
						esc_html__('Icons', 'prohost') => 'icons',
						esc_html__('Images', 'prohost') => 'images'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "size",
					"heading" => esc_html__("Icon's size", "prohost"),
					"description" => wp_kses_data( __("Size of the icons", "prohost") ),
					"class" => "",
					"std" => "small",
					"value" => array_flip(prohost_get_sc_param('sizes')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "shape",
					"heading" => esc_html__("Icon's shape", "prohost"),
					"description" => wp_kses_data( __("Shape of the icons", "prohost") ),
					"class" => "",
					"std" => "square",
					"value" => array_flip(prohost_get_sc_param('shapes')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "socials",
					"heading" => esc_html__("Manual socials list", "prohost"),
					"description" => wp_kses_data( __("Custom list of social networks. For example: twitter=http://twitter.com/my_profile|facebook=http://facebook.com/my_profile. If empty - use socials from Theme options.", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "custom",
					"heading" => esc_html__("Custom socials", "prohost"),
					"description" => wp_kses_data( __("Make custom icons from inner shortcodes (prepare it on tabs)", "prohost") ),
					"class" => "",
					"value" => array(esc_html__('Custom socials', 'prohost') => 'yes'),
					"type" => "checkbox"
				),
				prohost_get_vc_param('id'),
				prohost_get_vc_param('class'),
				prohost_get_vc_param('animation'),
				prohost_get_vc_param('css'),
				prohost_get_vc_param('margin_top'),
				prohost_get_vc_param('margin_bottom'),
				prohost_get_vc_param('margin_left'),
				prohost_get_vc_param('margin_right')
			))
		) );
		
		
		vc_map( array(
			"base" => "trx_social_item",
			"name" => esc_html__("Custom social item", "prohost"),
			"description" => wp_kses_data( __("Custom social item: name, profile url and icon url", "prohost") ),
			"show_settings_on_create" => true,
			"content_element" => true,
			"is_container" => false,
			'icon' => 'icon_trx_social_item',
			"class" => "trx_sc_single trx_sc_social_item",
			"as_child" => array('only' => 'trx_socials'),
			"as_parent" => array('except' => 'trx_socials'),
			"params" => array(
				array(
					"param_name" => "name",
					"heading" => esc_html__("Social name", "prohost"),
					"description" => wp_kses_data( __("Name (slug) of the social network (twitter, facebook, linkedin, etc.)", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "url",
					"heading" => esc_html__("Your profile URL", "prohost"),
					"description" => wp_kses_data( __("URL of your profile in specified social network", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("URL (source) for icon file", "prohost"),
					"description" => wp_kses_data( __("Select or upload image or write URL from other site for the current social icon", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				)
			)
		) );
		
		class WPBakeryShortCode_Trx_Socials extends PROHOST_VC_ShortCodeCollection {}
		class WPBakeryShortCode_Trx_Social_Item extends PROHOST_VC_ShortCodeSingle {}
	}
}
?>