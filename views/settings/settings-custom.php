<?php
defined('ABSPATH') || exit;

$header_code = get_option('custom_header_code', '');
$footer_code = get_option('custom_footer_code', '');
$ads_txt_code = get_option('custom_ads_txt', '');
?>

<div id="tab-custom" class="adx-tab" style="display:none;">
    <h3>Custom Code</h3>

    <!-- Enable Toggle -->
    <p>
        <label>
            <input type="checkbox"
                   id="custom_enabled"
                   name="custom_enabled"
                   value="true" <?php checked(get_option('custom_enabled'), 'true'); ?> />
            Enable Custom Code Slot
        </label>
    </p>

    <style>
        .custom-code-wrapper {
            display: flex;
            max-width: 900px;
            margin-top: 15px;
        }

        .custom-tab-buttons {
            display: flex;
            flex-direction: column;
            border: 1px solid #ccc;
            border-right: none;
            background: #f1f1f1;
        }

        .custom-tab-buttons button {
            background: none;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            font-weight: 600;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }

        .custom-tab-buttons button.active {
            background: #fff;
            border-left: 4px solid #2271b1;
            color: #2271b1;
        }

        .custom-tab-content {
            flex-grow: 1;
            border: 1px solid #ccc;
            padding: 10px;
            background: #fff;
        }

        .custom-tab-content textarea {
            width: 100%;
            height: 200px;
            font-family: monospace;
            font-size: 13px;
            background: #1e1e1e;
            color: #eee;
            border: none;
            padding: 10px;
            resize: vertical;
        }
    </style>

    <div class="custom-code-wrapper">
        <div class="custom-tab-buttons">
            <button type="button" class="custom-tab-toggle active" data-tab="header">Header</button>
            <button type="button" class="custom-tab-toggle" data-tab="footer">Footer</button>
            <button type="button" class="custom-tab-toggle" data-tab="ads-txt">ads.txt</button>
        </div>
        <div class="custom-tab-content" id="custom-code-header">
            <label for="custom_header_code"><strong>Header Code (within &lt;head&gt;)</strong></label><br>
            <textarea name="custom_header_code" id="custom_header_code"><?php echo esc_textarea($header_code); ?></textarea>
        </div>
        <div class="custom-tab-content" id="custom-code-footer" style="display:none;">
            <label for="custom_footer_code"><strong>Footer Code (before &lt;/body&gt;)</strong></label><br>
            <textarea name="custom_footer_code" id="custom_footer_code"><?php echo esc_textarea($footer_code); ?></textarea>
        </div>
        <div class="custom-tab-content" id="custom-ads-txt" style="display:none;">
            <label for="custom_ads_txt"><strong>ads.txt (before &lt;/body&gt;)</strong></label><br>
            <textarea name="custom_ads_txt" id="custom_ads_txt"><?php echo esc_textarea($ads_txt_code); ?></textarea>
        </div>
    </div>

    <script>
        document.querySelectorAll(".custom-tab-toggle").forEach(btn => {
            btn.addEventListener("click", function () {
                document.querySelectorAll(".custom-tab-toggle").forEach(b => b.classList.remove("active"));
                btn.classList.add("active");

                const tab = btn.getAttribute("data-tab");
                document.getElementById("custom-code-header").style.display = tab === "header" ? "block" : "none";
                document.getElementById("custom-code-footer").style.display = tab === "footer" ? "block" : "none";
                document.getElementById("custom-ads-txt").style.display = tab === "ads-txt" ? "block" : "none";
            });
        });
    </script>
</div>
