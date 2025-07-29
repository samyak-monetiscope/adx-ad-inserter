<?php
defined('ABSPATH') || exit;

/** Print custom header code */
function adx_render_custom_header_slot() {
    $code = get_option('custom_header_code');
    if ( ! empty( $code ) ) {
        echo $code; // phpcs:ignore WordPress.Security.EscapeOutput
    }
}

/** Print custom footer code */
function adx_render_custom_footer_slot() {
    $code = get_option('custom_footer_code');
    if ( ! empty( $code ) ) {
        echo $code; // phpcs:ignore WordPress.Security.EscapeOutput
    }
}
