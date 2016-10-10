<?php
/**
 * Theme sprecific functions and definitions
 */

/* Theme setup section
------------------------------------------------------------------- */

// Set the content width based on the theme's design and stylesheet.
if ( ! isset( $content_width ) ) $content_width = 1170; /* pixels */

// Add theme specific actions and filters
// Attention! Function were add theme specific actions and filters handlers must have priority 1
if ( !function_exists( 'prohost_theme_setup' ) ) {
	add_action( 'prohost_action_before_init_theme', 'prohost_theme_setup', 1 );
	function prohost_theme_setup() {

		// Register theme menus
		add_filter( 'prohost_filter_add_theme_menus',		'prohost_add_theme_menus' );

		// Register theme sidebars
		add_filter( 'prohost_filter_add_theme_sidebars',	'prohost_add_theme_sidebars' );

		// Set options for importer
		add_filter( 'prohost_filter_importer_options',		'prohost_set_importer_options' );

        // Add theme specified classes into the body
        add_filter( 'body_class', 'prohost_body_classes' );

        // Set list of the theme required plugins
		prohost_storage_set('required_plugins', array(
			'essgrids',
			'instagram_widget',
			'revslider',
			'trx_utils',
			'visual_composer',
			'woocommerce'
			)
		);
		
	}
}


// Add/Remove theme nav menus
if ( !function_exists( 'prohost_add_theme_menus' ) ) {
	//add_filter( 'prohost_filter_add_theme_menus', 'prohost_add_theme_menus' );
	function prohost_add_theme_menus($menus) {
		return $menus;
	}
}


// Add theme specific widgetized areas
if ( !function_exists( 'prohost_add_theme_sidebars' ) ) {
	//add_filter( 'prohost_filter_add_theme_sidebars',	'prohost_add_theme_sidebars' );
	function prohost_add_theme_sidebars($sidebars=array()) {
		if (is_array($sidebars)) {
			$theme_sidebars = array(
				'sidebar_main'		=> esc_html__( 'Main Sidebar', 'prohost' ),
				'sidebar_footer'	=> esc_html__( 'Footer Sidebar', 'prohost' )
			);
			if (function_exists('prohost_exists_woocommerce') && prohost_exists_woocommerce()) {
				$theme_sidebars['sidebar_cart']  = esc_html__( 'WooCommerce Cart Sidebar', 'prohost' );
			}
			$sidebars = array_merge($theme_sidebars, $sidebars);
		}
		return $sidebars;
	}
}


// Add theme specified classes into the body
if ( !function_exists('prohost_body_classes') ) {
    //add_filter( 'body_class', 'prohost_body_classes' );
    function prohost_body_classes( $classes ) {

        $classes[] = 'prohost_body';
        $classes[] = 'body_style_' . trim(prohost_get_custom_option('body_style'));
        $classes[] = 'body_' . (prohost_get_custom_option('body_filled')=='yes' ? 'filled' : 'transparent');
        $classes[] = 'theme_skin_' . trim(prohost_get_custom_option('theme_skin'));
        $classes[] = 'article_style_' . trim(prohost_get_custom_option('article_style'));

        $blog_style = prohost_get_custom_option(is_singular() && !prohost_storage_get('blog_streampage') ? 'single_style' : 'blog_style');
        $classes[] = 'layout_' . trim($blog_style);
        $classes[] = 'template_' . trim(prohost_get_template_name($blog_style));

        $body_scheme = prohost_get_custom_option('body_scheme');
        if (empty($body_scheme)  || prohost_is_inherit_option($body_scheme)) $body_scheme = 'original';
        $classes[] = 'scheme_' . $body_scheme;

        $top_panel_position = prohost_get_custom_option('top_panel_position');
        if (!prohost_param_is_off($top_panel_position)) {
            $classes[] = 'top_panel_show';
            $classes[] = 'top_panel_' . trim($top_panel_position);
        } else
            $classes[] = 'top_panel_hide';
        $classes[] = prohost_get_sidebar_class();

        if (prohost_get_custom_option('show_video_bg')=='yes' && (prohost_get_custom_option('video_bg_youtube_code')!='' || prohost_get_custom_option('video_bg_url')!=''))
            $classes[] = 'video_bg_show';

        if (prohost_get_theme_option('page_preloader')!='')
            $classes[] = 'preloader';

        return $classes;
    }
}


// Theme init
if ( !function_exists( 'prohost_theme_init' ) ) {
    function prohost_theme_init(){
        prohost_core_init_theme();
        prohost_profiler_add_point(esc_html__('Before Theme HTML output', 'prohost'));
    }
}


