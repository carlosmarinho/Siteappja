<?php
/**
 * Skin file for the theme.
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Theme init
if (!function_exists('prohost_action_skin_theme_setup')) {
	add_action( 'prohost_action_init_theme', 'prohost_action_skin_theme_setup', 1 );
	function prohost_action_skin_theme_setup() {

		// Add skin fonts in the used fonts list
		add_filter('prohost_filter_used_fonts',			'prohost_filter_skin_used_fonts');
		// Add skin fonts (from Google fonts) in the main fonts list (if not present).
		add_filter('prohost_filter_list_fonts',			'prohost_filter_skin_list_fonts');

		// Add skin stylesheets
		add_action('prohost_action_add_styles',			'prohost_action_skin_add_styles');
		// Add skin inline styles
		add_filter('prohost_filter_add_styles_inline',		'prohost_filter_skin_add_styles_inline');
		// Add skin responsive styles
		add_action('prohost_action_add_responsive',		'prohost_action_skin_add_responsive');
		// Add skin responsive inline styles
		add_filter('prohost_filter_add_responsive_inline',	'prohost_filter_skin_add_responsive_inline');

		// Add skin scripts
		add_action('prohost_action_add_scripts',			'prohost_action_skin_add_scripts');
		// Add skin scripts inline
		add_action('prohost_action_add_scripts_inline',	'prohost_action_skin_add_scripts_inline');

		// Add skin less files into list for compilation
		add_filter('prohost_filter_compile_less',			'prohost_filter_skin_compile_less');


		/* Color schemes

		// Accenterd colors
		accent1			- theme accented color 1
		accent1_hover	- theme accented color 1 (hover state)
		accent2			- theme accented color 2
		accent2_hover	- theme accented color 2 (hover state)
		accent3			- theme accented color 3
		accent3_hover	- theme accented color 3 (hover state)

		// Headers, text and links
		text			- main content
		text_light		- post info
		text_dark		- headers
		inverse_text	- text on accented background
		inverse_light	- post info on accented background
		inverse_dark	- headers on accented background
		inverse_link	- links on accented background
		inverse_hover	- hovered links on accented background

		// Block's border and background
		bd_color		- border for the entire block
		bg_color		- background color for the entire block
		bg_image, bg_image_position, bg_image_repeat, bg_image_attachment  - first background image for the entire block
		bg_image2,bg_image2_position,bg_image2_repeat,bg_image2_attachment - second background image for the entire block

		// Alternative colors - highlight blocks, form fields, etc.
		alter_text		- text on alternative background
		alter_light		- post info on alternative background
		alter_dark		- headers on alternative background
		alter_link		- links on alternative background
		alter_hover		- hovered links on alternative background
		alter_bd_color	- alternative border
		alter_bd_hover	- alternative border for hovered state or active field
		alter_bg_color	- alternative background
		alter_bg_hover	- alternative background for hovered state or active field
		alter_bg_image, alter_bg_image_position, alter_bg_image_repeat, alter_bg_image_attachment - background image for the alternative block

		*/

		// Add color schemes
		prohost_add_color_scheme('original', array(

			'title'					=> esc_html__('Original', 'prohost'),

			// Accent colors
			'accent1'				=> '#87c500',
			'accent1_hover'			=> '#63aa40',

			// Headers, text and links colors
			'text'					=> '#7e7f80',
			'text_light'			=> '#c2c2c2',
			'text_dark'				=> '#1a1f24',
			'inverse_text'			=> '#ffffff',
			'inverse_light'			=> '#ffffff',
			'inverse_dark'			=> '#ffffff',
			'inverse_link'			=> '#7e7f80',
			'inverse_hover'			=> '#7e7f80',

			// Whole block border and background
			'bd_color'				=> '#f0f0f0',
			'bg_color'				=> '#ffffff',
			'bg_image'				=> '',
			'bg_image_position'		=> 'left top',
			'bg_image_repeat'		=> 'repeat',
			'bg_image_attachment'	=> 'scroll',
			'bg_image2'				=> '',
			'bg_image2_position'	=> 'left top',
			'bg_image2_repeat'		=> 'repeat',
			'bg_image2_attachment'	=> 'scroll',

			// Alternative blocks (submenu items, form's fields, etc.)
			'alter_text'			=> '#1a1f24',
			'alter_light'			=> '#ffffff',
			'alter_dark'			=> '#e2e2e2',
			'alter_link'			=> '#247fe1',
			'alter_hover'			=> '#146dcf',
			'alter_bd_color'		=> '#31363a',
			'alter_bd_hover'		=> '#0c1015',
			'alter_bg_color'		=> '#f3f3f3',
			'alter_bg_hover'		=> '#1a1f24',
            'alter_bg_image'			=> prohost_get_file_url('images/image_404.png'),
            'alter_bg_image_position'	=> 'center center',
            'alter_bg_image_repeat'		=> 'no-repeat',
            'alter_bg_image_attachment'	=> 'scroll',
			)
		);

		// Add color schemes
		prohost_add_color_scheme('color2', array(

			'title'					=> esc_html__('Color 2', 'prohost'),

            // Accent colors
            'accent1'				=> '#e7d224',
            'accent1_hover'			=> '#dbc403',

            // Headers, text and links colors
            'text'					=> '#7e7f80',
            'text_light'			=> '#c2c2c2',
            'text_dark'				=> '#1a1f24',
            'inverse_text'			=> '#ffffff',
            'inverse_light'			=> '#ffffff',
            'inverse_dark'			=> '#ffffff',
            'inverse_link'			=> '#7e7f80',
            'inverse_hover'			=> '#7e7f80',

            // Whole block border and background
            'bd_color'				=> '#f0f0f0',
            'bg_color'				=> '#ffffff',
            'bg_image'				=> '',
            'bg_image_position'		=> 'left top',
            'bg_image_repeat'		=> 'repeat',
            'bg_image_attachment'	=> 'scroll',
            'bg_image2'				=> '',
            'bg_image2_position'	=> 'left top',
            'bg_image2_repeat'		=> 'repeat',
            'bg_image2_attachment'	=> 'scroll',

            // Alternative blocks (submenu items, form's fields, etc.)
            'alter_text'			=> '#1a1f24',
            'alter_light'			=> '#ffffff',
            'alter_dark'			=> '#e2e2e2',
            'alter_link'			=> '#f34135',
            'alter_hover'			=> '#fcc71f',
            'alter_bd_color'		=> '#31363a',
            'alter_bd_hover'		=> '#0c1015',
            'alter_bg_color'		=> '#f3f3f3',
            'alter_bg_hover'		=> '#1a1f24',
            'alter_bg_image'			=> prohost_get_file_url('images/image_404.png'),
            'alter_bg_image_position'	=> 'center center',
            'alter_bg_image_repeat'		=> 'no-repeat',
            'alter_bg_image_attachment'	=> 'scroll',
			)
		);

		// Add color schemes
		prohost_add_color_scheme('color3', array(

			'title'					=> esc_html__('Color 3', 'prohost'),

            // Accent colors
            'accent1'				=> '#96934e',
            'accent1_hover'			=> '#868231',

            // Headers, text and links colors
            'text'					=> '#7e7f80',
            'text_light'			=> '#c2c2c2',
            'text_dark'				=> '#1a1f24',
            'inverse_text'			=> '#ffffff',
            'inverse_light'			=> '#ffffff',
            'inverse_dark'			=> '#ffffff',
            'inverse_link'			=> '#7e7f80',
            'inverse_hover'			=> '#7e7f80',

            // Whole block border and background
            'bd_color'				=> '#f0f0f0',
            'bg_color'				=> '#ffffff',
            'bg_image'				=> '',
            'bg_image_position'		=> 'left top',
            'bg_image_repeat'		=> 'repeat',
            'bg_image_attachment'	=> 'scroll',
            'bg_image2'				=> '',
            'bg_image2_position'	=> 'left top',
            'bg_image2_repeat'		=> 'repeat',
            'bg_image2_attachment'	=> 'scroll',

            // Alternative blocks (submenu items, form's fields, etc.)
            'alter_text'			=> '#1a1f24',
            'alter_light'			=> '#ffffff',
            'alter_dark'			=> '#e2e2e2',
            'alter_link'			=> '#fcc71f',
            'alter_hover'			=> '#f34135',
            'alter_bd_color'		=> '#31363a',
            'alter_bd_hover'		=> '#0c1015',
            'alter_bg_color'		=> '#f3f3f3',
            'alter_bg_hover'		=> '#1a1f24',
            'alter_bg_image'			=> prohost_get_file_url('images/image_404.png'),
            'alter_bg_image_position'	=> 'center center',
            'alter_bg_image_repeat'		=> 'no-repeat',
            'alter_bg_image_attachment'	=> 'scroll',
			)
		);

		// Add color schemes
		prohost_add_color_scheme('color4', array(

			'title'					=> esc_html__('Color 4', 'prohost'),

            // Accent colors
            'accent1'				=> '#9158c7',
            'accent1_hover'			=> '#7d44b3',

            // Headers, text and links colors
            'text'					=> '#7e7f80',
            'text_light'			=> '#c2c2c2',
            'text_dark'				=> '#1a1f24',
            'inverse_text'			=> '#ffffff',
            'inverse_light'			=> '#ffffff',
            'inverse_dark'			=> '#ffffff',
            'inverse_link'			=> '#7e7f80',
            'inverse_hover'			=> '#7e7f80',

            // Whole block border and background
            'bd_color'				=> '#f0f0f0',
            'bg_color'				=> '#ffffff',
            'bg_image'				=> '',
            'bg_image_position'		=> 'left top',
            'bg_image_repeat'		=> 'repeat',
            'bg_image_attachment'	=> 'scroll',
            'bg_image2'				=> '',
            'bg_image2_position'	=> 'left top',
            'bg_image2_repeat'		=> 'repeat',
            'bg_image2_attachment'	=> 'scroll',

            // Alternative blocks (submenu items, form's fields, etc.)
            'alter_text'			=> '#1a1f24',
            'alter_light'			=> '#ffffff',
            'alter_dark'			=> '#e2e2e2',
            'alter_link'			=> '#29a453',
            'alter_hover'			=> '#146dcf',
            'alter_bd_color'		=> '#31363a',
            'alter_bd_hover'		=> '#0c1015',
            'alter_bg_color'		=> '#f3f3f3',
            'alter_bg_hover'		=> '#1a1f24',
            'alter_bg_image'			=> prohost_get_file_url('images/image_404.png'),
            'alter_bg_image_position'	=> 'center center',
            'alter_bg_image_repeat'		=> 'no-repeat',
            'alter_bg_image_attachment'	=> 'scroll',
			)
		);

		/* Font slugs:
		h1 ... h6	- headers
		p			- plain text
		link		- links
		info		- info blocks (Posted 15 May, 2015 by John Doe)
		menu		- main menu
		submenu		- dropdown menus
		logo		- logo text
		button		- button's caption
		input		- input fields
		*/

		// Add Custom fonts
		prohost_add_custom_font('h1', array(
			'title'			=> esc_html__('Heading 1', 'prohost'),
			'description'	=> '',
			'font-family'	=> '',
			'font-size' 	=> '4.688rem',
			'font-weight'	=> '600',
			'font-style'	=> '',
			'line-height'	=> '1.3em',
			'margin-top'	=> '0',
			'margin-bottom'	=> '0'
			)
		);
		prohost_add_custom_font('h2', array(
			'title'			=> esc_html__('Heading 2', 'prohost'),
			'description'	=> '',
			'font-family'	=> '',
			'font-size' 	=> '3.125rem',
			'font-weight'	=> '500',
			'font-style'	=> '',
			'line-height'	=> '1.3em',
			'margin-top'	=> '0',
			'margin-bottom'	=> '0'
			)
		);
		prohost_add_custom_font('h3', array(
			'title'			=> esc_html__('Heading 3', 'prohost'),
			'description'	=> '',
			'font-family'	=> '',
			'font-size' 	=> '2.5rem',
			'font-weight'	=> '600',
			'font-style'	=> '',
			'line-height'	=> '1.3em',
			'margin-top'	=> '0',
			'margin-bottom'	=> '0'
			)
		);
		prohost_add_custom_font('h4', array(
			'title'			=> esc_html__('Heading 4', 'prohost'),
			'description'	=> '',
			'font-family'	=> '',
			'font-size' 	=> '2.5rem',
			'font-weight'	=> '600',
			'font-style'	=> '',
			'line-height'	=> '1.3em',
			'margin-top'	=> '0',
			'margin-bottom'	=> '0'
			)
		);
		prohost_add_custom_font('h5', array(
			'title'			=> esc_html__('Heading 5', 'prohost'),
			'description'	=> '',
			'font-family'	=> '',
			'font-size' 	=> '1.563rem',
			'font-weight'	=> '600',
			'font-style'	=> '',
			'line-height'	=> '1.3em',
			'margin-top'	=> '0',
			'margin-bottom'	=> '0'
			)
		);
		prohost_add_custom_font('h6', array(
			'title'			=> esc_html__('Heading 6', 'prohost'),
			'description'	=> '',
			'font-family'	=> '',
			'font-size' 	=> '1.438rem',
			'font-weight'	=> '600',
			'font-style'	=> '',
			'line-height'	=> '1.3em',
			'margin-top'	=> '0',
			'margin-bottom'	=> '0'
			)
		);
		prohost_add_custom_font('p', array(
			'title'			=> esc_html__('Text', 'prohost'),
			'description'	=> '',
			'font-family'	=> 'Hind',
			'font-size' 	=> '16px',
			'font-weight'	=> '400',
			'font-style'	=> '',
			'line-height'	=> '1.5em',
			'margin-top'	=> '',
			'margin-bottom'	=> '1em'
			)
		);
		prohost_add_custom_font('menu', array(
			'title'			=> esc_html__('Main menu items', 'prohost'),
			'description'	=> '',
			'font-family'	=> '',
			'font-size' 	=> '1rem',
			'font-weight'	=> '700',
			'font-style'	=> '',
			'line-height'	=> '1.2857em',
			'margin-top'	=> '1.8rem',
			'margin-bottom'	=> '1.8rem'
			)
		);
		prohost_add_custom_font('submenu', array(
			'title'			=> esc_html__('Dropdown menu items', 'prohost'),
			'description'	=> '',
			'font-family'	=> '',
			'font-size' 	=> '0.938rem',
			'font-weight'	=> '600',
			'font-style'	=> '',
			'line-height'	=> '1.2857em',
			'margin-top'	=> '1.25rem',
			'margin-bottom'	=> '1.25rem'
			)
		);
		prohost_add_custom_font('logo', array(
			'title'			=> esc_html__('Logo', 'prohost'),
			'description'	=> '',
			'font-family'	=> 'Play',
			'font-size' 	=> '2rem',
			'font-weight'	=> '700',
			'font-style'	=> '',
			'line-height'	=> '1em',
			'margin-top'	=> '1.8rem',
			'margin-bottom'	=> '1.65rem'
			)
		);
	}
}





