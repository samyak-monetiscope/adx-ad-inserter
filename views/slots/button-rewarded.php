<?php
defined('ABSPATH') || exit;

if ( ! function_exists('adxbymonetiscope_render_button_rewarded_slot') ) :
function adxbymonetiscope_render_button_rewarded_slot() {
    $enabled       = (get_option('ad2_enabled') === 'true');
    $network_code  = trim((string) get_option('ad2_network_code'));
    $keywords_raw  = get_option('ad2_keywords', '');
    $keywords      = array_map('trim', explode(',', $keywords_raw));

    if ( ! $enabled || ! $network_code ) {
        return;
    }

    // 1️⃣ Enqueue Google GPT
    wp_register_script(
        'adxbymonetiscope-gpt',
        'https://securepubads.g.doubleclick.net/tag/js/gpt.js',
        array(),
        ADXBYMS_GPT_VERSION,
        true
    );
    wp_enqueue_script('adxbymonetiscope-gpt');

    // 1a️⃣ Force async GPT loading
    add_filter('script_loader_tag', function($tag, $handle) {
        if ($handle === 'adxbymonetiscope-gpt' && strpos($tag, ' async') === false) {
            $tag = str_replace(' src', ' async src', $tag);
        }
        return $tag;
    }, 10, 2);

    // 2️⃣ Build JS URL using your ADXBYMS_URL constant
    $rewarded_js_url  = trailingslashit(ADXBYMS_URL) . 'views/js/button-rewarded.js';
    $rewarded_css_url = trailingslashit(ADXBYMS_URL) . 'views/css/button-rewarded.css';
    wp_enqueue_style('adxbymonetiscope-button-rewarded', $rewarded_css_url, [], ADXBYMS_CSS_VERSION);


    // 3️⃣ Register + enqueue JS (depends on GPT)
    wp_register_script(
        'adxbymonetiscope-button-rewarded',
        $rewarded_js_url,
        array('adxbymonetiscope-gpt'),
        ADXBYMS_JS_VERSION,
        true
    );

    // 4️⃣ Pass PHP data → JS
    wp_localize_script('adxbymonetiscope-button-rewarded', 'ADX_BUTTON_REWARDED', array(
        'networkCode' => $network_code,
        'keywords'    => $keywords,
    ));

    wp_enqueue_script('adxbymonetiscope-button-rewarded');
}
endif;
