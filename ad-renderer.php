<?php
defined('ABSPATH') || exit;

// Level 2: Header & Footer renderers
require_once __DIR__ . '/renderer/header-renderer.php';
require_once __DIR__ . '/renderer/footer-renderer.php';



if ( get_option('adx_enabled','false') === 'true' ) {
  
    wp_register_script(
        'adxbyms-gpt',
        'https://securepubads.g.doubleclick.net/tag/js/gpt.js',
        array(),
        ADXMS_GPT_VERSION,           // let Google handle caching
        true
    );
    wp_enqueue_script('adxbyms-gpt');

    //Force async loading
    add_filter('script_loader_tag', function($tag, $handle) {
        if ($handle === 'adxbyms-gpt' && strpos($tag, ' async') === false) {
            $tag = str_replace(' src', ' async src', $tag);
        }
        return $tag;
    }, 10, 2);

    add_action('wp_head', 'adx_v4_render_header_ads');
    add_action('wp_footer', 'adx_v4_render_footer_ads');
}
