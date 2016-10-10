<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_title_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_title_theme_setup' );
	function prohost_sc_title_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_title_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_sc_title_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_title id="unique_id" style='regular|iconed' icon='' image='' background="on|off" type="1-6"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_title]
*/

if (!function_exists('prohost_sc_title')) {	
	function prohost_sc_title($atts, $content=null){	
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"type" => "1",
			"style" => "regular",
			"align" => "",
			"font_weight" => "",
			"font_size" => "",
			"color" => "",
			"icon" => "",
			"image" => "",
			"picture" => "",
			"image_size" => "small",
			"position" => "left",
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
		$css .= prohost_get_css_dimensions_from_values($width)
			.($align && $align!='none' && !prohost_param_is_inherit($align) ? 'text-align:' . esc_attr($align) .';' : '')
			.($color ? 'color:' . esc_attr($color) .';' : '')
			.($font_weight && !prohost_param_is_inherit($font_weight) ? 'font-weight:' . esc_attr($font_weight) .';' : '')
			.($font_size   ? 'font-size:' . esc_attr($font_size) .';' : '')
			;
		$type = min(6, max(1, $type));
		if ($picture > 0) {
			$attach = wp_get_attachment_image_src( $picture, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$picture = $attach[0];
		}
		$pic = $style!='iconed' 
			? '' 
			: '<span class="sc_title_icon sc_title_icon_'.esc_attr($position).'  sc_title_icon_'.esc_attr($image_size).($icon!='' && $icon!='none' ? ' '.esc_attr($icon) : '').'"'.'>'
				.($picture ? '<img src="'.esc_url($picture).'" alt="" />' : '')
				.(empty($picture) && $image && $image!='none' ? '<img src="'.esc_url(prohost_strpos($image, 'http:')!==false ? $image : prohost_get_file_url('images/icons/'.($image).'.png')).'" alt="" />' : '')
				.'</span>';
		$output = '<h' . esc_attr($type) . ($id ? ' id="'.esc_attr($id).'"' : '')
				. ' class="sc_title sc_title_'.esc_attr($style)
					.($align && $align!='none' && !prohost_param_is_inherit($align) ? ' sc_align_' . esc_attr($align) : '')
					.(!empty($class) ? ' '.esc_attr($class) : '')
					.'"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. (!prohost_param_is_off($animation) ? ' data-animation="'.esc_attr(prohost_get_animation_classes($animation)).'"' : '')
				. '>'
					. ($pic)
					. ($style=='divider' ? '<span class="sc_title_divider_before"'.($color ? ' style="background-color: '.esc_attr($color).'"' : '').'></span>' : '')
					. do_shortcode($content) 
					. ($style=='divider' ? '<span class="sc_title_divider_after"'.($color ? ' style="background-color: '.esc_attr($color).'"' : '').'></span>' : '')
				. '</h' . esc_attr($type) . '>';
		return apply_filters('prohost_shortcode_output', $output, 'trx_title', $atts, $content);
	}
	prohost_require_shortcode('trx_title', 'prohost_sc_title');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_title_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_title_reg_shortcodes');
	function prohost_sc_title_reg_shortcodes() {
	
		prohost_sc_map("trx_title", array(
			"title" => esc_html__("Title", "prohost"),
			"desc" => wp_kses_data( __("Create header tag (1-6 level) with many styles", "prohost") ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"_content_" => array(
					"title" => esc_html__("Title content", "prohost"),
					"desc" => wp_kses_data( __("Title content", "prohost") ),
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
				),
				"type" => array(
					"title" => esc_html__("Title type", "prohost"),
					"desc" => wp_kses_data( __("Title type (header level)", "prohost") ),
					"divider" => true,
					"value" => "1",
					"type" => "select",
					"options" => array(
						'1' => esc_html__('Header 1', 'prohost'),
						'2' => esc_html__('Header 2', 'prohost'),
						'3' => esc_html__('Header 3', 'prohost'),
						'4' => esc_html__('Header 4', 'prohost'),
						'5' => esc_html__('Header 5', 'prohost'),
						'6' => esc_html__('Header 6', 'prohost'),
					)
				),
				"style" => array(
					"title" => esc_html__("Title style", "prohost"),
					"desc" => wp_kses_data( __("Title style", "prohost") ),
					"value" => "regular",
					"type" => "select",
					"options" => array(
						'regular' => esc_html__('Regular', 'prohost'),
						'underline' => esc_html__('Underline', 'prohost'),
						'divider' => esc_html__('Divider', 'prohost'),
						'iconed' => esc_html__('With icon (image)', 'prohost')
					)
				),
				"align" => array(
					"title" => esc_html__("Alignment", "prohost"),
					"desc" => wp_kses_data( __("Title text alignment", "prohost") ),
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => prohost_get_sc_param('align')
				), 
				"font_size" => array(
					"title" => esc_html__("Font_size", "prohost"),
					"desc" => wp_kses_data( __("Custom font size. If empty - use theme default", "prohost") ),
					"value" => "",
					"type" => "text"
				),
				"font_weight" => array(
					"title" => esc_html__("Font weight", "prohost"),
					"desc" => wp_kses_data( __("Custom font weight. If empty or inherit - use theme default", "prohost") ),
					"value" => "",
					"type" => "select",
					"size" => "medium",
					"options" => array(
						'inherit' => esc_html__('Default', 'prohost'),
						'100' => esc_html__('Thin (100)', 'prohost'),
						'300' => esc_html__('Light (300)', 'prohost'),
						'400' => esc_html__('Normal (400)', 'prohost'),
						'600' => esc_html__('Semibold (600)', 'prohost'),
						'700' => esc_html__('Bold (700)', 'prohost'),
						'900' => esc_html__('Black (900)', 'prohost')
					)
				),
				"color" => array(
					"title" => esc_html__("Title color", "prohost"),
					"desc" => wp_kses_data( __("Select color for the title", "prohost") ),
					"value" => "",
					"type" => "color"
				),
				"icon" => array(
					"title" => esc_html__('Title font icon',  'prohost'),
					"desc" => wp_kses_data( __("Select font icon for the title from Fontello icons set (if style=iconed)",  'prohost') ),
					"dependency" => array(
						'style' => array('iconed')
					),
					"value" => "",
					"type" => "icons",
					"options" => prohost_get_sc_param('icons')
				),
				"image" => array(
					"title" => esc_html__('or image icon',  'prohost'),
					"desc" => wp_kses_data( __("Select image icon for the title instead icon above (if style=iconed)",  'prohost') ),
					"dependency" => array(
						'style' => array('iconed')
					),
					"value" => "",
					"type" => "images",
					"size" => "small",
					"options" => prohost_get_sc_param('images')
				),
				"picture" => array(
					"title" => esc_html__('or URL for image file', "prohost"),
					"desc" => wp_kses_data( __("Select or upload image or write URL from other site (if style=iconed)", "prohost") ),
					"dependency" => array(
						'style' => array('iconed')
					),
					"readonly" => false,
					"value" => "",
					"type" => "media"
				),
				"image_size" => array(
					"title" => esc_html__('Image (picture) size', "prohost"),
					"desc" => wp_kses_data( __("Select image (picture) size (if style='iconed')", "prohost") ),
					"dependency" => array(
						'style' => array('iconed')
					),
					"value" => "small",
					"type" => "checklist",
					"options" => array(
						'small' => esc_html__('Small', 'prohost'),
						'medium' => esc_html__('Medium', 'prohost'),
						'large' => esc_html__('Large', 'prohost')
					)
				),
				"position" => array(
					"title" => esc_html__('Icon (image) position', "prohost"),
					"desc" => wp_kses_data( __("Select icon (image) position (if style=iconed)", "prohost") ),
					"dependency" => array(
						'style' => array('iconed')
					),
					"value" => "left",
					"type" => "checklist",
					"options" => array(
						'top' => esc_html__('Top', 'prohost'),
						'left' => esc_html__('Left', 'prohost')
					)
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
if ( !function_exists( 'prohost_sc_title_reg_shortcodes_vc' ) ) {
	//add_action('prohost_action_shortcodes_list_vc', 'prohost_sc_title_reg_shortcodes_vc');
	function prohost_sc_title_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_title",
			"name" => esc_html__("Title", "prohost"),
			"description" => wp_kses_data( __("Create header tag (1-6 level) with many styles", "prohost") ),
			"category" => esc_html__('Content', 'prohost'),
			'icon' => 'icon_trx_title',
			"class" => "trx_sc_single trx_sc_title",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "content",
					"heading" => esc_html__("Title content", "prohost"),
					"description" => wp_kses_data( __("Title content", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "textarea_html"
				),
				array(
					"param_name" => "type",
					"heading" => esc_html__("Title type", "prohost"),
					"description" => wp_kses_data( __("Title type (header level)", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
						esc_html__('Header 1', 'prohost') => '1',
						esc_html__('Header 2', 'prohost') => '2',
						esc_html__('Header 3', 'prohost') => '3',
						esc_html__('Header 4', 'prohost') => '4',
						esc_html__('Header 5', 'prohost') => '5',
						esc_html__('Header 6', 'prohost') => '6'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "style",
					"heading" => esc_html__("Title style", "prohost"),
					"description" => wp_kses_data( __("Title style: only text (regular) or with icon/image (iconed)", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
						esc_html__('Regular', 'prohost') => 'regular',
						esc_html__('Underline', 'prohost') => 'underline',
						esc_html__('Divider', 'prohost') => 'divider',
						esc_html__('With icon (image)', 'prohost') => 'iconed'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", "prohost"),
					"description" => wp_kses_data( __("Title text alignment", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(prohost_get_sc_param('align')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "font_size",
					"heading" => esc_html__("Font size", "prohost"),
					"description" => wp_kses_data( __("Custom font size. If empty - use theme default", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "font_weight",
					"heading" => esc_html__("Font weight", "prohost"),
					"description" => wp_kses_data( __("Custom font weight. If empty or inherit - use theme default", "prohost") ),
					"class" => "",
					"value" => array(
						esc_html__('Default', 'prohost') => 'inherit',
						esc_html__('Thin (100)', 'prohost') => '100',
						esc_html__('Light (300)', 'prohost') => '300',
						esc_html__('Normal (400)', 'prohost') => '400',
						esc_html__('Semibold (600)', 'prohost') => '600',
						esc_html__('Bold (700)', 'prohost') => '700',
						esc_html__('Black (900)', 'prohost') => '900'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Title color", "prohost"),
					"description" => wp_kses_data( __("Select color for the title", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Title font icon", "prohost"),
					"description" => wp_kses_data( __("Select font icon for the title from Fontello icons set (if style=iconed)", "prohost") ),
					"class" => "",
					"group" => esc_html__('Icon &amp; Image', 'prohost'),
					'dependency' => array(
						'element' => 'style',
						'value' => array('iconed')
					),
					"value" => prohost_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "image",
					"heading" => esc_html__("or image icon", "prohost"),
					"description" => wp_kses_data( __("Select image icon for the title instead icon above (if style=iconed)", "prohost") ),
					"class" => "",
					"group" => esc_html__('Icon &amp; Image', 'prohost'),
					'dependency' => array(
						'element' => 'style',
						'value' => array('iconed')
					),
					"value" => prohost_get_sc_param('images'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "picture",
					"heading" => esc_html__("or select uploaded image", "prohost"),
					"description" => wp_kses_data( __("Select or upload image or write URL from other site (if style=iconed)", "prohost") ),
					"group" => esc_html__('Icon &amp; Image', 'prohost'),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "image_size",
					"heading" => esc_html__("Image (picture) size", "prohost"),
					"description" => wp_kses_data( __("Select image (picture) size (if style=iconed)", "prohost") ),
					"group" => esc_html__('Icon &amp; Image', 'prohost'),
					"class" => "",
					"value" => array(
						esc_html__('Small', 'prohost') => 'small',
						esc_html__('Medium', 'prohost') => 'medium',
						esc_html__('Large', 'prohost') => 'large'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "position",
					"heading" => esc_html__("Icon (image) position", "prohost"),
					"description" => wp_kses_data( __("Select icon (image) position (if style=iconed)", "prohost") ),
					"group" => esc_html__('Icon &amp; Image', 'prohost'),
					"class" => "",
					"std" => "left",
					"value" => array(
						esc_html__('Top', 'prohost') => 'top',
						esc_html__('Left', 'prohost') => 'left'
					),
					"type" => "dropdown"
				),
				prohost_get_vc_param('id'),
				prohost_get_vc_param('class'),
				prohost_get_vc_param('animation'),
				prohost_get_vc_param('css'),
				prohost_get_vc_param('margin_top'),
				prohost_get_vc_param('margin_bottom'),
				prohost_get_vc_param('margin_left'),
				prohost_get_vc_param('margin_right')
			),
			'js_view' => 'VcTrxTextView'
		) );
		
		class WPBakeryShortCode_Trx_Title extends PROHOST_VC_ShortCodeSingle {}
	}
}
?>