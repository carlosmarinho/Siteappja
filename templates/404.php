<?php
/*
 * The template for displaying "Page 404"
*/

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'prohost_template_404_theme_setup' ) ) {
	add_action( 'prohost_action_before_init_theme', 'prohost_template_404_theme_setup', 1 );
	function prohost_template_404_theme_setup() {
		prohost_add_template(array(
			'layout' => '404',
			'mode'   => 'internal',
			'title'  => 'Page 404',
			'theme_options' => array(
				'article_style' => 'stretch'
			)
		));
	}
}

// Template output
if ( !function_exists( 'prohost_template_404_output' ) ) {
	function prohost_template_404_output() {
		?>
		<article class="post_item post_item_404">
			<div class="post_content">
                <div class="image_page_404"></div>
                <h2 class="page_title"><?php esc_html_e( 'We Are Sorry! ', 'prohost' ); ?><span><?php esc_html_e( 'error 404!', 'prohost' ); ?></span></h2>
                <p class="page_description">
                    <?php
                        echo esc_html__('Can\'t find what you need? Take a moment and do', 'prohost')
                        . '<br>'
                        . wp_kses_post( sprintf( __('a search below or start from our <a href="%s">homepage</a>.', 'prohost'), esc_url( home_url( '/' ) ) ) );
                    ?></p>
				<div class="page_search"><?php echo trim(prohost_sc_search(array('state'=>'fixed', 'title'=> esc_html__('...', 'prohost')))); ?></div>
			</div>
		</article>
		<?php
	}
}
?>