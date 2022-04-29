<?php
class CFSPZW_Licence {

	static $status = null;

	private static $_instance = null;
	static $activation_menuname = 'Sagepay Add-on Licence',
		$licence_status = 'cfspzw_addon_license_status',
		$activation_redirect = 'cfspzw_addon_activation_redirect' ,
		$licence_nonce = 'cfspzw_addon_nonce' ,
		$activation_action = 'cfspzw_addon_license_activate' ,
		$zw_deactivation_action = 'cfspzw_addon_license_deactivate',
		$valid_url = 'https://www.zealousweb.com/wp-json/activator/v1/activate/',
		$item_name = 'Accept Sagepay Payments using Contact Form 7',
		$license_page = 'cfspzw-license-activation',
		$item_id = '15032';

	const cfspzw_licence_key = 'cfspzw_addon_license_key',
		  cfspzw_licence_email = 'cfspzw_addon_license_email';

	public static function instance() {
		return self::$status;
	}

	function __construct() {

		self::$status = get_option( self::$licence_status );

		register_activation_hook( CFSPZW_FILE, array( $this, 'zw_licence_extension' ) );
		add_action( 'setup_theme',		array( $this, 'action__setup_theme' ) );
		add_action( 'rest_api_init',	array( $this, 'action__rest_api_init' ) );
		add_action( 'admin_init',		array( $this, 'zw_licence_check_activation' ) );
		add_action( 'admin_init',		array( $this, 'zw_licence_activate_license' ) );
		add_action( 'admin_init',		array( $this, 'zw_licence_deactivate_license' ) );
		add_action( 'admin_menu',		array( $this, 'zw_licence_menu' ) );
		add_action( 'admin_notices',	array( $this, 'zw_licence_admin_notices') );
		register_deactivation_hook( CFSPZW_FILE, array( $this, 'zw_licence_extension_deactivation' ) );

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

	function action__setup_theme() {
		if ( !empty( self::$status ) ) {

			if ( is_admin() ) {
				CFSPZW()->admin = new CFSPZW_Admin;
				CFSPZW()->admin->action = new CFSPZW_Admin_Action;
				CFSPZW()->admin->filter = new CFSPZW_Admin_Filter;
			} else {
				CFSPZW()->front = new CFSPZW_Front;
				CFSPZW()->front->action = new CFSPZW_Front_Action;
				CFSPZW()->front->filter = new CFSPZW_Front_Filter;
			}
			CFSPZW()->lib = new CFSPZW_Lib;
		}
	}

	function action__rest_api_init() {
		register_rest_route(
			'licences',
			'/removed',
			array(
				'callback' =>	array( $this, 'api__removed' ),
			)
		);
	}

	function zw_licence_extension() {
		update_option( self::$activation_redirect, 'yes' );
		flush_rewrite_rules();
	}

	function zw_licence_check_activation() {

		if ( class_exists('WPCF7') ) {

			if ( 'yes' === get_option( self::$activation_redirect, 'no' ) ) {

				update_option( self::$activation_redirect, 'no' );

				if ( ! isset( $_GET['activate-multi'] ) ) {
					wp_redirect( admin_url( 'admin.php?page=' . self::$license_page ) );
				}
			}
		}
	}

	function zw_licence_menu() {
		add_submenu_page(
			'wpcf7',
			self::$activation_menuname,
			self::$activation_menuname,
			'manage_options',
			self::$license_page,
			array( __CLASS__, 'zw_license_page' )
		);
	}

	public static function zw_licence_activate_license() {

		// listen for our activate button to be clicked
		if ( isset( $_POST[ self::$activation_action ] ) ) {

			// run a quick security check
			if ( ! check_admin_referer( self::$licence_nonce, self::$licence_nonce ) ) {
				return;
			} // get out if we didn't click the Activate button

			// retrieve the license from the database
			$license = trim( get_option( self::cfspzw_licence_key ) );
			$license_email = trim( get_option( self::cfspzw_licence_email ) );

			// Save license key
			$license = $_POST['cfspzw_license_key'];
			$license_email = $_POST['cfspzw_license_email'];

			$license_data = array();

			// data to send in our API request
			$api_params = array(
				'action'	=> 'activate_license',
				'key'		=> $license,
				'email'		=> $license_email,
				'id'		=> self::$item_id,
				'host'		=> home_url()
			);

			// Call the custom API.
			$response = wp_remote_get( self::$valid_url, array(
				'timeout'	=> 15,
				'sslverify'	=> false,
				'body'		=> $api_params
			) );

			$message = '';


			// make sure the response came back okay
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

				if ( is_wp_error( $response ) ) {
					$message = $response->get_error_message();
				} else {
					$message = __( 'An error occurred, please try again.', 'accept-sagepay-payments-using-contact-form-7' );
				}

			} else {

				$license_data = json_decode( wp_remote_retrieve_body( $response ), true );

				if ( is_array($license_data) && array_key_exists( 'success', $license_data ) && empty(  $license_data['success'] ) ) {

					switch( $license_data['error'] ) {

						case 'expired' :

							$message = sprintf(
								__( 'Your license key expired.', 'accept-sagepay-payments-using-contact-form-7' )
							);
							break;

						case 'revoked' :

							$message = __( 'Your license key has been disabled.', 'accept-sagepay-payments-using-contact-form-7' );
							break;

						case 'missing' :
							$message = __( 'Invalid license.', 'accept-sagepay-payments-using-contact-form-7' );
							break;

						case 'invalid' :
						case 'site_inactive' :

							$message = __( 'Your license is not active for this URL.', 'accept-sagepay-payments-using-contact-form-7' );
							break;

						case 'item_name_mismatch' :

							$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'accept-sagepay-payments-using-contact-form-7' ), self::$item_name );
							break;

						case 'no_activations_left':

							$message = __( 'Your license key has reached its activation limit.', 'accept-sagepay-payments-using-contact-form-7' );
							break;

						default :

							$message = __( 'An error occurred, please try again.', 'accept-sagepay-payments-using-contact-form-7' );
							break;
					}

				}

			}

