<?php
/**
 * ProHost Framework: Services support
 *
 * @package	prohost
 * @since	prohost 1.0
 */

// Theme init
if (!function_exists('prohost_services_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_services_theme_setup',1 );
	function prohost_services_theme_setup() {
		
		// Detect current page type, taxonomy and title (for custom post_types use priority < 10 to fire it handles early, than for standard post types)
		add_filter('prohost_filter_get_blog_type',			'prohost_services_get_blog_type', 9, 2);
		add_filter('prohost_filter_get_blog_title',		'prohost_services_get_blog_title', 9, 2);
		add_filter('prohost_filter_get_current_taxonomy',	'prohost_services_get_current_taxonomy', 9, 2);
		add_filter('prohost_filter_is_taxonomy',			'prohost_services_is_taxonomy', 9, 2);
		add_filter('prohost_filter_get_stream_page_title',	'prohost_services_get_stream_page_title', 9, 2);
		add_filter('prohost_filter_get_stream_page_link',	'prohost_services_get_stream_page_link', 9, 2);
		add_filter('prohost_filter_get_stream_page_id',	'prohost_services_get_stream_page_id', 9, 2);
		add_filter('prohost_filter_query_add_filters',		'prohost_services_query_add_filters', 9, 2);
		add_filter('prohost_filter_detect_inheritance_key','prohost_services_detect_inheritance_key', 9, 1);

		// Extra column for services lists
		if (prohost_get_theme_option('show_overriden_posts')=='yes') {
			add_filter('manage_edit-services_columns',			'prohost_post_add_options_column', 9);
			add_filter('manage_services_posts_custom_column',	'prohost_post_fill_options_column', 9, 2);
		}

		// Register shortcodes [trx_services] and [trx_services_item]
		add_action('prohost_action_shortcodes_list',		'prohost_services_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_services_reg_shortcodes_vc');
		
		// Add supported data types
		prohost_theme_support_pt('services');
		prohost_theme_support_tx('services_group');
	}
}

if ( !function_exists( 'prohost_services_settings_theme_setup2' ) ) {
	add_action( 'prohost_action_before_init_theme', 'prohost_services_settings_theme_setup2', 3 );
	function prohost_services_settings_theme_setup2() {
		// Add post type 'services' and taxonomy 'services_group' into theme inheritance list
		prohost_add_theme_inheritance( array('services' => array(
			'stream_template' => 'blog-services',
			'single_template' => 'single-service',
			'taxonomy' => array('services_group'),
			'taxonomy_tags' => array(),
			'post_type' => array('services'),
			'override' => 'post'
			) )
		);
	}
}



// Return true, if current page is services page
if ( !function_exists( 'prohost_is_services_page' ) ) {
	function prohost_is_services_page() {
		$is = in_array(prohost_storage_get('page_template'), array('blog-services', 'single-service'));
		if (!$is) {
			if (!prohost_storage_empty('pre_query'))
				$is = prohost_storage_call_obj_method('pre_query', 'get', 'post_type')=='services' 
						|| prohost_storage_call_obj_method('pre_query', 'is_tax', 'services_group') 
						|| (prohost_storage_call_obj_method('pre_query', 'is_page') 
								&& ($id=prohost_get_template_page_id('blog-services')) > 0 
								&& $id==prohost_storage_get_obj_property('pre_query', 'queried_object_id', 0) 
							);
			else
				$is = get_query_var('post_type')=='services' 
						|| is_tax('services_group') 
						|| (is_page() && ($id=prohost_get_template_page_id('blog-services')) > 0 && $id==get_the_ID());
		}
		return $is;
	}
}

// Filter to detect current page inheritance key
if ( !function_exists( 'prohost_services_detect_inheritance_key' ) ) {
	//add_filter('prohost_filter_detect_inheritance_key',	'prohost_services_detect_inheritance_key', 9, 1);
	function prohost_services_detect_inheritance_key($key) {
		if (!empty($key)) return $key;
		return prohost_is_services_page() ? 'services' : '';
	}
}

// Filter to detect current page slug
if ( !function_exists( 'prohost_services_get_blog_type' ) ) {
	//add_filter('prohost_filter_get_blog_type',	'prohost_services_get_blog_type', 9, 2);
	function prohost_services_get_blog_type($page, $query=null) {
		if (!empty($page)) return $page;
		if ($query && $query->is_tax('services_group') || is_tax('services_group'))
			$page = 'services_category';
		else if ($query && $query->get('post_type')=='services' || get_query_var('post_type')=='services')
			$page = $query && $query->is_single() || is_single() ? 'services_item' : 'services';
		return $page;
	}
}

// Filter to detect current page title
if ( !function_exists( 'prohost_services_get_blog_title' ) ) {
	//add_filter('prohost_filter_get_blog_title',	'prohost_services_get_blog_title', 9, 2);
	function prohost_services_get_blog_title($title, $page) {
		if (!empty($title)) return $title;
		if ( prohost_strpos($page, 'services')!==false ) {
			if ( $page == 'services_category' ) {
				$term = get_term_by( 'slug', get_query_var( 'services_group' ), 'services_group', OBJECT);
				$title = $term->name;
			} else if ( $page == 'services_item' ) {
				$title = prohost_get_post_title();
			} else {
				$title = esc_html__('All services', 'prohost');
			}
		}
		return $title;
	}
}

