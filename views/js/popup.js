console.log("popup script loaded");
(function () {
  console.log("popup function Loaded");
  try {
    // console.log("hi i'm in try")
    // ---- Read config injected by PHP ----
    var CFG = (window.ADXBYMS_POPUP_DATA || {});
    var POPUP_OPTION = CFG.popup_option;   // "ONCE_PER_SESSION" | "ONCE_PER_PAGE"
    var NETWORK_CODE = CFG.network_code;   // used in googletag.defineSlot(...)

    var SESSION_KEY  = "adxbymsPopupShown";
    var SHOW_ONCE_PER_SESSION = (POPUP_OPTION === "ONCE_PER_SESSION");

    if (SHOW_ONCE_PER_SESSION && window.sessionStorage && sessionStorage.getItem(SESSION_KEY) === "true") {
      return;
    }

    var WRAP_ID = "adxbyms-popup";
    var SLOT_ID = "adxbyms-slot";
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
      "border-radius:2px","display:flex","flex-direction:column","justify-content:center","padding:0"
    ].join(";");

    var brand = document.createElement("div");
    brand.innerHTML = '<span>Powered By</span> <a href="https://monetiscope.com" target="_blank" rel="noopener" style="color:#206cd7;text-decoration:none;">Monetiscope</a>';
    brand.style.cssText = [
      "width:max-content","font-family:Arial,sans-serif","color:#ff0000",
      "font-size:12px","background:#fff","padding:2px 8px","border-radius:4px 4px 0 0","box-shadow:0 -3px 3px rgba(0,0,0,0.2)"
    ].join(";");

    var close = document.createElement("button");
    close.type = "button";
    close.setAttribute("aria-label","Close ad");
    close.textContent = "×";
    close.style.cssText = [
      "display:flex","justify-content:center","align-items:center",
      "width:20px","height:20px","background:#000","color:#fff","border:1px solid #000",
      "cursor:pointer","font-weight:700","line-height:20px","padding:0","border-radius:2px"
    ].join(";");

    var slot = document.createElement("div");
    slot.id = SLOT_ID;
    slot.style.cssText = ["width:fit-content","padding:0","border-radius:2px","overflow:hidden"].join(";");

    var myUpperDiv = document.createElement("div");
    myUpperDiv.style.cssText = ["display:flex","justify-content:space-between","align-items:end"].join(";");
    myUpperDiv.appendChild(brand);
    myUpperDiv.appendChild(close);

    inner.appendChild(myUpperDiv);
    inner.appendChild(slot);
    wrap.appendChild(inner);
    document.body.appendChild(wrap);
    // console.log("appended myUpperDiv, slot, inner, wrarp")

    // ---- GPT setup (kept inside JS exactly as before) ----
    window.googletag = window.googletag || { cmd: [] };
    var gptLoaded = false, adRequested = false, adSlotRef = null;

    var gpt = document.createElement("script");
    gpt.src = "https://securepubads.g.doubleclick.net/tag/js/gpt.js";
    gpt.async = true;
    gpt.setAttribute("myData", "from_popupjs");
    gpt.onload = function () {
      gptLoaded = true;
      googletag.cmd.push(function () {
        try {
          console.log("in the moment of making gptLoader", NETWORK_CODE, SLOT_ID, window.location.href)
          adSlotRef = googletag
            .defineSlot(NETWORK_CODE, [[300,250],[336,280],[300,280],[250,250],[200,200]], SLOT_ID)
            .addService(googletag.pubads());

          googletag.pubads().set("page_url", window.location.href);

          googletag.pubads().addEventListener("slotRenderEnded", function (evt) {
            console.log(evt, evt.slot);
            console.log(adSlotRef);
            if (evt.slot !== adSlotRef) {
              console.log("evt.slot !=adslotRef");
              return;
              }
            if (evt.isEmpty){console.log("ent.Is empty"); wrap.style.display = "none";}
          });

          googletag.enableServices();
        } catch (e) { if (console && console.warn) console.warn("GPT init error", e); }
      });
    };
    gpt.onerror = function () { gptLoaded = false; };
    document.head.appendChild(gpt);

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
      wrap.style.display = "flex";
      window.googletag.cmd.push(function () { try { googletag.display(SLOT_ID); } catch(_) {} });
      window.googletag.cmd.push(function () { try { googletag.display(SLOT_ID); } catch(_) {} });
      if (SHOW_ONCE_PER_SESSION) sessionStorage.setItem(SESSION_KEY, "true");
      if (SHOW_ONCE_PER_SESSION) sessionStorage.setItem(SESSION_KEY, "true");
      window.removeEventListener("scroll", onScroll, { passive: true });
      window.removeEventListener("scroll", onScroll, { passive: true });
    }
    // console.log("after showonce")
    
    function onScroll() { if (scrolledHalf()) showOnce(); }
    window.addEventListener("scroll", onScroll, { passive: true });
    // console.log("after onScroll")

    close.addEventListener("click", function () { wrap.style.display = "none"; });
  } catch (err) { if (console && console.log) console.log("Monetiscope popup error:", err); }
})();
