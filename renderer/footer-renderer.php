<?php
defined('ABSPATH') || exit;

// existing footer slots
require_once ADXBYMS_DIR . '/views/slots/popup.php';
require_once ADXBYMS_DIR . '/views/slots/button-rewarded.php';
// require_once ADXBYMS_DIR . '/views/slots/bottom-sticky.php';
// require_once ADXBYMS_DIR . '/views/slots/side-floater.php';
// require_once ADXBYMS_DIR . '/views/slots/reward-on-scroll.php';
require_once ADXBYMS_DIR . '/views/slots/offerwall-onscroll.php';
require_once ADXBYMS_DIR . '/views/slots/interstitial.php';  
// require_once ADXBYMS_DIR . '/views/slots/coupon-rewarded.php';
require_once ADXBYMS_DIR . '/views/slots/display.php';
require_once ADXBYMS_DIR . '/views/slots/custom.php';
// require_once ADXBYMS_DIR . '/views/slots/flying-carpet.php';


/**
 * Fire all footer‐going ad slots
 */
function adx_v4_render_footer_ads() {
    adxbymonetiscope_render_popup_slot();
    adxbymonetiscope_render_button_rewarded_slot();
    // adx_render_bottom_sticky_slot();
    // adx_render_side_floater_slot();
	// adx_render_reward_on_scroll_slot();
    adx_render_offerwall_onscroll_slot();
    adx_render_interstitial_slot();
//    adx_render_coupon_rewarded_slot();
//    adxbymonetiscope_render_flying_carpet_slot();
	if ( get_option('custom_enabled') === 'true' ) {
        adx_render_custom_footer_slot();
    }
}
// add_action('wp_footer','adx_v4_render_footer_ads');
