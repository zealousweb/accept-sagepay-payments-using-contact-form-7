<?php
/**
* Displays content for plugin option page
*
* @package WordPress
* @subpackage Accept Sagepay Payments Using Contact Form 7
* @since 1.0
* @version 1.0
*/

$post_id = ( isset( $_REQUEST[ 'post' ] ) ? sanitize_text_field( $_REQUEST[ 'post' ] ) : '' );

if ( empty( $post_id ) ) {
	$wpcf7 = WPCF7_ContactForm::get_current();
	$post_id = $wpcf7->id();
}

wp_enqueue_script( 'wp-pointer' );
wp_enqueue_style( 'wp-pointer' );

wp_enqueue_style( 'select2' );
wp_enqueue_script( 'select2' );

wp_enqueue_style( CFSPZW_PREFIX . '_admin_css' );

$use_sagepay					= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'use_sagepay', true );
$debug_sagepay					= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'debug', true );
$status_val						= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'status', true );
$sandbox_vendor_name			= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'sandbox_vendor_name', true );
$sandbox_encryption_password	= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'sandbox_encryption_password', true );
$live_vendor_name				= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'live_vendor_name', true );
$live_encryption_password		= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'live_encryption_password', true );
$transaction_type_val			= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'transaction_type', true );
$apply3d_val					= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'apply3d', true );
$vendor_txcode_prefix			= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'vendor_txcode_prefix', true );
$amount							= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'amount', true );
$customer_email					= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'customer_email', true );
$quantity						= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'quantity', true );
$success_return_url				= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'returnurl', true );
$cancel_return_url				= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'cancel_returnurl', true );
$currency						= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'currency', true );
$country						= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'country', true );

$billing_firstnames				= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'billing_firstnames', true );
$billing_surname				= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'billing_surname', true );
$billing_address				= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'billing_address', true );
$billing_city					= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'billing_city', true );
$billing_state					= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'billing_state', true );
$billing_zip					= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'billing_zip', true );
$billing_country				= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'billing_country', true );


$shipping_firstnames			= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'shipping_firstnames', true );
$shipping_surname				= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'shipping_surname', true );
$shipping_address				= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'shipping_address', true );
$shipping_city					= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'shipping_city', true );
$shipping_state					= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'shipping_state', true );
$shipping_zip					= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'shipping_zip', true );
$shipping_country				= get_post_meta( $post_id, CFSPZW_META_PREFIX . 'shipping_country', true );

$currency_code = array(
	'GBP' => 'Pound Sterling',
	'EUR' => 'Euro',
);
$currency_code = apply_filters( CFSPZW_META_PREFIX .'add_currency', $currency_code );

$status = array(
	'sandbox'	=> __( 'Sandbox', 'accept-sagepay-payments-using-contact-form-7'),
	'live'		=> __( 'Live', 'accept-sagepay-payments-using-contact-form-7')
);

$transaction_type = array(
	'PAYMENT' 		=> __('Payment', 'accept-sagepay-payments-using-contact-form-7'),
	'DEFERRED' 		=> __('Deferred', 'accept-sagepay-payments-using-contact-form-7'),
	'AUTHENTICATE'	=> __('Authenticate', 'accept-sagepay-payments-using-contact-form-7'),
);

$apply3d = array(
	'1' => __('Yes', 'accept-sagepay-payments-using-contact-form-7'),
	'0' => __('No', 'accept-sagepay-payments-using-contact-form-7'),
);

$selected = '';

$args = array(
	'post_type'			=> array( 'page' ),
	'orderby'			=> 'title',
	'posts_per_page'	=> -1
);

$pages = get_posts( $args );
$all_pages = array();

if ( !empty( $pages ) ) {
	foreach ( $pages as $page ) {
		$all_pages[$page->ID] = $page->post_title;
	}
}

if ( !empty( $post_id ) ) {
	$cf7 = WPCF7_ContactForm::get_instance($_REQUEST['post']);
	$tags = $cf7->collect_mail_tags();
}

