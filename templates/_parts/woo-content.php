<?php
    /*+ Does the content store on the all width */
    $shop = wc_get_page_id('shop');
    if ( !empty( $shop ) && ($shop > 0) ) {
        $shop_page_id = get_post($shop);
        $shop_page_ct = $shop_page_id->post_content;
        echo '<div class="content_woo_full_width">'
            . prohost_do_shortcode($shop_page_ct)
            . '</div>';
    }
?>