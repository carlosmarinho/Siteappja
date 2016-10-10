<?php
/**
 * ProHost Framework: Testimonial support
 *
 * @package	prohost
 * @since	prohost 1.0
 */

// Theme init
if (!function_exists('prohost_testimonial_theme_setup')) {
	add_action( 'prohost_action_before_init_theme', 'prohost_testimonial_theme_setup', 1 );
	function prohost_testimonial_theme_setup() {
	
		// Add item in the admin menu
		add_action('add_meta_boxes',		'prohost_testimonial_add_meta_box');

		// Save data from meta box
		add_action('save_post',				'prohost_testimonial_save_data');

		// Register shortcodes [trx_testimonials] and [trx_testimonials_item]
		add_action('prohost_action_shortcodes_list',		'prohost_testimonials_reg_shortcodes');
		if (function_exists('prohost_exists_visual_composer') && prohost_exists_visual_composer())
			add_action('prohost_action_shortcodes_list_vc','prohost_testimonials_reg_shortcodes_vc');

		// Meta box fields
		prohost_storage_set('testimonial_meta_box', array(
			'id' => 'testimonial-meta-box',
			'title' => esc_html__('Testimonial Details', 'prohost'),
			'page' => 'testimonial',
			'context' => 'normal',
			'priority' => 'high',
			'fields' => array(
				"testimonial_author" => array(
					"title" => esc_html__('Testimonial author',  'prohost'),
					"desc" => wp_kses_data( __("Name of the testimonial's author", 'prohost') ),
					"class" => "testimonial_author",
					"std" => "",
					"type" => "text"),
				"testimonial_position" => array(
					"title" => esc_html__("Author's position",  'prohost'),
					"desc" => wp_kses_data( __("Position of the testimonial's author", 'prohost') ),
					"class" => "testimonial_author",
					"std" => "",
					"type" => "text"),
				"testimonial_email" => array(
					"title" => esc_html__("Author's e-mail",  'prohost'),
					"desc" => wp_kses_data( __("E-mail of the testimonial's author - need to take Gravatar (if registered)", 'prohost') ),
					"class" => "testimonial_email",
					"std" => "",
					"type" => "text"),
				"testimonial_link" => array(
					"title" => esc_html__('Testimonial link',  'prohost'),
					"desc" => wp_kses_data( __("URL of the testimonial source or author profile page", 'prohost') ),
					"class" => "testimonial_link",
					"std" => "",
					"type" => "text")
				)
			)
		);
		
		// Add supported data types
		prohost_theme_support_pt('testimonial');
		prohost_theme_support_tx('testimonial_group');
		
	}
}


// Add meta box
if (!function_exists('prohost_testimonial_add_meta_box')) {
	//add_action('add_meta_boxes', 'prohost_testimonial_add_meta_box');
	function prohost_testimonial_add_meta_box() {
		$mb = prohost_storage_get('testimonial_meta_box');
		add_meta_box($mb['id'], $mb['title'], 'prohost_testimonial_show_meta_box', $mb['page'], $mb['context'], $mb['priority']);
	}
}

// Callback function to show fields in meta box
if (!function_exists('prohost_testimonial_show_meta_box')) {
	function prohost_testimonial_show_meta_box() {
		global $post;

		// Use nonce for verification
		echo '<input type="hidden" name="meta_box_testimonial_nonce" value="'.esc_attr(wp_create_nonce(admin_url())).'" />';
		
		$data = get_post_meta($post->ID, 'prohost_testimonial_data', true);
	
		$fields = prohost_storage_get_array('testimonial_meta_box', 'fields');
		?>
		<table class="testimonial_area">
		<?php
		if (is_array($fields) && count($fields) > 0) {
			foreach ($fields as $id=>$field) { 
				$meta = isset($data[$id]) ? $data[$id] : '';
				?>
				<tr class="testimonial_field <?php echo esc_attr($field['class']); ?>" valign="top">
					<td><label for="<?php echo esc_attr($id); ?>"><?php echo esc_attr($field['title']); ?></label></td>
					<td><input type="text" name="<?php echo esc_attr($id); ?>" id="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($meta); ?>" size="30" />
						<br><small><?php echo esc_attr($field['desc']); ?></small></td>
				</tr>
				<?php
			}
		}
		?>
		</table>
		<?php
	}
}


