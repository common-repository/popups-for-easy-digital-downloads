// See ../license.txt for licensing information applicable to the plugin containing this file

window.jQuery(document).ready(function($) {
	
	$( window.document.body ).on('click', '.edd_subscription_cancel', function( ev ) {
		window.wpz_wppop_edd.handlePopupTrigger(ev, 'wpz-edd-subscription-cancel-pre');
	});
	
});