// Filter to detect stream page title
if ( !function_exists( 'prohost_services_get_stream_page_title' ) ) {
	//add_filter('prohost_filter_get_stream_page_title',	'prohost_services_get_stream_page_title', 9, 2);
	function prohost_services_get_stream_page_title($title, $page) {
		if (!empty($title)) return $title;
		if (prohost_strpos($page, 'services')!==false) {
			if (($page_id = prohost_services_get_stream_page_id(0, $page=='services' ? 'blog-services' : $page)) > 0)
				$title = prohost_get_post_title($page_id);
			else
				$title = esc_html__('All services', 'prohost');				
		}
		return $title;
	}
}

// Filter to detect stream page ID
if ( !function_exists( 'prohost_services_get_stream_page_id' ) ) {
	//add_filter('prohost_filter_get_stream_page_id',	'prohost_services_get_stream_page_id', 9, 2);
	function prohost_services_get_stream_page_id($id, $page) {
		if (!empty($id)) return $id;
		if (prohost_strpos($page, 'services')!==false) $id = prohost_get_template_page_id('blog-services');
		return $id;
	}
}

// Filter to detect stream page URL
if ( !function_exists( 'prohost_services_get_stream_page_link' ) ) {
	//add_filter('prohost_filter_get_stream_page_link',	'prohost_services_get_stream_page_link', 9, 2);
	function prohost_services_get_stream_page_link($url, $page) {
		if (!empty($url)) return $url;
		if (prohost_strpos($page, 'services')!==false) {
			$id = prohost_get_template_page_id('blog-services');
			if ($id) $url = get_permalink($id);
		}
		return $url;
	}
}

// Filter to detect current taxonomy
if ( !function_exists( 'prohost_services_get_current_taxonomy' ) ) {
	//add_filter('prohost_filter_get_current_taxonomy',	'prohost_services_get_current_taxonomy', 9, 2);
	function prohost_services_get_current_taxonomy($tax, $page) {
		if (!empty($tax)) return $tax;
		if ( prohost_strpos($page, 'services')!==false ) {
			$tax = 'services_group';
		}
		return $tax;
	}
}

// Return taxonomy name (slug) if current page is this taxonomy page
if ( !function_exists( 'prohost_services_is_taxonomy' ) ) {
	//add_filter('prohost_filter_is_taxonomy',	'prohost_services_is_taxonomy', 9, 2);
	function prohost_services_is_taxonomy($tax, $query=null) {
		if (!empty($tax))
			return $tax;
		else 
			return $query && $query->get('services_group')!='' || is_tax('services_group') ? 'services_group' : '';
	}
}

// Add custom post type and/or taxonomies arguments to the query
if ( !function_exists( 'prohost_services_query_add_filters' ) ) {
	//add_filter('prohost_filter_query_add_filters',	'prohost_services_query_add_filters', 9, 2);
	function prohost_services_query_add_filters($args, $filter) {
		if ($filter == 'services') {
			$args['post_type'] = 'services';
		}
		return $args;
	}
}





// ---------------------------------- [trx_services] ---------------------------------------

