<?php
defined('ABSPATH') || exit;

/* -------------------------------------------------- */
/* 1 – Register All Plugin Settings                   */
/* -------------------------------------------------- */
function adx_v4_register_settings() {
    // Main slot/plugin-wide settings
    $settings = [
        'adx_enabled',
        'global_head_script',
        'popup_enabled',
        'popup_network_code',
        'ad2_enabled',
        'ad2_network_code',
        'ad2_keywords',
        'flying_enabled',
        'flying_network_code',
        'anchor_enabled',
        'anchor_network_code',
        'anchor_position',
        'bottom_sticky_enabled',
        'bottom_sticky_network_code',
        'side_floater_enabled',
        'side_floater_network_code',
        'reward_on_scroll_enabled',
        'reward_on_scroll_network_code',
        'offerwall_onscroll_enabled',
        'offerwall_onscroll_network_code',
        'offerwall_onscroll_logo_url',
        'coupon_rewarded_enabled',
        'coupon_rewarded_network_code',
        'coupon_rewarded_code',
        'interstitial_enabled',
        'interstitial_network_code',
        'custom_enabled',
        'custom_header_code',
        'custom_footer_code',
        'custom_ads_txt'
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

    // Now register these 2 options with custom/no sanitization
    register_setting('adx_v4_settings', 'custom_header_code', [
        'sanitize_callback' => null // or your custom callback
    ]);
    register_setting('adx_v4_settings', 'custom_footer_code', [
        'sanitize_callback' => null // or your custom callback
    ]);
    register_setting('adx_v4_settings', 'custom_ads_txt', [
        'sanitize_callback' => null // or your custom callback
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
        'flying_enabled',
        'anchor_enabled',
        'bottom_sticky_enabled',
        'side_floater_enabled',
        'reward_on_scroll_enabled',
        'offerwall_onscroll_enabled',
        'coupon_rewarded_enabled',
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
            update_option($opt, ['desktop', 'tablet', 'mobile']);
        }
    }

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
    add_options_page(
        'AdX Ad Inserter',
        'AdX Ad Inserter',
        'manage_options',
        'adx-ad-inserter',
        'adx_v4_settings_page' // this function will be called to render the page
    );
}
add_action('admin_menu', 'adx_v4_add_settings_page');

/* -------------------------------------------------- */
/* 3 – Enqueue Admin Scripts/CSS                      */
/* -------------------------------------------------- */
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook !== 'settings_page_adx-ad-inserter') {
        return;
    }

    wp_enqueue_style(
        'monetiscope-admin-css',
        plugin_dir_url( __FILE__ ) . './css/index.css',
        [],
        '1.2.0'
    );
    // Uncomment if you have admin CSS:
    // wp_enqueue_style(
    //     'monetiscope-admin-css',
    //     plugin_dir_url(__FILE__) . './css/index.css',
    //     [],
    //     '1.2.0'
    // );
    wp_enqueue_script(
        'monetiscope-admin-js',
        plugin_dir_url(__FILE__) . './js/admin-scripts.js',
        [],
        '1.2.0',
        true
    );
});

/* -------------------------------------------------- */
/* 4 – Load Main Settings Template (UI)               */
/* -------------------------------------------------- */
require_once plugin_dir_path(__FILE__) . './views/settings-template.php';
