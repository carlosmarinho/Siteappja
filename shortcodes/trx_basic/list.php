<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_list_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_list_theme_setup' );
	function prohost_sc_list_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_list_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_sc_list_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_list id="unique_id" style="arrows|iconed|ol|ul"]
	[trx_list_item id="unique_id" title="title_of_element"]Et adipiscing integer.[/trx_list_item]
	[trx_list_item]A pulvinar ut, parturient enim porta ut sed, mus amet nunc, in.[/trx_list_item]
	[trx_list_item]Duis sociis, elit odio dapibus nec, dignissim purus est magna integer.[/trx_list_item]
	[trx_list_item]Nec purus, cras tincidunt rhoncus proin lacus porttitor rhoncus.[/trx_list_item]
[/trx_list]
*/

if (!function_exists('prohost_sc_list')) {	
	function prohost_sc_list($atts, $content=null){	
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "ul",
			"icon" => "icon-right",
			"icon_color" => "",
			"color" => "",
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
		$css .= $color !== '' ? 'color:' . esc_attr($color) .';' : '';
		if (trim($style) == '' || (trim($icon) == '' && $style=='iconed')) $style = 'ul';
		prohost_storage_set('sc_list_data', array(
			'counter' => 0,
            'icon' => empty($icon) || prohost_param_is_inherit($icon) ? "icon-right" : $icon,
            'icon_color' => $icon_color,
            'style' => $style
            )
        );
		$output = '<' . ($style=='ol' ? 'ol' : 'ul')
				. ($id ? ' id="'.esc_attr($id).'"' : '')
				. ' class="sc_list sc_list_style_' . esc_attr($style) . (!empty($class) ? ' '.esc_attr($class) : '') . '"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. (!prohost_param_is_off($animation) ? ' data-animation="'.esc_attr(prohost_get_animation_classes($animation)).'"' : '')
				. '>'
				. do_shortcode($content)
				. '</' .($style=='ol' ? 'ol' : 'ul') . '>';
		return apply_filters('prohost_shortcode_output', $output, 'trx_list', $atts, $content);
	}
	prohost_require_shortcode('trx_list', 'prohost_sc_list');
}


