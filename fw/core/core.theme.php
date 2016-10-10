<?php
/**
 * ProHost Framework: Theme specific actions
 *
 * @package	prohost
 * @since	prohost 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'prohost_core_theme_setup' ) ) {
	add_action( 'prohost_action_before_init_theme', 'prohost_core_theme_setup', 11 );
	function prohost_core_theme_setup() {

		// Add default posts and comments RSS feed links to head 
		add_theme_support( 'automatic-feed-links' );
		
		// Enable support for Post Thumbnails
		add_theme_support( 'post-thumbnails' );
		
		// Custom header setup
		add_theme_support( 'custom-header', array('header-text'=>false));
		
		// Custom backgrounds setup
		add_theme_support( 'custom-background');
		
		// Supported posts formats
		add_theme_support( 'post-formats', array('gallery', 'video', 'audio', 'link', 'quote', 'image', 'status', 'aside', 'chat') ); 
 
 		// Autogenerate title tag
		add_theme_support('title-tag');
 		
		// Add user menu
		add_theme_support('nav-menus');
		
		// WooCommerce Support
		add_theme_support( 'woocommerce' );
		
		// Editor custom stylesheet - for user
		add_editor_style(prohost_get_file_url('css/editor-style.css'));	
		
		// Make theme available for translation
		// Translations can be filed in the /languages/ directory
		load_theme_textdomain( 'prohost', prohost_get_folder_dir('languages') );


		/* Front and Admin actions and filters:
		------------------------------------------------------------------------ */

		if ( !is_admin() ) {
			
			/* Front actions and filters:
			------------------------------------------------------------------------ */
	
			// Filters wp_title to print a neat <title> tag based on what is being viewed
			if (floatval(get_bloginfo('version')) < "4.1") {
				add_action('wp_head',						'prohost_wp_title_show');
				add_filter('wp_title',						'prohost_wp_title_modify', 10, 2);
			}

			// Add main menu classes
			//add_filter('wp_nav_menu_objects', 			'prohost_add_mainmenu_classes', 10, 2);
	
			// Prepare logo text
			add_filter('prohost_filter_prepare_logo_text',	'prohost_prepare_logo_text', 10, 1);
	
			// Add class "widget_number_#' for each widget
			add_filter('dynamic_sidebar_params', 			'prohost_add_widget_number', 10, 1);

			// Frontend editor: Save post data
			add_action('wp_ajax_frontend_editor_save',		'prohost_callback_frontend_editor_save');
			add_action('wp_ajax_nopriv_frontend_editor_save', 'prohost_callback_frontend_editor_save');

			// Frontend editor: Delete post
			add_action('wp_ajax_frontend_editor_delete', 	'prohost_callback_frontend_editor_delete');
			add_action('wp_ajax_nopriv_frontend_editor_delete', 'prohost_callback_frontend_editor_delete');
	
			// Enqueue scripts and styles
			add_action('wp_enqueue_scripts', 				'prohost_core_frontend_scripts');
			add_action('wp_footer',		 					'prohost_core_frontend_scripts_inline');
			add_action('prohost_action_add_scripts_inline','prohost_core_add_scripts_inline');

			// Prepare theme core global variables
			add_action('prohost_action_prepare_globals',	'prohost_core_prepare_globals');
		}

		// Register theme specific nav menus
		prohost_register_theme_menus();

		// Register theme specific sidebars
		prohost_register_theme_sidebars();
	}
}




/* Theme init
------------------------------------------------------------------------ */

// Init theme template
function prohost_core_init_theme() {
	if (prohost_storage_get('theme_inited')===true) return;
	prohost_storage_set('theme_inited', true);

	if (!is_admin()) prohost_profiler_add_point(esc_html__('After WP INIT actions', 'prohost'), false);

	// Load custom options from GET and post/page/cat options
	if (isset($_GET['set']) && $_GET['set']==1) {
		foreach ($_GET as $k=>$v) {
			if (prohost_get_theme_option($k, null) !== null) {
				setcookie($k, $v, 0, '/');
				$_COOKIE[$k] = $v;
			}
		}
	}

	// Get custom options from current category / page / post / shop / event
	prohost_load_custom_options();

	// Load skin
	$skin = prohost_esc(prohost_get_custom_option('theme_skin'));
	prohost_storage_set('theme_skin', $skin);
	if ( file_exists(prohost_get_file_dir('skins/'.($skin).'/skin.php')) ) {
		get_template_part(prohost_get_file_slug('skins/'.($skin).'/skin.php'));
	}

	// Fire init theme actions (after skin and custom options are loaded)
	do_action('prohost_action_init_theme');

	// Prepare theme core global variables
	do_action('prohost_action_prepare_globals');

	// Fire after init theme actions
	do_action('prohost_action_after_init_theme');
	prohost_profiler_add_point(esc_html__('After Theme Init', 'prohost'));
}


