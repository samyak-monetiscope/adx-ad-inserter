<?php
defined('ABSPATH') || exit;

if ( ! defined('DISPLAY_GPT_VERSION') ) {
    define('DISPLAY_GPT_VERSION', '1.0.0');
}


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
        $offset    = absint(get_option("display_slot_{$i}_offset", 0));
        $alignment = strtolower(trim((string) get_option("display_slot_{$i}_alignment", 'left')));
 // N for paragraph/image

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
        $ad_html = adxbymonetiscope_build_ad_html($network, $sizes, $i, $alignment);

        // 4) Apply insertion logic
        switch ($insertion) {
            case 'before_post':
                
                $content = adxbymonetiscope_insert_ad_before_first_h1($content, $ad_html);

                // $content = $ad_html . $content;
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
function adxbymonetiscope_build_ad_html($network, $sizes, $slot_index = null, $alignment = 'left') {
    $div_id       = adxbymonetiscope_extract_div_id($network);
    $js_sizes_str = adxbymonetiscope_sizes_js_array($sizes);
    $site_host    = wp_parse_url(get_site_url(), PHP_URL_HOST);
        // Normalize alignment
    $alignment = in_array($alignment, ['left','center','right'], true) ? $alignment : 'left';

    // Compute inline style based on alignment
    // - left: default flow
    // - center: shrink-to-content and center
    // - right: shrink-to-content and push to the right
    if ($alignment === 'center') {
        $align_style = 'display:table;margin:12px auto; text-align:center;';
    } elseif ($alignment === 'right') {
        $align_style = 'display:table;margin-right:0 !important; width:fit-content; text-align:end;';
    } else { // left
        $align_style = 'margin-left: 0 !important;';
    }
    wp_register_script(
        'gpt',
        'https://securepubads.g.doubleclick.net/tag/js/gpt.js',
        array(),
        DISPLAY_GPT_VERSION,   // let Google manage caching
        true    // footer
    );
    wp_enqueue_script('gpt');

    ob_start();
    ?>
    <div id="<?php echo esc_attr($div_id); ?>" class="adxbymonetiscope-display-slot" style="<?php echo esc_attr($align_style); ?>">

        <span aria-hidden="true" data-adx-debug="display-slot" style="opacity:0.5;display:block; font-size:10px;">
            Display Advertisement <?php echo esc_html($slot_index !== null ? (int)$slot_index : '-'); ?>
        </span>

        
        <script>
        window.googletag = window.googletag || {cmd: []};
        googletag.cmd.push(function() {
            googletag.defineSlot(
                '<?php echo esc_js($network); ?>',
                <?php echo esc_js($js_sizes_str); ?>,
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
    // Normalize incoming array and map 'tablet' to 'mobile' so we have exactly two groups.
    $norm = [];
    foreach ((array)$devices as $d) {
        $d = strtolower(trim($d));
        if ($d === 'tablet') $d = 'mobile';
        if (in_array($d, ['desktop', 'mobile'], true)) {
            $norm[$d] = true; // use keys to dedupe
        }
    }
    $has_desktop = isset($norm['desktop']);
    $has_mobile  = isset($norm['mobile']);

    // None or both selected → allow everywhere
    if ((!$has_desktop && !$has_mobile) || ($has_desktop && $has_mobile)) {
        return true;
    }

    // Only mobile/tablet selected
    if ($has_mobile && !$has_desktop) {
        return wp_is_mobile();
    }

    // Only desktop selected
    if ($has_desktop && !$has_mobile) {
        return !wp_is_mobile();
    }

    // Safety fallback
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
 * Insert ad before the first <h1> in content.
 * Fallback (no <h1> found): prepend at the start of content.
 */
function adxbymonetiscope_insert_ad_before_first_h1($content, $ad_html) {
    $pattern = '/(<h1\b[^>]l*>)/i';

    if (preg_match($pattern, $content, $m, PREG_OFFSET_CAPTURE)) {// preg_match is taking 3 args i.e. preg_match($pattern, $content, $m). pattern is for finding <h1>, $m is for storing the position of <h1>, preg_offset_capture is for storing the position of <h1>
        $pos = $m[1][1]; // byte offset of the opening <h1>
        return substr($content, 0, $pos) . $ad_html . substr($content, $pos);
    }

    // Fallback: no <h1> inside content → prepend
    return $ad_html . $content;
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

    // Special handling for paragraphs: skip empty <p>...</p>
    if ($tag === 'p') {
        // Match full paragraph blocks and capture inner HTML
        if (!preg_match_all('/<p\b[^>]*>(.*?)<\/p>/is', $content, $matches, PREG_OFFSET_CAPTURE)) {
            // No <p> blocks → fallback append
            return $content . $ad_html;
        }

        // Build a list of non-empty paragraph matches with offsets
        $valid = [];
        foreach ($matches[0] as $idx => $fullMatch) {
            $fullHtml  = $fullMatch[0];       // entire <p>...</p>
            $fullPos   = $fullMatch[1];       // offset of <p>
            $innerHtml = $matches[1][$idx][0]; // inner content inside <p>...</p>

            // Normalize & test emptiness
            $decoded   = html_entity_decode($innerHtml, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $normalized = trim(preg_replace('/\x{00A0}/u', ' ', str_replace('&nbsp;', ' ', wp_strip_all_tags($decoded)))); // replace NBSP + strip tags
            if ($normalized === '') {
                continue; // skip empty paragraph
            }

            $valid[] = [
                'full_html' => $fullHtml,
                'pos'       => $fullPos,
                'len'       => strlen($fullHtml),
            ];
        }

        if (count($valid) < $offset) {
            // Not enough non-empty paragraphs → fallback append
            return $content . $ad_html;
        }

        $target = $valid[$offset - 1];

        if ($position === 'before') {
            return substr($content, 0, $target['pos']) . $ad_html . substr($content, $target['pos']);
        } else {
            $after_pos = $target['pos'] + $target['len'];
            return substr($content, 0, $after_pos) . $ad_html . substr($content, $after_pos);
        }
    }

    // Original logic for non-<p> tags (unchanged)
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
