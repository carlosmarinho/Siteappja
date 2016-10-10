<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'prohost_template_form_2_theme_setup' ) ) {
	add_action( 'prohost_action_before_init_theme', 'prohost_template_form_2_theme_setup', 1 );
	function prohost_template_form_2_theme_setup() {
		prohost_add_template(array(
			'layout' => 'form_2',
			'mode'   => 'forms',
			'title'  => esc_html__('Contact Form 2', 'prohost')
			));
	}
}

// Template output
if ( !function_exists( 'prohost_template_form_2_output' ) ) {
	function prohost_template_form_2_output($post_options, $post_data) {
        $title = trim(prohost_storage_get('title_form_2'));
        $subtitle = trim(prohost_storage_get('subtitle_form_2'));
        $description = trim(prohost_storage_get('description_form_2'));
        $address_1 = prohost_get_theme_option('contact_address_1');
		$address_2 = prohost_get_theme_option('contact_address_2');
		$phone = prohost_get_theme_option('contact_phone');
		$fax = prohost_get_theme_option('contact_fax');
		$email = prohost_get_theme_option('contact_email');
		$open_hours = prohost_get_theme_option('contact_open_hours');
		?>
		<div class="sc_columns">
            <div class="sc_form_fields column-left">
                <?php  if (!empty($title)) { ?><div class="sc_form_title sc_item_title"><h3><?php echo trim($title) ?></h3></div><?php }?>
                <?php  if (!empty($subtitle)) { ?><div class="sc_form_subtitle sc_item_subtitle"><h6><?php echo trim($subtitle) ?></h6></div><?php }?>
                <?php  if (!empty($description)){ ?><div class="sc_form_descr sc_item_descr"><?php echo trim($description) ?></div><?php }?>
				<form <?php echo !empty($post_options['id']) ? ' id="'.esc_attr($post_options['id']).'_form"' : ''; ?> data-formtype="<?php echo esc_attr($post_options['layout']); ?>" method="post" action="<?php echo esc_url($post_options['action'] ? $post_options['action'] : admin_url('admin-ajax.php')); ?>">
					<?php prohost_sc_form_show_fields($post_options['fields']); ?>
					<div class="sc_form_info">
						<div class="sc_form_item sc_form_field label_over"><label class="required" for="sc_form_username"><?php esc_html_e('Name', 'prohost'); ?></label><input id="sc_form_username" type="text" name="username" placeholder="<?php esc_attr_e('Name *', 'prohost'); ?>"></div>
						<div class="sc_form_item sc_form_field label_over"><label class="required" for="sc_form_email"><?php esc_html_e('E-mail', 'prohost'); ?></label><input id="sc_form_email" type="text" name="email" placeholder="<?php esc_attr_e('E-mail *', 'prohost'); ?>"></div>
					</div>
					<div class="sc_form_item sc_form_message label_over"><label class="required" for="sc_form_message"><?php esc_html_e('Message', 'prohost'); ?></label><textarea id="sc_form_message" name="message" placeholder="<?php esc_attr_e('Message', 'prohost'); ?>"></textarea></div>
					<div class="sc_form_item sc_form_button"><button><?php esc_html_e('Send Message', 'prohost'); ?></button></div>
					<div class="result sc_infobox"></div>
				</form>
			</div>
            <div class="sc_form_address column-right">
                <?php  if (!empty($title)) { ?><div class="sc_form_title sc_item_title"><h3><?php echo esc_html__('Find us', 'prohost') ?></h3></div><?php }?>
                <?php  if (!empty($address_1) || !empty($address_2)) { ?>
                    <div class="sc_form_address_field">
                        <span class="sc_form_address_label"><?php esc_html_e('Address', 'prohost'); ?></span>
                        <span class="sc_form_address_data"><?php echo trim($address_1) . (!empty($address_1) && !empty($address_2) ? ', ' : '') . $address_2; ?></span>
                    </div>
                <?php  } ?>
                <?php  if (!empty($open_hours)) { ?>
                    <div class="sc_form_address_field">
                        <span class="sc_form_address_label"><?php esc_html_e('We are open', 'prohost'); ?></span>
                        <span class="sc_form_address_data"><?php echo trim($open_hours); ?></span>
                    </div>
                <?php  } ?>
                <?php  if (!empty($phone) || !empty($fax)) { ?>
                    <div class="sc_form_address_field">
                            <span class="sc_form_address_label"><?php esc_html_e('Phone', 'prohost'); ?></span>
                            <span class="sc_form_address_data"><?php echo trim($phone) . (!empty($phone) && !empty($fax) ? ', ' : '') . $fax; ?></span>
                        </div>
                <?php  } ?>
                <?php  if (!empty($email)) { ?>
                    <div class="sc_form_address_field">
                            <span class="sc_form_address_label"><?php esc_html_e('E-mail', 'prohost'); ?></span>
                            <span class="sc_form_address_data"><?php echo trim($email); ?></span>
                        </div>
                <?php  } ?>
                <?php echo do_shortcode('[trx_socials size="tiny" shape="square"][/trx_socials]'); ?>
            </div>
		</div>
		<?php
	}
}
?>