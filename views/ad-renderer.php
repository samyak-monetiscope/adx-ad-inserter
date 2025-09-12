<?php
defined('ABSPATH') || exit;
// Define plugin version constant
if ( ! defined('ADXB_MONETISCOPE_VERSION') ) {
    define('ADXB_MONETISCOPE_VERSION', '1.0.0');
}

// Level 2: Header & Footer renderers
require_once __DIR__ . '/header-renderer.php';
require_once __DIR__ . '/footer-renderer.php';

/** 
 * Inject header slots inside <head>…</head>
 */
add_action('wp_head', 'adx_v4_render_header_ads');

/** 
 * Inject footer slots just before </body>
 */
add_action('wp_footer', 'adx_v4_render_footer_ads');
