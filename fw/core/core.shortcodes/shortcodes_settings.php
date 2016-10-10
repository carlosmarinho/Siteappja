<?php

// Check if shortcodes settings are now used
if ( !function_exists( 'prohost_shortcodes_is_used' ) ) {
	function prohost_shortcodes_is_used() {
		return prohost_options_is_used() 															// All modes when Theme Options are used
			|| (is_admin() && isset($_POST['action']) 
					&& in_array($_POST['action'], array('vc_edit_form', 'wpb_show_edit_form')))		// AJAX query when save post/page
			|| (is_admin() && prohost_strpos($_SERVER['REQUEST_URI'], 'vc-roles')!==false)			// VC Role Manager
			|| (function_exists('prohost_vc_is_frontend') && prohost_vc_is_frontend());			// VC Frontend editor mode
	}
}

// Width and height params
if ( !function_exists( 'prohost_shortcodes_width' ) ) {
	function prohost_shortcodes_width($w="") {
		return array(
			"title" => esc_html__("Width", "prohost"),
			"divider" => true,
			"value" => $w,
			"type" => "text"
		);
	}
}
if ( !function_exists( 'prohost_shortcodes_height' ) ) {
	function prohost_shortcodes_height($h='') {
		return array(
			"title" => esc_html__("Height", "prohost"),
			"desc" => wp_kses_data( __("Width and height of the element", "prohost") ),
			"value" => $h,
			"type" => "text"
		);
	}
}

// Return sc_param value
if ( !function_exists( 'prohost_get_sc_param' ) ) {
	function prohost_get_sc_param($prm) {
		return prohost_storage_get_array('sc_params', $prm);
	}
}

// Set sc_param value
if ( !function_exists( 'prohost_set_sc_param' ) ) {
	function prohost_set_sc_param($prm, $val) {
		prohost_storage_set_array('sc_params', $prm, $val);
	}
}

// Add sc settings in the sc list
if ( !function_exists( 'prohost_sc_map' ) ) {
	function prohost_sc_map($sc_name, $sc_settings) {
		prohost_storage_set_array('shortcodes', $sc_name, $sc_settings);
	}
}

// Add sc settings in the sc list after the key
if ( !function_exists( 'prohost_sc_map_after' ) ) {
	function prohost_sc_map_after($after, $sc_name, $sc_settings='') {
		prohost_storage_set_array_after('shortcodes', $after, $sc_name, $sc_settings);
	}
}

// Add sc settings in the sc list before the key
if ( !function_exists( 'prohost_sc_map_before' ) ) {
	function prohost_sc_map_before($before, $sc_name, $sc_settings='') {
		prohost_storage_set_array_before('shortcodes', $before, $sc_name, $sc_settings);
	}
}

// Compare two shortcodes by title
if ( !function_exists( 'prohost_compare_sc_title' ) ) {
	function prohost_compare_sc_title($a, $b) {
		return strcmp($a['title'], $b['title']);
	}
}