/*
[trx_services id="unique_id" columns="4" count="4" style="services-1|services-2|..." title="Block title" subtitle="xxx" description="xxxxxx"]
	[trx_services_item icon="url" title="Item title" description="Item description" link="url" link_caption="Link text"]
	[trx_services_item icon="url" title="Item title" description="Item description" link="url" link_caption="Link text"]
[/trx_services]
*/
if ( !function_exists( 'prohost_sc_services' ) ) {
	function prohost_sc_services($atts, $content=null)
    {
        if (prohost_in_shortcode_blogger()) return '';
        extract(prohost_html_decode(shortcode_atts(array(
            // Individual params
            "style" => "services-1",
            "columns" => 4,
            "slider" => "no",
            "slides_space" => 0,
            "controls" => "no",
            "interval" => "",
            "autoheight" => "no",
            "align" => "",
            "custom" => "no",
            "type" => "icons",    // icons | images
            "ids" => "",
            "cat" => "",
            "count" => 4,
            "offset" => "",
            "orderby" => "date",
            "order" => "desc",
            "readmore" => esc_html__('Read more', 'prohost'),
            "title" => "",
            "subtitle" => "",
            "description" => "",
            "link_caption" => esc_html__('Read more', 'prohost'),
            "link" => '',
            "scheme" => '',
            "image" => '',
            "image_align" => '',
            // Common params
            "id" => "",
            "class" => "",
            "animation" => "",
            "css" => "",
            "width" => "",
            "height" => "",
            "top" => "",
            "bottom" => "",
            "left" => "",
            "right" => ""
        ), $atts)));

        if (prohost_param_is_off($slider) && $columns > 1 && $style == 'services-5' && !empty($image)) $columns = 2;
        if (!empty($image)) {
            if ($image > 0) {
                $attach = wp_get_attachment_image_src($image, 'full');
                if (isset($attach[0]) && $attach[0] != '')
                    $image = $attach[0];
            }
        }

        if (empty($id)) $id = "sc_services_" . str_replace('.', '', mt_rand());
        if (empty($width)) $width = "100%";
        if (!empty($height) && prohost_param_is_on($autoheight)) $autoheight = "no";
        if (empty($interval)) $interval = mt_rand(5000, 10000);

        $class .= ($class ? ' ' : '') . prohost_get_css_position_as_classes($top, $right, $bottom, $left);

        $ws = prohost_get_css_dimensions_from_values($width);
        $hs = prohost_get_css_dimensions_from_values('', $height);
        $css .= ($hs) . ($ws);

        $count = max(1, (int)$count);
        $columns = max(1, min(12, (int)$columns));
        if (prohost_param_is_off($custom) && $count < $columns) $columns = $count;

        if (prohost_param_is_on($slider)) prohost_enqueue_slider('swiper');

        prohost_storage_set('sc_services_data', array(
                'id' => $id,
                'style' => $style,
                'type' => $type,
                'columns' => $columns,
                'counter' => 0,
                'slider' => $slider,
                'css_wh' => $ws . $hs,
                'readmore' => $readmore
            )
        );

        $output = '<div' . ($id ? ' id="' . esc_attr($id) . '_wrap"' : '')
            . ' class="sc_services_wrap'
            . ($scheme && !prohost_param_is_off($scheme) && !prohost_param_is_inherit($scheme) ? ' scheme_' . esc_attr($scheme) : '')
            . '">'
            . '<div' . ($id ? ' id="' . esc_attr($id) . '"' : '')
            . ' class="sc_services'
            . ' sc_services_style_' . esc_attr($style)
            . ' sc_services_type_' . esc_attr($type)
            . ' ' . esc_attr(prohost_get_template_property($style, 'container_classes'))
            . ' ' . esc_attr(prohost_get_slider_controls_classes($controls))
            . (prohost_param_is_on($slider)
                ? ' sc_slider_swiper swiper-slider-container'
                . (prohost_param_is_on($autoheight) ? ' sc_slider_height_auto' : '')
                . ($hs ? ' sc_slider_height_fixed' : '')
                : '')
            . (!empty($class) ? ' ' . esc_attr($class) : '')
            . ($align != '' && $align != 'none' ? ' align' . esc_attr($align) : '')
            . '"'
            . ($css != '' ? ' style="' . esc_attr($css) . '"' : '')
            . (!empty($width) && prohost_strpos($width, '%') === false ? ' data-old-width="' . esc_attr($width) . '"' : '')
            . (!empty($height) && prohost_strpos($height, '%') === false ? ' data-old-height="' . esc_attr($height) . '"' : '')
            . ((int)$interval > 0 ? ' data-interval="' . esc_attr($interval) . '"' : '')
            . ($columns > 1 ? ' data-slides-per-view="' . esc_attr($columns) . '"' : '')
            . ($slides_space > 0 ? ' data-slides-space="' . esc_attr($slides_space) . '"' : '')
            . ' data-slides-min-width="250"'
            . (!prohost_param_is_off($animation) ? ' data-animation="' . esc_attr(prohost_get_animation_classes($animation)) . '"' : '')
            . '>'
            . (!empty($subtitle) ? '<h6 class="sc_services_subtitle sc_item_subtitle">' . trim(prohost_strmacros($subtitle)) . '</h6>' : '')
            . (!empty($title) ? '<h2 class="sc_services_title sc_item_title">' . trim(prohost_strmacros($title)) . '</h2>' : '')
            . (!empty($description) ? '<div class="sc_services_descr sc_item_descr">' . trim(prohost_strmacros($description)) . '</div>' : '')
            . (prohost_param_is_on($slider)
                ? '<div class="slides swiper-wrapper">'
                : ($columns > 1
                    ? ($style == 'services-5' && !empty($image)
                        ? '<div class="sc_service_container sc_align_' . esc_attr($image_align) . '">'
                        . '<div class="sc_services_image"><img src="' . esc_url($image) . '" alt=""></div>'
                        : '')
                    . '<div class="sc_columns columns_wrap">'
                    : '')


            );

        $content = do_shortcode($content);

        if (prohost_param_is_on($custom) && $content) {
            $output .= $content;
        } else {
            global $post;

            if (!empty($ids)) {
                $posts = explode(',', $ids);
                $count = count($posts);
            }

            $args = array(
                'post_type' => 'services',
                'post_status' => 'publish',
                'posts_per_page' => $count,
                'ignore_sticky_posts' => true,
                'order' => $order == 'asc' ? 'asc' : 'desc',
                'readmore' => $readmore
            );

            if ($offset > 0 && empty($ids)) {
                $args['offset'] = $offset;
            }

            $args = prohost_query_add_sort_order($args, $orderby, $order);
            $args = prohost_query_add_posts_and_cats($args, $ids, 'services', $cat, 'services_group');
            $query = new WP_Query($args);

            $post_number = 0;

            while ($query->have_posts()) {
                $query->the_post();
                $post_number++;
                $args = array(
                    'layout' => $style,
                    'show' => false,
                    'number' => $post_number,
                    'posts_on_page' => ($count > 0 ? $count : $query->found_posts),
                    "descr" => prohost_get_custom_option('post_excerpt_maxlength' . ($columns > 1 ? '_masonry' : '')),
                    "orderby" => $orderby,
                    'content' => false,
                    'terms_list' => false,
                    'readmore' => $readmore,
                    'tag_type' => $type,
                    'columns_count' => $columns,
                    'slider' => $slider,
                    'tag_id' => $id ? $id . '_' . $post_number : '',
                    'tag_class' => '',
                    'tag_animation' => '',
                    'tag_css' => '',
                    'tag_css_wh' => $ws . $hs
                );
                $output .= prohost_show_post_layout($args);
            }
            wp_reset_postdata();
        }




		if (prohost_param_is_on($slider)) {
			$output .= '</div>'
				. '<div class="sc_slider_controls_wrap"><a class="sc_slider_prev" href="#"></a><a class="sc_slider_next" href="#"></a></div>'
				. '<div class="sc_slider_pagination_wrap"></div>';
		} else if ($columns > 1) {
			$output .= '</div>';
			if ($style == 'services-5' && !empty($image))
				$output .= '</div>';
		}

		$output .=  (!empty($link) ? '<div class="sc_services_button sc_item_button">'.prohost_do_shortcode('[trx_button link="'.esc_url($link).'" size="small"]'.esc_html($link_caption).'[/trx_button]').'</div>' : '')
					. '</div><!-- /.sc_services -->'
				. '</div><!-- /.sc_services_wrap -->';
	
		// Add template specific scripts and styles
		do_action('prohost_action_blog_scripts', $style);
	
		return apply_filters('prohost_shortcode_output', $output, 'trx_services', $atts, $content);
	}
	prohost_require_shortcode('trx_services', 'prohost_sc_services');
}


