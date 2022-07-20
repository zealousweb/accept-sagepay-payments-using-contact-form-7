<?php
/**
* CFSPZW_Admin_Action Class
*
* Handles the admin functionality.
*
* @package WordPress
* @subpackage Accept Sagepay Payments Using Contact Form 7
* @since 1.2
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
			add_action( 'add_meta_boxes',	array( $this, 'action__cfspzw_add_meta_boxes' ) );

			// Save settings of contact form 7 admin
			add_action( 'wpcf7_save_contact_form',	array( $this, 'action__cfspzw_wpcf7_save_contact_form' ), 20, 2 );

			add_action( 'manage_'.CFSPZW_POST_TYPE.'_posts_custom_column',	array( $this, 'action__manage_cfspzw_data_posts_custom_column' ), 10, 2 );

			add_action( 'pre_get_posts',			array( $this, 'action__cfspzw_pre_get_posts' ) );
			add_action( 'restrict_manage_posts',	array( $this, 'action__cfspzw_restrict_manage_posts' ) );
			add_action( 'parse_query',				array( $this, 'action__cfspzw_parse_query' ) );

			add_action( CFSPZW_PREFIX . '/postbox', array( $this, 'action__cfspzw_postbox' ) );

			add_action( 'wp_ajax_cfspzw_review_done',			array( $this, 'action__cfspzw_review_done'));
			add_action( 'wp_ajax_nopriv_cfspzw_review_done',	array( $this, 'action__cfspzw_review_done'));

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

			wp_register_script( CFSPZW_PREFIX . '_modal_js', CFSPZW_URL . 'assets/js/bootstrap.min.js', array(), CFSPZW_VERSION );
			wp_register_script( CFSPZW_PREFIX . '_cookie_js', CFSPZW_URL . 'assets/js/cookie.min.js', array(), CFSPZW_VERSION );
			
			wp_register_style( CFSPZW_PREFIX . '_admin_css', CFSPZW_URL . 'assets/css/admin.min.css', array(), CFSPZW_VERSION );
			wp_register_script( CFSPZW_PREFIX . '_admin_js', CFSPZW_URL . 'assets/js/admin.min.js', array( 'jquery-core' ), CFSPZW_VERSION );

			wp_register_style( 'select2', CFSPZW_URL . 'assets/css/select2.min.css', array(), CFSPZW_VERSION );
			wp_register_script( 'select2', CFSPZW_URL . 'assets/js/select2.min.js', array( 'jquery-core' ), CFSPZW_VERSION );
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

			if(!get_option('_exceed_cfspzw_l')){
				add_option('_exceed_cfspzw_l', 'cfspzw10');
			}

			if ( !empty( $form_fields ) ) {
				foreach ( $form_fields as $key ) {
					if( isset( $_REQUEST[ $key ] ) ){
						$keyval = sanitize_text_field( $_REQUEST[ $key ] );
						trim( sanitize_text_field( update_post_meta( $post_id, $key, $keyval ) ) );
					}else{
						trim( sanitize_text_field( update_post_meta( $post_id, $key, '' ) ) );
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


			$data_ct = $this->cfspzw_check_data_ct( sanitize_text_field( $post_id ) );

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
							: sanitize_text_field( get_post_meta( $post_id , '_user_name', true ) )
						)
						: ''
					);
				break;

				case 'invoice_no' :
					if( $data_ct ){
						echo '<a href='.CFSPZW_PRODUCT_LINK.' target="_blank">To unlock more features consider upgrading to PRO</a>';
					}else{
						echo (
							!empty( get_post_meta( $post_id , '_invoice_no', true ) )
							? (
								(
									!empty( CFSPZW()->lib->response_status )
									&& array_key_exists( get_post_meta( $post_id , '_invoice_no', true ), CFSPZW()->lib->response_status)
								)
								? CFSPZW()->lib->response_status[get_post_meta( $post_id , '_invoice_no', true )]
								: sanitize_text_field( get_post_meta( $post_id , '_invoice_no', true ) )
							)
							: ''
						);
					}
				break;

				case 'transaction_status' :
					if( $data_ct ){
						echo '<a href='.CFSPZW_PRODUCT_LINK.' target="_blank">To unlock more features consider upgrading to PRO</a>';
					}else{
						echo (
							!empty( get_post_meta( $post_id , '_transaction_status', true ) )
							? (
								(
									!empty( CFSPZW()->lib->response_status )
									&& array_key_exists( get_post_meta( $post_id , '_transaction_status', true ), CFSPZW()->lib->response_status)
								)
								? CFSPZW()->lib->response_status[get_post_meta( $post_id , '_transaction_status', true )]
								: sanitize_text_field( get_post_meta( $post_id , '_transaction_status', true ) )
							)
							: ''
						);
					}
				break;

				case 'total' :
					if( $data_ct ){
						echo '<a href='.CFSPZW_PRODUCT_LINK.' target="_blank">To unlock more features consider upgrading to PRO</a>';
					}else{
						echo ( !empty( get_post_meta( $post_id , '_total', true ) ) ? trim( sanitize_text_field( get_post_meta( $post_id , '_total', true ) ) ) : '' );
					}
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

			$selected = ( isset( $_REQUEST['form-id'] ) ? sanitize_text_field( $_REQUEST['form-id'] ) : '' );

			echo '<select name="form-id" id="form-id">';
			echo '<option value="all">' . __( 'Select Forms', 'accept-2checkout-payments-using-contact-form-7' ) . '</option>';
			foreach ( $posts as $post ) {
				echo '<option value="' . $post->ID . '" ' . selected( $selected, $post->ID, false ) . '>' . $post->post_title  . '</option>';
			}
			echo '</select>';
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
				&& isset( $_REQUEST['form-id'] )
				&& 'all' != $_REQUEST['form-id']
			) {
				$query->query_vars['meta_value']	= sanitize_text_field( $_REQUEST['form-id'] );
				$query->query_vars['meta_compare']	= '=';
			}

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
						'<li><a href="'.CFSPZW_DOCUMENT.'" target="_blank">' .__("Refer the document.", 'accept-sagepay-payments-using-contact-form-7'). 
						'</a></li>' .
						'<li><a href="'.CFSPZW_SUPPORT.'" target="_blank">' .__("Support Link", 'accept-sagepay-payments-using-contact-form-7'). '</a></li>' .
					'</ol>'
				) .
			'</div>';
		}


		/**
		 * Action: review done
		 *
		 * - Review done.
		 *
		 * @method action__cfspzw_review_done
		 */
		function action__cfspzw_review_done(){
			if( isset( $_POST['value'] ) && $_POST['value'] == 1 ){
				add_option( 'cfspzw_review', "1" );
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

		/**
		* - Used to display the form data in CPT detail page.
		*
		* @method cfspzw_show_from_data
		*
		* @param  object $post WP_Post
		*/

		function cfspzw_show_from_data( $post ) {

			$fields = CFSPZW()->lib->data_fields;
			$form_id = sanitize_text_field( get_post_meta( $post->ID, '_form_id', true ) );

			$post_type = $post->post_type;

			$data_ct = $this->cfspzw_check_data_ct( $post->ID );

			if( $data_ct ) {

				echo '<table><tbody>'.
				'<style>.inside-field th{ text-align: left; }</style>';
					echo'<tr class="inside-field"><th scope="row">You are using Free Accept Sagepay Payments Using Contact Form 7 - no license needed. Enjoy! 🙂‚</th></tr>';
					echo'<tr class="inside-field"><th scope="row"><a href='.CFSPZW_PRODUCT_LINK.' target="_blank">To unlock more features consider upgrading to PRO.</a></th></tr>';
				echo '</tbody></table>';

			}else{

				echo '<table class="cfspzw-box-data form-table">' .
					'<style>.inside-field td, .inside-field th{ padding-top: 5px; padding-bottom: 5px;}</style>';

					if ( !empty( $fields ) ) {

						if ( array_key_exists( '_transaction_response', $fields ) && empty( get_post_meta( $form_id, CFSPZW_META_PREFIX . 'debug', true )  ) ) {
							unset( $fields['_transaction_response'] );
						}

						$attachment = ( !empty( get_post_meta( $post->ID, '_attachment', true ) ) ? unserialize( get_post_meta( $post->ID, '_attachment', true ) ) : '' );

						//echo "<pre>";print_r($attachment);
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
											: sanitize_text_field( get_post_meta( $post->ID, $key, true ) )
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
											: trim( sanitize_text_field( get_post_meta( $post->ID , $key, true ) ), '{}')
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
											: sanitize_text_field( get_post_meta( $post->ID , $key, true ) )
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
																	? '<a href="' . esc_url( home_url( str_replace( $root_path, '/', $attachment[$key] ) ) ) . '" target="_blank" download>' . substr($attachment[$key], strrpos($attachment[$key], '/') + 1) . '</a>'
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
		 * Check data ct
		 */		
		function cfspzw_check_data_ct( $post_id ){
			$data = unserialize( get_post_meta( $post_id, '_form_data', true ) );
			if( !empty( get_post_meta( $post_id, '_form_data', true ) ) && isset( $data['_exceed_num_cfspzw'] ) && !empty( $data['_exceed_num_cfspzw'] ) ){
				return $data['_exceed_num_cfspzw'];
			}else{
				return '';
			}
		}
	}

	add_action( 'plugins_loaded', function() {
		CFSPZW()->admin->action = new CFSPZW_Admin_Action;
	} );

}