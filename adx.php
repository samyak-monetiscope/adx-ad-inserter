<?php
/**
 * Plugin Name: AdX Ad Inserter
 * Plugin URI: https://monetiscope.com/adx-ad-inserter-plugin/
 * Description: Revolutionize your website's monetization with advanced ad formats by Monetiscope. Enable rewarded ads, pop-ups, floater ads, sticky ads, and more in one click.
 * Author: Monetiscope
 * Version: 1.0.0
 * Author URI: http://monetiscope.com
 * Requires at least: 5.0
 * Tested up to: 6.5
 * Requires PHP: 7.4
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Developer : Samyak Jain (samyak.jn2431@gmail.com)
 */

defined( 'ABSPATH' ) || exit;
define( 'ADXBYMS_FILE', __FILE__ );
define( 'ADXBYMS_DIR', plugin_dir_path( __FILE__ ) );
define( 'ADXBYMS_URL', plugin_dir_url( __FILE__ ) );
define( 'ADXBYMS_JS_VERSION', '1.0.0' );  // for your plugin files
define( 'ADXBYMS_CSS_VERSION', '1.0.0' );  // for your plugin files
define( 'ADXBYMS_GPT_VERSION', '1.0.0' );  // for Google GPT script



//Settings registration & UI
require_once ADXBYMS_DIR . 'settings-page.php';

//Renderers (always loaded, but we’ll hook conditionally)
require_once ADXBYMS_DIR . '/ad-renderer.php';


// 5. Add "Settings" link on the Plugins page
add_filter(
  'plugin_action_links_' . plugin_basename( __FILE__ ),
  'adxbyms_add_settings_action_link'
);
function adxbyms_add_settings_action_link( $links ) {
    $url   = admin_url( 'options-general.php?page=adx-ad-inserter' );
    $label = __( 'Settings', 'adx-ad-inserter' );
    array_unshift(
      $links,
      '<a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>'
    );
    return $links;
}


// ... your existing plugin header and bootstrapping

// Include the ads.txt slot so the function exists
require_once plugin_dir_path(__FILE__) . 'views/slots/ads-txt.php';

register_activation_hook(__FILE__, 'adxbyms_activate');
function adxbyms_activate() {
    adxbyms_register_ads_txt_rewrite();
    flush_rewrite_rules();
}

register_deactivation_hook(__FILE__, 'adxbyms_deactivate');
function adxbyms_deactivate() {
    flush_rewrite_rules();
}
