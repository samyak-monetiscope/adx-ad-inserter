<?php
defined('ABSPATH') || exit;

if ( ! function_exists('adx_render_interstitial_slot') ) :
function adx_render_interstitial_slot() {
    $enabled      = (get_option('interstitial_enabled') === 'true');
    $network_code = trim( (string) get_option('interstitial_network_code') );

    if ( ! $enabled || ! $network_code ) {
        return;
    }

    // 1) Enqueue Google GPT (same as your anchor slot)
    wp_register_script(
        'adxbmon-gpt',
        'https://securepubads.g.doubleclick.net/tag/js/gpt.js',
        array(),
        ADXMS_GPT_VERSION,
        true
    );
    wp_enqueue_script('adxbmon-gpt');

    // 1a) Force async loading
    add_filter('script_loader_tag', function($tag, $handle) {
        if ($handle === 'adxbmon-gpt' && strpos($tag, ' async') === false) {
            $tag = str_replace(' src', ' async src', $tag);
        }
        return $tag;
    }, 10, 2);

    // 2) Build JS path (like your anchor.php)
    $interstitial_js_url = trailingslashit(ADXMS_URL) . 'views/js/interstitial.js';


    // 3) Register and enqueue interstitial.js (depends on GPT)
    wp_register_script(
        'adxbmon-interstitial',
        $interstitial_js_url,
        array('adxbmon-gpt'),
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