if ( !function_exists( 'prohost_sc_services_item' ) ) {
	function prohost_sc_services_item($atts, $content=null) {
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts( array(
			// Individual params
			"icon" => "",
			"image" => "",
			"title" => "",
			"link" => "",
			"readmore" => "(none)",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => ""
		), $atts)));
	
		prohost_storage_inc_array('sc_services_data', 'counter');

		$id = $id ? $id : (prohost_storage_get_array('sc_services_data', 'id') ? prohost_storage_get_array('sc_services_data', 'id') . '_' . prohost_storage_get_array('sc_services_data', 'counter') : '');

		$descr = trim(chop(do_shortcode($content)));
		$readmore = $readmore=='(none)' ? prohost_storage_get_array('sc_services_data', 'readmore') : $readmore;

		$type = prohost_storage_get_array('sc_services_data', 'type');
		if (!empty($icon)) {
			$type = 'icons';
		} else if (!empty($image)) {
			$type = 'images';
			if ($image > 0) {
				$attach = wp_get_attachment_image_src( $image, 'full' );
				if (isset($attach[0]) && $attach[0]!='')
					$image = $attach[0];
			}
			$thumb_sizes = prohost_get_thumb_sizes(array('layout' => prohost_storage_get_array('sc_services_data', 'style')));
			$image = prohost_get_resized_image_tag($image, $thumb_sizes['w'], $thumb_sizes['h']);
		}
	
		$post_data = array(
			'post_title' => $title,
			'post_excerpt' => $descr,
			'post_thumb' => $image,
			'post_icon' => $icon,
			'post_link' => $link,
			'post_protected' => false,
			'post_format' => 'standard'
		);
		$args = array(
			'layout' => prohost_storage_get_array('sc_services_data', 'style'),
			'number' => prohost_storage_get_array('sc_services_data', 'counter'),
			'columns_count' => prohost_storage_get_array('sc_services_data', 'columns'),
			'slider' => prohost_storage_get_array('sc_services_data', 'slider'),
			'show' => false,
			'descr'  => -1,		// -1 - don't strip tags, 0 - strip_tags, >0 - strip_tags and truncate string
			'readmore' => $readmore,
			'tag_type' => $type,
			'tag_id' => $id,
			'tag_class' => $class,
			'tag_animation' => $animation,
			'tag_css' => $css,
			'tag_css_wh' => prohost_storage_get_array('sc_services_data', 'css_wh')
		);
		$output = prohost_show_post_layout($args, $post_data);
		return apply_filters('prohost_shortcode_output', $output, 'trx_services_item', $atts, $content);
	}
	prohost_require_shortcode('trx_services_item', 'prohost_sc_services_item');
}
// ---------------------------------- [/trx_services] ---------------------------------------



