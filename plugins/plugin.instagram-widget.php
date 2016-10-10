<?php
/* Instagram Widget support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('prohost_instagram_widget_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_instagram_widget_theme_setup', 1 );
	function prohost_instagram_widget_theme_setup() {
		if (prohost_exists_instagram_widget()) {
			add_action( 'prohost_action_add_styles', 						'prohost_instagram_widget_frontend_scripts' );
		}
		if (is_admin()) {
			add_filter( 'prohost_filter_importer_required_plugins',		'prohost_instagram_widget_importer_required_plugins', 10, 2 );
			add_filter( 'prohost_filter_required_plugins',					'prohost_instagram_widget_required_plugins' );
		}
	}
}

// Check if Instagram Widget installed and activated
if ( !function_exists( 'prohost_exists_instagram_widget' ) ) {
	function prohost_exists_instagram_widget() {
		return function_exists('wpiw_init');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'prohost_instagram_widget_required_plugins' ) ) {
	//add_filter('prohost_filter_required_plugins',	'prohost_instagram_widget_required_plugins');
	function prohost_instagram_widget_required_plugins($list=array()) {
		if (in_array('instagram_widget', prohost_storage_get('required_plugins')))
			$list[] = array(
					'name' 		=> 'Instagram Widget',
					'slug' 		=> 'wp-instagram-widget',
					'required' 	=> false
				);
		return $list;
	}
}

// Enqueue custom styles
if ( !function_exists( 'prohost_instagram_widget_frontend_scripts' ) ) {
	//add_action( 'prohost_action_add_styles', 'prohost_instagram_widget_frontend_scripts' );
	function prohost_instagram_widget_frontend_scripts() {
		if (file_exists(prohost_get_file_dir('css/plugin.instagram-widget.css')))
			prohost_enqueue_style( 'prohost-plugin.instagram-widget-style',  prohost_get_file_url('css/plugin.instagram-widget.css'), array(), null );
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check Instagram Widget in the required plugins
if ( !function_exists( 'prohost_instagram_widget_importer_required_plugins' ) ) {
	//add_filter( 'prohost_filter_importer_required_plugins',	'prohost_instagram_widget_importer_required_plugins', 10, 2 );
	function prohost_instagram_widget_importer_required_plugins($not_installed='', $list='') {
		//if (in_array('instagram_widget', prohost_storage_get('required_plugins')) && !prohost_exists_instagram_widget() )
		if (prohost_strpos($list, 'instagram_widget')!==false && !prohost_exists_instagram_widget() )
			$not_installed .= '<br>WP Instagram Widget';
		return $not_installed;
	}
}
?>