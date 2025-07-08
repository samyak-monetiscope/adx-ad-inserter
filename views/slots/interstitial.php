<?php
defined('ABSPATH') || exit;

/**
 * Render the Interstitial slot if enabled
 */
function adx_render_interstitial_slot() {
    $enabled    = get_option('interstitial_enabled') === 'true';
    $network    = trim( get_option('interstitial_network_code') );

    if ( ! $enabled || ! $network ) {
        return;
    }

    // JSON-encode for safe JS
    $js_network = json_encode( $network );

    echo '
     <script async src="https://securepubads.g.doubleclick.net/tag/js/gpt.js"></script>
<script>
  window.googletag = window.googletag || { cmd: [] };
  googletag.cmd.push(function() {
    var interstitialSlot = googletag
      .defineOutOfPageSlot(' . $js_network . ', googletag.enums.OutOfPageFormat.INTERSTITIAL)
      .addService(googletag.pubads());

    googletag.pubads().enableSingleRequest();
    googletag.enableServices();
    googletag.display(interstitialSlot);
  });
</script>';
}