//------------------------------------------------------------------------------
// Skin's fonts
//------------------------------------------------------------------------------

// Add skin fonts in the used fonts list
if (!function_exists('prohost_filter_skin_used_fonts')) {
	//add_filter('prohost_filter_used_fonts', 'prohost_filter_skin_used_fonts');
	function prohost_filter_skin_used_fonts($theme_fonts) {
		//$theme_fonts['Roboto'] = 1;
		//$theme_fonts['Love Ya Like A Sister'] = 1;
		$theme_fonts['Hind'] = 1;
		$theme_fonts['Play'] = 1;
		return $theme_fonts;
	}
}

// Add skin fonts (from Google fonts) in the main fonts list (if not present).
// To use custom font-face you not need add it into list in this function
// How to install custom @font-face fonts into the theme?
// All @font-face fonts are located in "theme_name/css/font-face/" folder in the separate subfolders for the each font. Subfolder name is a font-family name!
// Place full set of the font files (for each font style and weight) and css-file named stylesheet.css in the each subfolder.
// Create your @font-face kit by using Fontsquirrel @font-face Generator (http://www.fontsquirrel.com/fontface/generator)
// and then extract the font kit (with folder in the kit) into the "theme_name/css/font-face" folder to install
if (!function_exists('prohost_filter_skin_list_fonts')) {
	//add_filter('prohost_filter_list_fonts', 'prohost_filter_skin_list_fonts');
	function prohost_filter_skin_list_fonts($list) {
		if (!isset($list['Lato']))	$list['Lato'] = array('family'=>'sans-serif');
        if (!isset($list['Hind']))	    $list['Hind'] = array('family'=>'sans-serif', 'link'=>'Hind:400,700,600,500');
        if (!isset($list['Play']))	    $list['Play'] = array('family'=>'sans-serif', 'link'=>'Play:400,700');
		return $list;
	}
}



