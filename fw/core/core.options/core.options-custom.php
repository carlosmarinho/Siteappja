<?php
/**
 * ProHost Framework: Theme options custom fields
 *
 * @package	prohost
 * @since	prohost 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'prohost_options_custom_theme_setup' ) ) {
	add_action( 'prohost_action_before_init_theme', 'prohost_options_custom_theme_setup' );
	function prohost_options_custom_theme_setup() {

		if ( is_admin() ) {
			add_action("admin_enqueue_scripts",	'prohost_options_custom_load_scripts');
		}
		
	}
}

// Load required styles and scripts for custom options fields
if ( !function_exists( 'prohost_options_custom_load_scripts' ) ) {
	//add_action("admin_enqueue_scripts", 'prohost_options_custom_load_scripts');
	function prohost_options_custom_load_scripts() {
		prohost_enqueue_script( 'prohost-options-custom-script',	prohost_get_file_url('core/core.options/js/core.options-custom.js'), array(), null, true );	
	}
}


// Show theme specific fields in Post (and Page) options
if ( !function_exists( 'prohost_show_custom_field' ) ) {
	function prohost_show_custom_field($id, $field, $value) {
		$output = '';
		switch ($field['type']) {
			case 'reviews':
				$output .= '<div class="reviews_block">' . trim(prohost_reviews_get_markup($field, $value, true)) . '</div>';
				break;
	
			case 'mediamanager':
				wp_enqueue_media( );
				$output .= '<a id="'.esc_attr($id).'" class="button mediamanager prohost_media_selector"
					data-param="' . esc_attr($id) . '"
					data-choose="'.esc_attr(isset($field['multiple']) && $field['multiple'] ? esc_html__( 'Choose Images', 'prohost') : esc_html__( 'Choose Image', 'prohost')).'"
					data-update="'.esc_attr(isset($field['multiple']) && $field['multiple'] ? esc_html__( 'Add to Gallery', 'prohost') : esc_html__( 'Choose Image', 'prohost')).'"
					data-multiple="'.esc_attr(isset($field['multiple']) && $field['multiple'] ? 'true' : 'false').'"
					data-linked-field="'.esc_attr($field['media_field_id']).'"
					>' . (isset($field['multiple']) && $field['multiple'] ? esc_html__( 'Choose Images', 'prohost') : esc_html__( 'Choose Image', 'prohost')) . '</a>';
				break;
		}
		return apply_filters('prohost_filter_show_custom_field', $output, $id, $field, $value);
	}
}
?>