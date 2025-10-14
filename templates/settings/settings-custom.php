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

    
</div>