echo '<div class="cfspzw-settings">' .
	'<div class="left-box postbox">' .
		'<table class="form-table">' .
			'<tbody>';

				if( empty( $tags ) ) {

					echo '<tr class="form-field">' .
						'<td>' .
							__( 'To use SagePay option, first you need to create and save form tags.', 'accept-sagepay-payments-using-contact-form-7' ).
							' <a href="'.CFSPZW_DOCUMENT.'" target="_blank">' . __( 'Document Link', 'accept-sagepay-payments-using-contact-form-7' ) . '</a>'.
						'</td>' .
					'</tr>';

				} else {

					echo '<tr class="form-field">' .
						'<th scope="row">' .
							'<label for="' . CFSPZW_META_PREFIX . 'use_sagepay">' .
								__( 'Sagepay Enable', 'accept-sagepay-payments-using-contact-form-7' ) .
							'</label>' .
						'</th>' .
						'<td>' .
							'<input id="' . CFSPZW_META_PREFIX . 'use_sagepay" name="' . CFSPZW_META_PREFIX . 'use_sagepay" type="checkbox" class="enable_required" value="1" ' . checked( $use_sagepay, 1, false ) . '/>' .
						'</td>' .
					'</tr>' .
					'<tr class="form-field">' .
						'<th scope="row">' .
							'<label for="' . CFSPZW_META_PREFIX . 'debug">' .
								__( 'Enable Debug Mode', 'accept-sagepay-payments-using-contact-form-7' ) .
							'</label>' .
						'</th>' .
						'<td>' .
							'<input id="' . CFSPZW_META_PREFIX . 'debug" name="' . CFSPZW_META_PREFIX . 'debug" type="checkbox" value="1" ' . checked( $debug_sagepay, 1, false ) . '/>' .
						'</td>' .
					'</tr>' .
					'<tr class="form-field">' .
						'<th scope="row">' .
							'<label for="' . CFSPZW_META_PREFIX . 'status">' .
								__( 'Payment Mode ', 'accept-sagepay-payments-using-contact-form-7' ) .
							'*</label>' .
						'</th>' .
						'<td>' .
							'<select id="' . CFSPZW_META_PREFIX . 'status" class="'.CFSPZW_META_PREFIX. 'required-fields" name="' . CFSPZW_META_PREFIX . 'status" ' . ( !empty( $use_sagepay ) ? 'required' : '' ) . '>';
								if ( !empty( $status ) ) {
									foreach ( $status as $key => $value ) {
										echo '<option value="' . esc_attr( $key ) . '" ' . selected( $status_val, $key, false ) . '>' . esc_attr( $value ) . '</option>';
									}
								}

							echo '</select>' .
						'</td>' .
					'</tr>' .					
					'<tr class="form-field">' .
						'<th>' .
							'<label for="' . CFSPZW_META_PREFIX . 'sandbox_vendor_name">' .
								__( 'Sandbox Vendor Name ', 'accept-sagepay-payments-using-contact-form-7' ) .
							'*</label>' .
							'<span class="cfspzw-tooltip hide-if-no-js" id="cfspzw-sandbox-vendor-name"></span>' .
						'</th>' .
						'<td>' .
							'<input id="' . CFSPZW_META_PREFIX . 'sandbox_vendor_name" name="' . CFSPZW_META_PREFIX . 'sandbox_vendor_name" type="text" class="large-text" value="' . esc_attr( $sandbox_vendor_name ) . '" ' . ( !empty( $use_sagepay ) ? 'required' : '' ) . ' />' .
						'</td>' .
					'</tr>' .
					'<tr class="form-field">' .
						'<th>' .
							'<label for="' . CFSPZW_META_PREFIX . 'sandbox_encryption_password">' .
								__( 'Sandbox Encryption Password ', 'accept-sagepay-payments-using-contact-form-7' ) .
							'*</label>' .
							'<span class="cfspzw-tooltip hide-if-no-js" id="cfspzw-sandbox-encryption-password"></span>' .
						'</th>' .
						'<td>' .
							'<input id="' . CFSPZW_META_PREFIX . 'sandbox_encryption_password" name="' . CFSPZW_META_PREFIX . 'sandbox_encryption_password" type="text" class="large-text" value="' . esc_attr( $sandbox_encryption_password ) . '" ' . ( !empty( $use_sagepay ) ? 'required' : '' ) . ' />' .
						'</td>' .
					'</tr>' .

					'<tr class="form-field">' .
						'<th>' .
							'<label for="' . CFSPZW_META_PREFIX . 'live_vendor_name">' .
								__( 'Live Vendor Name ', 'accept-sagepay-payments-using-contact-form-7' ) .
							'*</label>' .
							'<span class="cfspzw-tooltip hide-if-no-js" id="cfspzw-live-vendor-name"></span>' .
						'</th>' .
						'<td>' .
							'<input id="' . CFSPZW_META_PREFIX . 'live_vendor_name" name="' . CFSPZW_META_PREFIX . 'live_vendor_name" type="text" class="large-text" value="' . esc_attr( $live_vendor_name ) . '" ' . ( !empty( $use_sagepay ) ? 'required' : '' ) . ' />' .
						'</td>' .
					'</tr>' .
					'<tr class="form-field">' .
						'<th>' .
							'<label for="' . CFSPZW_META_PREFIX . 'live_encryption_password">' .
								__( 'Live Encryption Password ', 'accept-sagepay-payments-using-contact-form-7' ) .
							'*</label>' .
							'<span class="cfspzw-tooltip hide-if-no-js" id="cfspzw-live-encryption-password"></span>' .
						'</th>' .
						'<td>' .
							'<input id="' . CFSPZW_META_PREFIX . 'live_encryption_password" name="' . CFSPZW_META_PREFIX . 'live_encryption_password" type="text" class="large-text" value="' . esc_attr( $live_encryption_password ) . '" ' . ( !empty( $use_sagepay ) ? 'required' : '' ) . ' />' .
						'</td>' .
					'</tr>' .

					'<tr class="form-field">' .
						'<th>' .
							'<label for="' . CFSPZW_META_PREFIX . 'amount">' .
								__( 'Amount Field Name ', 'accept-sagepay-payments-using-contact-form-7' ) .
							'*</label>' .
							'<span class="cfspzw-tooltip hide-if-no-js" id="cfspzw-amount"></span>' .
						'</th>'.
						'<td>' .
							'<select id="' . CFSPZW_META_PREFIX . 'amount" class="form-required-fields" name="' . CFSPZW_META_PREFIX . 'amount">' .
								'<option value="">' . __( 'Select field name for amount', 'accept-sagepay-payments-using-contact-form-7' ) . '</option>';
								if( !empty( $tags ) ) {
									foreach ( $tags as $key => $tag ) {
										echo '<option value="' . esc_attr( $tag ) . '" ' . selected( $amount, $tag, false )  . '>' . esc_html( $tag ) . '</option>';
									}
								}

							echo '</select>' .
						'</td>' .
					'</tr>'.
					'<tr class="form-field">' .
						'<th>' .
							'<label for="' . CFSPZW_META_PREFIX . 'customer_email">' .
								__( 'Customer Email ', 'accept-sagepay-payments-using-contact-form-7' ) .
							'*</label>' .
							'<span class="cfspzw-tooltip hide-if-no-js" id="cfspzw-email"></span>' .
						'</th>'.
						'<td>' .
							'<select id="' . CFSPZW_META_PREFIX . 'customer_email" class="form-required-fields" name="' . CFSPZW_META_PREFIX . 'customer_email" ' . ( !empty( $use_sagepay ) ? 'required' : '' ) . '>' .
								'<option value="">' . __( 'Select field name for customer email', 'accept-sagepay-payments-using-contact-form-7' ) . '</option>';
								if( !empty( $tags ) ) {
									foreach ( $tags as $key => $tag ) {
										echo '<option value="' . esc_attr( $tag ) . '" ' . selected( $customer_email, $tag, false )  . '>' . esc_html( $tag ) . '</option>';
									}
								}

							echo '</select>' .
						'</td>' .
					'</tr>'.
					'<tr class="form-field">' .
						'<th>' .
							'<label for="' . CFSPZW_META_PREFIX . 'quantity">' .
								__( 'Quantity Field Name (Optional)', 'accept-sagepay-payments-using-contact-form-7' ) .
							'</label>' .
							'<span class="cfspzw-tooltip hide-if-no-js" id="cfspzw-quantity"></span>' .
						'</th>'.
						'<td>' .
							'<select id="' . CFSPZW_META_PREFIX . 'quantity" name="' . CFSPZW_META_PREFIX . 'quantity">' .
								'<option>' . __( 'Select field name for quantity', 'accept-sagepay-payments-using-contact-form-7' ) . '</option>';
								if( !empty( $tags ) ) {
									foreach ( $tags as $key => $tag ) {
										echo '<option value="' . esc_attr( $tag ) . '" ' . selected( $quantity, $tag, false )  . '>' . esc_html( $tag ) . '</option>';
									}
								}

							echo '</select>' .
						'</td>' .
					'</tr>'.

					'<tr class="form-field">' .
						'<th scope="row">' .
							'<label for="' . CFSPZW_META_PREFIX . 'transaction_type">' .
								__( 'Transaction type', 'accept-sagepay-payments-using-contact-form-7' ) .
							' *</label>' .
						'</th>' .
						'<td>' .
							'<select id="' . CFSPZW_META_PREFIX . 'transaction_type" name="' . CFSPZW_META_PREFIX . 'transaction_type">';

								if ( !empty( $transaction_type ) ) {
									foreach ( $transaction_type as $key => $value ) {
										echo '<option value="' . esc_attr( $key ) . '" ' . selected( $transaction_type_val, $key, false ) . '>' . esc_attr( $value ) . '</option>';
									}
								}

							echo '</select>' .
						'</td>' .
					'</tr>' .
					'<tr class="form-field">' .
						'<th scope="row">' .
							'<label for="' . CFSPZW_META_PREFIX . 'apply3d">' .
								__( 'Apply 3D Secure', 'accept-sagepay-payments-using-contact-form-7' ),
							'</label>' .
						'</th>' .
						'<td>' .
							'<select id="' . CFSPZW_META_PREFIX . 'apply3d" name="' . CFSPZW_META_PREFIX . 'apply3d">';

								if ( !empty( $apply3d ) ) {
									foreach ( $apply3d as $key => $value ) {
										echo '<option value="' . esc_attr( $key ) . '" ' . selected( $apply3d_val, $key, false ) . '>' . esc_attr( $value ) . '</option>';
									}
								}

							echo '</select>' .
						'</td>' .
					'</tr>' .					
					'<tr class="form-field">' .
						'<th>' .
							'<label for="' . CFSPZW_META_PREFIX . 'currency">' .
								__( 'Select Currency', 'accept-sagepay-payments-using-contact-form-7' ) .
							' *</label>' .
							'<span class="cfspzw-tooltip hide-if-no-js" id="cfspzw-select-currency"></span>' .
						'</th>' .
						'<td>' .
							'<select id="' . CFSPZW_META_PREFIX . 'currency" name="' . CFSPZW_META_PREFIX . 'currency">';

								if ( !empty( $currency_code ) ) {
									foreach ( $currency_code as $key => $value ) {
										echo '<option value="' . esc_attr( $key ) . '" ' . selected( $currency, $key, false ) . '>' . esc_attr( $value ) . '</option>';
									}
								}

							echo '</select>' .
						'</td>' .
					'</tr/>' .
					'<tr class="form-field">' .
						'<th>' .
							'<label for="' . CFSPZW_META_PREFIX . 'vendor_txcode_prefix">' .
								__( 'VendorTXCode Prefix (Optional)', 'accept-sagepay-payments-using-contact-form-7' ) .
							'</label>' .
							'<span class="cfspzw-tooltip hide-if-no-js" id="cfspzw-prefix"></span>' .
						'</th>' .
						'<td>' .
							'<input id="' . CFSPZW_META_PREFIX . 'vendor_txcode_prefix" name="' . CFSPZW_META_PREFIX . 'vendor_txcode_prefix" type="text" class="large-text" value="' . esc_attr( $vendor_txcode_prefix ) . '" />' .
						'</td>' .
					'</tr>' .
					'<tr class="form-field">' .
						'<th>' .
							'<label for="' . CFSPZW_META_PREFIX . 'returnurl">' .
								__( 'Success Return URL (Optional)', 'accept-sagepay-payments-using-contact-form-7' ) .
							'</label>' .
							'<span class="cfspzw-tooltip hide-if-no-js" id="cfspzw-success-returnurl"></span>' .
						'</th>'.
						'<td>' .
							'<select id="' . CFSPZW_META_PREFIX . 'returnurl" name="' . CFSPZW_META_PREFIX . 'returnurl">' .
								'<option>' . __( 'Select page', 'accept-sagepay-payments-using-contact-form-7' ) . '</option>';

								if( !empty( $all_pages ) ) {
									foreach ( $all_pages as $post_Id => $title ) {
										echo '<option value="' . esc_attr( $post_Id ) . '" ' . selected( $success_return_url, $post_Id, false )  . '>' . $title . '</option>';
									}
								}

							echo '</select>' .
						'</td>' .
					'</tr>'.
					'<tr class="form-field">' .
						'<th>' .
							'<label for="' . CFSPZW_META_PREFIX . 'cancel_returnurl">' .
								__( 'Cancel Return URL (Optional)', 'accept-sagepay-payments-using-contact-form-7' ) .
							'</label>' .
							'<span class="cfspzw-tooltip hide-if-no-js" id="cfspzw-cancel-returnurl"></span>' .
						'</th>'.
						'<td>' .
							'<select id="' . CFSPZW_META_PREFIX . 'cancel_returnurl" name="' . CFSPZW_META_PREFIX . 'cancel_returnurl">' .
								'<option>' . __( 'Select page', 'accept-sagepay-payments-using-contact-form-7' ) . '</option>';

								if( !empty( $all_pages ) ) {
									foreach ( $all_pages as $post_Id => $title ) {
										echo '<option value="' . esc_attr( $post_Id ) . '" ' . selected( $cancel_return_url, $post_Id, false )  . '>' . $title . '</option>';
									}
								}

							echo '</select>' .
						'</td>' .
					'</tr>';


					// Billing Fields
					echo '<tr class="form-field">' .
						'<th colspan="2">' .
							'<label for="' . CFSPZW_META_PREFIX . 'customer_billing_details">' .
								'<h3 style="margin: 0;">' .
									__( 'Customer Billing Details', 'accept-sagepay-payments-using-contact-form-7' ) .
									'<span class="arrow-switch"></span>' .
								'</h3>' .
							'</label>' .
						'</th>' .
					'</tr>'.
					'<tr class="form-field">' .
						'<th>' .
							'<label for="' . CFSPZW_META_PREFIX . 'billing_firstnames">' .
								__( 'Billing First Name ', 'accept-sagepay-payments-using-contact-form-7' ) .
							'*</label>' .
							'<span class="cfspzw-tooltip hide-if-no-js" id="cfspzw-billing-firstnames"></span>' .
						'</th>'.
						'<td>' .
							'<select id="' . CFSPZW_META_PREFIX . 'billing_firstnames" class="form-required-fields" name="' . CFSPZW_META_PREFIX . 'billing_firstnames" ' . ( !empty( $use_sagepay ) ? 'required' : '' ) . '>' .
								'<option value="">' . __( 'Select field name for billing first name', 'accept-sagepay-payments-using-contact-form-7' ) . '</option>';
								if( !empty( $tags ) ) {
									foreach ( $tags as $key => $tag ) {
										echo '<option value="' . esc_attr( $tag ) . '" ' . selected( $billing_firstnames, $tag, false )  . '>' . esc_html( $tag ) . '</option>';
									}
								}

							echo '</select>' .
						'</td>' .
					'</tr>'.
					'<tr class="form-field">' .
						'<th>' .
							'<label for="' . CFSPZW_META_PREFIX . 'billing_surname">' .
								__( 'Billing Last Name ', 'accept-sagepay-payments-using-contact-form-7' ) .
							'*</label>' .
							'<span class="cfspzw-tooltip hide-if-no-js" id="cfspzw-billing-surname"></span>' .
						'</th>'.
						'<td>' .
							'<select id="' . CFSPZW_META_PREFIX . 'billing_surname" class="form-required-fields" name="' . CFSPZW_META_PREFIX . 'billing_surname" ' . ( !empty( $use_sagepay ) ? 'required' : '' ) . '>' .
								'<option value="">' . __( 'Select field name for billing last name', 'accept-sagepay-payments-using-contact-form-7' ) . '</option>';
								if( !empty( $tags ) ) {
									foreach ( $tags as $key => $tag ) {
										echo '<option value="' . esc_attr( $tag ) . '" ' . selected( $billing_surname, $tag, false )  . '>' . esc_html( $tag ) . '</option>';
									}
								}

							echo '</select>' .
						'</td>' .
					'</tr>'.
					'<tr class="form-field">' .
						'<th>' .
							'<label for="' . CFSPZW_META_PREFIX . 'billing_address">' .
								__( 'Billing Address ', 'accept-sagepay-payments-using-contact-form-7' ) .
							'*</label>' .
							'<span class="cfspzw-tooltip hide-if-no-js" id="cfspzw-billing-address"></span>' .
						'</th>'.
						'<td>' .
							'<select id="' . CFSPZW_META_PREFIX . 'billing_address" class="form-required-fields" name="' . CFSPZW_META_PREFIX . 'billing_address" ' . ( !empty( $use_sagepay ) ? 'required' : '' ) . '>' .
								'<option value="">' . __( 'Select field name for billing address', 'accept-sagepay-payments-using-contact-form-7' ) . '</option>';
								if( !empty( $tags ) ) {
									foreach ( $tags as $key => $tag ) {
										echo '<option value="' . esc_attr( $tag ) . '" ' . selected( $billing_address, $tag, false )  . '>' . esc_html( $tag ) . '</option>';
									}
								}

							echo '</select>' .
						'</td>' .
					'</tr>'.
					'<tr class="form-field">' .
						'<th>' .
							'<label for="' . CFSPZW_META_PREFIX . 'billing_city">' .
								__( 'Billing City ', 'accept-sagepay-payments-using-contact-form-7' ) .
							'*</label>' .
							'<span class="cfspzw-tooltip hide-if-no-js" id="cfspzw-billing-city"></span>' .
						'</th>'.
						'<td>' .
							'<select id="' . CFSPZW_META_PREFIX . 'billing_city" class="form-required-fields" name="' . CFSPZW_META_PREFIX . 'billing_city" ' . ( !empty( $use_sagepay ) ? 'required' : '' ) . '>' .
								'<option value="">' . __( 'Select field name for billing city', 'accept-sagepay-payments-using-contact-form-7' ) . '</option>';
								if( !empty( $tags ) ) {
									foreach ( $tags as $key => $tag ) {
										echo '<option value="' . esc_attr( $tag ) . '" ' . selected( $billing_city, $tag, false )  . '>' . esc_html( $tag ) . '</option>';
									}
								}

							echo '</select>' .
						'</td>' .
					'</tr>'.
					'<tr class="form-field">' .
						'<th>' .
							'<label for="' . CFSPZW_META_PREFIX . 'billing_state">' .
								__( 'Billing State ', 'accept-sagepay-payments-using-contact-form-7' ) .
							'*</label>' .
							'<span class="cfspzw-tooltip hide-if-no-js" id="cfspzw-billing-state"></span>' .
						'</th>'.
						'<td>' .
							'<select id="' . CFSPZW_META_PREFIX . 'billing_state" class="form-required-fields" name="' . CFSPZW_META_PREFIX . 'billing_state" ' . ( !empty( $use_sagepay ) ? 'required' : '' ) . '>' .
								'<option value="">' . __( 'Select field name for billing state', 'accept-sagepay-payments-using-contact-form-7' ) . '</option>';
								if( !empty( $tags ) ) {
									foreach ( $tags as $key => $tag ) {
										echo '<option value="' . esc_attr( $tag ) . '" ' . selected( $billing_state, $tag, false )  . '>' . esc_html( $tag ) . '</option>';
									}
								}

							echo '</select>' .
						'</td>' .
					'</tr>'.
					'<tr class="form-field">' .
						'<th>' .
							'<label for="' . CFSPZW_META_PREFIX . 'billing_country">' .
								__( 'Select Billing Country', 'accept-sagepay-payments-using-contact-form-7' ) .
							' *</label>' .
							'<span class="cfspzw-tooltip hide-if-no-js" id="cfspzw-billing-country"></span>' .
						'</th>' .
						'<td>' .
							'<select id="' . CFSPZW_META_PREFIX . 'billing_country" class="form-required-fields" name="' . CFSPZW_META_PREFIX . 'billing_country" ' . ( !empty( $use_sagepay ) ? 'required' : '' ) . '>' .
								'<option value="">' . __( 'Select field name for billing country', 'accept-sagepay-payments-using-contact-form-7' ) . '</option>';
								if( !empty( $tags ) ) {
									foreach ( $tags as $key => $tag ) {
										echo '<option value="' . esc_attr( $tag ) . '" ' . selected( $billing_country, $tag, false )  . '>' . esc_html( $tag ) . '</option>';
									}
								}

							echo '</select>' .
						'</td>' .
					'</tr/>' .
					'<tr class="form-field">' .
						'<th>' .
							'<label for="' . CFSPZW_META_PREFIX . 'billing_zip">' .
								__( 'Billing Zipcode ', 'accept-sagepay-payments-using-contact-form-7' ) .
							'*</label>' .
							'<span class="cfspzw-tooltip hide-if-no-js" id="cfspzw-billing-zip"></span>' .
						'</th>'.
						'<td>' .
							'<select id="' . CFSPZW_META_PREFIX . 'billing_zip" class="form-required-fields" name="' . CFSPZW_META_PREFIX . 'billing_zip" ' . ( !empty( $use_sagepay ) ? 'required' : '' ) . '>' .
								'<option value="">' . __( 'Select field name for billing zipcode', 'accept-sagepay-payments-using-contact-form-7' ) . '</option>';
								if( !empty( $tags ) ) {
									foreach ( $tags as $key => $tag ) {
										echo '<option value="' . esc_attr( $tag ) . '" ' . selected( $billing_zip, $tag, false )  . '>' . esc_html( $tag ) . '</option>';
									}
								}

							echo '</select>' .
						'</td>' .
					'</tr>';


					// Shipping Fields
					echo '<tr class="form-field">' .
						'<th colspan="2">' .
							'<label for="' . CFSPZW_META_PREFIX . 'customer_shipping_details">' .
								'<h3 style="margin: 0;">' .
									__( 'Customer Shipping Details', 'accept-sagepay-payments-using-contact-form-7' ) .
									'<span class="arrow-switch"></span>' .
								'</h3>' .
							'</label>' .
						'</th>' .
					'</tr>'.
					'<tr class="form-field">' .
						'<th>' .
							'<label for="' . CFSPZW_META_PREFIX . 'shipping_firstnames">' .
								__( 'Shipping First Name ', 'accept-sagepay-payments-using-contact-form-7' ) .
							'*</label>' .
							'<span class="cfspzw-tooltip hide-if-no-js" id="cfspzw-shipping-firstnames"></span>' .
						'</th>'.
						'<td>' .
							'<select id="' . CFSPZW_META_PREFIX . 'shipping_firstnames" class="form-required-fields" name="' . CFSPZW_META_PREFIX . 'shipping_firstnames" ' . ( !empty( $use_sagepay ) ? 'required' : '' ) . '>' .
								'<option value="">' . __( 'Select field name for shipping first name', 'accept-sagepay-payments-using-contact-form-7' ) . '</option>';
								if( !empty( $tags ) ) {
									foreach ( $tags as $key => $tag ) {
										echo '<option value="' . esc_attr( $tag ) . '" ' . selected( $shipping_firstnames, $tag, false )  . '>' . esc_html( $tag ) . '</option>';
									}
								}

							echo '</select>' .
						'</td>' .
					'</tr>'.
					'<tr class="form-field">' .
						'<th>' .
							'<label for="' . CFSPZW_META_PREFIX . 'shipping_surname">' .
								__( 'Shipping Last Name ', 'accept-sagepay-payments-using-contact-form-7' ) .
							'*</label>' .
							'<span class="cfspzw-tooltip hide-if-no-js" id="cfspzw-shipping-surname"></span>' .
						'</th>'.
						'<td>' .
							'<select id="' . CFSPZW_META_PREFIX . 'shipping_surname" class="form-required-fields" name="' . CFSPZW_META_PREFIX . 'shipping_surname" ' . ( !empty( $use_sagepay ) ? 'required' : '' ) . '>' .
								'<option value="">' . __( 'Select field name for shipping last name', 'accept-sagepay-payments-using-contact-form-7' ) . '</option>';
								if( !empty( $tags ) ) {
									foreach ( $tags as $key => $tag ) {
										echo '<option value="' . esc_attr( $tag ) . '" ' . selected( $shipping_surname, $tag, false )  . '>' . esc_html( $tag ) . '</option>';
									}
								}

							echo '</select>' .
						'</td>' .
					'</tr>'.
					'<tr class="form-field">' .
						'<th>' .
							'<label for="' . CFSPZW_META_PREFIX . 'shipping_address">' .
								__( 'Shipping Address ', 'accept-sagepay-payments-using-contact-form-7' ) .
							'*</label>' .
							'<span class="cfspzw-tooltip hide-if-no-js" id="cfspzw-shipping-address"></span>' .
						'</th>'.
						'<td>' .
							'<select id="' . CFSPZW_META_PREFIX . 'shipping_address" class="form-required-fields" name="' . CFSPZW_META_PREFIX . 'shipping_address" ' . ( !empty( $use_sagepay ) ? 'required' : '' ) . '>' .
								'<option value="">' . __( 'Select field name for shipping address', 'accept-sagepay-payments-using-contact-form-7' ) . '</option>';
								if( !empty( $tags ) ) {
									foreach ( $tags as $key => $tag ) {
										echo '<option value="' . esc_attr( $tag ) . '" ' . selected( $shipping_address, $tag, false )  . '>' . esc_html( $tag ) . '</option>';
									}
								}

							echo '</select>' .
						'</td>' .
					'</tr>'.
					'<tr class="form-field">' .
						'<th>' .
							'<label for="' . CFSPZW_META_PREFIX . 'shipping_city">' .
								__( 'Shipping City ', 'accept-sagepay-payments-using-contact-form-7' ) .
							'*</label>' .
							'<span class="cfspzw-tooltip hide-if-no-js" id="cfspzw-shipping-city"></span>' .
						'</th>'.
						'<td>' .
							'<select id="' . CFSPZW_META_PREFIX . 'shipping_city" class="form-required-fields" name="' . CFSPZW_META_PREFIX . 'shipping_city" ' . ( !empty( $use_sagepay ) ? 'required' : '' ) . '>' .
								'<option value="">' . __( 'Select field name for shipping city', 'accept-sagepay-payments-using-contact-form-7' ) . '</option>';
								if( !empty( $tags ) ) {
									foreach ( $tags as $key => $tag ) {
										echo '<option value="' . esc_attr( $tag ) . '" ' . selected( $shipping_city, $tag, false )  . '>' . esc_html( $tag ) . '</option>';
									}
								}

							echo '</select>' .
						'</td>' .
					'</tr>'.
					'<tr class="form-field">' .
						'<th>' .
							'<label for="' . CFSPZW_META_PREFIX . 'shipping_state">' .
								__( 'Shipping State ', 'accept-sagepay-payments-using-contact-form-7' ) .
							'*</label>' .
							'<span class="cfspzw-tooltip hide-if-no-js" id="cfspzw-shipping-state"></span>' .
						'</th>'.
						'<td>' .
							'<select id="' . CFSPZW_META_PREFIX . 'shipping_state" class="form-required-fields" name="' . CFSPZW_META_PREFIX . 'shipping_state" ' . ( !empty( $use_sagepay ) ? 'required' : '' ) . '>' .
								'<option value="">' . __( 'Select field name for shipping state', 'accept-sagepay-payments-using-contact-form-7' ) . '</option>';
								if( !empty( $tags ) ) {
									foreach ( $tags as $key => $tag ) {
										echo '<option value="' . esc_attr( $tag ) . '" ' . selected( $shipping_state, $tag, false )  . '>' . esc_html( $tag ) . '</option>';
									}
								}

							echo '</select>' .
						'</td>' .
					'</tr>'.
					'<tr class="form-field">' .
						'<th>' .
							'<label for="' . CFSPZW_META_PREFIX . 'shipping_country">' .
								__( 'Select Shipping Country', 'accept-sagepay-payments-using-contact-form-7' ) .
							' *</label>' .
							'<span class="cfspzw-tooltip hide-if-no-js" id="cfspzw-shipping-country"></span>' .
						'</th>' .
						'<td>' .
							'<select id="' . CFSPZW_META_PREFIX . 'shipping_country" class="form-required-fields" name="' . CFSPZW_META_PREFIX . 'shipping_country" ' . ( !empty( $use_sagepay ) ? 'required' : '' ) . '>' .
								'<option value="">' . __( 'Select field name for shipping country', 'accept-sagepay-payments-using-contact-form-7' ) . '</option>';
								if( !empty( $tags ) ) {
									foreach ( $tags as $key => $tag ) {
										echo '<option value="' . esc_attr( $tag ) . '" ' . selected( $shipping_country, $tag, false )  . '>' . esc_html( $tag ) . '</option>';
									}
								}

							echo '</select>' .
						'</td>' .
					'</tr/>' .
					'<tr class="form-field">' .
						'<th>' .
							'<label for="' . CFSPZW_META_PREFIX . 'shipping_zip">' .
								__( 'Shipping Zipcode ', 'accept-sagepay-payments-using-contact-form-7' ) .
							'*</label>' .
							'<span class="cfspzw-tooltip hide-if-no-js" id="cfspzw-shipping-zip"></span>' .
						'</th>'.
						'<td>' .
							'<select id="' . CFSPZW_META_PREFIX . 'shipping_zip" class="form-required-fields" name="' . CFSPZW_META_PREFIX . 'shipping_zip" ' . ( !empty( $use_sagepay ) ? 'required' : '' ) . '>' .
								'<option value="">' . __( 'Select field name for shipping zipcode', 'accept-sagepay-payments-using-contact-form-7' ) . '</option>';
								if( !empty( $tags ) ) {
									foreach ( $tags as $key => $tag ) {
										echo '<option value="' . esc_attr( $tag ) . '" ' . selected( $shipping_zip, $tag, false )  . '>' . esc_html( $tag ) . '</option>';
									}
								}

							echo '</select>' .
						'</td>' .
					'</tr>';

					/**
					 * - Add new field at the end.
					 *
					 * @var int $post_id
					 */
					do_action( CFSPZW_META_PREFIX . 'add_fields', $post_id );

					echo '<input type="hidden" name="post" value="' . $post_id . '">';
				}
			echo '</tbody>'.
		'</table>' .
	'</div>' .
	'<div class="right-box">';
		/**
		 * Add new post box to display the information.
		 */
		do_action( CFSPZW_PREFIX . '/postbox' );

	echo '</div>' .
