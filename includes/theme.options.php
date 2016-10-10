<?php

/* Theme setup section
-------------------------------------------------------------------- */

// ONLY FOR PROGRAMMERS, NOT FOR CUSTOMER
// Framework settings

prohost_storage_set('settings', array(
	
	'less_compiler'		=> 'lessc',								// no|lessc|less - Compiler for the .less
																// lessc - fast & low memory required, but .less-map, shadows & gradients not supprted
																// less  - slow, but support all features
	'less_nested'		=> false,								// Use nested selectors when compiling less - increase .css size, but allow using nested color schemes
	'less_prefix'		=> '',									// any string - Use prefix before each selector when compile less. For example: 'html '
	'less_separator'	=> '/*---LESS_SEPARATOR---*/',			// string - separator inside .less file to split it when compiling to reduce memory usage
																// (compilation speed gets a bit slow)
	'less_map'			=> 'no',								// no|internal|external - Generate map for .less files.
																// Warning! You need more then 128Mb for PHP scripts on your server! Supported only if less_compiler=less (see above)
	
	'customizer_demo'	=> true,								// Show color customizer demo (if many color settings) or not (if only accent colors used)

	'allow_fullscreen'	=> false,								// Allow fullscreen and fullwide body styles

	'socials_type'		=> 'icons',								// images|icons - Use this kind of pictograms for all socials: share, social profiles, team members socials, etc.
	'slides_type'		=> 'bg',								// images|bg - Use image as slide's content or as slide's background

	'add_image_size'	=> false,								// Add theme's thumb sizes into WP list sizes. 
																// If false - new image thumb will be generated on demand,
																// otherwise - all thumb sizes will be generated when image is loaded

	'use_list_cache'	=> true,								// Use cache for any lists (increase theme speed, but get 15-20K memory)
	'use_post_cache'	=> true,								// Use cache for post_data (increase theme speed, decrease queries number, but get more memory - up to 300K)

	'allow_profiler'	=> true,								// Allow to show theme profiler when 'debug mode' is on

	'admin_dummy_style' => 2									// 1 | 2 - Progress bar style when import dummy data
	)
);