/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'prohost_shortcodes_settings_theme_setup' ) ) {
//	if ( prohost_vc_is_frontend() )
	if ( (isset($_GET['vc_editable']) && $_GET['vc_editable']=='true') || (isset($_GET['vc_action']) && $_GET['vc_action']=='vc_inline') )
		add_action( 'prohost_action_before_init_theme', 'prohost_shortcodes_settings_theme_setup', 20 );
	else
		add_action( 'prohost_action_after_init_theme', 'prohost_shortcodes_settings_theme_setup' );
	function prohost_shortcodes_settings_theme_setup() {
		if (prohost_shortcodes_is_used()) {

			// Sort templates alphabetically
			$tmp = prohost_storage_get('registered_templates');
			ksort($tmp);
			prohost_storage_set('registered_templates', $tmp);

			// Prepare arrays 
			prohost_storage_set('sc_params', array(
			
				// Current element id
				'id' => array(
					"title" => esc_html__("Element ID", "prohost"),
					"desc" => wp_kses_data( __("ID for current element", "prohost") ),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
			
				// Current element class
				'class' => array(
					"title" => esc_html__("Element CSS class", "prohost"),
					"desc" => wp_kses_data( __("CSS class for current element (optional)", "prohost") ),
					"value" => "",
					"type" => "text"
				),
			
				// Current element style
				'css' => array(
					"title" => esc_html__("CSS styles", "prohost"),
					"desc" => wp_kses_data( __("Any additional CSS rules (if need)", "prohost") ),
					"value" => "",
					"type" => "text"
				),
			
			
				// Switcher choises
				'list_styles' => array(
					'ul'	=> esc_html__('Unordered', 'prohost'),
					'ol'	=> esc_html__('Ordered', 'prohost'),
					'iconed'=> esc_html__('Iconed', 'prohost')
				),

				'yes_no'	=> prohost_get_list_yesno(),
				'on_off'	=> prohost_get_list_onoff(),
				'dir' 		=> prohost_get_list_directions(),
				'align'		=> prohost_get_list_alignments(),
				'float'		=> prohost_get_list_floats(),
				'hpos'		=> prohost_get_list_hpos(),
				'show_hide'	=> prohost_get_list_showhide(),
				'sorting' 	=> prohost_get_list_sortings(),
				'ordering' 	=> prohost_get_list_orderings(),
				'shapes'	=> prohost_get_list_shapes(),
				'sizes'		=> prohost_get_list_sizes(),
				'sliders'	=> prohost_get_list_sliders(),
				'controls'	=> prohost_get_list_controls(),
				'categories'=> prohost_get_list_categories(),
				'columns'	=> prohost_get_list_columns(),
				'images'	=> array_merge(array('none'=>"none"), prohost_get_list_files("images/icons", "png")),
				'icons'		=> array_merge(array("inherit", "none"), prohost_get_list_icons()),
				'locations'	=> prohost_get_list_dedicated_locations(),
				'filters'	=> prohost_get_list_portfolio_filters(),
				'formats'	=> prohost_get_list_post_formats_filters(),
				'hovers'	=> prohost_get_list_hovers(true),
				'hovers_dir'=> prohost_get_list_hovers_directions(true),
				'schemes'	=> prohost_get_list_color_schemes(true),
				'animations'		=> prohost_get_list_animations_in(),
				'margins' 			=> prohost_get_list_margins(true),
				'blogger_styles'	=> prohost_get_list_templates_blogger(),
				'forms'				=> prohost_get_list_templates_forms(),
				'posts_types'		=> prohost_get_list_posts_types(),
				'googlemap_styles'	=> prohost_get_list_googlemap_styles(),
				'field_types'		=> prohost_get_list_field_types(),
				'label_positions'	=> prohost_get_list_label_positions()
				)
			);

			// Common params
			prohost_set_sc_param('animation', array(
				"title" => esc_html__("Animation",  'prohost'),
				"desc" => wp_kses_data( __('Select animation while object enter in the visible area of page',  'prohost') ),
				"value" => "none",
				"type" => "select",
				"options" => prohost_get_sc_param('animations')
				)
			);
			prohost_set_sc_param('top', array(
				"title" => esc_html__("Top margin",  'prohost'),
				"divider" => true,
				"value" => "inherit",
				"type" => "select",
				"options" => prohost_get_sc_param('margins')
				)
			);
			prohost_set_sc_param('bottom', array(
				"title" => esc_html__("Bottom margin",  'prohost'),
				"value" => "inherit",
				"type" => "select",
				"options" => prohost_get_sc_param('margins')
				)
			);
			prohost_set_sc_param('left', array(
				"title" => esc_html__("Left margin",  'prohost'),
				"value" => "inherit",
				"type" => "select",
				"options" => prohost_get_sc_param('margins')
				)
			);
			prohost_set_sc_param('right', array(
				"title" => esc_html__("Right margin",  'prohost'),
				"desc" => wp_kses_data( __("Margins around this shortcode", "prohost") ),
				"value" => "inherit",
				"type" => "select",
				"options" => prohost_get_sc_param('margins')
				)
			);

			prohost_storage_set('sc_params', apply_filters('prohost_filter_shortcodes_params', prohost_storage_get('sc_params')));

			// Shortcodes list
			//------------------------------------------------------------------
			prohost_storage_set('shortcodes', array());
			
			// Register shortcodes
			do_action('prohost_action_shortcodes_list');

			// Sort shortcodes list
			$tmp = prohost_storage_get('shortcodes');
			uasort($tmp, 'prohost_compare_sc_title');
			prohost_storage_set('shortcodes', $tmp);
		}
	}
}
?>