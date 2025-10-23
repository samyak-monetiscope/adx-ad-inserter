<?php
defined('ABSPATH') || exit;

/* -------------------------------------------------- */
/* 1 – Register All Plugin Settings                   */
/* -------------------------------------------------- */
function adxbymonetiscope_sanitize_raw_code( $value ) {
    if ( current_user_can( 'unfiltered_html' ) ) {
        // Keep exactly what admin pasted (scripts allowed)
        return $value;
    }

    // Fallback for users without unfiltered_html
    // (Script tags won't survive; this is expected by WP security model)
    $allowed = array(
        'div'      => array(
            'id'    => true,
            'class' => true,
            'style' => true,
            'data-*'=> true,
        ),
        'span'     => array(
            'id'    => true,
            'class' => true,
            'style' => true,
            'data-*'=> true,
        ),
        'ins'      => array(
            'class' => true,
            'style' => true,
            'data-*'=> true,
        ),
        'noscript' => array(),
    );

    return wp_kses( $value, $allowed );
}

function adx_v4_register_settings() {
    // Main slot/plugin-wide settings
    $settings = [
        'adx_enabled',
        // 'global_head_script',
        'popup_enabled',
        'popup_network_code',
        'popup_option',
        'ad2_enabled',
        'ad2_network_code',
        'ad2_keywords',
        // 'flying_enabled',
        // 'flying_network_code',
        'anchor_enabled',
        'anchor_network_code',
        'anchor_position',
        // 'bottom_sticky_enabled',
        // 'bottom_sticky_network_code',
        // 'side_floater_enabled',
        // 'side_floater_network_code',
        // 'reward_on_scroll_enabled',
        // 'reward_on_scroll_network_code',
        'offerwall_onscroll_enabled',
        'offerwall_onscroll_network_code',
        'offerwall_onscroll_logo_url',
        // 'coupon_rewarded_enabled',
        // 'coupon_rewarded_network_code',
        // 'coupon_rewarded_code',
        'interstitial_enabled',
        'interstitial_network_code',
        'custom_enabled',
        'custom_header_code',
        'custom_footer_code',
        'custom_ads_txt',
        'display_slot_enabled',
    ];
    
    foreach ($settings as $opt) {
        // Skip custom header/footer code for special handling below
        if ($opt === 'custom_header_code' || $opt === 'custom_footer_code' || $opt === 'custom_ads_txt') {
            continue;
        }
        register_setting('adx_v4_settings', $opt, [
            'sanitize_callback' => 'adx_v4_sanitize_option'
        ]);
    }
    
    // register_setting('adx_v4_settings', "flying_pages",        ['sanitize_callback' => 'adx_v4_sanitize_option']);
    // register_setting('adx_v4_settings', "flying_insertion",    ['sanitize_callback' => 'sanitize_text_field']);
    // register_setting('adx_v4_settings', "flying_alignment",    ['sanitize_callback' => 'sanitize_text_field']);
    // register_setting('adx_v4_settings', "flying_offset",       ['sanitize_callback' => 'absint']);
    // register_setting('adx_v4_settings',  "flying_devices",     ['sanitize_callback' => 'adx_v4_sanitize_option']);
    // Now register these 2 options with custom/no sanitization
    register_setting('adx_v4_settings', 'custom_header_code', [
        'type'              => 'string',
        'sanitize_callback' => 'adxbymonetiscope_sanitize_raw_code',
        'show_in_rest'      => false,
    ]);

    register_setting('adx_v4_settings', 'custom_footer_code', [
        'type'              => 'string',
        'sanitize_callback' => 'adxbymonetiscope_sanitize_raw_code',
        'show_in_rest'      => false,
    ]);

    // ads.txt is plain text — keeping this is fine.
    // (If you ever need commas/colons/newlines preserved, this still allows them.)
    register_setting('adx_v4_settings', 'custom_ads_txt', [
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_textarea_field',
        'show_in_rest'      => false,
    ]);


    // Subslot (Display Slot) settings — register for all 10 subslots
    for ($i = 1; $i <= 10; $i++) {
        register_setting('adx_v4_settings', "display_slot_{$i}_enabled",      ['sanitize_callback' => 'adx_v4_sanitize_option']);
        register_setting('adx_v4_settings', "display_slot_{$i}_network_code", ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('adx_v4_settings', "display_slot_{$i}_sizes",        ['sanitize_callback' => 'adx_v4_sanitize_option']);
        register_setting('adx_v4_settings', "display_slot_{$i}_pages",        ['sanitize_callback' => 'adx_v4_sanitize_option']);
        register_setting('adx_v4_settings', "display_slot_{$i}_insertion",    ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('adx_v4_settings', "display_slot_{$i}_alignment",    ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('adx_v4_settings', "display_slot_{$i}_text",         ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('adx_v4_settings', "display_slot_{$i}_offset",       ['sanitize_callback' => 'absint']);
        register_setting('adx_v4_settings',  "display_slot_{$i}_devices",     ['sanitize_callback' => 'adx_v4_sanitize_option']);
    }

    // Set default values for booleans if not already present (first install)
    $booleans = [
        'adx_enabled',
        'popup_enabled',
        'ad2_enabled',
        // 'flying_enabled',
        'anchor_enabled',
        // 'bottom_sticky_enabled',
        // 'side_floater_enabled',
        // 'reward_on_scroll_enabled',
        'offerwall_onscroll_enabled',
        // 'coupon_rewarded_enabled',
        'interstitial_enabled',
        'custom_enabled',
        'display_slot_enabled',
    ];
    foreach ($booleans as $b) {
        if (get_option($b) === false) {
            update_option($b, 'false');
        }
    }
    // Defaults for all subslot enable toggles
    for ($i = 1; $i <= 10; $i++) {
        $opt = "display_slot_{$i}_enabled";
        if (get_option($opt) === false) {
            update_option($opt, 'false');
        }
    }
    // Defaults for all subslot device toggles
    for ($i = 1; $i <= 10; $i++) {
        $opt = "display_slot_{$i}_devices";
        if (get_option($opt) === false) {
            update_option($opt, ['desktop', 'mobile']);
        }
    }

        // Defaults for Flying Carpet (single-slot)
    // if (get_option('flying_devices') === false) {
    //     update_option('flying_devices', ['desktop', 'mobile']);
    // }
    // if (get_option('flying_insertion') === false) {
    //     update_option('flying_insertion', 'after_post'); // safe, always works
    // }
    // if (get_option('flying_alignment') === false) {
    //     update_option('flying_alignment', 'center'); // match frontend default
    // }
    // if (get_option('flying_offset') === false) {
    //     update_option('flying_offset', 1); // used when insertion targets p/img
    // }
    // if (get_option('flying_pages') === false) {
    //     update_option('flying_pages', ['post']); // sensible default scope
    // }


}
add_action('admin_init', 'adx_v4_register_settings');

/**
 * Sanitizer callback for all plugin options
 */
function adx_v4_sanitize_option($value) {
    // Arrays: sanitize recursively (for checkboxes)
    if (is_array($value)) {
        return array_map('sanitize_text_field', $value);
    }
    // Allow <script> etc only in ad code
    if (is_string($value) && strpos($value, '<script') !== false) {
        return wp_kses_post($value);
    }
    // Handle booleans as 'true'/'false'
    if ($value === 'true' || $value === 'false') {
        return $value;
    }
    // Safe default: plain text
    return sanitize_text_field($value);
}

/* -------------------------------------------------- */
/* 2 – Add Settings Page to WordPress Admin           */
/* -------------------------------------------------- */
function adx_v4_add_settings_page() {
    // Create a TOP-LEVEL admin menu instead of a Settings submenu
    add_menu_page(
        'AdX Ad Inserter',              // Page title
        'AdX Ad Inserter',              // Menu title
        'manage_options',               // Capability
        'adx-ad-inserter',              // Menu slug (kept same)
        'adx_v4_settings_page',         // Callback to render content
        'dashicons-megaphone',          // Icon (choose what you like)
        59                               // Position (optional; before Settings)
    );
}
add_action('admin_menu', 'adx_v4_add_settings_page');

/* -------------------------------------------------- */
/* 3 – Enqueue Admin Scripts/CSS                      */
/* -------------------------------------------------- */
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook !== 'toplevel_page_adx-ad-inserter') {
        return;
    }

    wp_enqueue_style(
        'monetiscope-admin-css',
        plugin_dir_url(__FILE__) . './templates/template.css',
        [],
        ADXBYMS_CSS_VERSION
    );

    wp_enqueue_script(
        'monetiscope-admin-js',
        plugin_dir_url(__FILE__) . './templates/template.js',
        [],
        ADXBYMS_JS_VERSION,
        true
    );
    
});
// add_action('admin_enqueue_scripts', 'adxbymonetiscope_enqueue_admin_assets');
// function adxbymonetiscope_enqueue_admin_assets( $hook_suffix ) {
//     // Load only on your plugin settings page (adjust slug if different)
//     if ( ! ( isset($_GET['page']) && $_GET['page'] === 'adx_v4_settings' ) ) {
//         return;
//     }

//     $rel_path = './css/tailwind.css'; // compiled local file
//     $url = plugins_url( $rel_path, __FILE__ );
//     $ver = file_exists( plugin_dir_path(__FILE__) . $rel_path )
//         ? filemtime( plugin_dir_path(__FILE__) . $rel_path )
//         : false;

//     wp_register_style(
//         'adxbymonetiscope-tailwind',
//         $url,
//         [],
//         $ver
//     );
//     wp_enqueue_style('adxbymonetiscope-tailwind');
// }





/* -------------------------------------------------- */
/* 4 – Load Main Settings Template (UI)               */
/* -------------------------------------------------- */
require_once plugin_dir_path(__FILE__) . './templates/settings-template.php';
