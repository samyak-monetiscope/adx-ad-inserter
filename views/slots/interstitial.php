<?php
defined( 'ABSPATH' ) || exit;
if ( ! defined('INTERSTITIAL_GPT_VERSION') ) {
    define('INTERSTITIAL_GPT_VERSION', '1.0.0');
}

/**
 * Render the Interstitial slot if enabled
 */
function adx_render_interstitial_slot() {
    $enabled      = get_option( 'interstitial_enabled' ) === 'true';
    $network_code = trim( get_option( 'interstitial_network_code' ) );

    if ( ! $enabled || ! $network_code ) {
        return;
    }

    // ---- 1) Register & enqueue GPT ----
    wp_register_script(
        'gpt',
        'https://securepubads.g.doubleclick.net/tag/js/gpt.js',
        array(),
        INTERSTITIAL_GPT_VERSION,   // let Google manage caching
        true    // footer
    );
    wp_enqueue_script('gpt');

    // ---- 2) Ensure async ----
    add_filter(
        'script_loader_tag',
        function( $tag, $handle ) {
            if ( $handle === 'gpt' && strpos( $tag, ' async' ) === false ) {
                $tag = str_replace( ' src', ' async src', $tag );
            }
            return $tag;
        },
        10,
        2
    );

    // ---- 3) Escape value for JS ----
    $network_code_js = wp_json_encode( $network_code ); // safe quoted string

    // ---- 4) Build inline JS ----
    $js_lines = array(
        '(function(){',
        '  window.googletag = window.googletag || {cmd: []};',
        '  googletag.cmd.push(function() {',
        '    var slot = googletag.defineOutOfPageSlot(' . $network_code_js . ', googletag.enums.OutOfPageFormat.INTERSTITIAL);',
        '    if (slot) {',
        '      slot.addService(googletag.pubads());',
        '      googletag.pubads().enableSingleRequest();',
        '      googletag.enableServices();',
        '      googletag.display(slot);',
        '    }',
        '  });',
        '})();'
    );

    $inline_js = implode( "\n", $js_lines );

    // ---- 5) Attach inline JS ----
    wp_add_inline_script( 'gpt', $inline_js, 'after' );
}
