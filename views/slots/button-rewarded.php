<?php
defined( 'ABSPATH' ) || exit;

/**
 * Render the Button Rewarded slot if enabled
 * — echoes the inline <script> at footer via the footer‐renderer hook
 */
function adxbymonetiscope_render_button_rewarded_slot() {
    // (Using your original option keys so it actually fires)
    $enabled      = get_option( 'ad2_enabled' ) === 'true';
    $network_code = trim( get_option( 'ad2_network_code' ) );
    $keywords_raw = get_option( 'ad2_keywords', '' );
    $keywords     = array_map( 'trim', explode( ',', $keywords_raw ) );

    if ( ! $enabled || ! $network_code ) {
        return;
    }

    // — Inline script: IIFE that loads GPT, shows popup, then rewarded ad —
    ?>
    <script>
    (function () {
        const KEYWORDS     = <?php echo wp_json_encode( $keywords ); ?>;
        const NETWORK_CODE = "<?php echo esc_js( $network_code ); ?>";
        let   adShownCount = 0;

        function loadGPT() {
            const s = document.createElement("script");
            s.src   = "https://securepubads.g.doubleclick.net/tag/js/gpt.js";
            s.async = true;
            document.head.appendChild(s);
        }

        const matchesKeyword = txt => KEYWORDS.includes(txt.trim());

        function showPopup(onProceed, onCancel) {
            const ov = Object.assign(document.createElement("div"), {
                style: "position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9998"
            });
            const box = document.createElement("div");
            box.style.cssText =
              "position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);" +
              "z-index:9999;background:#fff;padding:20px;border-radius:8px;" +
              "box-shadow:0 0 10px rgba(0,0,0,.2);text-align:center";
            box.innerHTML = `
              <p style="font-size:18px">Play an ad to continue.</p>
              <button id="adxbymonetiscope-go"   style="margin:5px;padding:7px 14px;border-radius:4px;background:#007bff;color:#fff;">Proceed</button>
              <button id="adxbymonetiscope-stop" style="margin:5px;padding:7px 14px;border-radius:4px;background:#dc3545;color:#fff;">Cancel</button>
              <p style="font-size:12px;color:red;margin-top:10px">
                Ads By <a href="https://monetiscope.com" target="_blank" style="color:blue">Monetiscope</a>
              </p>`;
            document.body.append(ov, box);

            const close = () => { box.remove(); ov.remove(); };
            box.querySelector("#adxbymonetiscope-go").onclick   = () => { close(); onProceed(); };
            box.querySelector("#adxbymonetiscope-stop").onclick = () => { close(); onCancel();  };
        }

        function showRewarded(onGranted, onClosed) {
            window.googletag = window.googletag || { cmd: [] };
            googletag.cmd.push(() => {
                const slot = googletag.defineOutOfPageSlot(
                    NETWORK_CODE,
                    googletag.enums.OutOfPageFormat.REWARDED
                ).addService(googletag.pubads());

                googletag.enableServices();
                googletag.pubads().addEventListener("rewardedSlotReady",   e => e.makeRewardedVisible());
                googletag.pubads().addEventListener("rewardedSlotGranted", onGranted);
                googletag.pubads().addEventListener("rewardedSlotClosed",  onClosed);
                googletag.display(slot);
            });
        }

        document.addEventListener("click", evt => {
            const link = evt.target.closest("a,button");
            if (!link) return;
            const label = (link.textContent || "").trim();

            // FIRST (real user) click
            if (adShownCount === 0) {
                if (!matchesKeyword(label)) return;

                if (link.tagName === "BUTTON" &&
                    (link.getAttribute("type")||"submit").toLowerCase()==="submit") {
                    const form = link.closest("form");
                    if (!form || !form.checkValidity()) return;
                }

                evt.preventDefault();
                evt.stopImmediatePropagation();

                showPopup(
                    () => {
                        showRewarded(
                            () => { // onGranted
                                adShownCount++;
                                if (link.tagName === "A") {
                                    window.location.href = link.href;
                                } else {
                                    const t = (link.getAttribute("type")||"submit").toLowerCase();
                                    if (t==="submit" && link.form) link.form.submit();
                                    else if (t==="reset"  && link.form) link.form.reset();
                                    else link.click();
                                }
                            },
                            () => {} // onClosed
                        );
                    },
                    () => {} // onCancel
                );
            }
            // SECOND (programmatic) click
            else {
                adShownCount = 0;
            }
        });

        document.addEventListener("DOMContentLoaded", loadGPT);
    })();
    </script>
    <?php
}
