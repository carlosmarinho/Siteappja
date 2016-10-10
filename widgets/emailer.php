<?php
/**
 * Theme Widget: Emailer
 */

// Theme init
if (!function_exists('prohost_widget_emailer_theme_setup')) {
    add_action( 'prohost_action_before_init_theme', 'prohost_widget_emailer_theme_setup', 1 );
    function prohost_widget_emailer_theme_setup() {

        // Register shortcodes in the shortcodes list
        //add_action('prohost_action_shortcodes_list',	'prohost_widget_emailer_reg_shortcodes');
        if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
            add_action('prohost_action_shortcodes_list_vc','prohost_widget_emailer_reg_shortcodes_vc');
    }
}

// Load widget
if (!function_exists('prohost_widget_emailer_load')) {
    add_action( 'widgets_init', 'prohost_widget_emailer_load' );
    function prohost_widget_emailer_load() {
        register_widget( 'prohost_widget_emailer' );
    }
}

// Widget Class
class prohost_widget_emailer extends WP_Widget {

    function __construct() {
        $widget_ops = array( 'classname' => 'widget_emailer', 'description' => esc_html__('Show emailer', 'prohost') );
        parent::__construct( 'prohost_widget_emailer', esc_html__('ProHost - Emailer', 'prohost'), $widget_ops );
    }

    // Show widget
    function widget( $args, $instance ) {
        extract( $args );

        $title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '' );
        $text = isset($instance['text']) ? prohost_do_shortcode($instance['text']) : '';
        $group = isset($instance['group']) ? prohost_do_shortcode($instance['group']) : '';

        // Before widget (defined by themes)
        echo trim($before_widget);

        // Display the widget title if one was input (before and after defined by themes)
        if ($title) echo trim($before_title . $title . $after_title);

        // Display widget body
        ?>
        <div class="widget_inner">
            <?php

            if (!empty($text)) {
                ?>
                <div class="logo_descr"><?php echo nl2br(do_shortcode($text)); ?></div>
                <?php
            }

            echo prohost_do_shortcode('[trx_emailer group="' . $group . '"]');

            ?>
        </div>

        <?php
        // After widget (defined by themes)
        echo trim($after_widget);
    }

    // Update the widget settings.
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['text'] = $new_instance['text'];
        $instance['group'] = $new_instance['group'];
        return $instance;
    }

    // Displays the widget settings controls on the widget panel.
    function form( $instance ) {

        // Set up some default widget settings
        $instance = wp_parse_args( (array) $instance, array(
                'title' => '',
                'text' => '',
                'group' => '',
            )
        );
        $title = $instance['title'];
        $text = $instance['text'];
        $group = $instance['group'];
        ?>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e('Title:', 'prohost'); ?></label>
            <input id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($instance['title']); ?>" class="widgets_param_fullwidth" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'text' )); ?>"><?php esc_html_e('Description:', 'prohost'); ?></label>
            <textarea id="<?php echo esc_attr($this->get_field_id( 'text' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'text' )); ?>" class="widgets_param_fullwidth"><?php echo htmlspecialchars($instance['text']); ?></textarea>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'group' )); ?>"><?php esc_html_e('Group:', 'prohost'); ?></label>
            <textarea id="<?php echo esc_attr($this->get_field_id( 'group' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'group' )); ?>" class="widgets_param_fullwidth"><?php echo htmlspecialchars($instance['group']); ?></textarea>
        </p>


        <?php
    }
}



// trx_widget_emailer
//-------------------------------------------------------------
/*
[trx_widget_emailer title="Widget title" text="Description" group=""]
*/
if ( !function_exists( 'prohost_sc_widget_emailer' ) ) {
    function prohost_sc_widget_emailer($atts, $content=null){
        $atts = prohost_html_decode(shortcode_atts(array(
            // Individual params
            "title" => "",
            "text" => "",
            "group" => "",
            // Common params
            "id" => "",
            "class" => "",
            "css" => ""
        ), $atts));
        extract($atts);
        $type = 'prohost_widget_emailer';
        $output = '';
        global $wp_widget_factory;
        if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
            $output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
                . ' class="widget_area sc_widget_emailer'
                . (prohost_exists_visual_composer() ? ' vc_widget_emailer wpb_content_element' : '')
                . (!empty($class) ? ' ' . esc_attr($class) : '')
                . '">';
            ob_start();
            the_widget( $type, $atts, prohost_prepare_widgets_args(prohost_storage_get('widgets_args'), $id ? $id.'_widget' : 'widget_emailer', 'widget_emailer') );
            $output .= ob_get_contents();
            ob_end_clean();
            $output .= '</div>';
        }
        return apply_filters('prohost_shortcode_output', $output, 'trx_widget_emailer', $atts, $content);
    }
    prohost_require_shortcode("trx_widget_emailer", "prohost_sc_widget_emailer");
}


// Add [trx_widget_emailer] in the VC shortcodes list
if (!function_exists('prohost_widget_emailer_reg_shortcodes_vc')) {
    function prohost_widget_emailer_reg_shortcodes_vc() {

        vc_map( array(
            "base" => "trx_widget_emailer",
            "name" => esc_html__("Widget Socials", "prohost"),
            "description" => wp_kses_data( __("Insert site logo, description and/or socials list", "prohost") ),
            "category" => esc_html__('Content', 'prohost'),
            "icon" => 'icon_trx_widget_emailer',
            "class" => "trx_widget_emailer",
            "content_element" => true,
            "is_container" => false,
            "show_settings_on_create" => true,
            "params" => array(
                array(
                    "param_name" => "title",
                    "heading" => esc_html__("Widget title", "prohost"),
                    "description" => wp_kses_data( __("Title of the widget", "prohost") ),
                    "admin_label" => true,
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "text",
                    "heading" => esc_html__("Widget text", "prohost"),
                    "description" => wp_kses_data( __("Any description", "prohost") ),
                    "class" => "",
                    "value" => "",
                    "type" => "textarea"
                ),
                array(
                    "param_name" => "group",
                    "heading" => esc_html__("Group", "prohost"),
                    "description" => wp_kses_data( __("The name of group to collect e-mail address", "prohost") ),
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                prohost_get_vc_param('id'),
                prohost_get_vc_param('class'),
                prohost_get_vc_param('css')
            )
        ) );

        class WPBakeryShortCode_Trx_widget_emailer extends WPBakeryShortCode {}

    }
}
?>