<?php
defined('ABSPATH') || exit;

add_action('wp_footer', 'adxbymonetiscope_render_display_slot_footer');

function adxbymonetiscope_render_display_slot_footer() {
    // if (get_option('display_slot_enabled') !== 'true') return;

    // for ($i = 1; $i <= 10; $i++) {
    //     $enabled   = get_option("display_slot_{$i}_enabled") === 'true';
    //     $code      = trim(get_option("display_slot_{$i}_network_code", ''));
    //     $text      = trim(get_option("display_slot_{$i}_text", ''));
    //     $pages     = get_option("display_slot_{$i}_pages", []);
    //     $insertion = get_option("display_slot_{$i}_insertion", '');
    //     $alignment = get_option("display_slot_{$i}_alignment", '');

    //     if (!$enabled || $text === '') continue;

    //     if (
    //         (is_single() && in_array('post', (array)$pages)) ||
    //         (is_front_page() && in_array('homepage', (array)$pages))
    //     ) {
    //         $align_style = match($alignment) {
    //             'center' => 'text-align:center;',
    //             'end'    => 'text-align:right;',
    //             default  => 'text-align:left;',
    //         };

    //         $output = "<div style=\"$align_style\">" . esc_html($text) . "</div>";

    //         if ($insertion === 'footer') {
    //             echo esc_js($output);
    //         }

    //         if ($insertion === 'before_content') {
    //             add_filter('the_content', function($content) use ($output) {
    //                 return $output . $content;
    //             });
    //         }
    //     }
    // }
    console.log("hello")
}
