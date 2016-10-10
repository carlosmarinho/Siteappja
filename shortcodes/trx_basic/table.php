<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_table_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_table_theme_setup' );
	function prohost_sc_table_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_table_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_sc_table_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_table id="unique_id" style="1"]
Table content, generated on one of many public internet resources, for example: http://www.impressivewebs.com/html-table-code-generator/
[/trx_table]
*/

if (!function_exists('prohost_sc_table')) {	
	function prohost_sc_table($atts, $content=null){	
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"align" => "",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => "",
			"width" => "100%"
		), $atts)));
		$class .= ($class ? ' ' : '') . prohost_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= prohost_get_css_dimensions_from_values($width);
		$content = str_replace(
					array('<p><table', 'table></p>', '><br />'),
					array('<table', 'table>', '>'),
					html_entity_decode($content, ENT_COMPAT, 'UTF-8'));
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_table' 
					. (!empty($align) && $align!='none' ? ' align'.esc_attr($align) : '') 
					. (!empty($class) ? ' '.esc_attr($class) : '') 
					. '"'
				. (!prohost_param_is_off($animation) ? ' data-animation="'.esc_attr(prohost_get_animation_classes($animation)).'"' : '')
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
				.'>' 
				. do_shortcode($content) 
				. '</div>';
		return apply_filters('prohost_shortcode_output', $output, 'trx_table', $atts, $content);
	}
	prohost_require_shortcode('trx_table', 'prohost_sc_table');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_table_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_table_reg_shortcodes');
	function prohost_sc_table_reg_shortcodes() {
	
		prohost_sc_map("trx_table", array(
			"title" => esc_html__("Table", "prohost"),
			"desc" => wp_kses_data( __("Insert a table into post (page). ", "prohost") ),
			"decorate" => true,
			"container" => true,
			"params" => array(
				"align" => array(
					"title" => esc_html__("Content alignment", "prohost"),
					"desc" => wp_kses_data( __("Select alignment for each table cell", "prohost") ),
					"value" => "none",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => prohost_get_sc_param('align')
				),
				"_content_" => array(
					"title" => esc_html__("Table content", "prohost"),
					"desc" => wp_kses_data( __("Content, created with any table-generator", "prohost") ),
					"divider" => true,
					"rows" => 8,
					"value" => "Paste here table content, generated on one of many public internet resources, for example: http://www.impressivewebs.com/html-table-code-generator/ or http://html-tables.com/",
					"type" => "textarea"
				),
				"width" => prohost_shortcodes_width(),
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
if ( !function_exists( 'prohost_sc_table_reg_shortcodes_vc' ) ) {
	//add_action('prohost_action_shortcodes_list_vc', 'prohost_sc_table_reg_shortcodes_vc');
	function prohost_sc_table_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_table",
			"name" => esc_html__("Table", "prohost"),
			"description" => wp_kses_data( __("Insert a table", "prohost") ),
			"category" => esc_html__('Content', 'prohost'),
			'icon' => 'icon_trx_table',
			"class" => "trx_sc_container trx_sc_table",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "align",
					"heading" => esc_html__("Cells content alignment", "prohost"),
					"description" => wp_kses_data( __("Select alignment for each table cell", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(prohost_get_sc_param('align')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "content",
					"heading" => esc_html__("Table content", "prohost"),
					"description" => wp_kses_data( __("Content, created with any table-generator", "prohost") ),
					"class" => "",
					"value" => esc_html__("Paste here table content, generated on one of many public internet resources, for example: http://www.impressivewebs.com/html-table-code-generator/ or http://html-tables.com/", 'prohost'),
					"type" => "textarea_html"
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
			'js_view' => 'VcTrxTextContainerView'
		) );
		
		class WPBakeryShortCode_Trx_Table extends PROHOST_VC_ShortCodeContainer {}
	}
}
?>