// /views/js/display-slot.js
(function () {
  document.addEventListener('DOMContentLoaded', function () {
    var nodes = document.querySelectorAll('.adxbymonetiscope-display-slot');
    if (!nodes.length) return;

    // Ensure GPT queue exists
    window.googletag = window.googletag || { cmd: [] };

    nodes.forEach(function (el) {
      var network = el.getAttribute('data-network');
      var sizesStr = el.getAttribute('data-sizes');
      var divId = el.getAttribute('data-div-id');
      var pageUrl = el.getAttribute('data-page-url') || '';

      if (!network || !sizesStr || !divId) return;

      // Convert the JS-literal string (e.g., [300,250] or [[300,250],'fluid']) into a real value
      var sizes;
      try {
        // Minimal change: interpret the literal as JS (not JSON). Matches your current PHP output.
        sizes = (new Function('return ' + sizesStr))();
      } catch (e) {
        return;
      }

      googletag.cmd.push(function () {
        var slot = googletag.defineSlot(network, sizes, divId);
        if (!slot) return;

        slot.addService(googletag.pubads());
        googletag.enableServices();

        if (pageUrl) {
          googletag.pubads().set('page_url', pageUrl);
        }

        googletag.display(divId);
      });
    });
  });
})();
