<?php
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'adx_v4_settings_page' ) ) {

function adx_v4_settings_page() {
    $slots = [
      'Display Slot'          => ['enabled'=>'display_slot_enabled','code'=>null],
        'Popup'                 => ['enabled'=>'popup_enabled','code'=>'popup_network_code'],
        'Button Rewarded'       => ['enabled'=>'ad2_enabled','code'=>'ad2_network_code'],
        'Flying Carpet'         => ['enabled'=>'flying_enabled','code'=>'flying_network_code'],
        'Anchor'                => ['enabled'=>'anchor_enabled','code'=>'anchor_network_code'],
        // 'Side Floater'          => ['enabled'=>'side_floater_enabled','code'=>'side_floater_network_code'],
        'Offerwall (on Scroll)' => ['enabled'=>'offerwall_onscroll_enabled','code'=>'offerwall_onscroll_network_code'],
        // 'Coupon Rewarded'       => ['enabled'=>'coupon_rewarded_enabled','code'=>'coupon_rewarded_network_code'],
        'Interstitial'          => ['enabled'=>'interstitial_enabled','code'=>'interstitial_network_code'],
        'Custom'                => ['enabled'=>'custom_enabled','code'=>null],
    ];

    $tabs = [
      'tab-display-slot'       => 'Display Ad Slot',
        'tab-popup'              => 'Popup Ad',
        'tab-button-rewarded'    => 'Button Rewarded Ad',
        'tab-flying'             => 'Flying Carpet Ad',
        'tab-anchor'             => 'Anchor Ad',
        // 'tab-side-floater'       => 'Side Floater Ad',
        'tab-offerwall-onscroll' => 'Offerwall (on Scroll) Ad',
        // 'tab-coupon-rewarded'    => 'Coupon Rewarded Ad',
        'tab-interstitial'       => 'Interstitial Ad',
        'tab-custom'             => 'Custom',
    ];

    $panels = [
      'settings-display.php',
        'settings-popup.php',
        'settings-button-rewarded.php',
        'settings-flying-carpet.php',
        'settings-anchor.php',
        // 'settings-side-floater.php',
        'settings-offerwall-onscroll.php',
        // 'settings-coupon-rewarded.php',
        'settings-interstitial.php',
        'settings-custom.php',
    ];
?>
<div class="wrap">
  <style>
    /* … your existing CSS … */
  </style>

  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  

  <form method="post" action="options.php">
    <?php
      settings_fields('adx_v4_settings');
      do_settings_sections('adx_v4_settings');
    ?>

    <div class="form-header">
      <h1 class="settings-title">AdX Ad Inserter (Powered by Monetiscope)</h1>
      <hr class="settings-separator">
      <div class="form-actions">
        <div class="form-toggle">
          <?php $is_active = get_option('adx_enabled') === 'true'; ?>
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

        <div class="head banner" style="position: relative; left: -20px;">
          <a href="https://monetiscope.com/contact/" target="_blank" rel="noopener noreferrer">
            <img src="https://i.postimg.cc/GpsGRqZt/Ad-X-Ad-Insertor-Banner-Ad.jpg" alt="banner" class="rounded-md hover:rounded-none hover:shadow-lg duration-200">
          </a>
        </div>

        <?php submit_button( 'Save Changes', 'primary', 'submit', false, [ 'id'=>'adx_save_top' ] ); ?>
      </div>
    </div>

    <div class="settings-container">
      <div class="settings-left">
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

        <div class="settings-main">
          <?php foreach ( $panels as $panel ) {
              require __DIR__ . '/settings/' . $panel;
          } ?>
        </div>
      </div>

      <aside class="settings-sidebar">
        <h2>Slot Status</h2>
        <ul>
          <?php foreach ( $slots as $label => $keys ) :
            if ( $label === 'Display Slot' ) {
                $display_enabled = get_option('display_slot_enabled') === 'true';
                $any_code = false;
                $any_enabled_with_code = false;
                for ( $j = 1; $j <= 10; $j++ ) {
                    $sub_code    = trim( get_option("display_slot_{$j}_network_code") );
                    $sub_enabled = get_option("display_slot_{$j}_enabled") === 'true';
                    if ( $sub_code !== '' ) {
                        $any_code = true;
                        if ( $sub_enabled ) {
                            $any_enabled_with_code = true;
                        }
                    }
                }
                if ( ! $any_code ) {
                    $cls = 'status-empty';      // grey
                } elseif ( $display_enabled && $any_enabled_with_code ) {
                    $cls = 'status-active';     // green
                } else {
                    $cls = 'status-filled';     // red
                }
            }
            elseif ( 'Custom' === $label ) {
                $enabled  = get_option( $keys['enabled'] ) === 'true';
                $hdr      = trim( get_option('custom_header_code') );
                $ftr      = trim( get_option('custom_footer_code') );
                $has_code = $hdr !== '' || $ftr !== '';
                $cls = ! $has_code ? 'status-empty'
                     : ( ! $enabled ? 'status-filled' : 'status-active' );
            }
            else {
                $enabled = get_option( $keys['enabled'] ) === 'true';
                $code    = $keys['code'] ? trim( get_option( $keys['code'] ) ) : '';
                $cls = ! $code ? 'status-empty'
                     : ( ! $enabled ? 'status-filled' : 'status-active' );
            }
          ?>
            <li>
              <span class="status-indicator <?php echo esc_attr( $cls ); ?>"></span>
              <span class="status-label"><?php echo esc_html( $label ); ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
        <hr>
        <div class="links">
          <h3 class="mb-0" style="margin-bottom: 0;">Connect to Us</h3>
          <div class="social-icons grid grid-cols-3 gap-4">
            <a href="https://monetiscope.com/adx-ad-inserter-plugin/" target="_blank" rel="noopener noreferrer" class="shadow-lg transition duration-150 opacity-75 hover:opacity-100 rounded-lg p-4 hover:shadow-xl ">
              <img src="https://cdn-icons-png.flaticon.com/512/74/74842.png" alt="Website">
            </a>

            <a href="https://www.linkedin.com/company/monetiscope" target="_blank" rel="noopener noreferrer" class="shadow-lg transition duration-150 opacity-75 hover:opacity-100 rounded-lg p-4 hover:shadow-xl ">
              <img src="https://cdn-icons-png.flaticon.com/512/3128/3128219.png" alt="LinkedIn">
            </a>

            <a href="https://x.com/monetiscope" target="_blank" rel="noopener noreferrer" class="shadow-lg transition duration-150 opacity-75 hover:opacity-100 rounded-lg p-4 hover:shadow-xl ">
              <img src="https://cdn-icons-png.flaticon.com/512/5968/5968958.png" alt="Twitter">
            </a>
          </div>
        </div>
      </aside>
    </div>
  </form>
</div>

<script async type="module"
  src="https://interfaces.zapier.com/assets/web-components/zapier-interfaces/zapier-interfaces.esm.js">
</script>

<zapier-interfaces-chatbot-embed
  is-popup="true"
  chatbot-id="cmc8tco1i00178eenqd9m351r">
</zapier-interfaces-chatbot-embed>

<script>
document.addEventListener('DOMContentLoaded', function(){
  var toggle = document.getElementById('adx_enabled');
  var title  = document.querySelector('.toggle-title');
  toggle.addEventListener('change', function(){
    title.textContent = this.checked ? 'Plugin Active' : 'Plugin Inactive';
  });
});
</script>

<?php
} // end function
} // end if
