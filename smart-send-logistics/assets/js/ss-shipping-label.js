jQuery(function ($) {

    var ss_shipping_label_items = {
        // init Class
        init: function () {
            $('#ss-shipping-label-form')
                .on('click', '#ss-shipping-label-button', {return_label: 0}, this.save_ss_shipping_label);
            $('#ss-shipping-label-form')
                .on('click', '#ss-shipping-return-label-button', {return_label: 1}, this.save_ss_shipping_label);
            $('#ss-shipping-label-form')
                .on('click', '#ss-shipping-split-parcels', {}, this.show_parcel_option);
                
        },

        show_parcel_option: function() {
            if ($(this).is(':checked')) {
                $('#ss-shipping-order-items').show();
            } else {
                $('#ss-shipping-order-items').hide();
            }
        },

        get_parcels_input: function() {
            var parcels = [];
            $('select[name="ss_shipping_box_no\[\]"]').each(function() {
                parcels.push({
                    id: $(this).data('id'),
                    name: $(this).data('name'),
                    value: $(this).val()
                });
            });

            return parcels;
        },

        save_ss_shipping_label: function (event) {
            // Remove any errors from last attempt to create label
            $('#ss-shipping-label-form .error').remove();
            $('#ss-shipping-label-form .updated').remove();

            // Block metabox while doing AJAX
            $('#ss-shipping-label-form').block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });

            var data = {
                action: 'ss_shipping_generate_label',
                order_id: woocommerce_admin_meta_boxes.post_id,
                return_label: event.data.return_label,
                ss_shipping_agent_no: $('#ss_shipping_agent_no').val(),
                ss_shipping_label_nonce: $('#ss_shipping_label_nonce').val(),
                ss_shipping_parcels: ss_shipping_label_items.get_parcels_input(),
                ss_shipping_split_parcel: $('input#ss-shipping-split-parcels').is(':checked') ? 1 : 0
            };

            // AJAX call with order data to create label
            $.post(woocommerce_admin_meta_boxes.ajax_url, data, function (response) {
                // Unblock AJAX
                $('#ss-shipping-label-form').unblock();

                // Loop through response, could be two if we are creating return label as well as normal label
                $.each( response, function( key, value ) {
                    
                    // Add error to metabox if exists
                    if (value.error) {
                         $('#ss-shipping-label-form').append('<div id="ss-shipping-error" class="error ss-meta-message">' + value.error + '</div>'); 

                    } else if (value.success.woocommerce) {
                        
                        // If return label, place correct link
                        if( value.success.woocommerce.return ) {
                            $('#ss-shipping-label-form').append('<div id="ss-label-created" class="updated ss-meta-message"><a href="' + value.success.woocommerce.label_url + '" target="_blank">' + ss_label_data.download_return_label + '</a></div>');
                        } else {
                            $('#ss-shipping-label-form').append('<div id="ss-label-created" class="updated ss-meta-message"><a href="' + value.success.woocommerce.label_url + '" target="_blank">' + ss_label_data.download_label + '</a></div>');
                        }

                        // Add order note with tracking info
                        if (value.success.woocommerce.order_note) {

                            $('#woocommerce-order-notes').block({
                                message: null,
                                overlayCSS: {
                                    background: '#fff',
                                    opacity: 0.6
                                }
                            });

                            var data = {
                                action: 'woocommerce_add_order_note',
                                post_id: woocommerce_admin_meta_boxes.post_id,
                                note_type: '',
                                note: value.success.woocommerce.order_note,
                                security: woocommerce_admin_meta_boxes.add_order_note_nonce
                            };

                            // Order note AJAX call
                            $.post(woocommerce_admin_meta_boxes.ajax_url, data, function (response_note) {
                                // alert(response_note);
                                $('ul.order_notes').prepend(response_note);
                                $('#woocommerce-order-notes').unblock();
                                $('#add_order_note').val('');
                            });
                        }

                    } else {
                        // Print error message
                        $('#ss-shipping-label-form').append('<div id="ss-shipping-error" class="error ss-meta-message"><strong>' + ss_label_data.unexpected_error + '</strong></div>');
                    }
                });

            });

            return false;
        },
    }

    // Init object
    ss_shipping_label_items.init();

});
