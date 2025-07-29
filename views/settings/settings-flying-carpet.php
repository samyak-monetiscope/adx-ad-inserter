<?php
defined('ABSPATH') || exit;
?>
<div id="tab-flying" class="adx-tab" style="display:none">
    <h3>Flying Carpet Ad</h3>

    <p>
        <input type="hidden" name="flying_enabled" value="false" />
        <label>
            <input type="checkbox"
                   id="flying_enabled"
                   name="flying_enabled"
                   value="true" <?php checked(get_option('flying_enabled'), 'true'); ?> />
            Enable Flying Carpet Ad
        </label>
    </p>

    <p>
        <label for="flying_network_code"><strong>Flying Carpet Network Code</strong></label><br>
        <input type="text"
               id="flying_network_code"
               name="flying_network_code"
               value="<?php echo esc_attr(get_option('flying_network_code')); ?>"
               class="regular-text" />
    </p>
</div>
