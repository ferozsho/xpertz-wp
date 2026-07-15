<?php
/**
 * Plugin Name: Xpertz Loopback Fix
 * Description: Fixes loopback requests targeting localhost:9080 inside Docker by rewriting them to 127.0.0.1 and keeping the Host header.
 * Version: 1.2
 */

add_filter( 'pre_http_request', 'xpertz_fix_loopback_request', 10, 3 );
function xpertz_fix_loopback_request( $pre, $parsed_args, $url ) {
    // Intercept loopback requests targeting localhost:9080
    if ( strpos( $url, '://localhost:9080' ) !== false ) {
        // Rewrite target to internal 127.0.0.1 (port 80)
        $new_url = str_replace( '://localhost:9080', '://127.0.0.1', $url );
        
        // Ensure there is a path separator if query string starts immediately
        if ( strpos( $new_url, '://127.0.0.1?' ) !== false ) {
            $new_url = str_replace( '://127.0.0.1?', '://127.0.0.1/?', $new_url );
        }
        
        // Ensure headers array exists
        if ( ! isset( $parsed_args['headers'] ) || ! is_array( $parsed_args['headers'] ) ) {
            $parsed_args['headers'] = array();
        }
        
        // Preserve the original Host header so WordPress does not trigger a canonical 301 redirect
        $parsed_args['headers']['Host'] = 'localhost:9080';
        
        return wp_remote_request( $new_url, $parsed_args );
    }
    return $pre;
}
