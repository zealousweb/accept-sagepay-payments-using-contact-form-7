( function($) {

	"use strict";
	function cfspzw_validate() {
		if ( jQuery( '.cfspzw-settings #cfspzw_use_sagepay' ).prop( 'checked' ) == true ) {

			jQuery('.cfspzw-settings .form-required-fields').each(function() {
				jQuery( jQuery(this) ).prop( 'required', true );
			});

		} else {

			jQuery('.cfspzw-settings .form-required-fields').each(function() {
				jQuery( jQuery(this) ).removeAttr( 'required' );
			});
		}
	}

	function cfspzw_sandbox_validate() {
		if ( jQuery( '.cfspzw-settings #cfspzw_status' ).val() == 'sandbox' && jQuery( '.cfspzw-settings #cfspzw_use_sagepay' ).prop( 'checked' ) == true) {
			jQuery( '.cfspzw-settings #cfspzw_sandbox_vendor_name, .cfspzw-settings #cfspzw_sandbox_encryption_password' ).prop( 'required', true );
		} else {
			jQuery( '.cfspzw-settings #cfspzw_sandbox_vendor_name, .cfspzw-settings #cfspzw_sandbox_encryption_password' ).removeAttr( 'required' );
		}
	}

	function cfspzw_live_validate() {
		if ( jQuery( '.cfspzw-settings #cfspzw_status' ).val() == 'live' && jQuery( '.cfspzw-settings #cfspzw_use_sagepay' ).prop( 'checked' ) == true ) {
			jQuery( '.cfspzw-settings #cfspzw_live_vendor_name, .cfspzw-settings #cfspzw_live_encryption_password' ).prop( 'required', true );
		} else {
			jQuery( '.cfspzw-settings #cfspzw_live_vendor_name, .cfspzw-settings #cfspzw_live_encryption_password' ).removeAttr( 'required' );
		}
	}

	if ( jQuery( '.cfspzw-settings #cfspzw_status' ).val() != '' && jQuery( '.cfspzw-settings #cfspzw_use_sagepay' ).prop( 'checked' ) == true ) {
		cfspzw_live_validate();
		cfspzw_sandbox_validate();
	}

	if ( jQuery( '.cfspzw-settings #cfspzw_amount' ).val() == '' && jQuery( '.cfspzw-settings #cfspzw_use_sagepay' ).prop( 'checked' ) == true ) {
		cfspzw_validate();
	}

	/**
	 * Validate field according payment mode selected
	 */
	jQuery( document ).on( 'change', '.cfspzw-settings #cfspzw_status', function() {
		cfspzw_live_validate();
		cfspzw_sandbox_validate();
	} );

	/**
	 * Validate sagepay admin option whene plugin functionality enabled for particular form
	 */
	jQuery( document ).on( 'change', '.cfspzw-settings .enable_required', function() {
		cfspzw_validate();
		cfspzw_sandbox_validate();
		cfspzw_live_validate();
	} );


	function check_sagepay_field_validation(){

		cfspzw_validate();
		cfspzw_sandbox_validate();
		cfspzw_live_validate();

		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );

		if ( jQuery( '.cfspzw-settings #cfspzw_status' ).val() == 'live' && jQuery( '.cfspzw-settings #cfspzw_use_sagepay' ).prop( 'checked' ) == true ) {
			if(
				jQuery( '.cfspzw-settings #cfspzw_live_vendor_name' ).val() == '' ||
				jQuery( '.cfspzw-settings #cfspzw_live_encryption_password').val() == ''
			){
				jQuery("#sagepay-add-on-tab .ui-tabs-anchor").find('span').remove();
				jQuery("#sagepay-add-on-tab .ui-tabs-anchor").append('<span class="icon-in-circle" aria-hidden="true">!</span>');
			}else{
				jQuery("#sagepay-add-on-tab .ui-tabs-anchor").find('span').remove();
			}
		}

		if ( jQuery( '.cfspzw-settings #cfspzw_status' ).val() == 'sandbox' && jQuery( '.cfspzw-settings #cfspzw_use_sagepay' ).prop( 'checked' ) == true ) {
			if(
				jQuery( '.cfspzw-settings #cfspzw_sandbox_vendor_name' ).val() == '' ||
				jQuery( '.cfspzw-settings #cfspzw_sandbox_encryption_password').val() == ''
			){
				jQuery("#sagepay-add-on-tab .ui-tabs-anchor").find('span').remove();
				jQuery("#sagepay-add-on-tab .ui-tabs-anchor").append('<span class="icon-in-circle" aria-hidden="true">!</span>');
			}else{
				jQuery("#sagepay-add-on-tab .ui-tabs-anchor").find('span').remove();
			}
		}

		if( jQuery( '.cfspzw-settings #cfspzw_use_sagepay' ).prop( 'checked' ) == true ){

			 jQuery('.cfspzw-settings .form-required-fields').each(function() {
				if (jQuery.trim(jQuery(this).val()) == '') {
					jQuery("#sagepay-add-on-tab .ui-tabs-anchor").find('span').remove();
					jQuery("#sagepay-add-on-tab .ui-tabs-anchor").append('<span class="icon-in-circle" aria-hidden="true">!</span>');
				}
			 });

		}else{
			jQuery("#sagepay-add-on-tab .ui-tabs-anchor").find('span').remove();
		}
	}

	/**
	 * Validate sagepay admin option required fields
	 */
	jQuery( document ).ready( function() {

		if(cfspzw_object.translate_string_cfspzw.cfspzw_review != 1){
			if (typeof Cookies.get('review_cfspzw') === 'undefined'){ // no cookie
				jQuery('#myModal').modal('show');
				Cookies.set('review_cfspzw', 'yes', { expires: 15 }); // set cookie expiry to 15 day
			}
		}

		jQuery(".review-cfspzw, .remind-cfspzw").click(function(){
			jQuery("#myModal").modal('hide');
		});

		jQuery(".review-cfspzw").click(function(){
			jQuery.ajax({
				type: "post",
				dataType: "json",
				url: cfspzw_object.ajax_url,
				data: 'action=cfspzw_review_done&value=1',
				success: function(){
				}
			});
		});

		check_sagepay_field_validation();

	});


	jQuery( document ).on('click',".ui-state-default",function() {
		check_sagepay_field_validation();
	});

	/**
	 * Remove Conatct from 7 if plugin required field is there.
	 */
	jQuery(document).on('click','input[name="wpcf7-delete"]',function(){
		jQuery('.cfspzw-settings #cfspzw_sandbox_vendor_name,.cfspzw-settings #cfspzw_sandbox_encryption_password,.cfspzw-settings #cfspzw_live_vendor_name,.cfspzw-settings #cfspzw_live_encryption_password').removeAttr( 'required' );
		
		jQuery('.cfspzw-settings .form-required-fields').each(function() {
			jQuery( jQuery(this) ).removeAttr( 'required' );
		});
	});

	
	/**
	* Apply sagepay dunctionality for dropdown box
	*/
	jQuery( document ).ready( function() {
		jQuery('.cfspzw-settings #cfspzw_status, .cfspzw-settings #cfspzw_currency, .cfspzw-settings #cfspzw_returnurl, .cfspzw-settings #cfspzw_transaction_type,.cfspzw-settings #cfspzw_apply3d,.cfspzw-settings #cfspzw_amount,.cfspzw-settings #cfspzw_quantity,.cfspzw-settings #cfspzw_cancel_returnurl').select2();

		jQuery('.cfspzw-settings .form-required-fields').each(function() {
			jQuery( jQuery(this) ).select2();
		});
	});


	/**
	* Show and hide tooltip logic
	*/
	jQuery( '#cfspzw-sandbox-vendor-name' ).on( 'mouseenter click', function() {
		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#cfspzw-sandbox-vendor-name' ).pointer({
			pointerClass: 'wp-pointer cfspzw-pointer',
			content: cfspzw_object.translate_string_cfspzw.vendor_name,
			position: 'left center',
		}).pointer('open');
	});

	jQuery( '#cfspzw-sandbox-encryption-password' ).on( 'mouseenter click', function() {
		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#cfspzw-sandbox-encryption-password' ).pointer({
			pointerClass: 'wp-pointer cfspzw-pointer',
			content: cfspzw_object.translate_string_cfspzw.encryption_password,
			position: 'left center',
		}).pointer('open');
	});

	jQuery( '#cfspzw-live-vendor-name' ).on( 'mouseenter click', function() {
		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#cfspzw-live-vendor-name' ).pointer({
			pointerClass: 'wp-pointer cfspzw-pointer',
			content: cfspzw_object.translate_string_cfspzw.vendor_name,
			position: 'left center',
		}).pointer('open');
	});

	jQuery( '#cfspzw-live-encryption-password' ).on( 'mouseenter click', function() {
		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#cfspzw-live-encryption-password' ).pointer({
			pointerClass: 'wp-pointer cfspzw-pointer',
			content: cfspzw_object.translate_string_cfspzw.encryption_password,
			position: 'left center',
		}).pointer('open');
	});

	jQuery( '#cfspzw-select-currency' ).on( 'mouseenter click', function() {
		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#cfspzw-select-currency' ).pointer({
			pointerClass: 'wp-pointer cfspzw-pointer',
			content: cfspzw_object.translate_string_cfspzw.currency,
			position: 'left center',
		}).pointer('open');
	});

	jQuery( '#cfspzw-amount' ).on( 'mouseenter click', function() {
		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#cfspzw-amount' ).pointer({
			pointerClass: 'wp-pointer cfspzw-pointer',
			content: cfspzw_object.translate_string_cfspzw.amount,
			position: 'left center',
		}).pointer('open');
	});

	jQuery( '#cfspzw-quantity' ).on( 'mouseenter click', function() {
		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#cfspzw-quantity' ).pointer({
			pointerClass: 'wp-pointer cfspzw-pointer',
			content: cfspzw_object.translate_string_cfspzw.quantity,
			position: 'left center',
		}).pointer('open');
	});

	jQuery( '#cfspzw-email' ).on( 'mouseenter click', function() {
		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#cfspzw-email' ).pointer({
			pointerClass: 'wp-pointer cfspzw-pointer',
			content: cfspzw_object.translate_string_cfspzw.email,
			position: 'left center',
		}).pointer('open');
	});

	jQuery( '#cfspzw-prefix' ).on( 'mouseenter click', function() {
		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#cfspzw-prefix' ).pointer({
			pointerClass: 'wp-pointer cfspzw-pointer',
			content: cfspzw_object.translate_string_cfspzw.prefix,
			position: 'left center',
		}).pointer('open');
	});

	jQuery( '#cfspzw-success-returnurl' ).on( 'mouseenter click', function() {
		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#cfspzw-success-returnurl' ).pointer({
			pointerClass: 'wp-pointer cfspzw-pointer',
			content: cfspzw_object.translate_string_cfspzw.success_returnurl,
			position: 'left center',
		}).pointer('open');
	});

	jQuery( '#cfspzw-cancel-returnurl' ).on( 'mouseenter click', function() {
		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#cfspzw-cancel-returnurl' ).pointer({
			pointerClass: 'wp-pointer cfspzw-pointer',
			content: cfspzw_object.translate_string_cfspzw.cancel_returnurl,
			position: 'left center',
		}).pointer('open');
	});

	jQuery( '#cfspzw-billing-firstnames' ).on( 'mouseenter click', function() {
		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#cfspzw-billing-firstnames' ).pointer({
			pointerClass: 'wp-pointer cfspzw-pointer',
			content: cfspzw_object.translate_string_cfspzw.billing_firstnames,
			position: 'left center',
		}).pointer('open');
	});

	jQuery( '#cfspzw-billing-surname' ).on( 'mouseenter click', function() {
		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#cfspzw-billing-surname' ).pointer({
			pointerClass: 'wp-pointer cfspzw-pointer',
			content: cfspzw_object.translate_string_cfspzw.billing_surname,
			position: 'left center',
		}).pointer('open');
	});

	jQuery( '#cfspzw-billing-address' ).on( 'mouseenter click', function() {
		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#cfspzw-billing-address' ).pointer({
			pointerClass: 'wp-pointer cfspzw-pointer',
			content: cfspzw_object.translate_string_cfspzw.billing_address,
			position: 'left center',
		}).pointer('open');
	});

	jQuery( '#cfspzw-billing-city' ).on( 'mouseenter click', function() {
		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#cfspzw-billing-city' ).pointer({
			pointerClass: 'wp-pointer cfspzw-pointer',
			content: cfspzw_object.translate_string_cfspzw.billing_city,
			position: 'left center',
		}).pointer('open');
	});

	jQuery( '#cfspzw-billing-state' ).on( 'mouseenter click', function() {
		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#cfspzw-billing-state' ).pointer({
			pointerClass: 'wp-pointer cfspzw-pointer',
			content: cfspzw_object.translate_string_cfspzw.billing_state,
			position: 'left center',
		}).pointer('open');
	});

	jQuery( '#cfspzw-billing-country' ).on( 'mouseenter click', function() {
		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#cfspzw-billing-country' ).pointer({
			pointerClass: 'wp-pointer cfspzw-pointer',
			content: cfspzw_object.translate_string_cfspzw.billing_country,
			position: 'left center',
		}).pointer('open');
	});


	jQuery( '#cfspzw-billing-zip' ).on( 'mouseenter click', function() {
		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#cfspzw-billing-zip' ).pointer({
			pointerClass: 'wp-pointer cfspzw-pointer',
			content: cfspzw_object.translate_string_cfspzw.billing_zip,
			position: 'left center',
		}).pointer('open');
	});

	jQuery( '#cfspzw-shipping-firstnames' ).on( 'mouseenter click', function() {
		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#cfspzw-shipping-firstnames' ).pointer({
			pointerClass: 'wp-pointer cfspzw-pointer',
			content: cfspzw_object.translate_string_cfspzw.shipping_firstnames,
			position: 'left center',
		}).pointer('open');
	});

	jQuery( '#cfspzw-shipping-surname' ).on( 'mouseenter click', function() {
		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#cfspzw-shipping-surname' ).pointer({
			pointerClass: 'wp-pointer cfspzw-pointer',
			content: cfspzw_object.translate_string_cfspzw.shipping_surname,
			position: 'left center',
		}).pointer('open');
	});

	jQuery( '#cfspzw-shipping-address' ).on( 'mouseenter click', function() {
		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#cfspzw-shipping-address' ).pointer({
			pointerClass: 'wp-pointer cfspzw-pointer',
			content: cfspzw_object.translate_string_cfspzw.shipping_address,
			position: 'left center',
		}).pointer('open');
	});

	jQuery( '#cfspzw-shipping-city' ).on( 'mouseenter click', function() {
		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#cfspzw-shipping-city' ).pointer({
			pointerClass: 'wp-pointer cfspzw-pointer',
			content: cfspzw_object.translate_string_cfspzw.shipping_city,
			position: 'left center',
		}).pointer('open');
	});

	jQuery( '#cfspzw-shipping-state' ).on( 'mouseenter click', function() {
		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#cfspzw-shipping-state' ).pointer({
			pointerClass: 'wp-pointer cfspzw-pointer',
			content: cfspzw_object.translate_string_cfspzw.shipping_state,
			position: 'left center',
		}).pointer('open');
	});

	jQuery( '#cfspzw-shipping-country' ).on( 'mouseenter click', function() {
		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#cfspzw-shipping-country' ).pointer({
			pointerClass: 'wp-pointer cfspzw-pointer',
			content: cfspzw_object.translate_string_cfspzw.shipping_country,
			position: 'left center',
		}).pointer('open');
	});

	jQuery( '#cfspzw-shipping-zip' ).on( 'mouseenter click', function() {
		jQuery( 'body .wp-pointer-buttons .close' ).trigger( 'click' );
		jQuery( '#cfspzw-shipping-zip' ).pointer({
			pointerClass: 'wp-pointer cfspzw-pointer',
			content: cfspzw_object.translate_string_cfspzw.shipping_zip,
			position: 'left center',
		}).pointer('open');
	} );

} )( jQuery );
