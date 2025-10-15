// /views/js/interstitial.js
(function () {
  // Expecting window.ADX_INTERSTITIAL = { networkCode: '/1234567/interstitial' }
  if (!window.ADX_INTERSTITIAL || !window.ADX_INTERSTITIAL.networkCode) return;

  window.googletag = window.googletag || { cmd: [] };
  // Create a new <p> element
const paragraph = document.createElement('p');
paragraph.textContent = "I'm from interstitial";

// Append the <p> element to the document body
document.body.appendChild(paragraph);

  googletag.cmd.push(function () {
    var slot = googletag.defineOutOfPageSlot(
      window.ADX_INTERSTITIAL.networkCode,
      googletag.enums.OutOfPageFormat.INTERSTITIAL
    );

    if (slot) {
      slot.addService(googletag.pubads());
      googletag.pubads().enableSingleRequest();
      googletag.enableServices();
      googletag.display(slot);
    }
  });
})();
