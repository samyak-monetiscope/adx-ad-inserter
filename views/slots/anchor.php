<?php
defined('ABSPATH') || exit;

/**
 * Render the Anchor slot if enabled
 */
function adx_render_anchor_slot() {
    $enabled      = get_option('anchor_enabled') === 'true';
    $position     = get_option('anchor_position');       // 'TOP_ANCHOR' or 'BOTTOM_ANCHOR'
    $network_code = trim( get_option('anchor_network_code') );

    if ( ! $enabled || ! $network_code || ! in_array($position, ['TOP_ANCHOR','BOTTOM_ANCHOR'], true) ) {
        return;
    }

    // Inject the GPT slot setup
    echo '<script async src="https://securepubads.g.doubleclick.net/tag/js/gpt.js"></script>';
    echo '<script>
    window.googletag = window.googletag || {cmd: []};
    googletag.cmd.push(function() {
        var anchorSlot = googletag.defineOutOfPageSlot(
            "' . esc_js( $network_code ) . '",
            googletag.enums.OutOfPageFormat.' . $position . '
        ).addService(googletag.pubads());
        googletag.pubads().enableSingleRequest();
        googletag.enableServices();
        googletag.display(anchorSlot);  // <-- This line ensures the slot actually renders
    });
    </script>';
}
