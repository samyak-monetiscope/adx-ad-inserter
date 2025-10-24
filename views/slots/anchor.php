<?php
// /views/slots/anchor.php
defined('ABSPATH') || exit;

if ( ! function_exists('adxbyms_render_anchor_slot') ) :
function adxbyms_render_anchor_slot() {
    $enabled      = (get_option('anchor_enabled') === 'true');
    $position     = get_option('anchor_position');                 // 'TOP_ANCHOR' | 'BOTTOM_ANCHOR'
    $network_code = trim( (string) get_option('anchor_network_code') );

    if ( ! $enabled || ! $network_code || ! in_array($position, array('TOP_ANCHOR','BOTTOM_ANCHOR'), true) ) {
        return;
    }



    // 2) Build URL to /assets-runtime/frontend/anchor.js WITHOUT needing adx.php constants
    // plugin root path: /wp-content/plugins/<your-plugin>
                  // folder name
    $anchor_js_url    = ADXBYMS_URL . '/views/js/anchor.js';

    // 3) Register + enqueue our anchor.js (depends on GPT)
    wp_register_script(
        'adxbyms-anchor',
        $anchor_js_url,
        array('adxbyms-gpt'),
        ADXBYMS_JS_VERSION,
        true
    );

    // 4) Pass small payload to JS (safe & standard)
    wp_localize_script('adxbyms-anchor', 'ADXBYMS_ANCHOR', array(
        'networkCode' => $network_code,
        'position'    => $position,
    ));

    wp_enqueue_script('adxbyms-anchor');
}
endif;
