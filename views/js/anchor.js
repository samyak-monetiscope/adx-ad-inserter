// /assets-runtime/frontend/anchor.js
(function () {
  // Expecting: window.ADXBYMS_ANCHOR = { networkCode: '...', position: 'TOP_ANCHOR'|'BOTTOM_ANCHOR' }
  if (!window.ADXBYMS_ANCHOR || !window.ADXBYMS_ANCHOR.networkCode || !window.ADXBYMS_ANCHOR.position) return;

  window.googletag = window.googletag || { cmd: [] };
// Create a new <p> element
const paragraph = document.createElement('p');
paragraph.textContent = "I'm from anchor";

// Append the <p> element to the document body
// document.body.appendChild(paragraph);


  googletag.cmd.push(function () {
    var posKey = String(window.ADXBYMS_ANCHOR.position).toUpperCase(); // "TOP_ANCHOR" | "BOTTOM_ANCHOR"
    var fmt = (googletag.enums && googletag.enums.OutOfPageFormat)
      ? googletag.enums.OutOfPageFormat[posKey]
      : null;
    if (!fmt) return;

    var slot = googletag.defineOutOfPageSlot(window.ADXBYMS_ANCHOR.networkCode, fmt);
    if (!slot) return;

    slot.addService(googletag.pubads());
    googletag.pubads().enableSingleRequest();
    googletag.enableServices();
    googletag.display(slot);
  });
})();
