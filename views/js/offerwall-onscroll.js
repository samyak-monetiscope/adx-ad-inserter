// /views/js/offerwall-onscroll.js
(function () {
  if (!window.ADX_OFFERWALL || !ADX_OFFERWALL.networkCode) return;

  var logoUrl = ADX_OFFERWALL.logoUrl || "";
  var triggerPercent = Number(ADX_OFFERWALL.triggerPercent || 30);
  var shown = false;
  var rewardedEvt = null;

  // build UI
  var bar = document.createElement("div");
  bar.id = "notification-bar";
  bar.innerHTML = [
    '<img id="publisher-logo" alt="Publisher Logo">',
    '<h2>Unlock more content</h2>',
    '<p>Take action to continue accessing the content on this site</p>',
    '<button class="notification-button">View a short ad <span class="loading">Loading...</span></button>',
    '<div class="branding"><span class="ads-by">Ads by</span> ',
      '<a href="https://monetiscope.com" class="monetiscope" target="_blank" rel="noopener">Monetiscope</a>',
    '</div>'
  ].join("");

  document.addEventListener("DOMContentLoaded", function () {
    document.body.appendChild(bar);
    var img = bar.querySelector("#publisher-logo");
    img.src = logoUrl;

    // show on scroll > triggerPercent, only once per session
    window.addEventListener("scroll", function () {
      if (shown || sessionStorage.getItem("offerwallShown")) return;
      var pct = (window.scrollY + window.innerHeight) / document.documentElement.scrollHeight * 100;
      if (pct > triggerPercent) {
        bar.style.display = "block";
      }
    });

    // button interaction
    var btn = bar.querySelector(".notification-button");
    var loading = btn.querySelector(".loading");

    btn.addEventListener("click", function () {
      loading.style.display = "inline";
      if (rewardedEvt) {
        // close bar right before making the ad visible
        bar.style.display = "none";
        rewardedEvt.makeRewardedVisible();
      }
    });
  });

  // GPT flow (assumes GPT already enqueued by PHP)
  window.googletag = window.googletag || { cmd: [] };
  googletag.cmd.push(function () {
    var slot = googletag.defineOutOfPageSlot(
      ADX_OFFERWALL.networkCode,
      googletag.enums.OutOfPageFormat.REWARDED
    );
    if (!slot) return;

    slot.addService(googletag.pubads());
    googletag.enableServices();

    googletag.pubads().addEventListener("rewardedSlotReady", function (evt) {
      rewardedEvt = evt;
    });
    googletag.pubads().addEventListener("rewardedSlotGranted", function () {
      sessionStorage.setItem("offerwallShown", "true");
      shown = true;
    });
    googletag.pubads().addEventListener("rewardedSlotClosed", function () {
      var loadingSpan = document.querySelector("#notification-bar .loading");
      if (loadingSpan) loadingSpan.style.display = "none";
    });

    googletag.display(slot);
  });
})();
