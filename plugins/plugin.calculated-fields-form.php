<?php
/* Calculated fields form support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('prohost_calcfields_form_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_calcfields_form_theme_setup', 1 );
	function prohost_calcfields_form_theme_setup() {
		// Register shortcode in the shortcodes list
		if (prohost_exists_calcfields_form()) {
			add_action('prohost_action_shortcodes_list',				'prohost_calcfields_form_reg_shortcodes');
			if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
				add_action('prohost_action_shortcodes_list_vc',		'prohost_calcfields_form_reg_shortcodes_vc');
			if (is_admin()) {
				add_filter( 'prohost_filter_importer_options',			'prohost_calcfields_form_importer_set_options', 10, 1 );
				add_action( 'prohost_action_importer_params',			'prohost_calcfields_form_importer_show_params', 10, 1 );
				add_action( 'prohost_action_importer_import',			'prohost_calcfields_form_importer_import', 10, 2 );
				add_action( 'prohost_action_importer_import_fields',	'prohost_calcfields_form_importer_import_fields', 10, 1 );
				add_action( 'prohost_action_importer_export',			'prohost_calcfields_form_importer_export', 10, 1 );
				add_action( 'prohost_action_importer_export_fields',	'prohost_calcfields_form_importer_export_fields', 10, 1 );
			}
			add_action('wp_enqueue_scripts', 							'prohost_calcfields_form_frontend_scripts');
		}
		if (is_admin()) {
			add_filter( 'prohost_filter_importer_required_plugins',	'prohost_calcfields_form_importer_required_plugins', 10, 2 );
			add_filter( 'prohost_filter_required_plugins',				'prohost_calcfields_form_required_plugins' );
		}
	}
}

// Check if plugin installed and activated
if ( !function_exists( 'prohost_exists_calcfields_form' ) ) {
	function prohost_exists_calcfields_form() {
		return defined('CP_SCHEME');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'prohost_calcfields_form_required_plugins' ) ) {
	//add_filter('prohost_filter_required_plugins',	'prohost_calcfields_form_required_plugins');
	function prohost_calcfields_form_required_plugins($list=array()) {
		if (in_array('calcfields', prohost_storage_get('required_plugins')))
			$list[] = array(
					'name' 		=> 'Calculated Fields Form',
					'slug' 		=> 'calculated-fields-form',
					'required' 	=> false
					);
		return $list;
	}
}

// Remove jquery_ui from frontend
if ( !function_exists( 'prohost_calcfields_form_frontend_scripts' ) ) {
	//add_action('wp_enqueue_scripts', 'prohost_calcfields_form_frontend_scripts');
	function prohost_calcfields_form_frontend_scripts() {
		// Disable loading JQuery UI CSS
		//global $wp_styles, $wp_scripts;
		//$wp_styles->done[] = 'cpcff_jquery_ui';
	}
}


// One-click import support
//------------------------------------------------------------------------

// Check in the required plugins
if ( !function_exists( 'prohost_calcfields_form_importer_required_plugins' ) ) {
	//add_filter( 'prohost_filter_importer_required_plugins',	'prohost_calcfields_form_importer_required_plugins', 10, 2 );
	function prohost_calcfields_form_importer_required_plugins($not_installed='', $list='') {
		//if (in_array('calcfields', prohost_storage_get('required_plugins')) && !prohost_exists_calcfields_form() )
		if (prohost_strpos($list, 'calcfields')!==false && !prohost_exists_calcfields_form() )
			$not_installed .= '<br>Calculated Fields Form';
		return $not_installed;
	}
}

// Set options for one-click importer
if ( !function_exists( 'prohost_calcfields_form_importer_set_options' ) ) {
	//add_filter( 'prohost_filter_importer_options',	'prohost_calcfields_form_importer_set_options', 10, 1 );
	function prohost_calcfields_form_importer_set_options($options=array()) {
		if ( in_array('calcfields', prohost_storage_get('required_plugins')) && prohost_exists_calcfields_form() ) {
			$options['file_with_calcfields_form'] = 'demo/calcfields_form.txt';			// Name of the file with Calculated Fields Form data
		}
		return $options;
	}
}

// Add checkbox to the one-click importer
if ( !function_exists( 'prohost_calcfields_form_importer_show_params' ) ) {
	//add_action( 'prohost_action_importer_params',	'prohost_calcfields_form_importer_show_params', 10, 1 );
	function prohost_calcfields_form_importer_show_params($importer) {
		?>
		<input type="checkbox" <?php echo in_array('calcfields', prohost_storage_get('required_plugins')) && $importer->options['plugins_initial_state'] 
											? 'checked="checked"' 
											: ''; ?> value="1" name="import_calcfields_form" id="import_calcfields_form" /> <label for="import_calcfields_form"><?php esc_html_e('Import Calculated Fields Form', 'prohost'); ?></label><br>
		<?php
	}
}

// Import posts
if ( !function_exists( 'prohost_calcfields_form_importer_import' ) ) {
	//add_action( 'prohost_action_importer_import',	'prohost_calcfields_form_importer_import', 10, 2 );
	function prohost_calcfields_form_importer_import($importer, $action) {
		if ( $action == 'import_calcfields_form' ) {
			$importer->import_dump('calcfields_form', esc_html__('Calculated Fields Form', 'prohost'));
		}
	}
}

// Display import progress
if ( !function_exists( 'prohost_calcfields_form_importer_import_fields' ) ) {
	//add_action( 'prohost_action_importer_import_fields',	'prohost_calcfields_form_importer_import_fields', 10, 1 );
	function prohost_calcfields_form_importer_import_fields($importer) {
		?>
		<tr class="import_calcfields_form">
			<td class="import_progress_item"><?php esc_html_e('Calculated Fields Form', 'prohost'); ?></td>
			<td class="import_progress_status"></td>
		</tr>
		<?php
	}
}

// Export posts
if ( !function_exists( 'prohost_calcfields_form_importer_export' ) ) {
	//add_action( 'prohost_action_importer_export',	'prohost_calcfields_form_importer_export', 10, 1 );
	function prohost_calcfields_form_importer_export($importer) {
		prohost_storage_set('export_calcfields_form', serialize( array(
			CP_CALCULATEDFIELDSF_FORMS_TABLE => $importer->export_dump(CP_CALCULATEDFIELDSF_FORMS_TABLE)
			) )
		);
	}
}

// Display exported data in the fields
if ( !function_exists( 'prohost_calcfields_form_importer_export_fields' ) ) {
	//add_action( 'prohost_action_importer_export_fields',	'prohost_calcfields_form_importer_export_fields', 10, 1 );
	function prohost_calcfields_form_importer_export_fields($importer) {
		?>
		<tr>
			<th align="left"><?php esc_html_e('Calculated Fields Form', 'prohost'); ?></th>
			<td><?php prohost_fpc(prohost_get_file_dir('core/core.importer/export/calcfields_form.txt'), prohost_storage_get('export_calcfields_form')); ?>
				<a download="calcfields_form.txt" href="<?php echo esc_url(prohost_get_file_url('core/core.importer/export/calcfields_form.txt')); ?>"><?php esc_html_e('Download', 'prohost'); ?></a>
			</td>
		</tr>
		<?php
	}
}


// Lists
//------------------------------------------------------------------------

// Return Calculated forms list list, prepended inherit (if need)
if ( !function_exists( 'prohost_get_list_calcfields_form' ) ) {
	function prohost_get_list_calcfields_form($prepend_inherit=false) {
		if (($list = prohost_storage_get('list_calcfields_form'))=='') {
			$list = array();
			if (prohost_exists_calcfields_form()) {
				global $wpdb;
				$rows = $wpdb->get_results( "SELECT id, form_name FROM " . esc_sql($wpdb->prefix . CP_CALCULATEDFIELDSF_FORMS_TABLE) );
				if (is_array($rows) && count($rows) > 0) {
					foreach ($rows as $row) {
						$list[$row->id] = $row->form_name;
					}
				}
			}
			$list = apply_filters('prohost_filter_list_calcfields_form', $list);
			if (prohost_get_theme_setting('use_list_cache')) prohost_storage_set('list_calcfields_form', $list); 
		}
		return $prepend_inherit ? prohost_array_merge(array('inherit' => esc_html__("Inherit", 'prohost')), $list) : $list;
	}
}



// Shortcodes
//------------------------------------------------------------------------

// Register shortcode in the shortcodes list
if (!function_exists('prohost_calcfields_form_reg_shortcodes')) {
	//add_filter('prohost_action_shortcodes_list',	'prohost_calcfields_form_reg_shortcodes');
	function prohost_calcfields_form_reg_shortcodes() {
		if (prohost_storage_isset('shortcodes')) {

			$forms_list = prohost_get_list_calcfields_form();

			prohost_sc_map_after( 'trx_button', 'CP_CALCULATED_FIELDS', array(
					"title" => esc_html__("Calculated fields form", "prohost"),
					"desc" => esc_html__("Insert calculated fields form", "prohost"),
					"decorate" => true,
					"container" => false,
					"params" => array(
						"id" => array(
							"title" => esc_html__("Form ID", "prohost"),
							"desc" => esc_html__("Select Form to insert into current page", "prohost"),
							"value" => "",
							"size" => "medium",
							"options" => $forms_list,
							"type" => "select"
							)
						)
					)
			);
		}
	}
}


// Register shortcode in the VC shortcodes list
if (!function_exists('prohost_calcfields_form_reg_shortcodes_vc')) {
	//add_filter('prohost_action_shortcodes_list_vc',	'prohost_calcfields_form_reg_shortcodes_vc');
	function prohost_calcfields_form_reg_shortcodes_vc() {

		$forms_list = prohost_get_list_calcfields_form();

		// Calculated fields form
		vc_map( array(
				"base" => "CP_CALCULATED_FIELDS",
				"name" => esc_html__("Calculated fields form", "prohost"),
				"description" => esc_html__("Insert calculated fields form", "prohost"),
				"category" => esc_html__('Content', 'prohost'),
				'icon' => 'icon_trx_calcfields',
				"class" => "trx_sc_single trx_sc_calcfields",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "id",
						"heading" => esc_html__("Form ID", "prohost"),
						"description" => esc_html__("Select Form to insert into current page", "prohost"),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($forms_list),
						"type" => "dropdown"
					)
				)
			) );
			
		class WPBakeryShortCode_Cp_Calculated_Fields extends PROHOST_VC_ShortCodeSingle {}

	}
}
?>