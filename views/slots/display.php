<?php
defined('ABSPATH') || exit;

/**
 * Keep existing hooks to avoid breaking, but do nothing here now.
 * We insert via the_content to support all 6 insertion types + offsets.
 */
add_action('wp_head', 'adxbymonetiscope_render_display_slot_head');
add_action('wp_footer', 'adxbymonetiscope_render_display_slot_footer');

function adxbymonetiscope_render_display_slot_head() {
    // Intentionally no-op. Insertion now handled in the_content.
    return;
}

function adxbymonetiscope_render_display_slot_footer() {
    // Intentionally no-op. Insertion now handled in the_content.
    return;
}

/**
 * Insert Display slot ads inside post content based on user-selected position & offset.
 * Applies page-type & device filters per sub-slot before inserting.
 */
add_filter('the_content', 'adxbymonetiscope_insert_display_ads', 9);
function adxbymonetiscope_insert_display_ads($content) {
    if (is_admin()) return $content;
    if (get_option('display_slot_enabled') !== 'true') return $content;

    // If you want stricter scope, uncomment:
    // if (!in_the_loop() || !is_main_query()) return $content;

    for ($i = 1; $i <= 10; $i++) {
        $enabled   = get_option("display_slot_{$i}_enabled") === 'true';
        $network   = trim(get_option("display_slot_{$i}_network_code", ''));
        $sizes     = get_option("display_slot_{$i}_sizes", []);
        $insertion = get_option("display_slot_{$i}_insertion", '');
        $pages     = get_option("display_slot_{$i}_pages", []);
        $devices   = get_option("display_slot_{$i}_devices", []);
        $offset    = absint(get_option("display_slot_{$i}_offset", 0)); // N for paragraph/image

        // Basic checks
        if (!$enabled || !$network || empty($sizes) || empty($pages)) {
            continue;
        }

        // 1) PAGE FILTER
        if (!adxbymonetiscope_page_type_matches($pages)) {
            continue;
        }

        // 2) DEVICE FILTER
        if (!adxbymonetiscope_device_type_matches($devices)) {
            continue;
        }

        // 3) Build dynamic ad HTML (network, sizes, div id, site host)
        $ad_html = adxbymonetiscope_build_ad_html($network, $sizes);

        // 4) Apply insertion logic
        switch ($insertion) {
            case 'before_post':
                $content = $ad_html . $content;
                break;

            case 'after_post':
                $content = $content . $ad_html;
                break;

            case 'before_paragraph':
                $content = adxbymonetiscope_insert_ad_around_nth_tag($content, 'p', $offset, 'before', $ad_html);
                break;

            case 'after_paragraph':
                $content = adxbymonetiscope_insert_ad_around_nth_tag($content, 'p', $offset, 'after', $ad_html);
                break;

            case 'before_image':
                $content = adxbymonetiscope_insert_ad_around_nth_tag($content, 'img', $offset, 'before', $ad_html);
                break;

            case 'after_image':
                $content = adxbymonetiscope_insert_ad_around_nth_tag($content, 'img', $offset, 'after', $ad_html);
                break;

            default:
                // Fallback: append at end
                $content = $content . $ad_html;
                break;
        }

        // If you only want ONE display sub-slot to fire per page, uncomment:
        // break;
    }

    return $content;
}

/** ---------------------------
 *  Helpers
 * ----------------------------*/

/**
 * Build the real GPT ad HTML for a slot with dynamic values:
 * - Network code (e.g. /23118073583/MS_Steppa_300x250_5)
 * - Sizes: single [w,h] or multi [[w,h],[w,h],'fluid']
 * - Div ID: last segment of the network code
 * - page_url: current site host
 */
function adxbymonetiscope_build_ad_html($network, $sizes) {
    $div_id       = adxbymonetiscope_extract_div_id($network);
    $js_sizes_str = adxbymonetiscope_sizes_js_array($sizes);
    $site_host    = parse_url(get_site_url(), PHP_URL_HOST);

    ob_start();
    ?>
    <!-- AdX Display Slot -->
    <div id="<?php echo esc_attr($div_id); ?>" class="adxbymonetiscope-display-slot" style="margin:12px 0;">
        
        <script async src="https://securepubads.g.doubleclick.net/tag/js/gpt.js"></script>
        <script>
        window.googletag = window.googletag || {cmd: []};
        googletag.cmd.push(function() {
            googletag.defineSlot(
                '<?php echo esc_js($network); ?>',
                <?php echo $js_sizes_str; ?>,
                '<?php echo esc_js($div_id); ?>'
            ).addService(googletag.pubads());
            googletag.enableServices();
            googletag.pubads().set('page_url', '<?php echo esc_js($site_host); ?>');
            googletag.display('<?php echo esc_js($div_id); ?>');
        });
        </script>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Page-type matching against the selected filters for the sub-slot.
 */
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

/**
 * Device-type matching based on display_slot_{i}_devices.
 * - If none or all three are selected: allow everywhere.
 * - Only mobile/tablet selected: allow only wp_is_mobile() === true
 * - Only desktop selected: allow only wp_is_mobile() === false
 */
function adxbymonetiscope_device_type_matches($devices) {
    if (empty($devices) || count($devices) === 3) {
        return true;
    }

    $has_mobile  = in_array('mobile', $devices, true) || in_array('tablet', $devices, true);
    $has_desktop = in_array('desktop', $devices, true);

    if ($has_mobile && !$has_desktop) {
        return wp_is_mobile();
    }
    if (!$has_mobile && $has_desktop) {
        return !wp_is_mobile();
    }
    // If both sets or unexpected input → allow
    return true;
}

/**
 * Extract the DIV id from network code: /account/slotid → slotid
 */
function adxbymonetiscope_extract_div_id($network) {
    $parts = explode('/', $network);
    return count($parts) >= 3 ? $parts[2] : preg_replace('/[^a-zA-Z0-9_]/', '', end($parts));
}

/**
 * Convert size strings to proper JS arrays:
 * - "300x250" => [300,250]
 * - "fluid"   => 'fluid'
 * Single size returns a single array; multiple sizes return an array of arrays.
 */
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

/**
 * Insert $ad_html before/after the Nth occurrence of <p> or <img>.
 * Fallbacks (per spec):
 * - invalid/zero offset → append at end
 * - fewer than N tags present → append at end
 * - no matching tags → append at end
 */
function adxbymonetiscope_insert_ad_around_nth_tag($content, $tag, $offset, $position, $ad_html) {
    if (!in_array($position, ['before', 'after'], true) || $offset < 1) {
        return $content . $ad_html;
    }

    $pattern = ($tag === 'img')
        ? '/(<img[^>]*>)/i'
        : '/(<\/?' . preg_quote($tag, '/') . '[^>]*>)/i';

    preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);
    $targets = $matches[1] ?? [];

    if (count($targets) < $offset) {
        return $content . $ad_html;
    }

    $insert_pos = $targets[$offset - 1][1];

    if ($position === 'before') {
        return substr($content, 0, $insert_pos) . $ad_html . substr($content, $insert_pos);
    }

    // After → place right after the matched tag
    $tag_html  = $targets[$offset - 1][0];
    $after_pos = $insert_pos + strlen($tag_html);
    return substr($content, 0, $after_pos) . $ad_html . substr($content, $after_pos);
}
