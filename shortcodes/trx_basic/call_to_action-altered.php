<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_call_to_action_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_call_to_action_theme_setup' );
	function prohost_sc_call_to_action_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_call_to_action_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_sc_call_to_action_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_call_to_action id="unique_id" style="1|2" align="left|center|right"]
	[inner shortcodes]
[/trx_call_to_action]
*/

if (!function_exists('prohost_sc_call_to_action')) {	
	function prohost_sc_call_to_action($atts, $content=null){	
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "1",
			"align" => "center",
			"custom" => "no",
			"accent" => "no",
			"image" => "",
			"video" => "",
			"title" => "",
			"subtitle" => "",
			"description" => "",
			"link" => '',
			"link_caption" => esc_html__('Learn more', 'prohost'),
			"link2" => '',
			"link2_caption" => '',
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
			"right" => "",
			"money" => "",
			"period" => "",
			"target" => ""
		), $atts)));
	
		if (empty($id)) $id = "sc_call_to_action_".str_replace('.', '', mt_rand());
		if (empty($width)) $width = "100%";
	
		if ($image > 0) {
			$attach = wp_get_attachment_image_src( $image, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$image = $attach[0];
		}
		if (!empty($image)) {
			$thumb_sizes = prohost_get_thumb_sizes(array('layout' => 'excerpt'));
			$image = !empty($video) 
				? prohost_get_resized_image_url($image, $thumb_sizes['w'], $thumb_sizes['h']) 
				: prohost_get_resized_image_tag($image, $thumb_sizes['w'], $thumb_sizes['h']);
		}
	
		if (!empty($video)) {
			$video = '<video' . ($id ? ' id="' . esc_attr($id.'_video') . '"' : '') 
				. ' class="sc_video"'
				. ' src="' . esc_url(prohost_get_video_player_url($video)) . '"'
				. ' width="' . esc_attr($width) . '" height="' . esc_attr($height) . '"' 
				. ' data-width="' . esc_attr($width) . '" data-height="' . esc_attr($height) . '"' 
				. ' data-ratio="16:9"'
				. ($image ? ' poster="'.esc_attr($image).'" data-image="'.esc_attr($image).'"' : '') 
				. ' controls="controls" loop="loop"'
				. '>'
				. '</video>';
			if (prohost_get_custom_option('substitute_video')=='no') {
				$video = prohost_get_video_frame($video, $image, '', '');
			} else {
				if ((isset($_GET['vc_editable']) && $_GET['vc_editable']=='true') && (isset($_POST['action']) && $_POST['action']=='vc_load_shortcode')) {
					$video = prohost_substitute_video($video, $width, $height, false);
				}
			}
			if (prohost_get_theme_option('use_mediaelement')=='yes')
				prohost_enqueue_script('wp-mediaelement');
		}
		
		$class .= ($class ? ' ' : '') . prohost_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= prohost_get_css_dimensions_from_values($width, $height);
		
		$content = do_shortcode($content);
		
        $featured = ($style==1 && (!empty($content) || !empty($image) || !empty($video))
                ? '<div class="sc_call_to_action_featured column-1_2">'
                . (!empty($content)
                    ? $content
                    : (!empty($video)
                        ? $video
                        : $image)
                )
                . '</div>'
                : '');

         if ($style==2 && !empty($image)) {
             if ($image > 0) {
                 $attach = wp_get_attachment_image_src( $image, 'full' );
                 if (isset($attach[0]) && $attach[0]!='')
                     $image = $attach[0];
             }
             $featured2 = $attach[0];
         }
	
		$need_columns = ($featured || $style==2) && !in_array($align, array('center', 'none'))
							? ($style==2 ? 4 : 2)
							: 0;
		if(! empty($target))
			$target = 'target="' . $target . '"';
		
		$buttons = (!empty($link) || !empty($link2) 
						? '<div class="sc_call_to_action_buttons sc_item_buttons'.($need_columns && $style==2 ? ' column-1_'.esc_attr($need_columns) : '').'">'
							. (!empty($link) 
								? '<div class="sc_call_to_action_button sc_item_button">'.do_shortcode('[trx_button ' . $target . ' link="'.esc_url($link).'" icon="icon-right"]'.esc_html($link_caption).'[/trx_button]').'</div>' 
								: '')
							. (!empty($link2) 
								? '<div class="sc_call_to_action_button sc_item_button">'.do_shortcode('[trx_button ' . $target . ' link="'.esc_url($link2).'" icon="icon-right"]'.esc_html($link2_caption).'[/trx_button]').'</div>' 
								: '')
							. '</div>'
						: '');

        $buttons2 = (!empty($link)
								? '<div class="sc_call_to_action_button sc_item_button">'.do_shortcode('[trx_button ' . $target . ' size="medium" link="'.esc_url($link).'"]'.esc_html($link_caption).'[/trx_button]').'</div>'
								: '');

		
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_call_to_action'
					. (prohost_param_is_on($accent) ? ' sc_call_to_action_accented' : '')
					. ' sc_call_to_action_style_' . esc_attr($style) 
					. ' sc_call_to_action_align_'.esc_attr($align)
					. (!empty($class) ? ' '.esc_attr($class) : '')
					. '"'
				. (!prohost_param_is_off($animation) ? ' data-animation="'.esc_attr(prohost_get_animation_classes($animation)).'"' : '')
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
			. '>'
//				. (prohost_param_is_on($accent) ? '<div class="content_wrap">' : '')
                . ($need_columns ? '<div class="columns_wrap">' : '')
                . ($align!='right' ? $featured : '')
                . ($style==2 && $align=='right' ? $buttons : '')
                . '<div class="sc_call_to_action_info'.($need_columns ? ' column-'.esc_attr($need_columns-1).'_'.esc_attr($need_columns) : '').'">'
                    . (!empty($subtitle) ? '<h6 class="sc_call_to_action_subtitle sc_item_subtitle">' . trim(prohost_strmacros($subtitle)) . '</h6>' : '')
                    . (!empty($title) ? '<h2 class="sc_call_to_action_title sc_item_title '.(empty($description)|| empty($money) || empty($period) ? ' padding-left' : '').'">' . trim(prohost_strmacros($title)) . '</h2>' : '')
                    . ((!empty($featured2) && $style==2 && (empty($description)|| empty($money) || empty($period))) ? '<div class="img_call_to_action" style="background-image: url(' . $featured2 . ')"></div>' : '')
                    . (!empty($description) || !empty($money) || !empty($period) ? '<div class="sc_call_to_action_descr sc_item_descr ' . (!empty($featured2) ? ' padding-left' : '') . '">'
                        . '<div class="descr">'
                            . '<div class="descr_text">' . trim(prohost_strmacros($description)) . '</div>'
                            . '<div class="descr_money">'
                                . '<span class="money">' . esc_attr($money) . '</span>'
                                . '<span class="period">' . esc_attr($period) . '</span>'
                            . '</div>'
                        .  '</div>'
                        . ((!empty($featured2) && $style==2 && (!empty($description)|| !empty($money) || !empty($period))) ? '<div class="img_call_to_action" style="background-image: url(' . $featured2 . ')"></div>' : '')
                    . '</div>' : '')
                    . ($style==1 ? $buttons : '')
                    . ($style==2 ? $buttons2 : '')
                . '</div>'
                . ($align=='right' ? $featured : '')
                . ($need_columns ? '</div>' : '')
//				. (prohost_param_is_on($accent) ? '</div>' : '')
			. '</div>';
	
		return apply_filters('prohost_shortcode_output', $output, 'trx_call_to_action', $atts, $content);
	}
	prohost_require_shortcode('trx_call_to_action', 'prohost_sc_call_to_action');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_call_to_action_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_call_to_action_reg_shortcodes');
	function prohost_sc_call_to_action_reg_shortcodes() {
	
		prohost_sc_map("trx_call_to_action", array(
			"title" => esc_html__("Call to action", "prohost"),
			"desc" => wp_kses_data( __("Insert call to action block in your page (post)", "prohost") ),
			"decorate" => true,
			"container" => true,
			"params" => array(
				"title" => array(
					"title" => esc_html__("Title", "prohost"),
					"desc" => wp_kses_data( __("Title for the block", "prohost") ),
					"value" => "",
					"type" => "text"
				),
				"subtitle" => array(
					"title" => esc_html__("Subtitle", "prohost"),
					"desc" => wp_kses_data( __("Subtitle for the block", "prohost") ),
                    "dependency" => array(
                        'style' => array('1')
                    ),
					"value" => "",
					"type" => "text"
				),
				"description" => array(
					"title" => esc_html__("Description", "prohost"),
					"desc" => wp_kses_data( __("Short description for the block", "prohost") ),
					"value" => "",
					"type" => "textarea"
				),
                "money" => array(
                    "title" => esc_html__("Money", "prohost"),
                    "desc" => wp_kses_data( __("Money value (dot or comma separated)", "prohost") ),
                    "dependency" => array(
                        'style' => array('2')
                    ),
                    "value" => "",
                    "type" => "text"
                ),
                "period" => array(
                    "title" => esc_html__("Period", "prohost"),
                    "desc" => wp_kses_data( __("Period text (if need). For example: monthly, daily, etc.", "prohost") ),
                    "dependency" => array(
                        'style' => array('2')
                    ),
                    "value" => "",
                    "type" => "text"
                ),
				"style" => array(
					"title" => esc_html__("Style", "prohost"),
					"desc" => wp_kses_data( __("Select style to display block", "prohost") ),
					"value" => "1",
					"type" => "checklist",
					"options" => prohost_get_list_styles(1, 2)
				),
				"align" => array(
					"title" => esc_html__("Alignment", "prohost"),
					"desc" => wp_kses_data( __("Alignment elements in the block", "prohost") ),
                    "dependency" => array(
                        'style' => array('1')
                    ),
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => prohost_get_sc_param('align')
				),
				"accent" => array(
					"title" => esc_html__("Accented", "prohost"),
					"desc" => wp_kses_data( __("Fill entire block with Accent1 color from current color scheme", "prohost") ),
                    "dependency" => array(
                        'style' => array('1')
                    ),
					"divider" => true,
					"value" => "no",
					"type" => "switch",
					"options" => prohost_get_sc_param('yes_no')
				),
				"custom" => array(
					"title" => esc_html__("Custom", "prohost"),
					"desc" => wp_kses_data( __("Allow get featured image or video from inner shortcodes (custom) or get it from shortcode parameters below", "prohost") ),
					"divider" => true,
					"value" => "no",
					"type" => "switch",
					"options" => prohost_get_sc_param('yes_no')
				),
				"image" => array(
					"title" => esc_html__("Image", "prohost"),
					"desc" => wp_kses_data( __("Select or upload image or write URL from other site to include image into this block", "prohost") ),
					"divider" => true,
					"readonly" => false,
					"value" => "",
					"type" => "media"
				),
				"video" => array(
					"title" => esc_html__("URL for video file", "prohost"),
					"desc" => wp_kses_data( __("Select video from media library or paste URL for video file from other site to include video into this block", "prohost") ),
					"readonly" => false,
					"value" => "",
					"type" => "media",
					"before" => array(
						'title' => esc_html__('Choose video', 'prohost'),
						'action' => 'media_upload',
						'type' => 'video',
						'multiple' => false,
						'linked_field' => '',
						'captions' => array( 	
							'choose' => esc_html__('Choose video file', 'prohost'),
							'update' => esc_html__('Select video file', 'prohost')
						)
					),
					"after" => array(
						'icon' => 'icon-cancel',
						'action' => 'media_reset'
					)
				),
				"link" => array(
					"title" => esc_html__("Button URL", "prohost"),
					"desc" => wp_kses_data( __("Link URL for the button at the bottom of the block", "prohost") ),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
				"link_caption" => array(
					"title" => esc_html__("Button caption", "prohost"),
					"desc" => wp_kses_data( __("Caption for the button at the bottom of the block", "prohost") ),
					"value" => "",
					"type" => "text"
				),
				"link2" => array(
					"title" => esc_html__("Button 2 URL", "prohost"),
					"desc" => wp_kses_data( __("Link URL for the second button at the bottom of the block", "prohost") ),
                    "dependency" => array(
                        'style' => array('1')
                    ),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
				"link2_caption" => array(
					"title" => esc_html__("Button 2 caption", "prohost"),
					"desc" => wp_kses_data( __("Caption for the second button at the bottom of the block", "prohost") ),
                    "dependency" => array(
                        'style' => array('1')
                    ),
					"value" => "",
					"type" => "text"
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
if ( !function_exists( 'prohost_sc_call_to_action_reg_shortcodes_vc' ) ) {
	//add_action('prohost_action_shortcodes_list_vc', 'prohost_sc_call_to_action_reg_shortcodes_vc');
	function prohost_sc_call_to_action_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_call_to_action",
			"name" => esc_html__("Call to Action", "prohost"),
			"description" => wp_kses_data( __("Insert call to action block in your page (post)", "prohost") ),
			"category" => esc_html__('Content', 'prohost'),
			'icon' => 'icon_trx_call_to_action',
			"class" => "trx_sc_collection trx_sc_call_to_action",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "style",
					"heading" => esc_html__("Block's style", "prohost"),
					"description" => wp_kses_data( __("Select style to display this block", "prohost") ),
					"class" => "",
					"admin_label" => true,
					"value" => array_flip(prohost_get_list_styles(1, 2)),
					"type" => "dropdown"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", "prohost"),
					"description" => wp_kses_data( __("Select block alignment", "prohost") ),
                    'dependency' => array(
                        'element' => 'style',
                        'value' => '1'
                    ),
					"class" => "",
					"value" => array_flip(prohost_get_sc_param('align')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "accent",
					"heading" => esc_html__("Accent", "prohost"),
					"description" => wp_kses_data( __("Fill entire block with Accent1 color from current color scheme", "prohost") ),
                    'dependency' => array(
                        'element' => 'style',
                        'value' => '1'
                    ),
					"class" => "",
					"value" => array("Fill with Accent1 color" => "yes" ),
					"type" => "checkbox"
				),
				array(
					"param_name" => "custom",
					"heading" => esc_html__("Custom", "prohost"),
					"description" => wp_kses_data( __("Allow get featured image or video from inner shortcodes (custom) or get it from shortcode parameters below", "prohost") ),
					"class" => "",
					"value" => array("Custom content" => "yes" ),
					"type" => "checkbox"
				),
				array(
					"param_name" => "image",
					"heading" => esc_html__("Image", "prohost"),
					"description" => wp_kses_data( __("Image to display inside block", "prohost") ),
					'dependency' => array(
						'element' => 'custom',
						'is_empty' => true
					),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "video",
					"heading" => esc_html__("URL for video file", "prohost"),
					"description" => wp_kses_data( __("Paste URL for video file to display inside block", "prohost") ),
					'dependency' => array(
						'element' => 'custom',
						'is_empty' => true
					),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", "prohost"),
					"description" => wp_kses_data( __("Title for the block", "prohost") ),
					"admin_label" => true,
					"group" => esc_html__('Captions', 'prohost'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "subtitle",
					"heading" => esc_html__("Subtitle", "prohost"),
					"description" => wp_kses_data( __("Subtitle for the block", "prohost") ),
                    'dependency' => array(
                        'element' => 'style',
                        'value' => '1'
                    ),
					"group" => esc_html__('Captions', 'prohost'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "description",
					"heading" => esc_html__("Description", "prohost"),
					"description" => wp_kses_data( __("Description for the block", "prohost") ),
					"group" => esc_html__('Captions', 'prohost'),
					"class" => "",
					"value" => "",
					"type" => "textarea"
				),
                array(
                    "param_name" => "money",
                    "heading" => esc_html__("Money", "prohost"),
                    "description" => wp_kses_data( __("Money value (dot or comma separated)", "prohost") ),
                    'dependency' => array(
                        'element' => 'style',
                        'value' => '2'
                    ),
                    "admin_label" => true,
                    "group" => esc_html__('Captions', 'prohost'),
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "period",
                    "heading" => esc_html__("Period", "prohost"),
                    "description" => wp_kses_data( __("Period text (if need). For example: monthly, daily, etc.", "prohost") ),
                    'dependency' => array(
                        'element' => 'style',
                        'value' => '2'
                    ),
                    "group" => esc_html__('Captions', 'prohost'),
                    "admin_label" => true,
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
				array(
					"param_name" => "link",
					"heading" => esc_html__("Button URL", "prohost"),
					"description" => wp_kses_data( __("Link URL for the button at the bottom of the block", "prohost") ),
					"group" => esc_html__('Captions', 'prohost'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "link_caption",
					"heading" => esc_html__("Button caption", "prohost"),
					"description" => wp_kses_data( __("Caption for the button at the bottom of the block", "prohost") ),
					"group" => esc_html__('Captions', 'prohost'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "link2",
					"heading" => esc_html__("Button 2 URL", "prohost"),
					"description" => wp_kses_data( __("Link URL for the second button at the bottom of the block", "prohost") ),
                    'dependency' => array(
                        'element' => 'style',
                        'value' => '1'
                    ),
					"group" => esc_html__('Captions', 'prohost'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "link2_caption",
					"heading" => esc_html__("Button 2 caption", "prohost"),
					"description" => wp_kses_data( __("Caption for the second button at the bottom of the block", "prohost") ),
                    'dependency' => array(
                        'element' => 'style',
                        'value' => '1'
                    ),
					"group" => esc_html__('Captions', 'prohost'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
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
		
		class WPBakeryShortCode_Trx_Call_To_Action extends PROHOST_VC_ShortCodeCollection {}
	}
}
?>