			// Check if anything passed on a message constituting a failure
			if ( ! empty( $message ) ) {
				$base_url = admin_url( 'admin.php?page=' . self::$license_page );
				$redirect = add_query_arg( array('zw_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

				wp_redirect( $redirect );
				exit();
			}

			update_option( self::cfspzw_licence_key, $license );
			update_option( self::cfspzw_licence_email, $license_email );
			update_option( self::$licence_status, $license_data['license'] );

			$base_url = admin_url( 'admin.php?page=' . self::$license_page );
			$redirect = add_query_arg( array('zw_activation' => 'true', 'message' => urlencode( 'success' ) ), $base_url );
			wp_redirect( $redirect );
			exit();

		}
	}


	public static function zw_licence_deactivate_license() {

		// listen for our activate button to be clicked
		if ( isset( $_POST[ self::$zw_deactivation_action ] ) ) {

			// run a quick security check
			if ( ! check_admin_referer( self::$licence_nonce, self::$licence_nonce ) ) {
				return;
			} // get out if we didn't click the Activate button

			// retrieve the license from the database
			$license_status = trim( get_option( self::$licence_status ) );
			$license = trim( get_option( self::cfspzw_licence_key ) );
			$license_email = trim( get_option( self::cfspzw_licence_email ) );

			// data to send in our API request
			$api_params = array(
				'action'	=> 'deactivate_license',
				'key'		=> $license_status,
				'email'		=> $license_email,
				'id'		=> urlencode( self::$item_id ), // the name of our product in uo
				'host'		=> home_url()
			);

			// Call the custom API.
			$response = wp_remote_get( self::$valid_url, array(
				'timeout'	=> 15,
				'sslverify'	=> false,
				'body'		=> $api_params
			) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

				if ( is_wp_error( $response ) ) {
					$message = $response->get_error_message();
				} else {
					$message = __( 'An error occurred, please try again.', 'accept-sagepay-payments-using-contact-form-7' );
				}

				$base_url = admin_url( 'admin.php?page=' . self::$license_page );
				$redirect = add_query_arg( array( 'zw_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

				wp_redirect( $redirect );
				exit();
			}

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ), true );

			// $license_data->license will be either "deactivated" or "failed"
			if ( $license_data['license'] == 'deactivated' || $license_data['license'] == 'failed' ) {
				delete_option( self::cfspzw_licence_key );
				delete_option( self::cfspzw_licence_email );
				delete_option( self::$licence_status );
			}

			$base_url = admin_url( 'admin.php?page=' . self::$license_page );
			$redirect = add_query_arg( array('zw_activation' => 'false', 'message' => urlencode( 'Successfully Deactivated!' ) ), $base_url );
			wp_redirect( $redirect );
			exit();
		}
	}

