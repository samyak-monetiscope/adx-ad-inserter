<?php
// File: /wp-content/plugins/adx-ad-inserter/includes/slots/flying-carpet.php
// Note: Keeps your original function names:
//   - adxbymonetiscope_flying_carpet_assets()
//   - adxbymonetiscope_render_flying_carpet_slot()
// Adds single-slot insertion via the_content, alignment support, page/device gates,
// and a light-green debug line with current config JSON.

defined( 'ABSPATH' ) || exit;

/**
 * 1️⃣ Enqueue Flying Carpet assets (CSS & JS) on the front end
 *    (Gated by pages/devices to avoid loading when not needed)
 */
function adxbymonetiscope_flying_carpet_assets() {
    $enabled      = get_option( 'flying_enabled' ) === 'true';
    $network_code = trim( (string) get_option( 'flying_network_code' ) );

    if ( ! $enabled || empty( $network_code ) ) {
        return;
    }

    // Gates for conditional enqueue
    $pages   = (array) get_option( 'flying_pages', [] );
    $devices = (array) get_option( 'flying_devices', [] );

    // Must have at least one page filter selected
    if ( empty( $pages ) ) {
        return;
    }
    if ( ! adxbymonetiscope_fcarpet_page_type_matches( $pages ) ) {
        return;
    }
    if ( ! adxbymonetiscope_fcarpet_device_type_matches( $devices ) ) {
        return;
    }

    // — Register style (inline CSS) with explicit version
    wp_register_style( 'adxbymonetiscope_flying_style', false, [], '0.3.0' );
    wp_enqueue_style( 'adxbymonetiscope_flying_style' );

    $inline_css = '
        .parallax-ad-container {
            position: relative;
            width: 100%;
            height: 320px;
            margin: 0 auto;
            overflow: auto;
        }
        .parallax-ad {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            border: 0 !important;
            padding: 0 !important;
            clip: rect(0, auto, auto, 0) !important;
        }
        .parallax-ad > div,
        .parallax-ad > div > iframe {
            display: block !important;
        }
        .parallax-ad > div[id^="google_ads_iframe"] {
            width: max-content !important;
        }
        .parallax-ad > iframe {
            position: fixed;
            top: 130px;
            height: 100%;
        }
        .monetiscope-loader {
            position: fixed;
            background: white;
            top: 41px;
            left: 50%;
            width: 48px;
            height: 48px;
            margin: -24px 0 0 -24px;
            border: 6px solid rgba(0,0,0,0.1);
            border-top-color: rgba(0,0,0,0.4);
            border-radius: 50%;
            animation: monetiscope-spin 2s linear infinite;
        }
        @keyframes monetiscope-spin {
            to { transform: rotate(360deg); }
        }
        .ad-label {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.875rem;
            color: #8d969e;
            text-align: center;
            padding: 1rem 1rem 0 1rem;
        }

        /* -------- Alignment control (left|center|right) -------- */
        .adxb-align-left .parallax-ad > div,
        .adxb-align-left .parallax-ad > div > iframe {
            margin-left: 0 !important;
            margin-right: auto !important;
        }
        .adxb-align-center .parallax-ad > div,
        .adxb-align-center .parallax-ad > div > iframe {
            margin-left: auto !important;
            margin-right: auto !important;
        }
        .adxb-align-right .parallax-ad > div,
        .adxb-align-right .parallax-ad > div > iframe {
            margin-left: auto !important;
            margin-right: 0 !important;
        }
        /* Fixed iframe horizontal positioning per alignment */
        .adxb-align-left  .parallax-ad > iframe { left: 0;   right: auto; transform: none; }
        .adxb-align-center .parallax-ad > iframe { left: 50%; right: auto; transform: translateX(-50%); }
        .adxb-align-right .parallax-ad > iframe { right: 0;  left: auto;  transform: none; }

        /* Debug line style */
        .adxb-flying-debug {
            background:#d4edda;
            padding:6px;
            font-size:13px;
            margin-bottom:8px;
            border:1px solid #c3e6cb;
            color:#155724;
            word-break: break-word;
        }
    ';
    wp_add_inline_style( 'adxbymonetiscope_flying_style', $inline_css );

    // — Register script (inline JS) with explicit version
    wp_register_script( 'adxbymonetiscope_flying_script', false, [], '0.3.0', true );
    wp_enqueue_script( 'adxbymonetiscope_flying_script' );

    $network_code_json = esc_js( wp_json_encode( $network_code ) );

    $inline_js = '
        (function(){
            var gptScript = document.createElement("script");
            gptScript.src = "https://securepubads.g.doubleclick.net/tag/js/gpt.js";
            gptScript.async = true;
            document.head.appendChild(gptScript);

            gptScript.onload = function() {
                window.googletag = window.googletag || { cmd: [] };
                googletag.cmd.push(function(){
                    googletag.defineSlot(' . $network_code_json . ', [300, 600], "ADXBMONETISCOPE_FLYING_SLOT")
                        .addService(googletag.pubads());
                    googletag.pubads().set("page_url", window.location.hostname);
                    googletag.enableServices();
                    googletag.display("ADXBMONETISCOPE_FLYING_SLOT");
                });
            };

            document.addEventListener("DOMContentLoaded", function(){
                var interval = setInterval(function(){
                    var container = document.getElementById("ADXBMONETISCOPE_FLYING_SLOT");
                    if (container && container.querySelector("iframe")) {
                        var loader = document.getElementById("adxbymonetiscope-loader");
                        if (loader) loader.style.display = "none";

                        container.parentElement.classList.add("parallax-ad-container");
                        container.classList.add("parallax-ad");
                        clearInterval(interval);
                    }
                }, 100);
            });
        })();
    ';

    wp_add_inline_script( 'adxbymonetiscope_flying_script', $inline_js );
}
add_action( 'wp_enqueue_scripts', 'adxbymonetiscope_flying_carpet_assets' );

