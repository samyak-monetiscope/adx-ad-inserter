<?php
defined('ABSPATH') || exit;

/**
 * Render the Popup Ad slot if enabled
 */
function adxbymonetiscope_render_popup_slot() {
    $enabled = get_option('adxbymonetiscope_popup_enabled') === 'true';
    $network_code = trim(get_option('adxbymonetiscope_popup_network_code'));

    if (!$enabled || !$network_code) {
        return;
    }

    // Enqueue popup styles and scripts
    wp_register_style('adxbymonetiscope_popup_style', false);
    wp_enqueue_style('adxbymonetiscope_popup_style');
    $css = <<<CSS
#adxbymonetiscope-popup {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 100000;
}
#adxbymonetiscope-popup .container {
    display: flex;
    align-items: flex-start;
    background: transparent;
    position: relative;
}
#adxbymonetiscope-slot {
    min-width: 300px;
    min-height: 250px;
}
#adxbymonetiscope-close {
    cursor: pointer;
    font-size: 24px;
    background: black;
    border: none;
    color: white;
    position: absolute;
    top: -22px;
    right: 0;
    padding: 5px;
    box-shadow: #00000073 1px 1px 4px;
}
.adxbymonetiscope-brand {
    z-index: -11;
    padding: 2px 7px;
    margin: 0;
    position: absolute;
    color: blue;
    top: -20px;
    left: 0;
    font-size: 12px;
    background: white;
    box-shadow: #00000073 1px 1px 4px;
    border-radius: 2px;
}
CSS;
    wp_add_inline_style('adxbymonetiscope_popup_style', $css);

    wp_register_script('adxbymonetiscope_popup_script', false);
    wp_enqueue_script('adxbymonetiscope_popup_script');

    $network_code_json = wp_json_encode(esc_js($network_code));

    $js = <<<JS
(function(){
    var adPopup = document.createElement("div");
    adPopup.id = "adxbymonetiscope-popup";
    adPopup.innerHTML = `
        <div class="container">
            <p class="adxbymonetiscope-brand">
                Powered by <a href="https://monetiscope.com" target="_blank" style="color:red;text-decoration:none;">Monetiscope</a>
            </p>
            <button id="adxbymonetiscope-close" aria-label="Close ad">×</button>
            <div id="adxbymonetiscope-slot"></div>
        </div>`;

    document.body.appendChild(adPopup);

    var gptScript = document.createElement("script");
    gptScript.src = "https://securepubads.g.doubleclick.net/tag/js/gpt.js";
    gptScript.async = true;
    document.head.appendChild(gptScript);

    window.googletag = window.googletag || {cmd:[]};
    var adLoaded = true, adDisplayed = false;

    gptScript.onload = function() {
        googletag.cmd.push(function(){
            var slot = googletag.defineSlot(
                {$network_code_json},
                [[300, 250], [320, 100]],
                "adxbymonetiscope-slot"
            ).addService(googletag.pubads());

            googletag.pubads().enableSingleRequest();
            googletag.enableServices();

            googletag.pubads().addEventListener("slotRenderEnded", function(event){
                if(event.slot === slot && event.isEmpty){
                    adLoaded = false;
                    adPopup.style.display = "none";
                }
            });
        });
    };

    window.addEventListener("scroll", function(){
        if(!adDisplayed && window.pageYOffset > 200 && adLoaded){
            googletag.cmd.push(function(){
                googletag.display("adxbymonetiscope-slot");
            });
            adPopup.style.display = "block";
            adDisplayed = true;
        }
    });

    adPopup.addEventListener("click", function(e){
        if(e.target.id === "adxbymonetiscope-close"){
            adPopup.style.display = "none";
        }
    });
})();
JS;

    wp_add_inline_script('adxbymonetiscope_popup_script', $js);
}
