<?php
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'adx_v4_settings_page' ) ) {

function adx_v4_settings_page() {
    // slot definitions for sidebar status
    $slots = [
        'Popup'                 => ['enabled'=>'popup_enabled','code'=>'popup_network_code'],
        'Button Rewarded'       => ['enabled'=>'ad2_enabled','code'=>'ad2_network_code'],
        'Flying Carpet'         => ['enabled'=>'flying_enabled','code'=>'flying_network_code'],
        'Anchor'                => ['enabled'=>'anchor_enabled','code'=>'anchor_network_code'],
//         'Bottom Sticky'         => ['enabled'=>'bottom_sticky_enabled','code'=>'bottom_sticky_network_code'],
        'Side Floater'          => ['enabled'=>'side_floater_enabled','code'=>'side_floater_network_code'],
        'Reward on Scroll'      => ['enabled'=>'reward_on_scroll_enabled','code'=>'reward_on_scroll_network_code'],
        'Offerwall (on Scroll)' => ['enabled'=>'offerwall_onscroll_enabled','code'=>'offerwall_onscroll_network_code'],
        'Coupon Rewarded'       => ['enabled'=>'coupon_rewarded_enabled','code'=>'coupon_rewarded_network_code'],
        'Interstitial'          => ['enabled'=>'interstitial_enabled','code'=>'interstitial_network_code'],
        'Custom'                => ['enabled'=>'custom_enabled','code'=>null],
    ];

    // tab IDs & labels
    $tabs = [
        'tab-popup'              => 'Popup Ad',
        'tab-button-rewarded'    => 'Button Rewarded Ad',
        'tab-flying'             => 'Flying Carpet Ad',
        'tab-anchor'             => 'Anchor Ad',
//         'tab-bottom-sticky'      => 'Bottom Sticky Ad',
        'tab-side-floater'       => 'Side Floater Ad',
        'tab-reward-on-scroll'   => 'Reward on Scroll Ad',
        'tab-offerwall-onscroll' => 'Offerwall (on Scroll) Ad',
        'tab-coupon-rewarded'    => 'Coupon Rewarded Ad',
        'tab-interstitial'       => 'Interstitial Ad',
        'tab-custom'             => 'Custom',
    ];

    // settings panel files (one per slot)
    $panels = [
        'settings-popup.php',
        'settings-button-rewarded.php',
        'settings-flying-carpet.php',
        'settings-anchor.php',
//         'settings-bottom-sticky.php',
        'settings-side-floater.php',
        'settings-reward-on-scroll.php',
        'settings-offerwall-onscroll.php',
        'settings-coupon-rewarded.php',
        'settings-interstitial.php',
        'settings-custom.php',
    ];
    ?>
    <div class="wrap">
		<style>
		/* reset WP admin interferences */
.wrap {
  background: #f9fafb;
  padding: 0 20px 10px 20px;
  font-family: "Segoe UI", sans-serif;
}

/* ---- header ---- */
.form-header {
  /* display: flex; */
  align-items: center;
  /* justify-content: space-between; */
  margin-bottom: 24px;
}
.settings-title {
  
  margin: 10px 0 20px 0;
  font-size: 1.5rem;
  font-weight: 600;
  padding: 10px 0 0px 0 !important;
  color: #111827;
}
.form-actions {
  display: flex;
  align-items: end;
  justify-content: space-between;
  gap : 4rem;
}
.form-toggle{
  display: flex;
  align-items: center;
  justify-content:flex-start;
  gap: 10px;
}
.toggle-title{
  width : 8rem;
}
.btn-primary {
  background-color: #2563eb; 
  color: #fff;
  border: none;
  padding: 8px 16px;
  font-size: 0.875rem;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color .2s;
}
.btn-primary:hover {
  background-color: #1e40af;
}

/* ---- toggle switch ---- */
.switch {
  position: relative;
  display: inline-block;
  width: 42px;
  height: 24px;
  margin-right: 16px;
}
.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}
.slider {
  position: absolute;
  cursor: pointer;
  inset: 0;
  background-color: #d1d5db;
  transition: .4s;
  border-radius: 34px;
}
.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}
input:checked + .slider {
  background-color: #10b981;
}
input:checked + .slider:before {
  transform: translateX(18px);
}
.slider.round {
  border-radius: 34px;
}

/* ---- overall layout ---- */
.settings-container {
  display: grid;
  grid-template-columns: 4fr 1fr;
  gap: 24px;
}

/* ---- left side: nav + panels ---- */
.settings-left {
  display: grid;
  grid-template-columns: 1fr 3fr;
  gap: 16px;
}
.settings-nav ul {
  list-style: none;
  padding: 1px;
  margin: 0;
}
.settings-nav li + li {
  margin-top: 8px;
}
.nav-tab {
  display: block;
  padding: 10px 14px;
  text-decoration: none;
  color: #4b5563;
  font-size: 0.875rem;
  border-left: 4px solid transparent;
  transition: background-color .2s, border-color .2s;
  width: 100%;
}
.nav-tab:hover {
  background: #f3f4f6;
}
.nav-tab-active {
  background: #eff6ff;
  border-left-color: #3b82f6;
  color: #1e40af;
}

/* panel area */
.settings-main {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  padding: 20px;
}
.bottom-save {
  margin-top: 24px;
  text-align: right;
}

