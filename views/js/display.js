// /views/js/display-slot.js
const pa = document.createElement('p');
pa.textContent = "I'm from display";

// Append the <p> element to the document body
document.body.appendChild(pa);
console.log("I'm in display.js")
(function () {
  console.log("I'm in display function")
  document.addEventListener('DOMContentLoaded', function () {
    console.log("i'm in DOMContentLoaded")
    var nodes = document.querySelectorAll('.adxbymonetiscope-display-slot');
    if (!nodes.length) return;

    // Ensure GPT queue exists
    window.googletag = window.googletag || { cmd: [] };

    nodes.forEach(function (el) {
      console.log("i'm in forEach and teh network code is ", el.getAttribute('data-network'))
      var network = el.getAttribute('data-network');
      var sizesStr = el.getAttribute('data-sizes');
      var divId = el.getAttribute('data-div-id');
      var pageUrl = el.getAttribute('data-page-url') || '';

      if (!network || !sizesStr || !divId) return;

      // Convert the JS-literal string (e.g., [300,250] or [[300,250],'fluid']) into a real value
      var sizes;
      try {
        console.log("i'm in try")
        // Minimal change: interpret the literal as JS (not JSON). Matches your current PHP output.
        sizes = (new Function('return ' + sizesStr))();
      } catch (e) {
        console.log(e, "i'm in the catch")
        return;
      }

      googletag.cmd.push(function () {
        var slot = googletag.defineSlot(network, sizes, divId);
        console.log("i'm in the googletag", network, sizes, divId)
        if (!slot) return;

        slot.addService(googletag.pubads());
        googletag.enableServices();

        if (pageUrl) {
          googletag.pubads().set('page_url', pageUrl);
        }

        googletag.display(divId);
        console.log("code executed")
      });
    });
  });
})();
