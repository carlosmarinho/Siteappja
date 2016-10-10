<?php
/* Instagram Feed support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('prohost_instagram_feed_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_instagram_feed_theme_setup', 1 );
	function prohost_instagram_feed_theme_setup() {
		if (prohost_exists_instagram_feed()) {
			if (is_admin()) {
				add_filter( 'prohost_filter_importer_options',				'prohost_instagram_feed_importer_set_options' );
			}
		}
		if (is_admin()) {
			add_filter( 'prohost_filter_importer_required_plugins',		'prohost_instagram_feed_importer_required_plugins', 10, 2 );
			add_filter( 'prohost_filter_required_plugins',					'prohost_instagram_feed_required_plugins' );
		}
	}
}

// Check if Instagram Feed installed and activated
if ( !function_exists( 'prohost_exists_instagram_feed' ) ) {
	function prohost_exists_instagram_feed() {
		return defined('SBIVER');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'prohost_instagram_feed_required_plugins' ) ) {
	//add_filter('prohost_filter_required_plugins',	'prohost_instagram_feed_required_plugins');
	function prohost_instagram_feed_required_plugins($list=array()) {
		if (in_array('instagram_feed', prohost_storage_get('required_plugins')))
			$list[] = array(
					'name' 		=> 'Instagram Feed',
					'slug' 		=> 'instagram-feed',
					'required' 	=> false
				);
		return $list;
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check Instagram Feed in the required plugins
if ( !function_exists( 'prohost_instagram_feed_importer_required_plugins' ) ) {
	//add_filter( 'prohost_filter_importer_required_plugins',	'prohost_instagram_feed_importer_required_plugins', 10, 2 );
	function prohost_instagram_feed_importer_required_plugins($not_installed='', $list='') {
		//if (in_array('instagram_feed', prohost_storage_get('required_plugins')) && !prohost_exists_instagram_feed() )
		if (prohost_strpos($list, 'instagram_feed')!==false && !prohost_exists_instagram_feed() )
			$not_installed .= '<br>Instagram Feed';
		return $not_installed;
	}
}

// Set options for one-click importer
if ( !function_exists( 'prohost_instagram_feed_importer_set_options' ) ) {
	//add_filter( 'prohost_filter_importer_options',	'prohost_instagram_feed_importer_set_options' );
	function prohost_instagram_feed_importer_set_options($options=array()) {
		if ( in_array('instagram_feed', prohost_storage_get('required_plugins')) && prohost_exists_instagram_feed() ) {
			$options['additional_options'][] = 'sb_instagram_settings';		// Add slugs to export options for this plugin
		}
		return $options;
	}
}
?>