<?php
/**
* CFSPZW_Admin_Action Class
*
* Handles the admin functionality.
*
* @package WordPress
* @subpackage Accept Sagepay Payments Using Contact Form 7
* @since 1.0
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'CFSPZW_Admin_Action' ) ){

	/**
	*  The CFSPZW_Admin_Action Class
	*/
	class CFSPZW_Admin_Action {

		function __construct()  {

			add_action( 'init',				array( $this, 'action__cfspzw_init' ) );
			add_action( 'init',				array( $this, 'action__cfspzw_init_99' ), 99 );
			add_action( 'add_meta_boxes',	array( $this, 'action__cfspzw_add_meta_boxes' ) );

			// Create import functionality page
			add_action( 'admin_menu', array( $this,'action__cfspzw_admin_menu' ) );
			add_action( 'admin_init', array( $this,'action__cfspzw_admin_init' ) );

			// Save settings of contact form 7 admin
			add_action( 'wpcf7_save_contact_form',	array( $this, 'action__cfspzw_wpcf7_save_contact_form' ), 20, 2 );

			add_action( 'manage_'.CFSPZW_POST_TYPE.'_posts_custom_column',	array( $this, 'action__manage_cfspzw_data_posts_custom_column' ), 10, 2 );

			add_action( 'pre_get_posts',			array( $this, 'action__cfspzw_pre_get_posts' ) );
			add_action( 'restrict_manage_posts',	array( $this, 'action__cfspzw_restrict_manage_posts' ) );
			add_action( 'parse_query',				array( $this, 'action__cfspzw_parse_query' ) );

			add_action( CFSPZW_PREFIX . '/postbox', array( $this, 'action__cfspzw_postbox' ) );

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
		* - Register neccessary assets for backend.
		*
		* @method action__cfspzw_init
		*/

		function action__cfspzw_init() {
			wp_register_style( CFSPZW_PREFIX . '_admin_css', CFSPZW_URL . 'assets/css/admin.min.css', array(), CFSPZW_VERSION );
			wp_register_script( CFSPZW_PREFIX . '_admin_js', CFSPZW_URL . 'assets/js/admin.min.js', array( 'jquery-core' ), CFSPZW_VERSION );

			wp_register_style( 'select2', CFSPZW_URL . 'assets/css/select2.min.css', array(), CFSPZW_VERSION );
			wp_register_script( 'select2', CFSPZW_URL . 'assets/js/select2.min.js', array( 'jquery-core' ), CFSPZW_VERSION );
		}

		/**
		* Action: init 99
		*
		* - Used to perform the CSV export functionality.
		*
		*/
		function action__cfspzw_init_99() {
			if (
				   isset( $_REQUEST['export_csv'] )
				&& isset( $_REQUEST['form-id'] )
				&& !empty( $_REQUEST['form-id'] )
				&& !empty( $_REQUEST['post_type'] == CFSPZW_POST_TYPE )
			) {
				$form_id = $_REQUEST['form-id'];

				if ( 'all' == $form_id ) {
					add_action( 'admin_notices', array( $this, 'action__cfspzw_admin_notices_export' ) );
					return;
				}

				$args = array(
					'post_type' => CFSPZW_POST_TYPE,
					'posts_per_page' => -1
				);

				$exported_data = get_posts( $args );


				if ( empty( $exported_data ) )
					return;

				/** CSV Export**/
				$filename = 'cfspzw-' . $form_id . '-' . time() . '.csv';

				$header_row = array(
					'_form_id'				=> 'Form ID/Name',
					'_email'				=> 'Email Address',
					'_user_name'			=> 'User Name',
					'_transaction_id'		=> 'Transaction ID',
					'_invoice_no'			=> 'Invoice ID',
					'_amount'				=> 'Amount',
					'_quantity'				=> 'Quantity',
					'_total'				=> 'Total',
					'_currency'				=> 'Currency Code',
					'_submit_time'			=> 'Submit Time',
					'_request_ip'			=> 'Request Ip',
					'_transaction_status'	=> 'Transaction status',
					'_form_data'			=> 'Form Data',
					'_transaction_response'	=> 'Transaction Response',
					'_attachment'			=> 'Attachement',
				);

				$data_rows = array();

				if ( !empty( $exported_data ) ) {
					foreach ( $exported_data as $entry ) {

						$row = array();

						if ( !empty( $header_row ) ) {
							foreach ( $header_row as $key => $value ) {

								if (
									   $key != '_transaction_status'
									&& $key != '_submit_time'
									&& $key != '_transaction_id'
									&& $key != '_quantity'
								) {

									$row[$key] = __(
										(
											(
												'_form_id' == $key
												&& !empty( get_the_title( get_post_meta( $entry->ID, $key, true ) ) )
											)
											? get_the_title( get_post_meta( $entry->ID, $key, true ) )
											: get_post_meta( $entry->ID, $key, true )
										)
									);

								}else if ( '_submit_time' == $key ) {
									$row[$key] = __( get_the_date( 'd, M Y H:i:s', $entry->ID ) );
								}else if ( '_transaction_id' == $key ) {
									$row[$key] = trim( get_post_meta( $entry->ID, $key, true ), '{}');
								}else if ( '_quantity' == $key ) {
									$quantity = get_post_meta( $entry->ID, $key, true );
									if($quantity > 0){
										$row[$key] = $quantity;
									}else{
										$row[$key] = 1;
									}
								}else if ( '_transaction_status' == $key ) {
									$row[$key] = get_post_meta( $entry->ID, $key, true );
								}

								if( $key == '_form_data' ){

									$row[$key] = get_post_meta( $entry->ID, '_form_data', true );
								}
								if( $key == '_transaction_response' ){

									$row[$key] = get_post_meta( $entry->ID, '_transaction_response', true );
								}
								if( $key == '_attachment' ){

									$row[$key] = get_post_meta( $entry->ID, '_attachment', true );
								}
							}
						}

						/* form_data*/
						$data = unserialize( get_post_meta( $entry->ID, '_form_data', true ) );

						$hide_data = apply_filters( 'accept-sagepay-payments-using-contact-form-7' . '/hide-display', array( '_wpcf7', '_wpcf7_version', '_wpcf7_locale', '_wpcf7_unit_tag', '_wpcf7_container_post' ) );
						foreach ( $hide_data as $key => $value ) {
							if ( array_key_exists( $value, $data ) ) {
								unset( $data[$value] );
							}
						}

						if ( !empty( $data ) ) {
							foreach ( $data as $key => $value ) {
								if ( strpos( $key, 'sagepay-' ) === false ) {

									if ( !in_array( $key, $header_row ) ) {
										$header_row[$key] = $key;
									}

									$row[$key] = ( is_array( $value ) ? implode( ', ', $value ) : __( $value ) );

								}
							}
						}

						$data_rows[] = $row;

					}
				}

				ob_start();

				$fh = @fopen( 'php://output', 'w' );
				fprintf( $fh, chr(0xEF) . chr(0xBB) . chr(0xBF) );
				header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
				header( 'Content-Description: File Transfer' );
				header( 'Content-type: text/csv' );
				header( "Content-Disposition: attachment; filename={$filename}" );
				header( 'Expires: 0' );
				header( 'Pragma: public' );
				fputcsv( $fh, $header_row );
				foreach ( $data_rows as $data_row ) {
					fputcsv( $fh, $data_row );
				}
				fclose( $fh );

				ob_end_flush();
				die();

			}
		}

		/**
		* Action: add_meta_boxes
		*
		* - Add mes boxes for the CPT "cfspzw_data"
		*/

		function action__cfspzw_add_meta_boxes() {
			add_meta_box( 'cfspzw-data', __( 'From Data', 'accept-sagepay-payments-using-contact-form-7' ), array( $this, 'cfspzw_show_from_data' ), CFSPZW_POST_TYPE, 'normal', 'high' );
			add_meta_box( 'cfspzw-help', __( 'Do you need help for configuration?', 'accept-sagepay-payments-using-contact-form-7' ), array( $this, 'cfspzw_show_help_data' ), CFSPZW_POST_TYPE, 'side', 'high' );
		}


		/**
		 * Action: admin_menu
		 *
		 * - Add Sagepay import data menu
		 */
		function action__cfspzw_admin_menu() {
			add_submenu_page(
				'wpcf7',
				'Import Sagepay data',
				'Import Sagepay data',
				'manage_options',
				'cfspzw-import',
				array( $this, 'cfspzw_import_submenu_page_callback')
			);
		}


		/**
		 * Action: admin_init
		 *
		 * - Import csv logic
		 */
		function action__cfspzw_admin_init(){
			// checking on form submit
			if( array_key_exists('cfspzw-import-plugin-submit', $_REQUEST ) && $_REQUEST['cfspzw-import-plugin-submit'] != '' ) {
				$error = array();
				// checking the nonce first
				if( $_REQUEST['_wpnonce_cfspzw'] != '' && isset( $_REQUEST['_wpnonce_cfspzw'] ) ) {
					if( ! wp_verify_nonce( $_REQUEST['_wpnonce_cfspzw'], 'cfspzw_import' ) ){
						add_action( 'admin_notices', array( $this, 'action__admin_notices_import_nonce_issue' ) );
						return;
					}
				}

				//checking filet type of uploaded file
				if( $_FILES['cfspzw_importcsv']['type'] != '' ) {
					$fileName		= $_FILES['cfspzw_importcsv']['name'];
					$fileArray		= explode ( '.', $fileName );
					$fileExtension	= end ( $fileArray );
					$ext			= strtolower( $fileExtension );
					$type			= $_FILES['cfspzw_importcsv']['type'];
					$tmpName		= $_FILES['cfspzw_importcsv']['tmp_name'];

					// check the file is a csv
					if( $ext === 'csv' ){
						if(($handle = fopen($tmpName, 'r')) !== FALSE) {
							// necessary if a large csv file
							set_time_limit(0);

							$row  = 0;
							$flag = true;

							$col_count =  count(file($tmpName, FILE_SKIP_EMPTY_LINES));

							while( ($data = fgetcsv( $handle, 10000, ',') ) !== FALSE ) {

								// Check data is blank or not
								if ( !empty( $data ) ) {

									// Skipped the first record
									if( $row == 0 && $data[3] != "Transaction ID") {
										// File Format is not belongs to our format
										add_action( 'admin_notices', array( $this, 'action__admin_notices_import_file_format' ) );
									} else {
										if( $flag === true ) {

											$form_name = 'Imported Data Form';
											if( $_REQUEST['formname'] != '' ){
												$form_name = $_REQUEST['formname'];
											}
											$cfspzw_import_contactform_id = wp_insert_post( array (
												'post_type' => 'wpcf7_contact_form',
												'post_title' => $form_name, // email/invoice_no
												'post_status' => 'publish',
												'comment_status' => 'closed',
												'ping_status' => 'closed',
											) );
											add_post_meta($cfspzw_import_contactform_id,'cfspzw_use_sagepay','1',true);
											$flag = false;
										}

										if( $row > 0 ) {

											//Finally inserting the data
											$form_name		= $data[0];
											$email			= $data[1];
											$user_name		= $data[2];
											$txn_id			= $data[3];
											$invoice_no		= $data[4];
											$amount_val		= $data[5];
											$quanity_val	= $data[6];
											$paidAmount		= $data[7];
											$paidCurrency	= $data[8];
											$submitTime		= $data[9];
											$ip_address		= $data[10];
											$payment_status	= $data[11];
											$stored_data	= $data[12];
											$trans_respose	= $data[13];
											$attachment		= $data[14];

											try {
												//Finally inserting the data
												$cfspzw_import_post_id = wp_insert_post( array (
													'post_type' => CFSPZW_POST_TYPE,
													'post_title' => ( !empty( $email ) ? $email : $invoice_no ), // email/invoice_no
													'post_status' => 'publish',
													'comment_status' => 'closed',
													'ping_status' => 'closed',
												) );

												if ( !empty( $cfspzw_import_post_id ) ) {

													add_post_meta( $cfspzw_import_post_id, '_form_name', $form_name );
													add_post_meta( $cfspzw_import_post_id, '_form_id', $cfspzw_import_contactform_id);
													add_post_meta( $cfspzw_import_post_id, '_user_name' , $user_name );
													add_post_meta( $cfspzw_import_post_id, '_email', $email );
													add_post_meta( $cfspzw_import_post_id, '_transaction_id', $txn_id );
													add_post_meta( $cfspzw_import_post_id, '_invoice_no', $invoice_no );
													add_post_meta( $cfspzw_import_post_id, '_amount', $amount_val );
													add_post_meta( $cfspzw_import_post_id, '_quantity', $quanity_val );
													add_post_meta( $cfspzw_import_post_id, '_total', $paidAmount );
													add_post_meta( $cfspzw_import_post_id, '_request_ip', $ip_address );
													add_post_meta( $cfspzw_import_post_id, '_currency', $paidCurrency );
													add_post_meta( $cfspzw_import_post_id, '_transaction_status', $payment_status );
													add_post_meta( $cfspzw_import_post_id, '_transaction_response', $trans_respose );
													add_post_meta( $cfspzw_import_post_id, '_attachment', $attachment );
													add_post_meta( $cfspzw_import_post_id, '_form_data', $stored_data );

												}

											} catch( Exception $e ) {
												// Handele the exception and store for the support team
												$errorArray = array();
												$errorArray['row'] = $row;
												$errorArray['message'] = $e->getMessage();
												update_option('import_error', $errorArray);
												add_action( 'admin_notices', array( $this, 'action__admin_notices_import_fail' ) );
												break;
											}
										}
									}
									// increament the row
									$row++;

								}
							}

							if( $row ==  $col_count){
								//Import success message
								add_action( 'admin_notices', array( $this, 'action__admin_notices_import_done' ) );
							}

							// File Close
							fclose($handle);
						}
					} else {
						// File type error
						add_action( 'admin_notices', array( $this, 'action__admin_notices_import_file_type' ) );
					}

				} else {
					// File type error
					add_action( 'admin_notices', array( $this, 'action__admin_notices_import_file_type' ) );
				}
			}

		}

		/**
		* Action: cfspzw_wpcf7_save_contact_form
		*
		* - Save setting fields data.
		*
		* @param object $WPCF7_form
		*/
		public function action__cfspzw_wpcf7_save_contact_form( $WPCF7_form ) {

			$wpcf7 = WPCF7_ContactForm::get_current();

			if ( !empty( $wpcf7 ) ) {
				$post_id = $wpcf7->id();
			}

			$form_fields = array(
				CFSPZW_META_PREFIX . 'use_sagepay',
				CFSPZW_META_PREFIX . 'debug',
				CFSPZW_META_PREFIX . 'status',
				CFSPZW_META_PREFIX . 'sandbox_vendor_name',
				CFSPZW_META_PREFIX . 'sandbox_encryption_password',
				CFSPZW_META_PREFIX . 'live_vendor_name',
				CFSPZW_META_PREFIX . 'live_encryption_password',
				CFSPZW_META_PREFIX . 'transaction_type',
				CFSPZW_META_PREFIX . 'apply3d',
				CFSPZW_META_PREFIX . 'vendor_txcode_prefix',
				CFSPZW_META_PREFIX . 'amount',
				CFSPZW_META_PREFIX . 'customer_email',
				CFSPZW_META_PREFIX . 'quantity',
				CFSPZW_META_PREFIX . 'currency',
				CFSPZW_META_PREFIX . 'returnurl',
				CFSPZW_META_PREFIX . 'cancel_returnurl',

				CFSPZW_META_PREFIX . 'billing_firstnames',
				CFSPZW_META_PREFIX . 'billing_surname',
				CFSPZW_META_PREFIX . 'billing_address',
				CFSPZW_META_PREFIX . 'billing_city',
				CFSPZW_META_PREFIX . 'billing_state',
				CFSPZW_META_PREFIX . 'billing_zip',
				CFSPZW_META_PREFIX . 'billing_country',

				CFSPZW_META_PREFIX . 'shipping_firstnames',
				CFSPZW_META_PREFIX . 'shipping_surname',
				CFSPZW_META_PREFIX . 'shipping_address',
				CFSPZW_META_PREFIX . 'shipping_city',
				CFSPZW_META_PREFIX . 'shipping_state',
				CFSPZW_META_PREFIX . 'shipping_zip',
				CFSPZW_META_PREFIX . 'shipping_country',
			);

			/**
			* Save custom form setting fields
			*
			* @var array $form_fields
			*/

			$form_fields = apply_filters( CFSPZW_META_PREFIX . '/save_fields', $form_fields );

			if ( !empty( $form_fields ) ) {
				foreach ( $form_fields as $key ) {
					if( isset( $_REQUEST[ $key ] ) ){
						$keyval = sanitize_text_field( $_REQUEST[ $key ] );
						update_post_meta( $post_id, $key, $keyval );
					}else{
						update_post_meta( $post_id, $key, '' );
					}
				}
			}
		}

		/**
		* Action: manage_data_posts_custom_column
		*
		* @method manage_cfspzw_data_posts_custom_column
		*
		* @param  string  $column
		* @param  int     $post_id
		*
		* @return string
		*/
		function action__manage_cfspzw_data_posts_custom_column( $column, $post_id ) {
			switch ( $column ) {
				case 'user_name' :
					echo (
						!empty( get_post_meta( $post_id , '_user_name', true ) )
						? (
							(
								!empty( CFSPZW()->lib->response_status )
								&& array_key_exists( get_post_meta( $post_id , '_user_name', true ), CFSPZW()->lib->response_status)
							)
							? CFSPZW()->lib->response_status[get_post_meta( $post_id , '_user_name', true )]
							: get_post_meta( $post_id , '_user_name', true )
						)
						: ''
					);
				break;

				case 'invoice_no' :
					echo (
						!empty( get_post_meta( $post_id , '_invoice_no', true ) )
						? (
							(
								!empty( CFSPZW()->lib->response_status )
								&& array_key_exists( get_post_meta( $post_id , '_invoice_no', true ), CFSPZW()->lib->response_status)
							)
							? CFSPZW()->lib->response_status[get_post_meta( $post_id , '_invoice_no', true )]
							: get_post_meta( $post_id , '_invoice_no', true )
						)
						: ''
					);
				break;

				case 'transaction_status' :
					echo (
						!empty( get_post_meta( $post_id , '_transaction_status', true ) )
						? (
							(
								!empty( CFSPZW()->lib->response_status )
								&& array_key_exists( get_post_meta( $post_id , '_transaction_status', true ), CFSPZW()->lib->response_status)
							)
							? CFSPZW()->lib->response_status[get_post_meta( $post_id , '_transaction_status', true )]
							: get_post_meta( $post_id , '_transaction_status', true )
						)
						: ''
					);
				break;

				case 'total' :
					echo ( !empty( get_post_meta( $post_id , '_total', true ) ) ? get_post_meta( $post_id , '_total', true ) : '' );
				break;

			}
		}

		/**
		* Action: pre_get_posts
		*
		* - Used to perform order by into CPT List.
		*
		* @method action__cfspzw_pre_get_posts
		*
		* @param  object $query WP_Query
		*/
		function action__cfspzw_pre_get_posts( $query ) {

			if (
				! is_admin()
				|| !in_array ( $query->get( 'post_type' ), array( CFSPZW_POST_TYPE ) )
			)
				return;

			$orderby = $query->get( 'orderby' );

			if ( '_total' == $orderby ) {
				$query->set( 'meta_key', '_total' );
				$query->set( 'orderby', 'meta_value_num' );
			}
		}

		/**
		* Action: restrict_manage_posts
		*
		* - Used to creat filter by form and export functionality.
		*
		* @method action__cfspzw_restrict_manage_posts
		*
		* @param  string $post_type
		*/
		function action__cfspzw_restrict_manage_posts( $post_type ) {

			if ( CFSPZW_POST_TYPE != $post_type ) {
				return;
			}

			$posts = get_posts(
				array(
					'post_type'			=> 'wpcf7_contact_form',
					'post_status'		=> 'publish',
					'suppress_filters'	=> false,
					'posts_per_page'	=> -1,
					'meta_key'			=> 'cfspzw_use_sagepay',
                    'meta_value'		=> 1,
				)
			);

			if ( empty( $posts ) ) {
				return;
			}

			$selected = ( isset( $_GET['form-id'] ) ? $_GET['form-id'] : '' );

			echo '<select name="form-id" id="form-id">';
			echo '<option value="all">' . __( 'Select Forms', 'accept-sagepay-payments-using-contact-form-7' ) . '</option>';
			foreach ( $posts as $post ) {
				echo '<option value="' . $post->ID . '" ' . selected( $selected, $post->ID, false ) . '>' . $post->post_title  . '</option>';
			}
			echo '</select>';

			echo '<input type="submit" id="doaction2" name="export_csv" class="button action" value="'. __( 'Export CSV', CFSPZW_PREFIX ) . '">';

		}

		/**
		* Action: parse_query
		*
		* - Filter data by form id.
		*
		* @method action__cfspzw_parse_query
		*
		* @param  object $query WP_Query
		*/
		function action__cfspzw_parse_query( $query ) {
			if (
				! is_admin()
				|| !in_array ( $query->get( 'post_type' ), array( CFSPZW_POST_TYPE ) )
			)
				return;

			if (
				is_admin()
				&& isset( $_GET['form-id'] )
				&& 'all' != $_GET['form-id']
			) {
				$query->query_vars['meta_value']	= $_GET['form-id'];
				$query->query_vars['meta_compare']	= '=';
			}

		}

		/**
		* Action: admin_notices
		*
		* - Added use notice when trying to export without selecting the form.
		*
		* @method action__cfspzw_admin_notices_export
		*/
		function action__cfspzw_admin_notices_export() {
			echo '<div class="error">' .
				'<p>' .
					__( 'Please select Form to export.', 'accept-sagepay-payments-using-contact-form-7' ) .
				'</p>' .
			'</div>';
		}


		/**
		* Action: CFSPZW_PREFIX /postbox
		*
		* - Added metabox for the setting fields in backend.
		*
		* @method action__cfspzw_postbox
		*/
		function action__cfspzw_postbox() {

			echo '<div id="configuration-help" class="postbox">' .
				apply_filters(
					CFSPZW_META_PREFIX . '/help/postbox',
					'<h3>' . __( 'Do you need help for configuration?', 'accept-sagepay-payments-using-contact-form-7' ) . '</h3>' .
					'<p></p>' .
					'<ol>' .
						'<li><a href="'.CFSPZW_DOCUMENT.'" target="_blank">Refer the document.</a></li>' .
						'<li><a href="'.CFSPZW_SUPPORT.'" target="_blank">Support Link</a></li>' .
					'</ol>'
				) .
			'</div>';
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
		* - Used to display the form data in CPT detail page.
		*
		* @method cfspzw_show_from_data
		*
		* @param  object $post WP_Post
		*/

		function cfspzw_show_from_data( $post ) {

			$fields = CFSPZW()->lib->data_fields;
			$form_id = get_post_meta( $post->ID, '_form_id', true );

			echo '<table class="cfspzw-box-data form-table">' .
				'<style>.inside-field td, .inside-field th{ padding-top: 5px; padding-bottom: 5px;}</style>';

				if ( !empty( $fields ) ) {

					if ( array_key_exists( '_transaction_response', $fields ) && empty( get_post_meta( $form_id, CFSPZW_META_PREFIX . 'debug', true )  ) ) {
						unset( $fields['_transaction_response'] );
					}

					$attachment = ( !empty( get_post_meta( $post->ID, '_attachment', true ) ) ? unserialize( get_post_meta( $post->ID, '_attachment', true ) ) : '' );
					$root_path = get_home_path();

					foreach ( $fields as $key => $value ) {

						if (
							!empty( get_post_meta( $post->ID, $key, true ) )
							&& $key != '_form_data'
							&& $key != '_transaction_status'
							&& $key != '_transaction_response'
							&& $key != '_transaction_id'
						) {

							$val = get_post_meta( $post->ID, $key, true );

							echo '<tr class="form-field">' .
								'<th scope="row">' .
									'<label for="hcf_author">' . __( sprintf( '%s', $value ), 'accept-sagepay-payments-using-contact-form-7' ) . '</label>' .
								'</th>' .
								'<td>' .
									(
										(
											'_form_id' == $key
											&& !empty( get_the_title( get_post_meta( $post->ID, $key, true ) ) )
										)
										? get_the_title( get_post_meta( $post->ID, $key, true ) )
										: get_post_meta( $post->ID, $key, true )
									) .
								'</td>' .
							'</tr>';

						}else if(
							!empty( get_post_meta( $post->ID, $key, true ) )
							&& $key == '_transaction_id'
						){
							echo '<tr class="form-field">' .
								'<th scope="row">' .
									'<label for="hcf_author">' . __( sprintf( '%s', $value ), 'accept-sagepay-payments-using-contact-form-7' ) . '</label>' .
								'</th>' .
								'<td>' .
									(
										(
											!empty( CFSPZW()->lib->response_status )
											&& array_key_exists( get_post_meta( $post->ID , $key, true ), CFSPZW()->lib->response_status )
										)
										? CFSPZW()->lib->response_status[get_post_meta( $post->ID , $key, true )]
										: trim( get_post_meta( $post->ID , $key, true ), '{}')
									) .
								'</td>' .
							'</tr>';
						} else if(
							!empty( get_post_meta( $post->ID, $key, true ) )
							&& $key == '_transaction_status'
						){
							echo '<tr class="form-field">' .
								'<th scope="row">' .
									'<label for="hcf_author">' . __( sprintf( '%s', $value ), 'accept-sagepay-payments-using-contact-form-7' ) . '</label>' .
								'</th>' .
								'<td>' .
									(
										(
											!empty( CFSPZW()->lib->response_status )
											&& array_key_exists( get_post_meta( $post->ID , $key, true ), CFSPZW()->lib->response_status )
										)
										? CFSPZW()->lib->response_status[get_post_meta( $post->ID , $key, true )]
										: get_post_meta( $post->ID , $key, true )
									) .
								'</td>' .
							'</tr>';
						} else if (
							!empty( get_post_meta( $post->ID, $key, true ) )
							&& $key == '_form_data'
						) {

							echo '<tr class="form-field">' .
								'<th scope="row">' .
									'<label for="hcf_author">' . __( sprintf( '%s', $value ), 'accept-sagepay-payments-using-contact-form-7' ) . '</label>' .
								'</th>' .
								'<td>' .
									'<table>';

										$data = unserialize( get_post_meta( $post->ID, $key, true ) );
										$hide_data = apply_filters( CFSPZW_META_PREFIX . '/hide-display', array( '_wpcf7', '_wpcf7_version', '_wpcf7_locale', '_wpcf7_unit_tag', '_wpcf7_container_post' ) );
										foreach ( $hide_data as $key => $value ) {
											if ( array_key_exists( $value, $data ) ) {
												unset( $data[$value] );
											}
										}

										if ( !empty( $data ) ) {
											foreach ( $data as $key => $value ) {
												if ( strpos( $key, 'sagepay-' ) === false ) {
													echo '<tr class="inside-field">' .
														'<th scope="row">' .
															__( sprintf( '%s', $key ), 'accept-sagepay-payments-using-contact-form-7' ) .
														'</th>' .
														'<td>' .
															(
																(
																	!empty( $attachment )
																	&& array_key_exists( $key, $attachment )
																)
																? '<a href="' . esc_url( home_url( str_replace( $root_path, '/', $attachment[$key] ) ) ) . '" target="_blank" download>' . __( sprintf( '%s', $value ), 'accept-sagepay-payments-using-contact-form-7' ) . '</a>'
																: __( sprintf( '%s', ( is_array($value) ? implode( ', ', $value ) :  $value ) ), 'accept-sagepay-payments-using-contact-form-7' )
															) .
														'</td>' .
													'</tr>';
												}
											}
										}

									echo '</table>' .
								'</td>
							</tr>';

						} else if (
							!empty( get_post_meta( $post->ID, $key, true ) )
							&& $key == '_transaction_response'
						) {

							$response_data = explode("&", get_post_meta( $post->ID , $key, true ));

							echo '<tr class="form-field">' .
								'<th scope="row">' .
									'<label for="hcf_author">' . __( sprintf( '%s', $value ), 'accept-sagepay-payments-using-contact-form-7' ) . '</label>' .
								'</th>' .
								'<td>' .
									'<table>';

										$hide_data = apply_filters( CFSPZW_META_PREFIX . '/hide-display', array( '_wpcf7', '_wpcf7_version', '_wpcf7_locale', '_wpcf7_unit_tag', '_wpcf7_container_post' ) );
										foreach ( $hide_data as $key => $value ) {
											if ( array_key_exists( $value, $response_data ) ) {
												unset( $response_data[$value] );
											}
										}

										if ( !empty( $response_data ) ) {
											foreach ( $response_data as $key => $value ) {

												$response_data_key = explode("=", $value);

												if ( strpos( $key, 'sagepay-' ) === false ) {
													echo '<tr class="inside-field">' .
														'<th scope="row">' .
															__( sprintf( '%s', $response_data_key[0] ), 'accept-sagepay-payments-using-contact-form-7' ) .
														'</th>' .
														'<td>' .
															(
																(
																	!empty( $attachment )
																	&& array_key_exists( $key, $attachment )
																)
																? '<a href="' . esc_url( home_url( str_replace( $root_path, '/', $attachment[$key] ) ) ) . '" target="_blank" download>' . __( sprintf( '%s', $value ), 'accept-sagepay-payments-using-contact-form-7' ) . '</a>'
																: __( sprintf( '%s', $response_data_key[1] ), 'accept-sagepay-payments-using-contact-form-7' )
															) .
														'</td>' .
													'</tr>';
												}
											}
										}

									echo '</table>' .
								'</td>
							</tr>';
						}
					}
				}

			echo '</table>';
		}

		/**
		* - Used to add meta box in CPT detail page.
		*/
		function cfspzw_show_help_data() {
			echo '<div id="cfspzw-data-help">' .
				apply_filters(
					CFSPZW_META_PREFIX . '/help/'.CFSPZW_POST_TYPE.'/postbox',
					'<ol>' .
						'<li><a href="'.CFSPZW_DOCUMENT.'" target="_blank">Refer the document.</a></li>' .
						'<li><a href="'.CFSPZW_SUPPORT.'" target="_blank">Support Link</a></li>' .
					'</ol>'
				) .
			'</div>';
		}

		/**
		 * - Add import submenu page callback
		 */
		function cfspzw_import_submenu_page_callback() {
			echo '<div class="wrap cfspzw_wrap_import show-upload-view">';
				echo '<h1 class ="wp-heading-inline">'. __( 'Import your CSV.', 'accept-sagepay-payments-using-contact-form-7' ) .'</h1>';
				echo '<div class="upload-plugin">
						<p class ="install-help">'. __( 'Check demo CSV ', 'accept-sagepay-payments-using-contact-form-7' ) .'<a download href="'.CFSPZW_URL.'import-example/cfspzw-demo.csv">'. __( 'here..', 'accept-sagepay-payments-using-contact-form-7' ) .'</a></p>
						<form method="post" enctype="multipart/form-data" class="wp-upload-form" style="max-width:780px;">
							<label style="margin-right:6px;">'.__( 'Enter New Form Name','accept-sagepay-payments-using-contact-form-7' ).'
							<input type="text" placeholder="Enter New Form Name" name="formname" required /></label>
							<label>'. __( 'Upload File','accept-sagepay-payments-using-contact-form-7' ) .'
							<input type="hidden" id="_wpnonce" name="_wpnonce_cfspzw" value="'. wp_create_nonce( 'cfspzw_import' ) .'">
							<input type="file" id="pluginzip" name="cfspzw_importcsv"></label>
							<label><input type="submit" name="cfspzw-import-plugin-submit" id="install-plugin-submit" class="button" value="Import Now" disabled=""></label>
						</form>
					</div>';
			echo '</div>';
		}

		/**
		 * - Import is success notice
		 */
		function action__admin_notices_import_done() {
			echo '<div class="updated">' .
				sprintf(
					/* translators: Accept Sagepay Payments Using Contact Form 7 */
					__( '<p>Import is done successfully.</p>', 'accept-sagepay-payments-using-contact-form-7' ),
					'Accept Sagepay Payments Using Contact Form 7'
				) .
			'</div>';
		}

		/**
		 * Import nonce issue notice
		 */
		function action__admin_notices_import_nonce_issue(){
			echo '<div class="error">' .
				sprintf(
					/* translators: Accept Sagepay Payments Using Contact Form 7 */
					__( '<p>Nonce issue.. Please try again.</p>', 'accept-sagepay-payments-using-contact-form-7' ),
					'Accept Sagepay Payments Using Contact Form 7'
				) .
			'</div>';
		}

		/**
		 * - Import file format notice
		 */
		function action__admin_notices_import_file_format() {
			echo '<div class="error">' .
				sprintf(
					/* translators: Accept Sagepay Payments Using Contact Form 7 */
					__( '<p>File Format is not suported.</p>', 'accept-sagepay-payments-using-contact-form-7' ),
					'Accept Sagepay Payments Using Contact Form 7'
				) .
			'</div>';
		}

		/**
		 * Import file type notice
		 */
		function action__admin_notices_import_file_type() {
			echo '<div class="error">' .
				sprintf(
					/* translators: Accept Sagepay Payments Using Contact Form 7 */
					__( '<p>File type is not correct. Please upload CSV.</p>', 'accept-sagepay-payments-using-contact-form-7' ),
					'Accept Sagepay Payments Using Contact Form 7'
				) .
			'</div>';
		}

		/**
		 * - Import fail notice
		 */
		function action__admin_notices_import_fail(){
			echo '<div class="error">' .
				sprintf(
					/* translators: Accept Sagepay Payments Using Contact Form 7 */
					__( '<p>Import is failed contact plugin author.</p>', 'accept-sagepay-payments-using-contact-form-7' ),
					'Accept Sagepay Payments Using Contact Form 7'
				) .
			'</div>';
		}

	}

}
