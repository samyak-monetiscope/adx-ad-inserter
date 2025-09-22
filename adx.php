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

// 1. Settings registration & UI
require_once plugin_dir_path( __FILE__ ) . 'settings-page.php';
// 2. Settings-template is loaded by settings-page.php

// 3. Renderers (always loaded, but we’ll hook conditionally)
require_once plugin_dir_path( __FILE__ ) . 'views/header-renderer.php';
require_once plugin_dir_path( __FILE__ ) . 'views/footer-renderer.php';

// 4. Conditional hooks based on global toggle
if ( get_option('adx_enabled','false') === 'true' ) {
    add_action( 'wp_head',   'adx_v4_render_header_ads' );
    add_action( 'wp_footer', 'adx_v4_render_footer_ads' );
}

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
