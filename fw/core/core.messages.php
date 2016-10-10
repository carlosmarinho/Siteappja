<?php
/**
 * ProHost Framework: messages subsystem
 *
 * @package	prohost
 * @since	prohost 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Theme init
if (!function_exists('prohost_messages_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_messages_theme_setup' );
	function prohost_messages_theme_setup() {
		// Core messages strings
		add_action('prohost_action_add_scripts_inline', 'prohost_messages_add_scripts_inline');
	}
}


/* Session messages
------------------------------------------------------------------------------------- */

if (!function_exists('prohost_get_error_msg')) {
	function prohost_get_error_msg() {
		return prohost_storage_get('error_msg');
	}
}

if (!function_exists('prohost_set_error_msg')) {
	function prohost_set_error_msg($msg) {
		$msg2 = prohost_get_error_msg();
		prohost_storage_set('error_msg', trim($msg2) . ($msg2=='' ? '' : '<br />') . trim($msg));
	}
}

if (!function_exists('prohost_get_success_msg')) {
	function prohost_get_success_msg() {
		return prohost_storage_get('success_msg');
	}
}

if (!function_exists('prohost_set_success_msg')) {
	function prohost_set_success_msg($msg) {
		$msg2 = prohost_get_success_msg();
		prohost_storage_set('success_msg', trim($msg2) . ($msg2=='' ? '' : '<br />') . trim($msg));
	}
}

if (!function_exists('prohost_get_notice_msg')) {
	function prohost_get_notice_msg() {
		return prohost_storage_get('notice_msg');
	}
}

if (!function_exists('prohost_set_notice_msg')) {
	function prohost_set_notice_msg($msg) {
		$msg2 = prohost_get_notice_msg();
		prohost_storage_set('notice_msg', trim($msg2) . ($msg2=='' ? '' : '<br />') . trim($msg));
	}
}


/* System messages (save when page reload)
------------------------------------------------------------------------------------- */
if (!function_exists('prohost_set_system_message')) {
	function prohost_set_system_message($msg, $status='info', $hdr='') {
		update_option('prohost_message', array('message' => $msg, 'status' => $status, 'header' => $hdr));
	}
}

if (!function_exists('prohost_get_system_message')) {
	function prohost_get_system_message($del=false) {
		$msg = get_option('prohost_message', false);
		if (!$msg)
			$msg = array('message' => '', 'status' => '', 'header' => '');
		else if ($del)
			prohost_del_system_message();
		return $msg;
	}
}

if (!function_exists('prohost_del_system_message')) {
	function prohost_del_system_message() {
		delete_option('prohost_message');
	}
}


/* Messages strings
------------------------------------------------------------------------------------- */

