<?php
defined('ABSPATH') || exit;
if ( ! defined('ADXB_MONETISCOPE_VERSION') ) {
    define('ADXB_MONETISCOPE_VERSION', '1.0.0');
}
/**
 * Render the Interstitial slot if enabled
 */

$gpt_ver = ADXB_MONETISCOPE_VERSION;

function adx_render_interstitial_slot() {
    $enabled      = get_option('interstitial_enabled') === 'true';
    $network_code = trim((string) get_option('interstitial_network_code'));

    if (!$enabled || $network_code === '') {
        return;
    }

    // Register + enqueue GPT script
    wp_register_script(
        'gpt',
        'https://securepubads.g.doubleclick.net/tag/js/gpt.js',
        [],                // no deps
        $gpt_ver,           // version (can use plugin constant too)
        true               // load in footer
    );

    // Add async to GPT script
    add_filter('script_loader_tag', function($tag, $handle) {
        if ($handle === 'gpt' && strpos($tag, 'async') === false) {
            $tag = str_replace(' src', ' async src', $tag);
        }
        return $tag;
    }, 10, 2);

    wp_enqueue_script('gpt');

    // Inline JS for Interstitial slot
    $js = sprintf(
        'window.googletag = window.googletag || {cmd: []};
         googletag.cmd.push(function() {
             var interstitialSlot = googletag.defineOutOfPageSlot(%s, googletag.enums.OutOfPageFormat.INTERSTITIAL);
             if (interstitialSlot) {
                 interstitialSlot.addService(googletag.pubads());
                 googletag.pubads().enableSingleRequest();
                 googletag.enableServices();
                 googletag.display(interstitialSlot);
             }
         });',
        wp_json_encode($network_code)
    );

    wp_add_inline_script('gpt', $js, 'after');
}
add_action('wp_enqueue_scripts', 'adx_render_interstitial_slot', 20);
