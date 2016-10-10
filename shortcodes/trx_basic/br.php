<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_br_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_br_theme_setup' );
	function prohost_sc_br_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_br_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_sc_br_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_br clear="left|right|both"]
*/

if (!function_exists('prohost_sc_br')) {	
	function prohost_sc_br($atts, $content = null) {
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			"clear" => ""
		), $atts)));
		$output = in_array($clear, array('left', 'right', 'both', 'all')) 
			? '<div class="clearfix" style="clear:' . str_replace('all', 'both', $clear) . '"></div>'
			: '<br />';
		return apply_filters('prohost_shortcode_output', $output, 'trx_br', $atts, $content);
	}
	prohost_require_shortcode("trx_br", "prohost_sc_br");
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_br_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_br_reg_shortcodes');
	function prohost_sc_br_reg_shortcodes() {
	
		prohost_sc_map("trx_br", array(
			"title" => esc_html__("Break", "prohost"),
			"desc" => wp_kses_data( __("Line break with clear floating (if need)", "prohost") ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"clear" => 	array(
					"title" => esc_html__("Clear floating", "prohost"),
					"desc" => wp_kses_data( __("Clear floating (if need)", "prohost") ),
					"value" => "",
					"type" => "checklist",
					"options" => array(
						'none' => esc_html__('None', 'prohost'),
						'left' => esc_html__('Left', 'prohost'),
						'right' => esc_html__('Right', 'prohost'),
						'both' => esc_html__('Both', 'prohost')
					)
				)
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_br_reg_shortcodes_vc' ) ) {
	//add_action('prohost_action_shortcodes_list_vc', 'prohost_sc_br_reg_shortcodes_vc');
	function prohost_sc_br_reg_shortcodes_vc() {
/*
		vc_map( array(
			"base" => "trx_br",
			"name" => esc_html__("Line break", "prohost"),
			"description" => wp_kses_data( __("Line break or Clear Floating", "prohost") ),
			"category" => esc_html__('Content', 'prohost'),
			'icon' => 'icon_trx_br',
			"class" => "trx_sc_single trx_sc_br",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "clear",
					"heading" => esc_html__("Clear floating", "prohost"),
					"description" => wp_kses_data( __("Select clear side (if need)", "prohost") ),
					"class" => "",
					"value" => "",
					"value" => array(
						esc_html__('None', 'prohost') => 'none',
						esc_html__('Left', 'prohost') => 'left',
						esc_html__('Right', 'prohost') => 'right',
						esc_html__('Both', 'prohost') => 'both'
					),
					"type" => "dropdown"
				)
			)
		) );
		
		class WPBakeryShortCode_Trx_Br extends PROHOST_VC_ShortCodeSingle {}
*/
	}
}
?>