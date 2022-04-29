<?php
/**
* Plugin Name: Accept Sagepay Payments Using Contact Form 7
* Plugin URL: https://www.zealousweb.com/wordpress-plugins/product/accept-sagepay-payments-using-contact-form-7/
* Description:  This plugin will integrate Sagepay payment gateway for making your payments through Contact Form 7.
* Version: 1.0
* Author: ZealousWeb Technologies
* Author URI: https://www.zealousweb.com
* Developer: The Zealousweb Team
* Support: opensource@zealousweb.com
* Text Domain: accept-sagepay-payments-using-contact-form-7
* Domain Path: /languages
*
* Copyright: © 2009-2020 ZealousWeb Technologies.
* License: GNU General Public License v3.0
* License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
* Basic plugin definitions
*
* @package Accept Sagepay Payments using Contact Form 7
* @since 1.0
*/

if ( !defined( 'CFSPZW_VERSION' ) ) {
	define( 'CFSPZW_VERSION', '1.0' ); // Version of plugin
}

if ( !defined( 'CFSPZW_FILE' ) ) {
	define( 'CFSPZW_FILE', __FILE__ ); // Plugin File
}

if ( !defined( 'CFSPZW_DIR' ) ) {
	define( 'CFSPZW_DIR', dirname( __FILE__ ) ); // Plugin dir
}

if ( !defined( 'CFSPZW_URL' ) ) {
	define( 'CFSPZW_URL', plugin_dir_url( __FILE__ ) ); // Plugin url
}

if ( !defined( 'CFSPZW_PLUGIN_BASENAME' ) ) {
	define( 'CFSPZW_PLUGIN_BASENAME', plugin_basename( __FILE__ ) ); // Plugin base name
}

if ( !defined( 'CFSPZW_META_PREFIX' ) ) {
	define( 'CFSPZW_META_PREFIX', 'cfspzw_' ); // Plugin metabox prefix
}

if ( !defined( 'CFSPZW_PREFIX' ) ) {
	define( 'CFSPZW_PREFIX', 'cfspzw' ); // Plugin prefix
}

if ( !defined( 'CFSPZW_POST_TYPE' ) ) {
	define( 'CFSPZW_POST_TYPE', 'cfspzw_data' ); // Plugin post type
}

if ( !defined( 'CFSPZW_SUPPORT' ) ) {
	define( 'CFSPZW_SUPPORT', 'mailto:opensource@zealousweb.com' ); // Plugin Support Link
}

if ( !defined( 'CFSPZW_DOCUMENT' ) ) {
	define( 'CFSPZW_DOCUMENT', 'https://www.zealousweb.com/documentation/wordpress-plugins/accept-sagepay-payments-using-contact-form-7/' ); // Plugin Document Link
}

/**
* Initialize the main class
*/
if ( !function_exists( 'CFSPZW' ) ) {

	if ( is_admin() ) {
		require_once( CFSPZW_DIR . '/inc/admin/class.' . CFSPZW_PREFIX . '.admin.php' );
		require_once( CFSPZW_DIR . '/inc/admin/class.' . CFSPZW_PREFIX . '.admin.action.php' );
		require_once( CFSPZW_DIR . '/inc/admin/class.' . CFSPZW_PREFIX . '.admin.filter.php' );
	} else {
		require_once( CFSPZW_DIR . '/inc/front/class.' . CFSPZW_PREFIX . '.front.php' );
		require_once( CFSPZW_DIR . '/inc/front/class.' . CFSPZW_PREFIX . '.front.action.php' );
		require_once( CFSPZW_DIR . '/inc/front/class.' . CFSPZW_PREFIX . '.front.filter.php' );
	}

	require_once( CFSPZW_DIR . '/inc/lib/class.' . CFSPZW_PREFIX . '.lib.php' );

	//Initialize all the things.
	require_once( CFSPZW_DIR . '/inc/class.' . CFSPZW_PREFIX . '.php' );
}
