<?php
defined('ABSPATH') || exit;

/**
 * Render the Offerwall (on Scroll) slot if enabled
 */
if ( ! function_exists('adxbyms_render_offerwall_onscroll_slot') ) :
function adxbyms_render_offerwall_onscroll_slot() {
    $enabled      = (get_option('offerwall_onscroll_enabled') === 'true');
    $network_code = trim( (string) get_option('offerwall_onscroll_network_code') );
    $logo_url     = trim( (string) get_option('offerwall_onscroll_logo_url') );

    if (!$logo_url) {
        $logo_url = 'https://monetiscope.com/wp-content/uploads/2025/05/cropped-e-2.png';
    }
    if (!$enabled || !$network_code) {
        return;
    }

    

    // 2) URLs (use your constant)
    $base_url = trailingslashit(ADXBYMS_URL);
    $css_url  = $base_url . 'views/css/offerwall-onscroll.css';
    $js_url   = $base_url . 'views/js/offerwall-onscroll.js';


    // 4) Enqueue CSS
    wp_enqueue_style('adxbyms-offerwall', $css_url, array(), ADXBYMS_CSS_VERSION);

    // 5) Enqueue JS (depends on GPT)
    wp_register_script('adxbyms-offerwall', $js_url, array('adxbyms-gpt'), ADXBYMS_JS_VERSION, true);

    // 6) Pass data to JS
    wp_localize_script('adxbyms-offerwall', 'ADXBYMS_OFFERWALL', array(
        'networkCode'    => $network_code,
        'logoUrl'        => $logo_url,
        'triggerPercent' => 30, // show after 30% scroll (same as your code)
    ));

    wp_enqueue_script('adxbyms-offerwall');
}
endif;