//------------------------------------------------------------------------------
// Skin's stylesheets
//------------------------------------------------------------------------------
// Add skin stylesheets
if (!function_exists('prohost_action_skin_add_styles')) {
	//add_action('prohost_action_add_styles', 'prohost_action_skin_add_styles');
	function prohost_action_skin_add_styles() {
		// Add stylesheet files
		prohost_enqueue_style( 'prohost-skin-style', prohost_get_file_url('skin.css'), array(), null );
		if (file_exists(prohost_get_file_dir('skin.customizer.css')))
			prohost_enqueue_style( 'prohost-skin-customizer-style', prohost_get_file_url('skin.customizer.css'), array(), null );
	}
}

// Add skin inline styles
if (!function_exists('prohost_filter_skin_add_styles_inline')) {
	//add_filter('prohost_filter_add_styles_inline', 'prohost_filter_skin_add_styles_inline');
	function prohost_filter_skin_add_styles_inline($custom_style) {
		// Todo: add skin specific styles in the $custom_style to override
		//       rules from style.css and shortcodes.css
		return $custom_style;
	}
}

// Add skin responsive styles
if (!function_exists('prohost_action_skin_add_responsive')) {
	//add_action('prohost_action_add_responsive', 'prohost_action_skin_add_responsive');
	function prohost_action_skin_add_responsive() {
		$suffix = prohost_param_is_off(prohost_get_custom_option('show_sidebar_outer')) ? '' : '-outer';
		if (file_exists(prohost_get_file_dir('skin.responsive'.($suffix).'.css')))
			prohost_enqueue_style( 'theme-skin-responsive-style', prohost_get_file_url('skin.responsive'.($suffix).'.css'), array(), null );
	}
}