// Prepare theme global variables
if ( !function_exists( 'prohost_core_prepare_globals' ) ) {
	function prohost_core_prepare_globals() {
		if (!is_admin()) {
			// Logo text and slogan
			prohost_storage_set('logo_text', apply_filters('prohost_filter_prepare_logo_text', prohost_get_custom_option('logo_text')));
			prohost_storage_set('logo_slogan', get_bloginfo('description'));
			
			// Logo image and icons from skin
			$logo        = prohost_get_logo_icon('logo');
			$logo_side   = prohost_get_logo_icon('logo_side');
			$logo_fixed  = prohost_get_logo_icon('logo_fixed');
			$logo_footer = prohost_get_logo_icon('logo_footer');
			prohost_storage_set('logo', $logo);
			prohost_storage_set('logo_icon',   prohost_get_logo_icon('logo_icon'));
			prohost_storage_set('logo_side',   $logo_side   ? $logo_side   : $logo);
			prohost_storage_set('logo_fixed',  $logo_fixed  ? $logo_fixed  : $logo);
			prohost_storage_set('logo_footer', $logo_footer ? $logo_footer : $logo);
	
			$shop_mode = '';
			if (prohost_get_custom_option('show_mode_buttons')=='yes')
				$shop_mode = prohost_get_value_gpc('prohost_shop_mode');
			if (empty($shop_mode))
				$shop_mode = prohost_get_custom_option('shop_mode', '');
			if (empty($shop_mode) || !is_archive())
				$shop_mode = 'thumbs';
			prohost_storage_set('shop_mode', $shop_mode);
		}
	}
}


// Return url for the uploaded logo image or (if not uploaded) - to image from skin folder
if ( !function_exists( 'prohost_get_logo_icon' ) ) {
	function prohost_get_logo_icon($slug) {
		$mult = prohost_get_retina_multiplier();
		$logo_icon = '';
		if ($mult > 1) 			$logo_icon = prohost_get_custom_option($slug.'_retina');
		if (empty($logo_icon))	$logo_icon = prohost_get_custom_option($slug);
		return $logo_icon;
	}
}


// Display logo image with text and slogan (if specified)
if ( !function_exists( 'prohost_show_logo' ) ) {
	function prohost_show_logo($logo_main=true, $logo_fixed=false, $logo_footer=false, $logo_side=false, $logo_text=true, $logo_slogan=true) {
		if ($logo_main===true)		$logo_main   = prohost_storage_get('logo');
		if ($logo_fixed===true)		$logo_fixed  = prohost_storage_get('logo_fixed');
		if ($logo_footer===true)	$logo_footer = prohost_storage_get('logo_footer');
		if ($logo_side===true)		$logo_side   = prohost_storage_get('logo_side');
		if ($logo_text===true)		$logo_text   = prohost_storage_get('logo_text');
		if ($logo_slogan===true)	$logo_slogan = prohost_storage_get('logo_slogan');
		if ($logo_main || $logo_fixed || $logo_footer || $logo_side || $logo_text) {
		?>
		<div class="logo">
			<a href="<?php echo esc_url(home_url('/')); ?>"><?php
				if (!empty($logo_main)) {
					$attr = prohost_getimagesize($logo_main);
					echo '<img src="'.esc_url($logo_main).'" class="logo_main" alt=""'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>';
				}
				if (!empty($logo_fixed)) {
					$attr = prohost_getimagesize($logo_fixed);
					echo '<img src="'.esc_url($logo_fixed).'" class="logo_fixed" alt=""'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>';
				}
				if (!empty($logo_footer)) {
					$attr = prohost_getimagesize($logo_footer);
					echo '<img src="'.esc_url($logo_footer).'" class="logo_footer" alt=""'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>';
				}
				if (!empty($logo_side)) {
					$attr = prohost_getimagesize($logo_side);
					echo '<img src="'.esc_url($logo_side).'" class="logo_side" alt=""'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>';
				}
				echo !empty($logo_text) ? '<div class="logo_text">'.trim($logo_text).'</div>' : '';
				echo !empty($logo_slogan) ? '<br><div class="logo_slogan">' . esc_html($logo_slogan) . '</div>' : '';
			?></a>
		</div>
		<?php 
		}
	} 
}


