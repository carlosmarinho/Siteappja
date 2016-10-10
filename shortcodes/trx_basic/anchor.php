<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_anchor_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_anchor_theme_setup' );
	function prohost_sc_anchor_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_anchor_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_sc_anchor_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_anchor id="unique_id" description="Anchor description" title="Short Caption" icon="icon-class"]
*/

if (!function_exists('prohost_sc_anchor')) {	
	function prohost_sc_anchor($atts, $content = null) {
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"description" => '',
			"icon" => '',
			"url" => "",
			"separator" => "no",
			// Common params
			"id" => ""
		), $atts)));
		$output = $id 
			? '<a id="'.esc_attr($id).'"'
				. ' class="sc_anchor"' 
				. ' title="' . ($title ? esc_attr($title) : '') . '"'
				. ' data-description="' . ($description ? esc_attr(prohost_strmacros($description)) : ''). '"'
				. ' data-icon="' . ($icon ? $icon : '') . '"' 
				. ' data-url="' . ($url ? esc_attr($url) : '') . '"' 
				. ' data-separator="' . (prohost_param_is_on($separator) ? 'yes' : 'no') . '"'
				. '></a>'
			: '';
		return apply_filters('prohost_shortcode_output', $output, 'trx_anchor', $atts, $content);
	}
	prohost_require_shortcode("trx_anchor", "prohost_sc_anchor");
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_anchor_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_anchor_reg_shortcodes');
	function prohost_sc_anchor_reg_shortcodes() {
	
		prohost_sc_map("trx_anchor", array(
			"title" => esc_html__("Anchor", "prohost"),
			"desc" => wp_kses_data( __("Insert anchor for the TOC (table of content)", "prohost") ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"icon" => array(
					"title" => esc_html__("Anchor's icon",  'prohost'),
					"desc" => wp_kses_data( __('Select icon for the anchor from Fontello icons set',  'prohost') ),
					"value" => "",
					"type" => "icons",
					"options" => prohost_get_sc_param('icons')
				),
				"title" => array(
					"title" => esc_html__("Short title", "prohost"),
					"desc" => wp_kses_data( __("Short title of the anchor (for the table of content)", "prohost") ),
					"value" => "",
					"type" => "text"
				),
				"description" => array(
					"title" => esc_html__("Long description", "prohost"),
					"desc" => wp_kses_data( __("Description for the popup (then hover on the icon). You can use:<br>'{{' and '}}' - to make the text italic,<br>'((' and '))' - to make the text bold,<br>'||' - to insert line break", "prohost") ),
					"value" => "",
					"type" => "text"
				),
				"url" => array(
					"title" => esc_html__("External URL", "prohost"),
					"desc" => wp_kses_data( __("External URL for this TOC item", "prohost") ),
					"value" => "",
					"type" => "text"
				),
				"separator" => array(
					"title" => esc_html__("Add separator", "prohost"),
					"desc" => wp_kses_data( __("Add separator under item in the TOC", "prohost") ),
					"value" => "no",
					"type" => "switch",
					"options" => prohost_get_sc_param('yes_no')
				),
				"id" => prohost_get_sc_param('id')
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_anchor_reg_shortcodes_vc' ) ) {
	//add_action('prohost_action_shortcodes_list_vc', 'prohost_sc_anchor_reg_shortcodes_vc');
	function prohost_sc_anchor_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_anchor",
			"name" => esc_html__("Anchor", "prohost"),
			"description" => wp_kses_data( __("Insert anchor for the TOC (table of content)", "prohost") ),
			"category" => esc_html__('Content', 'prohost'),
			'icon' => 'icon_trx_anchor',
			"class" => "trx_sc_single trx_sc_anchor",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Anchor's icon", "prohost"),
					"description" => wp_kses_data( __("Select icon for the anchor from Fontello icons set", "prohost") ),
					"class" => "",
					"value" => prohost_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Short title", "prohost"),
					"description" => wp_kses_data( __("Short title of the anchor (for the table of content)", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "description",
					"heading" => esc_html__("Long description", "prohost"),
					"description" => wp_kses_data( __("Description for the popup (then hover on the icon). You can use:<br>'{{' and '}}' - to make the text italic,<br>'((' and '))' - to make the text bold,<br>'||' - to insert line break", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "url",
					"heading" => esc_html__("External URL", "prohost"),
					"description" => wp_kses_data( __("External URL for this TOC item", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "separator",
					"heading" => esc_html__("Add separator", "prohost"),
					"description" => wp_kses_data( __("Add separator under item in the TOC", "prohost") ),
					"class" => "",
					"value" => array("Add separator" => "yes" ),
					"type" => "checkbox"
				),
				prohost_get_vc_param('id')
			),
		) );
		
		class WPBakeryShortCode_Trx_Anchor extends PROHOST_VC_ShortCodeSingle {}
	}
}
?>