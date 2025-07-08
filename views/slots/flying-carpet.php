<?php
defined('ABSPATH') || exit;

/**
 * Render the Flying Carpet ad slot using GPT with parallax loader
 */
function adxbymonetiscope_render_flying_carpet_slot() {
    $enabled = get_option('adxbymonetiscope_flying_enabled') === 'true';
    $network_code = trim(get_option('adxbymonetiscope_flying_network_code'));

    if (! $enabled || empty($network_code)) {
        return;
    }

    // Register & enqueue empty style handle for inline CSS
    wp_register_style('adxbymonetiscope_flying_style', false);
    wp_enqueue_style('adxbymonetiscope_flying_style');

    $css = <<<CSS
/* Outer wrapper */
.parallax-ad-container {
    position: relative;
    width: 100%;
    height: 320px;
    margin: 0 auto;
    overflow: auto;
}
/* Inner container */
.parallax-ad {
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    border: 0 !important;
    padding: 0 !important;
    clip: rect(0, auto, auto, 0) !important;
}
/* GPT slot div + iframe */
.parallax-ad > div,
.parallax-ad > div > iframe {
    display: block !important;
    margin: auto !important;
}
.parallax-ad > div[id^="google_ads_iframe"] {
    width: max-content !important;
}
/* Fixed parallax positioning */
.parallax-ad > iframe {
    position: fixed;
    top: 130px;
    height: 100%;
    transform: translateX(-50%);
    margin-left: 0 !important;
}
/* Spinner loader */
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
/* Ad label */
.ad-label {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 0.875rem;
    color: #8d969e;
    text-align: center;
    padding: 1rem 1rem 0 1rem;
}
CSS;

    wp_add_inline_style('adxbymonetiscope_flying_style', $css);

    // Render base DOM
    echo '<div id="adxbymonetiscope-parallax-wrapper">
            <div id="adxbymonetiscope-loader" class="monetiscope-loader"></div>
            <div id="ADXBMONETISCOPE_FLYING_SLOT"></div>
          </div>
          <div class="ad-label">
            <p>Advertisement</p>
          </div>';

    // Register & enqueue script handle
    wp_register_script('adxbymonetiscope_flying_script', false);
    wp_enqueue_script('adxbymonetiscope_flying_script');

    $escaped_code = wp_json_encode(esc_js($network_code));

    $js = <<<JS
(function(){
    var gptScript = document.createElement("script");
    gptScript.src = "https://securepubads.g.doubleclick.net/tag/js/gpt.js";
    gptScript.async = true;
    document.head.appendChild(gptScript);

    gptScript.onload = function(){
        window.googletag = window.googletag || {cmd: []};
        googletag.cmd.push(function() {
            googletag.defineSlot(
                {$escaped_code},
                [300, 600],
                "ADXBMONETISCOPE_FLYING_SLOT"
            ).addService(googletag.pubads());

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

                var parentDiv = container.parentElement;
                parentDiv.classList.add("parallax-ad-container");
                container.classList.add("parallax-ad");

                clearInterval(interval);
            }
        }, 100);
    });
})();
JS;

    wp_add_inline_script('adxbymonetiscope_flying_script', $js);
}