// Add menu locations
if ( !function_exists( 'prohost_register_theme_menus' ) ) {
	function prohost_register_theme_menus() {
		register_nav_menus(apply_filters('prohost_filter_add_theme_menus', array(
			'menu_main'		=> esc_html__('Main Menu', 'prohost'),
			'menu_user'		=> esc_html__('User Menu', 'prohost'),
			'menu_footer'	=> esc_html__('Footer Menu', 'prohost'),
			'menu_side'		=> esc_html__('Side Menu', 'prohost')
		)));
	}
}


// Register widgetized area
if ( !function_exists( 'prohost_register_theme_sidebars' ) ) {
	function prohost_register_theme_sidebars($sidebars=array()) {
		if (!is_array($sidebars)) $sidebars = array();
		// Custom sidebars
		$custom = prohost_get_theme_option('custom_sidebars');
		if (is_array($custom) && count($custom) > 0) {
			foreach ($custom as $i => $sb) {
				if (trim(chop($sb))=='') continue;
				$sidebars['sidebar_custom_'.($i)]  = $sb;
			}
		}
		$sidebars = apply_filters( 'prohost_filter_add_theme_sidebars', $sidebars );
		prohost_storage_set('registered_sidebars', $sidebars);
		if (is_array($sidebars) && count($sidebars) > 0) {
			foreach ($sidebars as $id=>$name) {
				register_sidebar( array_merge( array(
													'name'          => $name,
													'id'            => $id
												),
												prohost_storage_get('widgets_args')
									)
				);
			}
		}
	}
}





/* Front actions and filters:
------------------------------------------------------------------------ */

