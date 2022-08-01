<?php
/**
* CFSPZW_Lib Class
*
* Handles the Library functionality.
*
* @package WordPress
* @subpackage Accept Sagepay Payments Using Contact Form 7
* @since 1.2
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'CFSPZW_Lib' ) ) {

	class CFSPZW_Lib {

		var $context = '';

		var $data_fields = array(
			'_form_id'				=> 'Form ID/Name',
			'_user_name'			=> 'User Name',
			'_email'				=> 'Email Address',
			'_transaction_id'		=> 'Transaction ID',
			'_invoice_no'			=> 'Invoice ID',
			'_amount'				=> 'Amount',
			'_quantity'				=> 'Quantity',
			'_total'				=> 'Total',
			'_submit_time'			=> 'Submit Time',
			'_request_Ip'			=> 'Request Ip',
			'_currency'				=> 'Currency Code',
			'_form_data'			=> 'Form data',
			'_transaction_status'	=> 'Transaction status',
			'_transaction_response'	=> 'Transaction Response',
		);


		function __construct() {
			add_action( 'init', array( $this, 'action__cfspzw_init' ) );
			add_action( 'wpcf7_init', array( $this, 'action__cfspzw_wpcf7_verify_version' ), 10, 0 );
			add_action( 'wpcf7_init', array( $this, 'action__cfspzw_wpcf7_init' ), 10, 0 );
			add_action( 'init', array( $this, 'action__cfspzw_sagepay_direct_ipn' ) );
			add_action( 'wpcf7_before_send_mail', array( $this, 'action__cfspzw_wpcf7_before_send_mail' ), 20, 3 );
			add_shortcode( 'sagepay-details', array( $this, 'shortcode__sagepay_details' ) );
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

		/**
		* Action: init
		*
		* - Fire the email when return back from the sagepay.
		*
		* @method action__init
		*
		*/

		function action__cfspzw_init() {
			if ( !isset( $_SESSION ) || session_status() == PHP_SESSION_NONE ) {
				session_start();
			}
		}


		/**
		 * Sagepay Verify CF7 dependencies.
		 *
		 * @method action__cfspzw_wpcf7_verify_version
		 *
		 */
		function action__cfspzw_wpcf7_verify_version(){

			$cf7_verify = $this->wpcf7_version();

			if ( version_compare( $cf7_verify, '5.2' ) >= 0 ) {
				add_filter( 'wpcf7_feedback_response',	array( $this, 'filter__cfspzw_wpcf7_ajax_json_echo' ), 20, 2 );
			} else{
				add_filter( 'wpcf7_ajax_json_echo',	array( $this, 'filter__cfspzw_wpcf7_ajax_json_echo' ), 20, 2 );
			}

		}

		/**
		 * Initialize Sagepay tag
		 *
		 * @method action__cfspzw_wpcf7_init
		 *
		 *  @param  array form_tag
		 *
		 * @return	mixed
		 */
		function action__cfspzw_wpcf7_init() {

			wpcf7_add_form_tag(
				array( 'sagepay_country', 'sagepay_country*' ),
				array( $this, 'wpcf7_sagepay_country_form_tag_handler' ),
				array( 'name-attr' => true )
			);

		}


		/**
		* Action: init
		*
		* - Fire the email when return back from the Sagepay.
		*
		* @method action__init
		*
		*/
		function action__cfspzw_sagepay_direct_ipn(){
			global $wpdb;

			$form_ID = (int)( isset( $_REQUEST['form'] ) ? sanitize_text_field( $_REQUEST['form'] ) : '' );

			if (
				   isset( $_REQUEST['crypt'] )
				&& !empty( $_REQUEST['crypt'] )
				&& isset( $_REQUEST['sagepay_direct'] )
				&& !empty( $_REQUEST['sagepay_direct'] == 'ipn' )
				&& !empty( $form_ID )
			){

				$from_data  = unserialize( $_SESSION[ CFSPZW_META_PREFIX . 'form_instance' ] );
				$form_ID	= sanitize_text_field( $_REQUEST['form'] );

				$attachment = '';
				if(!empty($_SESSION[ CFSPZW_META_PREFIX . 'form_attachment_' . $form_ID ])){
					$attachment = str_replace('\\', '/', $_SESSION[ CFSPZW_META_PREFIX . 'form_attachment_' . $form_ID ] );
				}

				$get_posted_data = $from_data->get_posted_data();

				$mode  = sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'status', true ) );
				$sandbox_encryption_password	= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'sandbox_encryption_password', true ) );
				$live_encryption_password		= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'live_encryption_password', true ) );

				$encryption_password = ( !empty( $mode ) ? $sandbox_encryption_password : $live_encryption_password );

				$fetch_result = $this->decryptFieldData( sanitize_text_field( $_REQUEST['crypt'] ), $encryption_password );
				wp_parse_str($fetch_result, $output);

				$VendorTxCode 	= $output['VendorTxCode'];
				$VPSTxId		= $output['VPSTxId'];
				$Status			= $output['Status'];
				$StatusDetail	= $output['StatusDetail'];
				$Amount			= $output['Amount'];

				$currency  = sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'currency', true ) );

				$billing_firstnames	= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'billing_firstnames', true ) );
				$billing_surname	= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'billing_surname', true ) );
				$quantity			= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'quantity', true ) );
				$amount 			= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'amount', true ) );
				$customer_email		= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'customer_email', true ) );
				$exceed_ct			= sanitize_text_field( substr( get_option( '_exceed_cfspzw_l' ), 6 ) );

				$billing_firstnames = ( ( !empty( $billing_firstnames ) && array_key_exists( $billing_firstnames, $get_posted_data ) ) ?  $get_posted_data[$billing_firstnames]  : '' );
				$billing_surname	= ( ( !empty( $billing_surname ) && array_key_exists( $billing_surname, $get_posted_data ) ) ? $get_posted_data[$billing_surname] : '' );
				$amount_val	= ( ( !empty( $amount ) && array_key_exists( $amount, $get_posted_data ) ) ? floatval( $get_posted_data[$amount] ) : '0' );
				$quanity_val	= ( ( !empty( $quantity ) && array_key_exists( $quantity, $get_posted_data ) ) ? floatval( $get_posted_data[$quantity] ) : '0' );
				$customer_email	= ( ( !empty( $customer_email ) && array_key_exists( $customer_email, $get_posted_data ) ) ? $get_posted_data[$customer_email] : '' );

				if (
					!empty( $amount )
					&& array_key_exists( $amount, $get_posted_data )
					&& is_array( $get_posted_data[$amount] )
					&& !empty( $get_posted_data[$amount] )
				) {
					$val = 0;
					foreach ( $get_posted_data[$amount] as $k => $value ) {
						$val = $val + floatval($value);
					}
					$amount_val = $val;
				}

				if (
					!empty( $quantity )
					&& array_key_exists( $quantity, $get_posted_data )
					&& is_array( $get_posted_data[$quantity] )
					&& !empty( $get_posted_data[$quantity] )
				) {
					$qty_val = 0;
					foreach ( $get_posted_data[$quantity] as $k => $qty ) {
						$qty_val = $qty_val + floatval($qty);
					}
					$quanity_val = $qty_val;
				}

				$total_amount_Payable = (float) ( empty( $quanity_val ) ? $amount_val : ( $quanity_val* $amount_val ) );

				$transaction_status = $Status;

				if( $transaction_status == 'OK' || $transaction_status == 'AUTHENTICATED' || $transaction_status == 'REGISTERED' ){
					$transaction_icon = 'success';
					$bgColor = '#3c763d';
					$transactions_message = substr( $StatusDetail, 7 );
				}else{
					$transaction_icon = 'error';
					$bgColor = '#a94442';
					$transactions_message = $StatusDetail;
				}

				$sagePay_heading = __( 'SagePay Payment Notification', 'accept-sagepay-payments-using-contact-form-7' );
				add_action( 'wp_footer', function() use ( $transactions_message, $transaction_icon, $bgColor, $sagePay_heading )  { ?>
					<script type="text/javascript">
						jQuery.toast({
							heading: '<?php echo $sagePay_heading ?>',
							bgColor : '<?php echo $bgColor ?>',
							text: '<?php echo $transactions_message; ?>',
							icon: '<?php echo $transaction_icon ?>',
							position : 'bottom-right',
							hideAfter : false,
							showHideTransition: 'slide',
						});
					</script>
				<?php }, 10, 5 );

				$different = $wpdb->get_var( "SELECT* FROM {$wpdb->postmeta} WHERE meta_key = '_invoice_no' AND meta_value = '$VendorTxCode'" );
				if( !empty( $different) ) {
					return;
				}

				if( $transaction_status == 'OK' ||
					$transaction_status == 'AUTHENTICATED' ||
					$transaction_status == 'REGISTERED' ||
					$transaction_status == 'REJECTED' )
				{
					$cfspzw_post_id = wp_insert_post( array (
						'post_type'		=> CFSPZW_POST_TYPE,
						'post_title'	=> $customer_email, // email/invoice_no
						'post_status'	=> 'publish',
						'comment_status'=> 'closed',
						'ping_status'	=> 'closed',
					) );
				}


				if ( !empty( $cfspzw_post_id )  && $transaction_status == 'OK' || $transaction_status == 'AUTHENTICATED' || $transaction_status == 'REGISTERED'
					|| $transaction_status == 'REJECTED' ) {

					if(!get_option('_exceed_cfspzw')){
						sanitize_text_field( add_option('_exceed_cfspzw', '1') );
					}else{
						$exceed_val = sanitize_text_field( get_option( '_exceed_cfspzw' ) ) + 1;
						update_option( '_exceed_cfspzw', $exceed_val );
					}

					if ( !empty( sanitize_text_field( get_option( '_exceed_cfspzw' ) ) ) && sanitize_text_field( get_option( '_exceed_cfspzw' ) ) > $exceed_ct ) {
						$get_posted_data['_exceed_num_cfspzw'] = '1';
					}

					add_post_meta( $cfspzw_post_id, '_form_id', sanitize_text_field( $form_ID ) );
					add_post_meta( $cfspzw_post_id, '_user_name' ,  sanitize_text_field( $billing_firstnames.' '.$billing_surname ) );
					add_post_meta( $cfspzw_post_id, '_email',  sanitize_text_field( $customer_email ) );
					add_post_meta( $cfspzw_post_id, '_transaction_id',  sanitize_text_field( $VPSTxId ) );
					add_post_meta( $cfspzw_post_id, '_invoice_no',  sanitize_text_field( $VendorTxCode ) );
					add_post_meta( $cfspzw_post_id, '_amount',  sanitize_text_field( str_replace( "", '', $amount_val ) .' '. $currency ) );
					add_post_meta( $cfspzw_post_id, '_quantity', $quanity_val );
					add_post_meta( $cfspzw_post_id, '_total',  sanitize_text_field( str_replace( "", '', round( $total_amount_Payable, 2 ) ) .' '. $currency ) );
					add_post_meta( $cfspzw_post_id, '_request_ip', $this->getUserIpAddr() );
					add_post_meta( $cfspzw_post_id, '_currency',  sanitize_text_field( $currency ) );
					add_post_meta( $cfspzw_post_id, '_transaction_status',  sanitize_text_field( $transactions_message ) );
					add_post_meta( $cfspzw_post_id, '_transaction_response',  sanitize_text_field( $fetch_result  ) );
					add_post_meta( $cfspzw_post_id, '_attachment', $attachment );
					add_post_meta( $cfspzw_post_id, '_form_data', serialize( $get_posted_data ) );

					$data = array();
					$data['Transaction ID'] =  trim( $VPSTxId, '{}' );
					$data['Transaction Message'] =  $transactions_message;
					$data['Amount'] =  str_replace( "", '', $total_amount_Payable .' '. $currency );
					$data['Invoice Number'] =  $VendorTxCode;


					add_filter( 'wpcf7_mail_components', array( $this, 'cfspzw_filter__wpcf7_mail_components' ), 888, 3 );
					$this->mail( $from_data, $get_posted_data, $data);
					remove_filter( 'wpcf7_mail_components', array( $this, 'cfspzw_filter__wpcf7_mail_components' ), 888, 3 );

				}
				unset( $_SESSION[ CFSPZW_META_PREFIX . 'secure_form' . $form_ID ] );
				unset( $_SESSION[ CFSPZW_META_PREFIX . 'form_attachment_' . $form_ID ] );
			}
		}

		/**
		* Email send
		*
		* @method mail
		*
		* @param  object $contact_form WPCF7_ContactForm::get_instance()
		* @param  [type] $posted_data  WPCF7_Submission::get_posted_data()
		*
		* @uses $this->prop(), $this->mail_replace_tags(), $this->get_form_attachments(),
		*
		* @return bool
		*/
		function mail( $contact_form, $posted_data, $payment_info_data) {

			if( empty( $contact_form ) ) {
				return false;
			}
			$contact_form_data = $contact_form->get_contact_form();

			$mail = $contact_form_data->prop( 'mail' );
			$mail = $this->mail_replace_tags( $mail, $posted_data, $payment_info_data );

			$result = WPCF7_Mail::send( $mail, 'mail' );

			if ( $result ) {
				$additional_mail = array();

				if (
					$mail_2 = $this->prop( 'mail_2', $contact_form_data )
					and $mail_2['active']
				) {

					$mail_2 = $this->mail_replace_tags( $mail_2, $posted_data, $payment_info_data );
					$additional_mail['mail_2'] = $mail_2;
				}

				$additional_mail = apply_filters( 'wpcf7_additional_mail', $additional_mail, $contact_form_data );

				foreach ( $additional_mail as $name => $template ) {
					WPCF7_Mail::send( $template, $name );
				}

				return true;
			}

			return false;
		}


		/**
		* get the property from the
		*
		* @method prop    used from WPCF7_ContactForm:prop()
		*
		* @param  string $name
		* @param  object $class_object WPCF7_ContactForm:get_current()
		*
		* @return mixed
		*/
		public function prop( $name, $class_object ) {
			$props = $class_object->get_properties();
			return isset( $props[$name] ) ? $props[$name] : null;
		}

		/**
		* Mail tag replace
		*
		* @method mail_replace_tags
		*
		* @param  array $mail
		* @param  array $data
		*
		* @return array
		*/
		function mail_replace_tags( $mail, $data, $payment_info_data ) {
			$mail = ( array ) $mail;
			$data = ( array ) $data;

			$amount = (
				(
					!empty( $data )
					&& is_array( $data )
					&& array_key_exists( '_wpcf7', $data )
				)
				? sanitize_text_field( get_post_meta( $data['_wpcf7'], CFSPZW_META_PREFIX . 'amount', true ) )
				: ''
			) ;

			$quantity = (
				(
					!empty( $data )
					&& is_array( $data )
					&& array_key_exists( '_wpcf7', $data )
				)
				? sanitize_text_field( get_post_meta( $data['_wpcf7'], CFSPZW_META_PREFIX . 'quantity', true ) )
				: ''
			) ;

			$new_mail = array();

			if ( !empty( $mail ) && !empty( $data ) ) {

				foreach ( $mail as $key => $value ) {
					if( $key != 'attachments' ) {

						foreach ( $data as $k => $v ) {
							if (
								!empty( $amount )
								&& is_array( $v )
								&& $k == $amount
							) {
								$v2 = array_sum( $v );
							}elseif (
								!empty( $quantity )
								&& is_array( $v )
								&& $k == $quantity
							) {
								$v2 = array_sum( $v );
							} else if ( is_array( $v ) ) {
								$v2 = implode (", ", $v );
							} else {
								$v2 = $v;
							}

							$value = str_replace( '[' . $k . ']' , $v2, $value );
						}

						if ( $key == 'body' ){

							if( is_array( $payment_info_data ) ){

								$paypaldetails = '';
								if ( $mail['use_html'] == 2 ) {
									$paypaldetails .= "<h2>".__( 'Sagepay Response Details:', 'accept-sagepay-payments-using-contact-form-7' )."</h2><table>";

									foreach($payment_info_data as $paymentKey => $paymentData){
										$paypaldetails .= '<tr><td>'.__( $paymentKey, 'accept-sagepay-payments-using-contact-form-7' ).'</td><td>'.$paymentData.
										'</td></tr>';
									}

									$paypaldetails .= '</table>';
								} else {

									$paypaldetails .= __( 'Sagepay Response Details:', 'accept-sagepay-payments-using-contact-form-7' )."\n"."\n";
									foreach($payment_info_data as $paymentKey => $paymentData){
										$paypaldetails .= __( $paymentKey, 'accept-sagepay-payments-using-contact-form-7' ).' : '.$paymentData."\n";
									}
								}

								$value = str_replace('[sagepay-payment-details]', $paypaldetails, $value);
							}
						}
					}
					$new_mail[ $key ] = $value;
				}
			}

			return $new_mail;
		}

		/**
		* Get attachment for the from
		*
		* @method get_form_attachments
		*
		* @param  int $form_ID form_id
		*
		* @return array
		*/
		function get_form_attachments( $form_ID ) {
			if(
				!empty( $form_ID )
				&& isset( $_SESSION[ CFSPZW_META_PREFIX . 'form_attachment_' . $form_ID ] )
				&& !empty( $_SESSION[ CFSPZW_META_PREFIX . 'form_attachment_' . $form_ID ] )
			) {
				return unserialize( $_SESSION[ CFSPZW_META_PREFIX . 'form_attachment_' . $form_ID ] );
			}
		}

		/**
		* Filter: Modify the email components.
		*
		* @method filter__wpcf7_mail_components
		*
		* @param  array $components
		* @param  object $current_form WPCF7_ContactForm::get_current()
		* @param  object $mail WPCF7_Mail::get_current()
		*
		* @return array
		*/
		function cfspzw_filter__wpcf7_mail_components( $components, $current_form, $mail ) {

			$from_data = unserialize( $_SESSION[ CFSPZW_META_PREFIX . 'form_instance' ] );

			$form_ID = $from_data->get_contact_form()->id();

			if (
				   !empty( $mail->get( 'attachments', true ) )
				&& !empty( $this->get_form_attachments( $form_ID ) )
			) {
				$components['attachments'] = $this->get_form_attachments( $form_ID );
			}

			return $components;
		}

		/**
		* Action: CF7 before send email
		*
		* @method action__cfspzw_wpcf7_before_send_mail
		*
		* @param  object $contact_form WPCF7_ContactForm::get_instance()
		* @param  bool   $abort
		* @param  object $contact_form WPCF7_Submission class
		*
		*/

		function action__cfspzw_wpcf7_before_send_mail( $contact_form, $abort, $wpcf7_submission ) {

			$submission		= WPCF7_Submission::get_instance(); // CF7 Submission Instance
			$form_ID		= $contact_form->id();
			$form_instance	= WPCF7_ContactForm::get_instance($form_ID); // CF7 From Instance


			if ( $submission ) {
				// CF7 posted data
				$posted_data = $submission->get_posted_data();
			}

			if ( !empty( $form_ID ) ) {

				$use_sagepay = sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'use_sagepay', true ) );

				if ( empty( $use_sagepay ) )
					return;

				$mode							= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'status', true ) );
				$sandbox_vendorName				= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'sandbox_vendor_name', true ) );
				$sandbox_encryption_password	= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'sandbox_encryption_password', true ) );
				$live_vendorName				= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'live_vendor_name', true ) );
				$live_encryption_password		= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'live_encryption_password', true ) );
				$transaction_type				= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'transaction_type', true ) );
				$vendorTxCode_prefix			= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'vendor_txcode_prefix', true ) );
				$amount							= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'amount', true ) );
				$customer_email					= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'customer_email', true ) );
				$quantity						= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'quantity', true ) );
				$get_success_redirect_Id		= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'returnurl', true ) );
				$get_cancel_redirect_Id			= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'cancel_returnurl', true ) );
				$success_returnurl				= sanitize_text_field( get_permalink( $get_success_redirect_Id ) );
				$cancel_returnurl				= sanitize_text_field( get_permalink( $get_cancel_redirect_Id ) );

				$billing_firstnames				= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'billing_firstnames', true ) );
				$billing_surname				= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'billing_surname', true ) );
				$billing_address				= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'billing_address', true ) );
				$billing_city					= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'billing_city', true ) );
				$billing_state					= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'billing_state', true ) );
				$billing_country				= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'billing_country', true ) );
				$billing_zip					= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'billing_zip', true ) );

				$shipping_firstnames			= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'shipping_firstnames', true ) );
				$shipping_surname				= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'shipping_surname', true ) );
				$shipping_address				= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'shipping_address', true ) );
				$shipping_city					= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'shipping_city', true ) );
				$shipping_state					= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'shipping_state', true ) );
				$shipping_country				= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'shipping_country', true ) );
				$shipping_zip					= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'shipping_zip', true ) );

				// Set some example data for the payment.
				$currency		= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'currency', true ) );
				$country		= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'country', true ) );
				$Apply3DSecure	= sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'apply3d', true ) );

				if ( empty( $billing_firstnames ) || empty( $billing_surname ) || empty( $billing_address ) || empty( $billing_city )
					||empty( $billing_state ) || empty( $billing_zip ) || empty( $billing_country ) || empty( $shipping_firstnames ) || empty( $shipping_surname ) || empty( $shipping_address ) || empty( $shipping_city ) ||empty( $shipping_state ) || empty( $shipping_country ) || empty( $shipping_zip ) || empty( $customer_email ) )	{
						$error = __( 'Payment Page not Configured Properly. Please Conatct Admin. ', 'accept-sagepay-payments-using-contact-form-7' );
				}

				if ( !empty( $result ) ){
					add_filter( 'wpcf7_skip_mail', array( $this, 'cfspzw_filter__wpcf7_skip_mail' ), 20 );
					$_SESSION[ CFSPZW_META_PREFIX . 'sagepay_configured_error' . $form_ID ] = $error;
					return;
				}


				$amount_val  = ( ( !empty( $amount ) && array_key_exists( $amount, $posted_data ) ) ? floatval( $posted_data[$amount] ) : '0' );
				$customer_email = ( ( !empty( $customer_email ) && array_key_exists( $customer_email, $posted_data ) ) ? $posted_data[$customer_email] : '' );
				$quanity_val = ( ( !empty( $quantity ) && array_key_exists( $quantity, $posted_data ) ) ? floatval( $posted_data[$quantity] ) : '' );

				$billing_firstnames  = ( ( !empty( $billing_firstnames ) && array_key_exists( $billing_firstnames, $posted_data ) ) ?  $posted_data[$billing_firstnames]  : '' );

				$billing_surname = ( ( !empty( $billing_surname ) && array_key_exists( $billing_surname, $posted_data ) ) ? $posted_data[$billing_surname] : '' );
				$billing_address  = ( ( !empty( $billing_address ) && array_key_exists( $billing_address, $posted_data ) ) ?  $posted_data[$billing_address]  : '' );
				$billing_city = ( ( !empty( $billing_city ) && array_key_exists( $billing_city, $posted_data ) ) ?  $posted_data[$billing_city]  : '' );
				$billing_state  = ( ( !empty( $billing_state ) && array_key_exists( $billing_state, $posted_data ) ) ?  $posted_data[$billing_state] : '' );
				$billing_country  = ( ( !empty( $billing_country ) && array_key_exists( $billing_country, $posted_data ) ) ?  $posted_data[$billing_country] : '' );
				$billing_zip = ( ( !empty( $billing_zip ) && array_key_exists( $billing_zip, $posted_data ) ) ?  $posted_data[$billing_zip] : '' );


				$shipping_firstnames  = ( ( !empty( $shipping_firstnames ) && array_key_exists( $shipping_firstnames, $posted_data ) ) ?  $posted_data[$shipping_firstnames]  : '' );
				$shipping_surname = ( ( !empty( $shipping_surname ) && array_key_exists( $shipping_surname, $posted_data ) ) ? $posted_data[$shipping_surname] : '' );
				$shipping_address  = ( ( !empty( $shipping_address ) && array_key_exists( $shipping_address, $posted_data ) ) ?  $posted_data[$shipping_address]  : '' );
				$shipping_city = ( ( !empty( $shipping_city ) && array_key_exists( $shipping_city, $posted_data ) ) ?  $posted_data[$shipping_city]  : '' );
				$shipping_state  = ( ( !empty( $shipping_state ) && array_key_exists( $shipping_state, $posted_data ) ) ?  $posted_data[$shipping_state] : '' );
				$shipping_country  = ( ( !empty( $shipping_country ) && array_key_exists( $shipping_country, $posted_data ) ) ?  $posted_data[$shipping_country] : '' );
				$shipping_zip = ( ( !empty( $shipping_zip ) && array_key_exists( $shipping_zip, $posted_data ) ) ?  $posted_data[$shipping_zip] : '' );

				if (
					!empty( $amount )
					&& array_key_exists( $amount, $posted_data )
					&& is_array( $posted_data[$amount] )
					&& !empty( $posted_data[$amount] )
				) {
					$val = 0;
					foreach ( $posted_data[$amount] as $k => $value ) {
						$val = $val + floatval($value);
					}
					$amount_val = $val;
				}

				if (
					!empty( $quantity )
					&& array_key_exists( $quantity, $posted_data )
					&& is_array( $posted_data[$quantity] )
					&& !empty( $posted_data[$quantity] )
				) {
					$qty_val = 0;
					foreach ( $posted_data[$quantity] as $k => $qty ) {
						$qty_val = $qty_val + floatval($qty);
					}
					$quanity_val = $qty_val;
				}


				$amountPayable = (float) ( empty( $quanity_val ) ? $amount_val : ( $quanity_val* $amount_val ) );

				if ( empty( $amountPayable ) ) {
					add_filter( 'wpcf7_skip_mail', array( $this, 'filter__wpcf7_skip_mail' ), 20 );
					$_SESSION[ CFSPZW_META_PREFIX . 'amount_error' . $form_ID ] = __( 'Please Enter Amount value or Value in Numeric.', 'accept-sagepay-payments-using-contact-form-7' );
					return;
				}

				if (
					$amountPayable < 0
					&& $amountPayable != 0
				)  {
					add_filter( 'wpcf7_skip_mail', array( $this, 'filter__wpcf7_skip_mail' ), 20 );
					$_SESSION[ CFSPZW_META_PREFIX . 'amount_error' . $form_ID ] = __( 'Please Enter Amount value or Value in Numeric.', 'accept-sagepay-payments-using-contact-form-7' );
					return;
				}

				$amountPayable = sprintf('%0.2f', $amountPayable);

				$validate_field = array();

				if ( empty( $amount_val ) )
					$validate_field[] = sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'amount', true ) );

				if ( empty( $billing_firstnames ) )
					$validate_field[] = sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'billing_firstnames', true ) );

				if ( empty( $billing_surname ) )
					$validate_field[] = sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'billing_surname', true ) );

				if ( empty( $billing_address ) )
					$validate_field[] = sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'billing_address', true ) );

				if ( empty( $billing_city ) )
					$validate_field[] = sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'billing_city', true ) );

				if ( empty( $billing_state ) )
					$validate_field[] = sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'billing_state', true ) );

				if ( empty( $billing_country ) )
					$validate_field[] = sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'billing_country', true ) );

				if ( empty( $billing_zip ) )
					$validate_field[] = sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'billing_zip', true ) );

				if ( empty( $shipping_firstnames ) )
					$validate_field[] = sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'shipping_firstnames', true ) );

				if ( empty( $shipping_surname ) )
					$validate_field[] = sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'shipping_surname', true ) );

				if ( empty( $shipping_address ) )
					$validate_field[] = sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'shipping_address', true ) );

				if ( empty( $shipping_city ) )
					$validate_field[] = sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'shipping_city', true ) );

				if ( empty( $shipping_state ) )
					$validate_field[] = sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'shipping_state', true ) );

				if ( empty( $shipping_country ) )
					$validate_field[] = sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'shipping_country', true ) );

				if ( empty( $shipping_zip ) )
					$validate_field[] = sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'shipping_zip', true ) );

				if ( !empty( $validate_field ) ){
					add_filter( 'wpcf7_skip_mail', array( $this, 'cfspzw_filter__wpcf7_skip_mail' ), 20 );
					$_SESSION[ CFSPZW_META_PREFIX . 'sagepay_fields_error' . $form_ID ] = array_unique( $validate_field );
					return;
				}

				$vendorName				= ( !empty( $mode === 'sandbox' ) ? $sandbox_vendorName : $live_vendorName );
				$encryption_password 	= ( !empty( $mode === 'sandbox' ) ? $sandbox_encryption_password : $live_encryption_password );

				if( $mode == 'sandbox'){
					$sagepay_gateway_url = 'https://test.sagepay.com/gateway/service/vspform-register.vsp';
				}else{
					$sagepay_gateway_url = 'https://live.sagepay.com/gateway/service/vspform-register.vsp';
				}

				if ( !empty( $get_success_redirect_Id ) && $get_success_redirect_Id != 'Select page') {
					$success_returnurl = $success_returnurl;
				}else{
					$success_returnurl = get_permalink( $submission->get_meta('container_post_id') );
				}

				if ( !empty( $get_cancel_redirect_Id ) && $get_cancel_redirect_Id != 'Select page') {
					$cancel_returnurl = $cancel_returnurl;
				}else{
					$cancel_returnurl = get_permalink( $submission->get_meta('container_post_id') );
				}

				$mail = $contact_form->prop( 'mail' );
				$VendorEMail = $mail['recipient'];

				$generate_success_returnurl = add_query_arg( array( 'sagepay_direct' => 'ipn','form' => $form_ID ), $success_returnurl );
				$generate_cancel_returnurl = add_query_arg( array( 'sagepay_direct' => 'ipn','form' => $form_ID ), $cancel_returnurl );

				$time_stamp = date("ymdHis");
				$VendorTxCode = $vendorTxCode_prefix.$form_ID . "-" . $time_stamp;

				$sagepay_arg['VendorTxCode']			= $VendorTxCode;
				$sagepay_arg['Amount']					= $amountPayable;
				$sagepay_arg['Currency']				= $currency;
				$sagepay_arg['Description']				= sprintf(__('Order #%s', 'accept-sagepay-payments-using-contact-form-7'), $VendorTxCode);
				$sagepay_arg['CustomerEMail']			= $customer_email;
				$sagepay_arg['CustomerName']			= $billing_firstnames.' '.$billing_surname;

				$sagepay_arg['BillingSurname']			= $billing_surname;
				$sagepay_arg['BillingFirstnames']		= $billing_firstnames;
				$sagepay_arg['BillingAddress1']			= $billing_address;
				$sagepay_arg['BillingCity']				= $billing_city;
				if( $country == 'US' ){
					$sagepay_arg['BillingState']		= $billing_state;
				}else{
					$sagepay_arg['BillingState']		= '';
				}
				$sagepay_arg['BillingPostCode']			= $billing_zip;
				$sagepay_arg['BillingCountry']			= $billing_country;

				$sagepay_arg['DeliverySurname']			= $shipping_surname;
				$sagepay_arg['DeliveryFirstnames']		= $shipping_firstnames;
				$sagepay_arg['DeliveryAddress1']		= $shipping_address;
				$sagepay_arg['DeliveryCity']			= $shipping_city;
				if( $country == 'US' ){
					$sagepay_arg['DeliveryState']		= $shipping_state;
				}else{
					$sagepay_arg['DeliveryState']		= '';
				}
				$sagepay_arg['DeliveryPostCode']		= $shipping_zip;
				$sagepay_arg['DeliveryCountry']			= $shipping_country;

				$sagepay_arg['Website']					= get_bloginfo( 'name' );

				if( $Apply3DSecure != 0 ){
					$sagepay_arg['Apply3DSecure']		= $Apply3DSecure;
				}

				$sagepay_arg['SuccessURL']				= $generate_success_returnurl;
				$sagepay_arg['FailureURL']				= $generate_cancel_returnurl;

				if ( '[_site_admin_email]' == $VendorEMail ) {
					$vendoremail = get_bloginfo( 'admin_email' );
				}else{
					$vendoremail = $VendorEMail;
				}

				$sagepay_arg['VendorEMail']	= $vendoremail;


				/**
				* - Modify request data for the sagepay_arg
				*
				* @var object $sagepay_arg
				* @var int    $form_ID 		Form ID
				*/
				do_action( CFSPZW_PREFIX . 'sagepay_arg', $sagepay_arg );

				$post_values = "";
				foreach( $sagepay_arg as $key => $value ) {
					$post_values .= "$key=" . $value . "&";
				}
				$post_values = rtrim( $post_values, "& " );

				// Set string in block size
				$datapadded = $this->pkcs5_pad( trim( $post_values ),16 );

				$cryptpadded = "@" . $this->encryptFieldData( $datapadded, $encryption_password );

				$array = array(
					'VPSProtocol'		=> '3.00',
					'TxType'			=> $transaction_type,
					'Vendor'			=> $vendorName,
					'Crypt' 			=> $cryptpadded,
				);

				$sagepay_arg_array = array();
				foreach ($array as $key => $value) {
					$sagepay_arg_array[] = '<input type="hidden" name="'.esc_attr( $key ).'" value="'.esc_attr( $value ).'" />';
				}

				$secure_form = '<form id="sagepay-payment-form-'.$form_ID.'" action="'.$sagepay_gateway_url.'" method="post" name="sagepay_direct_3dsecure_form" >' . implode('', $sagepay_arg_array) . '</form>';

				$_SESSION[ CFSPZW_META_PREFIX . '3dauth' . $form_ID ] = 'redirect';
				$_SESSION[ CFSPZW_META_PREFIX . 'secure_form' . $form_ID ] = serialize( $secure_form );

				if( !empty( $submission->uploaded_files() ) ) {

					$cf7_verify = $this->wpcf7_version();

					if ( version_compare( $cf7_verify, '5.4' ) >= 0 ) {
						$uploaded_files = $this->zw_cf7_upload_files( $submission->uploaded_files(), 'new');
					}else{
						$uploaded_files = $this->zw_cf7_upload_files( array( $submission->uploaded_files() ), 'old' );
					}

					if ( !empty( $uploaded_files ) ) {
						$_SESSION[ CFSPZW_META_PREFIX . 'form_attachment_' . $form_ID ] = serialize( $uploaded_files );
					}
				}

				$_SESSION[ CFSPZW_META_PREFIX . 'form_instance' ] = serialize( $submission );

				add_filter( 'wpcf7_skip_mail', array( $this, 'cfspzw_filter__wpcf7_skip_mail' ), 20 );

			}

			return $submission;
		}

		/**
		* Sagepay Response Display usig shortcode
		*
		* @method shortcode__sagepay_details
		*
		* @param  string
		*
		* @return string
		*/
		function shortcode__sagepay_details() {

			$form_ID = (int)( isset( $_REQUEST['form'] ) ?  sanitize_text_field( $_REQUEST['form'] ) : '' );

			if (
				   isset( $_REQUEST['crypt'] )
				&& !empty( $_REQUEST['crypt'] )
				&& !empty( $form_ID )
			)
			{

				if ( empty( sanitize_text_field( $_REQUEST['crypt'] ) ) )
				return '<p style="color: #f00">' . __( 'Something goes wrong! Please try again.', 'accept-sagepay-payments-using-contact-form-7' ) . '</p>';

				$form_ID =  sanitize_text_field( $_REQUEST['form'] );

				$mode            = sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'status', true ) );
				$sandbox_encryption_password     = sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'sandbox_encryption_password', true ) );
				$live_encryption_password     = sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'live_encryption_password', true ) );

				$encryption_password = ( !empty( $mode === 'sandbox') ? $sandbox_encryption_password : $live_encryption_password );

				$fetch_result = $this->decryptFieldData( sanitize_text_field( $_REQUEST['crypt'] ), $encryption_password );
				wp_parse_str($fetch_result, $output);

				$currency   = sanitize_text_field( get_post_meta( $form_ID, CFSPZW_META_PREFIX . 'currency', true ) );

				if (
					( $output['Status'] == 'OK' )
				) {
					echo '<table class="cfspzw-transaction-details" align="center">' .
						'<tr>'.
							'<th align="left">' . __( 'Transaction Amount :', 'accept-sagepay-payments-using-contact-form-7' ) . '</th>'.
							'<td align="left">' . $output['Amount'] . ' ' . $currency . '</td>'.
						'</tr>' .
						'<tr>'.
							'<th align="left">' . __( 'Payment Status :', 'accept-sagepay-payments-using-contact-form-7' ) . '</th>'.
							'<td align="left">' . substr( $output['StatusDetail'], 7) . '</td>'.
						'</tr>' .
						'<tr>'.
							'<th align="left">' . __( 'Transaction Id :', 'accept-sagepay-payments-using-contact-form-7' ) . '</th>'.
							'<td align="left">' . trim( $output['VPSTxId'], '{}') . '</td>'.
						'</tr>' .
						'<tr>'.
							'<th align="left">' . __( 'Invoice ID :', 'accept-sagepay-payments-using-contact-form-7' ) . '</th>'.
							'<td align="left">' . $output['VendorTxCode'] . '</td>'.
						'</tr>' .
					'</table>';

				}else{
					echo '<table class="cfspzw-transaction-details" align="center">' .
					'<tr>'.
						'<th align="left">' . __( 'Response :', 'accept-sagepay-payments-using-contact-form-7' ) . '</th>'.
						'<td align="left" style="color: #f00">' . $output['StatusDetail'] . '</td>'.
					'</tr>' .
				'</table>';
				}

			}

			return ob_get_clean();

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


		/**
		* Filter: Modify the contact form 7 response.
		*
		* @method filter__cfspzw_wpcf7_ajax_json_echo
		*
		* @param  array $response
		* @param  array $result
		*
		* @return array
		*/
		function filter__cfspzw_wpcf7_ajax_json_echo( $response, $result ) {

			$cf7_verify = $this->wpcf7_version();

			if (
				   array_key_exists( 'contact_form_id' , $result )
				&& array_key_exists( 'status' , $result )
				&& !empty( $result[ 'contact_form_id' ] )
				&& !empty( $_SESSION[ CFSPZW_META_PREFIX . '3dauth' . $result[ 'contact_form_id' ] ]  )
				&& !empty( $_SESSION[ CFSPZW_META_PREFIX . 'secure_form' .$result[ 'contact_form_id' ]] )
				&& $_SESSION[ CFSPZW_META_PREFIX . '3dauth' . $result[ 'contact_form_id' ] ]  == 'redirect'
			) {
				$response["status"] = "mail_sent";
				$response["redirect_form"] = unserialize( $_SESSION[ CFSPZW_META_PREFIX . 'secure_form' . $result[ 'contact_form_id' ] ] );
				$response["message"] = __( 'Please wait you are redirecting to sagepay..!', 'accept-sagepay-payments-using-contact-form-7');
				unset( $_SESSION[ CFSPZW_META_PREFIX . '3dauth' . $result[ 'contact_form_id' ] ] );
				unset( $_SESSION[ CFSPZW_META_PREFIX . 'secure_form' . $result[ 'contact_form_id' ] ] );
			}

			if (
				   array_key_exists( 'contact_form_id' , $result )
				&& array_key_exists( 'status' , $result )
				&& !empty( $result[ 'contact_form_id' ] )
				&& !empty( $_SESSION[ CFSPZW_META_PREFIX . 'sagepay_configured_error' . $result[ 'contact_form_id' ] ] )
				&& $result[ 'status' ] == 'mail_sent'
			) {

				$response[ 'message' ] = $_SESSION[ CFSPZW_META_PREFIX . 'sagepay_configured_error' . $result[ 'contact_form_id' ] ];
				$response[ 'status' ] = 'mail_failed';
				unset( $_SESSION[ CFSPZW_META_PREFIX . 'sagepay_configured_error' . $result[ 'contact_form_id' ] ] );
			}


			if (
				   array_key_exists( 'contact_form_id' , $result )
				&& array_key_exists( 'status' , $result )
				&& !empty( $result[ 'contact_form_id' ] )
				&& !empty( $_SESSION[ CFSPZW_META_PREFIX . 'sagepay_fields_error' . $result[ 'contact_form_id' ] ] )
				&& $result[ 'status' ] == 'mail_sent'
			) {
				$response[ 'message' ] = __('One or more fields have an error. Please check and try again.', CFSPZW_PREFIX);
				$response[ 'status' ] = 'validation_failed';
				$fields_msg = array();

				foreach ($_SESSION[ CFSPZW_META_PREFIX . 'sagepay_fields_error' . $result[ 'contact_form_id' ] ] as $value) {
					$field_error_message['into'] = 'span.wpcf7-form-control-wrap.'.$value;
					if( $value == 'amount' ){
						$field_error_message['message'] = __( 'Please Enter Amount value or Value in Numeric.', 'accept-sagepay-payments-using-contact-form-7');
					}else{
						$field_error_message['message'] = __('The field is required.', 'accept-sagepay-payments-using-contact-form-7');
					}
					$fields_msg[] = $field_error_message;
				}

				if ( version_compare( $cf7_verify, '5.2' ) >= 0 ) {
					$response[ 'invalid_fields' ] = $fields_msg;
				} else {
					$response[ 'invalidFields' ] = $fields_msg;
				}

				unset( $_SESSION[ CFSPZW_META_PREFIX . 'sagepay_fields_error' . $result[ 'contact_form_id' ] ] );
			}


			if (
				array_key_exists( 'contact_form_id', $result )
				&& array_key_exists( 'status', $result )
				&& !empty( $result[ 'contact_form_id' ] )
				&& !empty( $_SESSION[ CFSPZW_META_PREFIX . 'amount_error' . $result[ 'contact_form_id' ] ] )
				&& $result[ 'status' ] == 'mail_sent'
			) {
				$amount  = sanitize_text_field( get_post_meta( $result[ 'contact_form_id' ], CFSPZW_META_PREFIX . 'amount', true ) );

				$response[ 'message' ] = __('Please Enter Amount value or Value in Numeric.', 'accept-sagepay-payments-using-contact-form-7');
				$response[ 'status' ] = 'validation_failed';

				if ( version_compare( $cf7_verify, '5.2' ) >= 0 ) {
					$response[ 'invalid_fields' ] = array(
													array(
													'into'=>'span.wpcf7-form-control-wrap.'.$amount,
													'message'=> $_SESSION[ CFSPZW_META_PREFIX . 'amount_error' . $result[ 'contact_form_id' ] ] ));
				} else {
					$response[ 'invalidFields' ] = array(
													array(
													'into'=>'span.wpcf7-form-control-wrap.'.$amount,
													'message'=> $_SESSION[ CFSPZW_META_PREFIX . 'amount_error' . $result[ 'contact_form_id' ] ] ));
				}


				unset( $_SESSION[ CFSPZW_META_PREFIX . 'amount_error' . $result[ 'contact_form_id' ] ] );
			}

			return $response;
		}


		/**
		* Filter: Skip email when sagepay enable.
		*
		* @method filter__wpcf7_skip_mail
		*
		* @param  bool $bool
		*
		* @return bool
		*/
		function cfspzw_filter__wpcf7_skip_mail( $bool ) {
			return true;
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


		/**
		 * - Render CF7 Shortcode on front end.
		 *
		 * @method wpcf7_sagepay_country_form_tag_handler
		 *
		 * @param $tag
		 *
		 * @return html
		 */

		function wpcf7_sagepay_country_form_tag_handler( $tag ) {

			if ( empty( $tag->name ) ) {
				return '';
			}

			$validation_error = wpcf7_get_validation_error( $tag->name );

			$class = wpcf7_form_controls_class( $tag->type, 'wpcf7-text' );

			if ( in_array( $tag->basetype, array( 'email', 'url', 'tel' ) ) ) {
				$class .= ' wpcf7-validates-as-' . $tag->basetype;
			}

			if ( $validation_error ) {
				$class .= ' wpcf7-not-valid';
			}

			$atts = array();

			if ( $tag->is_required() ) {
				$atts['aria-required'] = 'true';
			}

			$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

			$atts['value'] = 1;

			$atts['type'] = 'hidden';
			$atts['name'] = $tag->name;
			$atts = wpcf7_format_atts( $atts );

			$form_instance = WPCF7_ContactForm::get_current();
			$form_id = $form_instance->id();

			$use_sagepay	=	sanitize_text_field( get_post_meta( $form_id, CFSPZW_META_PREFIX . 'use_sagepay', true ) );

			if ( empty( $use_sagepay ) ) {
				return;
			}

			if ( !empty( $this->_validate_fields( $form_id ) ) )
				return $this->_validate_fields( $form_id );

			wp_enqueue_style( CFSPZW_PREFIX . '_select2' );
			wp_enqueue_script( CFSPZW_PREFIX . '_select2' );

			$value = (string) reset( $tag->values );

			$found = 0;
			$html = '';

			ob_start();

			if ( $contact_form = wpcf7_get_current_contact_form() ) {
				$form_tags = $contact_form->scan_form_tags();

				foreach ( $form_tags as $k => $v ) {

					if ( $v['type'] == $tag->type ) {
						$found++;
					}

					if ( $v['name'] == $tag->name ) {
						if ( $found <= 1 ) {
							echo '<span class="select-country wpcf7-form-control-wrap '.sanitize_html_class( $tag->name ).'">
								<select name="' . $tag->name . '" class="wpcf7-form-control sagepay-country">
									<option value="">Select Country</option>';
									echo $this->get_country();
							echo '</select></span>';
						}
						break;
					}
				}
			}

			return ob_get_clean();
		}


		/**
		 *
		 * @method Get Country
		 *
		 * @param array
		 *
		 * @return html
		 */
		function get_country(){

			$country_lists	= array( 'AF' => 'Afghanistan',
									'AX' => 'Aland Islands',
									'AL' => 'Albania',
									'DZ' => 'Algeria',
									'AS' => 'American Samoa',
									'AD' => 'Andorra',
									'AO' => 'Angola',
									'AI' => 'Anguilla',
									'AQ' => 'Antarctica',
									'AG' => 'Antigua and Barbuda',
									'AR' => 'Argentina',
									'AM' => 'Armenia',
									'AW' => 'Aruba',
									'AU' => 'Australia',
									'AT' => 'Austria',
									'AZ' => 'Azerbaijan',
									'BS' => 'Bahamas',
									'BH' => 'Bahrain',
									'BD' => 'Bangladesh',
									'BB' => 'Barbados',
									'BY' => 'Belarus',
									'BE' => 'Belgium',
									'BZ' => 'Belize',
									'BJ' => 'Benin',
									'BM' => 'Bermuda',
									'BT' => 'Bhutan',
									'BO' => 'Bolivia',
									'BQ' => 'Bonaire, Saint Eustatius and Saba',
									'BA' => 'Bosnia and Herzegovina',
									'BW' => 'Botswana',
									'BV' => 'Bouvet Island',
									'BR' => 'Brazil',
									'IO' => 'British Indian Ocean Territory',
									'VG' => 'British Virgin Islands',
									'BN' => 'Brunei',
									'BG' => 'Bulgaria',
									'BF' => 'Burkina Faso',
									'BI' => 'Burundi',
									'KH' => 'Cambodia',
									'CM' => 'Cameroon',
									'CA' => 'Canada',
									'CV' => 'Cape Verde',
									'KY' => 'Cayman Islands',
									'CF' => 'Central African Republic',
									'TD' => 'Chad',
									'CL' => 'Chile',
									'CN' => 'China',
									'CX' => 'Christmas Island',
									'CC' => 'Cocos Islands',
									'CO' => 'Colombia',
									'KM' => 'Comoros',
									'CK' => 'Cook Islands',
									'CR' => 'Costa Rica',
									'HR' => 'Croatia',
									'CU' => 'Cuba',
									'CW' => 'Curacao',
									'CY' => 'Cyprus',
									'CZ' => 'Czech Republic',
									'CD' => 'Democratic Republic of the Congo',
									'DK' => 'Denmark',
									'DJ' => 'Djibouti',
									'DM' => 'Dominica',
									'DO' => 'Dominican Republic',
									'TL' => 'East Timor',
									'EC' => 'Ecuador',
									'EG' => 'Egypt',
									'SV' => 'El Salvador',
									'GQ' => 'Equatorial Guinea',
									'ER' => 'Eritrea',
									'EE' => 'Estonia',
									'ET' => 'Ethiopia',
									'FK' => 'Falkland Islands',
									'FO' => 'Faroe Islands',
									'FJ' => 'Fiji',
									'FI' => 'Finland',
									'FR' => 'France',
									'GF' => 'French Guiana',
									'PF' => 'French Polynesia',
									'TF' => 'French Southern Territories',
									'GA' => 'Gabon',
									'GM' => 'Gambia',
									'GE' => 'Georgia',
									'DE' => 'Germany',
									'GH' => 'Ghana',
									'GI' => 'Gibraltar',
									'GR' => 'Greece',
									'GL' => 'Greenland',
									'GD' => 'Grenada',
									'GP' => 'Guadeloupe',
									'GU' => 'Guam',
									'GT' => 'Guatemala',
									'GG' => 'Guernsey',
									'GN' => 'Guinea',
									'GW' => 'Guinea-Bissau',
									'GY' => 'Guyana',
									'HT' => 'Haiti',
									'HM' => 'Heard Island and McDonald Islands',
									'HN' => 'Honduras',
									'HK' => 'Hong Kong',
									'HU' => 'Hungary',
									'IS' => 'Iceland',
									'IN' => 'India',
									'ID' => 'Indonesia',
									'IR' => 'Iran',
									'IQ' => 'Iraq',
									'IE' => 'Ireland',
									'IM' => 'Isle of Man',
									'IL' => 'Israel',
									'IT' => 'Italy',
									'CI' => 'Ivory Coast',
									'JM' => 'Jamaica',
									'JP' => 'Japan',
									'JE' => 'Jersey',
									'JO' => 'Jordan',
									'KZ' => 'Kazakhstan',
									'KE' => 'Kenya',
									'KI' => 'Kiribati',
									'XK' => 'Kosovo',
									'KW' => 'Kuwait',
									'KG' => 'Kyrgyzstan',
									'LA' => 'Laos',
									'LV' => 'Latvia',
									'LB' => 'Lebanon',
									'LS' => 'Lesotho',
									'LR' => 'Liberia',
									'LY' => 'Libya',
									'LI' => 'Liechtenstein',
									'LT' => 'Lithuania',
									'LU' => 'Luxembourg',
									'MO' => 'Macao',
									'MK' => 'Macedonia',
									'MG' => 'Madagascar',
									'MW' => 'Malawi',
									'MY' => 'Malaysia',
									'MV' => 'Maldives',
									'ML' => 'Mali',
									'MT' => 'Malta',
									'MH' => 'Marshall Islands',
									'MQ' => 'Martinique',
									'MR' => 'Mauritania',
									'MU' => 'Mauritius',
									'YT' => 'Mayotte',
									'MX' => 'Mexico',
									'FM' => 'Micronesia',
									'MD' => 'Moldova',
									'MC' => 'Monaco',
									'MN' => 'Mongolia',
									'ME' => 'Montenegro',
									'MS' => 'Montserrat',
									'MA' => 'Morocco',
									'MZ' => 'Mozambique',
									'MM' => 'Myanmar',
									'NA' => 'Namibia',
									'NR' => 'Nauru',
									'NP' => 'Nepal',
									'NL' => 'Netherlands',
									'NC' => 'New Caledonia',
									'NZ' => 'New Zealand',
									'NI' => 'Nicaragua',
									'NE' => 'Niger',
									'NG' => 'Nigeria',
									'NU' => 'Niue',
									'NF' => 'Norfolk Island',
									'KP' => 'North Korea',
									'MP' => 'Northern Mariana Islands',
									'NO' => 'Norway',
									'OM' => 'Oman',
									'PK' => 'Pakistan',
									'PW' => 'Palau',
									'PS' => 'Palestinian Territory',
									'PA' => 'Panama',
									'PG' => 'Papua New Guinea',
									'PY' => 'Paraguay',
									'PE' => 'Peru',
									'PH' => 'Philippines',
									'PN' => 'Pitcairn',
									'PL' => 'Poland',
									'PT' => 'Portugal',
									'PR' => 'Puerto Rico',
									'QA' => 'Qatar',
									'CG' => 'Republic of the Congo',
									'RE' => 'Reunion',
									'RO' => 'Romania',
									'RU' => 'Russia',
									'RW' => 'Rwanda',
									'BL' => 'Saint Barthelemy',
									'SH' => 'Saint Helena',
									'KN' => 'Saint Kitts and Nevis',
									'LC' => 'Saint Lucia',
									'MF' => 'Saint Martin',
									'PM' => 'Saint Pierre and Miquelon',
									'VC' => 'Saint Vincent and the Grenadines',
									'WS' => 'Samoa',
									'SM' => 'San Marino',
									'ST' => 'Sao Tome and Principe',
									'SA' => 'Saudi Arabia',
									'SN' => 'Senegal',
									'RS' => 'Serbia',
									'SC' => 'Seychelles',
									'SL' => 'Sierra Leone',
									'SG' => 'Singapore',
									'SX' => 'Sint Maarten',
									'SK' => 'Slovakia',
									'SI' => 'Slovenia',
									'SB' => 'Solomon Islands',
									'SO' => 'Somalia',
									'ZA' => 'South Africa',
									'GS' => 'South Georgia and the South Sandwich Islands',
									'KR' => 'South Korea',
									'SS' => 'South Sudan',
									'ES' => 'Spain',
									'LK' => 'Sri Lanka',
									'SD' => 'Sudan',
									'SR' => 'Suriname',
									'SJ' => 'Svalbard and Jan Mayen',
									'SZ' => 'Swaziland',
									'SE' => 'Sweden',
									'CH' => 'Switzerland',
									'SY' => 'Syria',
									'TW' => 'Taiwan',
									'TJ' => 'Tajikistan',
									'TZ' => 'Tanzania',
									'TH' => 'Thailand',
									'TG' => 'Togo',
									'TK' => 'Tokelau',
									'TO' => 'Tonga',
									'TT' => 'Trinidad and Tobago',
									'TN' => 'Tunisia',
									'TR' => 'Turkey',
									'TM' => 'Turkmenistan',
									'TC' => 'Turks and Caicos Islands',
									'TV' => 'Tuvalu',
									'VI' => 'U.S. Virgin Islands',
									'UG' => 'Uganda',
									'UA' => 'Ukraine',
									'AE' => 'United Arab Emirates',
									'GB' => 'United Kingdom',
									'US' => 'United States',
									'UM' => 'United States Minor Outlying Islands',
									'UY' => 'Uruguay',
									'UZ' => 'Uzbekistan',
									'VU' => 'Vanuatu',
									'VA' => 'Vatican',
									'VE' => 'Venezuela',
									'VN' => 'Vietnam',
									'WF' => 'Wallis and Futuna',
									'EH' => 'Western Sahara',
									'YE' => 'Yemen',
									'ZM' => 'Zambia',
									'ZW' => 'Zimbabwe',
							);

			$country_lists = apply_filters( CFSPZW_META_PREFIX .'country', $country_lists );

			$country_list_html = '';

			foreach ($country_lists as $iso => $country_name) {
				$country_list_html .='<option value="'.$iso.'">'.$country_name.'</option>';
			}
			return $country_list_html;

		}


		/**
		 * Function: _validate_fields
		 *
		 * @method _validate_fields
		 *
		 * @param int $form_id
		 *
		 * @return string
		 */
		function _validate_fields( $form_id ) {

			$use_sagepay 					= sanitize_text_field( get_post_meta( $form_id, CFSPZW_META_PREFIX . 'use_sagepay', true ) );
			$mode							= sanitize_text_field( get_post_meta( $form_id, CFSPZW_META_PREFIX . 'status', true ) );
			$sandbox_vendorName				= sanitize_text_field( get_post_meta( $form_id, CFSPZW_META_PREFIX . 'sandbox_vendor_name', true ) );
			$sandbox_encryption_password	= sanitize_text_field( get_post_meta( $form_id, CFSPZW_META_PREFIX . 'sandbox_encryption_password', true ) );
			$live_vendorName				= sanitize_text_field( get_post_meta( $form_id, CFSPZW_META_PREFIX . 'live_vendor_name', true ) );
			$live_encryption_password		= sanitize_text_field( get_post_meta( $form_id, CFSPZW_META_PREFIX . 'live_encryption_password', true ) );

			$vendorName				= ( !empty( $mode === 'sandbox' ) ? $sandbox_vendorName : $live_vendorName );
			$encryption_password 	= ( !empty( $mode === 'sandbox' ) ? $sandbox_encryption_password : $live_encryption_password );

			if ( !empty( $use_sagepay ) ) {

				if( empty( $vendorName ) || empty( $encryption_password ) )
					return __( 'Please enter VendorName  or Encryption Password.', CFSPZW_PREFIX );
			}

			return false;
		}


		/**
		* Function: getUserIpAddr
		*
		* @method getUserIpAddr
		*
		* @return string
		*/
		function getUserIpAddr() {
			$ip = false;

			if ( ! empty( $_SERVER['HTTP_X_REAL_IP'] ) ) {
				$ip = filter_var( $_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP );
			} elseif ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
				// Check ip from share internet.
				$ip = filter_var( $_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP );
			} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
				$ips = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
				if ( is_array( $ips ) ) {
					$ip = filter_var( $ips[0], FILTER_VALIDATE_IP );
				}
			} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
				$ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP );
			}

			$ip			= false !== $ip ? $ip : '127.0.0.1';
			$ip_array	= explode( ',', $ip );
			$ip_array	= array_map( 'trim', $ip_array );

			if($ip_array[0] == '::1' || $ip_array[0] == '127.0.0.1'){
				$ipser = array('http://ipv4.icanhazip.com','http://v4.ident.me','http://bot.whatismyipaddress.com');
				shuffle($ipser);
				$ipservices = array_slice($ipser, 0,1);
				$ret = wp_remote_get($ipservices[0]);
				if(!is_wp_error($ret)){
					if (isset($ret['body'])) {
						return sanitize_text_field( $ret['body'] );
					}
				}
			}

			return sanitize_text_field( apply_filters( 'cfspzw_get_ip', $ip_array[0] ) );
		}

		/**
		* Get the attachment upload directory from plugin.
		*
		* @method zw_wpcf7_upload_tmp_dir
		*
		* @return string
		*/
		function zw_wpcf7_upload_tmp_dir() {

			$upload = wp_upload_dir();
			$upload_dir = $upload['basedir'];
			$cfspzw_upload_dir = $upload_dir . '/cfspzw-uploaded-files';

			if ( !is_dir( $cfspzw_upload_dir ) ) {
				mkdir( $cfspzw_upload_dir, 0755 );
			}

			return $cfspzw_upload_dir;
		}


		/**
		* Copy the attachment into the plugin folder.
		*
		* @method zw_cf7_upload_files
		*
		* @param  array $attachment
		*
		* @uses $this->zw_wpcf7_upload_tmp_dir(), WPCF7::wpcf7_maybe_add_random_dir()
		*
		* @return array
		*/
		function zw_cf7_upload_files( $attachment, $version ) {
			if( empty( $attachment ) )
			return;

			$new_attachment = $attachment;

			foreach ( $attachment as $key => $value ) {
				$tmp_name = $value;
				$uploads_dir = wpcf7_maybe_add_random_dir( $this->zw_wpcf7_upload_tmp_dir() );
				foreach ($tmp_name as $newkey => $file_path) {
					$get_file_name = explode( '/', $file_path );
					$new_uploaded_file = path_join( $uploads_dir, end( $get_file_name ) );
					if ( copy( $file_path, $new_uploaded_file ) ) {
						chmod( $new_uploaded_file, 0755 );
						if($version == 'old'){
							$new_attachment_file[$newkey] = $new_uploaded_file;
						}else{
							$new_attachment_file[$key] = $new_uploaded_file;
						}
					}
				}
			}
			return $new_attachment_file;
		}


		function pkcs5_pad($text, $blocksize)
		{
			$pad = $blocksize - (strlen($text) % $blocksize);
			return $text . str_repeat(chr($pad), $pad);
		}

		/**
		* DecryptFieldData received form sagepay
		*
		* @method decryptFieldData
		*
		* @param  $str $key
		*
		*
		* @return string
		*/
		function decryptFieldData( $input, $encryption_password )
		{
			$strIn = substr($input, 1);
			$strIn = hex2bin($strIn);
			$dec = openssl_decrypt($strIn, 'AES-128-CBC', $encryption_password, OPENSSL_RAW_DATA, $encryption_password);
			return $dec;
		}

		/**
		* encryptFieldData before redirect to sagepay
		*
		* @method encryptFieldData
		*
		* @param  $str $key
		*
		*
		* @return string
		*/
		function encryptFieldData( $input, $encryption_password )
		{
			$cipher = openssl_encrypt($input, 'AES-128-CBC', $encryption_password, OPENSSL_RAW_DATA, $encryption_password);
			$enc = bin2hex($cipher);
			return $enc;
		}

		/**
		 * Get current conatct from 7 version.
		 *
		 * @method wpcf7_version
		 *
		 * @return string
		 */
		function wpcf7_version() {

			$wpcf7_path = plugin_dir_path( CFSPZW_DIR ) . 'contact-form-7/wp-contact-form-7.php';

			if( ! function_exists('get_plugin_data') ){
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			$plugin_data = get_plugin_data( $wpcf7_path );

			return $plugin_data['Version'];
		}

	}

	add_action( 'plugins_loaded', function() {
		CFSPZW()->lib = new CFSPZW_Lib;
	} );

}
