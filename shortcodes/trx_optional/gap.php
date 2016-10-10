<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_gap_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_gap_theme_setup' );
	function prohost_sc_gap_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_gap_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_sc_gap_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

//[trx_gap]Fullwidth content[/trx_gap]

if (!function_exists('prohost_sc_gap')) {	
	function prohost_sc_gap($atts, $content = null) {
		if (prohost_in_shortcode_blogger()) return '';
		$output = prohost_gap_start() . do_shortcode($content) . prohost_gap_end();
		return apply_filters('prohost_shortcode_output', $output, 'trx_gap', $atts, $content);
	}
	prohost_require_shortcode("trx_gap", "prohost_sc_gap");
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_gap_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_gap_reg_shortcodes');
	function prohost_sc_gap_reg_shortcodes() {
	
		prohost_sc_map("trx_gap", array(
			"title" => esc_html__("Gap", "prohost"),
			"desc" => wp_kses_data( __("Insert gap (fullwidth area) in the post content. Attention! Use the gap only in the posts (pages) without left or right sidebar", "prohost") ),
			"decorate" => true,
			"container" => true,
			"params" => array(
				"_content_" => array(
					"title" => esc_html__("Gap content", "prohost"),
					"desc" => wp_kses_data( __("Gap inner content", "prohost") ),
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
				)
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_gap_reg_shortcodes_vc' ) ) {
	//add_action('prohost_action_shortcodes_list_vc', 'prohost_sc_gap_reg_shortcodes_vc');
	function prohost_sc_gap_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_gap",
			"name" => esc_html__("Gap", "prohost"),
			"description" => wp_kses_data( __("Insert gap (fullwidth area) in the post content", "prohost") ),
			"category" => esc_html__('Structure', 'prohost'),
			'icon' => 'icon_trx_gap',
			"class" => "trx_sc_collection trx_sc_gap",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => false,
			"params" => array(
				/*
				array(
					"param_name" => "content",
					"heading" => esc_html__("Gap content", "prohost"),
					"description" => wp_kses_data( __("Gap inner content", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "textarea_html"
				)
				*/
			)
		) );
		
		class WPBakeryShortCode_Trx_Gap extends PROHOST_VC_ShortCodeCollection {}
	}
}
?>