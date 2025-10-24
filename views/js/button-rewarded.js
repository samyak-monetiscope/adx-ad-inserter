// /views/js/button-rewarded.js
(function () {
  if (!window.ADXBYMS_BUTTON_REWARDED || !window.ADXBYMS_BUTTON_REWARDED.networkCode) return;

  const KEYWORDS = window.ADXBYMS_BUTTON_REWARDED.keywords || [];
  const NETWORK_CODE = window.ADXBYMS_BUTTON_REWARDED.networkCode;
  let adShownCount = 0;



  const matchesKeyword = txt => KEYWORDS.includes(txt.trim());

  function showPopup(onProceed, onCancel) {
    const ov = document.createElement("div");
    ov.className = "adxbyms-popup-overlay";

    const box = document.createElement("div");
    box.className = "adxbyms-popup-box";

    box.innerHTML = `
      <p class="adxbyms-popup-text">Play an ad to continue.</p>
      <div class="adxbyms-popup-buttons">
        <button id="adxbyms-go" class="adxbyms-btn-go">Proceed</button>
        <button id="adxbyms-stop" class="adxbyms-btn-stop">Cancel</button>
      </div>
      <p class="adxbyms-popup-credit">
        Ads By <a href="https://monetiscope.com" target="_blank" rel="noopener">Monetiscope</a>
      </p>
    `;

    document.body.append(ov, box);

    const close = () => {
      box.remove();
      ov.remove();
    };

    box.querySelector("#adxbyms-go").onclick = () => { close(); onProceed(); };
    box.querySelector("#adxbyms-stop").onclick = () => { close(); onCancel(); };
  }

  function showRewarded(onGranted, onClosed) {
    window.googletag = window.googletag || { cmd: [] };
    googletag.cmd.push(() => {
      const slot = googletag.defineOutOfPageSlot(
        NETWORK_CODE,
        googletag.enums.OutOfPageFormat.REWARDED
      );
      if (!slot) return;

      slot.addService(googletag.pubads());
      googletag.enableServices();

      googletag.pubads().addEventListener("rewardedSlotReady", e => e.makeRewardedVisible());
      googletag.pubads().addEventListener("rewardedSlotGranted", onGranted);
      googletag.pubads().addEventListener("rewardedSlotClosed", onClosed);

      googletag.display(slot);
    });
  }

  document.addEventListener("click", evt => {
    const link = evt.target.closest("a,button");
    if (!link) return;
    const label = (link.textContent || "").trim();

    if (adShownCount === 0) {
      if (!matchesKeyword(label)) return;

      if (link.tagName === "BUTTON" && (link.type || "submit").toLowerCase() === "submit") {
        const form = link.closest("form");
        if (!form || !form.checkValidity()) return;
      }

      evt.preventDefault();
      evt.stopImmediatePropagation();

      showPopup(
        () => {
          showRewarded(
            () => {
              adShownCount++;
              if (link.tagName === "A") {
                window.location.href = link.href;
              } else {
                const t = (link.type || "submit").toLowerCase();
                if (t === "submit" && link.form) link.form.submit();
                else if (t === "reset" && link.form) link.form.reset();
                else link.click();
              }
            },
            () => {}
          );
        },
        () => {}
      );
    } else {
      adShownCount = 0;
    }
  });

//   document.addEventListener("DOMContentLoaded", loadGPT);
})();
