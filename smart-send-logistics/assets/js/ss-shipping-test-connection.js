/**
/**
 * Ajax call to test connection to Smart Send
 */
function ssTestConnection( btn_id ) {
	var btn = jQuery( btn_id );

	// Remove elements after button
	btn.nextAll().remove();

	btn.attr("disabled", true);
	btn.text( ss_test_connection_obj.validating_connection );

	var loaderContainer = jQuery( '<span/>', {
        'class': 'loader-image-container'
    }).insertAfter( btn );

    var loader = jQuery( '<img/>', {
        src: '/wp-admin/images/loading.gif',
        'class': 'loader-image'
    }).appendTo( loaderContainer );

	var data = {
		'action': 'ss_test_connection',
		'test_connection_nonce': ss_test_connection_obj.test_connection_nonce
	};

	jQuery.post(ss_test_connection_obj.ajax_url, data, function(response) {
		btn.attr("disabled", false);
		btn.text( response.button_txt );
		loaderContainer.remove();

		if ( response.error ) {
			var test_connection_class = 'error ss-connection';
		} else {
			var test_connection_class = 'updated ss-connection';
		}
		
		var test_connection_text = response.message;

		loaderContainer = jQuery( '<div/>', {
	        'class': test_connection_class
	    }).insertAfter( btn );

		loaderContainer.append( test_connection_text );
	});
}
