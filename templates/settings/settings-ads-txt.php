<?php
defined('ABSPATH') || exit;

$ads_txt_code = get_option('ads_txt_code', '');
?>

<div id="tab-ads-txt" class="adx-tab" style="display:none;">
    <h3 class="tab-title">Ads.txt</h3>

    <!-- Enable Toggle -->
    <p>
        <label>
            <input type="checkbox"
                   id="ads_txt_enabled"
                   name="ads_txt_enabled"
                   value="true" <?php checked(get_option('ads_txt_enabled'), 'true'); ?> />
        Enable Ads Txt    
        </label>
    </p>


    <div class="ads-txt-content" id="ads-txt-code" >
        <label for="ads_txt_code"><strong>Paste your Ads.txt</strong></label><br>
        <textarea rows="20" name="ads_txt_code" id="ads_txt_code"><?php echo esc_textarea($ads_txt_code); ?></textarea>
    </div>


    
</div>
