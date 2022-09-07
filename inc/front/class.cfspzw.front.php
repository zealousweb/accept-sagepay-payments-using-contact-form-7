<?php
/**
* CFSPZW_Front Class
*
* Handles the Frontend functionality.
*
* @package WordPress
* @subpackage Accept Sagepay Payments Using Contact Form 7
* @since 1.2
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'CFSPZW_Front' ) ) {

	/**
	* The CFSPZW_Front Class
	*/
	class CFSPZW_Front {

		/**
		* @var string Base URL endpoint for pages.
		*/
		const BASE_ENDPOINT = 'cfspzw-phpinfo';

		var $action = null,
			$filter = null;

		function __construct() {
			add_filter( 'query_vars',			array( $this, 'filter__cfspzw_query_vars' ) );
			add_filter( 'template_include',		array( $this, 'filter__cfspzw_template_include' ) );
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


		/*
		######## #### ##       ######## ######## ########   ######
		##        ##  ##          ##    ##       ##     ## ##    ##
		##        ##  ##          ##    ##       ##     ## ##
		######    ##  ##          ##    ######   ########   ######
		##        ##  ##          ##    ##       ##   ##         ##
		##        ##  ##          ##    ##       ##    ##  ##    ##
		##       #### ########    ##    ######## ##     ##  ######
		*/


		/**
		* Filter: query_vars
		*
		* - added query variable for custom endpoint.
		*
		* @param array $vars
		*
		* @return array
		*/
		function filter__cfspzw_query_vars( $vars ) {
			$vars[] = $this::BASE_ENDPOINT;
			return $vars;
		}

		/**
		* Filter: template_include
		*
		* - change template call for the server configuration
		*
		* @param string $template
		* @return string
		*/
		function filter__cfspzw_template_include( $template ) {
			global $wp_query;

			if ( !isset( $wp_query->query_vars[$this::BASE_ENDPOINT] ) )
				return $template;


			return CFSPZW_DIR . '/inc/front/template/cfspzw-info.php';

			return $template;
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

	add_action( 'plugins_loaded', function() {
		CFSPZW()->front = new CFSPZW_Front;
	} );

}