// Theme options
if ( !function_exists( 'prohost_theme_options' ) ) {
    function prohost_theme_options(){

        $theme_init = array();
        $theme_init['theme_skin'] = prohost_esc(prohost_get_custom_option('theme_skin'));
        $theme_init['body_scheme'] = prohost_get_custom_option('body_scheme');
        if (empty($theme_init['body_scheme']) || prohost_is_inherit_option($theme_init['body_scheme'])) $theme_init['body_scheme'] = 'original';
        $theme_init['blog_style'] = prohost_get_custom_option(is_singular() && !prohost_storage_get('blog_streampage') ? 'single_style' : 'blog_style');
        $theme_init['body_style'] = prohost_get_custom_option('body_style');
        $theme_init['article_style'] = prohost_get_custom_option('article_style');
        $theme_init['top_panel_style'] = prohost_get_custom_option('top_panel_style');
        $theme_init['top_panel_position'] = prohost_get_custom_option('top_panel_position');
        $theme_init['top_panel_scheme'] = prohost_get_custom_option('top_panel_scheme');
        $theme_init['video_bg_show'] = prohost_get_custom_option('show_video_bg') == 'yes' && (prohost_get_custom_option('video_bg_youtube_code') != '' || prohost_get_custom_option('video_bg_url') != '');

        return $theme_init;
    }
}


// Page preloader options
if ( !function_exists( 'prohost_page_preloader_style_css' ) ) {
    function prohost_page_preloader_style_css()    {
        if (($preloader = prohost_get_theme_option('page_preloader')) != '') {
            $clr = prohost_get_scheme_color('bg_color');
            ?>
            <style type="text/css">
                <!--
                #page_preloader {
                    background-color: <?php echo esc_attr($clr); ?>;
                    background-image: url(<?php echo esc_url($preloader); ?>);
                    background-position: center;
                    background-repeat: no-repeat;
                    position: fixed;
                    z-index: 1000000;
                    left: 0;
                    top: 0;
                    right: 0;
                    bottom: 0;
                    opacity: 0.8;
                }
                -->
            </style>
            <?php
        }
    }
}


// Add TOC items 'Home' and "To top"
if ( !function_exists( 'prohost_add_toc' ) ) {
    function prohost_add_toc()    {
        if (prohost_get_custom_option('menu_toc_home')=='yes')
            echo trim(prohost_sc_anchor(array(
                    'id' => "toc_home",
                    'title' => esc_html__('Home', 'prohost'),
                    'description' => esc_html__('{{Return to Home}} - ||navigate to home page of the site', 'prohost'),
                    'icon' => "icon-home",
                    'separator' => "yes",
                    'url' => esc_url(home_url('/'))
                )
            ));
        if (prohost_get_custom_option('menu_toc_top')=='yes')
            echo trim(prohost_sc_anchor(array(
                    'id' => "toc_top",
                    'title' => esc_html__('To Top', 'prohost'),
                    'description' => esc_html__('{{Back to top}} - ||scroll to top of the page', 'prohost'),
                    'icon' => "icon-double-up",
                    'separator' => "yes")
            ));
    }
}


// Set theme specific importer options
if ( !function_exists( 'prohost_set_importer_options' ) ) {
	//add_filter( 'prohost_filter_importer_options',	'prohost_set_importer_options' );
	function prohost_set_importer_options($options=array()) {
		if (is_array($options)) {
			$options['debug'] = prohost_get_theme_option('debug_mode')=='yes';
			$options['domain_dev'] = 'prohost.dv.ancorathemes.com';
			$options['domain_demo'] = 'prohost.ancorathemes.com';
			$options['menus'] = array(
				'menu-main'	  => esc_html__('Main menu', 'prohost'),
				'menu-user'	  => esc_html__('User menu', 'prohost'),
				'menu-footer' => esc_html__('Footer menu', 'prohost'),
				'menu-outer'  => esc_html__('Main menu', 'prohost')
			);
			$options['file_with_attachments'] = array(				// Array with names of the attachments
				'demo/uploads.zip',									// Name of the local file with attachments
			);
			$options['attachments_by_parts'] = true;				// Files above are parts of single file - large media archive. They are must be concatenated in one file before unpacking
		}
		return $options;
	}
}


/* Include framework core files
------------------------------------------------------------------- */
// If now is WP Heartbeat call - skip loading theme core files (to reduce server and DB uploads)
// Remove comments below only if your theme not work with own post types and/or taxonomies
//if (!isset($_POST['action']) || $_POST['action']!="heartbeat") {
	get_template_part('fw/loader');
//}
?>