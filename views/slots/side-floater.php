<?php
defined('ABSPATH') || exit;

/**
 * Render the Side Floater slot if enabled
 */
function adx_render_side_floater_slot() {
    $enabled      = get_option('side_floater_enabled') === 'true';
    $network_code = trim(get_option('side_floater_network_code'));

    if (!$enabled || !$network_code) {
        return;
    }

    // GPT library
    // echo '<script async src="https://securepubads.g.doubleclick.net/tag/js/gpt.js"></script>';

    // Floater CSS + HTML container
    echo '<style>
    #ad-container {
        position: fixed;
        bottom: 300px;
        left: -350px; /* Start hidden offscreen */
        background-color: transparent;
        padding: 0;
        z-index: 9999;
        display: none;
        transition: left 0.5s cubic-bezier(0.4,0,0.2,1); /* Smooth in/out */
    }
    #gpt-passback-float { position: relative; }
    #close-icon {
        position: absolute;
        top: -22px !important;
        right: 20px !important;
        background-color: #000 !important;
        color: #fff !important;
        font-weight: 300 !important;
        padding: 0px 6px !important;
        border-radius: 0px !important;
        font-size: 14px;
        cursor: pointer;
        z-index: 10001;
    }
    .branding {
        position: absolute; top: -18px; left: 0;
        background-color: white; font-size: 12px; padding: 2px 6px;
        border-radius: 4px; font-family: sans-serif; z-index: 10000;
    }
    .branding .powered { color: blue; }
    .branding .monetiscope { color: red; font-weight: bold; }
    </style>';

    echo '<div id="ad-container">
        <div class="branding">
            <span class="powered">Powered by</span>
            <span class="monetiscope">Monetiscope</span>
        </div>
        <div id="close-icon" onclick="closeAd()">×</div>
        <div id="gpt-passback-float"></div>
    </div>';

    // Floater GPT + toggle scripts with sizeMapping and animated left
    echo '<script>
    window.googletag = window.googletag || { cmd: [] };
    googletag.cmd.push(function () {
        var mapping = googletag.sizeMapping()
            .addSize([800, 0], [300, 250])
            .addSize([0, 0], [300, 50])
            .build();

        var slot = googletag.defineSlot(' . json_encode($network_code) . ',
            [300, 250],
            "gpt-passback-float"
        )
        .defineSizeMapping(mapping)
        .addService(googletag.pubads());

        googletag.pubads().addEventListener("slotRenderEnded", function(e) {
            var cont = document.getElementById("ad-container");
            if (e.slot === slot && e.isEmpty) {
                cont.style.display = "none";
            } else {
                // First, show the ad hidden offscreen
                cont.style.display = "block";
                cont.style.left = "-350px";
                // Then after a brief tick, slide it in
                setTimeout(function() {
                    cont.style.left = "0px";
                }, 50);
            }
        });

        googletag.pubads().set("page_url", window.location.hostname);
        googletag.pubads().enableLazyLoad({
            fetchMarginPercent: 50,
            renderMarginPercent: 25,
            mobileScaling: 1.5
        });
        googletag.enableServices();
        googletag.display("gpt-passback-float");
    });

    // Close button function
    function closeAd() {
        var cont = document.getElementById("ad-container");
        cont.style.left = "-350px";
        setTimeout(function() { cont.style.display = "none"; }, 500); // wait for animation
    }
    </script>';
}