/* ---- right sidebar: status ---- */
.settings-sidebar {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  padding: 20px;
}
.settings-sidebar h2 {
  margin-top: 0;
  font-size: 1rem;
  font-weight: 500;
  color: #111827;
  margin-bottom: 12px;
}
.settings-sidebar ul {
  list-style: none;
  padding: 0;
  margin: 0;
}
.settings-sidebar li {
  display: flex;
  align-items: center;
  margin-bottom: 12px;
}
.status-indicator {
  display: inline-block;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  margin-right: 8px;
}
.status-empty {
  background: transparent;
  border: 2px solid #d1d5db;
}
.status-filled {
  background: #d34343;
  border: none;
}
.status-active {
  background: #10b981;
  border: none;
}
.status-label {
  font-size: 0.875rem;
  color: #374151;
}
.form-script-input{
  width: 100%;
}
.script-input{
  display: flex;
  gap: .5rem;
}

		</style>
      <!-- load our custom CSS -->
		    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<!-- 		<link rel= stylesheet href= ../css/index.css > -->

<!--       <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__) . 'index.css'; ?>"> -->

      <form method="post" action="options.php">
        <?php
          settings_fields('adx_v4_settings');
          do_settings_sections('adx_v4_settings');
        ?>

        <!-- header: title, global toggle & save -->
        <div class="form-header">
          <h1 class="settings-title">AdX Ad Inserter (Powered by Monetiscope)</h1>
          <hr class="settings-separator">
          <div class="form-actions">
            <div class="form-toggle">
              <?php 
                $is_active = get_option('adx_enabled') === 'true'; 
              ?>
              <h2 class="toggle-title">
                <?php echo $is_active ? 'Plugin Active' : 'Plugin Inactive'; ?>
              </h2>
              <label class="switch">
                <input
                  type="checkbox"
                  id="adx_enabled"
                  name="adx_enabled"
                  value="true"
                  <?php checked( $is_active, true ); ?>
                >
                <span class="slider round"></span>
              </label>
            </div>
              <!-- Global head script input -->
           <div class="form-script-input" >
             <label for="global_head_script"><strong>Global Head Script</strong></label>
             <div class="script-input">
              <textarea
               name="global_head_script"
               id="global_head_script"
               rows="1"
               style="width:100%;font-family:monospace;"
               placeholder="<script>…</script>"
             ><?php echo esc_textarea( get_option('global_head_script','') ); ?></textarea>
             <button type="submit" name="set_global_head" class="button">Set</button>
             </div>
           </div>
            <?php submit_button( 'Save Changes', 'primary', 'submit', false, [ 'id'=>'adx_save_top' ] ); ?>
          </div>
        </div>

        <!-- main grid: left=nav+panels | right=status -->
        <div class="settings-container">
          <div class="settings-left">
            <!-- nav tabs -->
            <nav class="settings-nav">
              <ul>
                <?php foreach ( $tabs as $id => $label ) : ?>
                  <li>
                    <a
                      href="#"
                      class="nav-tab<?php echo $id === 'tab-popup' ? ' nav-tab-active' : ''; ?>"
                      data-target="<?php echo esc_attr( $id ); ?>"
                    >
                      <?php echo esc_html( $label ); ?>
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            </nav>

            <!-- panels -->
            <div class="settings-main">
              <?php
                foreach ( $panels as $panel ) {
                  require __DIR__ . '/settings/' . $panel;
                }
              ?>
              <div class="bottom-save">
<!--                  <?php submit_button(); ?>-->
              </div>
            </div>
          </div>

          <!-- sidebar status -->
          <aside class="settings-sidebar">
            <div class="slot-status">
              <h2>Slot Status</h2>
            <ul>
             <?php foreach ( $slots as $label => $keys ) :
                $enabled = get_option( $keys['enabled'] ) === 'true';

                if ( 'Custom' === $label ) {
                  // for Custom: check either header or footer code
                  $hdr = trim( get_option('custom_header_code') );
                  $ftr = trim( get_option('custom_footer_code') );
                  $has_code = $hdr !== '' || $ftr !== '';

                  if ( ! $has_code ) {
                    $cls = 'status-empty';
                  } elseif ( $has_code && ! $enabled ) {
                    $cls = 'status-filled';
                  } else {
                    $cls = 'status-active';
                  }
                } else {
                  // existing slots
                  $code = $keys['code']
                      ? trim( get_option( $keys['code'] ) )
                      : '';
                  if ( ! $code ) {
                    $cls = 'status-empty';
                  } elseif ( $code && ! $enabled ) {
                    $cls = 'status-filled';
                  } else {
                    $cls = 'status-active';
                  }
                }
              ?>
              <li>
                <span class="status-indicator <?php echo esc_attr( $cls ); ?>"></span>
                <span class="status-label"><?php echo esc_html( $label ); ?></span>
              </li>
              <?php endforeach; ?>

            </ul>
            </div>
            <div class="links">
              <a href="https://monetiscope.com/adx-ad-inserter-plugin/" target="_blank" rel="noopener noreferrer"><img</a>
            </div>
          </aside>
        </div>
      </form>
    </div>
<!-- Load the Zapier Interfaces web component bundle -->
<script async type="module"
  src="https://interfaces.zapier.com/assets/web-components/zapier-interfaces/zapier-interfaces.esm.js">
</script>

<!-- Place this wherever you want the pop-up chatbot trigger to appear -->
<zapier-interfaces-chatbot-embed
  is-popup="true"
  chatbot-id="cmc8tco1i00178eenqd9m351r">
</zapier-interfaces-chatbot-embed>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
      var toggle = document.getElementById('adx_enabled');
      var title  = document.querySelector('.toggle-title');

      // update on click
      toggle.addEventListener('change', function(){
        title.textContent = this.checked
          ? 'Plugin Active'
          : 'Plugin Inactive';
      });
    });
    </script>
    <?php
} // end adx_v4_settings_page()
} // endif function_exists
