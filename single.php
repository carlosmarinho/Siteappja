<?php
/**
Template Name: Single post
 */
get_header(); 

$single_style = prohost_storage_get('single_style');
if (empty($single_style)) $single_style = prohost_get_custom_option('single_style');

while ( have_posts() ) { the_post();
	prohost_show_post_layout(
		array(
			'layout' => $single_style,
			'sidebar' => !prohost_param_is_off(prohost_get_custom_option('show_sidebar_main')),
			'content' => prohost_get_template_property($single_style, 'need_content'),
			'terms_list' => prohost_get_template_property($single_style, 'need_terms')
		)
	);
}

get_footer();
?>