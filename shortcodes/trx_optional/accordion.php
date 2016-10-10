<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_accordion_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_accordion_theme_setup' );
	function prohost_sc_accordion_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_accordion_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_sc_accordion_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_accordion counter="off" initial="1"]
	[trx_accordion_item title="Accordion Title 1"]Lorem ipsum dolor sit amet, consectetur adipisicing elit[/trx_accordion_item]
	[trx_accordion_item title="Accordion Title 2"]Proin dignissim commodo magna at luctus. Nam molestie justo augue, nec eleifend urna laoreet non.[/trx_accordion_item]
	[trx_accordion_item title="Accordion Title 3 with custom icons" icon_closed="icon-check" icon_opened="icon-delete"]Curabitur tristique tempus arcu a placerat.[/trx_accordion_item]
[/trx_accordion]
*/
if (!function_exists('prohost_sc_accordion')) {	
	function prohost_sc_accordion($atts, $content=null){	
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"initial" => "1",
			"counter" => "off",
			"icon_closed" => "icon-plus",
			"icon_opened" => "icon-minus",
            "style" => "1",
            // Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . prohost_get_css_position_as_classes($top, $right, $bottom, $left);
		$initial = max(0, (int) $initial);
		prohost_storage_set('sc_accordion_data', array(
			'counter' => 0,
            'show_counter' => prohost_param_is_on($counter),
            'icon_closed' => empty($icon_closed) || prohost_param_is_inherit($icon_closed) ? "icon-plus" : $icon_closed,
            'icon_opened' => empty($icon_opened) || prohost_param_is_inherit($icon_opened) ? "icon-minus" : $icon_opened
            )
        );
		prohost_enqueue_script('jquery-ui-accordion', false, array('jquery','jquery-ui-core'), null, true);
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_accordion'
					. (!empty($class) ? ' '.esc_attr($class) : '')
					. (prohost_param_is_on($counter) ? ' sc_show_counter' : '')
                    . ' sc_accordion_style_' . esc_attr($style)
				. '"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
				. ' data-active="' . ($initial-1) . '"'
				. (!prohost_param_is_off($animation) ? ' data-animation="'.esc_attr(prohost_get_animation_classes($animation)).'"' : '')
				. '>'
				. do_shortcode($content)
				. '</div>';
		return apply_filters('prohost_shortcode_output', $output, 'trx_accordion', $atts, $content);
	}
	prohost_require_shortcode('trx_accordion', 'prohost_sc_accordion');
}