'</div>';

// Localize the script with tooltip data for admin option
$translation_array = array(
	'vendor_name'	=> __( '<h3>Vendor Name </h3>' .
						'<p>Get Vendor Name from <a href="#" target="_blank">here</a></p>',
						'accept-sagepay-payments-using-contact-form-7' ),

	'encryption_password'	=> __( '<h3>Encryption Password</h3>' .
						'<p>Get Encryption Password from <a href="#" target="_blank">here</a></p>',
						'accept-sagepay-payments-using-contact-form-7' ),

	'currency'	=> __( '<h3>Select Currency</h3>' .
						'<p>Select the currency.</p>',
						'accept-sagepay-payments-using-contact-form-7' ),

	'amount'	=> __( '<h3>Amount Field</h3>' .
						'<p>Select field from where amount value needs to be retrieved.</p><p><b>Note: </b> Save the FORM details to view the list of fields.</p>',
						'accept-sagepay-payments-using-contact-form-7' ),

	'quantity'	=> __( '<h3>Quantity Field</h3>' .
						'<p>Select field from where quantity value needs to be retrieved.</p><p><b>Note: </b> Save the FORM details to view the list of fields.</p>',
						'accept-sagepay-payments-using-contact-form-7' ),

	'email'	=> __( '<h3>Customer Email Field</h3>' .
						'<p>Select field from where customer email value needs to be retrieved.</p><p><b>Note: </b> Save the FORM details to view the list of fields.</p>',
						'accept-sagepay-payments-using-contact-form-7' ),

	'prefix'	=> __( '<h3>VendorTXCode Prefix Field</h3>' .
						'<p>Please enter unique prefix name which display in invoice order.</p>',
						'accept-sagepay-payments-using-contact-form-7' ),

	'success_returnurl'	=> __( '<h3>Success Return URL Field </h3>' .
						'<p>Select page and redirect customer after succesfully payment done.</p>',
						'accept-sagepay-payments-using-contact-form-7' ),

	'cancel_returnurl'	=> __( '<h3>Cancel Return URL Field  </h3>' .
						'<p>Select page and redirect customer after cancel payment process or payment not done.</p>',
						'accept-sagepay-payments-using-contact-form-7' ),

	'billing_firstnames'	=> __( '<h3>Billing First Name Field</h3>' .
						'<p>Select field from where billing first name value needs to be retrieved.</p><p><b>Note: </b> Save the FORM details to view the list of fields.</p>',
						'accept-sagepay-payments-using-contact-form-7' ),

	'billing_surname'	=> __( '<h3>Billing Last Name Field</h3>' .
						'<p>Select field from where billing last name value needs to be retrieved.</p><p><b>Note: </b> Save the FORM details to view the list of fields.</p>',
						'accept-sagepay-payments-using-contact-form-7' ),

	'billing_address'	=> __( '<h3>Billing Address Field</h3>' .
						'<p>Select field from where billing address value needs to be retrieved.</p><p><b>Note: </b> Save the FORM details to view the list of fields.</p>',
						'accept-sagepay-payments-using-contact-form-7' ),

	'billing_city'	=> __( '<h3>Billing City Field</h3>' .
						'<p>Select field from where billing city value needs to be retrieved.</p><p><b>Note: </b> Save the FORM details to view the list of fields.</p>',
						'accept-sagepay-payments-using-contact-form-7' ),

	'billing_state'	=> __( '<h3>Billing State Field</h3>' .
						'<p>Select field from where billing state value needs to be retrieved.</p><p><b>Note: </b> Save the FORM details to view the list of fields.</p>',
						'accept-sagepay-payments-using-contact-form-7' ),

	'billing_country'	=> __( '<h3>Billing Country Field</h3>' .
						'<p>Select field from where billing country value needs to be retrieved.</p><p><b>Note: </b> Save the FORM details to view the list of fields.</p>',
						'accept-sagepay-payments-using-contact-form-7' ),	

	'billing_zip'	=> __( '<h3>Billing ZipCode Field</h3>' .
						'<p>Select field from where billing zipcode value needs to be retrieved.</p><p><b>Note: </b> Save the FORM details to view the list of fields.</p>',
						'accept-sagepay-payments-using-contact-form-7' ),

	'shipping_firstnames'	=> __( '<h3>Shipping First Name Field</h3>' .
						'<p>Select field from where shipping first name value needs to be retrieved.</p><p><b>Note: </b> Save the FORM details to view the list of fields.</p>',
						'accept-sagepay-payments-using-contact-form-7' ),

	'shipping_surname'	=> __( '<h3>Shipping Last Name Field</h3>' .
						'<p>Select field from where shipping last name value needs to be retrieved.</p><p><b>Note: </b> Save the FORM details to view the list of fields.</p>',
						'accept-sagepay-payments-using-contact-form-7' ),

	'shipping_address'	=> __( '<h3>Shipping Address Field</h3>' .
						'<p>Select field from where shipping address value needs to be retrieved.</p><p><b>Note: </b> Save the FORM details to view the list of fields.</p>',
						'accept-sagepay-payments-using-contact-form-7' ),

	'shipping_city'	=> __( '<h3>Shipping City Field</h3>' .
						'<p>Select field from where shipping city value needs to be retrieved.</p><p><b>Note: </b> Save the FORM details to view the list of fields.</p>',
						'accept-sagepay-payments-using-contact-form-7' ),

	'shipping_state'	=> __( '<h3>Shipping State Field</h3>' .
						'<p>Select field from where shipping state value needs to be retrieved.</p><p><b>Note: </b> Save the FORM details to view the list of fields.</p>',
						'accept-sagepay-payments-using-contact-form-7' ),

	'shipping_country'	=> __( '<h3>Shipping Country Field</h3>' .
						'<p>Select field from where shipping country value needs to be retrieved.</p><p><b>Note: </b> Save the FORM details to view the list of fields.</p>',
						'accept-sagepay-payments-using-contact-form-7' ),

	'shipping_zip'	=> __( '<h3>Shipping ZipCode Field</h3>' .
						'<p>Select field from where shipping zipcode value needs to be retrieved.</p><p><b>Note: </b> Save the FORM details to view the list of fields.</p>',
						'accept-sagepay-payments-using-contact-form-7' )

);

wp_localize_script( CFSPZW_PREFIX . '_admin_js', 'translate_string_sagepay', $translation_array );
