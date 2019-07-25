<?php
function route_update_get_updates() {
	include_once( ABSPATH . 'wp-admin/includes/update.php' );
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	wp_cache_flush();
	
    return rest_ensure_response( array( get_plugin_updates() ) );
}

function route_update_update( $data ) {
    include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
    include_once( ABSPATH . 'wp-admin/includes/file.php' );
    include_once( ABSPATH . 'wp-admin/includes/misc.php' );
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    wp_cache_flush();
    
    $plugins = json_decode( $data -> get_body() )->plugins;
    
    $upgrader = new Plugin_Upgrader();
    $upgraded = $upgrader->bulk_upgrade( $plugins );
    activate_plugins( $plugins );

    return rest_ensure_response( array( $upgraded ) );
}
?>