// Add skin responsive inline styles
if (!function_exists('prohost_filter_skin_add_responsive_inline')) {
	//add_filter('prohost_filter_add_responsive_inline', 'prohost_filter_skin_add_responsive_inline');
	function prohost_filter_skin_add_responsive_inline($custom_style) {
		return $custom_style;
	}
}

// Add skin.less into list files for compilation
if (!function_exists('prohost_filter_skin_compile_less')) {
	//add_filter('prohost_filter_compile_less', 'prohost_filter_skin_compile_less');
	function prohost_filter_skin_compile_less($files) {
		if (file_exists(prohost_get_file_dir('skin.less'))) {
		 	$files[] = prohost_get_file_dir('skin.less');
		}
		return $files;
	}
}



//------------------------------------------------------------------------------
// Skin's scripts
//------------------------------------------------------------------------------

// Add skin scripts
if (!function_exists('prohost_action_skin_add_scripts')) {
	//add_action('prohost_action_add_scripts', 'prohost_action_skin_add_scripts');
	function prohost_action_skin_add_scripts() {
		if (file_exists(prohost_get_file_dir('skin.js')))
			prohost_enqueue_script( 'theme-skin-script', prohost_get_file_url('skin.js'), array(), null );
		if (prohost_get_theme_option('show_theme_customizer') == 'yes' && file_exists(prohost_get_file_dir('skin.customizer.js')))
			prohost_enqueue_script( 'theme-skin-customizer-script', prohost_get_file_url('skin.customizer.js'), array(), null );
        /*Special script from Fix autocomplete in Chrome*/
        if (file_exists(prohost_get_file_dir('special.fix-autocomplete.js')))
            prohost_enqueue_script( 'theme-skin-fix-autocomplete-script', prohost_get_file_url('special.fix-autocomplete.js'), array(), null );
        /*Special script from Woocommerce*/
        if (function_exists('is_cart')) {
            if (file_exists(prohost_get_file_dir('special.table-woo.js')) && is_cart())
                prohost_enqueue_script('theme-skin-table-woo-script', prohost_get_file_url('special.table-woo.js'), array(), null);
        }
	}
}

// Add skin scripts inline
if (!function_exists('prohost_action_skin_add_scripts_inline')) {
	//add_action('prohost_action_add_scripts_inline', 'prohost_action_skin_add_scripts_inline');
	function prohost_action_skin_add_scripts_inline() {
		// Todo: add skin specific scripts
	}
}
?>