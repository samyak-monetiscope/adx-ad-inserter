<?php
defined('ABSPATH') || exit;

// Level 2: Header & Footer renderers
require_once __DIR__ . '/renderer/header-renderer.php';
require_once __DIR__ . '/renderer/footer-renderer.php';



if ( get_option('adx_enabled','false') === 'true' ) {
    // echo "<script>console.log('adxbyms-gpt loading');</script>";
    wp_register_script(
        'adxbyms-gpt',
        'https://securepubads.g.doubleclick.net/tag/js/gpt.js',
        array(),
        ADXMS_GPT_VERSION,           // let Google handle caching
        true
    );
    wp_enqueue_script('adxbyms-gpt');
    // echo "<script>console.log('adxbyms-gpt loaded');</script>";

    add_action('wp_head', 'adx_v4_render_header_ads');
    add_action('wp_footer', 'adx_v4_render_footer_ads');
}
