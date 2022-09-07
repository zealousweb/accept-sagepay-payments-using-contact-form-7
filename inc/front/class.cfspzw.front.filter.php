<?php
/**
* CFSPZW_Front_Filter Class
*
* Handles the Frontend Filters.
*
* @package WordPress
* @subpackage Accept Sagepay Payments Using Contact Form 7
* @since 1.2
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'CFSPZW_Front_Filter' ) ) {

	/**
	*  The CFSPZW_Front_Filter Class
	*/
	class CFSPZW_Front_Filter {

		function __construct() {

			/**
			* Wrap form
			*/
			 add_filter( 'wpcf7_form_elements', array( $this, 'filter__cfspzw_wpcf7_form_elements' ), 10 );

		}

		/*
		######## #### ##       ######## ######## ########   ######
		##        ##  ##          ##    ##       ##     ## ##    ##
		##        ##  ##          ##    ##       ##     ## ##
		######    ##  ##          ##    ######   ########   ######
		##        ##  ##          ##    ##       ##   ##         ##
		##        ##  ##          ##    ##       ##    ##  ##    ##
		##       #### ########    ##    ######## ##     ##  ######
		*/

		function filter__cfspzw_wpcf7_form_elements( $code ) {

			/* If the form has multistep's shortcode */
			if ( strpos( $code, '<fieldset class="fieldset-cfspzw' ) ) {

				if ( defined( 'WPCF7_AUTOP ') && ( WPCF7_AUTOP == true ) ) {
					$code = preg_replace('#<p>(.*?)<\/fieldset><fieldset class=\"fieldset-cfspzw\"><\/p>#', '$1</fieldset><fieldset class="fieldset-cfspzw">', $code);
				}

				$code = '<fieldset class="fieldset-cfspzw">' . $code;

				$code .= '</fieldset>';
			}

			return $code;
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
		CFSPZW()->front->filter = new CFSPZW_Front_Filter;
	} );

}