// Save data from meta box
if (!function_exists('prohost_testimonial_save_data')) {
	//add_action('save_post', 'prohost_testimonial_save_data');
	function prohost_testimonial_save_data($post_id) {
		// verify nonce
		if ( !wp_verify_nonce( prohost_get_value_gp('meta_box_testimonial_nonce'), admin_url() ) )
			return $post_id;

		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}

		// check permissions
		if ($_POST['post_type']!='testimonial' || !current_user_can('edit_post', $post_id)) {
			return $post_id;
		}

		$data = array();

		$fields = prohost_storage_get_array('testimonial_meta_box', 'fields');

		// Post type specific data handling
		if (is_array($fields) && count($fields) > 0) {
			foreach ($fields as $id=>$field) { 
				if (isset($_POST[$id])) 
					$data[$id] = stripslashes($_POST[$id]);
			}
		}

		update_post_meta($post_id, 'prohost_testimonial_data', $data);
	}
}






// ---------------------------------- [trx_testimonials] ---------------------------------------

/*
[trx_testimonials id="unique_id" style="1|2|3"]
	[trx_testimonials_item user="user_login"]Testimonials text[/trx_testimonials_item]
	[trx_testimonials_item email="" name="" position="" photo="photo_url"]Testimonials text[/trx_testimonials]
[/trx_testimonials]
*/

