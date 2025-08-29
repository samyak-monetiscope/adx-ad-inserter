<?php
defined('ABSPATH') || exit;

/**
 * Render the Popup Ad slot if enabled.
 * Unified rules:
 * - Trigger at 50% scroll
 * - Sizes: [[300,250],[336,280],[300,280],[250,250],[200,200]]
 * - page_url = window.location.href
 * - CSS entirely in JS
 * - Close only via X button
 * Modes:
 * - ONCE_PER_SESSION (default): once per session (sessionStorage)
 * - ONCE_PER_PAGE: once per page-load (no sessionStorage guard)
 */
function adxbymonetiscope_render_popup_slot() {
    $enabled      = get_option('popup_enabled') === 'true';
    $network_code = trim((string) get_option('popup_network_code'));
    if (!$enabled || $network_code === '') {
        return;
    }

    // Read popup option; default to ONCE_PER_SESSION
    $popup_option = get_option('popup_option');
    if ($popup_option !== 'ONCE_PER_PAGE' && $popup_option !== 'ONCE_PER_SESSION') {
        $popup_option = 'ONCE_PER_SESSION';
    }

    // Prepare safe JSON for JS
    $network_code_js = wp_json_encode($network_code);
    $popup_option_js = wp_json_encode($popup_option);

    // Inline JS only; all CSS lives inside JS
    wp_register_script('adxbymonetiscope_popup_script', false, [], null, true);
    wp_enqueue_script('adxbymonetiscope_popup_script');

    $js = <<<JS
(function(){
    try {
        var POPUP_OPTION = {$popup_option_js}; // "ONCE_PER_SESSION" | "ONCE_PER_PAGE"
        var SESSION_KEY = "adxbymonetiscopePopupShown";
        var SHOW_ONCE_PER_SESSION = (POPUP_OPTION === "ONCE_PER_SESSION");

        // Guard for ONCE_PER_SESSION: skip if shown in this tab/session
        if (SHOW_ONCE_PER_SESSION && window.sessionStorage && sessionStorage.getItem(SESSION_KEY) === "true") {
            return;
        }

        // Create popup container once
        var wrapperId = "adxbymonetiscope-popup";
        var slotId    = "adxbymonetiscope-slot";

        if (document.getElementById(wrapperId)) {
            return; // avoid duplicates
        }

        var wrap = document.createElement("div");
        wrap.id = wrapperId;
        wrap.style.cssText = [
            "display:none",
            "position:fixed",
            "top:0",
            "left:0",
            "width:100%",
            "height:100vh",
            "z-index:9999999999",
            "justify-content:center",
            "align-items:center",
            "background:transparent"
        ].join(";");

        var inner = document.createElement("div");
        inner.setAttribute("data-ms-container","1");
        inner.style.cssText = [
            "position:relative",
            "min-width:fit-content",
            "min-height:200px",
            "background:transparent",
            "border-radius:2px",
            "box-shadow:0px 10px 20px rgba(128,128,128,0.65)",
            "display:flex",
            "flex-direction:column",
            "align-items:center",
            "justify-content:center",
            "padding:0"
        ].join(";");

        // Branding
        var brand = document.createElement("div");
        brand.setAttribute("data-ms-brand","1");
        brand.innerHTML = '<span>Powered By</span> <a href="https://monetiscope.com" target="_blank" rel="noopener noreferrer" style="color:#206cd7;text-decoration:none;">Monetiscope</a>';
        brand.style.cssText = [
            "position:absolute",
            "top:-1.19rem",
            "left:0",
            "font-family:Arial,sans-serif",
            "color:#ff0000",
            "font-size:12px",
            "background:#fff",
            "padding:2px 8px",
            "border-radius:4px 4px 0 0",
            "box-shadow:0 -3px 3px rgba(0,0,0,0.2)"
        ].join(";");

        // Debug marker line
        var debugLine = document.createElement("div");
        debugLine.textContent = "This is from popup slot (" + POPUP_OPTION + ")";
        debugLine.style.cssText = [
            "width:100%",
            "text-align:center",
            "padding:6px",
            "font-weight:bold",
            "color:black",
            "background:yellow"
        ].join(";");

        var modeSpan = document.createElement("span");
        modeSpan.textContent = (POPUP_OPTION === "ONCE_PER_SESSION" ? " ONCE PER SESSION " : " ONCE PER PAGE ");
        modeSpan.style.cssText = [
            "background:pink",
            "padding:2px 6px",
            "margin-left:8px"
        ].join(";");
        debugLine.appendChild(modeSpan);

        //This line is to show debug info along with popup.
        // inner.appendChild(debugLine);

        // Close button
        var closeWrap = document.createElement("div");
        closeWrap.id = "adxbymonetiscope-close-wrap";
        closeWrap.style.cssText = [
            "position:absolute",
            "top:-1.2rem",
            "right:-0.5rem",
            "display:flex",
            "flex-direction:row",
            "justify-content:end",
            "align-items:center",
            "width:fit-content",
            "height:16px",
            "color:gray",
            "font-family:Helvetica,Arial,sans-serif"
        ].join(";");

        var closeBtn = document.createElement("button");
        closeBtn.type = "button";
        closeBtn.id = "adxbymonetiscope-close";
        closeBtn.setAttribute("aria-label","Close ad");
        closeBtn.textContent = "×";
        closeBtn.style.cssText = [
            "display:flex",
            "justify-content:center",
            "align-items:center",
            "width:20px",
            "height:20px",
            "background:#000",
            "color:#fff",
            "border:1px solid #000",
            "cursor:pointer",
            "font-weight:700",
            "line-height:20px",
            "padding:0"
        ].join(";");
        closeWrap.appendChild(closeBtn);

        // Slot
        var slot = document.createElement("div");
        slot.id = slotId;
        slot.style.cssText = [
            "width:fit-content",
            "padding:0",
            "border-radius:2px",
            "overflow:hidden"
        ].join(";");

        inner.appendChild(brand);
        inner.appendChild(closeWrap);
        inner.appendChild(slot);
        wrap.appendChild(inner);
        document.body.appendChild(wrap);

        // GPT + Ad
        window.googletag = window.googletag || { cmd: [] };
        var adLoaded = true, adDisplayed = false;

        var gptScript = document.createElement("script");
        gptScript.src = "https://securepubads.g.doubleclick.net/tag/js/gpt.js";
        gptScript.async = true;
        document.head.appendChild(gptScript);

        gptScript.onload = function(){
            googletag.cmd.push(function() {
                try {
                    var adUnitPath = {$network_code_js};
                    var primaryAdSlot = googletag.defineSlot(
                        adUnitPath,
                        [[300,250],[336,280],[300,280],[250,250],[200,200]],
                        slotId
                    ).addService(googletag.pubads());

                    googletag.pubads().set("page_url", window.location.href);
                    googletag.enableServices();

                    googletag.pubads().addEventListener("slotRenderEnded", function (event) {
                        if (event.slot === primaryAdSlot && event.isEmpty) {
                            wrap.style.display = "none";
                            adLoaded = false;
                        }
                    });
                } catch(e){ adLoaded = false; }
            });
        };

        // Scroll trigger
        function hasScrolledHalfPage() {
            var scrollTop   = window.scrollY || document.documentElement.scrollTop || 0;
            var windowH     = window.innerHeight || 0;
            var docHeight   = Math.max(document.documentElement.scrollHeight || 0, document.body ? document.body.scrollHeight : 0) - windowH;
            return docHeight > 0 && (scrollTop / docHeight) >= 0.5;
        }

        function showPopupOnce(){
            if (adDisplayed || !adLoaded) return;
            if (SHOW_ONCE_PER_SESSION && sessionStorage.getItem(SESSION_KEY) === "true") return;

            googletag.cmd.push(function(){ googletag.display(slotId); });
            wrap.style.display = "flex";
            adDisplayed = true;

            if (SHOW_ONCE_PER_SESSION) sessionStorage.setItem(SESSION_KEY, "true");
            window.removeEventListener("scroll", onScroll);
        }

        function onScroll(){ if (hasScrolledHalfPage()) showPopupOnce(); }
        window.addEventListener("scroll", onScroll, { passive: true });

        closeBtn.addEventListener("click", function(){ wrap.style.display = "none"; });

    } catch(err) { console && console.warn && console.warn("Monetiscope popup error:", err); }
})();
JS;

    wp_add_inline_script('adxbymonetiscope_popup_script', $js);
}
