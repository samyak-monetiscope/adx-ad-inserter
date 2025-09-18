<?php
defined('ABSPATH') || exit;

/**
 * Render the Offerwall (on Scroll) slot if enabled
 */
function adx_render_offerwall_onscroll_slot() {
    $enabled      = get_option('offerwall_onscroll_enabled') === 'true';
    $network_code = trim( get_option('offerwall_onscroll_network_code') );
    $logo_url     = trim( get_option('offerwall_onscroll_logo_url') );

    // Fallback logo if none provided
    if ( ! $logo_url ) {
        $logo_url = 'https://monetiscope.com/wp-content/uploads/2025/05/cropped-e-2.png';
    }

    if ( ! $enabled || ! $network_code ) {
        return;
    }

    // ✅ Secure escaping for JS output
    $js_logo_url     = wp_json_encode( $logo_url );
    $js_network_code = wp_json_encode( $network_code );

    echo '<script>
    (function() {
        var logoUrl = ' . esc_url_raw($js_logo_url) . ';
        var networkCode = ' . esc_js($js_network_code ). ';

        // 1. Inject CSS
        var style = document.createElement("style");
        style.innerHTML = `
            #notification-bar {
                position: fixed;
                top: 50%; left: 50%;
                transform: translate(-50%, -50%);
                width: 30%;
                background: #fff;
                border: 1px solid #ccc;
                padding: 20px;
                box-shadow: 0 0 15px rgba(0,0,0,0.2);
                z-index: 10000;
                display: none;
                text-align: center;
                border-radius: 8px;
                font-family: Arial, sans-serif;
            }
            #publisher-logo {
                height: 80px;
                margin: 0 auto 20px;
                display: block;
            }
            #notification-bar h2 {
                font-size: 18px;
                color: #666;
                font-weight: bold;
                margin-bottom: 10px;
            }
            #notification-bar p {
                font-size: 14px;
                color: #555;
                margin-bottom: 20px;
            }
            .notification-button {
                background: #007bff;
                color: #fff;
                border-radius: 5px;
                padding: 10px 20px;
                font-size: 14px;
                cursor: pointer;
                border: none;
                margin-bottom: 10px;
                position: relative;
            }
            .notification-button .loading {
                display: none;
                margin-left: 8px;
                font-size: 12px;
            }
            .notification-button:hover,
            .notification-button:focus,
            .notification-button:active {
                background: #0056b3;
            }
            .branding {
                font-size: 12px;
                margin-top: 5px;
                color: black;
                text-align: center;
            }
            .branding .ads-by { color: red; }
            .branding .monetiscope { color: blue; }
            @media (max-width: 768px) {
                #notification-bar { width: 80%; padding: 15px; }
                #publisher-logo { height: 50px; }
                #notification-bar h2 { font-size: 16px; }
                #notification-bar p  { font-size: 12px; }
                .notification-button { padding: 8px 16px; font-size: 12px; }
            }
        `;
        document.head.appendChild(style);

        // 2. Build notification bar HTML
        var notificationBar = document.createElement("div");
        notificationBar.id = "notification-bar";
        notificationBar.innerHTML = `
            <img id="publisher-logo" src="${logoUrl}" alt="Publisher Logo">
            <h2>Unlock more content</h2>
            <p>Take action to continue accessing the content on this site</p>
            <button class="notification-button">
                View a short ad
                <span class="loading">Loading...</span>
            </button>
            <div class="branding">
                <span class="ads-by">Ads by</span>
                <a href="https://monetiscope.com" class="monetiscope" target="_blank">Monetiscope</a>
            </div>
        `;
        document.body.appendChild(notificationBar);

        // 3. Show on scroll >30%
        window.addEventListener("scroll", function() {
            var pct = (window.scrollY + window.innerHeight)
                    / document.documentElement.scrollHeight * 100;
            if (pct > 30 && !sessionStorage.getItem("offerwallShown")) {
                notificationBar.style.display = "block";
            }
        });

        // 4. Load GPT library
        var googletagScript = document.createElement("script");
        googletagScript.src = "https://www.googletagservices.com/tag/js/gpt.js";
        googletagScript.async = true;
        document.head.appendChild(googletagScript);

        // 5. Prepare to capture rewarded event
        var rewardedEvt = null;
        var button = notificationBar.querySelector(".notification-button");
        var loading = button.querySelector(".loading");

        // 6. Click handler: show loading & then ad
        button.addEventListener("click", function() {
            loading.style.display = "inline";
            if (rewardedEvt) {
                notificationBar.style.display = "none";
                rewardedEvt.makeRewardedVisible();
            }
        });

        // 7. GPT onload setup
        googletagScript.onload = function() {
            window.googletag = window.googletag || {cmd:[]};
            googletag.cmd.push(function() {
                var slot = googletag.defineOutOfPageSlot(
                    networkCode,
                    googletag.enums.OutOfPageFormat.REWARDED
                ).addService(googletag.pubads());

                googletag.enableServices();

                googletag.pubads().addEventListener("rewardedSlotReady", function(evt) {
                    rewardedEvt = evt;
                });
                googletag.pubads().addEventListener("rewardedSlotGranted", function() {
                    sessionStorage.setItem("offerwallShown","true");
                });
                googletag.pubads().addEventListener("rewardedSlotClosed", function() {
                    loading.style.display = "none";
                });

                googletag.display(slot);
            });
        };
    })();
    </script>';
}
