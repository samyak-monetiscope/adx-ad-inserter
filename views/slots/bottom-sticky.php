<?php
defined( 'ABSPATH' ) || exit;

/**
 * Render the Bottom Sticky slot if enabled
 */
function adx_render_bottom_sticky_slot() {
    $enabled      = get_option( 'bottom_sticky_enabled' ) === 'true';
    $network_code = trim( get_option( 'bottom_sticky_network_code' ) );

    if ( ! $enabled || ! $network_code ) {
        return;
    }

    // Escape for JS
    $code = esc_js( wp_json_encode( $network_code ) );

    // Single-echo of entire <script> block
    echo '<script>
    (function () {
        var gptScript = document.createElement("script");
        document.head.appendChild(gptScript);

        gptScript.onload = function () {
            window.googletag = window.googletag || { cmd: [] };
            googletag.cmd.push(function () {
                var REFRESH_KEY   = "refresh";
                var REFRESH_VALUE = "true";

                var mapping = googletag.sizeMapping()
                    .addSize([800, 90], [728, 90])
                    .addSize([0, 0], [320, 50])
                    .build();

                var slot = googletag.defineSlot(' . esc_js($code) . ', [728, 90], "gpt-ad-sticky")
                    .setTargeting(REFRESH_KEY, REFRESH_VALUE)
                    .defineSizeMapping(mapping)
                    .addService(googletag.pubads());

                googletag.pubads().addEventListener("impressionViewable", function (event) {
                    if (event.slot === slot &&
                        event.slot.getTargeting(REFRESH_KEY).indexOf(REFRESH_VALUE) > -1) {
                        setTimeout(function () {
                            googletag.pubads().refresh([slot]);
                        }, 30000);
                    }
                });

                googletag.pubads().enableSingleRequest();
                googletag.enableServices();
            });

            var container = document.createElement("div");
            container.id = "sticky_ad_beta";
            container.style.cssText = "width:100%;text-align:center;background:#fff;position:fixed;bottom:0;left:0;z-index:99;padding-top:4px;box-shadow:0 -3px 3px rgba(0,0,0,0.2)";

            var closeBtn = document.createElement("span");
            closeBtn.id = "close_sticky_ad_beta";
            closeBtn.innerHTML = "×";
            closeBtn.style.cssText = "position:absolute;top:-20px;right:12px;background:#fff;color:#000;font-size:26px;line-height:20px;cursor:pointer;box-shadow:0 -3px 3px rgba(0,0,0,0.2)";
            closeBtn.onclick = function () { container.style.display = "none"; };
            container.appendChild(closeBtn);

            var branding = document.createElement("div");
            branding.className = "desktop-branding";
            branding.style.cssText = "position:absolute;top:-20px;left:12px;font-size:12px;background:#fff;padding:2px 8px;border-radius:4px 4px 0 0;box-shadow:0 -3px 3px rgba(0,0,0,0.2)";
            branding.innerHTML =
                "<span style=\'color:#ff0000;\'>Ads By</span> " +
                "<a href=\'https://monetiscope.com\' target=\'_blank\' style=\'color:#206cd7;text-decoration:none;\'>Monetiscope</a>";
            container.appendChild(branding);

            var adSlot = document.createElement("div");
            adSlot.id = "gpt-ad-sticky";
            container.appendChild(adSlot);

            document.body.appendChild(container);

            googletag.cmd.push(function () {
                googletag.display("gpt-ad-sticky");
            });
        };
    })();
    </script>';
}
