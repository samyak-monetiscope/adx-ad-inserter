<?php
defined('ABSPATH') || exit;

/**
 * Register /ads.txt rewrite → index.php?adxbyms_ads_txt=1
 * (Called on init, and also from activation hook in the main file before flushing)
 */
function adxbyms_register_ads_txt_rewrite(): void {
    add_rewrite_rule('^ads\.txt$', 'index.php?adxbyms_ads_txt=1', 'top');
}
add_action('init', 'adxbyms_register_ads_txt_rewrite');

/** Allow our query var */
add_filter('query_vars', function ($vars) {
    $vars[] = 'adxbyms_ads_txt';
    return $vars;
});

/** Truthy helper */
if ( ! function_exists('adxbyms_truthy') ) {
    function adxbyms_truthy($v): bool {
        if (is_bool($v)) return $v;
        if (is_int($v))  return $v === 1;
        $v = is_string($v) ? strtolower(trim($v)) : $v;
        return in_array($v, ['1','true','on','yes'], true);
    }
}

/** Serve ads.txt virtually for both /ads.txt and /index.php/ads.txt */
add_action('template_redirect', function () {
    if (is_admin()) return;

    $is_ads_rewrite = get_query_var('adxbyms_ads_txt');

    $request_uri   = $_SERVER['REQUEST_URI'] ?? '';
    $path          = strtolower(strtok($request_uri, '?'));
    $is_index_ads  = ($path === '/index.php/ads.txt');

    if (!$is_ads_rewrite && !$is_index_ads) return;

    $enabled = get_option('ads_txt_enabled', '');
    $code    = (string) get_option('ads_txt_code', '');

    if (!adxbyms_truthy($enabled) || trim($code) === '') return;

    if (function_exists('ob_get_level')) {
        while (ob_get_level() > 0) { @ob_end_clean(); }
    }

    nocache_headers();
    status_header(200);
    header('Content-Type: text/plain; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');

    echo rtrim($code, "\r\n") . "\n";
    exit;
}, 0);