if (!function_exists('prohost_sc_list_item')) {	
	function prohost_sc_list_item($atts, $content=null) {
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts( array(
			// Individual params
			"color" => "",
			"icon" => "",
			"icon_color" => "",
			"title" => "",
			"link" => "",
			"target" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts)));
		prohost_storage_inc_array('sc_list_data', 'counter');
		$css .= $color !== '' ? 'color:' . esc_attr($color) .';' : '';
		if (trim($icon) == '' || prohost_param_is_inherit($icon)) $icon = prohost_storage_get_array('sc_list_data', 'icon');
		if (trim($color) == '' || prohost_param_is_inherit($icon_color)) $icon_color = prohost_storage_get_array('sc_list_data', 'icon_color');
		$content = do_shortcode($content);
		if (empty($content)) $content = $title;
		$output = '<li' . ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ' class="sc_list_item' 
			. (!empty($class) ? ' '.esc_attr($class) : '')
			. (prohost_storage_get_array('sc_list_data', 'counter') % 2 == 1 ? ' odd' : ' even') 
			. (prohost_storage_get_array('sc_list_data', 'counter') == 1 ? ' first' : '')  
			. '"' 
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
			. ($title ? ' title="'.esc_attr($title).'"' : '') 
			. '>' 
			. (!empty($link) ? '<a href="'.esc_url($link).'"' . (!empty($target) ? ' target="'.esc_attr($target).'"' : '') . '>' : '')
			. (prohost_storage_get_array('sc_list_data', 'style')=='iconed' && $icon!='' ? '<span class="sc_list_icon '.esc_attr($icon).'"'.($icon_color !== '' ? ' style="color:'.esc_attr($icon_color).';"' : '').'></span>' : '')
			. (prohost_storage_get_array('sc_list_data', 'style')=='ul' ? '<span class="sc_list_icon icon-right"></span>' : '')
            . '<span class="sc_list_content">' . trim($content) . '</span>'
			. (!empty($link) ? '</a>': '')
			. '</li>';
		return apply_filters('prohost_shortcode_output', $output, 'trx_list_item', $atts, $content);
	}
	prohost_require_shortcode('trx_list_item', 'prohost_sc_list_item');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_list_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_list_reg_shortcodes');
	function prohost_sc_list_reg_shortcodes() {
	
		prohost_sc_map("trx_list", array(
			"title" => esc_html__("List", "prohost"),
			"desc" => wp_kses_data( __("List items with specific bullets", "prohost") ),
			"decorate" => true,
			"container" => false,
			"params" => array(
				"style" => array(
					"title" => esc_html__("Bullet's style", "prohost"),
					"desc" => wp_kses_data( __("Bullet's style for each list item", "prohost") ),
					"value" => "ul",
					"type" => "checklist",
					"options" => prohost_get_sc_param('list_styles')
				), 
				"color" => array(
					"title" => esc_html__("Color", "prohost"),
					"desc" => wp_kses_data( __("List items color", "prohost") ),
					"value" => "",
					"type" => "color"
				),
				"icon" => array(
					"title" => esc_html__('List icon',  'prohost'),
					"desc" => wp_kses_data( __("Select list icon from Fontello icons set (only for style=Iconed)",  'prohost') ),
					"dependency" => array(
						'style' => array('iconed')
					),
					"value" => "",
					"type" => "icons",
					"options" => prohost_get_sc_param('icons')
				),
				"icon_color" => array(
					"title" => esc_html__("Icon color", "prohost"),
					"desc" => wp_kses_data( __("List icons color", "prohost") ),
					"value" => "",
					"dependency" => array(
						'style' => array('iconed')
					),
					"type" => "color"
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
				"name" => "trx_list_item",
				"title" => esc_html__("Item", "prohost"),
				"desc" => wp_kses_data( __("List item with specific bullet", "prohost") ),
				"decorate" => false,
				"container" => true,
				"params" => array(
					"_content_" => array(
						"title" => esc_html__("List item content", "prohost"),
						"desc" => wp_kses_data( __("Current list item content", "prohost") ),
						"rows" => 4,
						"value" => "",
						"type" => "textarea"
					),
					"title" => array(
						"title" => esc_html__("List item title", "prohost"),
						"desc" => wp_kses_data( __("Current list item title (show it as tooltip)", "prohost") ),
						"value" => "",
						"type" => "text"
					),
					"color" => array(
						"title" => esc_html__("Color", "prohost"),
						"desc" => wp_kses_data( __("Text color for this item", "prohost") ),
						"value" => "",
						"type" => "color"
					),
					"icon" => array(
						"title" => esc_html__('List icon',  'prohost'),
						"desc" => wp_kses_data( __("Select list item icon from Fontello icons set (only for style=Iconed)",  'prohost') ),
						"value" => "",
						"type" => "icons",
						"options" => prohost_get_sc_param('icons')
					),
					"icon_color" => array(
						"title" => esc_html__("Icon color", "prohost"),
						"desc" => wp_kses_data( __("Icon color for this item", "prohost") ),
						"value" => "",
						"type" => "color"
					),
					"link" => array(
						"title" => esc_html__("Link URL", "prohost"),
						"desc" => wp_kses_data( __("Link URL for the current list item", "prohost") ),
						"divider" => true,
						"value" => "",
						"type" => "text"
					),
					"target" => array(
						"title" => esc_html__("Link target", "prohost"),
						"desc" => wp_kses_data( __("Link target for the current list item", "prohost") ),
						"value" => "",
						"type" => "text"
					),
					"id" => prohost_get_sc_param('id'),
					"class" => prohost_get_sc_param('class'),
					"css" => prohost_get_sc_param('css')
				)
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_list_reg_shortcodes_vc' ) ) {
	//add_action('prohost_action_shortcodes_list_vc', 'prohost_sc_list_reg_shortcodes_vc');
	function prohost_sc_list_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_list",
			"name" => esc_html__("List", "prohost"),
			"description" => wp_kses_data( __("List items with specific bullets", "prohost") ),
			"category" => esc_html__('Content', 'prohost'),
			"class" => "trx_sc_collection trx_sc_list",
			'icon' => 'icon_trx_list',
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => false,
			"as_parent" => array('only' => 'trx_list_item'),
			"params" => array(
				array(
					"param_name" => "style",
					"heading" => esc_html__("Bullet's style", "prohost"),
					"description" => wp_kses_data( __("Bullet's style for each list item", "prohost") ),
					"class" => "",
					"admin_label" => true,
					"value" => array_flip(prohost_get_sc_param('list_styles')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Color", "prohost"),
					"description" => wp_kses_data( __("List items color", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("List icon", "prohost"),
					"description" => wp_kses_data( __("Select list icon from Fontello icons set (only for style=Iconed)", "prohost") ),
					"admin_label" => true,
					"class" => "",
					'dependency' => array(
						'element' => 'style',
						'value' => array('iconed')
					),
					"value" => prohost_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "icon_color",
					"heading" => esc_html__("Icon color", "prohost"),
					"description" => wp_kses_data( __("List icons color", "prohost") ),
					"class" => "",
					'dependency' => array(
						'element' => 'style',
						'value' => array('iconed')
					),
					"value" => "",
					"type" => "colorpicker"
				),
				prohost_get_vc_param('id'),
				prohost_get_vc_param('class'),
				prohost_get_vc_param('animation'),
				prohost_get_vc_param('css'),
				prohost_get_vc_param('margin_top'),
				prohost_get_vc_param('margin_bottom'),
				prohost_get_vc_param('margin_left'),
				prohost_get_vc_param('margin_right')
			),
			'default_content' => '
				[trx_list_item][/trx_list_item]
				[trx_list_item][/trx_list_item]
			'
		) );
		
		
		vc_map( array(
			"base" => "trx_list_item",
			"name" => esc_html__("List item", "prohost"),
			"description" => wp_kses_data( __("List item with specific bullet", "prohost") ),
			"class" => "trx_sc_container trx_sc_list_item",
			"show_settings_on_create" => true,
			"content_element" => true,
			"is_container" => true,
			'icon' => 'icon_trx_list_item',
			"as_child" => array('only' => 'trx_list'), // Use only|except attributes to limit parent (separate multiple values with comma)
			"as_parent" => array('except' => 'trx_list'),
			"params" => array(
				array(
					"param_name" => "title",
					"heading" => esc_html__("List item title", "prohost"),
					"description" => wp_kses_data( __("Title for the current list item (show it as tooltip)", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Link URL", "prohost"),
					"description" => wp_kses_data( __("Link URL for the current list item", "prohost") ),
					"admin_label" => true,
					"group" => esc_html__('Link', 'prohost'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "target",
					"heading" => esc_html__("Link target", "prohost"),
					"description" => wp_kses_data( __("Link target for the current list item", "prohost") ),
					"admin_label" => true,
					"group" => esc_html__('Link', 'prohost'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Color", "prohost"),
					"description" => wp_kses_data( __("Text color for this item", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("List item icon", "prohost"),
					"description" => wp_kses_data( __("Select list item icon from Fontello icons set (only for style=Iconed)", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => prohost_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "icon_color",
					"heading" => esc_html__("Icon color", "prohost"),
					"description" => wp_kses_data( __("Icon color for this item", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
/*
				array(
					"param_name" => "content",
					"heading" => esc_html__("List item text", "prohost"),
					"description" => wp_kses_data( __("Current list item content", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "textarea_html"
				),
*/
				prohost_get_vc_param('id'),
				prohost_get_vc_param('class'),
				prohost_get_vc_param('css')
			)
		
		) );
		
		class WPBakeryShortCode_Trx_List extends PROHOST_VC_ShortCodeCollection {}
		class WPBakeryShortCode_Trx_List_Item extends PROHOST_VC_ShortCodeContainer {}
	}
}
?>