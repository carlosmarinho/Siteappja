<?php
/**
 * ProHost Framework: less manipulations
 *
 * @package	prohost
 * @since	prohost 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


// Theme init
if (!function_exists('prohost_less_theme_setup2')) {
	add_action( 'prohost_action_after_init_theme', 'prohost_less_theme_setup2' );
	function prohost_less_theme_setup2() {
		if (prohost_storage_get('less_recompile')) {

			// Theme first run - compile and save css
			do_action('prohost_action_compile_less');

		} else if (!is_admin() && prohost_get_theme_option('debug_mode')=='yes') {

			// Regular run - if not admin - recompile only changed files
			prohost_storage_set('less_check_time', true);
			do_action('prohost_action_compile_less');
			prohost_storage_set('less_check_time', false);

		}
	}
}

// Theme first run - compile and save css
if (!function_exists('prohost_less_theme_setup3')) {
	add_action( 'after_switch_theme', 'prohost_less_theme_setup3' );
	function prohost_less_theme_setup3() {
		prohost_storage_set('less_recompile', true);
	}
}



/* LESS
-------------------------------------------------------------------------------- */

// Recompile all LESS files
if (!function_exists('prohost_compile_less')) {	
	function prohost_compile_less($list = array(), $recompile=true) {

		if (!function_exists('trx_utils_less_compiler')) return false;

		$success = true;

		// Less compiler
		$less_compiler = prohost_get_theme_setting('less_compiler');
		if ($less_compiler == 'no') return $success;
		
		// Generate map for the LESS-files
		$less_map = prohost_get_theme_setting('less_map');
		if (prohost_get_theme_option('debug_mode')=='no' || $less_compiler=='lessc') $less_map = 'no';
		
		// Get separator to split LESS-files
		$less_sep = $less_map!='no' ? '' : prohost_get_theme_setting('less_separator');
	
		// Prepare skin specific LESS-vars (colors, backgrounds, logo height, etc.)
		$vars = apply_filters('prohost_filter_prepare_less', '');

		// Collect .less files in parent and child themes
		if (empty($list)) {
			$list = prohost_collect_files(get_template_directory(), 'less');
			if (get_template_directory() != get_stylesheet_directory()) $list = array_merge($list, prohost_collect_files(get_stylesheet_directory(), 'less'));
		}
		// Prepare separate array with less utils (not compile it alone - only with main files)
		$utils = $less_map!='no' ? array() : '';
		$utils_time = 0;
		if (is_array($list) && count($list) > 0) {
			foreach($list as $k=>$file) {
				$fname = basename($file);
				if ($fname[0]=='_') {
					if ($less_map!='no')
						$utils[] = $file;
					else
						$utils .= prohost_fgc($file);
					$list[$k] = '';
					$tmp = filemtime($file);
					if ($utils_time < $tmp) $utils_time = $tmp;
				}
			}
		}
		
		// Compile all .less files
		if (is_array($list) && count($list) > 0) {
			$success = trx_utils_less_compiler($list, array(
				'compiler' => $less_compiler,
				'map' => $less_map,
				'utils' => $utils,
				'utils_time' => $utils_time,
				'vars' => $vars,
				'separator' => $less_sep,
				'check_time' => prohost_storage_get('less_check_time')==true,
				'compressed' => prohost_get_theme_option('debug_mode')=='no'
				)
			);
		}
		
		return $success;
	}
}
?>