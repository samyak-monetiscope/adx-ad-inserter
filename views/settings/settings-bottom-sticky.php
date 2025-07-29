<?php
defined('ABSPATH') || exit;
?>
<div id="tab-bottom-sticky" class="adx-tab" style="display:none">
    <h3>Bottom Sticky Ad</h3>

    <p>
        <input type="hidden" name="bottom_sticky_enabled" value="false" />
        <label>
            <input type="checkbox"
                   id="bottom_sticky_enabled"
                   name="bottom_sticky_enabled"
                   value="true" <?php checked(get_option('bottom_sticky_enabled'), 'true'); ?> />
            Enable Bottom Sticky Ad
        </label>
    </p>

    <p>
        <label for="bottom_sticky_network_code"><strong>Bottom Sticky Network Code</strong></label><br>
        <input type="text"
               id="bottom_sticky_network_code"
               name="bottom_sticky_network_code"
               value="<?php echo esc_attr(get_option('bottom_sticky_network_code')); ?>"
               class="regular-text" />
    </p>
</div>
