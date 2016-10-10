<?php
/* HTML5 jQuery Audio Player support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('prohost_html5_jquery_audio_player_theme_setup')) {
    add_action( 'prohost_action_before_init_theme', 'prohost_html5_jquery_audio_player_theme_setup' );
    function prohost_html5_jquery_audio_player_theme_setup() {
        // Add shortcode in the shortcodes list
        if (prohost_exists_html5_jquery_audio_player()) {
			add_action('prohost_action_add_styles',					'prohost_html5_jquery_audio_player_frontend_scripts' );
            add_action('prohost_action_shortcodes_list',				'prohost_html5_jquery_audio_player_reg_shortcodes');
			if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
	            add_action('prohost_action_shortcodes_list_vc',		'prohost_html5_jquery_audio_player_reg_shortcodes_vc');
            if (is_admin()) {
                add_filter( 'prohost_filter_importer_options',			'prohost_html5_jquery_audio_player_importer_set_options', 10, 1 );
                add_action( 'prohost_action_importer_params',			'prohost_html5_jquery_audio_player_importer_show_params', 10, 1 );
                add_action( 'prohost_action_importer_import',			'prohost_html5_jquery_audio_player_importer_import', 10, 2 );
				add_action( 'prohost_action_importer_import_fields',	'prohost_html5_jquery_audio_player_importer_import_fields', 10, 1 );
                add_action( 'prohost_action_importer_export',			'prohost_html5_jquery_audio_player_importer_export', 10, 1 );
                add_action( 'prohost_action_importer_export_fields',	'prohost_html5_jquery_audio_player_importer_export_fields', 10, 1 );
            }
        }
        if (is_admin()) {
            add_filter( 'prohost_filter_importer_required_plugins',	'prohost_html5_jquery_audio_player_importer_required_plugins', 10, 2 );
            add_filter( 'prohost_filter_required_plugins',				'prohost_html5_jquery_audio_player_required_plugins' );
        }
    }
}

// Check if plugin installed and activated
if ( !function_exists( 'prohost_exists_html5_jquery_audio_player' ) ) {
	function prohost_exists_html5_jquery_audio_player() {
		return function_exists('hmp_db_create');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'prohost_html5_jquery_audio_player_required_plugins' ) ) {
	//add_filter('prohost_filter_required_plugins',	'prohost_html5_jquery_audio_player_required_plugins');
	function prohost_html5_jquery_audio_player_required_plugins($list=array()) {
        if (in_array('html5_jquery_audio_player', prohost_storage_get('required_plugins')))
            $list[] = array(
					'name' 		=> 'HTML5 jQuery Audio Player',
					'slug' 		=> 'html5-jquery-audio-player',
					'required' 	=> false
				);
		return $list;
	}
}

// Enqueue custom styles
if ( !function_exists( 'prohost_html5_jquery_audio_player_frontend_scripts' ) ) {
	//add_action( 'prohost_action_add_styles', 'prohost_html5_jquery_audio_player_frontend_scripts' );
	function prohost_html5_jquery_audio_player_frontend_scripts() {
		if (file_exists(prohost_get_file_dir('css/plugin.html5-jquery-audio-player.css'))) {
			prohost_enqueue_style( 'prohost-plugin.html5-jquery-audio-player-style',  prohost_get_file_url('css/plugin.html5-jquery-audio-player.css'), array(), null );
		}
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check HTML5 jQuery Audio Player in the required plugins
if ( !function_exists( 'prohost_html5_jquery_audio_player_importer_required_plugins' ) ) {
	//add_filter( 'prohost_filter_importer_required_plugins',	'prohost_html5_jquery_audio_player_importer_required_plugins', 10, 2 );
	function prohost_html5_jquery_audio_player_importer_required_plugins($not_installed='', $importer=null) {
		//if ($importer && in_array('html5_jquery_audio_player', $importer->options['required_plugins']) && !prohost_exists_html5_jquery_audio_player() )
		if (prohost_strpos($list, 'html5_jquery_audio_player')!==false && !prohost_exists_html5_jquery_audio_player() )
			$not_installed .= '<br>HTML5 jQuery Audio Player';
		return $not_installed;
	}
}


// Set options for one-click importer
if ( !function_exists( 'prohost_html5_jquery_audio_player_importer_set_options' ) ) {
    //add_filter( 'prohost_filter_importer_options',	'prohost_html5_jquery_audio_player_importer_set_options', 10, 1 );
    function prohost_html5_jquery_audio_player_importer_set_options($options=array()) {
		if ( in_array('html5_jquery_audio_player', prohost_storage_get('required_plugins')) && prohost_exists_html5_jquery_audio_player() ) {
            $options['file_with_html5_jquery_audio_player'] = 'demo/html5_jquery_audio_player.txt';			// Name of the file with HTML5 jQuery Audio Player data
            $options['additional_options'][] = 'showbuy';		// Add slugs to export options for this plugin
            $options['additional_options'][] = 'buy_text';		// Add slugs to export options for this plugin
            $options['additional_options'][] = 'showlist';		// Add slugs to export options for this plugin
            $options['additional_options'][] = 'autoplay';		// Add slugs to export options for this plugin
            $options['additional_options'][] = 'tracks';		// Add slugs to export options for this plugin
            $options['additional_options'][] = 'currency';		// Add slugs to export options for this plugin
            $options['additional_options'][] = 'color';		    // Add slugs to export options for this plugin
            $options['additional_options'][] = 'tcolor';		// Add slugs to export options for this plugin
        }
        return $options;
    }
}

// Add checkbox to the one-click importer
if ( !function_exists( 'prohost_html5_jquery_audio_player_importer_show_params' ) ) {
    //add_action( 'prohost_action_importer_params',	'prohost_html5_jquery_audio_player_importer_show_params', 10, 1 );
    function prohost_html5_jquery_audio_player_importer_show_params($importer) {
        ?>
        <input type="checkbox" <?php echo in_array('html5_jquery_audio_player', prohost_storage_get('required_plugins')) && $importer->options['plugins_initial_state']
											? 'checked="checked"' 
											: ''; ?> value="1" name="import_html5_jquery_audio_player" id="import_html5_jquery_audio_player" /> <label for="import_html5_jquery_audio_player"><?php esc_html_e('Import HTML5 jQuery Audio Player', 'prohost'); ?></label><br>
    <?php
    }
}


// Import posts
if ( !function_exists( 'prohost_html5_jquery_audio_player_importer_import' ) ) {
    //add_action( 'prohost_action_importer_import',	'prohost_html5_jquery_audio_player_importer_import', 10, 2 );
    function prohost_html5_jquery_audio_player_importer_import($importer, $action) {
		if ( $action == 'import_html5_jquery_audio_player' ) {
            $importer->import_dump('html5_jquery_audio_player', esc_html__('HTML5 jQuery Audio Player', 'prohost'));
        }
    }
}

// Display import progress
if ( !function_exists( 'prohost_html5_jquery_audio_player_importer_import_fields' ) ) {
	//add_action( 'prohost_action_importer_import_fields',	'prohost_html5_jquery_audio_player_importer_import_fields', 10, 1 );
	function prohost_html5_jquery_audio_player_importer_import_fields($importer) {
		?>
		<tr class="import_html5_jquery_audio_player">
			<td class="import_progress_item"><?php esc_html_e('HTML5 jQuery Audio Player', 'prohost'); ?></td>
			<td class="import_progress_status"></td>
		</tr>
		<?php
	}
}


// Export posts
if ( !function_exists( 'prohost_html5_jquery_audio_player_importer_export' ) ) {
    //add_action( 'prohost_action_importer_export',	'prohost_html5_jquery_audio_player_importer_export', 10, 1 );
    function prohost_html5_jquery_audio_player_importer_export($importer) {
		prohost_storage_set('export_html5_jquery_audio_player', serialize( array(
			'hmp_playlist'	=> $importer->export_dump('hmp_playlist'),
			'hmp_rating'	=> $importer->export_dump('hmp_rating')
			) )
		);
    }
}


// Display exported data in the fields
if ( !function_exists( 'prohost_html5_jquery_audio_player_importer_export_fields' ) ) {
    //add_action( 'prohost_action_importer_export_fields',	'prohost_html5_jquery_audio_player_importer_export_fields', 10, 1 );
    function prohost_html5_jquery_audio_player_importer_export_fields($importer) {
        ?>
        <tr>
            <th align="left"><?php esc_html_e('HTML5 jQuery Audio Player', 'prohost'); ?></th>
            <td><?php prohost_fpc(prohost_get_file_dir('core/core.importer/export/html5_jquery_audio_player.txt'), prohost_storage_get('export_html5_jquery_audio_player')); ?>
                <a download="html5_jquery_audio_player.txt" href="<?php echo esc_url(prohost_get_file_url('core/core.importer/export/html5_jquery_audio_player.txt')); ?>"><?php esc_html_e('Download', 'prohost'); ?></a>
            </td>
        </tr>
    <?php
    }
}





// Shortcodes
//------------------------------------------------------------------------

// Register shortcode in the shortcodes list
if (!function_exists('prohost_html5_jquery_audio_player_reg_shortcodes')) {
    //add_filter('prohost_action_shortcodes_list',	'prohost_html5_jquery_audio_player_reg_shortcodes');
    function prohost_html5_jquery_audio_player_reg_shortcodes() {
		if (prohost_storage_isset('shortcodes')) {
			prohost_sc_map_after('trx_audio', 'hmp_player', array(
                "title" => esc_html__("HTML5 jQuery Audio Player", "prohost"),
                "desc" => esc_html__("Insert HTML5 jQuery Audio Player", "prohost"),
                "decorate" => true,
                "container" => false,
				"params" => array()
				)
            );
        }
    }
}


// Register shortcode in the VC shortcodes list
if (!function_exists('prohost_hmp_player_reg_shortcodes_vc')) {
    add_filter('prohost_action_shortcodes_list_vc',	'prohost_hmp_player_reg_shortcodes_vc');
    function prohost_hmp_player_reg_shortcodes_vc() {

        // ProHost HTML5 jQuery Audio Player
        vc_map( array(
            "base" => "hmp_player",
            "name" => esc_html__("HTML5 jQuery Audio Player", "prohost"),
            "description" => esc_html__("Insert HTML5 jQuery Audio Player", "prohost"),
            "category" => esc_html__('Content', 'prohost'),
            'icon' => 'icon_trx_audio',
            "class" => "trx_sc_single trx_sc_hmp_player",
            "content_element" => true,
            "is_container" => false,
            "show_settings_on_create" => false,
            "params" => array()
        ) );

        class WPBakeryShortCode_Hmp_Player extends PROHOST_VC_ShortCodeSingle {}

    }
}
?>