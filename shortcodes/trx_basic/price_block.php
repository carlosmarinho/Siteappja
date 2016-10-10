<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_price_block_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_price_block_theme_setup' );
	function prohost_sc_price_block_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_price_block_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_sc_price_block_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

if (!function_exists('prohost_sc_price_block')) {	
	function prohost_sc_price_block($atts, $content=null){	
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"style" => 1,
			"title" => "",
			"link" => "",
			"link_text" => "",
			"icon" => "",
			"money" => "",
			"currency" => "$",
			"period" => "",
			"popular" => "",
			"align" => "",
			"scheme" => "",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"width" => "",
			"height" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$output = '';
		$class .= ($class ? ' ' : '') . prohost_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= prohost_get_css_dimensions_from_values($width, $height);
		if ($money) $money = do_shortcode('[trx_price money="'.esc_attr($money).'" period="'.esc_attr($period).'"'.($currency ? ' currency="'.esc_attr($currency).'"' : '').']');
		$content = do_shortcode(prohost_sc_clear_around($content));
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
					. ' class="sc_price_block sc_price_block_style_'.max(1, min(2, $style))
						. (!empty($class) ? ' '.esc_attr($class) : '')
						. ($scheme && !prohost_param_is_off($scheme) && !prohost_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '') 
						. ($align && $align!='none' ? ' align'.esc_attr($align) : '')
						. '"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
					. (!prohost_param_is_off($animation) ? ' data-animation="'.esc_attr(prohost_get_animation_classes($animation)).'"' : '')
					. '>'
				. (($popular == 'yes' && $style == 1 ) ? '<div class="sc_price_block_popular">' . esc_html__('POPULAR','prohost') . '</div>' : '')
				. (!empty($title) ? '<div class="sc_price_block_title"><span>'.($title).'</span></div>' : '')
				. '<div class="sc_price_block_money">'
					. (!empty($icon) ? '<div class="sc_price_block_icon '.esc_attr($icon).'"></div>' : '')
					. ($money)
				. '</div>'
				. (!empty($content) ? '<div class="sc_price_block_description">'.($content).'</div>' : '')
				. ((!empty($link_text) && $style==1) ? '<div class="sc_price_block_link">'.do_shortcode('[trx_button link="'.($link ? esc_url($link) : '#').'"]'.($link_text).'[/trx_button]').'</div>' : '')
				. ((!empty($link_text) && $style==2) ? '<div class="sc_price_block_link">'.do_shortcode('[trx_button size="medium" link="'.($link ? esc_url($link) : '#').'"]'.($link_text).'[/trx_button]').'</div>' : '')
			. '</div>';
		return apply_filters('prohost_shortcode_output', $output, 'trx_price_block', $atts, $content);
	}
	prohost_require_shortcode('trx_price_block', 'prohost_sc_price_block');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_price_block_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_price_block_reg_shortcodes');
	function prohost_sc_price_block_reg_shortcodes() {
	
		prohost_sc_map("trx_price_block", array(
			"title" => esc_html__("Price block", "prohost"),
			"desc" => wp_kses_data( __("Insert price block with title, price and description", "prohost") ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"style" => array(
					"title" => esc_html__("Block style", "prohost"),
					"desc" => wp_kses_data( __("Select style for this price block", "prohost") ),
					"value" => 1,
					"options" => prohost_get_list_styles(1, 2),
					"type" => "checklist"
				),
				"title" => array(
					"title" => esc_html__("Title", "prohost"),
					"desc" => wp_kses_data( __("Block title", "prohost") ),
					"value" => "",
					"type" => "text"
				),
				"link" => array(
					"title" => esc_html__("Link URL", "prohost"),
					"desc" => wp_kses_data( __("URL for link from button (at bottom of the block)", "prohost") ),
					"value" => "",
					"type" => "text"
				),
				"link_text" => array(
					"title" => esc_html__("Link text", "prohost"),
					"desc" => wp_kses_data( __("Text (caption) for the link button (at bottom of the block). If empty - button not showed", "prohost") ),
					"value" => "",
					"type" => "text"
				),
				"icon" => array(
					"title" => esc_html__("Icon",  'prohost'),
					"desc" => wp_kses_data( __('Select icon from Fontello icons set (placed before/instead price)',  'prohost') ),
					"value" => "",
					"type" => "icons",
					"options" => prohost_get_sc_param('icons')
				),
				"money" => array(
					"title" => esc_html__("Money", "prohost"),
					"desc" => wp_kses_data( __("Money value (dot or comma separated)", "prohost") ),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
				"currency" => array(
					"title" => esc_html__("Currency", "prohost"),
					"desc" => wp_kses_data( __("Currency character", "prohost") ),
					"value" => "$",
					"type" => "text"
				),
				"period" => array(
					"title" => esc_html__("Period", "prohost"),
					"desc" => wp_kses_data( __("Period text (if need). For example: monthly, daily, etc.", "prohost") ),
					"value" => "",
					"type" => "text"
				),
                "popular" => array(
                    "title" => esc_html__("Popular", "prohost"),
                    "desc" => wp_kses_data( __("Select if need add text: Popular.", "prohost") ),
                    "dependency" => array(
                        'style' => array('1')
                    ),
                    "divider" => true,
                    "value" => "no",
                    "type" => "switch",
                    "options" => prohost_get_sc_param('yes_no')
                ),
				"scheme" => array(
					"title" => esc_html__("Color scheme", "prohost"),
					"desc" => wp_kses_data( __("Select color scheme for this block", "prohost") ),
					"value" => "",
					"type" => "checklist",
					"options" => prohost_get_sc_param('schemes')
				),
				"align" => array(
					"title" => esc_html__("Alignment", "prohost"),
					"desc" => wp_kses_data( __("Align price to left or right side", "prohost") ),
					"divider" => true,
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => prohost_get_sc_param('float')
				), 
				"_content_" => array(
					"title" => esc_html__("Description", "prohost"),
					"desc" => wp_kses_data( __("Description for this price block", "prohost") ),
					"divider" => true,
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
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
if ( !function_exists( 'prohost_sc_price_block_reg_shortcodes_vc' ) ) {
	//add_action('prohost_action_shortcodes_list_vc', 'prohost_sc_price_block_reg_shortcodes_vc');
	function prohost_sc_price_block_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_price_block",
			"name" => esc_html__("Price block", "prohost"),
			"description" => wp_kses_data( __("Insert price block with title, price and description", "prohost") ),
			"category" => esc_html__('Content', 'prohost'),
			'icon' => 'icon_trx_price_block',
			"class" => "trx_sc_single trx_sc_price_block",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "style",
					"heading" => esc_html__("Block style", "prohost"),
					"desc" => wp_kses_data( __("Select style of this price block", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"std" => 1,
					"value" => array_flip(prohost_get_list_styles(1, 2)),
					"type" => "dropdown"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", "prohost"),
					"description" => wp_kses_data( __("Block title", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Link URL", "prohost"),
					"description" => wp_kses_data( __("URL for link from button (at bottom of the block)", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "link_text",
					"heading" => esc_html__("Link text", "prohost"),
					"description" => wp_kses_data( __("Text (caption) for the link button (at bottom of the block). If empty - button not showed", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Icon", "prohost"),
					"description" => wp_kses_data( __("Select icon from Fontello icons set (placed before/instead price)", "prohost") ),
					"class" => "",
					"value" => prohost_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "money",
					"heading" => esc_html__("Money", "prohost"),
					"description" => wp_kses_data( __("Money value (dot or comma separated)", "prohost") ),
					"admin_label" => true,
					"group" => esc_html__('Money', 'prohost'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "currency",
					"heading" => esc_html__("Currency symbol", "prohost"),
					"description" => wp_kses_data( __("Currency character", "prohost") ),
					"admin_label" => true,
					"group" => esc_html__('Money', 'prohost'),
					"class" => "",
					"value" => "$",
					"type" => "textfield"
				),
				array(
					"param_name" => "period",
					"heading" => esc_html__("Period", "prohost"),
					"description" => wp_kses_data( __("Period text (if need). For example: monthly, daily, etc.", "prohost") ),
					"admin_label" => true,
					"group" => esc_html__('Money', 'prohost'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
                array(
                    "param_name" => "popular",
                    "heading" => esc_html__("Popular", "prohost"),
                    "description" => wp_kses_data( __("Select if need add text: Popular.", "prohost") ),
                    'dependency' => array(
                        'element' => 'style',
                        'value' => '1'
                    ),
                    "group" => esc_html__('Money', 'prohost'),
                    "class" => "",
                    "value" => array(esc_html__('Show Popular', 'prohost') => 'yes'),
                    "type" => "checkbox"
                ),
				array(
					"param_name" => "scheme",
					"heading" => esc_html__("Color scheme", "prohost"),
					"description" => wp_kses_data( __("Select color scheme for this block", "prohost") ),
					"group" => esc_html__('Colors and Images', 'prohost'),
					"class" => "",
					"value" => array_flip(prohost_get_sc_param('schemes')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", "prohost"),
					"description" => wp_kses_data( __("Align price to left or right side", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(prohost_get_sc_param('float')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "content",
					"heading" => esc_html__("Description", "prohost"),
					"description" => wp_kses_data( __("Description for this price block", "prohost") ),
					"class" => "",
					"value" => "",
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
			'js_view' => 'VcTrxTextView'
		) );
		
		class WPBakeryShortCode_Trx_PriceBlock extends PROHOST_VC_ShortCodeSingle {}
	}
}
?>