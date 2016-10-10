<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_tooltip_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_tooltip_theme_setup' );
	function prohost_sc_tooltip_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_tooltip_reg_shortcodes');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_tooltip id="unique_id" title="Tooltip text here"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/tooltip]
*/

if (!function_exists('prohost_sc_tooltip')) {	
	function prohost_sc_tooltip($atts, $content=null){	
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts)));
		$output = '<span' . ($id ? ' id="'.esc_attr($id).'"' : '') 
					. ' class="sc_tooltip_parent'. (!empty($class) ? ' '.esc_attr($class) : '').'"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
					. '>'
						. do_shortcode($content)
						. '<span class="sc_tooltip">' . ($title) . '</span>'
					. '</span>';
		return apply_filters('prohost_shortcode_output', $output, 'trx_tooltip', $atts, $content);
	}
	prohost_require_shortcode('trx_tooltip', 'prohost_sc_tooltip');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_tooltip_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_tooltip_reg_shortcodes');
	function prohost_sc_tooltip_reg_shortcodes() {
	
		prohost_sc_map("trx_tooltip", array(
			"title" => esc_html__("Tooltip", "prohost"),
			"desc" => wp_kses_data( __("Create tooltip for selected text", "prohost") ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"title" => array(
					"title" => esc_html__("Title", "prohost"),
					"desc" => wp_kses_data( __("Tooltip title (required)", "prohost") ),
					"value" => "",
					"type" => "text"
				),
				"_content_" => array(
					"title" => esc_html__("Tipped content", "prohost"),
					"desc" => wp_kses_data( __("Highlighted content with tooltip", "prohost") ),
					"divider" => true,
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
				),
				"id" => prohost_get_sc_param('id'),
				"class" => prohost_get_sc_param('class'),
				"css" => prohost_get_sc_param('css')
			)
		));
	}
}
?>