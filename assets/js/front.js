( function(jQuery) {
	"use strict";

jQuery( document ).ready( function( $ ) {

	if (jQuery('body').find(".sagepay-country").length > 0){ 

		jQuery('.sagepay-country').select2();
	}

	document.addEventListener('wpcf7mailsent', function( event ) {
		if( event.detail.apiResponse.redirect_form ) {
			setTimeout(function(){
				var contactform_id = event.detail.contactFormId;
				var formdata = event.detail.apiResponse.redirect_form;
				document.getElementById(event.detail.id).innerHTML += formdata;
				document.getElementById("sagepay-payment-form-"+event.detail.contactFormId).submit();
			}, 1000);
		}
	} );
} );

} )( jQuery );