	function zw_licence_admin_notices() {
		if (
			isset( $_GET[ 'page' ] )
			&& self::$license_page == $_GET['page']
		) {

			if (
				   isset( $_GET['zw_activation'] )
				&& isset( $_GET['zw_activation'] )
				&& ! empty( $_GET['message'] )
			) {

				switch( $_GET['zw_activation'] ) {

					case 'false':
						$message = urldecode( $_GET['message'] );
						?>
						<div class="error">
							<p><?php echo $message; ?></p>
						</div>
						<?php
						break;

					case 'true':
					default:
						?>
						<div class="updated">
							<p>Licence Activation Successfully!</p>
						</div>
						<?php
						break;

				}
			}
		}
	}

	function zw_licence_extension_deactivation() {

		// retrieve the license from the database
		$license_status = trim( get_option( self::$licence_status ) );
		$license = trim( get_option( self::cfspzw_licence_key ) );
		$license_email = trim( get_option( self::cfspzw_licence_email ) );

		// data to send in our API request
		$api_params = array(
			'action'	=> 'deactivate_license',
			'key'		=> $license_status,
			'email'		=> $license_email,
			'id'		=> urlencode( self::$item_id ), // the name of our product in uo
			'host'		=> home_url()
		);

		// Call the custom API.
		$response = wp_remote_get( self::$valid_url, array(
			'timeout'	=> 15,
			'sslverify'	=> false,
			'body'		=> $api_params
		) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || (
			200 !== wp_remote_retrieve_response_code( $response )
			&& 400 !== wp_remote_retrieve_response_code( $response )
		) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.', 'accept-sagepay-payments-using-contact-form-7' );
			}

			$base_url = admin_url( 'admin.php?page=' . self::$license_page );
			$redirect = add_query_arg( array( 'zw_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

			wp_redirect( $redirect );
			exit();
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ), true );

		// $license_data->license will be either "deactivated" or "failed"
		if ( $license_data['license'] == 'deactivated' || $license_data['license'] == 'failed' ) {
			delete_option( self::cfspzw_licence_key );
			delete_option( self::cfspzw_licence_email );
			delete_option( self::$licence_status );
		}
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

	public static function zw_license_page() {
		$license = get_option( self::cfspzw_licence_key );
		$license_email = get_option( self::cfspzw_licence_email );
		$status = get_option( self::$licence_status );
		$error = '';
		?>

		<div class="wrap">
			<h2 class=""><?php echo self::$activation_menuname; ?></h2>
			<form method="post" action="options.php">
				<?php settings_fields( 'cfspzw_license' ); ?>

				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e( 'Email Address *', CFSPZW_PREFIX  ); ?>
							</th>
							<td>
								<input
									id="cfspzw_license_email"
									name="cfspzw_license_email"
									type="email"
									class="regular-text"
									value="<?php esc_attr_e( $license_email ); ?>" <?php if ( !empty( $status ) ) { echo 'disabled'; } ?> required
								/>
								<label class="description" for="cfspzw_license_email">
									<?php _e( 'Enter your email which is used for purchase license', CFSPZW_PREFIX ); ?>
								</label>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e( 'License Key *', CFSPZW_PREFIX ); ?>
							</th>
							<td>
								<input
									id="cfspzw_license_key"
									name="cfspzw_license_key"
									type="text"
									class="regular-text"
									value="<?php esc_attr_e( $license ); ?>" <?php if ( !empty( $status )  ) { echo 'disabled'; }?> required
								/>
								<label class="description" for="cfspzw_license_key">
									<?php _e( 'Enter your license key', CFSPZW_PREFIX ); ?>
								</label>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e( 'Activate License', CFSPZW_PREFIX  ); ?>
							</th>
							<td>
								<?php if ( !empty( $status ) ) { ?>
									<span style="color: #29c129; font-weight:bold; line-height: 27px;padding-right: 20px;">Your License is active. </span>
									<?php wp_nonce_field( self::$licence_nonce, self::$licence_nonce ); ?>
									<input
										type="submit"
										class="button-secondary"
										name="<?php echo self::$zw_deactivation_action; ?>"
										value="<?php _e( 'Deactivate License', CFSPZW_PREFIX ); ?>
									"/>
								<?php } else {
									wp_nonce_field( self::$licence_nonce, self::$licence_nonce ); ?>
									<input
										type="submit"
										class="button-secondary"
										name="<?php echo self::$activation_action; ?>"
										value="<?php _e( 'Activate License' ); ?>"
										style="background: #29c129; border-color: #29c129!important; text-decoration: none; color: white; font-size: 17px; padding: 8px 0; width: 170px; line-height: 0;"
									/>
								<?php } ?>
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>

		<?php
	}

	function api__removed() {
		delete_option( self::cfspzw_licence_key );
		delete_option( self::cfspzw_licence_email );
		delete_option( self::$licence_status );

		return true;
	}

}
?>
