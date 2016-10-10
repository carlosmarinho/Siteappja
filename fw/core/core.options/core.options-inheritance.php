<?php
//####################################################
//#### Inheritance system (for internal use only) #### 
//####################################################

// Add item to the inheritance settings
if ( !function_exists( 'prohost_add_theme_inheritance' ) ) {
	function prohost_add_theme_inheritance($options, $append=true) {
		$inheritance = prohost_storage_get('inheritance');
		if (empty($inheritance)) $inheritance = array();
		prohost_storage_set('inheritance', $append 
			? prohost_array_merge($inheritance, $options) 
			: prohost_array_merge($options, $inheritance)
			);
	}
}



// Return inheritance settings
if ( !function_exists( 'prohost_get_theme_inheritance' ) ) {
	function prohost_get_theme_inheritance($key = '') {
		return $key ? prohost_storage_get_array('inheritance', $key) : prohost_storage_get('inheritance');
	}
}



// Detect inheritance key for the current mode
if ( !function_exists( 'prohost_detect_inheritance_key' ) ) {
	function prohost_detect_inheritance_key() {
		static $inheritance_key = '';
		if (!empty($inheritance_key)) return $inheritance_key;
		$key = apply_filters('prohost_filter_detect_inheritance_key', '');
		if (prohost_storage_empty('pre_query')) $inheritance_key = $key;
		return $key;
	}
}


// Return key for override parameter
if ( !function_exists( 'prohost_get_override_key' ) ) {
	function prohost_get_override_key($value, $by) {
		$key = '';
		$inheritance = prohost_get_theme_inheritance();
		if (!empty($inheritance) && is_array($inheritance)) {
			foreach ($inheritance as $k=>$v) {
				if (!empty($v[$by]) && in_array($value, $v[$by])) {
					$key = $by=='taxonomy' 
						? $value
						: (!empty($v['override']) ? $v['override'] : $k);
					break;
				}
			}
		}
		return $key;
	}
}


// Return taxonomy (for categories) by post_type from inheritance array
if ( !function_exists( 'prohost_get_taxonomy_categories_by_post_type' ) ) {
	function prohost_get_taxonomy_categories_by_post_type($value) {
		$key = '';
		$inheritance = prohost_get_theme_inheritance();
		if (!empty($inheritance) && is_array($inheritance)) {
			foreach ($inheritance as $k=>$v) {
				if (!empty($v['post_type']) && in_array($value, $v['post_type'])) {
					$key = !empty($v['taxonomy']) ? $v['taxonomy'][0] : '';
					break;
				}
			}
		}
		return $key;
	}
}


// Return taxonomy (for tags) by post_type from inheritance array
if ( !function_exists( 'prohost_get_taxonomy_tags_by_post_type' ) ) {
	function prohost_get_taxonomy_tags_by_post_type($value) {
		$key = '';
		$inheritance = prohost_get_theme_inheritance();
		if (!empty($inheritance) && is_array($inheritance)) {
			foreach($inheritance as $k=>$v) {
				if (!empty($v['post_type']) && in_array($value, $v['post_type'])) {
					$key = !empty($v['taxonomy_tags']) ? $v['taxonomy_tags'][0] : '';
					break;
				}
			}
		}
		return $key;
	}
}
?>