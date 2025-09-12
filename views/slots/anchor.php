<?php
defined('ABSPATH') || exit;
// Define plugin version constant
if ( ! defined('ADXB_MONETISCOPE_VERSION') ) {
    define('ADXB_MONETISCOPE_VERSION', '1.0.0');
}
/**
 * Only enqueue when the anchor is enabled & configured.
 */
function adx_anchor_is_enabled() : bool {
    $enabled      = get_option('anchor_enabled') === 'true';
    $position     = get_option('anchor_position'); // 'TOP_ANCHOR' or 'BOTTOM_ANCHOR'
    $network_code = trim( (string) get_option('anchor_network_code') );

    return $enabled
        && $network_code !== ''
        && in_array($position, ['TOP_ANCHOR', 'BOTTOM_ANCHOR'], true);
}

/**
 * Add async to the GPT tag.
 */
function adx_anchor_add_async_attr( $tag, $handle ) {
    if ( $handle === 'gpt' ) {
        // Add async if not already present.
        if ( strpos( $tag, ' async' ) === false ) {
            $tag = str_replace( ' src', ' async src', $tag );
        }
    }
    return $tag;
}

$gpt_ver = ADXB_MONETISCOPE_VERSION;
/**
 * Enqueue GPT and attach our slot code correctly.
 */
function adx_enqueue_anchor_assets() {
    if ( is_admin() || ! adx_anchor_is_enabled() ) {
        return;
    }

    $position     = get_option('anchor_position');                 // 'TOP_ANCHOR' | 'BOTTOM_ANCHOR'
    $network_code = trim( (string) get_option('anchor_network_code') );

    // 1) Register + enqueue GPT from Google’s CDN (don’t echo tags).
    wp_register_script(
        'gpt',
        'https://securepubads.g.doubleclick.net/tag/js/gpt.js',
        [], // no deps
        $gpt_ver, // version: let Google handle cache busting
        true  // in footer
    );
    add_filter('script_loader_tag', 'adx_anchor_add_async_attr', 10, 2);
    wp_enqueue_script('gpt');

    // 2) Add our GPT setup inline, *after* the gpt handle is printed.
    $js = sprintf(
        'window.googletag = window.googletag || {cmd: []};
         googletag.cmd.push(function() {
           var slot = googletag.defineOutOfPageSlot(%s, googletag.enums.OutOfPageFormat.%s);
           if (slot) {
             slot.addService(googletag.pubads());
             googletag.pubads().enableSingleRequest();
             googletag.enableServices();
             // For OOP/Anchor, calling display on the slot is supported:
             slot.display();
           }
         });',
        wp_json_encode($network_code),
        esc_js($position)
    );

    wp_add_inline_script('gpt', $js, 'after');
}
add_action('wp_enqueue_scripts', 'adx_enqueue_anchor_assets', 20);
