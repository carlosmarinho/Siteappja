<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_audio_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_audio_theme_setup' );
	function prohost_sc_audio_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_audio_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_sc_audio_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_audio url="http://trex2.themerex.dnw/wp-content/uploads/2014/12/Dream-Music-Relax.mp3" image="http://trex2.themerex.dnw/wp-content/uploads/2014/10/post_audio.jpg" title="Insert Audio Title Here" author="Lily Hunter" controls="show" autoplay="off"]
*/

if (!function_exists('prohost_sc_audio')) {	
	function prohost_sc_audio($atts, $content = null) {
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"author" => "",
			"image" => "",
			"mp3" => '',
			"wav" => '',
			"src" => '',
			"url" => '',
			"align" => '',
			"controls" => "",
			"autoplay" => "",
			"frame" => "on",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"width" => '',
			"height" => '',
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		if ($src=='' && $url=='' && isset($atts[0])) {
			$src = $atts[0];
		}
		if ($src=='') {
			if ($url) $src = $url;
			else if ($mp3) $src = $mp3;
			else if ($wav) $src = $wav;
		}
		if ($image > 0) {
			$attach = wp_get_attachment_image_src( $image, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$image = $attach[0];
		}
		$class .= ($class ? ' ' : '') . prohost_get_css_position_as_classes($top, $right, $bottom, $left);
		$data = ($title != ''  ? ' data-title="'.esc_attr($title).'"'   : '')
				. ($author != '' ? ' data-author="'.esc_attr($author).'"' : '')
				. ($image != ''  ? ' data-image="'.esc_url($image).'"'   : '')
				. ($align && $align!='none' ? ' data-align="'.esc_attr($align).'"' : '')
				. (!prohost_param_is_off($animation) ? ' data-animation="'.esc_attr(prohost_get_animation_classes($animation)).'"' : '');
		$audio = '<audio'
			. ($id ? ' id="'.esc_attr($id).'"' : '')
			. ' class="sc_audio' . (!empty($class) ? ' '.esc_attr($class) : '') . '"'
			. ' src="'.esc_url($src).'"'
			. (prohost_param_is_on($controls) ? ' controls="controls"' : '')
			. (prohost_param_is_on($autoplay) && is_single() ? ' autoplay="autoplay"' : '')
			. ( !empty($width) ? ' width="' . esc_attr($width) .'"' : '')
			. ( !empty($height) ? ' height="' . esc_attr($height) .'"' : '')
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
			. ($data)
			. '></audio>';
		if ( prohost_get_custom_option('substitute_audio')=='no') {
			if (prohost_param_is_on($frame)) {
				$audio = prohost_get_audio_frame($audio, $image, $s);
			}
		} else {
			if ((isset($_GET['vc_editable']) && $_GET['vc_editable']=='true') && (isset($_POST['action']) && $_POST['action']=='vc_load_shortcode')) {
				$audio = prohost_substitute_audio($audio, false);
			}
		}
		if (prohost_get_theme_option('use_mediaelement')=='yes')
			prohost_enqueue_script('wp-mediaelement');
		return apply_filters('prohost_shortcode_output', $audio, 'trx_audio', $atts, $content);
	}
	prohost_require_shortcode("trx_audio", "prohost_sc_audio");
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_audio_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_audio_reg_shortcodes');
	function prohost_sc_audio_reg_shortcodes() {
	
		prohost_sc_map("trx_audio", array(
			"title" => esc_html__("Audio", "prohost"),
			"desc" => wp_kses_data( __("Insert audio player", "prohost") ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"url" => array(
					"title" => esc_html__("URL for audio file", "prohost"),
					"desc" => wp_kses_data( __("URL for audio file", "prohost") ),
					"readonly" => false,
					"value" => "",
					"type" => "media",
					"before" => array(
						'title' => esc_html__('Choose audio', 'prohost'),
						'action' => 'media_upload',
						'type' => 'audio',
						'multiple' => false,
						'linked_field' => '',
						'captions' => array( 	
							'choose' => esc_html__('Choose audio file', 'prohost'),
							'update' => esc_html__('Select audio file', 'prohost')
						)
					),
					"after" => array(
						'icon' => 'icon-cancel',
						'action' => 'media_reset'
					)
				),
				"image" => array(
					"title" => esc_html__("Cover image", "prohost"),
					"desc" => wp_kses_data( __("Select or upload image or write URL from other site for audio cover", "prohost") ),
					"readonly" => false,
					"value" => "",
					"type" => "media"
				),
				"title" => array(
					"title" => esc_html__("Title", "prohost"),
					"desc" => wp_kses_data( __("Title of the audio file", "prohost") ),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
				"author" => array(
					"title" => esc_html__("Author", "prohost"),
					"desc" => wp_kses_data( __("Author of the audio file", "prohost") ),
					"value" => "",
					"type" => "text"
				),
				"controls" => array(
					"title" => esc_html__("Show controls", "prohost"),
					"desc" => wp_kses_data( __("Show controls in audio player", "prohost") ),
					"divider" => true,
					"size" => "medium",
					"value" => "show",
					"type" => "switch",
					"options" => prohost_get_sc_param('show_hide')
				),
				"autoplay" => array(
					"title" => esc_html__("Autoplay audio", "prohost"),
					"desc" => wp_kses_data( __("Autoplay audio on page load", "prohost") ),
					"value" => "off",
					"type" => "switch",
					"options" => prohost_get_sc_param('on_off')
				),
				"align" => array(
					"title" => esc_html__("Align", "prohost"),
					"desc" => wp_kses_data( __("Select block alignment", "prohost") ),
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
if ( !function_exists( 'prohost_sc_audio_reg_shortcodes_vc' ) ) {
	//add_action('prohost_action_shortcodes_list_vc', 'prohost_sc_audio_reg_shortcodes_vc');
	function prohost_sc_audio_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_audio",
			"name" => esc_html__("Audio", "prohost"),
			"description" => wp_kses_data( __("Insert audio player", "prohost") ),
			"category" => esc_html__('Content', 'prohost'),
			'icon' => 'icon_trx_audio',
			"class" => "trx_sc_single trx_sc_audio",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "url",
					"heading" => esc_html__("URL for audio file", "prohost"),
					"description" => wp_kses_data( __("Put here URL for audio file", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "image",
					"heading" => esc_html__("Cover image", "prohost"),
					"description" => wp_kses_data( __("Select or upload image or write URL from other site for audio cover", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", "prohost"),
					"description" => wp_kses_data( __("Title of the audio file", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "author",
					"heading" => esc_html__("Author", "prohost"),
					"description" => wp_kses_data( __("Author of the audio file", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "controls",
					"heading" => esc_html__("Controls", "prohost"),
					"description" => wp_kses_data( __("Show/hide controls", "prohost") ),
					"class" => "",
					"value" => array("Hide controls" => "hide" ),
					"type" => "checkbox"
				),
				array(
					"param_name" => "autoplay",
					"heading" => esc_html__("Autoplay", "prohost"),
					"description" => wp_kses_data( __("Autoplay audio on page load", "prohost") ),
					"class" => "",
					"value" => array("Autoplay" => "on" ),
					"type" => "checkbox"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", "prohost"),
					"description" => wp_kses_data( __("Select block alignment", "prohost") ),
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
			),
		) );
		
		class WPBakeryShortCode_Trx_Audio extends PROHOST_VC_ShortCodeSingle {}
	}
}
?>