// Default Theme Options
if ( !function_exists( 'prohost_options_settings_theme_setup' ) ) {
	add_action( 'prohost_action_before_init_theme', 'prohost_options_settings_theme_setup', 2 );	// Priority 1 for add prohost_filter handlers
	function prohost_options_settings_theme_setup() {
		
		// Clear all saved Theme Options on first theme run
		add_action('after_switch_theme', 'prohost_options_reset');

		// Settings 
		$socials_type = prohost_get_theme_setting('socials_type');
				
		// Prepare arrays 
		prohost_storage_set('options_params', apply_filters('prohost_filter_theme_options_params', array(
			'list_fonts'				=> array('$prohost_get_list_fonts' => ''),
			'list_fonts_styles'			=> array('$prohost_get_list_fonts_styles' => ''),
			'list_socials' 				=> array('$prohost_get_list_socials' => ''),
			'list_icons' 				=> array('$prohost_get_list_icons' => ''),
			'list_posts_types' 			=> array('$prohost_get_list_posts_types' => ''),
			'list_categories' 			=> array('$prohost_get_list_categories' => ''),
			'list_menus'				=> array('$prohost_get_list_menus' => ''),
			'list_sidebars'				=> array('$prohost_get_list_sidebars' => ''),
			'list_positions' 			=> array('$prohost_get_list_sidebars_positions' => ''),
			'list_skins'				=> array('$prohost_get_list_skins' => ''),
			'list_color_schemes'		=> array('$prohost_get_list_color_schemes' => ''),
			'list_bg_tints'				=> array('$prohost_get_list_bg_tints' => ''),
			'list_body_styles'			=> array('$prohost_get_list_body_styles' => ''),
			'list_header_styles'		=> array('$prohost_get_list_templates_header' => ''),
			'list_blog_styles'			=> array('$prohost_get_list_templates_blog' => ''),
			'list_single_styles'		=> array('$prohost_get_list_templates_single' => ''),
			'list_article_styles'		=> array('$prohost_get_list_article_styles' => ''),
			'list_blog_counters' 		=> array('$prohost_get_list_blog_counters' => ''),
			'list_animations_in' 		=> array('$prohost_get_list_animations_in' => ''),
			'list_animations_out'		=> array('$prohost_get_list_animations_out' => ''),
			'list_filters'				=> array('$prohost_get_list_portfolio_filters' => ''),
			'list_hovers'				=> array('$prohost_get_list_hovers' => ''),
			'list_hovers_dir'			=> array('$prohost_get_list_hovers_directions' => ''),
			'list_alter_sizes'			=> array('$prohost_get_list_alter_sizes' => ''),
			'list_sliders' 				=> array('$prohost_get_list_sliders' => ''),
			'list_bg_image_positions'	=> array('$prohost_get_list_bg_image_positions' => ''),
			'list_popups' 				=> array('$prohost_get_list_popup_engines' => ''),
			'list_gmap_styles'		 	=> array('$prohost_get_list_googlemap_styles' => ''),
			'list_yes_no' 				=> array('$prohost_get_list_yesno' => ''),
			'list_on_off' 				=> array('$prohost_get_list_onoff' => ''),
			'list_show_hide' 			=> array('$prohost_get_list_showhide' => ''),
			'list_sorting' 				=> array('$prohost_get_list_sortings' => ''),
			'list_ordering' 			=> array('$prohost_get_list_orderings' => ''),
			'list_locations' 			=> array('$prohost_get_list_dedicated_locations' => '')
			)
		));


		// Theme options array
		prohost_storage_set('options', array(

		
		//###############################
		//#### Customization         #### 
		//###############################
		'partition_customization' => array(
					"title" => esc_html__('Customization', 'prohost'),
					"start" => "partitions",
					"override" => "category,services_group,page,post",
					"icon" => "iconadmin-cog-alt",
					"type" => "partition"
					),
		
		
		// Customization -> Body Style
		//-------------------------------------------------
		
		'customization_body' => array(
					"title" => esc_html__('Body style', 'prohost'),
					"override" => "category,services_group,post,page",
					"icon" => 'iconadmin-picture',
					"start" => "customization_tabs",
					"type" => "tab"
					),
		
		'info_body_1' => array(
					"title" => esc_html__('Body parameters', 'prohost'),
					"desc" => wp_kses_data( __('Select body style, skin and color scheme for entire site. You can override this parameters on any page, post or category', 'prohost') ),
					"override" => "category,services_group,post,page",
					"type" => "info"
					),

		'body_style' => array(
					"title" => esc_html__('Body style', 'prohost'),
					"desc" => wp_kses_data( __('Select body style:', 'prohost') )
								. ' <br>' 
								. wp_kses_data( __('<b>boxed</b> - if you want use background color and/or image', 'prohost') )
								. ',<br>'
								. wp_kses_data( __('<b>wide</b> - page fill whole window with centered content', 'prohost') )
								. (prohost_get_theme_setting('allow_fullscreen') 
									? ',<br>' . wp_kses_data( __('<b>fullwide</b> - page content stretched on the full width of the window (with few left and right paddings)', 'prohost') )
									: '')
								. (prohost_get_theme_setting('allow_fullscreen') 
									? ',<br>' . wp_kses_data( __('<b>fullscreen</b> - page content fill whole window without any paddings', 'prohost') )
									: ''),
					"override" => "category,services_group,post,page",
					"std" => "wide",
					"options" => prohost_get_options_param('list_body_styles'),
					"dir" => "horizontal",
					"type" => "radio"
					),
		
		'body_paddings' => array(
					"title" => esc_html__('Page paddings', 'prohost'),
					"desc" => wp_kses_data( __('Add paddings above and below the page content', 'prohost') ),
					"override" => "post,page",
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"
					),

		'theme_skin' => array(
					"title" => esc_html__('Select theme skin', 'prohost'),
					"desc" => wp_kses_data( __('Select skin for the theme decoration', 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "default",
					"options" => prohost_get_options_param('list_skins'),
					"type" => "select"
					),

		"body_scheme" => array(
					"title" => esc_html__('Color scheme', 'prohost'),
					"desc" => wp_kses_data( __('Select predefined color scheme for the entire page', 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "original",
					"dir" => "horizontal",
					"options" => prohost_get_options_param('list_color_schemes'),
					"type" => "checklist"),
		
		'body_filled' => array(
					"title" => esc_html__('Fill body', 'prohost'),
					"desc" => wp_kses_data( __('Fill the page background with the solid color or leave it transparend to show background image (or video background)', 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"
					),

		'info_body_2' => array(
					"title" => esc_html__('Background color and image', 'prohost'),
					"desc" => wp_kses_data( __('Color and image for the site background', 'prohost') ),
					"override" => "category,services_group,post,page",
					"type" => "info"
					),

		'bg_custom' => array(
					"title" => esc_html__('Use custom background',  'prohost'),
					"desc" => wp_kses_data( __("Use custom color and/or image as the site background", 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "no",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"
					),
		
		'bg_color' => array(
					"title" => esc_html__('Background color',  'prohost'),
					"desc" => wp_kses_data( __('Body background color',  'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'bg_custom' => array('yes')
					),
					"std" => "#ffffff",
					"type" => "color"
					),

		'bg_pattern' => array(
					"title" => esc_html__('Background predefined pattern',  'prohost'),
					"desc" => wp_kses_data( __('Select theme background pattern (first case - without pattern)',  'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'bg_custom' => array('yes')
					),
					"std" => "",
					"options" => array(
						0 => prohost_get_file_url('images/spacer.png'),
						1 => prohost_get_file_url('images/bg/pattern_1.jpg'),
						2 => prohost_get_file_url('images/bg/pattern_2.jpg'),
						3 => prohost_get_file_url('images/bg/pattern_3.jpg'),
						4 => prohost_get_file_url('images/bg/pattern_4.jpg'),
						5 => prohost_get_file_url('images/bg/pattern_5.jpg')
					),
					"style" => "list",
					"type" => "images"
					),
		
		'bg_pattern_custom' => array(
					"title" => esc_html__('Background custom pattern',  'prohost'),
					"desc" => wp_kses_data( __('Select or upload background custom pattern. If selected - use it instead the theme predefined pattern (selected in the field above)',  'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'bg_custom' => array('yes')
					),
					"std" => "",
					"type" => "media"
					),
		
		'bg_image' => array(
					"title" => esc_html__('Background predefined image',  'prohost'),
					"desc" => wp_kses_data( __('Select theme background image (first case - without image)',  'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "",
					"dependency" => array(
						'bg_custom' => array('yes')
					),
					"options" => array(
						0 => prohost_get_file_url('images/spacer.png'),
						1 => prohost_get_file_url('images/bg/image_1_thumb.jpg'),
						2 => prohost_get_file_url('images/bg/image_2_thumb.jpg'),
						3 => prohost_get_file_url('images/bg/image_3_thumb.jpg')
					),
					"style" => "list",
					"type" => "images"
					),
		
		'bg_image_custom' => array(
					"title" => esc_html__('Background custom image',  'prohost'),
					"desc" => wp_kses_data( __('Select or upload background custom image. If selected - use it instead the theme predefined image (selected in the field above)',  'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'bg_custom' => array('yes')
					),
					"std" => "",
					"type" => "media"
					),
		
		'bg_image_custom_position' => array( 
					"title" => esc_html__('Background custom image position',  'prohost'),
					"desc" => wp_kses_data( __('Select custom image position',  'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "left_top",
					"dependency" => array(
						'bg_custom' => array('yes')
					),
					"options" => array(
						'left_top' => "Left Top",
						'center_top' => "Center Top",
						'right_top' => "Right Top",
						'left_center' => "Left Center",
						'center_center' => "Center Center",
						'right_center' => "Right Center",
						'left_bottom' => "Left Bottom",
						'center_bottom' => "Center Bottom",
						'right_bottom' => "Right Bottom",
					),
					"type" => "select"
					),
		
		'bg_image_load' => array(
					"title" => esc_html__('Load background image', 'prohost'),
					"desc" => wp_kses_data( __('Always load background images or only for boxed body style', 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "boxed",
					"size" => "medium",
					"dependency" => array(
						'bg_custom' => array('yes')
					),
					"options" => array(
						'boxed' => esc_html__('Boxed', 'prohost'),
						'always' => esc_html__('Always', 'prohost')
					),
					"type" => "switch"
					),

		
		'info_body_3' => array(
					"title" => esc_html__('Video background', 'prohost'),
					"desc" => wp_kses_data( __('Parameters of the video, used as site background', 'prohost') ),
					"override" => "category,services_group,post,page",
					"type" => "info"
					),

		'show_video_bg' => array(
					"title" => esc_html__('Show video background',  'prohost'),
					"desc" => wp_kses_data( __("Show video as the site background", 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "no",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"
					),

		'video_bg_youtube_code' => array(
					"title" => esc_html__('Youtube code for video bg',  'prohost'),
					"desc" => wp_kses_data( __("Youtube code of video", 'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'show_video_bg' => array('yes')
					),
					"std" => "",
					"type" => "text"
					),

		'video_bg_url' => array(
					"title" => esc_html__('Local video for video bg',  'prohost'),
					"desc" => wp_kses_data( __("URL to video-file (uploaded on your site)", 'prohost') ),
					"readonly" =>false,
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'show_video_bg' => array('yes')
					),
					"before" => array(	'title' => esc_html__('Choose video', 'prohost'),
										'action' => 'media_upload',
										'multiple' => false,
										'linked_field' => '',
										'type' => 'video',
										'captions' => array('choose' => esc_html__( 'Choose Video', 'prohost'),
															'update' => esc_html__( 'Select Video', 'prohost')
														)
								),
					"std" => "",
					"type" => "media"
					),

		'video_bg_overlay' => array(
					"title" => esc_html__('Use overlay for video bg', 'prohost'),
					"desc" => wp_kses_data( __('Use overlay texture for the video background', 'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'show_video_bg' => array('yes')
					),
					"std" => "no",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"
					),
		
		
		
		
		
		// Customization -> Header
		//-------------------------------------------------
		
		'customization_header' => array(
					"title" => esc_html__("Header", 'prohost'),
					"override" => "category,services_group,post,page",
					"icon" => 'iconadmin-window',
					"type" => "tab"),
		
		"info_header_1" => array(
					"title" => esc_html__('Top panel', 'prohost'),
					"desc" => wp_kses_data( __('Top panel settings. It include user menu area (with contact info, cart button, language selector, login/logout menu and user menu) and main menu area (with logo and main menu).', 'prohost') ),
					"override" => "category,services_group,post,page",
					"type" => "info"),
		
		"top_panel_style" => array(
					"title" => esc_html__('Top panel style', 'prohost'),
					"desc" => wp_kses_data( __('Select desired style of the page header', 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "header_3",
					"options" => prohost_get_options_param('list_header_styles'),
					"style" => "list",
					"type" => "images"),

		"top_panel_image" => array(
					"title" => esc_html__('Top panel image', 'prohost'),
					"desc" => wp_kses_data( __('Select default background image of the page header (if not single post or featured image for current post is not specified)', 'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'top_panel_style' => array('header_7')
					),
					"std" => "",
					"type" => "media"),
		
		"top_panel_position" => array( 
					"title" => esc_html__('Top panel position', 'prohost'),
					"desc" => wp_kses_data( __('Select position for the top panel with logo and main menu', 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "above",
					"options" => array(
						'hide'  => esc_html__('Hide', 'prohost'),
						'above' => esc_html__('Above slider', 'prohost'),
						'below' => esc_html__('Below slider', 'prohost'),
						'over'  => esc_html__('Over slider', 'prohost')
					),
					"type" => "checklist"),

		"top_panel_scheme" => array(
					"title" => esc_html__('Top panel color scheme', 'prohost'),
					"desc" => wp_kses_data( __('Select predefined color scheme for the top panel', 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "original",
					"dir" => "horizontal",
					"options" => prohost_get_options_param('list_color_schemes'),
					"type" => "checklist"),

		"pushy_panel_scheme" => array(
					"title" => esc_html__('Push panel color scheme', 'prohost'),
					"desc" => wp_kses_data( __('Select predefined color scheme for the push panel (with logo, menu and socials)', 'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'top_panel_style' => array('header_8')
					),
					"std" => "dark",
					"dir" => "horizontal",
					"options" => prohost_get_options_param('list_color_schemes'),
					"type" => "checklist"),
		
		"show_page_title" => array(
					"title" => esc_html__('Show Page title', 'prohost'),
					"desc" => wp_kses_data( __('Show post/page/category title', 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"show_breadcrumbs" => array(
					"title" => esc_html__('Show Breadcrumbs', 'prohost'),
					"desc" => wp_kses_data( __('Show path to current category (post, page)', 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"breadcrumbs_max_level" => array(
					"title" => esc_html__('Breadcrumbs max nesting', 'prohost'),
					"desc" => wp_kses_data( __("Max number of the nested categories in the breadcrumbs (0 - unlimited)", 'prohost') ),
					"dependency" => array(
						'show_breadcrumbs' => array('yes')
					),
					"std" => "0",
					"min" => 0,
					"max" => 100,
					"step" => 1,
					"type" => "spinner"),

		
		
		
		"info_header_2" => array( 
					"title" => esc_html__('Main menu style and position', 'prohost'),
					"desc" => wp_kses_data( __('Select the Main menu style and position', 'prohost') ),
					"override" => "category,services_group,post,page",
					"type" => "info"),
		
		"menu_main" => array( 
					"title" => esc_html__('Select main menu',  'prohost'),
					"desc" => wp_kses_data( __('Select main menu for the current page',  'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "default",
					"options" => prohost_get_options_param('list_menus'),
					"type" => "select"),
		
		"menu_attachment" => array( 
					"title" => esc_html__('Main menu attachment', 'prohost'),
					"desc" => wp_kses_data( __('Attach main menu to top of window then page scroll down', 'prohost') ),
					"std" => "fixed",
					"options" => array(
						"fixed"=>esc_html__("Fix menu position", 'prohost'), 
						"none"=>esc_html__("Don't fix menu position", 'prohost')
					),
					"dir" => "vertical",
					"type" => "radio"),

		"menu_slider" => array( 
					"title" => esc_html__('Main menu slider', 'prohost'),
					"desc" => wp_kses_data( __('Use slider background for main menu items', 'prohost') ),
					"std" => "yes",
					"type" => "switch",
					"options" => prohost_get_options_param('list_yes_no')),

		"menu_animation_in" => array( 
					"title" => esc_html__('Submenu show animation', 'prohost'),
					"desc" => wp_kses_data( __('Select animation to show submenu ', 'prohost') ),
					"std" => "bounceIn",
					"type" => "select",
					"options" => prohost_get_options_param('list_animations_in')),

		"menu_animation_out" => array( 
					"title" => esc_html__('Submenu hide animation', 'prohost'),
					"desc" => wp_kses_data( __('Select animation to hide submenu ', 'prohost') ),
					"std" => "fadeOutDown",
					"type" => "select",
					"options" => prohost_get_options_param('list_animations_out')),
		
		"menu_relayout" => array( 
					"title" => esc_html__('Main menu relayout', 'prohost'),
					"desc" => wp_kses_data( __('Allow relayout main menu if window width less then this value', 'prohost') ),
					"std" => 960,
					"min" => 320,
					"max" => 1024,
					"type" => "spinner"),
		
		"menu_responsive" => array( 
					"title" => esc_html__('Main menu responsive', 'prohost'),
					"desc" => wp_kses_data( __('Allow responsive version for the main menu if window width less then this value', 'prohost') ),
					"std" => 640,
					"min" => 320,
					"max" => 1024,
					"type" => "spinner"),
		
		"menu_width" => array( 
					"title" => esc_html__('Submenu width', 'prohost'),
					"desc" => wp_kses_data( __('Width for dropdown menus in main menu', 'prohost') ),
					"step" => 5,
					"std" => "",
					"min" => 180,
					"max" => 300,
					"mask" => "?999",
					"type" => "spinner"),
		
		
		
		"info_header_3" => array(
					"title" => esc_html__("User's menu area components", 'prohost'),
					"desc" => wp_kses_data( __("Select parts for the user's menu area", 'prohost') ),
					"override" => "category,services_group,page,post",
					"type" => "info"),
		
		"show_top_panel_top" => array(
					"title" => esc_html__('Show user menu area', 'prohost'),
					"desc" => wp_kses_data( __('Show user menu area on top of page', 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"menu_user" => array(
					"title" => esc_html__('Select user menu',  'prohost'),
					"desc" => wp_kses_data( __('Select user menu for the current page',  'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'show_top_panel_top' => array('yes')
					),
					"std" => "default",
					"options" => prohost_get_options_param('list_menus'),
					"type" => "select"),
		
		"show_languages" => array(
					"title" => esc_html__('Show language selector', 'prohost'),
					"desc" => wp_kses_data( __('Show language selector in the user menu (if WPML plugin installed and current page/post has multilanguage version)', 'prohost') ),
					"dependency" => array(
						'show_top_panel_top' => array('yes')
					),
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"show_login" => array( 
					"title" => esc_html__('Show Login/Logout buttons', 'prohost'),
					"desc" => wp_kses_data( __('Show Login and Logout buttons in the user menu area', 'prohost') ),
					"dependency" => array(
						'show_top_panel_top' => array('yes')
					),
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"show_bookmarks" => array(
					"title" => esc_html__('Show bookmarks', 'prohost'),
					"desc" => wp_kses_data( __('Show bookmarks selector in the user menu', 'prohost') ),
					"dependency" => array(
						'show_top_panel_top' => array('yes')
					),
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"show_socials" => array( 
					"title" => esc_html__('Show Social icons', 'prohost'),
					"desc" => wp_kses_data( __('Show Social icons in the user menu area', 'prohost') ),
					"dependency" => array(
						'show_top_panel_top' => array('yes')
					),
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		

		
		"info_header_4" => array( 
					"title" => esc_html__("Table of Contents (TOC)", 'prohost'),
					"desc" => wp_kses_data( __("Table of Contents for the current page. Automatically created if the page contains objects with id starting with 'toc_'", 'prohost') ),
					"override" => "category,services_group,page,post",
					"type" => "info"),
		
		"menu_toc" => array( 
					"title" => esc_html__('TOC position', 'prohost'),
					"desc" => wp_kses_data( __('Show TOC for the current page', 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "float",
					"options" => array(
						'hide'  => esc_html__('Hide', 'prohost'),
						'fixed' => esc_html__('Fixed', 'prohost'),
						'float' => esc_html__('Float', 'prohost')
					),
					"type" => "checklist"),
		
		"menu_toc_home" => array(
					"title" => esc_html__('Add "Home" into TOC', 'prohost'),
					"desc" => wp_kses_data( __('Automatically add "Home" item into table of contents - return to home page of the site', 'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'menu_toc' => array('fixed','float')
					),
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"menu_toc_top" => array( 
					"title" => esc_html__('Add "To Top" into TOC', 'prohost'),
					"desc" => wp_kses_data( __('Automatically add "To Top" item into table of contents - scroll to top of the page', 'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'menu_toc' => array('fixed','float')
					),
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),

		
		
		
		'info_header_5' => array(
					"title" => esc_html__('Main logo', 'prohost'),
					"desc" => wp_kses_data( __("Select or upload logos for the site's header and select it position", 'prohost') ),
					"override" => "category,services_group,post,page",
					"type" => "info"
					),

		'favicon' => array(
					"title" => esc_html__('Favicon', 'prohost'),
					"desc" => wp_kses_data( __("Upload a 16px x 16px image that will represent your website's favicon.<br /><em>To ensure cross-browser compatibility, we recommend converting the favicon into .ico format before uploading. (<a href='http://www.favicon.cc/'>www.favicon.cc</a>)</em>", 'prohost') ),
					"std" => "",
					"type" => "media"
					),

		'logo' => array(
					"title" => esc_html__('Logo image', 'prohost'),
					"desc" => wp_kses_data( __('Main logo image', 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "",
					"type" => "media"
					),

		'logo_retina' => array(
					"title" => esc_html__('Logo image for Retina', 'prohost'),
					"desc" => wp_kses_data( __('Main logo image used on Retina display', 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "",
					"type" => "media"
					),

		'logo_fixed' => array(
					"title" => esc_html__('Logo image (fixed header)', 'prohost'),
					"desc" => wp_kses_data( __('Logo image for the header (if menu is fixed after the page is scrolled)', 'prohost') ),
					"override" => "category,services_group,post,page",
					"divider" => false,
					"std" => "",
					"type" => "media"
					),

		'logo_text' => array(
					"title" => esc_html__('Logo text', 'prohost'),
					"desc" => wp_kses_data( __('Logo text - display it after logo image', 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => '',
					"type" => "text"
					),

		'logo_height' => array(
					"title" => esc_html__('Logo height', 'prohost'),
					"desc" => wp_kses_data( __('Height for the logo in the header area', 'prohost') ),
					"override" => "category,services_group,post,page",
					"step" => 1,
					"std" => '',
					"min" => 10,
					"max" => 300,
					"mask" => "?999",
					"type" => "spinner"
					),

		'logo_offset' => array(
					"title" => esc_html__('Logo top offset', 'prohost'),
					"desc" => wp_kses_data( __('Top offset for the logo in the header area', 'prohost') ),
					"override" => "category,services_group,post,page",
					"step" => 1,
					"std" => '',
					"min" => 0,
					"max" => 99,
					"mask" => "?99",
					"type" => "spinner"
					),
		
		
		
		
		
		
		
		// Customization -> Slider
		//-------------------------------------------------
		
		"customization_slider" => array( 
					"title" => esc_html__('Slider', 'prohost'),
					"icon" => "iconadmin-picture",
					"override" => "category,services_group,page",
					"type" => "tab"),
		
		"info_slider_1" => array(
					"title" => esc_html__('Main slider parameters', 'prohost'),
					"desc" => wp_kses_data( __('Select parameters for main slider (you can override it in each category and page)', 'prohost') ),
					"override" => "category,services_group,page",
					"type" => "info"),
					
		"show_slider" => array(
					"title" => esc_html__('Show Slider', 'prohost'),
					"desc" => wp_kses_data( __('Do you want to show slider on each page (post)', 'prohost') ),
					"override" => "category,services_group,page",
					"std" => "no",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
					
		"slider_display" => array(
					"title" => esc_html__('Slider display', 'prohost'),
					"desc" => wp_kses_data( __('How display slider: boxed (fixed width and height), fullwide (fixed height) or fullscreen', 'prohost') ),
					"override" => "category,services_group,page",
					"dependency" => array(
						'show_slider' => array('yes')
					),
					"std" => "fullwide",
					"options" => array(
						"boxed"=>esc_html__("Boxed", 'prohost'),
						"fullwide"=>esc_html__("Fullwide", 'prohost'),
						"fullscreen"=>esc_html__("Fullscreen", 'prohost')
					),
					"type" => "checklist"),
		
		"slider_height" => array(
					"title" => esc_html__("Height (in pixels)", 'prohost'),
					"desc" => wp_kses_data( __("Slider height (in pixels) - only if slider display with fixed height.", 'prohost') ),
					"override" => "category,services_group,page",
					"dependency" => array(
						'show_slider' => array('yes')
					),
					"std" => '',
					"min" => 100,
					"step" => 10,
					"type" => "spinner"),
		
		"slider_engine" => array(
					"title" => esc_html__('Slider engine', 'prohost'),
					"desc" => wp_kses_data( __('What engine use to show slider?', 'prohost') ),
					"override" => "category,services_group,page",
					"dependency" => array(
						'show_slider' => array('yes')
					),
					"std" => "swiper",
					"options" => prohost_get_options_param('list_sliders'),
					"type" => "radio"),
		
		"slider_category" => array(
					"title" => esc_html__('Posts Slider: Category to show', 'prohost'),
					"desc" => wp_kses_data( __('Select category to show in Flexslider (ignored for Revolution and Royal sliders)', 'prohost') ),
					"override" => "category,services_group,page",
					"dependency" => array(
						'show_slider' => array('yes'),
						'slider_engine' => array('swiper')
					),
					"std" => "",
					"options" => prohost_array_merge(array(0 => esc_html__('- Select category -', 'prohost')), prohost_get_options_param('list_categories')),
					"type" => "select",
					"multiple" => true,
					"style" => "list"),
		
		"slider_posts" => array(
					"title" => esc_html__('Posts Slider: Number posts or comma separated posts list',  'prohost'),
					"desc" => wp_kses_data( __("How many recent posts display in slider or comma separated list of posts ID (in this case selected category ignored)", 'prohost') ),
					"override" => "category,services_group,page",
					"dependency" => array(
						'show_slider' => array('yes'),
						'slider_engine' => array('swiper')
					),
					"std" => "5",
					"type" => "text"),
		
		"slider_orderby" => array(
					"title" => esc_html__("Posts Slider: Posts order by",  'prohost'),
					"desc" => wp_kses_data( __("Posts in slider ordered by date (default), comments, views, author rating, users rating, random or alphabetically", 'prohost') ),
					"override" => "category,services_group,page",
					"dependency" => array(
						'show_slider' => array('yes'),
						'slider_engine' => array('swiper')
					),
					"std" => "date",
					"options" => prohost_get_options_param('list_sorting'),
					"type" => "select"),
		
		"slider_order" => array(
					"title" => esc_html__("Posts Slider: Posts order", 'prohost'),
					"desc" => wp_kses_data( __('Select the desired ordering method for posts', 'prohost') ),
					"override" => "category,services_group,page",
					"dependency" => array(
						'show_slider' => array('yes'),
						'slider_engine' => array('swiper')
					),
					"std" => "desc",
					"options" => prohost_get_options_param('list_ordering'),
					"size" => "big",
					"type" => "switch"),
					
		"slider_interval" => array(
					"title" => esc_html__("Posts Slider: Slide change interval", 'prohost'),
					"desc" => wp_kses_data( __("Interval (in ms) for slides change in slider", 'prohost') ),
					"override" => "category,services_group,page",
					"dependency" => array(
						'show_slider' => array('yes'),
						'slider_engine' => array('swiper')
					),
					"std" => 7000,
					"min" => 100,
					"step" => 100,
					"type" => "spinner"),
		
		"slider_pagination" => array(
					"title" => esc_html__("Posts Slider: Pagination", 'prohost'),
					"desc" => wp_kses_data( __("Choose pagination style for the slider", 'prohost') ),
					"override" => "category,services_group,page",
					"dependency" => array(
						'show_slider' => array('yes'),
						'slider_engine' => array('swiper')
					),
					"std" => "no",
					"options" => array(
						'no'   => esc_html__('None', 'prohost'),
						'yes'  => esc_html__('Dots', 'prohost'), 
						'over' => esc_html__('Titles', 'prohost')
					),
					"type" => "checklist"),
		
		"slider_infobox" => array(
					"title" => esc_html__("Posts Slider: Show infobox", 'prohost'),
					"desc" => wp_kses_data( __("Do you want to show post's title, reviews rating and description on slides in slider", 'prohost') ),
					"override" => "category,services_group,page",
					"dependency" => array(
						'show_slider' => array('yes'),
						'slider_engine' => array('swiper')
					),
					"std" => "slide",
					"options" => array(
						'no'    => esc_html__('None',  'prohost'),
						'slide' => esc_html__('Slide', 'prohost'), 
						'fixed' => esc_html__('Fixed', 'prohost')
					),
					"type" => "checklist"),
					
		"slider_info_category" => array(
					"title" => esc_html__("Posts Slider: Show post's category", 'prohost'),
					"desc" => wp_kses_data( __("Do you want to show post's category on slides in slider", 'prohost') ),
					"override" => "category,services_group,page",
					"dependency" => array(
						'show_slider' => array('yes'),
						'slider_engine' => array('swiper')
					),
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
					
		"slider_info_reviews" => array(
					"title" => esc_html__("Posts Slider: Show post's reviews rating", 'prohost'),
					"desc" => wp_kses_data( __("Do you want to show post's reviews rating on slides in slider", 'prohost') ),
					"override" => "category,services_group,page",
					"dependency" => array(
						'show_slider' => array('yes'),
						'slider_engine' => array('swiper')
					),
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
					
		"slider_info_descriptions" => array(
					"title" => esc_html__("Posts Slider: Show post's descriptions", 'prohost'),
					"desc" => wp_kses_data( __("How many characters show in the post's description in slider. 0 - no descriptions", 'prohost') ),
					"override" => "category,services_group,page",
					"dependency" => array(
						'show_slider' => array('yes'),
						'slider_engine' => array('swiper')
					),
					"std" => 0,
					"min" => 0,
					"step" => 10,
					"type" => "spinner"),
		
		
		
		
		
		// Customization -> Sidebars
		//-------------------------------------------------
		
		"customization_sidebars" => array( 
					"title" => esc_html__('Sidebars', 'prohost'),
					"icon" => "iconadmin-indent-right",
					"override" => "category,services_group,post,page",
					"type" => "tab"),
		
		"info_sidebars_1" => array( 
					"title" => esc_html__('Custom sidebars', 'prohost'),
					"desc" => wp_kses_data( __('In this section you can create unlimited sidebars. You can fill them with widgets in the menu Appearance - Widgets', 'prohost') ),
					"type" => "info"),
		
		"custom_sidebars" => array(
					"title" => esc_html__('Custom sidebars',  'prohost'),
					"desc" => wp_kses_data( __('Manage custom sidebars. You can use it with each category (page, post) independently',  'prohost') ),
					"std" => "",
					"cloneable" => true,
					"type" => "text"),
		
		"info_sidebars_2" => array(
					"title" => esc_html__('Main sidebar', 'prohost'),
					"desc" => wp_kses_data( __('Show / Hide and select main sidebar', 'prohost') ),
					"override" => "category,services_group,post,page",
					"type" => "info"),
		
		'show_sidebar_main' => array( 
					"title" => esc_html__('Show main sidebar',  'prohost'),
					"desc" => wp_kses_data( __('Select position for the main sidebar or hide it',  'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "right",
					"options" => prohost_get_options_param('list_positions'),
					"dir" => "horizontal",
					"type" => "checklist"),

		"sidebar_main_scheme" => array(
					"title" => esc_html__("Color scheme", 'prohost'),
					"desc" => wp_kses_data( __('Select predefined color scheme for the main sidebar', 'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'show_sidebar_main' => array('left', 'right')
					),
					"std" => "original",
					"dir" => "horizontal",
					"options" => prohost_get_options_param('list_color_schemes'),
					"type" => "checklist"),
		
		"sidebar_main" => array( 
					"title" => esc_html__('Select main sidebar',  'prohost'),
					"desc" => wp_kses_data( __('Select main sidebar content',  'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'show_sidebar_main' => array('left', 'right')
					),
					"std" => "sidebar_main",
					"options" => prohost_get_options_param('list_sidebars'),
					"type" => "select"),
		
		
		
		
		// Customization -> Footer
		//-------------------------------------------------
		
		'customization_footer' => array(
					"title" => esc_html__("Footer", 'prohost'),
					"override" => "category,services_group,post,page",
					"icon" => 'iconadmin-window',
					"type" => "tab"),
		
		
		"info_footer_1" => array(
					"title" => esc_html__("Footer components", 'prohost'),
					"desc" => wp_kses_data( __("Select components of the footer, set style and put the content for the user's footer area", 'prohost') ),
					"override" => "category,services_group,page,post",
					"type" => "info"),
		
		"show_sidebar_footer" => array(
					"title" => esc_html__('Show footer sidebar', 'prohost'),
					"desc" => wp_kses_data( __('Select style for the footer sidebar or hide it', 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),

		"sidebar_footer_scheme" => array(
					"title" => esc_html__("Color scheme", 'prohost'),
					"desc" => wp_kses_data( __('Select predefined color scheme for the footer', 'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'show_sidebar_footer' => array('yes')
					),
					"std" => "original",
					"dir" => "horizontal",
					"options" => prohost_get_options_param('list_color_schemes'),
					"type" => "checklist"),
		
		"sidebar_footer" => array( 
					"title" => esc_html__('Select footer sidebar',  'prohost'),
					"desc" => wp_kses_data( __('Select footer sidebar for the blog page',  'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'show_sidebar_footer' => array('yes')
					),
					"std" => "sidebar_footer",
					"options" => prohost_get_options_param('list_sidebars'),
					"type" => "select"),
		
		"sidebar_footer_columns" => array( 
					"title" => esc_html__('Footer sidebar columns',  'prohost'),
					"desc" => wp_kses_data( __('Select columns number for the footer sidebar',  'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'show_sidebar_footer' => array('yes')
					),
					"std" => 3,
					"min" => 1,
					"max" => 6,
					"type" => "spinner"),
		
		
		"info_footer_2" => array(
					"title" => esc_html__('Testimonials in Footer', 'prohost'),
					"desc" => wp_kses_data( __('Select parameters for Testimonials in the Footer', 'prohost') ),
					"override" => "category,services_group,page,post",
					"type" => "info"),

		"show_testimonials_in_footer" => array(
					"title" => esc_html__('Show Testimonials in footer', 'prohost'),
					"desc" => wp_kses_data( __('Show Testimonials slider in footer. For correct operation of the slider (and shortcode testimonials) you must fill out Testimonials posts on the menu "Testimonials"', 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "no",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),

		"testimonials_scheme" => array(
					"title" => esc_html__("Color scheme", 'prohost'),
					"desc" => wp_kses_data( __('Select predefined color scheme for the testimonials area', 'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'show_testimonials_in_footer' => array('yes')
					),
					"std" => "original",
					"dir" => "horizontal",
					"options" => prohost_get_options_param('list_color_schemes'),
					"type" => "checklist"),

		"testimonials_count" => array( 
					"title" => esc_html__('Testimonials count', 'prohost'),
					"desc" => wp_kses_data( __('Number testimonials to show', 'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'show_testimonials_in_footer' => array('yes')
					),
					"std" => 3,
					"step" => 1,
					"min" => 1,
					"max" => 10,
					"type" => "spinner"),
		
		
		"info_footer_3" => array(
					"title" => esc_html__('Twitter in Footer', 'prohost'),
					"desc" => wp_kses_data( __('Select parameters for Twitter stream in the Footer (you can override it in each category and page)', 'prohost') ),
					"override" => "category,services_group,page,post",
					"type" => "info"),

		"show_twitter_in_footer" => array(
					"title" => esc_html__('Show Twitter in footer', 'prohost'),
					"desc" => wp_kses_data( __('Show Twitter slider in footer. For correct operation of the slider (and shortcode twitter) you must fill out the Twitter API keys on the menu "Appearance - Theme Options - Socials"', 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "no",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),

		"twitter_scheme" => array(
					"title" => esc_html__("Color scheme", 'prohost'),
					"desc" => wp_kses_data( __('Select predefined color scheme for the twitter area', 'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'show_twitter_in_footer' => array('yes')
					),
					"std" => "original",
					"dir" => "horizontal",
					"options" => prohost_get_options_param('list_color_schemes'),
					"type" => "checklist"),

		"twitter_count" => array( 
					"title" => esc_html__('Twitter count', 'prohost'),
					"desc" => wp_kses_data( __('Number twitter to show', 'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'show_twitter_in_footer' => array('yes')
					),
					"std" => 3,
					"step" => 1,
					"min" => 1,
					"max" => 10,
					"type" => "spinner"),


		"info_footer_4" => array(
					"title" => esc_html__('Google map parameters', 'prohost'),
					"desc" => wp_kses_data( __('Select parameters for Google map (you can override it in each category and page)', 'prohost') ),
					"override" => "category,services_group,page,post",
					"type" => "info"),
					
		"show_googlemap" => array(
					"title" => esc_html__('Show Google Map', 'prohost'),
					"desc" => wp_kses_data( __('Do you want to show Google map on each page (post)', 'prohost') ),
					"override" => "category,services_group,page,post",
					"std" => "no",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"googlemap_height" => array(
					"title" => esc_html__("Map height", 'prohost'),
					"desc" => wp_kses_data( __("Map height (default - in pixels, allows any CSS units of measure)", 'prohost') ),
					"override" => "category,services_group,page",
					"dependency" => array(
						'show_googlemap' => array('yes')
					),
					"std" => 400,
					"min" => 100,
					"step" => 10,
					"type" => "spinner"),
		
		"googlemap_address" => array(
					"title" => esc_html__('Address to show on map',  'prohost'),
					"desc" => wp_kses_data( __("Enter address to show on map center", 'prohost') ),
					"override" => "category,services_group,page,post",
					"dependency" => array(
						'show_googlemap' => array('yes')
					),
					"std" => "",
					"type" => "text"),
		
		"googlemap_latlng" => array(
					"title" => esc_html__('Latitude and Longitude to show on map',  'prohost'),
					"desc" => wp_kses_data( __("Enter coordinates (separated by comma) to show on map center (instead of address)", 'prohost') ),
					"override" => "category,services_group,page,post",
					"dependency" => array(
						'show_googlemap' => array('yes')
					),
					"std" => "",
					"type" => "text"),
		
		"googlemap_title" => array(
					"title" => esc_html__('Title to show on map',  'prohost'),
					"desc" => wp_kses_data( __("Enter title to show on map center", 'prohost') ),
					"override" => "category,services_group,page,post",
					"dependency" => array(
						'show_googlemap' => array('yes')
					),
					"std" => "",
					"type" => "text"),
		
		"googlemap_description" => array(
					"title" => esc_html__('Description to show on map',  'prohost'),
					"desc" => wp_kses_data( __("Enter description to show on map center", 'prohost') ),
					"override" => "category,services_group,page,post",
					"dependency" => array(
						'show_googlemap' => array('yes')
					),
					"std" => "",
					"type" => "text"),
		
		"googlemap_zoom" => array(
					"title" => esc_html__('Google map initial zoom',  'prohost'),
					"desc" => wp_kses_data( __("Enter desired initial zoom for Google map", 'prohost') ),
					"override" => "category,services_group,page,post",
					"dependency" => array(
						'show_googlemap' => array('yes')
					),
					"std" => 16,
					"min" => 1,
					"max" => 20,
					"step" => 1,
					"type" => "spinner"),
		
		"googlemap_style" => array(
					"title" => esc_html__('Google map style',  'prohost'),
					"desc" => wp_kses_data( __("Select style to show Google map", 'prohost') ),
					"override" => "category,services_group,page,post",
					"dependency" => array(
						'show_googlemap' => array('yes')
					),
					"std" => 'style1',
					"options" => prohost_get_options_param('list_gmap_styles'),
					"type" => "select"),
		
		"googlemap_marker" => array(
					"title" => esc_html__('Google map marker',  'prohost'),
					"desc" => wp_kses_data( __("Select or upload png-image with Google map marker", 'prohost') ),
					"dependency" => array(
						'show_googlemap' => array('yes')
					),
					"std" => '',
					"type" => "media"),
		
		
		
		"info_footer_5" => array(
					"title" => esc_html__("Contacts area", 'prohost'),
					"desc" => wp_kses_data( __("Show/Hide contacts area in the footer", 'prohost') ),
					"override" => "category,services_group,page,post",
					"type" => "info"),
		
		"show_contacts_in_footer" => array(
					"title" => esc_html__('Show Contacts in footer', 'prohost'),
					"desc" => wp_kses_data( __('Show contact information area in footer: site logo, contact info and large social icons', 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),

		"contacts_scheme" => array(
					"title" => esc_html__("Color scheme", 'prohost'),
					"desc" => wp_kses_data( __('Select predefined color scheme for the contacts area', 'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'show_contacts_in_footer' => array('yes')
					),
					"std" => "original",
					"dir" => "horizontal",
					"options" => prohost_get_options_param('list_color_schemes'),
					"type" => "checklist"),

		'logo_footer' => array(
					"title" => esc_html__('Logo image for footer', 'prohost'),
					"desc" => wp_kses_data( __('Logo image in the footer (in the contacts area)', 'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'show_contacts_in_footer' => array('yes')
					),
					"std" => "",
					"type" => "media"
					),

		'logo_footer_retina' => array(
					"title" => esc_html__('Logo image for footer for Retina', 'prohost'),
					"desc" => wp_kses_data( __('Logo image in the footer (in the contacts area) used on Retina display', 'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'show_contacts_in_footer' => array('yes')
					),
					"std" => "",
					"type" => "media"
					),
		
		'logo_footer_height' => array(
					"title" => esc_html__('Logo height', 'prohost'),
					"desc" => wp_kses_data( __('Height for the logo in the footer area (in the contacts area)', 'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'show_contacts_in_footer' => array('yes')
					),
					"step" => 1,
					"std" => 30,
					"min" => 10,
					"max" => 300,
					"mask" => "?999",
					"type" => "spinner"
					),
		
		
		
		"info_footer_6" => array(
					"title" => esc_html__("Copyright and footer menu", 'prohost'),
					"desc" => wp_kses_data( __("Show/Hide copyright area in the footer", 'prohost') ),
					"override" => "category,services_group,page,post",
					"type" => "info"),

		"show_copyright_in_footer" => array(
					"title" => esc_html__('Show Copyright area in footer', 'prohost'),
					"desc" => wp_kses_data( __('Show area with copyright information, footer menu and small social icons in footer', 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "plain",
					"options" => array(
						'none' => esc_html__('Hide', 'prohost'),
						'text' => esc_html__('Text', 'prohost'),
						'menu' => esc_html__('Text and menu', 'prohost'),
						'socials' => esc_html__('Text and Social icons', 'prohost')
					),
					"type" => "checklist"),

		"copyright_scheme" => array(
					"title" => esc_html__("Color scheme", 'prohost'),
					"desc" => wp_kses_data( __('Select predefined color scheme for the copyright area', 'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'show_copyright_in_footer' => array('text', 'menu', 'socials')
					),
					"std" => "original",
					"dir" => "horizontal",
					"options" => prohost_get_options_param('list_color_schemes'),
					"type" => "checklist"),
		
		"menu_footer" => array( 
					"title" => esc_html__('Select footer menu',  'prohost'),
					"desc" => wp_kses_data( __('Select footer menu for the current page',  'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "default",
					"dependency" => array(
						'show_copyright_in_footer' => array('menu')
					),
					"options" => prohost_get_options_param('list_menus'),
					"type" => "select"),

		"footer_copyright" => array(
					"title" => esc_html__('Footer copyright text',  'prohost'),
					"desc" => wp_kses_data( __("Copyright text to show in footer area (bottom of site)", 'prohost') ),
					"override" => "category,services_group,page,post",
					"dependency" => array(
						'show_copyright_in_footer' => array('text', 'menu', 'socials')
					),
					"allow_html" => true,
					"std" => "ProHost &copy; 2014 All Rights Reserved ",
					"rows" => "10",
					"type" => "editor"),




		// Customization -> Other
		//-------------------------------------------------
		
		'customization_other' => array(
					"title" => esc_html__('Other', 'prohost'),
					"override" => "category,services_group,page,post",
					"icon" => 'iconadmin-cog',
					"type" => "tab"
					),

		'info_other_1' => array(
					"title" => esc_html__('Theme customization other parameters', 'prohost'),
					"desc" => wp_kses_data( __('Animation parameters and responsive layouts for the small screens', 'prohost') ),
					"type" => "info"
					),

		'show_theme_customizer' => array(
					"title" => esc_html__('Show Theme customizer', 'prohost'),
					"desc" => wp_kses_data( __('Do you want to show theme customizer in the right panel? Your website visitors will be able to customise it yourself.', 'prohost') ),
					"std" => "no",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"
					),

		"customizer_demo" => array(
					"title" => esc_html__('Theme customizer panel demo time', 'prohost'),
					"desc" => wp_kses_data( __('Timer for demo mode for the customizer panel (in milliseconds: 1000ms = 1s). If 0 - no demo.', 'prohost') ),
					"dependency" => array(
						'show_theme_customizer' => array('yes')
					),
					"std" => "0",
					"min" => 0,
					"max" => 10000,
					"step" => 500,
					"type" => "spinner"),
		
		'css_animation' => array(
					"title" => esc_html__('Extended CSS animations', 'prohost'),
					"desc" => wp_kses_data( __('Do you want use extended animations effects on your site?', 'prohost') ),
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"
					),
		
		'animation_on_mobile' => array(
					"title" => esc_html__('Allow CSS animations on mobile', 'prohost'),
					"desc" => wp_kses_data( __('Do you allow extended animations effects on mobile devices?', 'prohost') ),
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"
					),

		'remember_visitors_settings' => array(
					"title" => esc_html__("Remember visitor's settings", 'prohost'),
					"desc" => wp_kses_data( __('To remember the settings that were made by the visitor, when navigating to other pages or to limit their effect only within the current page', 'prohost') ),
					"std" => "no",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"
					),
					
		'responsive_layouts' => array(
					"title" => esc_html__('Responsive Layouts', 'prohost'),
					"desc" => wp_kses_data( __('Do you want use responsive layouts on small screen or still use main layout?', 'prohost') ),
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"
					),
		
		'page_preloader' => array(
					"title" => esc_html__('Show page preloader',  'prohost'),
					"desc" => wp_kses_data( __('Do you want show animated page preloader?',  'prohost') ),
					"std" => "",
					"type" => "media"
					),


		'info_other_2' => array(
					"title" => esc_html__('Google fonts parameters', 'prohost'),
					"desc" => wp_kses_data( __('Specify additional parameters, used to load Google fonts', 'prohost') ),
					"type" => "info"
					),
		
		"fonts_subset" => array(
					"title" => esc_html__('Characters subset', 'prohost'),
					"desc" => wp_kses_data( __('Select subset, included into used Google fonts', 'prohost') ),
					"std" => "latin,latin-ext",
					"options" => array(
						'latin' => esc_html__('Latin', 'prohost'),
						'latin-ext' => esc_html__('Latin Extended', 'prohost'),
						'greek' => esc_html__('Greek', 'prohost'),
						'greek-ext' => esc_html__('Greek Extended', 'prohost'),
						'cyrillic' => esc_html__('Cyrillic', 'prohost'),
						'cyrillic-ext' => esc_html__('Cyrillic Extended', 'prohost'),
						'vietnamese' => esc_html__('Vietnamese', 'prohost')
					),
					"size" => "medium",
					"dir" => "vertical",
					"multiple" => true,
					"type" => "checklist"),


		'info_other_3' => array(
					"title" => esc_html__('Additional CSS and HTML/JS code', 'prohost'),
					"desc" => wp_kses_data( __('Put here your custom CSS and JS code', 'prohost') ),
					"override" => "category,services_group,page,post",
					"type" => "info"
					),
					
		'custom_css_html' => array(
					"title" => esc_html__('Use custom CSS/HTML/JS', 'prohost'),
					"desc" => wp_kses_data( __('Do you want use custom HTML/CSS/JS code in your site? For example: custom styles, Google Analitics code, etc.', 'prohost') ),
					"std" => "no",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"
					),
		
		"gtm_code" => array(
					"title" => esc_html__('Google tags manager or Google analitics code',  'prohost'),
					"desc" => wp_kses_data( __('Put here Google Tags Manager (GTM) code from your account: Google analitics, remarketing, etc. This code will be placed after open body tag.',  'prohost') ),
					"dependency" => array(
						'custom_css_html' => array('yes')
					),
					"cols" => 80,
					"rows" => 20,
					"std" => "",
					"allow_html" => true,
					"allow_js" => true,
					"type" => "textarea"),
		
		"gtm_code2" => array(
					"title" => esc_html__('Google remarketing code',  'prohost'),
					"desc" => wp_kses_data( __('Put here Google Remarketing code from your account. This code will be placed before close body tag.',  'prohost') ),
					"dependency" => array(
						'custom_css_html' => array('yes')
					),
					"divider" => false,
					"cols" => 80,
					"rows" => 20,
					"std" => "",
					"allow_html" => true,
					"allow_js" => true,
					"type" => "textarea"),
		
		'custom_code' => array(
					"title" => esc_html__('Your custom HTML/JS code',  'prohost'),
					"desc" => wp_kses_data( __('Put here your invisible html/js code: Google analitics, counters, etc',  'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'custom_css_html' => array('yes')
					),
					"cols" => 80,
					"rows" => 20,
					"std" => "",
					"allow_html" => true,
					"allow_js" => true,
					"type" => "textarea"
					),
		
		'custom_css' => array(
					"title" => esc_html__('Your custom CSS code',  'prohost'),
					"desc" => wp_kses_data( __('Put here your css code to correct main theme styles',  'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'custom_css_html' => array('yes')
					),
					"divider" => false,
					"cols" => 80,
					"rows" => 20,
					"std" => "",
					"type" => "textarea"
					),
		
		
		
		
		
		
		
		
		
		//###############################
		//#### Blog and Single pages #### 
		//###############################
		"partition_blog" => array(
					"title" => esc_html__('Blog &amp; Single', 'prohost'),
					"icon" => "iconadmin-docs",
					"override" => "category,services_group,post,page",
					"type" => "partition"),
		
		
		
		// Blog -> Stream page
		//-------------------------------------------------
		
		'blog_tab_stream' => array(
					"title" => esc_html__('Stream page', 'prohost'),
					"start" => 'blog_tabs',
					"icon" => "iconadmin-docs",
					"override" => "category,services_group,post,page",
					"type" => "tab"),
		
		"info_blog_1" => array(
					"title" => esc_html__('Blog streampage parameters', 'prohost'),
					"desc" => wp_kses_data( __('Select desired blog streampage parameters (you can override it in each category)', 'prohost') ),
					"override" => "category,services_group,post,page",
					"type" => "info"),
		
		"blog_style" => array(
					"title" => esc_html__('Blog style', 'prohost'),
					"desc" => wp_kses_data( __('Select desired blog style', 'prohost') ),
					"override" => "category,services_group,page",
					"std" => "excerpt",
					"options" => prohost_get_options_param('list_blog_styles'),
					"type" => "select"),
		
		"hover_style" => array(
					"title" => esc_html__('Hover style', 'prohost'),
					"desc" => wp_kses_data( __('Select desired hover style (only for Blog style = Portfolio)', 'prohost') ),
					"override" => "category,services_group,page",
					"dependency" => array(
						'blog_style' => array('portfolio','grid','square','colored')
					),
					"std" => "square effect_shift",
					"options" => prohost_get_options_param('list_hovers'),
					"type" => "select"),
		
		"hover_dir" => array(
					"title" => esc_html__('Hover dir', 'prohost'),
					"desc" => wp_kses_data( __('Select hover direction (only for Blog style = Portfolio and Hover style = Circle or Square)', 'prohost') ),
					"override" => "category,services_group,page",
					"dependency" => array(
						'blog_style' => array('portfolio','grid','square','colored'),
						'hover_style' => array('square','circle')
					),
					"std" => "left_to_right",
					"options" => prohost_get_options_param('list_hovers_dir'),
					"type" => "select"),
		
		"article_style" => array(
					"title" => esc_html__('Article style', 'prohost'),
					"desc" => wp_kses_data( __('Select article display method: boxed or stretch', 'prohost') ),
					"override" => "category,services_group,page",
					"std" => "stretch",
					"options" => prohost_get_options_param('list_article_styles'),
					"size" => "medium",
					"type" => "switch"),
		
		"dedicated_location" => array(
					"title" => esc_html__('Dedicated location', 'prohost'),
					"desc" => wp_kses_data( __('Select location for the dedicated content or featured image in the "excerpt" blog style', 'prohost') ),
					"override" => "category,services_group,page,post",
					"dependency" => array(
						'blog_style' => array('excerpt')
					),
					"std" => "center",
					"options" => prohost_get_options_param('list_locations'),
					"type" => "select"),
		
		"show_filters" => array(
					"title" => esc_html__('Show filters', 'prohost'),
					"desc" => wp_kses_data( __('What taxonomy use for filter buttons', 'prohost') ),
					"override" => "category,services_group,page",
					"dependency" => array(
						'blog_style' => array('portfolio','grid','square','colored')
					),
					"std" => "hide",
					"options" => prohost_get_options_param('list_filters'),
					"type" => "checklist"),
		
		"blog_sort" => array(
					"title" => esc_html__('Blog posts sorted by', 'prohost'),
					"desc" => wp_kses_data( __('Select the desired sorting method for posts', 'prohost') ),
					"override" => "category,services_group,page",
					"std" => "date",
					"options" => prohost_get_options_param('list_sorting'),
					"dir" => "vertical",
					"type" => "radio"),
		
		"blog_order" => array(
					"title" => esc_html__('Blog posts order', 'prohost'),
					"desc" => wp_kses_data( __('Select the desired ordering method for posts', 'prohost') ),
					"override" => "category,services_group,page",
					"std" => "desc",
					"options" => prohost_get_options_param('list_ordering'),
					"size" => "big",
					"type" => "switch"),
		
		"posts_per_page" => array(
					"title" => esc_html__('Blog posts per page',  'prohost'),
					"desc" => wp_kses_data( __('How many posts display on blog pages for selected style. If empty or 0 - inherit system wordpress settings',  'prohost') ),
					"override" => "category,services_group,page",
					"std" => "12",
					"mask" => "?99",
					"type" => "text"),
		
		"post_excerpt_maxlength" => array(
					"title" => esc_html__('Excerpt maxlength for streampage',  'prohost'),
					"desc" => wp_kses_data( __('How many characters from post excerpt are display in blog streampage (only for Blog style = Excerpt). 0 - do not trim excerpt.',  'prohost') ),
					"override" => "category,services_group,page",
					"dependency" => array(
						'blog_style' => array('excerpt', 'portfolio', 'grid', 'square', 'related')
					),
					"std" => "250",
					"mask" => "?9999",
					"type" => "text"),
		
		"post_excerpt_maxlength_masonry" => array(
					"title" => esc_html__('Excerpt maxlength for classic and masonry',  'prohost'),
					"desc" => wp_kses_data( __('How many characters from post excerpt are display in blog streampage (only for Blog style = Classic or Masonry). 0 - do not trim excerpt.',  'prohost') ),
					"override" => "category,services_group,page",
					"dependency" => array(
						'blog_style' => array('masonry', 'classic')
					),
					"std" => "150",
					"mask" => "?9999",
					"type" => "text"),
		
		
		
		
		// Blog -> Single page
		//-------------------------------------------------
		
		'blog_tab_single' => array(
					"title" => esc_html__('Single page', 'prohost'),
					"icon" => "iconadmin-doc",
					"override" => "category,services_group,post,page",
					"type" => "tab"),
		
		
		"info_single_1" => array(
					"title" => esc_html__('Single (detail) pages parameters', 'prohost'),
					"desc" => wp_kses_data( __('Select desired parameters for single (detail) pages (you can override it in each category and single post (page))', 'prohost') ),
					"override" => "category,services_group,page,post",
					"type" => "info"),
		
		"single_style" => array(
					"title" => esc_html__('Single page style', 'prohost'),
					"desc" => wp_kses_data( __('Select desired style for single page', 'prohost') ),
					"override" => "category,services_group,page,post",
					"std" => "single-standard",
					"options" => prohost_get_options_param('list_single_styles'),
					"dir" => "horizontal",
					"type" => "radio"),

		"icon" => array(
					"title" => esc_html__('Select post icon', 'prohost'),
					"desc" => wp_kses_data( __('Select icon for output before post/category name in some layouts', 'prohost') ),
					"override" => "services_group,page,post",
					"std" => "",
					"options" => prohost_get_options_param('list_icons'),
					"style" => "select",
					"type" => "icons"
					),

		"alter_thumb_size" => array(
					"title" => esc_html__('Alter thumb size (WxH)',  'prohost'),
					"override" => "page,post",
					"desc" => wp_kses_data( __("Select thumb size for the alternative portfolio layout (number items horizontally x number items vertically)", 'prohost') ),
					"class" => "",
					"std" => "1_1",
					"type" => "radio",
					"options" => prohost_get_options_param('list_alter_sizes')
					),
		
		"show_featured_image" => array(
					"title" => esc_html__('Show featured image before post',  'prohost'),
					"desc" => wp_kses_data( __("Show featured image (if selected) before post content on single pages", 'prohost') ),
					"override" => "category,services_group,page,post",
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"show_post_title" => array(
					"title" => esc_html__('Show post title', 'prohost'),
					"desc" => wp_kses_data( __('Show area with post title on single pages', 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"show_post_title_on_quotes" => array(
					"title" => esc_html__('Show post title on links, chat, quote, status', 'prohost'),
					"desc" => wp_kses_data( __('Show area with post title on single and blog pages in specific post formats: links, chat, quote, status', 'prohost') ),
					"override" => "category,services_group,page",
					"std" => "no",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"show_post_info" => array(
					"title" => esc_html__('Show post info', 'prohost'),
					"desc" => wp_kses_data( __('Show area with post info on single pages', 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"show_text_before_readmore" => array(
					"title" => esc_html__('Show text before "Read more" tag', 'prohost'),
					"desc" => wp_kses_data( __('Show text before "Read more" tag on single pages', 'prohost') ),
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
					
		"show_post_author" => array(
					"title" => esc_html__('Show post author details',  'prohost'),
					"desc" => wp_kses_data( __("Show post author information block on single post page", 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"show_post_tags" => array(
					"title" => esc_html__('Show post tags',  'prohost'),
					"desc" => wp_kses_data( __("Show tags block on single post page", 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"show_post_related" => array(
					"title" => esc_html__('Show related posts',  'prohost'),
					"desc" => wp_kses_data( __("Show related posts block on single post page", 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "no",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),

		"post_related_count" => array(
					"title" => esc_html__('Related posts number',  'prohost'),
					"desc" => wp_kses_data( __("How many related posts showed on single post page", 'prohost') ),
					"dependency" => array(
						'show_post_related' => array('yes')
					),
					"override" => "category,services_group,post,page",
					"std" => "3",
					"step" => 1,
					"min" => 2,
					"max" => 8,
					"type" => "spinner"),

		"post_related_columns" => array(
					"title" => esc_html__('Related posts columns',  'prohost'),
					"desc" => wp_kses_data( __("How many columns used to show related posts on single post page. 1 - use scrolling to show all related posts", 'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'show_post_related' => array('yes')
					),
					"std" => "3",
					"step" => 1,
					"min" => 1,
					"max" => 4,
					"type" => "spinner"),
		
		"post_related_sort" => array(
					"title" => esc_html__('Related posts sorted by', 'prohost'),
					"desc" => wp_kses_data( __('Select the desired sorting method for related posts', 'prohost') ),
					"dependency" => array(
						'show_post_related' => array('yes')
					),
					"std" => "date",
					"options" => prohost_get_options_param('list_sorting'),
					"type" => "select"),
		
		"post_related_order" => array(
					"title" => esc_html__('Related posts order', 'prohost'),
					"desc" => wp_kses_data( __('Select the desired ordering method for related posts', 'prohost') ),
					"dependency" => array(
						'show_post_related' => array('yes')
					),
					"std" => "desc",
					"options" => prohost_get_options_param('list_ordering'),
					"size" => "big",
					"type" => "switch"),
		
		"show_post_comments" => array(
					"title" => esc_html__('Show comments',  'prohost'),
					"desc" => wp_kses_data( __("Show comments block on single post page", 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		
		
		// Blog -> Other parameters
		//-------------------------------------------------
		
		'blog_tab_other' => array(
					"title" => esc_html__('Other parameters', 'prohost'),
					"icon" => "iconadmin-newspaper",
					"override" => "category,services_group,page",
					"type" => "tab"),
		
		"info_blog_other_1" => array(
					"title" => esc_html__('Other Blog parameters', 'prohost'),
					"desc" => wp_kses_data( __('Select excluded categories, substitute parameters, etc.', 'prohost') ),
					"type" => "info"),
		
		"exclude_cats" => array(
					"title" => esc_html__('Exclude categories', 'prohost'),
					"desc" => wp_kses_data( __('Select categories, which posts are exclude from blog page', 'prohost') ),
					"std" => "",
					"options" => prohost_get_options_param('list_categories'),
					"multiple" => true,
					"style" => "list",
					"type" => "select"),
		
		"blog_pagination" => array(
					"title" => esc_html__('Blog pagination', 'prohost'),
					"desc" => wp_kses_data( __('Select type of the pagination on blog streampages', 'prohost') ),
					"std" => "pages",
					"override" => "category,services_group,page",
					"options" => array(
						'pages'    => esc_html__('Standard page numbers', 'prohost'),
						'slider'   => esc_html__('Slider with page numbers', 'prohost'),
						'viewmore' => esc_html__('"View more" button', 'prohost'),
						'infinite' => esc_html__('Infinite scroll', 'prohost')
					),
					"dir" => "vertical",
					"type" => "radio"),
		
		"blog_counters" => array(
					"title" => esc_html__('Blog counters', 'prohost'),
					"desc" => wp_kses_data( __('Select counters, displayed near the post title', 'prohost') ),
					"override" => "category,services_group,page",
					"std" => "views",
					"options" => prohost_get_options_param('list_blog_counters'),
					"dir" => "vertical",
					"multiple" => true,
					"type" => "checklist"),
		
		"close_category" => array(
					"title" => esc_html__("Post's category announce", 'prohost'),
					"desc" => wp_kses_data( __('What category display in announce block (over posts thumb) - original or nearest parental', 'prohost') ),
					"override" => "category,services_group,page",
					"std" => "parental",
					"options" => array(
						'parental' => esc_html__('Nearest parental category', 'prohost'),
						'original' => esc_html__("Original post's category", 'prohost')
					),
					"dir" => "vertical",
					"type" => "radio"),
		
		"show_date_after" => array(
					"title" => esc_html__('Show post date after', 'prohost'),
					"desc" => wp_kses_data( __('Show post date after N days (before - show post age)', 'prohost') ),
					"override" => "category,services_group,page",
					"std" => "30",
					"mask" => "?99",
					"type" => "text"),
		
		
		
		
		
		//###############################
		//#### Reviews               #### 
		//###############################
		"partition_reviews" => array(
					"title" => esc_html__('Reviews', 'prohost'),
					"icon" => "iconadmin-newspaper",
					"override" => "category,services_group,services_group",
					"type" => "partition"),
		
		"info_reviews_1" => array(
					"title" => esc_html__('Reviews criterias', 'prohost'),
					"desc" => wp_kses_data( __('Set up list of reviews criterias. You can override it in any category.', 'prohost') ),
					"override" => "category,services_group,services_group",
					"type" => "info"),
		
		"show_reviews" => array(
					"title" => esc_html__('Show reviews block',  'prohost'),
					"desc" => wp_kses_data( __("Show reviews block on single post page and average reviews rating after post's title in stream pages", 'prohost') ),
					"override" => "category,services_group,services_group",
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"reviews_max_level" => array(
					"title" => esc_html__('Max reviews level',  'prohost'),
					"desc" => wp_kses_data( __("Maximum level for reviews marks", 'prohost') ),
					"std" => "5",
					"options" => array(
						'5'=>esc_html__('5 stars', 'prohost'), 
						'10'=>esc_html__('10 stars', 'prohost'), 
						'100'=>esc_html__('100%', 'prohost')
					),
					"type" => "radio",
					),
		
		"reviews_style" => array(
					"title" => esc_html__('Show rating as',  'prohost'),
					"desc" => wp_kses_data( __("Show rating marks as text or as stars/progress bars.", 'prohost') ),
					"std" => "stars",
					"options" => array(
						'text' => esc_html__('As text (for example: 7.5 / 10)', 'prohost'), 
						'stars' => esc_html__('As stars or bars', 'prohost')
					),
					"dir" => "vertical",
					"type" => "radio"),
		
		"reviews_criterias_levels" => array(
					"title" => esc_html__('Reviews Criterias Levels', 'prohost'),
					"desc" => wp_kses_data( __('Words to mark criterials levels. Just write the word and press "Enter". Also you can arrange words.', 'prohost') ),
					"std" => esc_html__("bad,poor,normal,good,great", 'prohost'),
					"type" => "tags"),
		
		"reviews_first" => array(
					"title" => esc_html__('Show first reviews',  'prohost'),
					"desc" => wp_kses_data( __("What reviews will be displayed first: by author or by visitors. Also this type of reviews will display under post's title.", 'prohost') ),
					"std" => "author",
					"options" => array(
						'author' => esc_html__('By author', 'prohost'),
						'users' => esc_html__('By visitors', 'prohost')
						),
					"dir" => "horizontal",
					"type" => "radio"),
		
		"reviews_second" => array(
					"title" => esc_html__('Hide second reviews',  'prohost'),
					"desc" => wp_kses_data( __("Do you want hide second reviews tab in widgets and single posts?", 'prohost') ),
					"std" => "show",
					"options" => prohost_get_options_param('list_show_hide'),
					"size" => "medium",
					"type" => "switch"),
		
		"reviews_can_vote" => array(
					"title" => esc_html__('What visitors can vote',  'prohost'),
					"desc" => wp_kses_data( __("What visitors can vote: all or only registered", 'prohost') ),
					"std" => "all",
					"options" => array(
						'all'=>esc_html__('All visitors', 'prohost'), 
						'registered'=>esc_html__('Only registered', 'prohost')
					),
					"dir" => "horizontal",
					"type" => "radio"),
		
		"reviews_criterias" => array(
					"title" => esc_html__('Reviews criterias',  'prohost'),
					"desc" => wp_kses_data( __('Add default reviews criterias.',  'prohost') ),
					"override" => "category,services_group,services_group",
					"std" => "",
					"cloneable" => true,
					"type" => "text"),

		// Don't remove this parameter - it used in admin for store marks
		"reviews_marks" => array(
					"std" => "",
					"type" => "hidden"),
		





		//###############################
		//#### Media                #### 
		//###############################
		"partition_media" => array(
					"title" => esc_html__('Media', 'prohost'),
					"icon" => "iconadmin-picture",
					"override" => "category,services_group,post,page",
					"type" => "partition"),
		
		"info_media_1" => array(
					"title" => esc_html__('Media settings', 'prohost'),
					"desc" => wp_kses_data( __('Set up parameters to show images, galleries, audio and video posts', 'prohost') ),
					"override" => "category,services_group,services_group",
					"type" => "info"),
					
		"retina_ready" => array(
					"title" => esc_html__('Image dimensions', 'prohost'),
					"desc" => wp_kses_data( __('What dimensions use for uploaded image: Original or "Retina ready" (twice enlarged)', 'prohost') ),
					"std" => "1",
					"size" => "medium",
					"options" => array(
						"1" => esc_html__("Original", 'prohost'), 
						"2" => esc_html__("Retina", 'prohost')
					),
					"type" => "switch"),
		
		"images_quality" => array(
					"title" => esc_html__('Quality for cropped images', 'prohost'),
					"desc" => wp_kses_data( __('Quality (1-100) to save cropped images', 'prohost') ),
					"std" => "70",
					"min" => 1,
					"max" => 100,
					"type" => "spinner"),
		
		"substitute_gallery" => array(
					"title" => esc_html__('Substitute standard Wordpress gallery', 'prohost'),
					"desc" => wp_kses_data( __('Substitute standard Wordpress gallery with our slider on the single pages', 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"gallery_instead_image" => array(
					"title" => esc_html__('Show gallery instead featured image', 'prohost'),
					"desc" => wp_kses_data( __('Show slider with gallery instead featured image on blog streampage and in the related posts section for the gallery posts', 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"gallery_max_slides" => array(
					"title" => esc_html__('Max images number in the slider', 'prohost'),
					"desc" => wp_kses_data( __('Maximum images number from gallery into slider', 'prohost') ),
					"override" => "category,services_group,post,page",
					"dependency" => array(
						'gallery_instead_image' => array('yes')
					),
					"std" => "5",
					"min" => 2,
					"max" => 10,
					"type" => "spinner"),
		
		"popup_engine" => array(
					"title" => esc_html__('Popup engine to zoom images', 'prohost'),
					"desc" => wp_kses_data( __('Select engine to show popup windows with images and galleries', 'prohost') ),
					"std" => "pretty",
					"options" => prohost_get_options_param('list_popups'),
					"type" => "select"),
		
		"substitute_audio" => array(
					"title" => esc_html__('Substitute audio tags', 'prohost'),
					"desc" => wp_kses_data( __('Substitute audio tag with source from soundcloud to embed player', 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"substitute_video" => array(
					"title" => esc_html__('Substitute video tags', 'prohost'),
					"desc" => wp_kses_data( __('Substitute video tags with embed players or leave video tags unchanged (if you use third party plugins for the video tags)', 'prohost') ),
					"override" => "category,services_group,post,page",
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"use_mediaelement" => array(
					"title" => esc_html__('Use Media Element script for audio and video tags', 'prohost'),
					"desc" => wp_kses_data( __('Do you want use the Media Element script for all audio and video tags on your site or leave standard HTML5 behaviour?', 'prohost') ),
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		
		
		
		//###############################
		//#### Socials               #### 
		//###############################
		"partition_socials" => array(
					"title" => esc_html__('Socials', 'prohost'),
					"icon" => "iconadmin-users",
					"override" => "category,services_group,page",
					"type" => "partition"),
		
		"info_socials_1" => array(
					"title" => esc_html__('Social networks', 'prohost'),
					"desc" => wp_kses_data( __("Social networks list for site footer and Social widget", 'prohost') ),
					"type" => "info"),
		
		"social_icons" => array(
					"title" => esc_html__('Social networks',  'prohost'),
					"desc" => wp_kses_data( __('Select icon and write URL to your profile in desired social networks.',  'prohost') ),
					"std" => array(array('url'=>'', 'icon'=>'')),
					"cloneable" => true,
					"size" => "small",
					"style" => $socials_type,
					"options" => $socials_type=='images' ? prohost_get_options_param('list_socials') : prohost_get_options_param('list_icons'),
					"type" => "socials"),
		
		"info_socials_2" => array(
					"title" => esc_html__('Share buttons', 'prohost'),
					"desc" => wp_kses_data( __("Add button's code for each social share network.<br>
					In share url you can use next macro:<br>
					<b>{url}</b> - share post (page) URL,<br>
					<b>{title}</b> - post title,<br>
					<b>{image}</b> - post image,<br>
					<b>{descr}</b> - post description (if supported)<br>
					For example:<br>
					<b>Facebook</b> share string: <em>http://www.facebook.com/sharer.php?u={link}&amp;t={title}</em><br>
					<b>Delicious</b> share string: <em>http://delicious.com/save?url={link}&amp;title={title}&amp;note={descr}</em>", 'prohost') ),
					"override" => "category,services_group,page",
					"type" => "info"),
		
		"show_share" => array(
					"title" => esc_html__('Show social share buttons',  'prohost'),
					"desc" => wp_kses_data( __("Show social share buttons block", 'prohost') ),
					"override" => "category,services_group,page",
					"std" => "horizontal",
					"options" => array(
						'hide'		=> esc_html__('Hide', 'prohost'),
						'vertical'	=> esc_html__('Vertical', 'prohost'),
						'horizontal'=> esc_html__('Horizontal', 'prohost')
					),
					"type" => "checklist"),

		"show_share_counters" => array(
					"title" => esc_html__('Show share counters',  'prohost'),
					"desc" => wp_kses_data( __("Show share counters after social buttons", 'prohost') ),
					"override" => "category,services_group,page",
					"dependency" => array(
						'show_share' => array('vertical', 'horizontal')
					),
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),

		"share_caption" => array(
					"title" => esc_html__('Share block caption',  'prohost'),
					"desc" => wp_kses_data( __('Caption for the block with social share buttons',  'prohost') ),
					"override" => "category,services_group,page",
					"dependency" => array(
						'show_share' => array('vertical', 'horizontal')
					),
					"std" => esc_html__('Share:', 'prohost'),
					"type" => "text"),
		
		"share_buttons" => array(
					"title" => esc_html__('Share buttons',  'prohost'),
					"desc" => wp_kses_data( __('Select icon and write share URL for desired social networks.<br><b>Important!</b> If you leave text field empty - internal theme link will be used (if present).',  'prohost') ),
					"dependency" => array(
						'show_share' => array('vertical', 'horizontal')
					),
					"std" => array(array('url'=>'', 'icon'=>'')),
					"cloneable" => true,
					"size" => "small",
					"style" => $socials_type,
					"options" => $socials_type=='images' ? prohost_get_options_param('list_socials') : prohost_get_options_param('list_icons'),
					"type" => "socials"),
		
		
		"info_socials_3" => array(
					"title" => esc_html__('Twitter API keys', 'prohost'),
					"desc" => wp_kses_data( __("Put to this section Twitter API 1.1 keys.<br>You can take them after registration your application in <strong>https://apps.twitter.com/</strong>", 'prohost') ),
					"type" => "info"),
		
		"twitter_username" => array(
					"title" => esc_html__('Twitter username',  'prohost'),
					"desc" => wp_kses_data( __('Your login (username) in Twitter',  'prohost') ),
					"divider" => false,
					"std" => "",
					"type" => "text"),
		
		"twitter_consumer_key" => array(
					"title" => esc_html__('Consumer Key',  'prohost'),
					"desc" => wp_kses_data( __('Twitter API Consumer key',  'prohost') ),
					"divider" => false,
					"std" => "",
					"type" => "text"),
		
		"twitter_consumer_secret" => array(
					"title" => esc_html__('Consumer Secret',  'prohost'),
					"desc" => wp_kses_data( __('Twitter API Consumer secret',  'prohost') ),
					"divider" => false,
					"std" => "",
					"type" => "text"),
		
		"twitter_token_key" => array(
					"title" => esc_html__('Token Key',  'prohost'),
					"desc" => wp_kses_data( __('Twitter API Token key',  'prohost') ),
					"divider" => false,
					"std" => "",
					"type" => "text"),
		
		"twitter_token_secret" => array(
					"title" => esc_html__('Token Secret',  'prohost'),
					"desc" => wp_kses_data( __('Twitter API Token secret',  'prohost') ),
					"divider" => false,
					"std" => "",
					"type" => "text"),
		
		
		
		
		
		//###############################
		//#### Contact info          #### 
		//###############################
		"partition_contacts" => array(
					"title" => esc_html__('Contact info', 'prohost'),
					"icon" => "iconadmin-mail",
					"type" => "partition"),
		
		"info_contact_1" => array(
					"title" => esc_html__('Contact information', 'prohost'),
					"desc" => wp_kses_data( __('Company address, phones and e-mail', 'prohost') ),
					"type" => "info"),
		
		"contact_info" => array(
					"title" => esc_html__('Contacts in the header', 'prohost'),
					"desc" => wp_kses_data( __('String with contact info in the left side of the site header', 'prohost') ),
					"std" => "",
					"before" => array('icon'=>'iconadmin-home'),
					"allow_html" => true,
					"type" => "text"),
		
		"contact_open_hours" => array(
					"title" => esc_html__('Open hours in the header', 'prohost'),
					"desc" => wp_kses_data( __('String with open hours in the site header', 'prohost') ),
					"std" => "",
					"before" => array('icon'=>'iconadmin-clock'),
					"allow_html" => true,
					"type" => "text"),
		
		"contact_email" => array(
					"title" => esc_html__('Contact form email', 'prohost'),
					"desc" => wp_kses_data( __('E-mail for send contact form and user registration data', 'prohost') ),
					"std" => "",
					"before" => array('icon'=>'iconadmin-mail'),
					"type" => "text"),
		
		"contact_address_1" => array(
					"title" => esc_html__('Company address (part 1)', 'prohost'),
					"desc" => wp_kses_data( __('Company country, post code and city', 'prohost') ),
					"std" => "",
					"before" => array('icon'=>'iconadmin-home'),
					"type" => "text"),
		
		"contact_address_2" => array(
					"title" => esc_html__('Company address (part 2)', 'prohost'),
					"desc" => wp_kses_data( __('Street and house number', 'prohost') ),
					"std" => "",
					"before" => array('icon'=>'iconadmin-home'),
					"type" => "text"),
		
		"text_before_phone" => array(
					"title" => esc_html__('Text before Phone', 'prohost'),
					"desc" => wp_kses_data( __('Add text to your phone (uses just header = Regular)', 'prohost') ),
					"std" => "",
					"before" => array('icon'=>'iconadmin-phone'),
					"allow_html" => true,
					"type" => "text"),

		"contact_phone" => array(
					"title" => esc_html__('Phone', 'prohost'),
					"desc" => wp_kses_data( __('Phone number', 'prohost') ),
					"std" => "",
					"before" => array('icon'=>'iconadmin-phone'),
					"allow_html" => true,
					"type" => "text"),
		
		"contact_fax" => array(
					"title" => esc_html__('Fax', 'prohost'),
					"desc" => wp_kses_data( __('Fax number', 'prohost') ),
					"std" => "",
					"before" => array('icon'=>'iconadmin-phone'),
					"allow_html" => true,
					"type" => "text"),
		
		"info_contact_2" => array(
					"title" => esc_html__('Contact and Comments form', 'prohost'),
					"desc" => wp_kses_data( __('Maximum length of the messages in the contact form shortcode and in the comments form', 'prohost') ),
					"type" => "info"),
		
		"message_maxlength_contacts" => array(
					"title" => esc_html__('Contact form message', 'prohost'),
					"desc" => wp_kses_data( __("Message's maxlength in the contact form shortcode", 'prohost') ),
					"std" => "1000",
					"min" => 0,
					"max" => 10000,
					"step" => 100,
					"type" => "spinner"),
		
		"message_maxlength_comments" => array(
					"title" => esc_html__('Comments form message', 'prohost'),
					"desc" => wp_kses_data( __("Message's maxlength in the comments form", 'prohost') ),
					"std" => "1000",
					"min" => 0,
					"max" => 10000,
					"step" => 100,
					"type" => "spinner"),
		
		"info_contact_3" => array(
					"title" => esc_html__('Default mail function', 'prohost'),
					"desc" => wp_kses_data( __('What function you want to use for sending mail: the built-in Wordpress wp_mail() or standard PHP mail() function? Attention! Some plugins may not work with one of them and you always have the ability to switch to alternative.', 'prohost') ),
					"type" => "info"),
		
		"mail_function" => array(
					"title" => esc_html__("Mail function", 'prohost'),
					"desc" => wp_kses_data( __("What function you want to use for sending mail?", 'prohost') ),
					"std" => "wp_mail",
					"size" => "medium",
					"options" => array(
						'wp_mail' => esc_html__('WP mail', 'prohost'),
						'mail' => esc_html__('PHP mail', 'prohost')
					),
					"type" => "switch"),
		
		
		
		
		
		
		
		//###############################
		//#### Search parameters     #### 
		//###############################
		"partition_search" => array(
					"title" => esc_html__('Search', 'prohost'),
					"icon" => "iconadmin-search",
					"type" => "partition"),
		
		"info_search_1" => array(
					"title" => esc_html__('Search parameters', 'prohost'),
					"desc" => wp_kses_data( __('Enable/disable AJAX search and output settings for it', 'prohost') ),
					"type" => "info"),
		
		"show_search" => array(
					"title" => esc_html__('Show search field', 'prohost'),
					"desc" => wp_kses_data( __('Show search field in the top area and side menus', 'prohost') ),
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"use_ajax_search" => array(
					"title" => esc_html__('Enable AJAX search', 'prohost'),
					"desc" => wp_kses_data( __('Use incremental AJAX search for the search field in top of page', 'prohost') ),
					"dependency" => array(
						'show_search' => array('yes')
					),
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"ajax_search_min_length" => array(
					"title" => esc_html__('Min search string length',  'prohost'),
					"desc" => wp_kses_data( __('The minimum length of the search string',  'prohost') ),
					"dependency" => array(
						'show_search' => array('yes'),
						'use_ajax_search' => array('yes')
					),
					"std" => 4,
					"min" => 3,
					"type" => "spinner"),
		
		"ajax_search_delay" => array(
					"title" => esc_html__('Delay before search (in ms)',  'prohost'),
					"desc" => wp_kses_data( __('How much time (in milliseconds, 1000 ms = 1 second) must pass after the last character before the start search',  'prohost') ),
					"dependency" => array(
						'show_search' => array('yes'),
						'use_ajax_search' => array('yes')
					),
					"std" => 500,
					"min" => 300,
					"max" => 1000,
					"step" => 100,
					"type" => "spinner"),
		
		"ajax_search_types" => array(
					"title" => esc_html__('Search area', 'prohost'),
					"desc" => wp_kses_data( __('Select post types, what will be include in search results. If not selected - use all types.', 'prohost') ),
					"dependency" => array(
						'show_search' => array('yes'),
						'use_ajax_search' => array('yes')
					),
					"std" => "",
					"options" => prohost_get_options_param('list_posts_types'),
					"multiple" => true,
					"style" => "list",
					"type" => "select"),
		
		"ajax_search_posts_count" => array(
					"title" => esc_html__('Posts number in output',  'prohost'),
					"dependency" => array(
						'show_search' => array('yes'),
						'use_ajax_search' => array('yes')
					),
					"desc" => wp_kses_data( __('Number of the posts to show in search results',  'prohost') ),
					"std" => 5,
					"min" => 1,
					"max" => 10,
					"type" => "spinner"),
		
		"ajax_search_posts_image" => array(
					"title" => esc_html__("Show post's image", 'prohost'),
					"dependency" => array(
						'show_search' => array('yes'),
						'use_ajax_search' => array('yes')
					),
					"desc" => wp_kses_data( __("Show post's thumbnail in the search results", 'prohost') ),
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"ajax_search_posts_date" => array(
					"title" => esc_html__("Show post's date", 'prohost'),
					"dependency" => array(
						'show_search' => array('yes'),
						'use_ajax_search' => array('yes')
					),
					"desc" => wp_kses_data( __("Show post's publish date in the search results", 'prohost') ),
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"ajax_search_posts_author" => array(
					"title" => esc_html__("Show post's author", 'prohost'),
					"dependency" => array(
						'show_search' => array('yes'),
						'use_ajax_search' => array('yes')
					),
					"desc" => wp_kses_data( __("Show post's author in the search results", 'prohost') ),
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"ajax_search_posts_counters" => array(
					"title" => esc_html__("Show post's counters", 'prohost'),
					"dependency" => array(
						'show_search' => array('yes'),
						'use_ajax_search' => array('yes')
					),
					"desc" => wp_kses_data( __("Show post's counters (views, comments, likes) in the search results", 'prohost') ),
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		
		
		
		
		//###############################
		//#### Service               #### 
		//###############################
		
		"partition_service" => array(
					"title" => esc_html__('Service', 'prohost'),
					"icon" => "iconadmin-wrench",
					"type" => "partition"),
		
		"info_service_1" => array(
					"title" => esc_html__('Theme functionality', 'prohost'),
					"desc" => wp_kses_data( __('Basic theme functionality settings', 'prohost') ),
					"type" => "info"),
		
		"notify_about_new_registration" => array(
					"title" => esc_html__('Notify about new registration', 'prohost'),
					"desc" => wp_kses_data( __('Send E-mail with new registration data to the contact email or to site admin e-mail (if contact email is empty)', 'prohost') ),
					"divider" => false,
					"std" => "no",
					"options" => array(
						'no'    => esc_html__('No', 'prohost'),
						'both'  => esc_html__('Both', 'prohost'),
						'admin' => esc_html__('Admin', 'prohost'),
						'user'  => esc_html__('User', 'prohost')
					),
					"dir" => "horizontal",
					"type" => "checklist"),
		
		"use_ajax_views_counter" => array(
					"title" => esc_html__('Use AJAX post views counter', 'prohost'),
					"desc" => wp_kses_data( __('Use javascript for post views count (if site work under the caching plugin) or increment views count in single page template', 'prohost') ),
					"std" => "no",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"allow_editor" => array(
					"title" => esc_html__('Frontend editor',  'prohost'),
					"desc" => wp_kses_data( __("Allow authors to edit their posts in frontend area)", 'prohost') ),
					"std" => "no",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),

		"admin_add_filters" => array(
					"title" => esc_html__('Additional filters in the admin panel', 'prohost'),
					"desc" => wp_kses_data( __('Show additional filters (on post formats, tags and categories) in admin panel page "Posts". <br>Attention! If you have more than 2.000-3.000 posts, enabling this option may cause slow load of the "Posts" page! If you encounter such slow down, simply open Appearance - Theme Options - Service and set "No" for this option.', 'prohost') ),
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),

		"show_overriden_taxonomies" => array(
					"title" => esc_html__('Show overriden options for taxonomies', 'prohost'),
					"desc" => wp_kses_data( __('Show extra column in categories list, where changed (overriden) theme options are displayed.', 'prohost') ),
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),

		"show_overriden_posts" => array(
					"title" => esc_html__('Show overriden options for posts and pages', 'prohost'),
					"desc" => wp_kses_data( __('Show extra column in posts and pages list, where changed (overriden) theme options are displayed.', 'prohost') ),
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"admin_dummy_data" => array(
					"title" => esc_html__('Enable Dummy Data Installer', 'prohost'),
					"desc" => wp_kses_data( __('Show "Install Dummy Data" in the menu "Appearance". <b>Attention!</b> When you install dummy data all content of your site will be replaced!', 'prohost') ),
					"std" => "yes",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),

		"admin_dummy_timeout" => array(
					"title" => esc_html__('Dummy Data Installer Timeout',  'prohost'),
					"desc" => wp_kses_data( __('Web-servers set the time limit for the execution of php-scripts. By default, this is 30 sec. Therefore, the import process will be split into parts. Upon completion of each part - the import will resume automatically! The import process will try to increase this limit to the time, specified in this field.',  'prohost') ),
					"std" => 120,
					"min" => 30,
					"max" => 1800,
					"type" => "spinner"),
		
		"admin_emailer" => array(
					"title" => esc_html__('Enable Emailer in the admin panel', 'prohost'),
					"desc" => wp_kses_data( __('Allow to use ProHost Emailer for mass-volume e-mail distribution and management of mailing lists in "Appearance - Emailer"', 'prohost') ),
					"std" => "no",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),

		"admin_po_composer" => array(
					"title" => esc_html__('Enable PO Composer in the admin panel', 'prohost'),
					"desc" => wp_kses_data( __('Allow to use "PO Composer" for edit language files in this theme (in the "Appearance - PO Composer")', 'prohost') ),
					"std" => "no",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"debug_mode" => array(
					"title" => esc_html__('Debug mode', 'prohost'),
					"desc" => wp_kses_data( __('In debug mode we are using unpacked scripts and styles, else - using minified scripts and styles (if present). <b>Attention!</b> If you have modified the source code in the js or css files, regardless of this option will be used latest (modified) version stylesheets and scripts. You can re-create minified versions of files using on-line services or utilities', 'prohost') ),
					"std" => "no",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),

            "info_service_3" => array(
                "title" => esc_html__('API Keys', 'prohost'),
                "desc" => wp_kses_data( __('API Keys for some Web services', 'prohost') ),
                "type" => "info"),
            'api_google' => array(
                "title" => esc_html__('Google API Key', 'prohost'),
                "desc" => wp_kses_data( __("Insert Google API Key for browsers into the field above to generate Google Maps", 'prohost') ),
                "std" => "",
                "type" => "text"),
		
		"info_service_2" => array(
					"title" => esc_html__('Wordpress cache', 'prohost'),
					"desc" => wp_kses_data( __('For example, it recommended after activating the WPML plugin - in the cache are incorrect data about the structure of categories and your site may display "white screen". After clearing the cache usually the performance of the site is restored.', 'prohost') ),
					"type" => "info"),
		
		"use_menu_cache" => array(
					"title" => esc_html__('Use menu cache', 'prohost'),
					"desc" => wp_kses_data( __('Use cache for any menu (increase theme speed, decrease queries number). Attention! Please, clear cache after change permalink settings!', 'prohost') ),
					"std" => "no",
					"options" => prohost_get_options_param('list_yes_no'),
					"type" => "switch"),
		
		"clear_cache" => array(
					"title" => esc_html__('Clear cache', 'prohost'),
					"desc" => wp_kses_data( __('Clear Wordpress cache data', 'prohost') ),
					"divider" => false,
					"icon" => "iconadmin-trash",
					"action" => "clear_cache",
					"type" => "button")
		));



		
		
		
		//###############################################
		//#### Hidden fields (for internal use only) #### 
		//###############################################
		/*
		prohost_storage_set_array('options', "custom_stylesheet_file", array(
			"title" => esc_html__('Custom stylesheet file', 'prohost'),
			"desc" => wp_kses_data( __('Path to the custom stylesheet (stored in the uploads folder)', 'prohost') ),
			"std" => "",
			"type" => "hidden"
			)
		);
		
		prohost_storage_set_array('options', "custom_stylesheet_url", array(
			"title" => esc_html__('Custom stylesheet url', 'prohost'),
			"desc" => wp_kses_data( __('URL to the custom stylesheet (stored in the uploads folder)', 'prohost') ),
			"std" => "",
			"type" => "hidden"
			)
		);
		*/

	}
}


// Update all temporary vars (start with $prohost_) in the Theme Options with actual lists
if ( !function_exists( 'prohost_options_settings_theme_setup2' ) ) {
	add_action( 'prohost_action_after_init_theme', 'prohost_options_settings_theme_setup2', 1 );
	function prohost_options_settings_theme_setup2() {
		if (prohost_options_is_used()) {
			// Replace arrays with actual parameters
			$lists = array();
			$tmp = prohost_storage_get('options');
			if (is_array($tmp) && count($tmp) > 0) {
				$prefix = '$prohost_';
				$prefix_len = prohost_strlen($prefix);
				foreach ($tmp as $k=>$v) {
					if (isset($v['options']) && is_array($v['options']) && count($v['options']) > 0) {
						foreach ($v['options'] as $k1=>$v1) {
							if (prohost_substr($k1, 0, $prefix_len) == $prefix || prohost_substr($v1, 0, $prefix_len) == $prefix) {
								$list_func = prohost_substr(prohost_substr($k1, 0, $prefix_len) == $prefix ? $k1 : $v1, 1);
								unset($tmp[$k]['options'][$k1]);
								if (isset($lists[$list_func]))
									$tmp[$k]['options'] = prohost_array_merge($tmp[$k]['options'], $lists[$list_func]);
								else {
									if (function_exists($list_func)) {
										$tmp[$k]['options'] = $lists[$list_func] = prohost_array_merge($tmp[$k]['options'], $list_func == 'prohost_get_list_menus' ? $list_func(true) : $list_func());
								   	} else
								   		dfl(sprintf(esc_html__('Wrong function name %s in the theme options array', 'prohost'), $list_func));
								}
							}
						}
					}
				}
				prohost_storage_set('options', $tmp);
			}
		}
	}
}

// Reset old Theme Options while theme first run
if ( !function_exists( 'prohost_options_reset' ) ) {
	function prohost_options_reset($clear=true) {
		$theme_data = wp_get_theme();
		$slug = str_replace(' ', '_', trim(prohost_strtolower((string) $theme_data->get('Name'))));
		$option_name = 'prohost_'.strip_tags($slug).'_options_reset';
		if ( get_option($option_name, false) === false ) {	// && (string) $theme_data->get('Version') == '1.0'
			if ($clear) {
				// Remove Theme Options from WP Options
				global $wpdb;
				$wpdb->query('delete from '.esc_sql($wpdb->options).' where option_name like "prohost_%"');
				// Add Templates Options
				if (file_exists(prohost_get_file_dir('demo/templates_options.txt'))) {
					$txt = prohost_fgc(prohost_get_file_dir('demo/templates_options.txt'));
					$data = prohost_unserialize($txt);
					// Replace upload url in options
					if (is_array($data) && count($data) > 0) {
						foreach ($data as $k=>$v) {
							if (is_array($v) && count($v) > 0) {
								foreach ($v as $k1=>$v1) {
									$v[$k1] = prohost_replace_uploads_url(prohost_replace_uploads_url($v1, 'uploads'), 'imports');
								}
							}
							add_option( $k, $v, '', 'yes' );
						}
					}
				}
			}
			add_option($option_name, 1, '', 'yes');
		}
	}
}
?>