<?php
/* ProHost Donations support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('prohost_trx_donations_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_trx_donations_theme_setup', 1 );
	function prohost_trx_donations_theme_setup() {

		// Register shortcode in the shortcodes list
		if (prohost_exists_trx_donations()) {
			// Detect current page type, taxonomy and title (for custom post_types use priority < 10 to fire it handles early, than for standard post types)
			add_filter('prohost_filter_get_blog_type',			'prohost_trx_donations_get_blog_type', 9, 2);
			add_filter('prohost_filter_get_blog_title',		'prohost_trx_donations_get_blog_title', 9, 2);
			add_filter('prohost_filter_get_current_taxonomy',	'prohost_trx_donations_get_current_taxonomy', 9, 2);
			add_filter('prohost_filter_is_taxonomy',			'prohost_trx_donations_is_taxonomy', 9, 2);
			add_filter('prohost_filter_get_stream_page_title',	'prohost_trx_donations_get_stream_page_title', 9, 2);
			add_filter('prohost_filter_get_stream_page_link',	'prohost_trx_donations_get_stream_page_link', 9, 2);
			add_filter('prohost_filter_get_stream_page_id',	'prohost_trx_donations_get_stream_page_id', 9, 2);
			add_filter('prohost_filter_query_add_filters',		'prohost_trx_donations_query_add_filters', 9, 2);
			add_filter('prohost_filter_detect_inheritance_key','prohost_trx_donations_detect_inheritance_key', 9, 1);
			add_filter('prohost_filter_list_post_types',		'prohost_trx_donations_list_post_types');
			// Register shortcodes in the list
			add_action('prohost_action_shortcodes_list',		'prohost_trx_donations_reg_shortcodes');
			if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
				add_action('prohost_action_shortcodes_list_vc','prohost_trx_donations_reg_shortcodes_vc');
			if (is_admin()) {
				add_filter( 'prohost_filter_importer_options',				'prohost_trx_donations_importer_set_options' );
			}
		}
		if (is_admin()) {
			add_filter( 'prohost_filter_importer_required_plugins',	'prohost_trx_donations_importer_required_plugins', 10, 2 );
			add_filter( 'prohost_filter_required_plugins',				'prohost_trx_donations_required_plugins' );
		}
	}
}

if ( !function_exists( 'prohost_trx_donations_settings_theme_setup2' ) ) {
	add_action( 'prohost_action_before_init_theme', 'prohost_trx_donations_settings_theme_setup2', 3 );
	function prohost_trx_donations_settings_theme_setup2() {
		// Add Donations post type and taxonomy into theme inheritance list
		if (prohost_exists_trx_donations()) {
			prohost_add_theme_inheritance( array('donations' => array(
				'stream_template' => 'blog-donations',
				'single_template' => 'single-donation',
				'taxonomy' => array(PROHOST_Donations::TAXONOMY),
				'taxonomy_tags' => array(),
				'post_type' => array(PROHOST_Donations::POST_TYPE),
				'override' => 'page'
				) )
			);
		}
	}
}

// Check if ProHost Donations installed and activated
if ( !function_exists( 'prohost_exists_trx_donations' ) ) {
	function prohost_exists_trx_donations() {
		return class_exists('PROHOST_Donations');
	}
}


// Return true, if current page is donations page
if ( !function_exists( 'prohost_is_trx_donations_page' ) ) {
	function prohost_is_trx_donations_page() {
		$is = false;
		if (prohost_exists_trx_donations()) {
			$is = in_array(prohost_storage_get('page_template'), array('blog-donations', 'single-donation'));
			if (!$is) {
				if (!prohost_storage_empty('pre_query'))
					$is = (prohost_storage_call_obj_method('pre_query', 'is_single') && prohost_storage_call_obj_method('pre_query', 'get', 'post_type') == PROHOST_Donations::POST_TYPE) 
							|| prohost_storage_call_obj_method('pre_query', 'is_post_type_archive', PROHOST_Donations::POST_TYPE) 
							|| prohost_storage_call_obj_method('pre_query', 'is_tax', PROHOST_Donations::TAXONOMY);
				else
					$is = (is_single() && get_query_var('post_type') == PROHOST_Donations::POST_TYPE) 
							|| is_post_type_archive(PROHOST_Donations::POST_TYPE) 
							|| is_tax(PROHOST_Donations::TAXONOMY);
			}
		}
		return $is;
	}
}

// Filter to detect current page inheritance key
if ( !function_exists( 'prohost_trx_donations_detect_inheritance_key' ) ) {
	//add_filter('prohost_filter_detect_inheritance_key',	'prohost_trx_donations_detect_inheritance_key', 9, 1);
	function prohost_trx_donations_detect_inheritance_key($key) {
		if (!empty($key)) return $key;
		return prohost_is_trx_donations_page() ? 'donations' : '';
	}
}

// Filter to detect current page slug
if ( !function_exists( 'prohost_trx_donations_get_blog_type' ) ) {
	//add_filter('prohost_filter_get_blog_type',	'prohost_trx_donations_get_blog_type', 9, 2);
	function prohost_trx_donations_get_blog_type($page, $query=null) {
		if (!empty($page)) return $page;
		if ($query && $query->is_tax(PROHOST_Donations::TAXONOMY) || is_tax(PROHOST_Donations::TAXONOMY))
			$page = 'donations_category';
		else if ($query && $query->get('post_type')==PROHOST_Donations::POST_TYPE || get_query_var('post_type')==PROHOST_Donations::POST_TYPE)
			$page = $query && $query->is_single() || is_single() ? 'donations_item' : 'donations';
		return $page;
	}
}

// Filter to detect current page title
if ( !function_exists( 'prohost_trx_donations_get_blog_title' ) ) {
	//add_filter('prohost_filter_get_blog_title',	'prohost_trx_donations_get_blog_title', 9, 2);
	function prohost_trx_donations_get_blog_title($title, $page) {
		if (!empty($title)) return $title;
		if ( prohost_strpos($page, 'donations')!==false ) {
			if ( $page == 'donations_category' ) {
				$term = get_term_by( 'slug', get_query_var( PROHOST_Donations::TAXONOMY ), PROHOST_Donations::TAXONOMY, OBJECT);
				$title = $term->name;
			} else if ( $page == 'donations_item' ) {
				$title = prohost_get_post_title();
			} else {
				$title = esc_html__('All donations', 'prohost');
			}
		}

		return $title;
	}
}

// Filter to detect stream page title
if ( !function_exists( 'prohost_trx_donations_get_stream_page_title' ) ) {
	//add_filter('prohost_filter_get_stream_page_title',	'prohost_trx_donations_get_stream_page_title', 9, 2);
	function prohost_trx_donations_get_stream_page_title($title, $page) {
		if (!empty($title)) return $title;
		if (prohost_strpos($page, 'donations')!==false) {
			if (($page_id = prohost_trx_donations_get_stream_page_id(0, $page=='donations' ? 'blog-donations' : $page)) > 0)
				$title = prohost_get_post_title($page_id);
			else
				$title = esc_html__('All donations', 'prohost');				
		}
		return $title;
	}
}

// Filter to detect stream page ID
if ( !function_exists( 'prohost_trx_donations_get_stream_page_id' ) ) {
	//add_filter('prohost_filter_get_stream_page_id',	'prohost_trx_donations_get_stream_page_id', 9, 2);
	function prohost_trx_donations_get_stream_page_id($id, $page) {
		if (!empty($id)) return $id;
		if (prohost_strpos($page, 'donations')!==false) $id = prohost_get_template_page_id('blog-donations');
		return $id;
	}
}

// Filter to detect stream page URL
if ( !function_exists( 'prohost_trx_donations_get_stream_page_link' ) ) {
	//add_filter('prohost_filter_get_stream_page_link',	'prohost_trx_donations_get_stream_page_link', 9, 2);
	function prohost_trx_donations_get_stream_page_link($url, $page) {
		if (!empty($url)) return $url;
		if (prohost_strpos($page, 'donations')!==false) {
			$id = prohost_get_template_page_id('blog-donations');
			if ($id) $url = get_permalink($id);
		}
		return $url;
	}
}

// Filter to detect current taxonomy
if ( !function_exists( 'prohost_trx_donations_get_current_taxonomy' ) ) {
	//add_filter('prohost_filter_get_current_taxonomy',	'prohost_trx_donations_get_current_taxonomy', 9, 2);
	function prohost_trx_donations_get_current_taxonomy($tax, $page) {
		if (!empty($tax)) return $tax;
		if ( prohost_strpos($page, 'donations')!==false ) {
			$tax = PROHOST_Donations::TAXONOMY;
		}
		return $tax;
	}
}

// Return taxonomy name (slug) if current page is this taxonomy page
if ( !function_exists( 'prohost_trx_donations_is_taxonomy' ) ) {
	//add_filter('prohost_filter_is_taxonomy',	'prohost_trx_donations_is_taxonomy', 9, 2);
	function prohost_trx_donations_is_taxonomy($tax, $query=null) {
		if (!empty($tax))
			return $tax;
		else 
			return $query && $query->get(PROHOST_Donations::TAXONOMY)!='' || is_tax(PROHOST_Donations::TAXONOMY) ? PROHOST_Donations::TAXONOMY : '';
	}
}

// Add custom post type and/or taxonomies arguments to the query
if ( !function_exists( 'prohost_trx_donations_query_add_filters' ) ) {
	//add_filter('prohost_filter_query_add_filters',	'prohost_trx_donations_query_add_filters', 9, 2);
	function prohost_trx_donations_query_add_filters($args, $filter) {
		if ($filter == 'donations') {
			$args['post_type'] = PROHOST_Donations::POST_TYPE;
		}
		return $args;
	}
}

// Add custom post type to the list
if ( !function_exists( 'prohost_trx_donations_list_post_types' ) ) {
	//add_filter('prohost_filter_list_post_types',		'prohost_trx_donations_list_post_types');
	function prohost_trx_donations_list_post_types($list) {
		$list[PROHOST_Donations::POST_TYPE] = esc_html__('Donations', 'prohost');
		return $list;
	}
}


// Register shortcode in the shortcodes list
if (!function_exists('prohost_trx_donations_reg_shortcodes')) {
	//add_filter('prohost_action_shortcodes_list',	'prohost_trx_donations_reg_shortcodes');
	function prohost_trx_donations_reg_shortcodes() {
		if (prohost_storage_isset('shortcodes')) {

			$plugin = PROHOST_Donations::get_instance();
			$donations_groups = prohost_get_list_terms(false, PROHOST_Donations::TAXONOMY);

			prohost_sc_map_before('trx_dropcaps', array(

				// ProHost Donations form
				"trx_donations_form" => array(
					"title" => esc_html__("Donations form", "prohost"),
					"desc" => esc_html__("Insert ProHost Donations form", "prohost"),
					"decorate" => true,
					"container" => false,
					"params" => array(
						"title" => array(
							"title" => esc_html__("Title", "prohost"),
							"desc" => esc_html__("Title for the donations form", "prohost"),
							"value" => "",
							"type" => "text"
						),
						"subtitle" => array(
							"title" => esc_html__("Subtitle", "prohost"),
							"desc" => esc_html__("Subtitle for the donations form", "prohost"),
							"value" => "",
							"type" => "text"
						),
						"description" => array(
							"title" => esc_html__("Description", "prohost"),
							"desc" => esc_html__("Short description for the donations form", "prohost"),
							"value" => "",
							"type" => "textarea"
						),
						"align" => array(
							"title" => esc_html__("Alignment", "prohost"),
							"desc" => esc_html__("Alignment of the donations form", "prohost"),
							"divider" => true,
							"value" => "",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => prohost_get_sc_param('align')
						),
						"account" => array(
							"title" => esc_html__("PayPal account", "prohost"),
							"desc" => esc_html__("PayPal account's e-mail. If empty - used from ProHost Donations settings", "prohost"),
							"divider" => true,
							"value" => "",
							"type" => "text"
						),
						"sandbox" => array(
							"title" => esc_html__("Sandbox mode", "prohost"),
							"desc" => esc_html__("Use PayPal sandbox to test payments", "prohost"),
							"dependency" => array(
								'account' => array('not_empty')
							),
							"value" => "yes",
							"type" => "switch",
							"options" => prohost_get_sc_param('yes_no')
						),
						"amount" => array(
							"title" => esc_html__("Default amount", "prohost"),
							"desc" => esc_html__("Specify amount, initially selected in the form", "prohost"),
							"dependency" => array(
								'account' => array('not_empty')
							),
							"value" => 5,
							"min" => 1,
							"step" => 5,
							"type" => "spinner"
						),
						"currency" => array(
							"title" => esc_html__("Currency", "prohost"),
							"desc" => esc_html__("Select payment's currency", "prohost"),
							"dependency" => array(
								'account' => array('not_empty')
							),
							"divider" => true,
							"value" => "",
							"type" => "select",
							"style" => "list",
							"multiple" => true,
							"options" => prohost_array_merge(array(0 => esc_html__('- Select currency -', 'prohost')), $plugin->currency_codes)
						),
						"width" => prohost_shortcodes_width(),
						"top" => prohost_get_sc_param('top'),
						"bottom" => prohost_get_sc_param('bottom'),
						"left" => prohost_get_sc_param('left'),
						"right" => prohost_get_sc_param('right'),
						"id" => prohost_get_sc_param('id'),
						"class" => prohost_get_sc_param('class'),
						"css" => prohost_get_sc_param('css')
					)
				),
				
				
				// ProHost Donations form
				"trx_donations_list" => array(
					"title" => esc_html__("Donations list", "prohost"),
					"desc" => esc_html__("Insert ProHost Doantions list", "prohost"),
					"decorate" => true,
					"container" => false,
					"params" => array(
						"title" => array(
							"title" => esc_html__("Title", "prohost"),
							"desc" => esc_html__("Title for the donations list", "prohost"),
							"value" => "",
							"type" => "text"
						),
						"subtitle" => array(
							"title" => esc_html__("Subtitle", "prohost"),
							"desc" => esc_html__("Subtitle for the donations list", "prohost"),
							"value" => "",
							"type" => "text"
						),
						"description" => array(
							"title" => esc_html__("Description", "prohost"),
							"desc" => esc_html__("Short description for the donations list", "prohost"),
							"value" => "",
							"type" => "textarea"
						),
						"link" => array(
							"title" => esc_html__("Button URL", "prohost"),
							"desc" => esc_html__("Link URL for the button at the bottom of the block", "prohost"),
							"divider" => true,
							"value" => "",
							"type" => "text"
						),
						"link_caption" => array(
							"title" => esc_html__("Button caption", "prohost"),
							"desc" => esc_html__("Caption for the button at the bottom of the block", "prohost"),
							"value" => "",
							"type" => "text"
						),
						"style" => array(
							"title" => esc_html__("List style", "prohost"),
							"desc" => esc_html__("Select style to display donations", "prohost"),
							"value" => "excerpt",
							"type" => "select",
							"options" => array(
								'excerpt' => esc_html__('Excerpt', 'prohost')
							)
						),
						"readmore" => array(
							"title" => esc_html__("Read more text", "prohost"),
							"desc" => esc_html__("Text of the 'Read more' link", "prohost"),
							"value" => esc_html__('Read more', 'prohost'),
							"type" => "text"
						),
						"cat" => array(
							"title" => esc_html__("Categories", "prohost"),
							"desc" => esc_html__("Select categories (groups) to show donations. If empty - select donations from any category (group) or from IDs list", "prohost"),
							"divider" => true,
							"value" => "",
							"type" => "select",
							"style" => "list",
							"multiple" => true,
							"options" => prohost_array_merge(array(0 => esc_html__('- Select category -', 'prohost')), $donations_groups)
						),
						"count" => array(
							"title" => esc_html__("Number of donations", "prohost"),
							"desc" => esc_html__("How many donations will be displayed? If used IDs - this parameter ignored.", "prohost"),
							"value" => 3,
							"min" => 1,
							"max" => 100,
							"type" => "spinner"
						),
						"columns" => array(
							"title" => esc_html__("Columns", "prohost"),
							"desc" => esc_html__("How many columns use to show donations list", "prohost"),
							"value" => 3,
							"min" => 2,
							"max" => 6,
							"step" => 1,
							"type" => "spinner"
						),
						"offset" => array(
							"title" => esc_html__("Offset before select posts", "prohost"),
							"desc" => esc_html__("Skip posts before select next part.", "prohost"),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => 0,
							"min" => 0,
							"type" => "spinner"
						),
						"orderby" => array(
							"title" => esc_html__("Donadions order by", "prohost"),
							"desc" => esc_html__("Select desired sorting method", "prohost"),
							"value" => "date",
							"type" => "select",
							"options" => prohost_get_sc_param('sorting')
						),
						"order" => array(
							"title" => esc_html__("Donations order", "prohost"),
							"desc" => esc_html__("Select donations order", "prohost"),
							"value" => "desc",
							"type" => "switch",
							"size" => "big",
							"options" => prohost_get_sc_param('ordering')
						),
						"ids" => array(
							"title" => esc_html__("Donations IDs list", "prohost"),
							"desc" => esc_html__("Comma separated list of donations ID. If set - parameters above are ignored!", "prohost"),
							"value" => "",
							"type" => "text"
						),
						"top" => prohost_get_sc_param('top'),
						"bottom" => prohost_get_sc_param('bottom'),
						"id" => prohost_get_sc_param('id'),
						"class" => prohost_get_sc_param('class'),
						"css" => prohost_get_sc_param('css')
					)
				)

			));
		}
	}
}


// Register shortcode in the VC shortcodes list
if (!function_exists('prohost_trx_donations_reg_shortcodes_vc')) {
	//add_filter('prohost_action_shortcodes_list_vc',	'prohost_trx_donations_reg_shortcodes_vc');
	function prohost_trx_donations_reg_shortcodes_vc() {

		$plugin = PROHOST_Donations::get_instance();
		$donations_groups = prohost_get_list_terms(false, PROHOST_Donations::TAXONOMY);

		// ProHost Donations form
		vc_map( array(
				"base" => "trx_donations_form",
				"name" => esc_html__("Donations form", "prohost"),
				"description" => esc_html__("Insert ProHost Donations form", "prohost"),
				"category" => esc_html__('Content', 'prohost'),
				'icon' => 'icon_trx_donations_form',
				"class" => "trx_sc_single trx_sc_donations_form",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "title",
						"heading" => esc_html__("Title", "prohost"),
						"description" => esc_html__("Title for the donations form", "prohost"),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "subtitle",
						"heading" => esc_html__("Subtitle", "prohost"),
						"description" => esc_html__("Subtitle for the donations form", "prohost"),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "description",
						"heading" => esc_html__("Description", "prohost"),
						"description" => esc_html__("Description for the donations form", "prohost"),
						"class" => "",
						"value" => "",
						"type" => "textarea"
					),
					array(
						"param_name" => "align",
						"heading" => esc_html__("Alignment", "prohost"),
						"description" => esc_html__("Alignment of the donations form", "prohost"),
						"class" => "",
						"value" => array_flip(prohost_get_sc_param('align')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "account",
						"heading" => esc_html__("PayPal account", "prohost"),
						"description" => esc_html__("PayPal account's e-mail. If empty - used from ProHost Donations settings", "prohost"),
						"admin_label" => true,
						"group" => esc_html__('PayPal', 'prohost'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "sandbox",
						"heading" => esc_html__("Sandbox mode", "prohost"),
						"description" => esc_html__("Use PayPal sandbox to test payments", "prohost"),
						"admin_label" => true,
						"group" => esc_html__('PayPal', 'prohost'),
						'dependency' => array(
							'element' => 'account',
							'not_empty' => true
						),
						"class" => "",
						"value" => array("Sandbox mode" => "yes" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "amount",
						"heading" => esc_html__("Default amount", "prohost"),
						"description" => esc_html__("Specify amount, initially selected in the form", "prohost"),
						"admin_label" => true,
						"group" => esc_html__('PayPal', 'prohost'),
						"class" => "",
						"value" => "5",
						"type" => "textfield"
					),
					array(
						"param_name" => "currency",
						"heading" => esc_html__("Currency", "prohost"),
						"description" => esc_html__("Select payment's currency", "prohost"),
						"class" => "",
						"value" => array_flip(prohost_array_merge(array(0 => esc_html__('- Select currency -', 'prohost')), $plugin->currency_codes)),
						"type" => "dropdown"
					),
					prohost_get_vc_param('id'),
					prohost_get_vc_param('class'),
					prohost_get_vc_param('css'),
					prohost_vc_width(),
					prohost_get_vc_param('margin_top'),
					prohost_get_vc_param('margin_bottom'),
					prohost_get_vc_param('margin_left'),
					prohost_get_vc_param('margin_right')
				)
			) );
			
		class WPBakeryShortCode_Trx_Donations_Form extends PROHOST_VC_ShortCodeSingle {}



		// ProHost Donations list
		vc_map( array(
				"base" => "trx_donations_list",
				"name" => esc_html__("Donations list", "prohost"),
				"description" => esc_html__("Insert ProHost Donations list", "prohost"),
				"category" => esc_html__('Content', 'prohost'),
				'icon' => 'icon_trx_donations_list',
				"class" => "trx_sc_single trx_sc_donations_list",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "style",
						"heading" => esc_html__("List style", "prohost"),
						"description" => esc_html__("Select style to display donations", "prohost"),
						"class" => "",
						"value" => array(
							esc_html__('Excerpt', 'prohost') => 'excerpt'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "title",
						"heading" => esc_html__("Title", "prohost"),
						"description" => esc_html__("Title for the donations form", "prohost"),
						"group" => esc_html__('Captions', 'prohost'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "subtitle",
						"heading" => esc_html__("Subtitle", "prohost"),
						"description" => esc_html__("Subtitle for the donations form", "prohost"),
						"group" => esc_html__('Captions', 'prohost'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "description",
						"heading" => esc_html__("Description", "prohost"),
						"description" => esc_html__("Description for the donations form", "prohost"),
						"group" => esc_html__('Captions', 'prohost'),
						"class" => "",
						"value" => "",
						"type" => "textarea"
					),
					array(
						"param_name" => "link",
						"heading" => esc_html__("Button URL", "prohost"),
						"description" => esc_html__("Link URL for the button at the bottom of the block", "prohost"),
						"group" => esc_html__('Captions', 'prohost'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "link_caption",
						"heading" => esc_html__("Button caption", "prohost"),
						"description" => esc_html__("Caption for the button at the bottom of the block", "prohost"),
						"group" => esc_html__('Captions', 'prohost'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "readmore",
						"heading" => esc_html__("Read more text", "prohost"),
						"description" => esc_html__("Text of the 'Read more' link", "prohost"),
						"group" => esc_html__('Captions', 'prohost'),
						"class" => "",
						"value" => esc_html__('Read more', 'prohost'),
						"type" => "textfield"
					),
					array(
						"param_name" => "cat",
						"heading" => esc_html__("Categories", "prohost"),
						"description" => esc_html__("Select category to show donations. If empty - select donations from any category (group) or from IDs list", "prohost"),
						"group" => esc_html__('Query', 'prohost'),
						"class" => "",
						"value" => array_flip(prohost_array_merge(array(0 => esc_html__('- Select category -', 'prohost')), $donations_groups)),
						"type" => "dropdown"
					),
					array(
						"param_name" => "columns",
						"heading" => esc_html__("Columns", "prohost"),
						"description" => esc_html__("How many columns use to show donations", "prohost"),
						"group" => esc_html__('Query', 'prohost'),
						"admin_label" => true,
						"class" => "",
						"value" => "3",
						"type" => "textfield"
					),
					array(
						"param_name" => "count",
						"heading" => esc_html__("Number of posts", "prohost"),
						"description" => esc_html__("How many posts will be displayed? If used IDs - this parameter ignored.", "prohost"),
						"group" => esc_html__('Query', 'prohost'),
						"class" => "",
						"value" => "3",
						"type" => "textfield"
					),
					array(
						"param_name" => "offset",
						"heading" => esc_html__("Offset before select posts", "prohost"),
						"description" => esc_html__("Skip posts before select next part.", "prohost"),
						"group" => esc_html__('Query', 'prohost'),
						"class" => "",
						"value" => "0",
						"type" => "textfield"
					),
					array(
						"param_name" => "orderby",
						"heading" => esc_html__("Post sorting", "prohost"),
						"description" => esc_html__("Select desired posts sorting method", "prohost"),
						"group" => esc_html__('Query', 'prohost'),
						"class" => "",
						"value" => array_flip(prohost_get_sc_param('sorting')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "order",
						"heading" => esc_html__("Post order", "prohost"),
						"description" => esc_html__("Select desired posts order", "prohost"),
						"group" => esc_html__('Query', 'prohost'),
						"class" => "",
						"value" => array_flip(prohost_get_sc_param('ordering')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "ids",
						"heading" => esc_html__("client's IDs list", "prohost"),
						"description" => esc_html__("Comma separated list of donation's ID. If set - parameters above (category, count, order, etc.)  are ignored!", "prohost"),
						"group" => esc_html__('Query', 'prohost'),
						'dependency' => array(
							'element' => 'cats',
							'is_empty' => true
						),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),

					prohost_get_vc_param('id'),
					prohost_get_vc_param('class'),
					prohost_get_vc_param('css'),
					prohost_get_vc_param('margin_top'),
					prohost_get_vc_param('margin_bottom')
				)
			) );
			
		class WPBakeryShortCode_Trx_Donations_List extends PROHOST_VC_ShortCodeSingle {}

	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'prohost_trx_donations_required_plugins' ) ) {
	//add_filter('prohost_filter_required_plugins',	'prohost_trx_donations_required_plugins');
	function prohost_trx_donations_required_plugins($list=array()) {
		if (in_array('trx_donations', prohost_storage_get('required_plugins'))) {
			$path = prohost_get_file_dir('plugins/install/trx_donations.zip');
			if (file_exists($path)) {
				$list[] = array(
					'name' 		=> 'ProHost Donations',
					'slug' 		=> 'trx_donations',
					'source'	=> $path,
					'required' 	=> false
					);
			}
		}
		return $list;
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check in the required plugins
if ( !function_exists( 'prohost_trx_donations_importer_required_plugins' ) ) {
	//add_filter( 'prohost_filter_importer_required_plugins',	'prohost_trx_donations_importer_required_plugins', 10, 2 );
	function prohost_trx_donations_importer_required_plugins($not_installed='', $list='') {
		//if (in_array('trx_donations', prohost_storage_get('required_plugins')) && !prohost_exists_trx_donations() )
		if (prohost_strpos($list, 'trx_donations')!==false && !prohost_exists_trx_donations() )
			$not_installed .= '<br>ProHost Donations';
		return $not_installed;
	}
}

// Set options for one-click importer
if ( !function_exists( 'prohost_trx_donations_importer_set_options' ) ) {
	//add_filter( 'prohost_filter_importer_options',	'prohost_trx_donations_importer_set_options' );
	function prohost_trx_donations_importer_set_options($options=array()) {
		if ( in_array('trx_donations', prohost_storage_get('required_plugins')) && prohost_exists_trx_donations() ) {
			$options['additional_options'][] = 'trx_donations_options';		// Add slugs to export options for this plugin

		}
		return $options;
	}
}
?>