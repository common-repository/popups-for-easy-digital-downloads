// See ../license.txt for licensing information applicable to the plugin containing this file

window.jQuery(document).ready(function($) {
	
	window.wpz_wppop_edd = {};
	
	function getPopupsByTrigger(trigger) {
		var popupIds = [];
		if (window.wppopups) {
			for (var popupId in window.wppopups.popups) {
				var settings = JSON.parse($(window.wppopups.popups[popupId]).attr('data-settings'));
				
				if (settings && settings.triggers) {
					for (var triggerId in settings.triggers) {
						if (settings.triggers[triggerId].trigger === trigger) {
							popupIds.push(popupId);
						}
					}
				}
			}
		}
		return popupIds;
	}
	
	window.wpz_wppop_edd.handlePopupTrigger = function(ev, trigger) {
		if (ev.originalEvent.isTrusted) {
			var popupIds = getPopupsByTrigger(trigger);
			if (popupIds.length) {
				ev.stopImmediatePropagation();
				var popupTriggerElem = ev.target;
				
				var handleConvert = function() {
					popupTriggerElem.click();
				};
				
				var removePopupEvents = function() {
					$(document).off('wppopups.popup_converted', handleConvert);
					$(document).off('wppopups.popup_closed', removePopupEvents);
				};
				
				$(document).on('wppopups.popup_converted', handleConvert);
				$(document).on('wppopups.popup_closed', removePopupEvents);
				
				popupIds.map(window.wppopups.showPopup);
			}
			
		}
	}
	
	$( window.document.body ).on('click.eddAddToCart', '.edd-add-to-cart', function( ev ) {
		window.wpz_wppop_edd.handlePopupTrigger(ev, 'wpz-edd-cart-add-pre');
		return false;
	});
	
	$( window.document.body ).on('edd_cart_item_added', function( ev ) {
		if (window.wppopups) {
			getPopupsByTrigger('wpz-edd-cart-add').map(window.wppopups.showPopup);
		}
	});
	
	
});