if (!function_exists('prohost_sc_testimonials')) {
	function prohost_sc_testimonials($atts, $content=null){	
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "testimonials-1",
			"columns" => 1,
			"slider" => "yes",
			"slides_space" => 0,
			"controls" => "no",
			"interval" => "",
			"autoheight" => "no",
			"align" => "",
			"custom" => "no",
			"ids" => "",
			"cat" => "",
			"count" => "3",
			"offset" => "",
			"orderby" => "date",
			"order" => "desc",
			"scheme" => "",
			"bg_color" => "",
			"bg_image" => "",
			"bg_overlay" => "",
			"bg_texture" => "",
			"title" => "",
			"subtitle" => "",
			"description" => "",
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
	
		if (empty($id)) $id = "sc_testimonials_".str_replace('.', '', mt_rand());
		if (empty($width)) $width = "100%";
		if (!empty($height) && prohost_param_is_on($autoheight)) $autoheight = "no";
		if (empty($interval)) $interval = mt_rand(5000, 10000);
	
		if ($bg_image > 0) {
			$attach = wp_get_attachment_image_src( $bg_image, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$bg_image = $attach[0];
		}
	
		if ($bg_overlay > 0) {
			if ($bg_color=='') $bg_color = prohost_get_scheme_color('bg');
			$rgb = prohost_hex2rgb($bg_color);
		}
		
		$class .= ($class ? ' ' : '') . prohost_get_css_position_as_classes($top, $right, $bottom, $left);

		$ws = prohost_get_css_dimensions_from_values($width);
		$hs = prohost_get_css_dimensions_from_values('', $height);
		$css .= ($hs) . ($ws);

		$count = max(1, (int) $count);
		$columns = max(1, min(12, (int) $columns));
		if (prohost_param_is_off($custom) && $count < $columns) $columns = $count;
		
		prohost_storage_set('sc_testimonials_data', array(
			'id' => $id,
            'style' => $style,
            'columns' => $columns,
            'counter' => 0,
            'slider' => $slider,
            'css_wh' => $ws . $hs
            )
        );

		if (prohost_param_is_on($slider)) prohost_enqueue_slider('swiper');
	
		$output = ($bg_color!='' || $bg_image!='' || $bg_overlay>0 || $bg_texture>0 || prohost_strlen($bg_texture)>2 || ($scheme && !prohost_param_is_off($scheme) && !prohost_param_is_inherit($scheme))
					? '<div class="sc_testimonials_wrap sc_section'
							. ($scheme && !prohost_param_is_off($scheme) && !prohost_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '') 
							. '"'
						.' style="'
							. ($bg_color !== '' && $bg_overlay==0 ? 'background-color:' . esc_attr($bg_color) . ';' : '')
							. ($bg_image !== '' ? 'background-image:url(' . esc_url($bg_image) . ');' : '')
							. '"'
						. (!prohost_param_is_off($animation) ? ' data-animation="'.esc_attr(prohost_get_animation_classes($animation)).'"' : '')
						. '>'
						. '<div class="sc_section_overlay'.($bg_texture>0 ? ' texture_bg_'.esc_attr($bg_texture) : '') . '"'
								. ' style="' . ($bg_overlay>0 ? 'background-color:rgba('.(int)$rgb['r'].','.(int)$rgb['g'].','.(int)$rgb['b'].','.min(1, max(0, $bg_overlay)).');' : '')
									. (prohost_strlen($bg_texture)>2 ? 'background-image:url('.esc_url($bg_texture).');' : '')
									. '"'
									. ($bg_overlay > 0 ? ' data-overlay="'.esc_attr($bg_overlay).'" data-bg_color="'.esc_attr($bg_color).'"' : '')
									. '>' 
					: '')
				. '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_testimonials sc_testimonials_style_'.esc_attr($style)
 					. ' ' . esc_attr(prohost_get_template_property($style, 'container_classes'))
 					. (prohost_param_is_on($slider)
						? ' sc_slider_swiper swiper-slider-container'
							. ' ' . esc_attr(prohost_get_slider_controls_classes($controls))
							. (prohost_param_is_on($autoheight) ? ' sc_slider_height_auto' : '')
							. ($hs ? ' sc_slider_height_fixed' : '')
						: '')
					. (!empty($class) ? ' '.esc_attr($class) : '')
					. ($align!='' && $align!='none' ? ' align'.esc_attr($align) : '')
					. '"'
				. ($bg_color=='' && $bg_image=='' && $bg_overlay==0 && ($bg_texture=='' || $bg_texture=='0') && !prohost_param_is_off($animation) ? ' data-animation="'.esc_attr(prohost_get_animation_classes($animation)).'"' : '')
				. (!empty($width) && prohost_strpos($width, '%')===false ? ' data-old-width="' . esc_attr($width) . '"' : '')
				. (!empty($height) && prohost_strpos($height, '%')===false ? ' data-old-height="' . esc_attr($height) . '"' : '')
				. ((int) $interval > 0 ? ' data-interval="'.esc_attr($interval).'"' : '')
				. ($columns > 1 ? ' data-slides-per-view="' . esc_attr($columns) . '"' : '')
				. ($slides_space > 0 ? ' data-slides-space="' . esc_attr($slides_space) . '"' : '')
				. ' data-slides-min-width="250"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
			. '>'
			. (!empty($subtitle) ? '<h6 class="sc_testimonials_subtitle sc_item_subtitle">' . trim(prohost_strmacros($subtitle)) . '</h6>' : '')
			. (!empty($title) ? '<h2 class="sc_testimonials_title sc_item_title">' . trim(prohost_strmacros($title)) . '</h2>' : '')
			. (!empty($description) ? '<div class="sc_testimonials_descr sc_item_descr">' . trim(prohost_strmacros($description)) . '</div>' : '')
			. (prohost_param_is_on($slider) 
				? '<div class="slides swiper-wrapper">' 
				: ($columns > 1 
					? '<div class="sc_columns columns_wrap">' 
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
				'post_type' => 'testimonial',
				'post_status' => 'publish',
				'posts_per_page' => $count,
				'ignore_sticky_posts' => true,
				'order' => $order=='asc' ? 'asc' : 'desc',
			);
		
			if ($offset > 0 && empty($ids)) {
				$args['offset'] = $offset;
			}
		
			$args = prohost_query_add_sort_order($args, $orderby, $order);
			$args = prohost_query_add_posts_and_cats($args, $ids, 'testimonial', $cat, 'testimonial_group');
	
			$query = new WP_Query( $args );
	
			$post_number = 0;
				
			while ( $query->have_posts() ) { 
				$query->the_post();
				$post_number++;
				$args = array(
					'layout' => $style,
					'show' => false,
					'number' => $post_number,
					'posts_on_page' => ($count > 0 ? $count : $query->found_posts),
					"descr" => prohost_get_custom_option('post_excerpt_maxlength'.($columns > 1 ? '_masonry' : '')),
					"orderby" => $orderby,
					'content' => false,
					'terms_list' => false,
					'columns_count' => $columns,
					'slider' => $slider,
					'tag_id' => $id ? $id . '_' . $post_number : '',
					'tag_class' => '',
					'tag_animation' => '',
					'tag_css' => '',
					'tag_css_wh' => $ws . $hs
				);
				$post_data = prohost_get_post_data($args);
				$post_data['post_content'] = wpautop($post_data['post_content']);	// Add <p> around text and paragraphs. Need separate call because 'content'=>false (see above)
				$post_meta = get_post_meta($post_data['post_id'], 'prohost_testimonial_data', true);
				$thumb_sizes = prohost_get_thumb_sizes(array('layout' => $style));
				$args['author'] = $post_meta['testimonial_author'];
				$args['position'] = $post_meta['testimonial_position'];
				$args['link'] = !empty($post_meta['testimonial_link']) ? $post_meta['testimonial_link'] : '';	//$post_data['post_link'];
				$args['email'] = $post_meta['testimonial_email'];
				$args['photo'] = $post_data['post_thumb'];
				$mult = prohost_get_retina_multiplier();
				if (empty($args['photo']) && !empty($args['email'])) $args['photo'] = get_avatar($args['email'], $thumb_sizes['w']*$mult);
				$output .= prohost_show_post_layout($args, $post_data);
			}
			wp_reset_postdata();
		}
	
		if (prohost_param_is_on($slider)) {
			$output .= '</div>'
				. '<div class="sc_slider_controls_wrap"><a class="sc_slider_prev" href="#"></a><a class="sc_slider_next" href="#"></a></div>'
				. '<div class="sc_slider_pagination_wrap"></div>';
		} else if ($columns > 1) {
			$output .= '</div>';
		}

		$output .= '</div>'
					. ($bg_color!='' || $bg_image!='' || $bg_overlay>0 || $bg_texture>0 || prohost_strlen($bg_texture)>2
						?  '</div></div>'
						: '');
	
		// Add template specific scripts and styles
		do_action('prohost_action_blog_scripts', $style);

		return apply_filters('prohost_shortcode_output', $output, 'trx_testimonials', $atts, $content);
	}
	prohost_require_shortcode('trx_testimonials', 'prohost_sc_testimonials');
}
	
	
if (!function_exists('prohost_sc_testimonials_item')) {
	function prohost_sc_testimonials_item($atts, $content=null){	
		if (prohost_in_shortcode_blogger()) return '';
		extract(prohost_html_decode(shortcode_atts(array(
			// Individual params
			"author" => "",
			"position" => "",
			"link" => "",
			"photo" => "",
			"email" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
		), $atts)));

		prohost_storage_inc_array('sc_testimonials_data', 'counter');
	
		$id = $id ? $id : (prohost_storage_get_array('sc_testimonials_data', 'id') ? prohost_storage_get_array('sc_testimonials_data', 'id') . '_' . prohost_storage_get_array('sc_testimonials_data', 'counter') : '');
	
		$thumb_sizes = prohost_get_thumb_sizes(array('layout' => prohost_storage_get_array('sc_testimonials_data', 'style')));

		if (empty($photo)) {
			if (!empty($email))
				$mult = prohost_get_retina_multiplier();
				$photo = get_avatar($email, $thumb_sizes['w']*$mult);
		} else {
			if ($photo > 0) {
				$attach = wp_get_attachment_image_src( $photo, 'full' );
				if (isset($attach[0]) && $attach[0]!='')
					$photo = $attach[0];
			}
			$photo = prohost_get_resized_image_tag($photo, $thumb_sizes['w'], $thumb_sizes['h']);
		}

		$post_data = array(
			'post_content' => do_shortcode($content)
		);
		$args = array(
			'layout' => prohost_storage_get_array('sc_testimonials_data', 'style'),
			'number' => prohost_storage_get_array('sc_testimonials_data', 'counter'),
			'columns_count' => prohost_storage_get_array('sc_testimonials_data', 'columns'),
			'slider' => prohost_storage_get_array('sc_testimonials_data', 'slider'),
			'show' => false,
			'descr'  => 0,
			'tag_id' => $id,
			'tag_class' => $class,
			'tag_animation' => '',
			'tag_css' => $css,
			'tag_css_wh' => prohost_storage_get_array('sc_testimonials_data', 'css_wh'),
			'author' => $author,
			'position' => $position,
			'link' => $link,
			'email' => $email,
			'photo' => $photo
		);
		$output = prohost_show_post_layout($args, $post_data);

		return apply_filters('prohost_shortcode_output', $output, 'trx_testimonials_item', $atts, $content);
	}
	prohost_require_shortcode('trx_testimonials_item', 'prohost_sc_testimonials_item');
}
// ---------------------------------- [/trx_testimonials] ---------------------------------------



// Add [trx_testimonials] and [trx_testimonials_item] in the shortcodes list
if (!function_exists('prohost_testimonials_reg_shortcodes')) {
	//add_filter('prohost_action_shortcodes_list',	'prohost_testimonials_reg_shortcodes');
	function prohost_testimonials_reg_shortcodes() {
		if (prohost_storage_isset('shortcodes')) {

			$testimonials_groups = prohost_get_list_terms(false, 'testimonial_group');
			$testimonials_styles = prohost_get_list_templates('testimonials');
			$controls = prohost_get_list_slider_controls();

			prohost_sc_map_before('trx_title', array(
			
				// Testimonials
				"trx_testimonials" => array(
					"title" => esc_html__("Testimonials", "prohost"),
					"desc" => wp_kses_data( __("Insert testimonials into post (page)", "prohost") ),
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
							"title" => esc_html__("Testimonials style", "prohost"),
							"desc" => wp_kses_data( __("Select style to display testimonials", "prohost") ),
							"value" => "testimonials-1",
							"type" => "select",
							"options" => $testimonials_styles
						),
						"columns" => array(
							"title" => esc_html__("Columns", "prohost"),
							"desc" => wp_kses_data( __("How many columns use to show testimonials", "prohost") ),
							"value" => 1,
							"min" => 1,
							"max" => 6,
							"step" => 1,
							"type" => "spinner"
						),
						"slider" => array(
							"title" => esc_html__("Slider", "prohost"),
							"desc" => wp_kses_data( __("Use slider to show testimonials", "prohost") ),
							"value" => "yes",
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
							"desc" => wp_kses_data( __("Alignment of the testimonials block", "prohost") ),
							"divider" => true,
							"value" => "",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => prohost_get_sc_param('align')
						),
						"custom" => array(
							"title" => esc_html__("Custom", "prohost"),
							"desc" => wp_kses_data( __("Allow get testimonials from inner shortcodes (custom) or get it from specified group (cat)", "prohost") ),
							"divider" => true,
							"value" => "no",
							"type" => "switch",
							"options" => prohost_get_sc_param('yes_no')
						),
						"cat" => array(
							"title" => esc_html__("Categories", "prohost"),
							"desc" => wp_kses_data( __("Select categories (groups) to show testimonials. If empty - select testimonials from any category (group) or from IDs list", "prohost") ),
							"dependency" => array(
								'custom' => array('no')
							),
							"divider" => true,
							"value" => "",
							"type" => "select",
							"style" => "list",
							"multiple" => true,
							"options" => prohost_array_merge(array(0 => esc_html__('- Select category -', 'prohost')), $testimonials_groups)
						),
						"count" => array(
							"title" => esc_html__("Number of posts", "prohost"),
							"desc" => wp_kses_data( __("How many posts will be displayed? If used IDs - this parameter ignored.", "prohost") ),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => 3,
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
							"value" => "date",
							"type" => "select",
							"options" => prohost_get_sc_param('sorting')
						),
						"order" => array(
							"title" => esc_html__("Post order", "prohost"),
							"desc" => wp_kses_data( __("Select desired posts order", "prohost") ),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => "desc",
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
						"scheme" => array(
							"title" => esc_html__("Color scheme", "prohost"),
							"desc" => wp_kses_data( __("Select color scheme for this block", "prohost") ),
							"value" => "",
							"type" => "checklist",
							"options" => prohost_get_sc_param('schemes')
						),
						"bg_color" => array(
							"title" => esc_html__("Background color", "prohost"),
							"desc" => wp_kses_data( __("Any background color for this section", "prohost") ),
							"value" => "",
							"type" => "color"
						),
						"bg_image" => array(
							"title" => esc_html__("Background image URL", "prohost"),
							"desc" => wp_kses_data( __("Select or upload image or write URL from other site for the background", "prohost") ),
							"readonly" => false,
							"value" => "",
							"type" => "media"
						),
						"bg_overlay" => array(
							"title" => esc_html__("Overlay", "prohost"),
							"desc" => wp_kses_data( __("Overlay color opacity (from 0.0 to 1.0)", "prohost") ),
							"min" => "0",
							"max" => "1",
							"step" => "0.1",
							"value" => "0",
							"type" => "spinner"
						),
						"bg_texture" => array(
							"title" => esc_html__("Texture", "prohost"),
							"desc" => wp_kses_data( __("Predefined texture style from 1 to 11. 0 - without texture.", "prohost") ),
							"min" => "0",
							"max" => "11",
							"step" => "1",
							"value" => "0",
							"type" => "spinner"
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
						"name" => "trx_testimonials_item",
						"title" => esc_html__("Item", "prohost"),
						"desc" => wp_kses_data( __("Testimonials item (custom parameters)", "prohost") ),
						"container" => true,
						"params" => array(
							"author" => array(
								"title" => esc_html__("Author", "prohost"),
								"desc" => wp_kses_data( __("Name of the testimonmials author", "prohost") ),
								"value" => "",
								"type" => "text"
							),
							"link" => array(
								"title" => esc_html__("Link", "prohost"),
								"desc" => wp_kses_data( __("Link URL to the testimonmials author page", "prohost") ),
								"value" => "",
								"type" => "text"
							),
							"email" => array(
								"title" => esc_html__("E-mail", "prohost"),
								"desc" => wp_kses_data( __("E-mail of the testimonmials author (to get gravatar)", "prohost") ),
								"value" => "",
								"type" => "text"
							),
							"photo" => array(
								"title" => esc_html__("Photo", "prohost"),
								"desc" => wp_kses_data( __("Select or upload photo of testimonmials author or write URL of photo from other site", "prohost") ),
								"value" => "",
								"type" => "media"
							),
							"_content_" => array(
								"title" => esc_html__("Testimonials text", "prohost"),
								"desc" => wp_kses_data( __("Current testimonials text", "prohost") ),
								"divider" => true,
								"rows" => 4,
								"value" => "",
								"type" => "textarea"
							),
							"id" => prohost_get_sc_param('id'),
							"class" => prohost_get_sc_param('class'),
							"css" => prohost_get_sc_param('css')
						)
					)
				)

			));
		}
	}
}


// Add [trx_testimonials] and [trx_testimonials_item] in the VC shortcodes list
if (!function_exists('prohost_testimonials_reg_shortcodes_vc')) {
	//add_filter('prohost_action_shortcodes_list_vc',	'prohost_testimonials_reg_shortcodes_vc');
	function prohost_testimonials_reg_shortcodes_vc() {

		$testimonials_groups = prohost_get_list_terms(false, 'testimonial_group');
		$testimonials_styles = prohost_get_list_templates('testimonials');
		$controls			 = prohost_get_list_slider_controls();
			
		// Testimonials			
		vc_map( array(
				"base" => "trx_testimonials",
				"name" => esc_html__("Testimonials", "prohost"),
				"description" => wp_kses_data( __("Insert testimonials slider", "prohost") ),
				"category" => esc_html__('Content', 'prohost'),
				'icon' => 'icon_trx_testimonials',
				"class" => "trx_sc_columns trx_sc_testimonials",
				"content_element" => true,
				"is_container" => true,
				"show_settings_on_create" => true,
				"as_parent" => array('only' => 'trx_testimonials_item'),
				"params" => array(
					array(
						"param_name" => "style",
						"heading" => esc_html__("Testimonials style", "prohost"),
						"description" => wp_kses_data( __("Select style to display testimonials", "prohost") ),
						"class" => "",
						"admin_label" => true,
						"value" => array_flip($testimonials_styles),
						"type" => "dropdown"
					),
					array(
						"param_name" => "slider",
						"heading" => esc_html__("Slider", "prohost"),
						"description" => wp_kses_data( __("Use slider to show testimonials", "prohost") ),
						"admin_label" => true,
						"group" => esc_html__('Slider', 'prohost'),
						"class" => "",
						"std" => "yes",
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
						"description" => wp_kses_data( __("Alignment of the testimonials block", "prohost") ),
						"class" => "",
						"value" => array_flip(prohost_get_sc_param('align')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "custom",
						"heading" => esc_html__("Custom", "prohost"),
						"description" => wp_kses_data( __("Allow get testimonials from inner shortcodes (custom) or get it from specified group (cat)", "prohost") ),
						"class" => "",
						"value" => array("Custom slides" => "yes" ),
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
						"description" => wp_kses_data( __("Select categories (groups) to show testimonials. If empty - select testimonials from any category (group) or from IDs list", "prohost") ),
						"group" => esc_html__('Query', 'prohost'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => array_flip(prohost_array_merge(array(0 => esc_html__('- Select category -', 'prohost')), $testimonials_groups)),
						"type" => "dropdown"
					),
					array(
						"param_name" => "columns",
						"heading" => esc_html__("Columns", "prohost"),
						"description" => wp_kses_data( __("How many columns use to show testimonials", "prohost") ),
						"group" => esc_html__('Query', 'prohost'),
						"admin_label" => true,
						"class" => "",
						"value" => "1",
						"type" => "textfield"
					),
					array(
						"param_name" => "count",
						"heading" => esc_html__("Number of posts", "prohost"),
						"description" => wp_kses_data( __("How many posts will be displayed? If used IDs - this parameter ignored.", "prohost") ),
						"group" => esc_html__('Query', 'prohost'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => "3",
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
						"heading" => esc_html__("Post IDs list", "prohost"),
						"description" => wp_kses_data( __("Comma separated list of posts ID. If set - parameters above are ignored!", "prohost") ),
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
						"param_name" => "scheme",
						"heading" => esc_html__("Color scheme", "prohost"),
						"description" => wp_kses_data( __("Select color scheme for this block", "prohost") ),
						"group" => esc_html__('Colors and Images', 'prohost'),
						"class" => "",
						"value" => array_flip(prohost_get_sc_param('schemes')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "bg_color",
						"heading" => esc_html__("Background color", "prohost"),
						"description" => wp_kses_data( __("Any background color for this section", "prohost") ),
						"group" => esc_html__('Colors and Images', 'prohost'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "bg_image",
						"heading" => esc_html__("Background image URL", "prohost"),
						"description" => wp_kses_data( __("Select background image from library for this section", "prohost") ),
						"group" => esc_html__('Colors and Images', 'prohost'),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "bg_overlay",
						"heading" => esc_html__("Overlay", "prohost"),
						"description" => wp_kses_data( __("Overlay color opacity (from 0.0 to 1.0)", "prohost") ),
						"group" => esc_html__('Colors and Images', 'prohost'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "bg_texture",
						"heading" => esc_html__("Texture", "prohost"),
						"description" => wp_kses_data( __("Texture style from 1 to 11. Empty or 0 - without texture.", "prohost") ),
						"group" => esc_html__('Colors and Images', 'prohost'),
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
				'js_view' => 'VcTrxColumnsView'
		) );
			
			
		vc_map( array(
				"base" => "trx_testimonials_item",
				"name" => esc_html__("Testimonial", "prohost"),
				"description" => wp_kses_data( __("Single testimonials item", "prohost") ),
				"show_settings_on_create" => true,
				"class" => "trx_sc_collection trx_sc_column_item trx_sc_testimonials_item",
				"content_element" => true,
				"is_container" => true,
				'icon' => 'icon_trx_testimonials_item',
				"as_child" => array('only' => 'trx_testimonials'),
				"as_parent" => array('except' => 'trx_testimonials'),
				"params" => array(
					array(
						"param_name" => "author",
						"heading" => esc_html__("Author", "prohost"),
						"description" => wp_kses_data( __("Name of the testimonmials author", "prohost") ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "link",
						"heading" => esc_html__("Link", "prohost"),
						"description" => wp_kses_data( __("Link URL to the testimonmials author page", "prohost") ),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "email",
						"heading" => esc_html__("E-mail", "prohost"),
						"description" => wp_kses_data( __("E-mail of the testimonmials author", "prohost") ),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "photo",
						"heading" => esc_html__("Photo", "prohost"),
						"description" => wp_kses_data( __("Select or upload photo of testimonmials author or write URL of photo from other site", "prohost") ),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					/*
					array(
						"param_name" => "content",
						"heading" => esc_html__("Testimonials text", "prohost"),
						"description" => wp_kses_data( __("Current testimonials text", "prohost") ),
						"class" => "",
						"value" => "",
						"type" => "textarea_html"
					),
					*/
					prohost_get_vc_param('id'),
					prohost_get_vc_param('class'),
					prohost_get_vc_param('css')
				),
				'js_view' => 'VcTrxColumnItemView'
		) );
			
		class WPBakeryShortCode_Trx_Testimonials extends PROHOST_VC_ShortCodeColumns {}
		class WPBakeryShortCode_Trx_Testimonials_Item extends PROHOST_VC_ShortCodeCollection {}
		
	}
}
?>