//  Enqueue scripts and styles
if ( !function_exists( 'prohost_core_frontend_scripts' ) ) {
	function prohost_core_frontend_scripts() {
		
		// Modernizr will load in head before other scripts and styles
		// Use older version (from photostack)
		prohost_enqueue_script( 'prohost-core-modernizr-script', prohost_get_file_url('js/photostack/modernizr.min.js'), array(), null, false );
		
		// Enqueue styles
		//-----------------------------------------------------------------------------------------------------
		
		// Prepare custom fonts
		$fonts = prohost_get_list_fonts(false);
		$theme_fonts = array();
		$custom_fonts = prohost_get_custom_fonts();
		if (is_array($custom_fonts) && count($custom_fonts) > 0) {
			foreach ($custom_fonts as $s=>$f) {
				if (!empty($f['font-family']) && !prohost_is_inherit_option($f['font-family'])) $theme_fonts[$f['font-family']] = 1;
			}
		}
		// Prepare current skin fonts
		$theme_fonts = apply_filters('prohost_filter_used_fonts', $theme_fonts);
		// Link to selected fonts
		if (is_array($theme_fonts) && count($theme_fonts) > 0) {
			$google_fonts = '';
			foreach ($theme_fonts as $font=>$v) {
				if (isset($fonts[$font])) {
					$font_name = ($pos=prohost_strpos($font,' ('))!==false ? prohost_substr($font, 0, $pos) : $font;
					if (!empty($fonts[$font]['css'])) {
						$css = $fonts[$font]['css'];
						prohost_enqueue_style( 'prohost-font-'.str_replace(' ', '-', $font_name).'-style', $css, array(), null );
					} else {
						$google_fonts .= ($google_fonts ? '|' : '') 
							. (!empty($fonts[$font]['link']) ? $fonts[$font]['link'] : str_replace(' ', '+', $font_name).':300,300italic,400,400italic,700,700italic');
					}
				}
			}
			if ($google_fonts)
				prohost_enqueue_style( 'prohost-font-google_fonts-style', prohost_get_protocol() . '://fonts.googleapis.com/css?family=' . $google_fonts . '&subset=' . prohost_get_theme_option('fonts_subset'), array(), null );
		}
		
		// Fontello styles must be loaded before main stylesheet
		prohost_enqueue_style( 'prohost-fontello-style',  prohost_get_file_url('css/fontello/css/fontello.css'),  array(), null);
		//prohost_enqueue_style( 'prohost-fontello-animation-style', prohost_get_file_url('css/fontello/css/animation.css'), array(), null);

		// Main stylesheet
		prohost_enqueue_style( 'prohost-main-style', get_stylesheet_uri(), array(), null );
		
		// Animations
		if (prohost_get_theme_option('css_animation')=='yes' && (prohost_get_theme_option('animation_on_mobile')=='yes' || !wp_is_mobile()) && !prohost_vc_is_frontend())
			prohost_enqueue_style( 'prohost-animation-style',	prohost_get_file_url('css/core.animation.css'), array(), null );

		// Theme skin stylesheet
		do_action('prohost_action_add_styles');
		
		// Theme customizer stylesheet and inline styles
		prohost_enqueue_custom_styles();

		// Responsive
		if (prohost_get_theme_option('responsive_layouts') == 'yes') {
			$suffix = prohost_param_is_off(prohost_get_custom_option('show_sidebar_outer')) ? '' : '-outer';
			prohost_enqueue_style( 'prohost-responsive-style', prohost_get_file_url('css/responsive'.($suffix).'.css'), array(), null );
			do_action('prohost_action_add_responsive');
			if (prohost_get_custom_option('theme_skin')!='') {
				$css = apply_filters('prohost_filter_add_responsive_inline', '');
				if (!empty($css)) wp_add_inline_style( 'prohost-responsive-style', $css );
			}
		}

		// Disable loading JQuery UI CSS
		//global $wp_styles, $wp_scripts;
		//$wp_styles->done[]	= 'jquery-ui';
		//$wp_styles->done[]	= 'date-picker-css';
		wp_deregister_style('jquery_ui');
		wp_deregister_style('date-picker-css');


		// Enqueue scripts	
		//----------------------------------------------------------------------------------------------------------------------------
		
		// Load separate theme scripts
		prohost_enqueue_script( 'superfish', prohost_get_file_url('js/superfish.js'), array('jquery'), null, true );
		if (prohost_get_theme_option('menu_slider')=='yes') {
			prohost_enqueue_script( 'prohost-slidemenu-script', prohost_get_file_url('js/jquery.slidemenu.js'), array('jquery'), null, true );
			//prohost_enqueue_script( 'prohost-jquery-easing-script', prohost_get_file_url('js/jquery.easing.js'), array('jquery'), null, true );
		}

		if ( is_single() && prohost_get_custom_option('show_reviews')=='yes' ) {
			prohost_enqueue_script( 'prohost-core-reviews-script', prohost_get_file_url('js/core.reviews.js'), array('jquery'), null, true );
		}

		prohost_enqueue_script( 'prohost-core-utils-script',	prohost_get_file_url('js/core.utils.js'), array('jquery'), null, true );
		prohost_enqueue_script( 'prohost-core-init-script',	prohost_get_file_url('js/core.init.js'), array('jquery'), null, true );	
		prohost_enqueue_script( 'prohost-theme-init-script',	prohost_get_file_url('js/theme.init.js'), array('jquery'), null, true );	

		// Media elements library	
		if (prohost_get_theme_option('use_mediaelement')=='yes') {
			wp_enqueue_style ( 'mediaelement' );
			wp_enqueue_style ( 'wp-mediaelement' );
			wp_enqueue_script( 'mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		} else {
//			$wp_styles->done[]	= 'mediaelement';
//			$wp_styles->done[]	= 'wp-mediaelement';
//			$wp_scripts->done[]	= 'mediaelement';
//			$wp_scripts->done[]	= 'wp-mediaelement';
			wp_deregister_style('mediaelement');
			wp_deregister_style('wp-mediaelement');
			wp_deregister_script('mediaelement');
			wp_deregister_script('wp-mediaelement');
		}
		
		// Video background
		if (prohost_get_custom_option('show_video_bg') == 'yes' && prohost_get_custom_option('video_bg_youtube_code') != '') {
			prohost_enqueue_script( 'prohost-video-bg-script', prohost_get_file_url('js/jquery.tubular.1.0.js'), array('jquery'), null, true );
		}

		// Google map
		if ( prohost_get_custom_option('show_googlemap')=='yes' ) {
            $api_key = prohost_get_theme_option('api_google');
            prohost_enqueue_script( 'googlemap', prohost_get_protocol().'://maps.google.com/maps/api/js'.($api_key ? '?key='.$api_key : ''), array(), null, true );
			prohost_enqueue_script( 'prohost-googlemap-script', prohost_get_file_url('js/core.googlemap.js'), array(), null, true );
		}

			
		// Social share buttons
		if (is_singular() && !prohost_storage_get('blog_streampage') && prohost_get_custom_option('show_share')!='hide') {
			prohost_enqueue_script( 'prohost-social-share-script', prohost_get_file_url('js/social/social-share.js'), array('jquery'), null, true );
		}

		// Comments
		if ( is_singular() && !prohost_storage_get('blog_streampage') && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply', false, array(), null, true );
		}

		// Custom panel
		if (prohost_get_theme_option('show_theme_customizer') == 'yes') {
			if (file_exists(prohost_get_file_dir('core/core.customizer/front.customizer.css')))
				prohost_enqueue_style(  'prohost-customizer-style',  prohost_get_file_url('core/core.customizer/front.customizer.css'), array(), null );
			if (file_exists(prohost_get_file_dir('core/core.customizer/front.customizer.js')))
				prohost_enqueue_script( 'prohost-customizer-script', prohost_get_file_url('core/core.customizer/front.customizer.js'), array(), null, true );	
		}
		
		//Debug utils
		if (prohost_get_theme_option('debug_mode')=='yes') {
			prohost_enqueue_script( 'prohost-core-debug-script', prohost_get_file_url('js/core.debug.js'), array(), null, true );
		}

		// Theme skin script
		do_action('prohost_action_add_scripts');
	}
}

//  Enqueue Swiper Slider scripts and styles
if ( !function_exists( 'prohost_enqueue_slider' ) ) {
	function prohost_enqueue_slider($engine='all') {
		if ($engine=='all' || $engine=='swiper') {
			prohost_enqueue_style(  'prohost-swiperslider-style', 			prohost_get_file_url('js/swiper/swiper.css'), array(), null );
			prohost_enqueue_script( 'prohost-swiperslider-script', 			prohost_get_file_url('js/swiper/swiper.js'), array(), null, true );
			// jQuery version conflict with Revolution Slider
			//prohost_enqueue_script( 'prohost-swiperslider-script', 			prohost_get_file_url('js/swiper/swiper.jquery.js'), array(), null, true );
		}
	}
}

//  Enqueue Photostack gallery
if ( !function_exists( 'prohost_enqueue_polaroid' ) ) {
	function prohost_enqueue_polaroid() {
		prohost_enqueue_style(  'prohost-polaroid-style', 	prohost_get_file_url('js/photostack/component.css'), array(), null );
		prohost_enqueue_script( 'prohost-classie-script',		prohost_get_file_url('js/photostack/classie.js'), array(), null, true );
		prohost_enqueue_script( 'prohost-polaroid-script',	prohost_get_file_url('js/photostack/photostack.js'), array(), null, true );
	}
}

//  Enqueue Messages scripts and styles
if ( !function_exists( 'prohost_enqueue_messages' ) ) {
	function prohost_enqueue_messages() {
		prohost_enqueue_style(  'prohost-messages-style',		prohost_get_file_url('js/core.messages/core.messages.css'), array(), null );
		prohost_enqueue_script( 'prohost-messages-script',	prohost_get_file_url('js/core.messages/core.messages.js'),  array('jquery'), null, true );
	}
}

//  Enqueue Portfolio hover scripts and styles
if ( !function_exists( 'prohost_enqueue_portfolio' ) ) {
	function prohost_enqueue_portfolio($hover='') {
		prohost_enqueue_style( 'prohost-portfolio-style',  prohost_get_file_url('css/core.portfolio.css'), array(), null );
		if (prohost_strpos($hover, 'effect_dir')!==false)
			prohost_enqueue_script( 'hoverdir', prohost_get_file_url('js/hover/jquery.hoverdir.js'), array(), null, true );
	}
}

//  Enqueue Charts and Diagrams scripts and styles
if ( !function_exists( 'prohost_enqueue_diagram' ) ) {
	function prohost_enqueue_diagram($type='all') {
		if ($type=='all' || $type=='pie') prohost_enqueue_script( 'prohost-diagram-chart-script',	prohost_get_file_url('js/diagram/chart.min.js'), array(), null, true );
		if ($type=='all' || $type=='arc') prohost_enqueue_script( 'prohost-diagram-raphael-script',	prohost_get_file_url('js/diagram/diagram.raphael.min.js'), array(), 'no-compose', true );
	}
}

// Enqueue Theme Popup scripts and styles
// Link must have attribute: data-rel="popup" or data-rel="popup[gallery]"
if ( !function_exists( 'prohost_enqueue_popup' ) ) {
	function prohost_enqueue_popup($engine='') {
		if ($engine=='pretty' || (empty($engine) && prohost_get_theme_option('popup_engine')=='pretty')) {
			prohost_enqueue_style(  'prohost-prettyphoto-style',	prohost_get_file_url('js/prettyphoto/css/prettyPhoto.css'), array(), null );
			prohost_enqueue_script( 'prohost-prettyphoto-script',	prohost_get_file_url('js/prettyphoto/jquery.prettyPhoto.min.js'), array('jquery'), 'no-compose', true );
		} else if ($engine=='magnific' || (empty($engine) && prohost_get_theme_option('popup_engine')=='magnific')) {
			prohost_enqueue_style(  'prohost-magnific-style',	prohost_get_file_url('js/magnific/magnific-popup.css'), array(), null );
			prohost_enqueue_script( 'prohost-magnific-script',prohost_get_file_url('js/magnific/jquery.magnific-popup.min.js'), array('jquery'), '', true );
		} else if ($engine=='internal' || (empty($engine) && prohost_get_theme_option('popup_engine')=='internal')) {
			prohost_enqueue_messages();
		}
	}
}

//  Add inline scripts in the footer hook
if ( !function_exists( 'prohost_core_frontend_scripts_inline' ) ) {
	function prohost_core_frontend_scripts_inline() {
		do_action('prohost_action_add_scripts_inline');
	}
}

//  Add inline scripts in the footer
if (!function_exists('prohost_core_add_scripts_inline')) {
	function prohost_core_add_scripts_inline() {

		$msg = prohost_get_system_message(true); 
		if (!empty($msg['message'])) prohost_enqueue_messages();

		echo "<script type=\"text/javascript\">"
			
			. "if (typeof PROHOST_STORAGE == 'undefined') var PROHOST_STORAGE = {};"
			
			// AJAX parameters
			. "PROHOST_STORAGE['ajax_url']			 = '" . esc_url(admin_url('admin-ajax.php')) . "';"
			. "PROHOST_STORAGE['ajax_nonce']		 = '" . esc_attr(wp_create_nonce(admin_url('admin-ajax.php'))) . "';"
			
			// Site base url
			. "PROHOST_STORAGE['site_url']			= '" . get_site_url() . "';"
			
			// VC frontend edit mode
			. "PROHOST_STORAGE['vc_edit_mode']		= " . (function_exists('prohost_vc_is_frontend') && prohost_vc_is_frontend() ? 'true' : 'false') . ";"
			
			// Theme base font
			. "PROHOST_STORAGE['theme_font']		= '" . prohost_get_custom_font_settings('p', 'font-family') . "';"
			
			// Theme skin
			. "PROHOST_STORAGE['theme_skin']			= '" . esc_attr(prohost_get_custom_option('theme_skin')) . "';"
			. "PROHOST_STORAGE['theme_skin_color']		= '" . prohost_get_scheme_color('text_dark') . "';"
			. "PROHOST_STORAGE['theme_skin_bg_color']	= '" . prohost_get_scheme_color('bg_color') . "';"
			
			// Slider height
			. "PROHOST_STORAGE['slider_height']	= " . max(100, prohost_get_custom_option('slider_height')) . ";"
			
			// System message
			. "PROHOST_STORAGE['system_message']	= {"
				. "message: '" . addslashes($msg['message']) . "',"
				. "status: '"  . addslashes($msg['status'])  . "',"
				. "header: '"  . addslashes($msg['header'])  . "'"
				. "};"
			
			// User logged in
			. "PROHOST_STORAGE['user_logged_in']	= " . (is_user_logged_in() ? 'true' : 'false') . ";"
			
			// Show table of content for the current page
			. "PROHOST_STORAGE['toc_menu']		= '" . esc_attr(prohost_get_custom_option('menu_toc')) . "';"
			. "PROHOST_STORAGE['toc_menu_home']	= " . (prohost_get_custom_option('menu_toc')!='hide' && prohost_get_custom_option('menu_toc_home')=='yes' ? 'true' : 'false') . ";"
			. "PROHOST_STORAGE['toc_menu_top']	= " . (prohost_get_custom_option('menu_toc')!='hide' && prohost_get_custom_option('menu_toc_top')=='yes' ? 'true' : 'false') . ";"
			
			// Fix main menu
			. "PROHOST_STORAGE['menu_fixed']		= " . (prohost_get_theme_option('menu_attachment')=='fixed' ? 'true' : 'false') . ";"
			
			// Use responsive version for main menu
			. "PROHOST_STORAGE['menu_relayout']	= " . max(0, (int) prohost_get_theme_option('menu_relayout')) . ";"
			. "PROHOST_STORAGE['menu_responsive']	= " . (prohost_get_theme_option('responsive_layouts') == 'yes' ? max(0, (int) prohost_get_theme_option('menu_responsive')) : 0) . ";"
			. "PROHOST_STORAGE['menu_slider']     = " . (prohost_get_theme_option('menu_slider')=='yes' ? 'true' : 'false') . ";"
			
			// Menu cache is used
			. "PROHOST_STORAGE['menu_cache']	= " . (prohost_get_theme_option('use_menu_cache')=='yes' ? 'true' : 'false') . ";"

			// Right panel demo timer
			. "PROHOST_STORAGE['demo_time']		= " . (prohost_get_theme_option('show_theme_customizer')=='yes' ? max(0, (int) prohost_get_theme_option('customizer_demo')) : 0) . ";"

			// Video and Audio tag wrapper
			. "PROHOST_STORAGE['media_elements_enabled'] = " . (prohost_get_theme_option('use_mediaelement')=='yes' ? 'true' : 'false') . ";"
			
			// Use AJAX search
			. "PROHOST_STORAGE['ajax_search_enabled'] 	= " . (prohost_get_theme_option('use_ajax_search')=='yes' ? 'true' : 'false') . ";"
			. "PROHOST_STORAGE['ajax_search_min_length']	= " . min(3, prohost_get_theme_option('ajax_search_min_length')) . ";"
			. "PROHOST_STORAGE['ajax_search_delay']		= " . min(200, max(1000, prohost_get_theme_option('ajax_search_delay'))) . ";"

			// Use CSS animation
			. "PROHOST_STORAGE['css_animation']      = " . (prohost_get_theme_option('css_animation')=='yes' ? 'true' : 'false') . ";"
			. "PROHOST_STORAGE['menu_animation_in']  = '" . esc_attr(prohost_get_theme_option('menu_animation_in')) . "';"
			. "PROHOST_STORAGE['menu_animation_out'] = '" . esc_attr(prohost_get_theme_option('menu_animation_out')) . "';"

			// Popup windows engine
			. "PROHOST_STORAGE['popup_engine']	= '" . esc_attr(prohost_get_theme_option('popup_engine')) . "';"

			// E-mail mask
			. "PROHOST_STORAGE['email_mask']		= '^([a-zA-Z0-9_\\-]+\\.)*[a-zA-Z0-9_\\-]+@[a-z0-9_\\-]+(\\.[a-z0-9_\\-]+)*\\.[a-z]{2,6}$';"
			
			// Messages max length
			. "PROHOST_STORAGE['contacts_maxlength']	= " . intval(prohost_get_theme_option('message_maxlength_contacts')) . ";"
			. "PROHOST_STORAGE['comments_maxlength']	= " . intval(prohost_get_theme_option('message_maxlength_comments')) . ";"

			// Remember visitors settings
			. "PROHOST_STORAGE['remember_visitors_settings']	= " . (prohost_get_theme_option('remember_visitors_settings')=='yes' ? 'true' : 'false') . ";"

			// Internal vars - do not change it!
			// Flag for review mechanism
			. "PROHOST_STORAGE['admin_mode']			= false;"
			// Max scale factor for the portfolio and other isotope elements before relayout
			. "PROHOST_STORAGE['isotope_resize_delta']	= 0.3;"
			// jQuery object for the message box in the form
			. "PROHOST_STORAGE['error_message_box']	= null;"
			// Waiting for the viewmore results
			. "PROHOST_STORAGE['viewmore_busy']		= false;"
			. "PROHOST_STORAGE['video_resize_inited']	= false;"
			. "PROHOST_STORAGE['top_panel_height']		= 0;"
			
			. "</script>";
	}
}


//  Enqueue Custom styles (main Theme options settings)
if ( !function_exists( 'prohost_enqueue_custom_styles' ) ) {
	function prohost_enqueue_custom_styles() {
		// Custom stylesheet
		$custom_css = '';	//prohost_get_custom_option('custom_stylesheet_url');
		prohost_enqueue_style( 'prohost-custom-style', $custom_css ? $custom_css : prohost_get_file_url('css/custom-style.css'), array(), null );
		// Custom inline styles
		wp_add_inline_style( 'prohost-custom-style', prohost_prepare_custom_styles() );
	}
}

// Add class "widget_number_#' for each widget
if ( !function_exists( 'prohost_add_widget_number' ) ) {
	//add_filter('dynamic_sidebar_params', 'prohost_add_widget_number', 10, 1);
	function prohost_add_widget_number($prm) {
		if (is_admin()) return $prm;
		static $num=0, $last_sidebar='', $last_sidebar_id='', $last_sidebar_columns=0, $last_sidebar_count=0, $sidebars_widgets=array();
		$cur_sidebar = prohost_storage_get('current_sidebar');
		if (empty($cur_sidebar)) $cur_sidebar = 'undefined';
		if (count($sidebars_widgets) == 0)
			$sidebars_widgets = wp_get_sidebars_widgets();
		if ($last_sidebar != $cur_sidebar) {
			$num = 0;
			$last_sidebar = $cur_sidebar;
			$last_sidebar_id = $prm[0]['id'];
			$last_sidebar_columns = max(1, (int) prohost_get_custom_option('sidebar_'.($cur_sidebar).'_columns'));
			$last_sidebar_count = count($sidebars_widgets[$last_sidebar_id]);
		}
		$num++;
		$prm[0]['before_widget'] = str_replace(' class="', ' class="widget_number_'.esc_attr($num).($last_sidebar_columns > 1 ? ' column-1_'.esc_attr($last_sidebar_columns) : '').' ', $prm[0]['before_widget']);
		return $prm;
	}
}


// Show <title> tag under old WP (version < 4.1)
if ( !function_exists( 'prohost_wp_title_show' ) ) {
	// add_action('wp_head', 'prohost_wp_title_show');
	function prohost_wp_title_show() {
		?><title><?php wp_title( '|', true, 'right' ); ?></title><?php
	}
}

// Filters wp_title to print a neat <title> tag based on what is being viewed.
if ( !function_exists( 'prohost_wp_title_modify' ) ) {
	// add_filter( 'wp_title', 'prohost_wp_title_modify', 10, 2 );
	function prohost_wp_title_modify( $title, $sep ) {
		global $page, $paged;
		if ( is_feed() ) return $title;
		// Add the blog name
		$title .= get_bloginfo( 'name' );
		// Add the blog description for the home/front page.
		if ( is_home() || is_front_page() ) {
			$site_description = get_bloginfo( 'description', 'display' );
			if ( $site_description )
				$title .= " $sep $site_description";
		}
		// Add a page number if necessary:
		if ( $paged >= 2 || $page >= 2 )
			$title .= " $sep " . sprintf( esc_html__( 'Page %s', 'prohost' ), max( $paged, $page ) );
		return $title;
	}
}

// Add main menu classes
if ( !function_exists( 'prohost_add_mainmenu_classes' ) ) {
	// add_filter('wp_nav_menu_objects', 'prohost_add_mainmenu_classes', 10, 2);
	function prohost_add_mainmenu_classes($items, $args) {
		if (is_admin()) return $items;
		if ($args->menu_id == 'mainmenu' && prohost_get_theme_option('menu_colored')=='yes' && is_array($items) && count($items) > 0) {
			foreach($items as $k=>$item) {
				if ($item->menu_item_parent==0) {
					if ($item->type=='taxonomy' && $item->object=='category') {
						$cur_tint = prohost_taxonomy_get_inherited_property('category', $item->object_id, 'bg_tint');
						if (!empty($cur_tint) && !prohost_is_inherit_option($cur_tint))
							$items[$k]->classes[] = 'bg_tint_'.esc_attr($cur_tint);
					}
				}
			}
		}
		return $items;
	}
}


// Save post data from frontend editor
if ( !function_exists( 'prohost_callback_frontend_editor_save' ) ) {
	function prohost_callback_frontend_editor_save() {

		if ( !wp_verify_nonce( prohost_get_value_gp('nonce'), admin_url('admin-ajax.php') ) )
			die();

		$response = array('error'=>'');

		parse_str($_REQUEST['data'], $output);
		$post_id = $output['frontend_editor_post_id'];

		if ( prohost_get_theme_option("allow_editor")=='yes' && (current_user_can('edit_posts', $post_id) || current_user_can('edit_pages', $post_id)) ) {
			if ($post_id > 0) {
				$title   = stripslashes($output['frontend_editor_post_title']);
				$content = stripslashes($output['frontend_editor_post_content']);
				$excerpt = stripslashes($output['frontend_editor_post_excerpt']);
				$rez = wp_update_post(array(
					'ID'           => $post_id,
					'post_content' => $content,
					'post_excerpt' => $excerpt,
					'post_title'   => $title
				));
				if ($rez == 0) 
					$response['error'] = esc_html__('Post update error!', 'prohost');
			} else {
				$response['error'] = esc_html__('Post update error!', 'prohost');
			}
		} else
			$response['error'] = esc_html__('Post update denied!', 'prohost');
		
		echo json_encode($response);
		die();
	}
}

// Delete post from frontend editor
if ( !function_exists( 'prohost_callback_frontend_editor_delete' ) ) {
	function prohost_callback_frontend_editor_delete() {

		if ( !wp_verify_nonce( prohost_get_value_gp('nonce'), admin_url('admin-ajax.php') ) )
			die();

		$response = array('error'=>'');
		
		$post_id = $_REQUEST['post_id'];

		if ( prohost_get_theme_option("allow_editor")=='yes' && (current_user_can('delete_posts', $post_id) || current_user_can('delete_pages', $post_id)) ) {
			if ($post_id > 0) {
				$rez = wp_delete_post($post_id);
				if ($rez === false) 
					$response['error'] = esc_html__('Post delete error!', 'prohost');
			} else {
				$response['error'] = esc_html__('Post delete error!', 'prohost');
			}
		} else
			$response['error'] = esc_html__('Post delete denied!', 'prohost');

		echo json_encode($response);
		die();
	}
}


// Prepare logo text
if ( !function_exists( 'prohost_prepare_logo_text' ) ) {
	function prohost_prepare_logo_text($text) {
		$text = str_replace(array('[', ']'), array('<span class="theme_accent">', '</span>'), $text);
		$text = str_replace(array('{', '}'), array('<strong>', '</strong>'), $text);
		return $text;
	}
}
?>