<?php
defined( 'ABSPATH' ) || exit;

/**
 * Render the Interstitial slot if enabled
 */
function adx_render_interstitial_slot() {
    $enabled      = get_option( 'interstitial_enabled' ) === 'true';
    $network_code = trim( get_option( 'interstitial_network_code' ) );

    if ( ! $enabled || ! $network_code ) {
        return;
    }

    $escaped_code = esc_js( $network_code );

    // Print the HTML/JS as a single output, using normal PHP string
    echo '
    <script async src="https://securepubads.g.doubleclick.net/tag/js/gpt.js"></script>
    <script>
    window.googletag = window.googletag || { cmd: [] };
    googletag.cmd.push(function() {
        var interstitialSlot = googletag
            .defineOutOfPageSlot("' . esc_js($escaped_code) . '", googletag.enums.OutOfPageFormat.INTERSTITIAL)
            .addService(googletag.pubads());
        googletag.pubads().enableSingleRequest();
        googletag.enableServices();
        googletag.display(interstitialSlot);
    });
    </script>
    ';
}
