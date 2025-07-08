<?php
defined( 'ABSPATH' ) || exit;

// Flying Carpet slot
require_once __DIR__ . '/slots/flying-carpet.php';

// Anchor slot
require_once __DIR__ . '/slots/anchor.php';

// Custom slot (both header & footer functions live here)
require_once __DIR__ . '/slots/custom.php';

/**
 * Output header‐going ads
 */
function adx_v4_render_header_ads() {
    if ( $script = get_option('global_head_script') ) {
        echo $script; // phpcs:ignore WordPress.Security.EscapeOutput
    }
    adxbymonetiscope_render_flying_carpet_slot();
    adx_render_anchor_slot();
    // only if enabled
    if ( get_option('custom_enabled') === 'true' ) {
        adx_render_custom_header_slot();
    }
}
