<?php
defined('ABSPATH') || exit;

$enabled = get_option('interstitial_enabled','false');
$netcode = get_option('interstitial_network_code','');
?>
<div id="tab-interstitial" class="adx-tab" style="display:none;">
  <h3>Interstitial Ad</h3>

  <p>
    <label>
      <input type="checkbox"
             id="interstitial_enabled"
             name="interstitial_enabled"
             value="true" <?php checked($enabled, 'true'); ?> />
      Enable Interstitial Slot
    </label>
  </p>

  <p>
    <label for="interstitial_network_code"><strong>Network Code</strong></label><br>
    <input type="text"
           id="interstitial_network_code"
           name="interstitial_network_code"
           value="<?php echo esc_attr($netcode); ?>"
           class="regular-text"
           placeholder="/23269135876/MS_TheGorakhpur_Interstitial" />
    <br><span class="description">
      Ad unit path must start with a slash.
    </span>
  </p>
</div>
