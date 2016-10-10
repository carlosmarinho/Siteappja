<?php
/**
 * ProHost Framework: theme variables storage
 *
 * @package	prohost
 * @since	prohost 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Get theme variable
if (!function_exists('prohost_storage_get')) {
	function prohost_storage_get($var_name, $default='') {
		global $PROHOST_STORAGE;
		return isset($PROHOST_STORAGE[$var_name]) ? $PROHOST_STORAGE[$var_name] : $default;
	}
}

// Set theme variable
if (!function_exists('prohost_storage_set')) {
	function prohost_storage_set($var_name, $value) {
		global $PROHOST_STORAGE;
		$PROHOST_STORAGE[$var_name] = $value;
	}
}

// Check if theme variable is empty
if (!function_exists('prohost_storage_empty')) {
	function prohost_storage_empty($var_name, $key='', $key2='') {
		global $PROHOST_STORAGE;
		if (!empty($key) && !empty($key2))
			return empty($PROHOST_STORAGE[$var_name][$key][$key2]);
		else if (!empty($key))
			return empty($PROHOST_STORAGE[$var_name][$key]);
		else
			return empty($PROHOST_STORAGE[$var_name]);
	}
}

// Check if theme variable is set
if (!function_exists('prohost_storage_isset')) {
	function prohost_storage_isset($var_name, $key='', $key2='') {
		global $PROHOST_STORAGE;
		if (!empty($key) && !empty($key2))
			return isset($PROHOST_STORAGE[$var_name][$key][$key2]);
		else if (!empty($key))
			return isset($PROHOST_STORAGE[$var_name][$key]);
		else
			return isset($PROHOST_STORAGE[$var_name]);
	}
}

// Inc/Dec theme variable with specified value
if (!function_exists('prohost_storage_inc')) {
	function prohost_storage_inc($var_name, $value=1) {
		global $PROHOST_STORAGE;
		if (empty($PROHOST_STORAGE[$var_name])) $PROHOST_STORAGE[$var_name] = 0;
		$PROHOST_STORAGE[$var_name] += $value;
	}
}

// Concatenate theme variable with specified value
if (!function_exists('prohost_storage_concat')) {
	function prohost_storage_concat($var_name, $value) {
		global $PROHOST_STORAGE;
		if (empty($PROHOST_STORAGE[$var_name])) $PROHOST_STORAGE[$var_name] = '';
		$PROHOST_STORAGE[$var_name] .= $value;
	}
}

// Get array (one or two dim) element
if (!function_exists('prohost_storage_get_array')) {
	function prohost_storage_get_array($var_name, $key, $key2='', $default='') {
		global $PROHOST_STORAGE;
		if (empty($key2))
			return !empty($var_name) && !empty($key) && isset($PROHOST_STORAGE[$var_name][$key]) ? $PROHOST_STORAGE[$var_name][$key] : $default;
		else
			return !empty($var_name) && !empty($key) && isset($PROHOST_STORAGE[$var_name][$key][$key2]) ? $PROHOST_STORAGE[$var_name][$key][$key2] : $default;
	}
}

// Set array element
if (!function_exists('prohost_storage_set_array')) {
	function prohost_storage_set_array($var_name, $key, $value) {
		global $PROHOST_STORAGE;
		if (!isset($PROHOST_STORAGE[$var_name])) $PROHOST_STORAGE[$var_name] = array();
		if ($key==='')
			$PROHOST_STORAGE[$var_name][] = $value;
		else
			$PROHOST_STORAGE[$var_name][$key] = $value;
	}
}

// Set two-dim array element
if (!function_exists('prohost_storage_set_array2')) {
	function prohost_storage_set_array2($var_name, $key, $key2, $value) {
		global $PROHOST_STORAGE;
		if (!isset($PROHOST_STORAGE[$var_name])) $PROHOST_STORAGE[$var_name] = array();
		if (!isset($PROHOST_STORAGE[$var_name][$key])) $PROHOST_STORAGE[$var_name][$key] = array();
		if ($key2==='')
			$PROHOST_STORAGE[$var_name][$key][] = $value;
		else
			$PROHOST_STORAGE[$var_name][$key][$key2] = $value;
	}
}

// Add array element after the key
if (!function_exists('prohost_storage_set_array_after')) {
	function prohost_storage_set_array_after($var_name, $after, $key, $value='') {
		global $PROHOST_STORAGE;
		if (!isset($PROHOST_STORAGE[$var_name])) $PROHOST_STORAGE[$var_name] = array();
		if (is_array($key))
			prohost_array_insert_after($PROHOST_STORAGE[$var_name], $after, $key);
		else
			prohost_array_insert_after($PROHOST_STORAGE[$var_name], $after, array($key=>$value));
	}
}

// Add array element before the key
if (!function_exists('prohost_storage_set_array_before')) {
	function prohost_storage_set_array_before($var_name, $before, $key, $value='') {
		global $PROHOST_STORAGE;
		if (!isset($PROHOST_STORAGE[$var_name])) $PROHOST_STORAGE[$var_name] = array();
		if (is_array($key))
			prohost_array_insert_before($PROHOST_STORAGE[$var_name], $before, $key);
		else
			prohost_array_insert_before($PROHOST_STORAGE[$var_name], $before, array($key=>$value));
	}
}

// Push element into array
if (!function_exists('prohost_storage_push_array')) {
	function prohost_storage_push_array($var_name, $key, $value) {
		global $PROHOST_STORAGE;
		if (!isset($PROHOST_STORAGE[$var_name])) $PROHOST_STORAGE[$var_name] = array();
		if ($key==='')
			array_push($PROHOST_STORAGE[$var_name], $value);
		else {
			if (!isset($PROHOST_STORAGE[$var_name][$key])) $PROHOST_STORAGE[$var_name][$key] = array();
			array_push($PROHOST_STORAGE[$var_name][$key], $value);
		}
	}
}

// Pop element from array
if (!function_exists('prohost_storage_pop_array')) {
	function prohost_storage_pop_array($var_name, $key='', $defa='') {
		global $PROHOST_STORAGE;
		$rez = $defa;
		if ($key==='') {
			if (isset($PROHOST_STORAGE[$var_name]) && is_array($PROHOST_STORAGE[$var_name]) && count($PROHOST_STORAGE[$var_name]) > 0) 
				$rez = array_pop($PROHOST_STORAGE[$var_name]);
		} else {
			if (isset($PROHOST_STORAGE[$var_name][$key]) && is_array($PROHOST_STORAGE[$var_name][$key]) && count($PROHOST_STORAGE[$var_name][$key]) > 0) 
				$rez = array_pop($PROHOST_STORAGE[$var_name][$key]);
		}
		return $rez;
	}
}

// Inc/Dec array element with specified value
if (!function_exists('prohost_storage_inc_array')) {
	function prohost_storage_inc_array($var_name, $key, $value=1) {
		global $PROHOST_STORAGE;
		if (!isset($PROHOST_STORAGE[$var_name])) $PROHOST_STORAGE[$var_name] = array();
		if (empty($PROHOST_STORAGE[$var_name][$key])) $PROHOST_STORAGE[$var_name][$key] = 0;
		$PROHOST_STORAGE[$var_name][$key] += $value;
	}
}

// Concatenate array element with specified value
if (!function_exists('prohost_storage_concat_array')) {
	function prohost_storage_concat_array($var_name, $key, $value) {
		global $PROHOST_STORAGE;
		if (!isset($PROHOST_STORAGE[$var_name])) $PROHOST_STORAGE[$var_name] = array();
		if (empty($PROHOST_STORAGE[$var_name][$key])) $PROHOST_STORAGE[$var_name][$key] = '';
		$PROHOST_STORAGE[$var_name][$key] .= $value;
	}
}

// Call object's method
if (!function_exists('prohost_storage_call_obj_method')) {
	function prohost_storage_call_obj_method($var_name, $method, $param=null) {
		global $PROHOST_STORAGE;
		if ($param===null)
			return !empty($var_name) && !empty($method) && isset($PROHOST_STORAGE[$var_name]) ? $PROHOST_STORAGE[$var_name]->$method(): '';
		else
			return !empty($var_name) && !empty($method) && isset($PROHOST_STORAGE[$var_name]) ? $PROHOST_STORAGE[$var_name]->$method($param): '';
	}
}

// Get object's property
if (!function_exists('prohost_storage_get_obj_property')) {
	function prohost_storage_get_obj_property($var_name, $prop, $default='') {
		global $PROHOST_STORAGE;
		return !empty($var_name) && !empty($prop) && isset($PROHOST_STORAGE[$var_name]->$prop) ? $PROHOST_STORAGE[$var_name]->$prop : $default;
	}
}
?>