<?php
// /views/slots/anchor.php
defined('ABSPATH') || exit;

if ( ! function_exists('adx_render_anchor_slot') ) :
function adx_render_anchor_slot() {
    $enabled      = (get_option('anchor_enabled') === 'true');
    $position     = get_option('anchor_position');                 // 'TOP_ANCHOR' | 'BOTTOM_ANCHOR'
    $network_code = trim( (string) get_option('anchor_network_code') );

    if ( ! $enabled || ! $network_code || ! in_array($position, array('TOP_ANCHOR','BOTTOM_ANCHOR'), true) ) {
        return;
    }



    // 2) Build URL to /assets-runtime/frontend/anchor.js WITHOUT needing adx.php constants
    // plugin root path: /wp-content/plugins/<your-plugin>
                  // folder name
    $anchor_js_url    = ADXMS_URL . '/views/js/anchor.js';

    // 3) Register + enqueue our anchor.js (depends on GPT)
    wp_register_script(
        'adxbmon-anchor',
        $anchor_js_url,
        array('adxbmon-gpt'),
        ADXMS_JS_VERSION,
        true
    );

    // 4) Pass small payload to JS (safe & standard)
    wp_localize_script('adxbmon-anchor', 'ADX_ANCHOR', array(
        'networkCode' => $network_code,
        'position'    => $position,
    ));

    wp_enqueue_script('adxbmon-anchor');
}
endif;