/**
 * 2️⃣ Render the Flying Carpet slot markup in the footer
 *    (We will NOT hook this to wp_footer by default; insertion is done via the_content.
 *     Keeping function name intact per your request.)
 */
function adxbymonetiscope_render_flying_carpet_slot() {
    // Echo the same markup used by content insertion
    echo wp_kses_post(adxbymonetiscope_build_flying_carpet_html());
}

/**
 * 3️⃣ Insert Flying Carpet via the_content (single-slot behavior)
 */
add_filter( 'the_content', 'adxbymonetiscope_insert_flying_carpet', 9 );
function adxbymonetiscope_insert_flying_carpet( $content ) {
    if ( is_admin() ) return $content;

    $enabled      = get_option( 'flying_enabled' ) === 'true';
    $network_code = trim( (string) get_option( 'flying_network_code' ) );

    if ( ! $enabled || $network_code === '' ) return $content;

    $pages     = (array) get_option( 'flying_pages', [] );
    $devices   = (array) get_option( 'flying_devices', [] );
    $insertion = (string) get_option( 'flying_insertion', '' );
    $offset    = absint( get_option( 'flying_offset', 0 ) );

    // Gates
    if ( empty( $pages ) ) return $content;
    if ( ! adxbymonetiscope_fcarpet_page_type_matches( $pages ) ) return $content;
    if ( ! adxbymonetiscope_fcarpet_device_type_matches( $devices ) ) return $content;

    // Build markup
    $ad_html = adxbymonetiscope_build_flying_carpet_html();
    if ( $ad_html === '' ) return $content;

    switch ( $insertion ) {
        case 'before_post':
            return adxbymonetiscope_fcarpet_insert_ad_before_first_h1( $content, $ad_html );

        case 'after_post':
            return $content . $ad_html;

        case 'before_paragraph':
            return adxbymonetiscope_fcarpet_insert_ad_around_nth_tag( $content, 'p', $offset, 'before', $ad_html );

        case 'after_paragraph':
            return adxbymonetiscope_fcarpet_insert_ad_around_nth_tag( $content, 'p', $offset, 'after', $ad_html );

        case 'before_image':
            return adxbymonetiscope_fcarpet_insert_ad_around_nth_tag( $content, 'img', $offset, 'before', $ad_html );

        case 'after_image':
            return adxbymonetiscope_fcarpet_insert_ad_around_nth_tag( $content, 'img', $offset, 'after', $ad_html );

        default:
            // Fallback → append
            return $content . $ad_html;
    }
}

