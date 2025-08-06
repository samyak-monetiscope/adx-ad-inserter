<?php
defined('ABSPATH') || exit;

add_action('wp_head', 'adxbymonetiscope_render_display_slot_head');
add_action('wp_footer', 'adxbymonetiscope_render_display_slot_footer');

function adxbymonetiscope_render_display_slot_head() {
    adxbymonetiscope_render_display_slot_by_insertion(['before_post', 'after_post']);
}

function adxbymonetiscope_render_display_slot_footer() {
    adxbymonetiscope_render_display_slot_by_insertion(['before_paragraph', 'after_paragraph', 'before_image', 'after_image']);
}

// Core rendering function shared by head and footer
function adxbymonetiscope_render_display_slot_by_insertion($allowed_insertions = []) {
    if (is_admin()) return;
    if (get_option('display_slot_enabled') !== 'true') return;

    for ($i = 1; $i <= 10; $i++) {
        $enabled    = get_option("display_slot_{$i}_enabled") === 'true';
        $network    = trim(get_option("display_slot_{$i}_network_code", ''));
        $sizes      = get_option("display_slot_{$i}_sizes", []);
        $insertion  = get_option("display_slot_{$i}_insertion", '');
        $pages      = get_option("display_slot_{$i}_pages", []);

        // Only output if enabled, network code set, correct insertion, and sizes selected
        if (
            !$enabled ||
            !$network ||
            !in_array($insertion, $allowed_insertions) ||
            empty($sizes) ||
            empty($pages)
        ) continue;

        // Page filter: skip if not matching any selected page type
        if (!adxbymonetiscope_page_type_matches($pages)) continue;

        // 1. Get div ID from network code
        $div_id = adxbymonetiscope_extract_div_id($network);

        // 2. Convert sizes for JS array
        $js_sizes_str = adxbymonetiscope_sizes_js_array($sizes);

        // 3. Get website host for page_url
        $site_url = parse_url(get_site_url(), PHP_URL_HOST);

        // Output
        echo "<p>I'm here from plugin Display slot. Thats' all, don't look at me too much. Do your work.</p>";
        
        echo "<!-- AdX Display Slot #$i -->\n";
        echo "<script async src=\"https://securepubads.g.doubleclick.net/tag/js/gpt.js\"></script>\n";
        echo "<div id=\"" . esc_attr($div_id) . "\">\n";
        echo "<script>\n";
        echo "window.googletag = window.googletag || {cmd: []};\n";
        echo "googletag.cmd.push(function() {\n";
        echo "  googletag.defineSlot('" . esc_js($network) . "', $js_sizes_str, '" . esc_js($div_id) . "').addService(googletag.pubads());\n";
        echo "  googletag.enableServices();\n";
        echo "  googletag.pubads().set('page_url', '" . esc_js($site_url) . "');\n";
        echo "  googletag.display('" . esc_js($div_id) . "');\n";
        echo "});\n";
        echo "</script>\n";
        echo "</div>\n";
    }
}

// Helper: Page-type matching
function adxbymonetiscope_page_type_matches($pages) {
    foreach ($pages as $page_type) {
        switch ($page_type) {
            case 'post':
                if (is_single()) return true;
                break;
            case 'static':
                if (is_page()) return true;
                break;
            case 'homepage':
                if (is_front_page() || is_home()) return true;
                break;
            case 'search':
                if (is_search()) return true;
                break;
            case 'category':
                if (is_category()) return true;
                break;
            case 'tag':
                if (is_tag()) return true;
                break;
        }
    }
    return false;
}

// Helper: Extract div ID from network code (/account/slotid → slotid)
function adxbymonetiscope_extract_div_id($network) {
    $parts = explode('/', $network);
    return count($parts) >= 3 ? $parts[2] : preg_replace('/[^a-zA-Z0-9_]/', '', end($parts));
}

// Helper: Format sizes for JS
function adxbymonetiscope_sizes_js_array($sizes) {
    $out = [];
    foreach ($sizes as $sz) {
        $sz = strtolower(trim($sz));
        if ($sz === 'fluid') {
            $out[] = "'fluid'";
        } else {
            $nums = array_map('intval', explode('x', str_replace(' ', '', $sz)));
            if (count($nums) === 2 && $nums[0] && $nums[1]) {
                $out[] = "[" . $nums[0] . "," . $nums[1] . "]";
            }
        }
    }
    if (count($out) === 1) return $out[0];
    return "[" . implode(",", $out) . "]";
}
