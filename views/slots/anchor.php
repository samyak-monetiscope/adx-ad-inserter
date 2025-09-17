<?php
defined('ABSPATH') || exit;
if ( ! defined('ANCHOR_GPT_VERSION') ) {
    define('ANCHOR_GPT_VERSION', '1.0.0');
}


/**
 * Render the Anchor slot if enabled
 */
function adx_render_anchor_slot() {
    $enabled      = get_option('anchor_enabled') === 'true';
    $position     = get_option('anchor_position');       // 'TOP_ANCHOR' or 'BOTTOM_ANCHOR'
    $network_code = trim( get_option('anchor_network_code') );

    if ( ! $enabled || ! $network_code || ! in_array( $position, array('TOP_ANCHOR', 'BOTTOM_ANCHOR'), true ) ) {
        return;
    }

    // ---- 1) Enqueue GPT ----
    wp_register_script(
        'gpt',
        'https://securepubads.g.doubleclick.net/tag/js/gpt.js',
        array(),
        ANCHOR_GPT_VERSION,   // let Google manage caching
        true    // footer
    );
    wp_enqueue_script('gpt');

    // ---- 2) Force async ----
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

    // ---- 3) Safe values for JS ----
    $network_code_js = wp_json_encode( $network_code ); // safe + quoted
    $position_js     = wp_json_encode( $position );     // "TOP_ANCHOR" | "BOTTOM_ANCHOR"

    // ---- 4) Build inline JS (industry-standard style) ----
    $js_lines = array(
        '(function(){',
        '  window.googletag = window.googletag || {cmd: []};',
        '  googletag.cmd.push(function() {',
        '    var fmt = (googletag.enums && googletag.enums.OutOfPageFormat) ? googletag.enums.OutOfPageFormat[' . $position_js . '] : null;',
        '    if (!fmt) { return; }',
        '    var slot = googletag.defineOutOfPageSlot(' . $network_code_js . ', fmt);',
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

    // ---- 5) Attach inline JS after GPT ----
    wp_add_inline_script( 'gpt', $inline_js, 'after' );
}
