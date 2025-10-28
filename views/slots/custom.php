<?php
defined('ABSPATH') || exit;

/** Print custom header code */
function adxbyms_render_custom_header_slot() {
    $headerCode = get_option('custom_header_code');
    if ( ! empty( $headerCode ) ) {
        echo $headerCode; // phpcs:ignore WordPress.Security.EscapeOutput
    }
}

/** Print custom footer code */
function adxbyms_render_custom_footer_slot() {
    $footerCode = get_option('custom_footer_code');
    if ( ! empty( $footerCode ) ) {
        echo $footerCode; // phpcs:ignore WordPress.Security.EscapeOutput
    }
}


