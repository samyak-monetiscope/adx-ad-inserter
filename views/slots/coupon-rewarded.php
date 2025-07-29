<?php
defined('ABSPATH') || exit;

function adx_render_coupon_rewarded_slot() {
    $enabled = get_option('coupon_rewarded_enabled') === 'true';
    $netcode = trim( get_option('coupon_rewarded_network_code') );
    $coupon  = trim( get_option('coupon_rewarded_code') );

    if ( ! $enabled || ! $netcode || ! $coupon ) {
        return;
    }

    // 1. Escape for JS
    $js_net  = esc_js( wp_json_encode( $netcode ) );
    $js_coup = esc_js( wp_json_encode( $coupon ) );

    // 2. Output everything in one clean echo
    echo '<script>(function(){
        var networkCode = ' . esc_js($js_net) . ';
        var couponCode  = ' . esc_js($js_coup). ';

        // Inject styles
        var style = document.createElement("style");
        style.textContent = `
            #coupon-box {
                position: fixed; top:50%; left:50%;
                transform:translate(-50%,-50%);
                background:#fff; border:1px solid #ccc;
                padding:20px; z-index:10000;
                display:none; text-align:center;
                border-radius:8px; font-family:sans-serif;
            }
            #coupon-close {
                position:absolute; top:-10px; right:-10px;
                width:24px; height:24px; 
                border:none; background:#000; color:#fff;
                font-size:16px; border-radius:50%;
                cursor:pointer;
            }
        `;
        document.head.appendChild(style);

        // Build coupon box
        var box = document.createElement("div");
        box.id = "coupon-box";
        box.innerHTML = `
            <h3>Here is your coupon:</h3>
            <p style="font-size:20px;font-weight:bold;">${couponCode}</p>
            <button id="coupon-close" aria-label="Close">×</button>
        `;
        document.body.appendChild(box);
        document.getElementById("coupon-close")
            .addEventListener("click", function(){ box.style.display = "none"; });

        // Load GPT & display rewarded ad
        var gt = document.createElement("script");
        document.head.appendChild(gt);
        gt.onload = function(){
            window.googletag = window.googletag || {cmd:[]};
            googletag.cmd.push(function(){
                var slot = googletag
                    .defineOutOfPageSlot(networkCode, googletag.enums.OutOfPageFormat.REWARDED)
                    .addService(googletag.pubads());
                googletag.enableServices();
                googletag.pubads().addEventListener("rewardedSlotReady", function(evt){
                    evt.makeRewardedVisible();
                });
                googletag.pubads().addEventListener("rewardedSlotGranted", function(){
                    box.style.display = "block";
                });
                googletag.display(slot);
            });
        };
    })();</script>';
}
