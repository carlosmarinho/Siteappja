<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_search_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_search_theme_setup' );
	function prohost_sc_search_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_search_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_sc_search_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_search id="unique_id" open="yes|no"]
*/

if (!function_exists('prohost_sc_search')) {	
	function prohost_sc_search($atts, $content=null){	
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "regular",
			"state" => "fixed",
			"scheme" => "original",
			"ajax" => "",
			"title" => esc_html__('Search', 'prohost'),
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
		if (empty($ajax)) $ajax = prohost_get_theme_option('use_ajax_search');
		// Load core messages
		prohost_enqueue_messages();
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') . ' class="search_wrap search_style_'.esc_attr($style).' search_state_'.esc_attr($state)
						. (prohost_param_is_on($ajax) ? ' search_ajax' : '')
						. ($class ? ' '.esc_attr($class) : '')
						. '"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
					. (!prohost_param_is_off($animation) ? ' data-animation="'.esc_attr(prohost_get_animation_classes($animation)).'"' : '')
					. '>
						<div class="search_form_wrap">
							<form role="search" method="get" class="search_form" action="' . esc_url(home_url('/')) . '">
								<button type="submit" class="search_submit icon-search-light" title="' . ($state=='closed' ? esc_attr__('Open search', 'prohost') : esc_attr__('Start search', 'prohost')) . '"></button>
								<input type="text" class="search_field" placeholder="' . esc_attr($title) . '" value="' . esc_attr(get_search_query()) . '" name="s" />
							</form>
						</div>
						<div class="search_results widget_area' . ($scheme && !prohost_param_is_off($scheme) && !prohost_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '') . '"><a class="search_results_close icon-cancel"></a><div class="search_results_content"></div></div>
				</div>';
		return apply_filters('prohost_shortcode_output', $output, 'trx_search', $atts, $content);
	}
	prohost_require_shortcode('trx_search', 'prohost_sc_search');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_search_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_search_reg_shortcodes');
	function prohost_sc_search_reg_shortcodes() {
	
		prohost_sc_map("trx_search", array(
			"title" => esc_html__("Search", "prohost"),
			"desc" => wp_kses_data( __("Show search form", "prohost") ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"style" => array(
					"title" => esc_html__("Style", "prohost"),
					"desc" => wp_kses_data( __("Select style to display search field", "prohost") ),
					"value" => "regular",
					"options" => array(
						"regular" => esc_html__('Regular', 'prohost'),
						"rounded" => esc_html__('Rounded', 'prohost')
					),
					"type" => "checklist"
				),
				"state" => array(
					"title" => esc_html__("State", "prohost"),
					"desc" => wp_kses_data( __("Select search field initial state", "prohost") ),
					"value" => "fixed",
					"options" => array(
						"fixed"  => esc_html__('Fixed',  'prohost'),
						"opened" => esc_html__('Opened', 'prohost'),
						"closed" => esc_html__('Closed', 'prohost')
					),
					"type" => "checklist"
				),
				"title" => array(
					"title" => esc_html__("Title", "prohost"),
					"desc" => wp_kses_data( __("Title (placeholder) for the search field", "prohost") ),
					"value" => esc_html__("Search &hellip;", 'prohost'),
					"type" => "text"
				),
				"ajax" => array(
					"title" => esc_html__("AJAX", "prohost"),
					"desc" => wp_kses_data( __("Search via AJAX or reload page", "prohost") ),
					"value" => "yes",
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
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_search_reg_shortcodes_vc' ) ) {
	//add_action('prohost_action_shortcodes_list_vc', 'prohost_sc_search_reg_shortcodes_vc');
	function prohost_sc_search_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_search",
			"name" => esc_html__("Search form", "prohost"),
			"description" => wp_kses_data( __("Insert search form", "prohost") ),
			"category" => esc_html__('Content', 'prohost'),
			'icon' => 'icon_trx_search',
			"class" => "trx_sc_single trx_sc_search",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "style",
					"heading" => esc_html__("Style", "prohost"),
					"description" => wp_kses_data( __("Select style to display search field", "prohost") ),
					"class" => "",
					"value" => array(
						esc_html__('Regular', 'prohost') => "regular",
						esc_html__('Flat', 'prohost') => "flat"
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "state",
					"heading" => esc_html__("State", "prohost"),
					"description" => wp_kses_data( __("Select search field initial state", "prohost") ),
					"class" => "",
					"value" => array(
						esc_html__('Fixed', 'prohost')  => "fixed",
						esc_html__('Opened', 'prohost') => "opened",
						esc_html__('Closed', 'prohost') => "closed"
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", "prohost"),
					"description" => wp_kses_data( __("Title (placeholder) for the search field", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => esc_html__("Search &hellip;", 'prohost'),
					"type" => "textfield"
				),
				array(
					"param_name" => "ajax",
					"heading" => esc_html__("AJAX", "prohost"),
					"description" => wp_kses_data( __("Search via AJAX or reload page", "prohost") ),
					"class" => "",
					"value" => array(esc_html__('Use AJAX search', 'prohost') => 'yes'),
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
			)
		) );
		
		class WPBakeryShortCode_Trx_Search extends PROHOST_VC_ShortCodeSingle {}
	}
}
?>