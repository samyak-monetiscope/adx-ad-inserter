<?php
defined('ABSPATH') || exit;

/**
 * Popup Ad slot
 * - Trigger at 50% scroll
 * - Sizes: [[300,250],[336,280],[300,280],[250,250],[200,200]]
 * - page_url = window.location.href
 * - CSS entirely in JS
 * Modes:
 * - ONCE_PER_SESSION (default)
 * - ONCE_PER_PAGE
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
    $network_code_js = '"' . esc_js($network_code) . '"';
    $popup_option_js = wp_json_encode($popup_option);

    // Inline JS only; all CSS lives inside JS
    wp_register_script('adxbymonetiscope_popup_script', false, [], null, true);
    wp_enqueue_script('adxbymonetiscope_popup_script');

    $js = <<<JS
(function () {
  try {
    var POPUP_OPTION = {$popup_option_js}; // "ONCE_PER_SESSION" | "ONCE_PER_PAGE"
    var SESSION_KEY  = "adxbymonetiscopePopupShown";
    var SHOW_ONCE_PER_SESSION = (POPUP_OPTION === "ONCE_PER_SESSION");

    // Guard for ONCE_PER_SESSION
    if (SHOW_ONCE_PER_SESSION && window.sessionStorage && sessionStorage.getItem(SESSION_KEY) === "true") {
      return;
    }

    // Build popup DOM once
    var WRAP_ID = "adxbymonetiscope-popup";
    var SLOT_ID = "adxbymonetiscope-slot";
    if (document.getElementById(WRAP_ID)) return;

    var wrap = document.createElement("div");
    wrap.id = WRAP_ID;
    wrap.style.cssText = [
      "display:none","position:fixed","top:0","left:0","width:100%","height:100vh",
      "z-index:9999999999","justify-content:center","align-items:center","background:transparent"
    ].join(";");

    var inner = document.createElement("div");
    inner.style.cssText = [
      "position:relative","min-width:fit-content","min-height:200px","background:transparent",
      "border-radius:2px","box-shadow:0 10px 20px rgba(128,128,128,0.65)",
      "display:flex","flex-direction:column","justify-content:center","padding:0"
    ].join(";");

    var brand = document.createElement("div");
    brand.innerHTML = '<span>Powered By</span> <a href="https://monetiscope.com" target="_blank" rel="noopener" style="color:#206cd7;text-decoration:none;">Monetiscope</a>';
    brand.style.cssText = [
      "width : max-content", "font-family:Arial,sans-serif","color:#ff0000",
      "font-size:12px","background:#fff","padding:2px 8px","border-radius:4px 4px 0 0","box-shadow:0 -3px 3px rgba(0,0,0,0.2)"
    ].join(";");

    var close = document.createElement("button");
    close.type = "button";
    close.setAttribute("aria-label","Close ad");
    close.textContent = "×";
    close.style.cssText = [
      "position:absolute","top:-1.2rem","right:-0.5rem",
      "display:flex","justify-content:center","align-items:center",
      "width:20px","height:20px","background:#000","color:#fff","border:1px solid #000",
      "cursor:pointer","font-weight:700","line-height:20px","padding:0","border-radius:2px"
    ].join(";");

    var slot = document.createElement("div");
    slot.id = SLOT_ID;
    slot.style.cssText = ["width:fit-content","padding:0","border-radius:2px","overflow:hidden"].join(";");

    var myUpperDiv = document.createElement("div");
    myUpperDiv.appendChild(brand);
    myUpperDiv.appendChild(close);

    inner.appendChild(myUpperDiv);
    // inner.appendChild(close);
    inner.appendChild(slot);
    wrap.appendChild(inner);
    document.body.appendChild(wrap);

    // -----------------------------
    // GPT load + safe init (FIX)
    // -----------------------------
    // 1) Always define googletag placeholder BEFORE any use
    window.googletag = window.googletag || { cmd: [] };

    var gptLoaded = false, adRequested = false, adSlotRef = null;

    // 2) Load gpt.js
    var gpt = document.createElement("script");
    gpt.src = "https://securepubads.g.doubleclick.net/tag/js/gpt.js";
    gpt.async = true;
    gpt.onload = function () {
      gptLoaded = true;

      // 3) All GPT calls inside cmd.push (safe even if script loads slightly later)
      googletag.cmd.push(function () {
        try {
          adSlotRef = googletag
            .defineSlot({$network_code_js}, [[300,250],[336,280],[300,280],[250,250],[200,200]], SLOT_ID)
            .addService(googletag.pubads());

          googletag.pubads().set("page_url", window.location.href);

          googletag.pubads().addEventListener("slotRenderEnded", function (evt) {
            if (evt.slot !== adSlotRef) return;
            // If empty, close overlay; if filled, keep visible
            if (evt.isEmpty) wrap.style.display = "none";
          });

          googletag.enableServices();
        } catch (e) {
          // If GPT throws, keep UI silent
          console && console.warn && console.warn("GPT init error", e);
        }
      });
    };
    gpt.onerror = function () {
      // Fail quietly if GPT can’t load (prevents ReferenceErrors)
      gptLoaded = false;
    };
    document.head.appendChild(gpt);

    // -----------------------------
    // Scroll trigger (50%)
    // -----------------------------
    function scrolledHalf() {
      var st = window.scrollY || document.documentElement.scrollTop || 0;
      var vh = window.innerHeight || 0;
      var dh = Math.max(document.documentElement.scrollHeight || 0, document.body ? document.body.scrollHeight : 0);
      var maxScroll = Math.max(dh - vh, 1);
      return (st / maxScroll) >= 0.5;
    }

    function showOnce() {
      if (adRequested) return;
      if (SHOW_ONCE_PER_SESSION && sessionStorage.getItem(SESSION_KEY) === "true") return;

      adRequested = true;
      wrap.style.display = "flex";

      // Display via cmd queue (works before/after gpt loaded)
      window.googletag.cmd.push(function () {
        try { googletag.display(SLOT_ID); } catch(_) {}
      });

      if (SHOW_ONCE_PER_SESSION) sessionStorage.setItem(SESSION_KEY, "true");
      window.removeEventListener("scroll", onScroll, { passive: true });
    }

    function onScroll() { if (scrolledHalf()) showOnce(); }
    window.addEventListener("scroll", onScroll, { passive: true });

    // Close button
    close.addEventListener("click", function () { wrap.style.display = "none"; });
  } catch (err) {
    console && console.warn && console.warn("Monetiscope popup error:", err);
  }
})();
JS;

    wp_add_inline_script('adxbymonetiscope_popup_script', $js);
}
