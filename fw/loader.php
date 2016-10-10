<?php
/**
 * ProHost Framework
 *
 * @package prohost
 * @since prohost 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Framework directory path from theme root
if ( ! defined( 'PROHOST_FW_DIR' ) )			define( 'PROHOST_FW_DIR', 'fw' );

// Theme timing
if ( ! defined( 'PROHOST_START_TIME' ) )		define( 'PROHOST_START_TIME', microtime(true));		// Framework start time
if ( ! defined( 'PROHOST_START_MEMORY' ) )		define( 'PROHOST_START_MEMORY', memory_get_usage());	// Memory usage before core loading
if ( ! defined( 'PROHOST_START_QUERIES' ) )	define( 'PROHOST_START_QUERIES', get_num_queries());	// DB queries used

// Include theme variables storage
get_template_part(PROHOST_FW_DIR.'/core/core.storage');

// Theme variables storage
prohost_storage_set('options_prefix', 'prohost');	// Used as prefix for store theme's options in the post meta and wp options
prohost_storage_set('page_template', '');			// Storage for current page template name (used in the inheritance system)
prohost_storage_set('widgets_args', array(			// Arguments to register widgets
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h5 class="widget_title">',
		'after_title'   => '</h5>',
	)
);

/* Theme setup section
-------------------------------------------------------------------- */
if ( !function_exists( 'prohost_loader_theme_setup' ) ) {
	add_action( 'after_setup_theme', 'prohost_loader_theme_setup', 20 );
	function prohost_loader_theme_setup() {

		prohost_profiler_add_point(esc_html__('After load theme required files', 'prohost'));

		// Before init theme
		do_action('prohost_action_before_init_theme');

		// Load current values for main theme options
		prohost_load_main_options();

		// Theme core init - only for admin side. In frontend it called from header.php
		if ( is_admin() ) {
			prohost_core_init_theme();
		}
	}
}


/* Include core parts
------------------------------------------------------------------------ */
// Manual load important libraries before load all rest files
// core.strings must be first - we use prohost_str...() in the prohost_get_file_dir()
get_template_part(PROHOST_FW_DIR.'/core/core.strings');
// core.files must be first - we use prohost_get_file_dir() to include all rest parts
get_template_part(PROHOST_FW_DIR.'/core/core.files');

// Include debug and profiler
get_template_part(prohost_get_file_slug('core/core.debug.php'));

// Include custom theme files
prohost_autoload_folder( 'includes' );

// Include core files
prohost_autoload_folder( 'core' );

// Include theme-specific plugins and post types
prohost_autoload_folder( 'plugins' );

// Include theme templates
prohost_autoload_folder( 'templates' );

// Include theme widgets
prohost_autoload_folder( 'widgets' );
?>