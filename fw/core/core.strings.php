<?php
/**
 * ProHost Framework: strings manipulations
 *
 * @package	prohost
 * @since	prohost 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Check multibyte functions
if ( ! defined( 'PROHOST_MULTIBYTE' ) ) define( 'PROHOST_MULTIBYTE', function_exists('mb_strpos') ? 'UTF-8' : false );

if (!function_exists('prohost_strlen')) {
	function prohost_strlen($text) {
		return PROHOST_MULTIBYTE ? mb_strlen($text) : strlen($text);
	}
}

if (!function_exists('prohost_strpos')) {
	function prohost_strpos($text, $char, $from=0) {
		return PROHOST_MULTIBYTE ? mb_strpos($text, $char, $from) : strpos($text, $char, $from);
	}
}

if (!function_exists('prohost_strrpos')) {
	function prohost_strrpos($text, $char, $from=0) {
		return PROHOST_MULTIBYTE ? mb_strrpos($text, $char, $from) : strrpos($text, $char, $from);
	}
}

if (!function_exists('prohost_substr')) {
	function prohost_substr($text, $from, $len=-999999) {
		if ($len==-999999) { 
			if ($from < 0)
				$len = -$from; 
			else
				$len = prohost_strlen($text)-$from;
		}
		return PROHOST_MULTIBYTE ? mb_substr($text, $from, $len) : substr($text, $from, $len);
	}
}

if (!function_exists('prohost_strtolower')) {
	function prohost_strtolower($text) {
		return PROHOST_MULTIBYTE ? mb_strtolower($text) : strtolower($text);
	}
}

if (!function_exists('prohost_strtoupper')) {
	function prohost_strtoupper($text) {
		return PROHOST_MULTIBYTE ? mb_strtoupper($text) : strtoupper($text);
	}
}

if (!function_exists('prohost_strtoproper')) {
	function prohost_strtoproper($text) { 
		$rez = ''; $last = ' ';
		for ($i=0; $i<prohost_strlen($text); $i++) {
			$ch = prohost_substr($text, $i, 1);
			$rez .= prohost_strpos(' .,:;?!()[]{}+=', $last)!==false ? prohost_strtoupper($ch) : prohost_strtolower($ch);
			$last = $ch;
		}
		return $rez;
	}
}

if (!function_exists('prohost_strrepeat')) {
	function prohost_strrepeat($str, $n) {
		$rez = '';
		for ($i=0; $i<$n; $i++)
			$rez .= $str;
		return $rez;
	}
}

if (!function_exists('prohost_strshort')) {
	function prohost_strshort($str, $maxlength, $add='...') {
	//	if ($add && prohost_substr($add, 0, 1) != ' ')
	//		$add .= ' ';
		if ($maxlength < 0) 
			return $str;
		if ($maxlength < 1 || $maxlength >= prohost_strlen($str)) 
			return strip_tags($str);
		$str = prohost_substr(strip_tags($str), 0, $maxlength - prohost_strlen($add));
		$ch = prohost_substr($str, $maxlength - prohost_strlen($add), 1);
		if ($ch != ' ') {
			for ($i = prohost_strlen($str) - 1; $i > 0; $i--)
				if (prohost_substr($str, $i, 1) == ' ') break;
			$str = trim(prohost_substr($str, 0, $i));
		}
		if (!empty($str) && prohost_strpos(',.:;-', prohost_substr($str, -1))!==false) $str = prohost_substr($str, 0, -1);
		return ($str) . ($add);
	}
}

// Clear string from spaces, line breaks and tags (only around text)
if (!function_exists('prohost_strclear')) {
	function prohost_strclear($text, $tags=array()) {
		if (empty($text)) return $text;
		if (!is_array($tags)) {
			if ($tags != '')
				$tags = explode($tags, ',');
			else
				$tags = array();
		}
		$text = trim(chop($text));
		if (is_array($tags) && count($tags) > 0) {
			foreach ($tags as $tag) {
				$open  = '<'.esc_attr($tag);
				$close = '</'.esc_attr($tag).'>';
				if (prohost_substr($text, 0, prohost_strlen($open))==$open) {
					$pos = prohost_strpos($text, '>');
					if ($pos!==false) $text = prohost_substr($text, $pos+1);
				}
				if (prohost_substr($text, -prohost_strlen($close))==$close) $text = prohost_substr($text, 0, prohost_strlen($text) - prohost_strlen($close));
				$text = trim(chop($text));
			}
		}
		return $text;
	}
}

// Return slug for the any title string
if (!function_exists('prohost_get_slug')) {
	function prohost_get_slug($title) {
		return prohost_strtolower(str_replace(array('\\','/','-',' ','.'), '_', $title));
	}
}

// Replace macros in the string
if (!function_exists('prohost_strmacros')) {
	function prohost_strmacros($str) {
		return str_replace(array("{{", "}}", "((", "))", "||"), array("<i>", "</i>", "<b>", "</b>", "<br>"), $str);
	}
}

// Unserialize string (try replace \n with \r\n)
if (!function_exists('prohost_unserialize')) {
	function prohost_unserialize($str) {
		if ( is_serialized($str) ) {
			try {
				$data = unserialize($str);
			} catch (Exception $e) {
				dcl($e->getMessage());
				$data = false;
			}
			if ($data===false) {
				try {
					$data = @unserialize(str_replace("\n", "\r\n", $str));
				} catch (Exception $e) {
					dcl($e->getMessage());
					$data = false;
				}
			}
			//if ($data===false) $data = @unserialize(str_replace(array("\n", "\r"), array('\\n','\\r'), $str));
			return $data;
		} else
			return $str;
	}
}
?>