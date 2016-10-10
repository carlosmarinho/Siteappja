<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('prohost_sc_skills_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_sc_skills_theme_setup' );
	function prohost_sc_skills_theme_setup() {
		add_action('prohost_action_shortcodes_list', 		'prohost_sc_skills_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_sc_skills_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_skills id="unique_id" type="bar|pie|arc|counter" dir="horizontal|vertical" layout="rows|columns" count="" max_value="100" align="left|right"]
	[trx_skills_item title="Scelerisque pid" value="50%"]
	[trx_skills_item title="Scelerisque pid" value="50%"]
	[trx_skills_item title="Scelerisque pid" value="50%"]
[/trx_skills]
*/

if (!function_exists('prohost_sc_skills')) {	
	function prohost_sc_skills($atts, $content=null){	
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"max_value" => "100",
			"type" => "bar",
			"layout" => "",
			"dir" => "",
			"style" => "1",
			"columns" => "",
			"align" => "",
			"color" => "",
			"bg_color" => "",
			"border_color" => "",
			"arc_caption" => esc_html__("Skills", "prohost"),
			"pie_compact" => "on",
			"pie_cutout" => 0,
			"title" => "",
			"subtitle" => "",
			"description" => "",
			"link_caption" => esc_html__('Learn more', 'prohost'),
			"link" => '',
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
		prohost_storage_set('sc_skills_data', array(
			'counter' => 0,
            'columns' => 0,
            'height'  => 0,
            'type'    => $type,
            'pie_compact' => prohost_param_is_on($pie_compact) ? 'on' : 'off',
            'pie_cutout'  => max(0, min(99, $pie_cutout)),
            'color'   => $color,
            'bg_color'=> $bg_color,
            'border_color'=> $border_color,
            'legend'  => '',
            'data'    => ''
			)
		);
		prohost_enqueue_diagram($type);
		if ($type!='arc') {
			if ($layout=='' || ($layout=='columns' && $columns<1)) $layout = 'rows';
			if ($layout=='columns') prohost_storage_set_array('sc_skills_data', 'columns', $columns);
			if ($type=='bar') {
				if ($dir == '') $dir = 'horizontal';
				if ($dir == 'vertical' && $height < 1) $height = 300;
			}
		}
		if (empty($id)) $id = 'sc_skills_diagram_'.str_replace('.','',mt_rand());
		if ($max_value < 1) $max_value = 100;
		if ($style) {
			$style = max(1, min(4, $style));
			prohost_storage_set_array('sc_skills_data', 'style', $style);
		}
		prohost_storage_set_array('sc_skills_data', 'max', $max_value);
		prohost_storage_set_array('sc_skills_data', 'dir', $dir);
		prohost_storage_set_array('sc_skills_data', 'height', prohost_prepare_css_value($height));
		$class .= ($class ? ' ' : '') . prohost_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= prohost_get_css_dimensions_from_values($width);
		if (!prohost_storage_empty('sc_skills_data', 'height') && (prohost_storage_get_array('sc_skills_data', 'type') == 'arc' || (prohost_storage_get_array('sc_skills_data', 'type') == 'pie' && prohost_param_is_on(prohost_storage_get_array('sc_skills_data', 'pie_compact')))))
			$css .= 'height: '.prohost_storage_get_array('sc_skills_data', 'height');
		$content = do_shortcode($content);
		$output = '<div id="'.esc_attr($id).'"' 
					. ' class="sc_skills sc_skills_' . esc_attr($type) 
						. ($type=='bar' ? ' sc_skills_'.esc_attr($dir) : '') 
						. ($type=='pie' ? ' sc_skills_compact_'.esc_attr(prohost_storage_get_array('sc_skills_data', 'pie_compact')) : '') 
						. (!empty($class) ? ' '.esc_attr($class) : '') 
						. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
						. '"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
					. (!prohost_param_is_off($animation) ? ' data-animation="'.esc_attr(prohost_get_animation_classes($animation)).'"' : '')
					. ' data-type="'.esc_attr($type).'"'
					. ' data-caption="'.esc_attr($arc_caption).'"'
					. ($type=='bar' ? ' data-dir="'.esc_attr($dir).'"' : '')
				. '>'
					. (!empty($subtitle) ? '<h6 class="sc_skills_subtitle sc_item_subtitle">' . esc_html($subtitle) . '</h6>' : '')
					. (!empty($title) ? '<h2 class="sc_skills_title sc_item_title">' . esc_html($title) . '</h2>' : '')
					. (!empty($description) ? '<div class="sc_skills_descr sc_item_descr">' . trim($description) . '</div>' : '')
					. ($layout == 'columns' ? '<div class="columns_wrap sc_skills_'.esc_attr($layout).' sc_skills_columns_'.esc_attr($columns).'">' : '')
					. ($type=='arc' 
						? ('<div class="sc_skills_legend">'.(prohost_storage_get_array('sc_skills_data', 'legend')).'</div>'
							. '<div id="'.esc_attr($id).'_diagram" class="sc_skills_arc_canvas"></div>'
							. '<div class="sc_skills_data" style="display:none;">' . (prohost_storage_get_array('sc_skills_data', 'data')) . '</div>'
						  )
						: '')
					. ($type=='pie' && prohost_param_is_on(prohost_storage_get_array('sc_skills_data', 'pie_compact'))
						? ('<div class="sc_skills_legend">'.(prohost_storage_get_array('sc_skills_data', 'legend')).'</div>'
							. '<div id="'.esc_attr($id).'_pie_item" class="sc_skills_item">'
								. '<canvas id="'.esc_attr($id).'_pie" class="sc_skills_pie_canvas"></canvas>'
								. '<div class="sc_skills_data" style="display:none;">' . (prohost_storage_get_array('sc_skills_data', 'data')) . '</div>'
							. '</div>'
						  )
						: '')
					. ($content)
					. ($layout == 'columns' ? '</div>' : '')
					. (!empty($link) ? '<div class="sc_skills_button sc_item_button">'.do_shortcode('[trx_button link="'.esc_url($link).'" icon="icon-right"]'.esc_html($link_caption).'[/trx_button]').'</div>' : '')
				. '</div>';
		return apply_filters('prohost_shortcode_output', $output, 'trx_skills', $atts, $content);
	}
	prohost_require_shortcode('trx_skills', 'prohost_sc_skills');
}


if (!function_exists('prohost_sc_skills_item')) {	
	function prohost_sc_skills_item($atts, $content=null) {
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts( array(
			// Individual params
			"title" => "",
			"value" => "",
			"counter_plus" => "",
			"color" => "",
			"bg_color" => "",
			"border_color" => "",
			"style" => "",
			"icon" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts)));
		prohost_storage_inc_array('sc_skills_data', 'counter');
		$ed = prohost_substr($value, -1)=='%' ? '%' : '';
		$value = str_replace('%', '', $value);
		if (prohost_storage_get_array('sc_skills_data', 'max') < $value) prohost_storage_set_array('sc_skills_data', 'max', $value);
		$percent = round($value / prohost_storage_get_array('sc_skills_data', 'max') * 100);
		$start = 0;
		$stop = $value;
		$steps = 100;
		$step = max(1, round(prohost_storage_get_array('sc_skills_data', 'max')/$steps));
		$speed = mt_rand(10,40);
		$animation = round(($stop - $start) / $step * $speed);
		$title_block = '<div class="sc_skills_info"><div class="sc_skills_label">' . ($title) . '</div></div>';
		$old_color = $color;
		if (empty($color)) $color = prohost_storage_get_array('sc_skills_data', 'color');
		if (empty($color)) $color = prohost_get_scheme_color('accent1', $color);
		if (empty($bg_color)) $bg_color = prohost_storage_get_array('sc_skills_data', 'bg_color');
		if (empty($bg_color)) $bg_color = prohost_get_scheme_color('bg_color', $bg_color);
		if (empty($border_color)) $border_color = prohost_storage_get_array('sc_skills_data', 'border_color');
		if (empty($border_color)) $border_color = prohost_get_scheme_color('bd_color', $border_color);;
		if (empty($style)) $style = prohost_storage_get_array('sc_skills_data', 'style');
		$style = max(1, min(4, $style));
		$output = '';
		if (prohost_storage_get_array('sc_skills_data', 'type') == 'arc' || (prohost_storage_get_array('sc_skills_data', 'type') == 'pie' && prohost_param_is_on(prohost_storage_get_array('sc_skills_data', 'pie_compact')))) {
			if (prohost_storage_get_array('sc_skills_data', 'type') == 'arc' && empty($old_color)) {
				$rgb = prohost_hex2rgb($color);
				$color = 'rgba('.(int)$rgb['r'].','.(int)$rgb['g'].','.(int)$rgb['b'].','.(1 - 0.1*(prohost_storage_get_array('sc_skills_data', 'counter')-1)).')';
			}
			prohost_storage_concat_array('sc_skills_data', 'legend', 
				'<div class="sc_skills_legend_item"><span class="sc_skills_legend_marker" style="background-color:'.esc_attr($color).'"></span><span class="sc_skills_legend_title">' . ($title) . '</span><span class="sc_skills_legend_value">' . ($value) . '</span></div>'
			);
			prohost_storage_concat_array('sc_skills_data', 'data', 
				'<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
					. ' class="'.esc_attr(prohost_storage_get_array('sc_skills_data', 'type')).'"'
					. (prohost_storage_get_array('sc_skills_data', 'type')=='pie'
						? ( ' data-start="'.esc_attr($start).'"'
							. ' data-stop="'.esc_attr($stop).'"'
							. ' data-step="'.esc_attr($step).'"'
							. ' data-steps="'.esc_attr($steps).'"'
							. ' data-max="'.esc_attr(prohost_storage_get_array('sc_skills_data', 'max')).'"'
							. ' data-speed="'.esc_attr($speed).'"'
							. ' data-duration="'.esc_attr($animation).'"'
							. ' data-color="'.esc_attr($color).'"'
							. ' data-bg_color="'.esc_attr($bg_color).'"'
							. ' data-border_color="'.esc_attr($border_color).'"'
							. ' data-cutout="'.esc_attr(prohost_storage_get_array('sc_skills_data', 'pie_cutout')).'"'
							. ' data-easing="easeOutCirc"'
							. ' data-ed="'.esc_attr($ed).'"'
							)
						: '')
					. '><input type="hidden" class="text" value="'.esc_attr($title).'" /><input type="hidden" class="percent" value="'.esc_attr($percent).'" /><input type="hidden" class="color" value="'.esc_attr($color).'" /></div>'
			);
		} else {
			$output .= (prohost_storage_get_array('sc_skills_data', 'columns') > 0 
							? '<div class="sc_skills_column column-1_'.esc_attr(prohost_storage_get_array('sc_skills_data', 'columns')).'">' 
							: '')
					. (prohost_storage_get_array('sc_skills_data', 'type')=='bar' && prohost_storage_get_array('sc_skills_data', 'dir')=='horizontal' ? $title_block : '')
					. '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
						. ' class="sc_skills_item' . ($style ? ' sc_skills_style_'.esc_attr($style) : '') 
							. (!empty($class) ? ' '.esc_attr($class) : '')
							. (prohost_storage_get_array('sc_skills_data', 'counter') % 2 == 1 ? ' odd' : ' even') 
							. (prohost_storage_get_array('sc_skills_data', 'counter') == 1 ? ' first' : '') 
							. '"'
						. (prohost_storage_get_array('sc_skills_data', 'height') !='' || $css 
							? ' style="' 
								. (prohost_storage_get_array('sc_skills_data', 'height') !='' 
										? 'height: '.esc_attr(prohost_storage_get_array('sc_skills_data', 'height')).';' 
										: '') 
								. ($css) 
								. '"' 
							: '')
					. '>'
					. (!empty($icon) ? '<div class="sc_skills_icon '.esc_attr($icon).'"></div>' : '');
			if (in_array(prohost_storage_get_array('sc_skills_data', 'type'), array('bar', 'counter'))) {
				$output .= '<div class="sc_skills_count"' . (prohost_storage_get_array('sc_skills_data', 'type')=='bar' && $color ? ' style="background-color:' . esc_attr($color) . '; border-color:' . esc_attr($color) . '"' : '') . '>'
							. '<div class="sc_skills_total"'
								. ' data-start="'.esc_attr($start).'"'
								. ' data-stop="'.esc_attr($stop).'"'
								. ' data-step="'.esc_attr($step).'"'
								. ' data-max="'.esc_attr(prohost_storage_get_array('sc_skills_data', 'max')).'"'
								. ' data-speed="'.esc_attr($speed).'"'
								. ' data-duration="'.esc_attr($animation).'"'
								. ' data-ed="'.esc_attr($ed).'">'
                                . ($start) . ($ed)
							.'</div>'
                            . ($counter_plus == 'yes' ? '<div class="icon-plus"></div> ' : '')
                    . '</div>';
			} else if (prohost_storage_get_array('sc_skills_data', 'type')=='pie') {
				if (empty($id)) $id = 'sc_skills_canvas_'.str_replace('.','',mt_rand());
				$output .= '<canvas id="'.esc_attr($id).'"></canvas>'
					. '<div class="sc_skills_total"'
						. ' data-start="'.esc_attr($start).'"'
						. ' data-stop="'.esc_attr($stop).'"'
						. ' data-step="'.esc_attr($step).'"'
						. ' data-steps="'.esc_attr($steps).'"'
						. ' data-max="'.esc_attr(prohost_storage_get_array('sc_skills_data', 'max')).'"'
						. ' data-speed="'.esc_attr($speed).'"'
						. ' data-duration="'.esc_attr($animation).'"'
						. ' data-color="'.esc_attr($color).'"'
						. ' data-bg_color="'.esc_attr($bg_color).'"'
						. ' data-border_color="'.esc_attr($border_color).'"'
						. ' data-cutout="'.esc_attr(prohost_storage_get_array('sc_skills_data', 'pie_cutout')).'"'
						. ' data-easing="easeOutCirc"'
						. ' data-ed="'.esc_attr($ed).'">'
						. ($start) . ($ed)
					.'</div>';
			}
			$output .=
					  (prohost_storage_get_array('sc_skills_data', 'type')=='counter' ? $title_block : '')
					. '</div>'
					. (prohost_storage_get_array('sc_skills_data', 'type')=='bar' && prohost_storage_get_array('sc_skills_data', 'dir')=='vertical' || prohost_storage_get_array('sc_skills_data', 'type') == 'pie' ? $title_block : '')
					. (prohost_storage_get_array('sc_skills_data', 'columns') > 0 ? '</div>' : '');
		}
		return apply_filters('prohost_shortcode_output', $output, 'trx_skills_item', $atts, $content);
	}
	prohost_require_shortcode('trx_skills_item', 'prohost_sc_skills_item');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_skills_reg_shortcodes' ) ) {
	//add_action('prohost_action_shortcodes_list', 'prohost_sc_skills_reg_shortcodes');
	function prohost_sc_skills_reg_shortcodes() {
	
		prohost_sc_map("trx_skills", array(
			"title" => esc_html__("Skills", "prohost"),
			"desc" => wp_kses_data( __("Insert skills diagramm in your page (post)", "prohost") ),
			"decorate" => true,
			"container" => false,
			"params" => array(
				"max_value" => array(
					"title" => esc_html__("Max value", "prohost"),
					"desc" => wp_kses_data( __("Max value for skills items", "prohost") ),
					"value" => 100,
					"min" => 1,
					"type" => "spinner"
				),
				"type" => array(
					"title" => esc_html__("Skills type", "prohost"),
					"desc" => wp_kses_data( __("Select type of skills block", "prohost") ),
					"value" => "bar",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => array(
						'bar' => esc_html__('Bar', 'prohost'),
						'pie' => esc_html__('Pie chart', 'prohost'),
						'counter' => esc_html__('Counter', 'prohost'),
						'arc' => esc_html__('Arc', 'prohost')
					)
				), 
				"layout" => array(
					"title" => esc_html__("Skills layout", "prohost"),
					"desc" => wp_kses_data( __("Select layout of skills block", "prohost") ),
					"dependency" => array(
						'type' => array('counter','pie','bar')
					),
					"value" => "rows",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => array(
						'rows' => esc_html__('Rows', 'prohost'),
						'columns' => esc_html__('Columns', 'prohost')
					)
				),
				"dir" => array(
					"title" => esc_html__("Direction", "prohost"),
					"desc" => wp_kses_data( __("Select direction of skills block", "prohost") ),
					"dependency" => array(
						'type' => array('counter','pie','bar')
					),
					"value" => "horizontal",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => prohost_get_sc_param('dir')
				), 
				"style" => array(
					"title" => esc_html__("Counters style", "prohost"),
					"desc" => wp_kses_data( __("Select style of skills items (only for type=counter)", "prohost") ),
					"dependency" => array(
						'type' => array('counter')
					),
					"value" => 1,
					"options" => prohost_get_list_styles(1, 4),
					"type" => "checklist"
				), 
				// "columns" - autodetect, not set manual
				"color" => array(
					"title" => esc_html__("Skills items color", "prohost"),
					"desc" => wp_kses_data( __("Color for all skills items", "prohost") ),
					"divider" => true,
					"value" => "",
					"type" => "color"
				),
				"bg_color" => array(
					"title" => esc_html__("Background color", "prohost"),
					"desc" => wp_kses_data( __("Background color for all skills items (only for type=pie)", "prohost") ),
					"dependency" => array(
						'type' => array('pie')
					),
					"value" => "",
					"type" => "color"
				),
				"border_color" => array(
					"title" => esc_html__("Border color", "prohost"),
					"desc" => wp_kses_data( __("Border color for all skills items (only for type=pie)", "prohost") ),
					"dependency" => array(
						'type' => array('pie')
					),
					"value" => "",
					"type" => "color"
				),
				"align" => array(
					"title" => esc_html__("Align skills block", "prohost"),
					"desc" => wp_kses_data( __("Align skills block to left or right side", "prohost") ),
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => prohost_get_sc_param('float')
				), 
				"arc_caption" => array(
					"title" => esc_html__("Arc Caption", "prohost"),
					"desc" => wp_kses_data( __("Arc caption - text in the center of the diagram", "prohost") ),
					"dependency" => array(
						'type' => array('arc')
					),
					"value" => "",
					"type" => "text"
				),
				"pie_compact" => array(
					"title" => esc_html__("Pie compact", "prohost"),
					"desc" => wp_kses_data( __("Show all skills in one diagram or as separate diagrams", "prohost") ),
					"dependency" => array(
						'type' => array('pie')
					),
					"value" => "yes",
					"type" => "switch",
					"options" => prohost_get_sc_param('yes_no')
				),
				"pie_cutout" => array(
					"title" => esc_html__("Pie cutout", "prohost"),
					"desc" => wp_kses_data( __("Pie cutout (0-99). 0 - without cutout, 99 - max cutout", "prohost") ),
					"dependency" => array(
						'type' => array('pie')
					),
					"value" => 0,
					"min" => 0,
					"max" => 99,
					"type" => "spinner"
				),
				"title" => array(
					"title" => esc_html__("Title", "prohost"),
					"desc" => wp_kses_data( __("Title for the block", "prohost") ),
					"value" => "",
					"type" => "text"
				),
				"subtitle" => array(
					"title" => esc_html__("Subtitle", "prohost"),
					"desc" => wp_kses_data( __("Subtitle for the block", "prohost") ),
					"value" => "",
					"type" => "text"
				),
				"description" => array(
					"title" => esc_html__("Description", "prohost"),
					"desc" => wp_kses_data( __("Short description for the block", "prohost") ),
					"value" => "",
					"type" => "textarea"
				),
				"link" => array(
					"title" => esc_html__("Button URL", "prohost"),
					"desc" => wp_kses_data( __("Link URL for the button at the bottom of the block", "prohost") ),
					"value" => "",
					"type" => "text"
				),
				"link_caption" => array(
					"title" => esc_html__("Button caption", "prohost"),
					"desc" => wp_kses_data( __("Caption for the button at the bottom of the block", "prohost") ),
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
			),
			"children" => array(
				"name" => "trx_skills_item",
				"title" => esc_html__("Skill", "prohost"),
				"desc" => wp_kses_data( __("Skills item", "prohost") ),
				"container" => false,
				"params" => array(
					"title" => array(
						"title" => esc_html__("Title", "prohost"),
						"desc" => wp_kses_data( __("Current skills item title", "prohost") ),
						"value" => "",
						"type" => "text"
					),
					"value" => array(
						"title" => esc_html__("Value", "prohost"),
						"desc" => wp_kses_data( __("Current skills level", "prohost") ),
						"value" => 50,
						"min" => 0,
						"step" => 1,
						"type" => "spinner"
					),
                    "counter_plus" => array(
                        "title" => esc_html__("Icon plus", "prohost"),
                        "desc" => wp_kses_data( __("Show plus after value (if type = Counter)", "prohost") ),
                        "value" => "no",
                        "type" => "switch",
                        "options" => prohost_get_sc_param('yes_no')
                    ),
					"color" => array(
						"title" => esc_html__("Color", "prohost"),
						"desc" => wp_kses_data( __("Current skills item color", "prohost") ),
						"value" => "",
						"type" => "color"
					),
					"bg_color" => array(
						"title" => esc_html__("Background color", "prohost"),
						"desc" => wp_kses_data( __("Current skills item background color (only for type=pie)", "prohost") ),
						"value" => "",
						"type" => "color"
					),
					"border_color" => array(
						"title" => esc_html__("Border color", "prohost"),
						"desc" => wp_kses_data( __("Current skills item border color (only for type=pie)", "prohost") ),
						"value" => "",
						"type" => "color"
					),
					"style" => array(
						"title" => esc_html__("Counter style", "prohost"),
						"desc" => wp_kses_data( __("Select style for the current skills item (only for type=counter)", "prohost") ),
						"value" => 1,
						"options" => prohost_get_list_styles(1, 4),
						"type" => "checklist"
					), 
					"icon" => array(
						"title" => esc_html__("Counter icon",  'prohost'),
						"desc" => wp_kses_data( __('Select icon from Fontello icons set, placed above counter (only for type=counter)',  'prohost') ),
						"value" => "",
						"type" => "icons",
						"options" => prohost_get_sc_param('icons')
					),
					"id" => prohost_get_sc_param('id'),
					"class" => prohost_get_sc_param('class'),
					"css" => prohost_get_sc_param('css')
				)
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_sc_skills_reg_shortcodes_vc' ) ) {
	//add_action('prohost_action_shortcodes_list_vc', 'prohost_sc_skills_reg_shortcodes_vc');
	function prohost_sc_skills_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_skills",
			"name" => esc_html__("Skills", "prohost"),
			"description" => wp_kses_data( __("Insert skills diagramm", "prohost") ),
			"category" => esc_html__('Content', 'prohost'),
			'icon' => 'icon_trx_skills',
			"class" => "trx_sc_collection trx_sc_skills",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => true,
			"as_parent" => array('only' => 'trx_skills_item'),
			"params" => array(
				array(
					"param_name" => "max_value",
					"heading" => esc_html__("Max value", "prohost"),
					"description" => wp_kses_data( __("Max value for skills items", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "100",
					"type" => "textfield"
				),
				array(
					"param_name" => "type",
					"heading" => esc_html__("Skills type", "prohost"),
					"description" => wp_kses_data( __("Select type of skills block", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
						esc_html__('Bar', 'prohost') => 'bar',
						esc_html__('Pie chart', 'prohost') => 'pie',
						esc_html__('Counter', 'prohost') => 'counter',
						esc_html__('Arc', 'prohost') => 'arc'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "layout",
					"heading" => esc_html__("Skills layout", "prohost"),
					"description" => wp_kses_data( __("Select layout of skills block", "prohost") ),
					"admin_label" => true,
					'dependency' => array(
						'element' => 'type',
						'value' => array('counter','bar','pie')
					),
					"class" => "",
					"value" => array(
						esc_html__('Rows', 'prohost') => 'rows',
						esc_html__('Columns', 'prohost') => 'columns'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "dir",
					"heading" => esc_html__("Direction", "prohost"),
					"description" => wp_kses_data( __("Select direction of skills block", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(prohost_get_sc_param('dir')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "style",
					"heading" => esc_html__("Counters style", "prohost"),
					"description" => wp_kses_data( __("Select style of skills items (only for type=counter)", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(prohost_get_list_styles(1, 4)),
					'dependency' => array(
						'element' => 'type',
						'value' => array('counter')
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "columns",
					"heading" => esc_html__("Columns count", "prohost"),
					"description" => wp_kses_data( __("Skills columns count (required)", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Color", "prohost"),
					"description" => wp_kses_data( __("Color for all skills items", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_color",
					"heading" => esc_html__("Background color", "prohost"),
					"description" => wp_kses_data( __("Background color for all skills items (only for type=pie)", "prohost") ),
					'dependency' => array(
						'element' => 'type',
						'value' => array('pie')
					),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "border_color",
					"heading" => esc_html__("Border color", "prohost"),
					"description" => wp_kses_data( __("Border color for all skills items (only for type=pie)", "prohost") ),
					'dependency' => array(
						'element' => 'type',
						'value' => array('pie')
					),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", "prohost"),
					"description" => wp_kses_data( __("Align skills block to left or right side", "prohost") ),
					"class" => "",
					"value" => array_flip(prohost_get_sc_param('float')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "arc_caption",
					"heading" => esc_html__("Arc caption", "prohost"),
					"description" => wp_kses_data( __("Arc caption - text in the center of the diagram", "prohost") ),
					'dependency' => array(
						'element' => 'type',
						'value' => array('arc')
					),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "pie_compact",
					"heading" => esc_html__("Pie compact", "prohost"),
					"description" => wp_kses_data( __("Show all skills in one diagram or as separate diagrams", "prohost") ),
					'dependency' => array(
						'element' => 'type',
						'value' => array('pie')
					),
					"class" => "",
					"value" => array(esc_html__('Show separate skills', 'prohost') => 'no'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "pie_cutout",
					"heading" => esc_html__("Pie cutout", "prohost"),
					"description" => wp_kses_data( __("Pie cutout (0-99). 0 - without cutout, 99 - max cutout", "prohost") ),
					'dependency' => array(
						'element' => 'type',
						'value' => array('pie')
					),
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
		
		
		vc_map( array(
			"base" => "trx_skills_item",
			"name" => esc_html__("Skill", "prohost"),
			"description" => wp_kses_data( __("Skills item", "prohost") ),
			"show_settings_on_create" => true,
			'icon' => 'icon_trx_skills_item',
			"class" => "trx_sc_single trx_sc_skills_item",
			"content_element" => true,
			"is_container" => false,
			"as_child" => array('only' => 'trx_skills'),
			"as_parent" => array('except' => 'trx_skills'),
			"params" => array(
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", "prohost"),
					"description" => wp_kses_data( __("Title for the current skills item", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "value",
					"heading" => esc_html__("Value", "prohost"),
					"description" => wp_kses_data( __("Value for the current skills item", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
                array(
                    "param_name" => "counter_plus",
                    "heading" => esc_html__("Icon plus", "prohost"),
                    "description" => wp_kses_data( __("Show plus after value (if type = Counter)", "prohost") ),
                    "admin_label" => true,
                    "class" => "",
                    "value" => array(esc_html__('Show plus', 'prohost') => 'yes'),
                    "type" => "checkbox"
                ),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Color", "prohost"),
					"description" => wp_kses_data( __("Color for current skills item", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_color",
					"heading" => esc_html__("Background color", "prohost"),
					"description" => wp_kses_data( __("Background color for current skills item (only for type=pie)", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "border_color",
					"heading" => esc_html__("Border color", "prohost"),
					"description" => wp_kses_data( __("Border color for current skills item (only for type=pie)", "prohost") ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "style",
					"heading" => esc_html__("Counter style", "prohost"),
					"description" => wp_kses_data( __("Select style for the current skills item (only for type=counter)", "prohost") ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(prohost_get_list_styles(1, 4)),
					"type" => "dropdown"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Counter icon", "prohost"),
					"description" => wp_kses_data( __("Select icon from Fontello icons set, placed before counter (only for type=counter)", "prohost") ),
					"class" => "",
					"value" => prohost_get_sc_param('icons'),
					"type" => "dropdown"
				),
				prohost_get_vc_param('id'),
				prohost_get_vc_param('class'),
				prohost_get_vc_param('css'),
			)
		) );
		
		class WPBakeryShortCode_Trx_Skills extends PROHOST_VC_ShortCodeCollection {}
		class WPBakeryShortCode_Trx_Skills_Item extends PROHOST_VC_ShortCodeSingle {}
	}
}
?>