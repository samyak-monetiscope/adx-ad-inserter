<?php
defined('ABSPATH') || exit;
?>
<div id="tab-button-rewarded" class="adx-tab" style="display:none">
    <h3>Button Rewarded Ad</h3>

<!--     <p>
        <input type="hidden" name="ad2_enabled" value="false" />
        <label>
            <input type="checkbox"
                   id="ad2_enabled"
                   name="ad2_enabled"
                   value="true" <?php checked(get_option('ad2_enabled'), 'true'); ?> />
            Enable Button Rewarded Ad
        </label>
    </p> -->

    <!-- 🧪 Extra Test Checkbox (duplicate field) -->
    <p>
        <label>
            <input type="checkbox"
                   id="ad2_enabled_test"
                   name="ad2_enabled"
                   value="true" <?php checked(get_option('ad2_enabled'), 'true'); ?> />
            Enable Button Rewarded Ad
        </label>
    </p>

    <p>
        <label for="ad2_network_code"><strong>Button Rewarded Network Code</strong></label><br>
        <input type="text"
               id="ad2_network_code"
               name="ad2_network_code"
               value="<?php echo esc_attr(get_option('ad2_network_code')); ?>"
               class="regular-text" />
    </p>

    <p>
        <label for="ad2_keywords"><strong>Trigger Keywords (comma-separated)</strong></label><br>
        <input type="text"
               id="ad2_keywords"
               name="ad2_keywords"
               value="<?php echo esc_attr(get_option('ad2_keywords')); ?>"
               class="regular-text"
               placeholder="Click, Download, Apply now" />
        <br>
        <span class="description">
            The ad will trigger on links/buttons containing these keywords.
        </span>
    </p>
</div>
