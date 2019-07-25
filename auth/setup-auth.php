<?php
add_filter( 'rest_authentication_errors', 's_maintain_authentication_errors' );
function s_maintain_authentication_errors() {
    $headers = getallheaders();
    
    if( !isset( $headers['Authorization'] ) || !isset ( $headers['Http-Date'] ) ) {
        return new WP_Error( 'rest_unauthorized', 'No authentication headers present.', array( 'status' => 401 ) );
    }
    
    $request_token = explode( ' ', $headers['Authorization'] )[1];
    $request_timestamp = $headers['Http-Date'];
    
    $timestamp = round( microtime( true ) * 1000 );
    
    if ( $timestamp - (int) $request_timestamp > 15000 ) {
        return new WP_Error( 'rest_token_expired', 'The token has expired.', array( 'status' => 401 ) );
    }
    
    require_once( ABSPATH . 'wp-config.php' );
    $token = hash( 'sha256', SECURE_AUTH_KEY . get_option( 's_maintain_settings' )['s_maintain_key'] . $request_timestamp );
    
    if ( $token == $request_token ) {
        return false;
    }

    return new WP_Error( 'rest_unauthorized', 'The token is invalid.', array( 'status' => 401 ) );
}
?>