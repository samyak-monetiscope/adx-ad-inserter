<?php
defined('ABSPATH') || exit;

/**
 * Render the Reward on Scroll slot if enabled
 */
function adx_render_reward_on_scroll_slot() {
    $enabled      = get_option('reward_on_scroll_enabled') === 'true';
    $network_code = trim( get_option('reward_on_scroll_network_code') );

    if ( ! $enabled || ! $network_code ) {
        return;
    }

    echo '<script>
    (function() {
        // Branding styles
        var style = document.createElement("style");
        style.innerHTML = `
            .branding {
                font-size: 12px;
                margin-top: 5px;
                color: black;
                text-align: center;
            }
            .branding .ads-by {
                color: red;
            }
            .branding .monetiscope {
                color: blue;
            }
        `;
        document.head.appendChild(style);

        // Download link container
        var downloadLink = document.createElement("div");
        downloadLink.id = "download-link";
        downloadLink.style.cssText = "display:none; position:fixed; bottom:10px; right:10px; z-index:10000;";
        downloadLink.innerHTML = \'<a href="your-download-link">Download your content</a>\';
        document.body.appendChild(downloadLink);

        // Load GPT
        var googletagScript = document.createElement("script");
         googletagScript.src = "https://www.googletagservices.com/tag/js/gpt.js";
         googletagScript.async = true;
        document.head.appendChild(googletagScript);

        googletagScript.onload = function() {
            window.googletag = window.googletag || { cmd: [] };

            function showRewardedAdOnScroll() {
                if (sessionStorage.getItem("rewardedAdShown")) {
                    return;
                }

                window.addEventListener("scroll", function onScroll() {
                    googletag.cmd.push(function () {
                        var rewardedSlot = googletag
                            .defineOutOfPageSlot(
                                ' . json_encode($network_code) . ',
                                googletag.enums.OutOfPageFormat.REWARDED,
                                [1, 1]
                            )
                            .addService(googletag.pubads());

                        googletag.enableServices();

                        googletag.pubads().addEventListener("rewardedSlotReady", function(evt) {
                            evt.makeRewardedVisible();
                        });

                        googletag.pubads().addEventListener("rewardedSlotGranted", function(evt) {
                            downloadLink.style.display = "block";
                            alert("Ad completed! You have unlocked the content for this session.");
                            sessionStorage.setItem("rewardedAdShown", "true");
                        });

                        googletag.pubads().addEventListener("rewardedSlotClosed", function() {
                            // No action on close
                        });

                        googletag.display(rewardedSlot);
                    });

                    window.removeEventListener("scroll", onScroll);
                });
            }

            window.addEventListener("load", showRewardedAdOnScroll);
        };
    })();
    </script>';
}
