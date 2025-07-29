<?php
defined('ABSPATH') || exit;
?>
<div id="tab-reward-on-scroll" class="adx-tab" style="display:none">
    <h3>Reward on Scroll Ad</h3>

    <p>
        <input type="hidden" name="reward_on_scroll_enabled" value="false" />
        <label>
            <input type="checkbox"
                   id="reward_on_scroll_enabled"
                   name="reward_on_scroll_enabled"
                   value="true" <?php checked(get_option('reward_on_scroll_enabled'), 'true'); ?> />
            Enable Reward on Scroll Ad
        </label>
    </p>

    <p>
        <label for="reward_on_scroll_network_code"><strong>Reward on Scroll Network Code</strong></label><br>
        <input type="text"
               id="reward_on_scroll_network_code"
               name="reward_on_scroll_network_code"
               value="<?php echo esc_attr(get_option('reward_on_scroll_network_code')); ?>"
               class="regular-text"
               placeholder="/123456789/Your_Reward_Slot" />
    </p>
</div>
