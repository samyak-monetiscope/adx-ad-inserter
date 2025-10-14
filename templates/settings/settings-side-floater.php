<?php
defined('ABSPATH') || exit;

$enabled = get_option('side_floater_enabled') === 'true';
$netcode = trim( get_option('side_floater_network_code') );
?>
<div id="tab-side-floater" class="adx-tab" style="display:none; position:relative;">
  <h3>Side Floater Ad</h3>

  <div class="locked-wrapper">
    <div class="locked-content blur-xs select-none pointer-events-auto">
      <!-- existing controls -->
      <p><label>
        <input type="checkbox"
               id="side_floater_enabled"
               name="side_floater_enabled"
               value="true" <?php checked( $enabled, 'true' ); ?> />
        Enable Side Floater Ad
      </label></p>

      <p>
        <label for="side_floater_network_code"><strong>Side Floater Network Code</strong></label><br>
        <input type="text"
               id="side_floater_network_code"
               name="side_floater_network_code"
               value="<?php echo esc_attr( $netcode ); ?>"
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
