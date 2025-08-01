<?php
defined('ABSPATH') || exit;

/** Print custom header code */
function adx_render_custom_header_slot() {
    $code = get_option('custom_header_code');
    if ( ! empty( $code ) ) {
        echo $code; // phpcs:ignore WordPress.Security.EscapeOutput
    }
}

/** Print custom footer code */
function adx_render_custom_footer_slot() {
    $code = get_option('custom_footer_code');
    if ( ! empty( $code ) ) {
        echo $code; // phpcs:ignore WordPress.Security.EscapeOutput
    }
}


/**
 * Whenever the 'custom_ads_txt' option is updated,
 * this will create or update the /ads.txt file in the site root.
 */
add_action('update_option_custom_ads_txt', 'adxbymonetiscope_update_ads_txt_file', 10, 2);

function adxbymonetiscope_update_ads_txt_file($old_value, $new_value) {
    // Full path to /public_html/ads.txt (ABSPATH points to WP root)
    $ads_txt_path = ABSPATH . 'ads.txt';

    // Write the new value to ads.txt, overwriting existing or creating new
    file_put_contents($ads_txt_path, $new_value);
}

