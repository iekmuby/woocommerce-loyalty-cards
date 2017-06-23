function hidePreviousNotices() {
	jQuery('.woocommerce-message').hide();
	jQuery('.woocommerce-error').hide();
	jQuery('.woocommerce-info').hide();
}
function removeLoyaltyCard() {
		jQuery.ajax({
			url: wc_add_to_cart_params.ajax_url,
			data: {
				security: sha_wlc_nonce,
				act: 'remove_card',
				action: 'loyalty_card_ajax',
			},
			method: 'post'
		}).done(function(data) {
			if (data.status == 'success') {
				hidePreviousNotices();
				$body = jQuery('body');
				if ( $body.hasClass('woocommerce-checkout') ) {
					$body.trigger( 'update_checkout' );
				} else {
					$body.trigger( 'wc_update_cart' );
				}
			}
		});
	}

jQuery(document).ready(function() {
	jQuery('#cardAddButton').on('click', function() {
		jQuery.ajax({
			url: wc_add_to_cart_params.ajax_url,
			data: {
				card: jQuery('#loyaltyCard').val(),
				act: 'add_card',
				security: sha_wlc_nonce,
				action: 'loyalty_card_ajax'
			},
			method: 'post'
		}).done(function(data) {
			if (data.status == 'success') {
				hidePreviousNotices();
				$body = jQuery('body');
				if ( $body.hasClass('woocommerce-checkout') ) {
					$body.trigger( 'update_checkout' );
				} else {
					$body.trigger( 'wc_update_cart' );
				}
			}
		});
	});
	
	jQuery(document).ready(function() {
		jQuery('#loyaltyCard').mask('0000000000000000');
	});
});