/**
 * 4️⃣ Build the Flying Carpet markup (used by both insertion and render function)
 *     Includes a light-green debug line with current configuration JSON.
 */
function adxbymonetiscope_build_flying_carpet_html() {
    $enabled       = get_option( 'flying_enabled' ) === 'true';
    $network_code  = trim( (string) get_option( 'flying_network_code' ) );
    if ( ! $enabled || $network_code === '' ) {
        return '';
    }

    $insertion = (string) get_option( 'flying_insertion', '' );
    $offset    = (int) get_option( 'flying_offset', 0 );
    $pages     = (array) get_option( 'flying_pages', [] );
    $devices   = (array) get_option( 'flying_devices', [] );
    $alignment = (string) get_option( 'flying_alignment', 'center' );
    if ( ! in_array( $alignment, ['left','center','right'], true ) ) {
        $alignment = 'center';
    }
    $align_class = 'adxb-align-' . $alignment;

    // Debug payload
    $debug_settings = [
        'enabled'   => $enabled,
        'network'   => $network_code,
        'insertion' => $insertion,
        'offset'    => $offset,
        'pages'     => array_values( $pages ),
        'devices'   => array_values( $devices ),
        'alignment' => $alignment,
    ];

    ob_start();
    ?>
    <div id="customParallax" class="<?php echo esc_attr( $align_class ); ?>">

        <!-- Debug line (light green) -->
        <div class="adxb-flying-debug">
            <?php
            echo esc_html(
                'This is from flying carpet, with configuration as ' .
                wp_json_encode( $debug_settings )
            );
            ?>
        </div>

        <div id="adxbymonetiscope-loader" class="monetiscope-loader"></div>
        <div id="ADXBMONETISCOPE_FLYING_SLOT"></div>
    </div>
    <div class="ad-label">
        <p><?php echo esc_html__( 'Advertisement', 'adx-ad-inserter' ); ?></p>
    </div>
    <?php
    return ob_get_clean();
}

/* ===========================
 * Helpers (page/device/insertion)
 * ===========================*/

/**
 * Page-type matching against selected filters.
 */
function adxbymonetiscope_fcarpet_page_type_matches( $pages ) {
    foreach ( (array) $pages as $page_type ) {
        switch ( $page_type ) {
            case 'post':
                if ( is_single() ) return true;
                break;
            case 'static':
                if ( is_page() ) return true;
                break;
            case 'homepage':
                if ( is_front_page() || is_home() ) return true;
                break;
            case 'search':
                if ( is_search() ) return true;
                break;
            case 'category':
                if ( is_category() ) return true;
                break;
            case 'tag':
                if ( is_tag() ) return true;
                break;
        }
    }
    return false;
}

/**
 * Device-type matching based on flying_devices.
 * - None or both selected → allow everywhere
 * - Only mobile/tablet selected → wp_is_mobile()
 * - Only desktop selected → !wp_is_mobile()
 */
