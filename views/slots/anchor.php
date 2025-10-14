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

    // 1) Enqueue Google GPT
    wp_register_script(
        'adxbmon-gpt',
        'https://securepubads.g.doubleclick.net/tag/js/gpt.js',
        array(),
        null,           // let Google handle caching
        true
    );
    wp_enqueue_script('adxbmon-gpt');

    // 1a) Force async on GPT tag
    add_filter('script_loader_tag', function($tag, $handle){
        if ($handle === 'adxbmon-gpt' && strpos($tag, ' async') === false) {
            $tag = str_replace(' src', ' async src', $tag);
        }
        return $tag;
    }, 10, 2);

    // 2) Build URL to /assets-runtime/frontend/anchor.js WITHOUT needing adx.php constants
    // plugin root path: /wp-content/plugins/<your-plugin>
    $plugin_root_path = dirname( dirname( __DIR__ ) );                // from views/slots → views → (plugin root)
    $plugin_slug      = basename( $plugin_root_path );                // folder name
    $anchor_js_url    = ADXMS_URL . '/views/js/anchor.js';

    // 3) Register + enqueue our anchor.js (depends on GPT)
    wp_register_script(
        'adxbmon-anchor',
        $anchor_js_url,
        array('adxbmon-gpt'),
        null,
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
