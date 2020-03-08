<?php
/*
Plugin Name: Nepali Post Date
Plugin URI: https://www.padamshankhadev.com
Description: A Nepali Post Date Plugin
Version: 3.0.0
Author: Padam Shankhadev
Author URI: https://www.padamshankhadev.com
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

/* Load Up the text domain */
function npdate_textdomain() {
	load_plugin_textdomain( 'npdate', false, basename( dirname( __FILE__ ) ) . '/languages' );
}

add_action( 'plugins_loaded', 'npdate_textdomain' );

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

if( ! function_exists( 'get_nepali_post_date' )) {

	function get_nepali_post_date( $post_date ) {

		require_once( NEPALIPOSTDATE_PLUGIN_DIR . 'class.nepali.date.php' );
		$default_opts = array(
            'active' => array( 'date' => true, 'time' => true ),
            'date_format' => 'd m y, l',
            'custom_date_format' => ''
        );

        $default_opts = apply_filters( 'npd_modify_default_opts', $default_opts );
        $opts = get_option( 'npd_opts', $default_opts );
        $post_date = ( !empty( $post_date ) ) ? strtotime( $post_date ) : time();
        $date = new Nepali_Date();
        $nepali_calender = $date->eng_to_nep( date( 'Y', $post_date ), date( 'm', $post_date ), date( 'd', $post_date ) );
        $nepali_year = $date->convert_to_nepali_number( $nepali_calender['year'] );
        $nepali_month = $nepali_calender['nmonth'];
        $nepali_day = $nepali_calender['day'];
        $nepali_date = $date->convert_to_nepali_number( $nepali_calender['date'] );
        $nepali_hour = $date->convert_to_nepali_number( date( 'H', $post_date ));
        $nepali_minute = $date->convert_to_nepali_number( date( 'i', $post_date ) );

        if ( $opts['custom_date_format'] ) {
            $format = $opts['custom_date_format'];
        } else {
            $format = $opts['date_format'];
        }

        $converted_date = str_replace( array( 'l', 'd', 'm', 'y' ), array( $nepali_day, $nepali_date, $nepali_month, $nepali_year ), $format );
        if ( $opts['active']['time'] ) {
            $converted_date .= ' ' . $nepali_hour . ':' . $nepali_minute;
        }

        return $converted_date;
	}
}
