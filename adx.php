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
define( 'ADXMS_FILE', __FILE__ );
define( 'ADXMS_DIR', plugin_dir_path( __FILE__ ) );
define( 'ADXMS_URL', plugin_dir_url( __FILE__ ) );
define( 'ADXMS_JS_VERSION', '1.0.0' );  // for your plugin files
define( 'ADXMS_GPT_VERSION', '1.0.0' );  // for Google GPT script



//Settings registration & UI
require_once ADXMS_DIR . 'settings-page.php';

//Renderers (always loaded, but we’ll hook conditionally)
require_once ADXMS_DIR . '/ad-renderer.php';


// 5. Add "Settings" link on the Plugins page
add_filter(
  'plugin_action_links_' . plugin_basename( __FILE__ ),
  'adx_add_settings_action_link'
);
function adx_add_settings_action_link( $links ) {
    $url   = admin_url( 'options-general.php?page=adx-ad-inserter' );
    $label = __( 'Settings', 'adx-ad-inserter' );
    array_unshift(
      $links,
      '<a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>'
    );
    return $links;
}