if (!function_exists('prohost_messages_add_scripts_inline')) {
	function prohost_messages_add_scripts_inline() {
		echo '<script type="text/javascript">'
			
			. "if (typeof PROHOST_STORAGE == 'undefined') var PROHOST_STORAGE = {};"
			
			// Strings for translation
			. 'PROHOST_STORAGE["strings"] = {'
				. 'ajax_error: 			"' . addslashes(esc_html__('Invalid server answer', 'prohost')) . '",'
				. 'bookmark_add: 		"' . addslashes(esc_html__('Add the bookmark', 'prohost')) . '",'
				. 'bookmark_added:		"' . addslashes(esc_html__('Current page has been successfully added to the bookmarks. You can see it in the right panel on the tab \'Bookmarks\'', 'prohost')) . '",'
				. 'bookmark_del: 		"' . addslashes(esc_html__('Delete this bookmark', 'prohost')) . '",'
				. 'bookmark_title:		"' . addslashes(esc_html__('Enter bookmark title', 'prohost')) . '",'
				. 'bookmark_exists:		"' . addslashes(esc_html__('Current page already exists in the bookmarks list', 'prohost')) . '",'
				. 'search_error:		"' . addslashes(esc_html__('Error occurs in AJAX search! Please, type your query and press search icon for the traditional search way.', 'prohost')) . '",'
				. 'email_confirm:		"' . addslashes(esc_html__('On the e-mail address "%s" we sent a confirmation email. Please, open it and click on the link.', 'prohost')) . '",'
				. 'reviews_vote:		"' . addslashes(esc_html__('Thanks for your vote! New average rating is:', 'prohost')) . '",'
				. 'reviews_error:		"' . addslashes(esc_html__('Error saving your vote! Please, try again later.', 'prohost')) . '",'
				. 'error_like:			"' . addslashes(esc_html__('Error saving your like! Please, try again later.', 'prohost')) . '",'
				. 'error_global:		"' . addslashes(esc_html__('Global error text', 'prohost')) . '",'
				. 'name_empty:			"' . addslashes(esc_html__('The name can\'t be empty', 'prohost')) . '",'
				. 'name_long:			"' . addslashes(esc_html__('Too long name', 'prohost')) . '",'
				. 'email_empty:			"' . addslashes(esc_html__('Too short (or empty) email address', 'prohost')) . '",'
				. 'email_long:			"' . addslashes(esc_html__('Too long email address', 'prohost')) . '",'
				. 'email_not_valid:		"' . addslashes(esc_html__('Invalid email address', 'prohost')) . '",'
				. 'subject_empty:		"' . addslashes(esc_html__('The subject can\'t be empty', 'prohost')) . '",'
				. 'subject_long:		"' . addslashes(esc_html__('Too long subject', 'prohost')) . '",'
				. 'text_empty:			"' . addslashes(esc_html__('The message text can\'t be empty', 'prohost')) . '",'
				. 'text_long:			"' . addslashes(esc_html__('Too long message text', 'prohost')) . '",'
				. 'send_complete:		"' . addslashes(esc_html__("Send message complete!", 'prohost')) . '",'
				. 'send_error:			"' . addslashes(esc_html__('Transmit failed!', 'prohost')) . '",'
				. 'login_empty:			"' . addslashes(esc_html__('The Login field can\'t be empty', 'prohost')) . '",'
				. 'login_long:			"' . addslashes(esc_html__('Too long login field', 'prohost')) . '",'
				. 'login_success:		"' . addslashes(esc_html__('Login success! The page will be reloaded in 3 sec.', 'prohost')) . '",'
				. 'login_failed:		"' . addslashes(esc_html__('Login failed!', 'prohost')) . '",'
				. 'password_empty:		"' . addslashes(esc_html__('The password can\'t be empty and shorter then 4 characters', 'prohost')) . '",'
				. 'password_long:		"' . addslashes(esc_html__('Too long password', 'prohost')) . '",'
				. 'password_not_equal:	"' . addslashes(esc_html__('The passwords in both fields are not equal', 'prohost')) . '",'
				. 'registration_success:"' . addslashes(esc_html__('Registration success! Please log in!', 'prohost')) . '",'
				. 'registration_failed:	"' . addslashes(esc_html__('Registration failed!', 'prohost')) . '",'
				. 'geocode_error:		"' . addslashes(esc_html__('Geocode was not successful for the following reason:', 'prohost')) . '",'
				. 'googlemap_not_avail:	"' . addslashes(esc_html__('Google map API not available!', 'prohost')) . '",'
				. 'editor_save_success:	"' . addslashes(esc_html__("Post content saved!", 'prohost')) . '",'
				. 'editor_save_error:	"' . addslashes(esc_html__("Error saving post data!", 'prohost')) . '",'
				. 'editor_delete_post:	"' . addslashes(esc_html__("You really want to delete the current post?", 'prohost')) . '",'
				. 'editor_delete_post_header:"' . addslashes(esc_html__("Delete post", 'prohost')) . '",'
				. 'editor_delete_success:	"' . addslashes(esc_html__("Post deleted!", 'prohost')) . '",'
				. 'editor_delete_error:		"' . addslashes(esc_html__("Error deleting post!", 'prohost')) . '",'
				. 'editor_caption_cancel:	"' . addslashes(esc_html__('Cancel', 'prohost')) . '",'
				. 'editor_caption_close:	"' . addslashes(esc_html__('Close', 'prohost')) . '"'
				. '};'
			
			. '</script>';
	}
}
?>