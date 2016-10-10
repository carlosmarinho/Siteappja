<?php
/* WPML support functions
------------------------------------------------------------------------------- */

// Check if WPML installed and activated
if ( !function_exists( 'prohost_exists_wpml' ) ) {
	function prohost_exists_wpml() {
		return defined('ICL_SITEPRESS_VERSION') && class_exists('sitepress');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'prohost_wpml_required_plugins' ) ) {
	//add_filter('prohost_filter_required_plugins',	'prohost_wpml_required_plugins');
	function prohost_wpml_required_plugins($list=array()) {
		if (in_array('wpml', prohost_storage_get('required_plugins'))) {
			$path = prohost_get_file_dir('plugins/install/wpml.zip');
			if (file_exists($path)) {
				$list[] = array(
					'name' 		=> 'WPML',
					'slug' 		=> 'wpml',
					'source'	=> $path,
					'required' 	=> false
					);
			}
		}
		return $list;
	}
}
?>