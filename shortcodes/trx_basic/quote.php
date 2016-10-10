<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_quote_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_quote_theme_setup' );
	function prohost_sc_quote_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_quote_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_sc_quote_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_quote id="unique_id" cite="url" title=""]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/quote]
*/

if (!function_exists('prohost_sc_quote')) {	
	function prohost_sc_quote($atts, $content=null){	
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"cite" => "",
            "style" => "1",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"width" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . prohost_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= prohost_get_css_dimensions_from_values($width);
        $style = min(2, max(1, $style));
		$cite_param = $cite != '' ? ' cite="'.esc_attr($cite).'"' : '';
		$title = $title=='' ? $cite : $title;
		$content = do_shortcode($content);
		if (prohost_substr($content, 0, 2)!='<p') $content = '<p>' . ($content) . '</p>';
		$output = '<blockquote' 
			. ($id ? ' id="'.esc_attr($id).'"' : '') . ($cite_param) 
			. ' class="sc_quote'. (!empty($class) ? ' '.esc_attr($class) : '')
            . ' sc_quote_style_' . esc_attr($style)
            .'"'
			. (!prohost_param_is_off($animation) ? ' data-animation="'.esc_attr(prohost_get_animation_classes($animation)).'"' : '')
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
			. '>'
				. ($content)
				. ($title == '' ? '' : ('<p class="sc_quote_title">' . ($cite!='' ? '<a href="'.esc_url($cite).'">' : '') . ($title) . ($cite!='' ? '</a>' : '') . '</p>'))
			.'</blockquote>';
		return apply_filters('prohost_shortcode_output', $output, 'trx_quote', $atts, $content);
	}
	prohost_require_shortcode('trx_quote', 'prohost_sc_quote');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_quote_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_quote_reg_shortcodes');
	function prohost_sc_quote_reg_shortcodes() {
	
		prohost_sc_map("trx_quote", array(
			"title" => esc_html__("Quote", "prohost"),
			"desc" => wp_kses_data( __("Quote text", "prohost") ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"cite" => array(
					"title" => esc_html__("Quote cite", "prohost"),
					"desc" => wp_kses_data( __("URL for quote cite", "prohost") ),
					"value" => "",
					"type" => "text"
				),
				"title" => array(
					"title" => esc_html__("Title (author)", "prohost"),
					"desc" => wp_kses_data( __("Quote title (author name)", "prohost") ),
					"value" => "",
					"type" => "text"
				),
                "style" => array(
                    "title" => esc_html__("Style", "prohost"),
                    "desc" => wp_kses_data( __("Dropcaps style", "prohost") ),
                    "value" => "1",
                    "type" => "checklist",
                    "options" => prohost_get_list_styles(1, 2)
                ),
				"_content_" => array(
					"title" => esc_html__("Quote content", "prohost"),
					"desc" => wp_kses_data( __("Quote content", "prohost") ),
					"rows" => 4,
					"value" => "",
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
if ( !function_exists( 'prohost_sc_quote_reg_shortcodes_vc' ) ) {
	//add_action('prohost_action_shortcodes_list_vc', 'prohost_sc_quote_reg_shortcodes_vc');
	function prohost_sc_quote_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_quote",
			"name" => esc_html__("Quote", "prohost"),
			"description" => wp_kses_data( __("Quote text", "prohost") ),
			"category" => esc_html__('Content', 'prohost'),
			'icon' => 'icon_trx_quote',
			"class" => "trx_sc_single trx_sc_quote",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "cite",
					"heading" => esc_html__("Quote cite", "prohost"),
					"description" => wp_kses_data( __("URL for the quote cite link", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title (author)", "prohost"),
					"description" => wp_kses_data( __("Quote title (author name)", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
                array(
                    "param_name" => "style",
                    "heading" => esc_html__("Style", "prohost"),
                    "description" => wp_kses_data( __("Dropcaps style", "prohost") ),
                    "admin_label" => true,
                    "class" => "",
                    "value" => array_flip(prohost_get_list_styles(1, 2)),
                    "type" => "dropdown"
                ),
				array(
					"param_name" => "content",
					"heading" => esc_html__("Quote content", "prohost"),
					"description" => wp_kses_data( __("Quote content", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "textarea_html"
				),
				prohost_get_vc_param('id'),
				prohost_get_vc_param('class'),
				prohost_get_vc_param('animation'),
				prohost_get_vc_param('css'),
				prohost_vc_width(),
				prohost_get_vc_param('margin_top'),
				prohost_get_vc_param('margin_bottom'),
				prohost_get_vc_param('margin_left'),
				prohost_get_vc_param('margin_right')
			),
			'js_view' => 'VcTrxTextView'
		) );
		
		class WPBakeryShortCode_Trx_Quote extends PROHOST_VC_ShortCodeSingle {}
	}
}
?>