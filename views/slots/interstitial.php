<?php
defined('ABSPATH') || exit;

if ( ! function_exists('adx_render_interstitial_slot') ) :
function adx_render_interstitial_slot() {
    $enabled      = (get_option('interstitial_enabled') === 'true');
    $network_code = trim( (string) get_option('interstitial_network_code') );

    if ( ! $enabled || ! $network_code ) {
        return;
    }



    // 2) Build JS path (like your anchor.php)
    $interstitial_js_url = trailingslashit(ADXMS_URL) . 'views/js/interstitial.js';


    // 3) Register and enqueue interstitial.js (depends on GPT)
    wp_register_script(
        'adxbmon-interstitial',
        $interstitial_js_url,
        array('adxbyms-gpt'),
        ADXMS_JS_VERSION,
        true
    );

    // 4) Pass safe data to JS
    wp_localize_script('adxbmon-interstitial', 'ADX_INTERSTITIAL', array(
        'networkCode' => $network_code,
    ));

    wp_enqueue_script('adxbmon-interstitial');
}
endif;