function adxbymonetiscope_fcarpet_device_type_matches( $devices ) {
    $norm = [];
    foreach ( (array) $devices as $d ) {
        $d = strtolower( trim( $d ) );
        if ( $d === 'tablet' ) $d = 'mobile';
        if ( in_array( $d, ['desktop','mobile'], true ) ) {
            $norm[ $d ] = true;
        }
    }
    $has_desktop = isset( $norm['desktop'] );
    $has_mobile  = isset( $norm['mobile'] );

    if ( ( ! $has_desktop && ! $has_mobile ) || ( $has_desktop && $has_mobile ) ) {
        return true;
    }
    if ( $has_mobile && ! $has_desktop )  return wp_is_mobile();
    if ( $has_desktop && ! $has_mobile )  return ! wp_is_mobile();
    return true;
}

/**
 * Insert $ad_html before the first <h1>. Fallback: prepend at start.
 */
function adxbymonetiscope_fcarpet_insert_ad_before_first_h1( $content, $ad_html ) {
    $pattern = '/(<h1\b[^>]*>)/i';
    if ( preg_match( $pattern, $content, $m, PREG_OFFSET_CAPTURE ) ) {
        $pos = $m[1][1];
        return substr( $content, 0, $pos ) . $ad_html . substr( $content, $pos );
    }
    return $ad_html . $content;
}

/**
 * Insert $ad_html before/after the Nth <p> or <img>.
 * Fallbacks:
 * - invalid/zero offset → append at end
 * - fewer than N tags present → append at end
 */
function adxbymonetiscope_fcarpet_insert_ad_around_nth_tag( $content, $tag, $offset, $position, $ad_html ) {
    if ( ! in_array( $position, ['before','after'], true ) || $offset < 1 ) {
        return $content . $ad_html;
    }

    if ( $tag === 'p' ) {
        // Match <p>...</p> blocks (capture inner HTML)
        if ( ! preg_match_all( '/<p\b[^>]*>(.*?)<\/p>/is', $content, $matches, PREG_OFFSET_CAPTURE ) ) {
            return $content . $ad_html;
        }

        // Keep only non-empty paragraphs
        $valid = [];
        foreach ( $matches[0] as $idx => $fullMatch ) {
            $fullHtml  = $fullMatch[0];
            $fullPos   = $fullMatch[1];
            $innerHtml = $matches[1][ $idx ][0];

            $decoded    = html_entity_decode( $innerHtml, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
            $normalized = trim( preg_replace( '/\x{00A0}/u', ' ', str_replace( '&nbsp;', ' ', wp_strip_all_tags( $decoded ) ) ) );
            if ( $normalized === '' ) continue;

            $valid[] = [
                'full_html' => $fullHtml,
                'pos'       => $fullPos,
                'len'       => strlen( $fullHtml ),
            ];
        }

        if ( count( $valid ) < $offset ) return $content . $ad_html;

        $target = $valid[ $offset - 1 ];
        if ( $position === 'before' ) {
            return substr( $content, 0, $target['pos'] ) . $ad_html . substr( $content, $target['pos'] );
        }
        $after_pos = $target['pos'] + $target['len'];
        return substr( $content, 0, $after_pos ) . $ad_html . substr( $content, $after_pos );
    }

    // Generic for <img> (and other simple tags)
    $pattern = ( $tag === 'img' )
        ? '/(<img[^>]*>)/i'
        : '/(<\/?' . preg_quote( $tag, '/' ) . '[^>]*>)/i';

    preg_match_all( $pattern, $content, $matches, PREG_OFFSET_CAPTURE );
    $targets = $matches[1] ?? [];
    if ( count( $targets ) < $offset ) return $content . $ad_html;

    $insert_pos = $targets[ $offset - 1 ][1];

    if ( $position === 'before' ) {
        return substr( $content, 0, $insert_pos ) . $ad_html . substr( $content, $insert_pos );
    }

    $tag_html  = $targets[ $offset - 1 ][0 ];
    $after_pos = $insert_pos + strlen( $tag_html );
    return substr( $content, 0, $after_pos ) . $ad_html . substr( $content, $after_pos );
}
