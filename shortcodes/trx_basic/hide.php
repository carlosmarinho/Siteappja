<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_hide_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_hide_theme_setup' );
	function prohost_sc_hide_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_hide_reg_shortcodes');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_hide selector="unique_id"]
*/

if (!function_exists('prohost_sc_hide')) {	
	function prohost_sc_hide($atts, $content=null){	
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"selector" => "",
			"hide" => "on",
			"delay" => 0
		), $atts)));
		$selector = trim(chop($selector));
		$output = $selector == '' ? '' : 
			'<script type="text/javascript">
				jQuery(document).ready(function() {
					'.($delay>0 ? 'setTimeout(function() {' : '').'
					jQuery("'.esc_attr($selector).'").' . ($hide=='on' ? 'hide' : 'show') . '();
					'.($delay>0 ? '},'.($delay).');' : '').'
				});
			</script>';
		return apply_filters('prohost_shortcode_output', $output, 'trx_hide', $atts, $content);
	}
	prohost_require_shortcode('trx_hide', 'prohost_sc_hide');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_hide_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_hide_reg_shortcodes');
	function prohost_sc_hide_reg_shortcodes() {
	
		prohost_sc_map("trx_hide", array(
			"title" => esc_html__("Hide/Show any block", "prohost"),
			"desc" => wp_kses_data( __("Hide or Show any block with desired CSS-selector", "prohost") ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"selector" => array(
					"title" => esc_html__("Selector", "prohost"),
					"desc" => wp_kses_data( __("Any block's CSS-selector", "prohost") ),
					"value" => "",
					"type" => "text"
				),
				"hide" => array(
					"title" => esc_html__("Hide or Show", "prohost"),
					"desc" => wp_kses_data( __("New state for the block: hide or show", "prohost") ),
					"value" => "yes",
					"size" => "small",
					"options" => prohost_get_sc_param('yes_no'),
					"type" => "switch"
				)
			)
		));
	}
}
?>