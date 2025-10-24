<?php
defined('ABSPATH') || exit;

// Level 2: Header & Footer renderers
require_once ADXBYMS_DIR . 'renderer/header-renderer.php';
require_once ADXBYMS_DIR . 'renderer/footer-renderer.php';



if ( get_option('adx_enabled','false') === 'true' ) {
  
    wp_register_script(
        'adxbyms-gpt',
        'https://securepubads.g.doubleclick.net/tag/js/gpt.js',
        array(),
        ADXBYMS_GPT_VERSION,           // let Google handle caching
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


    add_action('wp_head', 'adxbyms_render_header_ads');
    add_action('wp_footer', 'adxbyms_render_footer_ads');
}