// Add [trx_services] and [trx_services_item] in the shortcodes list
if (!function_exists('prohost_services_reg_shortcodes')) {
	//add_filter('prohost_action_shortcodes_list',	'prohost_services_reg_shortcodes');
	function prohost_services_reg_shortcodes() {
		if (prohost_storage_isset('shortcodes')) {

			$services_groups = prohost_get_list_terms(false, 'services_group');
			$services_styles = prohost_get_list_templates('services');
			$controls 		 = prohost_get_list_slider_controls();

			prohost_sc_map_after('trx_section', array(

				// Services
				"trx_services" => array(
					"title" => esc_html__("Services", "prohost"),
					"desc" => wp_kses_data( __("Insert services list in your page (post)", "prohost") ),
					"decorate" => true,
					"container" => false,
					"params" => array(
						"title" => array(
							"title" => esc_html__("Title", "prohost"),
							"desc" => wp_kses_data( __("Title for the block", "prohost") ),
							"value" => "",
							"type" => "text"
						),
						"subtitle" => array(
							"title" => esc_html__("Subtitle", "prohost"),
							"desc" => wp_kses_data( __("Subtitle for the block", "prohost") ),
							"value" => "",
							"type" => "text"
						),
						"description" => array(
							"title" => esc_html__("Description", "prohost"),
							"desc" => wp_kses_data( __("Short description for the block", "prohost") ),
							"value" => "",
							"type" => "textarea"
						),
						"style" => array(
							"title" => esc_html__("Services style", "prohost"),
							"desc" => wp_kses_data( __("Select style to display services list", "prohost") ),
							"value" => "services-1",
							"type" => "select",
							"options" => $services_styles
						),
						"image" => array(
								"title" => esc_html__("Item's image", "prohost"),
								"desc" => wp_kses_data( __("Item's image", "prohost") ),
								"dependency" => array(
									'style' => 'services-5'
								),
								"value" => "",
								"readonly" => false,
								"type" => "media"
						),
						"image_align" => array(
							"title" => esc_html__("Image alignment", "prohost"),
							"desc" => wp_kses_data( __("Alignment of the image", "prohost") ),
							"divider" => true,
							"value" => "",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => prohost_get_sc_param('align')
						),
						"type" => array(
							"title" => esc_html__("Icon's type", "prohost"),
							"desc" => wp_kses_data( __("Select type of icons: font icon or image", "prohost") ),
							"value" => "icons",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => array(
								'icons'  => esc_html__('Icons', 'prohost'),
								'images' => esc_html__('Images', 'prohost')
							)
						),
						"columns" => array(
							"title" => esc_html__("Columns", "prohost"),
							"desc" => wp_kses_data( __("How many columns use to show services list", "prohost") ),
							"value" => 4,
							"min" => 2,
							"max" => 6,
							"step" => 1,
							"type" => "spinner"
						),
						"scheme" => array(
							"title" => esc_html__("Color scheme", "prohost"),
							"desc" => wp_kses_data( __("Select color scheme for this block", "prohost") ),
							"value" => "",
							"type" => "checklist",
							"options" => prohost_get_sc_param('schemes')
						),
						"slider" => array(
							"title" => esc_html__("Slider", "prohost"),
							"desc" => wp_kses_data( __("Use slider to show services", "prohost") ),
							"value" => "no",
							"type" => "switch",
							"options" => prohost_get_sc_param('yes_no')
						),
						"controls" => array(
							"title" => esc_html__("Controls", "prohost"),
							"desc" => wp_kses_data( __("Slider controls style and position", "prohost") ),
							"dependency" => array(
								'slider' => array('yes')
							),
							"divider" => true,
							"value" => "",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $controls
						),
						"slides_space" => array(
							"title" => esc_html__("Space between slides", "prohost"),
							"desc" => wp_kses_data( __("Size of space (in px) between slides", "prohost") ),
							"dependency" => array(
								'slider' => array('yes')
							),
							"value" => 0,
							"min" => 0,
							"max" => 100,
							"step" => 10,
							"type" => "spinner"
						),
						"interval" => array(
							"title" => esc_html__("Slides change interval", "prohost"),
							"desc" => wp_kses_data( __("Slides change interval (in milliseconds: 1000ms = 1s)", "prohost") ),
							"dependency" => array(
								'slider' => array('yes')
							),
							"value" => 7000,
							"step" => 500,
							"min" => 0,
							"type" => "spinner"
						),
						"autoheight" => array(
							"title" => esc_html__("Autoheight", "prohost"),
							"desc" => wp_kses_data( __("Change whole slider's height (make it equal current slide's height)", "prohost") ),
							"dependency" => array(
								'slider' => array('yes')
							),
							"value" => "yes",
							"type" => "switch",
							"options" => prohost_get_sc_param('yes_no')
						),
						"align" => array(
							"title" => esc_html__("Alignment", "prohost"),
							"desc" => wp_kses_data( __("Alignment of the services block", "prohost") ),
							"divider" => true,
							"value" => "",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => prohost_get_sc_param('align')
						),
						"custom" => array(
							"title" => esc_html__("Custom", "prohost"),
							"desc" => wp_kses_data( __("Allow get services items from inner shortcodes (custom) or get it from specified group (cat)", "prohost") ),
							"divider" => true,
							"value" => "no",
							"type" => "switch",
							"options" => prohost_get_sc_param('yes_no')
						),
						"cat" => array(
							"title" => esc_html__("Categories", "prohost"),
							"desc" => wp_kses_data( __("Select categories (groups) to show services list. If empty - select services from any category (group) or from IDs list", "prohost") ),
							"dependency" => array(
								'custom' => array('no')
							),
							"divider" => true,
							"value" => "",
							"type" => "select",
							"style" => "list",
							"multiple" => true,
							"options" => prohost_array_merge(array(0 => esc_html__('- Select category -', 'prohost')), $services_groups)
						),
						"count" => array(
							"title" => esc_html__("Number of posts", "prohost"),
							"desc" => wp_kses_data( __("How many posts will be displayed? If used IDs - this parameter ignored.", "prohost") ),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => 4,
							"min" => 1,
							"max" => 100,
							"type" => "spinner"
						),
						"offset" => array(
							"title" => esc_html__("Offset before select posts", "prohost"),
							"desc" => wp_kses_data( __("Skip posts before select next part.", "prohost") ),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => 0,
							"min" => 0,
							"type" => "spinner"
						),
						"orderby" => array(
							"title" => esc_html__("Post order by", "prohost"),
							"desc" => wp_kses_data( __("Select desired posts sorting method", "prohost") ),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => "title",
							"type" => "select",
							"options" => prohost_get_sc_param('sorting')
						),
						"order" => array(
							"title" => esc_html__("Post order", "prohost"),
							"desc" => wp_kses_data( __("Select desired posts order", "prohost") ),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => "asc",
							"type" => "switch",
							"size" => "big",
							"options" => prohost_get_sc_param('ordering')
						),
						"ids" => array(
							"title" => esc_html__("Post IDs list", "prohost"),
							"desc" => wp_kses_data( __("Comma separated list of posts ID. If set - parameters above are ignored!", "prohost") ),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => "",
							"type" => "text"
						),
						"readmore" => array(
							"title" => esc_html__("Read more", "prohost"),
							"desc" => wp_kses_data( __("Caption for the Read more link (if empty - link not showed)", "prohost") ),
							"value" => "",
							"type" => "text"
						),
						"link" => array(
							"title" => esc_html__("Button URL", "prohost"),
							"desc" => wp_kses_data( __("Link URL for the button at the bottom of the block", "prohost") ),
							"value" => "",
							"type" => "text"
						),
						"link_caption" => array(
							"title" => esc_html__("Button caption", "prohost"),
							"desc" => wp_kses_data( __("Caption for the button at the bottom of the block", "prohost") ),
							"value" => "",
							"type" => "text"
						),
						"width" => prohost_shortcodes_width(),
						"height" => prohost_shortcodes_height(),
						"top" => prohost_get_sc_param('top'),
						"bottom" => prohost_get_sc_param('bottom'),
						"left" => prohost_get_sc_param('left'),
						"right" => prohost_get_sc_param('right'),
						"id" => prohost_get_sc_param('id'),
						"class" => prohost_get_sc_param('class'),
						"animation" => prohost_get_sc_param('animation'),
						"css" => prohost_get_sc_param('css')
					),
					"children" => array(
						"name" => "trx_services_item",
						"title" => esc_html__("Service item", "prohost"),
						"desc" => wp_kses_data( __("Service item", "prohost") ),
						"container" => true,
						"params" => array(
							"title" => array(
								"title" => esc_html__("Title", "prohost"),
								"desc" => wp_kses_data( __("Item's title", "prohost") ),
								"divider" => true,
								"value" => "",
								"type" => "text"
							),
							"icon" => array(
								"title" => esc_html__("Item's icon",  'prohost'),
								"desc" => wp_kses_data( __('Select icon for the item from Fontello icons set',  'prohost') ),
								"value" => "",
								"type" => "icons",
								"options" => prohost_get_sc_param('icons')
							),
							"image" => array(
								"title" => esc_html__("Item's image", "prohost"),
								"desc" => wp_kses_data( __("Item's image (if icon not selected)", "prohost") ),
								"dependency" => array(
									'icon' => array('is_empty', 'none')
								),
								"value" => "",
								"readonly" => false,
								"type" => "media"
							),
							"link" => array(
								"title" => esc_html__("Link", "prohost"),
								"desc" => wp_kses_data( __("Link on service's item page", "prohost") ),
								"divider" => true,
								"value" => "",
								"type" => "text"
							),
							"readmore" => array(
								"title" => esc_html__("Read more", "prohost"),
								"desc" => wp_kses_data( __("Caption for the Read more link (if empty - link not showed)", "prohost") ),
								"value" => "",
								"type" => "text"
							),
							"_content_" => array(
								"title" => esc_html__("Description", "prohost"),
								"desc" => wp_kses_data( __("Item's short description", "prohost") ),
								"divider" => true,
								"rows" => 4,
								"value" => "",
								"type" => "textarea"
							),
							"id" => prohost_get_sc_param('id'),
							"class" => prohost_get_sc_param('class'),
							"animation" => prohost_get_sc_param('animation'),
							"css" => prohost_get_sc_param('css')
						)
					)
				)

			));
		}
	}
}


