<?php
defined('ABSPATH') || exit;


/**
 * Popup Ad slot
 * - Trigger at 50% scroll
 * - Sizes: [[300,250],[336,280],[300,280],[250,250],[200,200]]
 * - page_url = window.location.href
 * - CSS entirely in JS
 * Modes:
 * - ONCE_PER_SESSION (default)
 * - ONCE_PER_PAGE
 */
function adxbyms_render_popup_slot() {
    $enabled      = (get_option('popup_enabled') === 'true');
    $network_code = trim((string) get_option('popup_network_code'));

    if ( ! $enabled || $network_code === '' ) {
        return;
    }

    // Read popup option; default to ONCE_PER_SESSION
    $popup_option = get_option('popup_option');
    if ($popup_option !== 'ONCE_PER_PAGE' && $popup_option !== 'ONCE_PER_SESSION') {
        $popup_option = 'ONCE_PER_SESSION';
    }

    // ---- Build config for JS (exact values you already use) ----
    $config = array(
        'popup_option' => $popup_option,  // "ONCE_PER_SESSION" | "ONCE_PER_PAGE"
        'network_code' => $network_code,  // passed to googletag.defineSlot(...)
    );

    // IMPORTANT: encode once; do not escape the JSON itself
    $config_js = 'window.ADXBYMS_POPUP_DATA = ' . wp_json_encode($config, JSON_UNESCAPED_SLASHES) . ';';



    // ---- Register + enqueue the external JS file ----
    // Path provided by you: ADXBYMS_URL . 'views/js/popup.js'
    $popup_js_url = trailingslashit(ADXBYMS_URL) . 'views/js/popup.js';

    wp_register_script(
        'adxbyms_popup_script',
        $popup_js_url,
        array(),                // no deps; GPT loader stays inside popup.js
        ADXBYMS_JS_VERSION,
        true                    // load in footer
    );

    // Make sure data is available BEFORE popup.js executes
    wp_add_inline_script('adxbyms_popup_script', $config_js, 'before');

    // Enqueue the script
    wp_enqueue_script('adxbyms_popup_script'); 
}
