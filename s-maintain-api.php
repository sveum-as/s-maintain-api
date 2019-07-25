<?php
/**
 * Plugin Name: S:maintain API
 * Version: 1.0.0
 * Author: Sveum AS
 * Author URI: https://sveum.as
 * Description: Creates API routes for your WP site that allows communication with the S:maintain dashboard.
 * Text Domain: s-maintain-api
 */
if ( ! defined( 'ABSPATH' ) ) exit;
 
require_once ( __DIR__ . '/auth/setup-auth.php' );
require_once ( __DIR__ . '/routes/setup-routes.php' );
require_once ( __DIR__ . '/admin/settings.php' );
require_once ( __DIR__ . '/updater.php' );
?>