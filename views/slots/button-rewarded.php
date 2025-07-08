<?php
defined('ABSPATH') || exit;

/**
 * Render the Button Rewarded slot if enabled
 */
function adx_render_button_rewarded_slot() {
    $enabled      = get_option('ad2_enabled') === 'true';
    $network_code = trim(get_option('ad2_network_code'));
    $keywords_raw = get_option('ad2_keywords');
    $keywords     = array_map('trim', explode(',', $keywords_raw));

    if (!$enabled || !$network_code) {
        return;
    }

    echo '<script>';
    echo '(() => {
        /* ---------- CONFIG FROM PHP ---------- */
        const KEYWORDS      = ' . json_encode($keywords) . ';
        const NETWORK_CODE  = "' . esc_js($network_code) . '";
        let   adShownCount  = 0;                    // prevents double popup

        /* ---------- GPT loader ---------- */
        function loadGPT() {
            const s = document.createElement("script");
            // s.src   = "//www.googletagservices.com/tag/js/gpt.js";
            // s.async = true;
            document.head.appendChild(s);
        }

        const matchesKeyword = txt => KEYWORDS.includes(txt.trim());

        /* ---------- Popup helper ---------- */
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
              <button id="adx-go"   style="background:#007bff;color:#fff;margin:5px;padding:7px 14px;border-radius:4px;">Proceed</button>
              <button id="adx-stop" style="background:#dc3545;color:#fff;margin:5px;padding:7px 14px;border-radius:4px;">Cancel</button>
              <p style="font-size:12px;color:red;margin-top:10px">
                Ads By <a href="https://monetiscope.com" target="_blank" style="color:blue">Monetiscope</a>
              </p>`;
            document.body.append(ov, box);

            const close = () => { box.remove(); ov.remove(); };
            box.querySelector("#adx-go").onclick   = () => { close(); onProceed(); };
            box.querySelector("#adx-stop").onclick = () => { close(); onCancel();  };
        }

        /* ---------- Rewarded ad helper ---------- */
        function showRewarded(onGranted, onClosed) {
            window.googletag = window.googletag || {cmd:[]};
            googletag.cmd.push(() => {
                const slot = googletag.defineOutOfPageSlot(
                    NETWORK_CODE,
                    googletag.enums.OutOfPageFormat.REWARDED
                ).addService(googletag.pubads());

                googletag.enableServices();
                googletag.pubads().addEventListener("rewardedSlotReady", e => e.makeRewardedVisible());
                googletag.pubads().addEventListener("rewardedSlotGranted", onGranted);
                googletag.pubads().addEventListener("rewardedSlotClosed",  onClosed);
                googletag.display(slot);
            });
        }

        /* ---------- Global click handler ---------- */
        document.addEventListener("click", evt => {
            const link = evt.target.closest("a,button");
            if (!link) return;

            const label = (link.textContent || "").trim();

            /* ----------------------------------------------------------------
               FIRST, REAL USER CLICK  (adShownCount === 0)
            ---------------------------------------------------------------- */
            if (adShownCount === 0) {
                if (!matchesKeyword(label)) return;

                /* If it is a submit button, enforce form validity BEFORE popup */
                if (link.tagName === "BUTTON" &&
                    (link.getAttribute("type") || "submit").toLowerCase() === "submit")
                {
                    const form = link.closest("form");
                    if (!form || !form.checkValidity()) return;   // let browser show errors
                }

                /* show popup + ad */
                evt.preventDefault();
                evt.stopImmediatePropagation();

                showPopup(
                    /* onProceed --------------- */
                    () => {
                        showRewarded(
                            /* onGranted (ad watched) */
                            () => {
                                adShownCount++;          // marks programmatic phase

                                if (link.tagName === "A") {
                                    window.location.href = link.href;
                                } else {
                                    const type = (link.getAttribute("type") || "submit").toLowerCase();
                                    if (type === "submit" && link.form) {
                                        /* >>> USE form.submit()  (reliable across browsers) */
                                        link.form.submit();
                                    } else if (type === "reset" && link.form) {
                                        link.form.reset();
                                    } else {
                                        link.click();   // normal / custom buttons
                                    }
                                }
                            },
                            /* onClosed (ad skipped)  */
                            () => { /* do nothing */ }
                        );
                    },
                    /* onCancel ----------------- */
                    () => { /* user cancelled popup */ }
                );
            }

            /* ----------------------------------------------------------------
               SECOND CLICK  (programmatic) – just allow it, then reset counter
            ---------------------------------------------------------------- */
            else {
                adShownCount = 0;
                /* allow default action */
            }
        });

        document.addEventListener("DOMContentLoaded", loadGPT);
    })();';
    echo '</script>';
}
