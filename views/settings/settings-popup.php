<?php
defined('ABSPATH') || exit;
?>
<div id="tab-popup" class="adx-tab">
    <h3>Popup Ad</h3>

    <p>
        <input type="hidden" name="popup_enabled" value="false" />
        <label>
            <input type="checkbox"
                   id="popup_enabled"
                   name="popup_enabled"
                   value="true" <?php checked(get_option('popup_enabled'), 'true'); ?> />
            Enable Popup Ad
        </label>
    </p>

    <p>
        <label for="popup_network_code"><strong>Popup Network Code</strong></label><br>
        <input type="text"
               id="popup_network_code"
               name="popup_network_code"
               value="<?php echo esc_attr(get_option('popup_network_code')); ?>"
               class="regular-text" />
    </p>

    <p class="description">
        This slot uses a pre-defined code template and does not support editing.
    </p>
</div>
