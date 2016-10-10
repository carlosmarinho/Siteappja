<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_chat_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_chat_theme_setup' );
	function prohost_sc_chat_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_chat_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_sc_chat_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_chat id="unique_id" link="url" title=""]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_chat]
[trx_chat id="unique_id" link="url" title=""]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_chat]
...
*/

if (!function_exists('prohost_sc_chat')) {	
	function prohost_sc_chat($atts, $content=null){	
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"photo" => "",
			"title" => "",
			"link" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"width" => "",
			"height" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . prohost_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= prohost_get_css_dimensions_from_values($width, $height);
		$title = $title=='' ? $link : $title;
		if (!empty($photo)) {
			if ($photo > 0) {
				$attach = wp_get_attachment_image_src( $photo, 'full' );
				if (isset($attach[0]) && $attach[0]!='')
					$photo = $attach[0];
			}
			$photo = prohost_get_resized_image_tag($photo, 75, 75);
		}
		$content = do_shortcode($content);
		if (prohost_substr($content, 0, 2)!='<p') $content = '<p>' . ($content) . '</p>';
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_chat' . (!empty($class) ? ' '.esc_attr($class) : '') . '"' 
				. (!prohost_param_is_off($animation) ? ' data-animation="'.esc_attr(prohost_get_animation_classes($animation)).'"' : '')
				. ($css ? ' style="'.esc_attr($css).'"' : '')
				. '>'
					. '<div class="sc_chat_inner">'
						. ($photo ? '<div class="sc_chat_avatar">'.($photo).'</div>' : '')
                        . '<div class="sc_chat_field">'
                            . ($title == '' ? '' : ('<div class="sc_chat_title">' . ($link!='' ? '<a href="'.esc_url($link).'">' : '') . ($title) . ($link!='' ? '</a>' : '') . '</div>'))
                            . '<div class="sc_chat_content">'.($content).'</div>'
                        . '</div>'
					. '</div>'
				. '</div>';
		return apply_filters('prohost_shortcode_output', $output, 'trx_chat', $atts, $content);
	}
	prohost_require_shortcode('trx_chat', 'prohost_sc_chat');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_chat_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_chat_reg_shortcodes');
	function prohost_sc_chat_reg_shortcodes() {
	
		prohost_sc_map("trx_chat", array(
			"title" => esc_html__("Chat", "prohost"),
			"desc" => wp_kses_data( __("Chat message", "prohost") ),
			"decorate" => true,
			"container" => true,
			"params" => array(
				"title" => array(
					"title" => esc_html__("Item title", "prohost"),
					"desc" => wp_kses_data( __("Chat item title", "prohost") ),
					"value" => "",
					"type" => "text"
				),
				"photo" => array(
					"title" => esc_html__("Item photo", "prohost"),
					"desc" => wp_kses_data( __("Select or upload image or write URL from other site for the item photo (avatar)", "prohost") ),
					"readonly" => false,
					"value" => "",
					"type" => "media"
				),
				"link" => array(
					"title" => esc_html__("Item link", "prohost"),
					"desc" => wp_kses_data( __("Chat item link", "prohost") ),
					"value" => "",
					"type" => "text"
				),
				"_content_" => array(
					"title" => esc_html__("Chat item content", "prohost"),
					"desc" => wp_kses_data( __("Current chat item content", "prohost") ),
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
if ( !function_exists( 'prohost_sc_chat_reg_shortcodes_vc' ) ) {
	//add_action('prohost_action_shortcodes_list_vc', 'prohost_sc_chat_reg_shortcodes_vc');
	function prohost_sc_chat_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_chat",
			"name" => esc_html__("Chat", "prohost"),
			"description" => wp_kses_data( __("Chat message", "prohost") ),
			"category" => esc_html__('Content', 'prohost'),
			'icon' => 'icon_trx_chat',
			"class" => "trx_sc_container trx_sc_chat",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "title",
					"heading" => esc_html__("Item title", "prohost"),
					"description" => wp_kses_data( __("Title for current chat item", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "photo",
					"heading" => esc_html__("Item photo", "prohost"),
					"description" => wp_kses_data( __("Select or upload image or write URL from other site for the item photo (avatar)", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Link URL", "prohost"),
					"description" => wp_kses_data( __("URL for the link on chat title click", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				/*
				array(
					"param_name" => "content",
					"heading" => esc_html__("Chat item content", "prohost"),
					"description" => wp_kses_data( __("Current chat item content", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "textarea_html"
				),
				*/
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
		
		class WPBakeryShortCode_Trx_Chat extends PROHOST_VC_ShortCodeContainer {}
	}
}
?>