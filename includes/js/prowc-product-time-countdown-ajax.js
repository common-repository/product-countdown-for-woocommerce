/**
 * Product Time Countdown for WooCommerce - Countdown AJAX
 *
 * @version 1.4.0
 * @since   1.0.0
 * @author  ProWCPlugins
 */

var product_ids = [];

function update_all() {
	var data = {
		'action': 'prowc_product_countdown',
		'product_ids': product_ids,
	};
	jQuery.post(prowc_data_countdown.ajax_url, data, function(response) {
		if ( '' != response ) {
			response = JSON.parse(response);
			jQuery( '.prowc_product_countdown' ).each( function() {
				var response_product = response[jQuery(this).attr('product_id')];
				if ( '' == response_product ) {
					if ( prowc_data_countdown.do_reload ) {
						location.reload();
					} else {
						response_product = prowc_data_countdown.end_message;
						jQuery(this).attr('class', 'prowc_product_countdown_time_ended');
					}
				}
				jQuery(this).html(response_product);
			});
		}
	});
}

jQuery( document ).ready( function() {
	jQuery( '.prowc_product_countdown' ).each( function() {
		product_ids.push(jQuery(this).attr('product_id'));
	} );
	if ( 0 != product_ids.length ) {
		update_all();
		setInterval( update_all, parseInt( prowc_data_countdown.update_rate_ms ) );
	}
});