if (!function_exists('prohost_sc_accordion_item')) {	
	function prohost_sc_accordion_item($atts, $content=null) {
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts( array(
			// Individual params
			"icon_closed" => "",
			"icon_opened" => "",
			"title" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts)));
		prohost_storage_inc_array('sc_accordion_data', 'counter');
		if (empty($icon_closed) || prohost_param_is_inherit($icon_closed)) $icon_closed = prohost_storage_get_array('sc_accordion_data', 'icon_closed', '', "icon-plus");
		if (empty($icon_opened) || prohost_param_is_inherit($icon_opened)) $icon_opened = prohost_storage_get_array('sc_accordion_data', 'icon_opened', '', "icon-minus");
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_accordion_item' 
				. (!empty($class) ? ' '.esc_attr($class) : '')
				. (prohost_storage_get_array('sc_accordion_data', 'counter') % 2 == 1 ? ' odd' : ' even') 
				. (prohost_storage_get_array('sc_accordion_data', 'counter') == 1 ? ' first' : '') 
				. '">'
				. '<h5 class="sc_accordion_title">'
				. (!prohost_param_is_off($icon_closed) ? '<span class="sc_accordion_icon sc_accordion_icon_closed '.esc_attr($icon_closed).'"></span>' : '')
				. (!prohost_param_is_off($icon_opened) ? '<span class="sc_accordion_icon sc_accordion_icon_opened '.esc_attr($icon_opened).'"></span>' : '')
				. (prohost_storage_get_array('sc_accordion_data', 'show_counter') ? '<span class="sc_items_counter">'.(prohost_storage_get_array('sc_accordion_data', 'counter')).'</span>' : '')
				. ($title)
				. '</h5>'
				. '<div class="sc_accordion_content"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
					. '>'
					. do_shortcode($content) 
				. '</div>'
				. '</div>';
		return apply_filters('prohost_shortcode_output', $output, 'trx_accordion_item', $atts, $content);
	}
	prohost_require_shortcode('trx_accordion_item', 'prohost_sc_accordion_item');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_accordion_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_accordion_reg_shortcodes');
	function prohost_sc_accordion_reg_shortcodes() {
	
		prohost_sc_map("trx_accordion", array(
			"title" => esc_html__("Accordion", "prohost"),
			"desc" => wp_kses_data( __("Accordion items", "prohost") ),
			"decorate" => true,
			"container" => false,
			"params" => array(
				"counter" => array(
					"title" => esc_html__("Counter", "prohost"),
					"desc" => wp_kses_data( __("Display counter before each accordion title", "prohost") ),
					"value" => "off",
					"type" => "switch",
					"options" => prohost_get_sc_param('on_off')
				),
                "style" => array(
                    "title" => esc_html__("Accordion style", "prohost"),
                    "desc" => wp_kses_data( __("Select style for accordion items", "prohost") ),
                    "value" => 1,
                    "options" => prohost_get_list_styles(1, 2),
                    "type" => "radio"
                ),
				"initial" => array(
					"title" => esc_html__("Initially opened item", "prohost"),
					"desc" => wp_kses_data( __("Number of initially opened item", "prohost") ),
                    "dependency" => array(
                        'style' => array('1')
                    ),
					"value" => 1,
					"min" => 0,
					"type" => "spinner"
				),
				"icon_closed" => array(
					"title" => esc_html__("Icon while closed",  'prohost'),
					"desc" => wp_kses_data( __('Select icon for the closed accordion item from Fontello icons set',  'prohost') ),
					"value" => "",
					"type" => "icons",
					"options" => prohost_get_sc_param('icons')
				),
				"icon_opened" => array(
					"title" => esc_html__("Icon while opened",  'prohost'),
					"desc" => wp_kses_data( __('Select icon for the opened accordion item from Fontello icons set',  'prohost') ),
					"value" => "",
					"type" => "icons",
					"options" => prohost_get_sc_param('icons')
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
				"name" => "trx_accordion_item",
				"title" => esc_html__("Item", "prohost"),
				"desc" => wp_kses_data( __("Accordion item", "prohost") ),
				"container" => true,
				"params" => array(
					"title" => array(
						"title" => esc_html__("Accordion item title", "prohost"),
						"desc" => wp_kses_data( __("Title for current accordion item", "prohost") ),
						"value" => "",
						"type" => "text"
					),
					"icon_closed" => array(
						"title" => esc_html__("Icon while closed",  'prohost'),
						"desc" => wp_kses_data( __('Select icon for the closed accordion item from Fontello icons set',  'prohost') ),
						"value" => "",
						"type" => "icons",
						"options" => prohost_get_sc_param('icons')
					),
					"icon_opened" => array(
						"title" => esc_html__("Icon while opened",  'prohost'),
						"desc" => wp_kses_data( __('Select icon for the opened accordion item from Fontello icons set',  'prohost') ),
						"value" => "",
						"type" => "icons",
						"options" => prohost_get_sc_param('icons')
					),
					"_content_" => array(
						"title" => esc_html__("Accordion item content", "prohost"),
						"desc" => wp_kses_data( __("Current accordion item content", "prohost") ),
						"rows" => 4,
						"value" => "",
						"type" => "textarea"
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
if ( !function_exists( 'prohost_sc_accordion_reg_shortcodes_vc' ) ) {
	//add_action('prohost_action_shortcodes_list_vc', 'prohost_sc_accordion_reg_shortcodes_vc');
	function prohost_sc_accordion_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_accordion",
			"name" => esc_html__("Accordion", "prohost"),
			"description" => wp_kses_data( __("Accordion items", "prohost") ),
			"category" => esc_html__('Content', 'prohost'),
			'icon' => 'icon_trx_accordion',
			"class" => "trx_sc_collection trx_sc_accordion",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => false,
			"as_parent" => array('only' => 'trx_accordion_item'),	// Use only|except attributes to limit child shortcodes (separate multiple values with comma)
			"params" => array(
				array(
					"param_name" => "counter",
					"heading" => esc_html__("Counter", "prohost"),
					"description" => wp_kses_data( __("Display counter before each accordion title", "prohost") ),
					"class" => "",
					"value" => array("Add item numbers before each element" => "on" ),
					"type" => "checkbox"
				),
                array(
                    "param_name" => "style",
                    "heading" => esc_html__("Accordion style", "prohost"),
                    "desc" => wp_kses_data( __("Select style of accordion items", "prohost") ),
                    "admin_label" => true,
                    "class" => "",
                    "value" => array_flip(prohost_get_list_styles(1, 2)),
                    "type" => "dropdown"
                ),
				array(
					"param_name" => "initial",
					"heading" => esc_html__("Initially opened item", "prohost"),
					"description" => wp_kses_data( __("Number of initially opened item", "prohost") ),
                    'dependency' => array(
                        'element' => 'style',
                        'value' => '1'
                    ),
					"class" => "",
					"value" => 1,
					"type" => "textfield"
				),
				array(
					"param_name" => "icon_closed",
					"heading" => esc_html__("Icon while closed", "prohost"),
					"description" => wp_kses_data( __("Select icon for the closed accordion item from Fontello icons set", "prohost") ),
					"class" => "",
					"value" => prohost_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "icon_opened",
					"heading" => esc_html__("Icon while opened", "prohost"),
					"description" => wp_kses_data( __("Select icon for the opened accordion item from Fontello icons set", "prohost") ),
					"class" => "",
					"value" => prohost_get_sc_param('icons'),
					"type" => "dropdown"
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
				[trx_accordion_item title="' . esc_html__( 'Item 1 title', 'prohost' ) . '"][/trx_accordion_item]
				[trx_accordion_item title="' . esc_html__( 'Item 2 title', 'prohost' ) . '"][/trx_accordion_item]
			',
			"custom_markup" => '
				<div class="wpb_accordion_holder wpb_holder clearfix vc_container_for_children">
					%content%
				</div>
				<div class="tab_controls">
					<button class="add_tab" title="'.esc_attr__("Add item", "prohost").'">'.esc_html__("Add item", "prohost").'</button>
				</div>
			',
			'js_view' => 'VcTrxAccordionView'
		) );
		
		
		vc_map( array(
			"base" => "trx_accordion_item",
			"name" => esc_html__("Accordion item", "prohost"),
			"description" => wp_kses_data( __("Inner accordion item", "prohost") ),
			"show_settings_on_create" => true,
			"content_element" => true,
			"is_container" => true,
			'icon' => 'icon_trx_accordion_item',
			"as_child" => array('only' => 'trx_accordion'), 	// Use only|except attributes to limit parent (separate multiple values with comma)
			"as_parent" => array('except' => 'trx_accordion'),
			"params" => array(
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", "prohost"),
					"description" => wp_kses_data( __("Title for current accordion item", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "icon_closed",
					"heading" => esc_html__("Icon while closed", "prohost"),
					"description" => wp_kses_data( __("Select icon for the closed accordion item from Fontello icons set", "prohost") ),
					"class" => "",
					"value" => prohost_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "icon_opened",
					"heading" => esc_html__("Icon while opened", "prohost"),
					"description" => wp_kses_data( __("Select icon for the opened accordion item from Fontello icons set", "prohost") ),
					"class" => "",
					"value" => prohost_get_sc_param('icons'),
					"type" => "dropdown"
				),
				prohost_get_vc_param('id'),
				prohost_get_vc_param('class'),
				prohost_get_vc_param('css')
			),
		  'js_view' => 'VcTrxAccordionTabView'
		) );

		class WPBakeryShortCode_Trx_Accordion extends PROHOST_VC_ShortCodeAccordion {}
		class WPBakeryShortCode_Trx_Accordion_Item extends PROHOST_VC_ShortCodeAccordionItem {}
	}
}
?>