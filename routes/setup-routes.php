<?php
add_action( 'rest_api_init', 'register_s_maintain_rest_routes' );
function register_s_maintain_rest_routes() {
    register_backup_routes();
    register_update_routes();
}

function register_backup_routes() {
    require_once( __DIR__ . '/routes-backup.php' );
    
    register_rest_route( 's-maintain', '/backups/backup/tables/', array(
        'methods'  => 'GET',
        'callback' => 'route_backup_get_tables',
    ) );
    
    register_rest_route( 's-maintain', '/backups/backup/create/tables/', array(
        'methods'  => 'POST',
        'callback' => 'route_backup_backup_tables',
    ) );
    
    register_rest_route( 's-maintain', '/backups/backup/create/files/', array(
        'methods'  => 'POST',
        'callback' => 'route_backup_backup_files',
    ) );    
}

function register_update_routes() {
    require_once( __DIR__ . '/routes-update.php' );
    
    register_rest_route( 's-maintain', '/updates/', array(
        'methods'  => 'GET',
        'callback' => 'route_update_get_updates',
    ) );
    
    register_rest_route( 's-maintain', '/updates/update/', array(
        'methods'  => 'POST',
        'callback' => 'route_update_update',
    ) );
}
?>