<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_emailer_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_emailer_theme_setup' );
	function prohost_sc_emailer_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_emailer_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_sc_emailer_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

//[trx_emailer group=""]

if (!function_exists('prohost_sc_emailer')) {	
	function prohost_sc_emailer($atts, $content = null) {
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"group" => "",
			"open" => "yes",
			"align" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => "",
			"width" => "",
			"height" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . prohost_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= prohost_get_css_dimensions_from_values($width, $height);
		// Load core messages
		prohost_enqueue_messages();
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
					. ' class="sc_emailer' . ($align && $align!='none' ? ' align' . esc_attr($align) : '') . (prohost_param_is_on($open) ? ' sc_emailer_opened' : '') . (!empty($class) ? ' '.esc_attr($class) : '') . '"' 
					. ($css ? ' style="'.esc_attr($css).'"' : '') 
					. (!prohost_param_is_off($animation) ? ' data-animation="'.esc_attr(prohost_get_animation_classes($animation)).'"' : '')
					. '>'
				. '<form class="sc_emailer_form">'
				. '<input type="text" class="sc_emailer_input" placeholder="e-mail" name="email" value="">'
				. '<a href="#" class="sc_emailer_button" title="'.esc_attr__('Submit', 'prohost').'" data-group="'.esc_attr($group ? $group : esc_html__('E-mailer subscription', 'prohost')).'">Sign Up</a>'
				. '</form>'
			. '</div>';
		return apply_filters('prohost_shortcode_output', $output, 'trx_emailer', $atts, $content);
	}
	prohost_require_shortcode("trx_emailer", "prohost_sc_emailer");
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_emailer_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_emailer_reg_shortcodes');
	function prohost_sc_emailer_reg_shortcodes() {
	
		prohost_sc_map("trx_emailer", array(
			"title" => esc_html__("E-mail collector", "prohost"),
			"desc" => wp_kses_data( __("Collect the e-mail address into specified group", "prohost") ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"group" => array(
					"title" => esc_html__("Group", "prohost"),
					"desc" => wp_kses_data( __("The name of group to collect e-mail address", "prohost") ),
					"value" => "",
					"type" => "text"
				),
				"open" => array(
					"title" => esc_html__("Open", "prohost"),
					"desc" => wp_kses_data( __("Initially open the input field on show object", "prohost") ),
					"divider" => true,
					"value" => "yes",
					"type" => "switch",
					"options" => prohost_get_sc_param('yes_no')
				),
				"align" => array(
					"title" => esc_html__("Alignment", "prohost"),
					"desc" => wp_kses_data( __("Align object to left, center or right", "prohost") ),
					"divider" => true,
					"value" => "none",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => prohost_get_sc_param('align')
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
if ( !function_exists( 'prohost_sc_emailer_reg_shortcodes_vc' ) ) {
	//add_action('prohost_action_shortcodes_list_vc', 'prohost_sc_emailer_reg_shortcodes_vc');
	function prohost_sc_emailer_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_emailer",
			"name" => esc_html__("E-mail collector", "prohost"),
			"description" => wp_kses_data( __("Collect e-mails into specified group", "prohost") ),
			"category" => esc_html__('Content', 'prohost'),
			'icon' => 'icon_trx_emailer',
			"class" => "trx_sc_single trx_sc_emailer",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "group",
					"heading" => esc_html__("Group", "prohost"),
					"description" => wp_kses_data( __("The name of group to collect e-mail address", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "open",
					"heading" => esc_html__("Opened", "prohost"),
					"description" => wp_kses_data( __("Initially open the input field on show object", "prohost") ),
					"class" => "",
					"value" => array(esc_html__('Initially opened', 'prohost') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", "prohost"),
					"description" => wp_kses_data( __("Align field to left, center or right", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(prohost_get_sc_param('align')),
					"type" => "dropdown"
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
		
		class WPBakeryShortCode_Trx_Emailer extends PROHOST_VC_ShortCodeSingle {}
	}
}
?>