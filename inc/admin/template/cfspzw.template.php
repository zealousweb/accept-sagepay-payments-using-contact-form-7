<?php
/**
* Displays content for plugin option page
*
* @package WordPress
* @subpackage Accept Sagepay Payments Using Contact Form 7
<<<<<<< HEAD
* @since 1.0
* @version 1.0
=======
* @since 1.2
* @version 1.2
>>>>>>> 9645c4c3e76bd1dc9a2ef514757ad7260f743e10
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

<<<<<<< HEAD
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
=======
$use_sagepay					= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'use_sagepay', true ) );
$debug_sagepay					= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'debug', true ) );
$status_val						= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'status', true ) );
$sandbox_vendor_name			= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'sandbox_vendor_name', true ) );
$sandbox_encryption_password	= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'sandbox_encryption_password', true ) );
$live_vendor_name				= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'live_vendor_name', true ) );
$live_encryption_password		= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'live_encryption_password', true ) );
$transaction_type_val			= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'transaction_type', true ) );
$apply3d_val					= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'apply3d', true ) );
$vendor_txcode_prefix			= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'vendor_txcode_prefix', true ) );
$amount							= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'amount', true ) );
$customer_email					= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'customer_email', true ) );
$quantity						= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'quantity', true ) );
$success_return_url				= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'returnurl', true ) );
$cancel_return_url				= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'cancel_returnurl', true ) );
$currency						= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'currency', true ) );
$country						= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'country', true ) );

$billing_firstnames				= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'billing_firstnames', true ) );
$billing_surname				= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'billing_surname', true ) );
$billing_address				= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'billing_address', true ) );
$billing_city					= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'billing_city', true ) );
$billing_state					= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'billing_state', true ) );
$billing_zip					= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'billing_zip', true ) );
$billing_country				= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'billing_country', true ) );

$shipping_firstnames			= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'shipping_firstnames', true ) );
$shipping_surname				= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'shipping_surname', true ) );
$shipping_address				= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'shipping_address', true ) );
$shipping_city					= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'shipping_city', true ) );
$shipping_state					= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'shipping_state', true ) );
$shipping_zip					= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'shipping_zip', true ) );
$shipping_country				= sanitize_text_field( get_post_meta( $post_id, CFSPZW_META_PREFIX . 'shipping_country', true ) );
$cfspzw_review					= get_option( 'cfspzw_review' );
>>>>>>> 9645c4c3e76bd1dc9a2ef514757ad7260f743e10

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
<<<<<<< HEAD
	$cf7 = WPCF7_ContactForm::get_instance($_REQUEST['post']);
=======
	$cf7 = WPCF7_ContactForm::get_instance( sanitize_text_field( $_REQUEST['post'] ) );
>>>>>>> 9645c4c3e76bd1dc9a2ef514757ad7260f743e10
	$tags = $cf7->collect_mail_tags();
}

echo '<div class="cfspzw-settings">' .
<<<<<<< HEAD
	'<div class="left-box postbox">' .
=======
	'<div class="modal fade" id="myModal" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
				<div class="inner-modal">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">'. __('Support us!', 'accept-2checkout-payments-using-contact-form-7').'</h4>
					</div>
					<div class="modal-body">
						<p>' . __('If you like this plugin please spare some time to review us.', 'accept-2checkout-payments-using-contact-form-7').'</p>
					</div>
					<div class="modal-footer">
						<a href="https://wordpress.org/support/plugin/accept-2checkout-payments-using-contact-form-7/reviews/" class="button primary-button review-cfspzw" target="_blank">' . __('Review us', 'accept-2checkout-payments-using-contact-form-7'). '</a>
						<button type="button" class="btn btn-default remind-cfspzw" data-dismiss="modal">'. __('Remind Me Later', 'accept-2checkout-payments-using-contact-form-7').'</button>
					</div>
					<div class="bird-icon">
						<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="71.366" height="49.822" viewBox="0 0 71.366 49.822"><defs><linearGradient id="a" x1="0.121" y1="0.5" x2="1.122" y2="0.5" gradientUnits="objectBoundingBox"><stop offset="0" stop-color="#3daeb5"/><stop offset="0.23" stop-color="#56c5d0"/><stop offset="0.505" stop-color="#56c5d0"/><stop offset="0.887" stop-color="#0074a2"/></linearGradient><linearGradient id="b" x1="0.142" y1="-0.312" x2="1.28" y2="1.261" gradientUnits="objectBoundingBox"><stop offset="0" stop-color="#56c5d0"/><stop offset="0.5" stop-color="#0074a2"/><stop offset="1" stop-color="#22566e"/></linearGradient><linearGradient id="c" x1="0.001" y1="0.5" x2="0.996" y2="0.5" gradientUnits="objectBoundingBox"><stop offset="0" stop-color="#c81f66"/><stop offset="0.446" stop-color="#f05b89"/><stop offset="1" stop-color="#c81f66"/></linearGradient><linearGradient id="d" x1="0.023" y1="0.477" x2="0.997" y2="0.477" gradientUnits="objectBoundingBox"><stop offset="0" stop-color="#ffc93e"/><stop offset="1" stop-color="#f69047"/></linearGradient><linearGradient id="e" x1="-0.009" y1="0.5" x2="1.091" y2="0.5" gradientUnits="objectBoundingBox"><stop offset="0" stop-color="#ed1651"/><stop offset="1" stop-color="#f05b7d"/></linearGradient><linearGradient id="f" y1="0.5" x2="1" y2="0.5" gradientUnits="objectBoundingBox"><stop offset="0" stop-color="#22566e"/><stop offset="0.992" stop-color="#3daeb5"/></linearGradient><linearGradient id="g" y1="0.5" x2="1" y2="0.5" gradientUnits="objectBoundingBox"><stop offset="0" stop-color="#0074a2"/><stop offset="1" stop-color="#56c5d0"/></linearGradient></defs><g transform="translate(-6211.895 1682)"><path d="M657.551,270.4H653.7v.708a2.425,2.425,0,0,0,2.417,2.418h3.851v-.708A2.424,2.424,0,0,0,657.551,270.4Z" transform="translate(5609.742 -1905.704)" fill="#0074a2"/><path d="M615.251,270.4H611.4v.708a2.424,2.424,0,0,0,2.418,2.418h3.851v-.708a2.424,2.424,0,0,0-2.418-2.418Z" transform="translate(5644.736 -1905.704)" fill="#f05b89"/><path d="M572.951,270.4H569.1v.708a2.424,2.424,0,0,0,2.418,2.418h3.851v-.708A2.425,2.425,0,0,0,572.951,270.4Z" transform="translate(5679.73 -1905.704)" fill="#f79548"/><g transform="translate(6211.895 -1682)"><path d="M396.147,61.5c-5.544-1.9-8.065-4.127-11.1-6.735s-4.093-4.491-5.838-6.649c-1.76-2.158-3.727-6.269-6.77-9.309s-8.635-3.416-12.676-1.261a9.288,9.288,0,0,0-4.555,6.6c5.406-.828,7.634,3.368,9.239,6.649s1.83,6.839,2.158,9.171c1.122,7.91,6.217,12.521,12.123,13.919,2.021.553,7.53,2.07,9,2.642,0,0-3.178-1.623-4.006-2.21H383.7c.553-.034,1.623-.138,2.174-.225a14.6,14.6,0,0,1,3.61,1.174s-2.124-1.537-2.158-1.537a17.242,17.242,0,0,0,7.081-3.886c.1-.1.207-.19.311-.294a19.955,19.955,0,0,1,4.577-3.16,29.966,29.966,0,0,1,11.122-3.039C403.5,63.039,398.254,62.228,396.147,61.5Z" transform="translate(-355.204 -29.935)" fill="url(#a)"/><g transform="translate(39.216 33.296)"><path d="M582.3,199.425a12.265,12.265,0,0,1,8.1-1.486c4.006.656,8.342,4.438,14.04,5.561.294.069.587.138.881.19,4.244.708,9.394-1.088,9.118-4.093C613.667,191.152,591.885,190.029,582.3,199.425Z" transform="translate(-582.3 -192.812)" fill="url(#b)"/></g><path d="M390.757,59.183a.558.558,0,0,1-.587.57.579.579,0,1,1,.587-.57Z" transform="translate(-383.663 -48.478)" fill="#fff"/><path d="M483.85,49.384c-4.179.587-8,1.261-11.485,1.987-37.735,7.944-36.2,24.126-31.38,29.135,13.332,13.9,40.188-25.8,43.227-30.706C484.351,49.591,484.127,49.35,483.85,49.384Z" transform="translate(-424.118 -40.855)" fill="url(#c)"/><path d="M471.249.044c-3.9,1.71-7.409,3.385-10.587,5.026-34.384,17.788-28.5,33.055-21.76,36.146,18.841,8.635,31.345-35.4,32.83-40.878C471.8.1,471.508-.06,471.249.044Z" transform="translate(-420.186 -0.011)" fill="url(#d)"/><path d="M433.693,84.508c.138-6.562,6.632-15.491,32.485-17.46.38-.881.794-2.07,1.242-3.209a5.587,5.587,0,0,1,.225-.57c.346-.9.639-1.727.863-2.366C440.636,66.616,433.572,77.548,433.693,84.508Z" transform="translate(-420.14 -50.384)" fill="url(#e)"/><path d="M489.539,94.923c-2.953-.1-6.51-.155-9.136-.138-7.478,9.36-20.793,25.9-31.414,26.319a9.764,9.764,0,0,1-1.606-.121h-.069a10.178,10.178,0,0,1-5.492-2.8c-1.382-1.313-2.021-2.522-1.191-1.33,11.226,15.854,45.161-17.391,49.185-21.467C490.006,95.2,489.833,94.939,489.539,94.923Z" transform="translate(-425.575 -78.415)" fill="url(#f)"/><path d="M475.131,94.8c-1.157,0-2.315.018-3.416.052-30.1.76-37.873,10.448-38.012,17.253a8.765,8.765,0,0,0,2.522,6.1,10.111,10.111,0,0,0,5.422,2.8h.069a15.594,15.594,0,0,0,1.589.121C454.355,121.119,468.172,104.367,475.131,94.8Z" transform="translate(-420.147 -78.43)" fill="url(#g)"/><path d="M466.105,96.6c-25.456,1.882-32.364,10.707-32.505,16.994a8.592,8.592,0,0,0,2.588,6.1,11.153,11.153,0,0,0,2.21,1.623,11.852,11.852,0,0,0,3.9,1.226c.38.034.76.052,1.139.069a13.52,13.52,0,0,0,2.608-.276C454.773,120.225,461.768,107.325,466.105,96.6Z" transform="translate(-420.065 -79.919)" fill="url(#g)"/></g></g></svg>
					</div>
				</div>
			</div>

		</div>
	</div>
	<div class="left-box postbox">' .
>>>>>>> 9645c4c3e76bd1dc9a2ef514757ad7260f743e10
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
<<<<<<< HEAD
						'accept-sagepay-payments-using-contact-form-7' )

);

wp_localize_script( CFSPZW_PREFIX . '_admin_js', 'translate_string_sagepay', $translation_array );
=======
						'accept-sagepay-payments-using-contact-form-7' ),
	'cfspzw_review'		=> $cfspzw_review,

);

wp_enqueue_script( CFSPZW_PREFIX . '_modal_js' );
wp_enqueue_script( CFSPZW_PREFIX . '_cookie_js' );
wp_localize_script( CFSPZW_PREFIX . '_admin_js', 'cfspzw_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'translate_string_cfspzw' => $translation_array ) );
>>>>>>> 9645c4c3e76bd1dc9a2ef514757ad7260f743e10
