<?php
defined('ABSPATH') || exit;
?>
<div id="tab-anchor" class="adx-tab" style="display:none">
    <h3>Anchor Ad</h3>

    <p>
        <input type="hidden" name="anchor_enabled" value="false" />
        <label>
            <input type="checkbox"
                   id="anchor_enabled"
                   name="anchor_enabled"
                   value="true" <?php checked(get_option('anchor_enabled'), 'true'); ?> />
            Enable Anchor Ad
        </label>
    </p>

    <p>
        <label for="anchor_position"><strong>Anchor Position</strong></label><br>
        <select id="anchor_position" name="anchor_position">
            <option value="TOP_ANCHOR"    <?php selected(get_option('anchor_position'), 'TOP_ANCHOR'); ?>>Top Anchor</option>
            <option value="BOTTOM_ANCHOR" <?php selected(get_option('anchor_position'), 'BOTTOM_ANCHOR'); ?>>Bottom Anchor</option>
        </select>
    </p>

    <p>
        <label for="anchor_network_code"><strong>Anchor Network Code</strong></label><br>
        <input type="text"
               id="anchor_network_code"
               name="anchor_network_code"
               value="<?php echo esc_attr(get_option('anchor_network_code')); ?>"
               class="regular-text" />
    </p>
</div>
