<?php
defined('ABSPATH') || exit;

// Level 2: Header & Footer renderers
require_once __DIR__ . '/renderer/header-renderer.php';
require_once __DIR__ . '/renderer/footer-renderer.php';



if ( get_option('adx_enabled','false') === 'true' ) {
    add_action('wp_head', 'adxbyms_render_header_ads');
    add_action('wp_footer', 'adxbyms_render_footer_ads');
}
