<?php
/* Visual Composer support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('prohost_vc_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_vc_theme_setup', 1 );
	function prohost_vc_theme_setup() {
		if (prohost_exists_visual_composer()) {
			if (is_admin()) {
				add_filter( 'prohost_filter_importer_options',				'prohost_vc_importer_set_options' );
			}
			add_action('prohost_action_add_styles',		 				'prohost_vc_frontend_scripts' );
		}
		if (is_admin()) {
			add_filter( 'prohost_filter_importer_required_plugins',		'prohost_vc_importer_required_plugins', 10, 2 );
			add_filter( 'prohost_filter_required_plugins',					'prohost_vc_required_plugins' );
		}
	}
}

// Check if Visual Composer installed and activated
if ( !function_exists( 'prohost_exists_visual_composer' ) ) {
	function prohost_exists_visual_composer() {
		return class_exists('Vc_Manager');
	}
}

// Check if Visual Composer in frontend editor mode
if ( !function_exists( 'prohost_vc_is_frontend' ) ) {
	function prohost_vc_is_frontend() {
		return (isset($_GET['vc_editable']) && $_GET['vc_editable']=='true')
			|| (isset($_GET['vc_action']) && $_GET['vc_action']=='vc_inline');
		//return function_exists('vc_is_frontend_editor') && vc_is_frontend_editor();
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'prohost_vc_required_plugins' ) ) {
	//add_filter('prohost_filter_required_plugins',	'prohost_vc_required_plugins');
	function prohost_vc_required_plugins($list=array()) {
		if (in_array('visual_composer', prohost_storage_get('required_plugins'))) {
			$path = prohost_get_file_dir('plugins/install/js_composer.zip');
			if (file_exists($path)) {
				$list[] = array(
					'name' 		=> 'Visual Composer',
					'slug' 		=> 'js_composer',
					'source'	=> $path,
					'required' 	=> false
				);
			}
		}
		return $list;
	}
}

// Enqueue VC custom styles
if ( !function_exists( 'prohost_vc_frontend_scripts' ) ) {
	//add_action( 'prohost_action_add_styles', 'prohost_vc_frontend_scripts' );
	function prohost_vc_frontend_scripts() {
		if (file_exists(prohost_get_file_dir('css/plugin.visual-composer.css')))
			prohost_enqueue_style( 'prohost-plugin.visual-composer-style',  prohost_get_file_url('css/plugin.visual-composer.css'), array(), null );
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check VC in the required plugins
if ( !function_exists( 'prohost_vc_importer_required_plugins' ) ) {
	//add_filter( 'prohost_filter_importer_required_plugins',	'prohost_vc_importer_required_plugins', 10, 2 );
	function prohost_vc_importer_required_plugins($not_installed='', $list='') {
		//if (in_array('visual_composer', prohost_storage_get('required_plugins')) && !prohost_exists_visual_composer() && prohost_get_value_gp('data_type')=='vc' )
		if (!prohost_exists_visual_composer() )		// && prohost_strpos($list, 'visual_composer')!==false
			$not_installed .= '<br>Visual Composer';
		return $not_installed;
	}
}

// Set options for one-click importer
if ( !function_exists( 'prohost_vc_importer_set_options' ) ) {
	//add_filter( 'prohost_filter_importer_options',	'prohost_vc_importer_set_options' );
	function prohost_vc_importer_set_options($options=array()) {
		if ( in_array('visual_composer', prohost_storage_get('required_plugins')) && prohost_exists_visual_composer() ) {
			$options['additional_options'][] = 'wpb_js_templates';		// Add slugs to export options for this plugin

		}
		return $options;
	}
}
?>