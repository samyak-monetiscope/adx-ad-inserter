<?php
defined('ABSPATH') || exit;

function adx_render_coupon_rewarded_slot() {
    $enabled   = get_option('coupon_rewarded_enabled') === 'true';
    $netcode   = trim( get_option('coupon_rewarded_network_code') );
    $coupon    = trim( get_option('coupon_rewarded_code') );

    if ( ! $enabled || ! $netcode || ! $coupon ) {
        return;
    }

    // Safely prepare for JS
    $js_net    = json_encode($netcode);
    $js_coup   = json_encode($coupon);

    echo '<script>
    (function(){
      const networkCode = ' . $js_net . ';
      const couponCode  = ' . $js_coup . ';

      // 1. styles
      let style = document.createElement("style");
      style.textContent = `
        #coupon-box {
          position: fixed; top:50%; left:50%;
          transform:translate(-50%,-50%);
          background:#fff; border:1px solid #ccc;
          padding:20px; z-index:10000;
          display:none; text-align:center;
          border-radius:8px; font-family:sans-serif;
        }
        #coupon-box button { margin-top:12px; }
      `;
      document.head.appendChild(style);

      // 2. build coupon box
      var box = document.createElement("div");
      box.id = "coupon-box";
      box.innerHTML = `
        <h3>Here is your coupon:</h3>
        <p style="font-size:20px;font-weight:bold;">${couponCode}</p>
        <button id="coupon-close">Close</button>
      `;
      document.body.appendChild(box);
      document.getElementById("coupon-close")
        .addEventListener("click", ()=> box.style.display="none");

      // 3. load GPT & show on click
      let gt = document.createElement("script");
      // gt.src   = "https://www.googletagservices.com/tag/js/gpt.js";
      // gt.async = true;
      document.head.appendChild(gt);

      gt.onload = function(){
        window.googletag = window.googletag||{cmd:[]};
        googletag.cmd.push(function(){
          const slot = googletag.defineOutOfPageSlot(
            networkCode,
            googletag.enums.OutOfPageFormat.REWARDED
          ).addService(googletag.pubads());
          googletag.enableServices();

          googletag.pubads().addEventListener("rewardedSlotReady", evt=>{
            evt.makeRewardedVisible();
          });
          googletag.pubads().addEventListener("rewardedSlotGranted", ()=>{
            box.style.display = "block";
          });

          // trigger on page load:
          googletag.display(slot);
        });
      };
    })();
    </script>';
}
