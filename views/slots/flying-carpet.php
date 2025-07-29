<?php
defined( 'ABSPATH' ) || exit;

/**
 * 1️⃣ Enqueue Flying Carpet assets (CSS & JS) on the front end
 */
function adxbymonetiscope_flying_carpet_assets() {
    $enabled      = get_option( 'flying_enabled' ) === 'true';
    $network_code = trim( get_option( 'flying_network_code' ) );

    if ( ! $enabled || empty( $network_code ) ) {
        return;
    }

    // — Register dummy style (inline CSS) with explicit version
    wp_register_style( 'adxbymonetiscope_flying_style', false, [], '0.2.0' );
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
            margin: auto !important;
        }
        .parallax-ad > div[id^="google_ads_iframe"] {
            width: max-content !important;
        }
        .parallax-ad > iframe {
            position: fixed;
            top: 130px;
            height: 100%;
            transform: translateX(-50%);
            margin-left: 0 !important;
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
    ';

    wp_add_inline_style( 'adxbymonetiscope_flying_style', $inline_css );

    // — Register dummy script (inline JS) with explicit version
    wp_register_script( 'adxbymonetiscope_flying_script', false, [], '0.2.0', true );
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
 */
function adxbymonetiscope_render_flying_carpet_slot() {
    $enabled      = get_option( 'flying_enabled' ) === 'true';
    $network_code = trim( get_option( 'flying_network_code' ) );

    if ( ! $enabled || empty( $network_code ) ) {
        return;
    }

    echo '
        <div id="customParallax">
            <div id="adxbymonetiscope-loader" class="monetiscope-loader"></div>
            <div id="ADXBMONETISCOPE_FLYING_SLOT"></div>
        </div>
        <div class="ad-label">
            <p>' . esc_html__( 'Advertisement', 'adx-ad-inserter' ) . '</p>
        </div>
    ';
}
