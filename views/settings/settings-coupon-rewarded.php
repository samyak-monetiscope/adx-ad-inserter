<?php
defined('ABSPATH') || exit;

$enabled = get_option('coupon_rewarded_enabled') === 'true';
$netcode = trim( get_option('coupon_rewarded_network_code') );
$coupon  = trim( get_option('coupon_rewarded_code') );
?>
<div id="tab-coupon-rewarded" class="adx-tab" style="display:none; position:relative;">
  <h3>Coupon Rewarded</h3>

  <div class="locked-wrapper">
    <div class="locked-content blur-xs select-none pointer-events-auto">
      <!-- existing controls -->
      <p><label>
        <input type="checkbox"
               id="coupon_rewarded_enabled"
               name="coupon_rewarded_enabled"
               value="true" <?php checked( $enabled, 'true' ); ?> />
        Enable Coupon Rewarded
      </label></p>

      <p>
        <label for="coupon_rewarded_network_code"><strong>Network Code</strong></label><br>
        <input type="text"
               id="coupon_rewarded_network_code"
               name="coupon_rewarded_network_code"
               value="<?php echo esc_attr( $netcode ); ?>"
               class="regular-text" />
      </p>

      <p>
        <label for="coupon_rewarded_code"><strong>Coupon Code</strong></label><br>
        <input type="text"
               id="coupon_rewarded_code"
               name="coupon_rewarded_code"
               value="<?php echo esc_attr( $coupon ); ?>"
               class="regular-text" />
      </p>
    </div>

    <div class="lock-overlay absolute top-10 px-10, py-5">
      <h2>This page is locked</h2>
      <p>For unlocking, visit<br>
        <a href="https://monetiscope.com" target="_blank">monetiscope.com</a>
      </p>
    </div>
  </div>
</div>
