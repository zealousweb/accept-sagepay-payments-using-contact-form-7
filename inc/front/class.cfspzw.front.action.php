<?php
/**
* CFSPZW_Front_Action Class
*
* Handles the Frontend Actions.
*
* @package WordPress
* @subpackage Accept Sagepay Payments Using Contact Form 7
* @since 1.0
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'CFSPZW_Front_Action' ) ){

	/**
	*  The CFSPZW_Front_Action Class
	*/
	class CFSPZW_Front_Action {

		function __construct()  {

			add_action( 'wp_enqueue_scripts', array( $this, 'action__cfspzw_wp_enqueue_scripts' ) );

		}

		/*
		   ###     ######  ######## ####  #######  ##    ##  ######
		  ## ##   ##    ##    ##     ##  ##     ## ###   ## ##    ##
		 ##   ##  ##          ##     ##  ##     ## ####  ## ##
		##     ## ##          ##     ##  ##     ## ## ## ##  ######
		######### ##          ##     ##  ##     ## ##  ####       ##
		##     ## ##    ##    ##     ##  ##     ## ##   ### ##    ##
		##     ##  ######     ##    ####  #######  ##    ##  ######
		*/

		function action__cfspzw_wp_enqueue_scripts() {
			wp_enqueue_script( CFSPZW_PREFIX . '_toast_js', CFSPZW_URL . 'assets/js/jquery.toast.min.js', array( 'jquery-core' ), '1.3.2' );
			wp_enqueue_script( CFSPZW_PREFIX . '_front_js', CFSPZW_URL . 'assets/js/front.min.js', array( 'jquery-core' ), CFSPZW_VERSION );
			
			wp_enqueue_style( CFSPZW_PREFIX . '_front_css', CFSPZW_URL . 'assets/css/front-style.min.css', array(), CFSPZW_VERSION );
			wp_enqueue_style( CFSPZW_PREFIX . '_toast_css', CFSPZW_URL . 'assets/css/jquery.toast.min.css', array(), '1.3.2' );

			wp_register_style( CFSPZW_PREFIX . '_select2', CFSPZW_URL . 'assets/css/select2.min.css', array(), CFSPZW_VERSION );
			wp_register_script( CFSPZW_PREFIX . '_select2', CFSPZW_URL . 'assets/js/select2.min.js', array( 'jquery-core' ), CFSPZW_VERSION );
		}



		/*
		######## ##     ## ##    ##  ######  ######## ####  #######  ##    ##  ######
		##       ##     ## ###   ## ##    ##    ##     ##  ##     ## ###   ## ##    ##
		##       ##     ## ####  ## ##          ##     ##  ##     ## ####  ## ##
		######   ##     ## ## ## ## ##          ##     ##  ##     ## ## ## ##  ######
		##       ##     ## ##  #### ##          ##     ##  ##     ## ##  ####       ##
		##       ##     ## ##   ### ##    ##    ##     ##  ##     ## ##   ### ##    ##
		##        #######  ##    ##  ######     ##    ####  #######  ##    ##  ######
		*/

	}
}