// Add [trx_services] and [trx_services_item] in the VC shortcodes list
if (!function_exists('prohost_services_reg_shortcodes_vc')) {
	//add_filter('prohost_action_shortcodes_list_vc',	'prohost_services_reg_shortcodes_vc');
	function prohost_services_reg_shortcodes_vc() {

		$services_groups = prohost_get_list_terms(false, 'services_group');
		$services_styles = prohost_get_list_templates('services');
		$controls		 = prohost_get_list_slider_controls();

		// Services
		vc_map( array(
				"base" => "trx_services",
				"name" => esc_html__("Services", "prohost"),
				"description" => wp_kses_data( __("Insert services list", "prohost") ),
				"category" => esc_html__('Content', 'prohost'),
				"icon" => 'icon_trx_services',
				"class" => "trx_sc_columns trx_sc_services",
				"content_element" => true,
				"is_container" => true,
				"show_settings_on_create" => true,
				"as_parent" => array('only' => 'trx_services_item'),
				"params" => array(
					array(
						"param_name" => "style",
						"heading" => esc_html__("Services style", "prohost"),
						"description" => wp_kses_data( __("Select style to display services list", "prohost") ),
						"class" => "",
						"admin_label" => true,
						"value" => array_flip($services_styles),
						"type" => "dropdown"
					),
					array(
						"param_name" => "type",
						"heading" => esc_html__("Icon's type", "prohost"),
						"description" => wp_kses_data( __("Select type of icons: font icon or image", "prohost") ),
						"class" => "",
						"admin_label" => true,
						"value" => array(
							esc_html__('Icons', 'prohost') => 'icons',
							esc_html__('Images', 'prohost') => 'images'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "scheme",
						"heading" => esc_html__("Color scheme", "prohost"),
						"description" => wp_kses_data( __("Select color scheme for this block", "prohost") ),
						"class" => "",
						"value" => array_flip(prohost_get_sc_param('schemes')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "image",
						"heading" => esc_html__("Image", "prohost"),
						"description" => wp_kses_data( __("Item's image", "prohost") ),
						'dependency' => array(
							'element' => 'style',
							'value' => 'services-5'
						),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "image_align",
						"heading" => esc_html__("Image alignment", "prohost"),
						"description" => wp_kses_data( __("Alignment of the image", "prohost") ),
						"class" => "",
						"value" => array_flip(prohost_get_sc_param('align')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "slider",
						"heading" => esc_html__("Slider", "prohost"),
						"description" => wp_kses_data( __("Use slider to show services", "prohost") ),
						"admin_label" => true,
						"group" => esc_html__('Slider', 'prohost'),
						"class" => "",
						"std" => "no",
						"value" => array_flip(prohost_get_sc_param('yes_no')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "controls",
						"heading" => esc_html__("Controls", "prohost"),
						"description" => wp_kses_data( __("Slider controls style and position", "prohost") ),
						"admin_label" => true,
						"group" => esc_html__('Slider', 'prohost'),
						'dependency' => array(
							'element' => 'slider',
							'value' => 'yes'
						),
						"class" => "",
						"std" => "no",
						"value" => array_flip($controls),
						"type" => "dropdown"
					),
					array(
						"param_name" => "slides_space",
						"heading" => esc_html__("Space between slides", "prohost"),
						"description" => wp_kses_data( __("Size of space (in px) between slides", "prohost") ),
						"admin_label" => true,
						"group" => esc_html__('Slider', 'prohost'),
						'dependency' => array(
							'element' => 'slider',
							'value' => 'yes'
						),
						"class" => "",
						"value" => "0",
						"type" => "textfield"
					),
					array(
						"param_name" => "interval",
						"heading" => esc_html__("Slides change interval", "prohost"),
						"description" => wp_kses_data( __("Slides change interval (in milliseconds: 1000ms = 1s)", "prohost") ),
						"group" => esc_html__('Slider', 'prohost'),
						'dependency' => array(
							'element' => 'slider',
							'value' => 'yes'
						),
						"class" => "",
						"value" => "7000",
						"type" => "textfield"
					),
					array(
						"param_name" => "autoheight",
						"heading" => esc_html__("Autoheight", "prohost"),
						"description" => wp_kses_data( __("Change whole slider's height (make it equal current slide's height)", "prohost") ),
						"group" => esc_html__('Slider', 'prohost'),
						'dependency' => array(
							'element' => 'slider',
							'value' => 'yes'
						),
						"class" => "",
						"value" => array("Autoheight" => "yes" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "align",
						"heading" => esc_html__("Alignment", "prohost"),
						"description" => wp_kses_data( __("Alignment of the services block", "prohost") ),
						"class" => "",
						"value" => array_flip(prohost_get_sc_param('align')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "custom",
						"heading" => esc_html__("Custom", "prohost"),
						"description" => wp_kses_data( __("Allow get services from inner shortcodes (custom) or get it from specified group (cat)", "prohost") ),
						"class" => "",
						"value" => array("Custom services" => "yes" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "title",
						"heading" => esc_html__("Title", "prohost"),
						"description" => wp_kses_data( __("Title for the block", "prohost") ),
						"admin_label" => true,
						"group" => esc_html__('Captions', 'prohost'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "subtitle",
						"heading" => esc_html__("Subtitle", "prohost"),
						"description" => wp_kses_data( __("Subtitle for the block", "prohost") ),
						"group" => esc_html__('Captions', 'prohost'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "description",
						"heading" => esc_html__("Description", "prohost"),
						"description" => wp_kses_data( __("Description for the block", "prohost") ),
						"group" => esc_html__('Captions', 'prohost'),
						"class" => "",
						"value" => "",
						"type" => "textarea"
					),
					array(
						"param_name" => "cat",
						"heading" => esc_html__("Categories", "prohost"),
						"description" => wp_kses_data( __("Select category to show services. If empty - select services from any category (group) or from IDs list", "prohost") ),
						"group" => esc_html__('Query', 'prohost'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => array_flip(prohost_array_merge(array(0 => esc_html__('- Select category -', 'prohost')), $services_groups)),
						"type" => "dropdown"
					),
					array(
						"param_name" => "columns",
						"heading" => esc_html__("Columns", "prohost"),
						"description" => wp_kses_data( __("How many columns use to show services list", "prohost") ),
						"group" => esc_html__('Query', 'prohost'),
						"admin_label" => true,
						"class" => "",
						"value" => "4",
						"type" => "textfield"
					),
					array(
						"param_name" => "count",
						"heading" => esc_html__("Number of posts", "prohost"),
						"description" => wp_kses_data( __("How many posts will be displayed? If used IDs - this parameter ignored.", "prohost") ),
						"admin_label" => true,
						"group" => esc_html__('Query', 'prohost'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => "4",
						"type" => "textfield"
					),
					array(
						"param_name" => "offset",
						"heading" => esc_html__("Offset before select posts", "prohost"),
						"description" => wp_kses_data( __("Skip posts before select next part.", "prohost") ),
						"group" => esc_html__('Query', 'prohost'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => "0",
						"type" => "textfield"
					),
					array(
						"param_name" => "orderby",
						"heading" => esc_html__("Post sorting", "prohost"),
						"description" => wp_kses_data( __("Select desired posts sorting method", "prohost") ),
						"group" => esc_html__('Query', 'prohost'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => array_flip(prohost_get_sc_param('sorting')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "order",
						"heading" => esc_html__("Post order", "prohost"),
						"description" => wp_kses_data( __("Select desired posts order", "prohost") ),
						"group" => esc_html__('Query', 'prohost'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => array_flip(prohost_get_sc_param('ordering')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "ids",
						"heading" => esc_html__("Service's IDs list", "prohost"),
						"description" => wp_kses_data( __("Comma separated list of service's ID. If set - parameters above (category, count, order, etc.)  are ignored!", "prohost") ),
						"group" => esc_html__('Query', 'prohost'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "readmore",
						"heading" => esc_html__("Read more", "prohost"),
						"description" => wp_kses_data( __("Caption for the Read more link (if empty - link not showed)", "prohost") ),
						"admin_label" => true,
						"group" => esc_html__('Captions', 'prohost'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "link",
						"heading" => esc_html__("Button URL", "prohost"),
						"description" => wp_kses_data( __("Link URL for the button at the bottom of the block", "prohost") ),
						"group" => esc_html__('Captions', 'prohost'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "link_caption",
						"heading" => esc_html__("Button caption", "prohost"),
						"description" => wp_kses_data( __("Caption for the button at the bottom of the block", "prohost") ),
						"group" => esc_html__('Captions', 'prohost'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					prohost_vc_width(),
					prohost_vc_height(),
					prohost_get_vc_param('margin_top'),
					prohost_get_vc_param('margin_bottom'),
					prohost_get_vc_param('margin_left'),
					prohost_get_vc_param('margin_right'),
					prohost_get_vc_param('id'),
					prohost_get_vc_param('class'),
					prohost_get_vc_param('animation'),
					prohost_get_vc_param('css')
				),
				'default_content' => '
					[trx_services_item title="' . esc_html__( 'Service item 1', 'prohost' ) . '"][/trx_services_item]
					[trx_services_item title="' . esc_html__( 'Service item 2', 'prohost' ) . '"][/trx_services_item]
					[trx_services_item title="' . esc_html__( 'Service item 3', 'prohost' ) . '"][/trx_services_item]
					[trx_services_item title="' . esc_html__( 'Service item 4', 'prohost' ) . '"][/trx_services_item]
				',
				'js_view' => 'VcTrxColumnsView'
			) );
			
			
		vc_map( array(
				"base" => "trx_services_item",
				"name" => esc_html__("Services item", "prohost"),
				"description" => wp_kses_data( __("Custom services item - all data pull out from shortcode parameters", "prohost") ),
				"show_settings_on_create" => true,
				"class" => "trx_sc_collection trx_sc_column_item trx_sc_services_item",
				"content_element" => true,
				"is_container" => true,
				'icon' => 'icon_trx_services_item',
				"as_child" => array('only' => 'trx_services'),
				"as_parent" => array('except' => 'trx_services'),
				"params" => array(
					array(
						"param_name" => "title",
						"heading" => esc_html__("Title", "prohost"),
						"description" => wp_kses_data( __("Item's title", "prohost") ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "icon",
						"heading" => esc_html__("Icon", "prohost"),
						"description" => wp_kses_data( __("Select icon for the item from Fontello icons set", "prohost") ),
						"admin_label" => true,
						"class" => "",
						"value" => prohost_get_sc_param('icons'),
						"type" => "dropdown"
					),
					array(
						"param_name" => "image",
						"heading" => esc_html__("Image", "prohost"),
						"description" => wp_kses_data( __("Item's image (if icon is empty)", "prohost") ),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "link",
						"heading" => esc_html__("Link", "prohost"),
						"description" => wp_kses_data( __("Link on item's page", "prohost") ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "readmore",
						"heading" => esc_html__("Read more", "prohost"),
						"description" => wp_kses_data( __("Caption for the Read more link (if empty - link not showed)", "prohost") ),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					prohost_get_vc_param('id'),
					prohost_get_vc_param('class'),
					prohost_get_vc_param('animation'),
					prohost_get_vc_param('css')
				),
				'js_view' => 'VcTrxColumnItemView'
			) );
			
		class WPBakeryShortCode_Trx_Services extends PROHOST_VC_ShortCodeColumns {}
		class WPBakeryShortCode_Trx_Services_Item extends PROHOST_VC_ShortCodeCollection {}

	}
}
?>