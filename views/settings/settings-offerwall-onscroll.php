<?php
defined('ABSPATH') || exit;
?>
<div id="tab-offerwall-onscroll" class="adx-tab" style="display:none">
    <h3>Offerwall (on Scroll) Ad</h3>

    <p>
        <input type="hidden" name="offerwall_onscroll_enabled" value="false" />
        <label>
            <input type="checkbox"
                   id="offerwall_onscroll_enabled"
                   name="offerwall_onscroll_enabled"
                   value="true" <?php checked(get_option('offerwall_onscroll_enabled'), 'true'); ?> />
            Enable Offerwall (on Scroll) Ad
        </label>
    </p>

    <p>
        <label for="offerwall_onscroll_network_code"><strong>Offerwall Network Code</strong></label><br>
        <input type="text"
               id="offerwall_onscroll_network_code"
               name="offerwall_onscroll_network_code"
               value="<?php echo esc_attr(get_option('offerwall_onscroll_network_code')); ?>"
               class="regular-text"
               placeholder="/22859853152/MS_024JOBS_Scroll_Offerwall" />
    </p>

    <p>
        <label for="offerwall_onscroll_logo_url"><strong>Publisher Logo URL</strong></label><br>
        <input type="text"
               id="offerwall_onscroll_logo_url"
               name="offerwall_onscroll_logo_url"
               value="<?php echo esc_attr(get_option('offerwall_onscroll_logo_url')); ?>"
               class="regular-text"
               placeholder="https://monetiscope.com/wp-content/uploads/2025/05/cropped-e-2.png" />
        <br><span class="description">
            If left empty, a default Monetiscope logo will be used.
        </span>
    </p>
</div>
