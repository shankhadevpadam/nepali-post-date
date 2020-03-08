<?php
/*
Plugin Name: Nepali Post Date
Plugin URI: http://www.padamshankhadev.com
Description: A Nepali Post Date Plugin
Version: 1.0.0
Author: Padam Shankhadev
Author URI: http://www.padamshankhadev.com
*/

/* Prevent Direct access */
if ( !defined( 'DB_NAME' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	die;
}

/* Define BaseName */
if ( !defined( 'NEPALIPOSTDATE_BASENAME' ) )
	define( 'NEPALIPOSTDATE_BASENAME', plugin_basename( __FILE__ ) );

/* Define plugin url */
if( !defined('NEPALIPOSTDATE_PLUGIN_URL' ))
	define('NEPALIPOSTDATE_PLUGIN_URL', plugin_dir_url(__FILE__));

/* Define plugin path */
if( !defined('NEPALIPOSTDATE_PLUGIN_DIR' ))
	define('NEPALIPOSTDATE_PLUGIN_DIR', plugin_dir_path(__FILE__));

/* Plugin version */
define('NEPALIPOSTDATE', '1.0.0');

/* Check if we're running compatible software */
if ( version_compare( PHP_VERSION, '5.2', '<' ) && version_compare( WP_VERSION, '3.7', '<' ) ) {
	if ( is_admin() ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		deactivate_plugins( __FILE__ );
		wp_die( __( 'Nepali post date plugin requires WordPress 3.8 and PHP 5.3 or greater. The plugin has now disabled itself' ) );
	}
}


/* Let's load up our plugin */
function npd_frontend_init() {
	require_once( NEPALIPOSTDATE_PLUGIN_DIR . 'class.nepali.date.php' );
	require_once( NEPALIPOSTDATE_PLUGIN_DIR . 'class.nepali.date.front.php' );
	new Nepali_Post_Date_Frontend;
}

function npd_admin_init() {
	require_once( NEPALIPOSTDATE_PLUGIN_DIR . 'class.nepali.date.admin.php' );
	new Nepali_Post_Date_Admin();
}

if( is_admin() ) :

	add_action( 'plugins_loaded', 'npd_admin_init', 15 );

else :

	add_action( 'plugins_loaded', 'npd_frontend_init', 50 );

endif;
