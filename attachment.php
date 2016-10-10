<?php
/**
Template Name: Attachment page
 */
get_header(); 

while ( have_posts() ) { the_post();

	// Move prohost_set_post_views to the javascript - counter will work under cache system
	if (prohost_get_custom_option('use_ajax_views_counter')=='no') {
		prohost_set_post_views(get_the_ID());
	}

	prohost_show_post_layout(
		array(
			'layout' => 'attachment',
			'sidebar' => !prohost_param_is_off(prohost_get_custom_option('show_sidebar_main'))
		)
	);

}

get_footer();
?>