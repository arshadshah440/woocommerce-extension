<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * WooCommerce Product
 *
 * @package  SS_Shipping_WC_Product
 * @category Product
 * @author   Smart Send
 */

if (!class_exists('SS_Shipping_WC_Product')) :

    class SS_Shipping_WC_Product
    {

        /**
         * Init and hook in the integration.
         */
        public function __construct()
        {
            // priority is '8' because WC Subscriptions hides fields in the shipping tabs which hide the fields here
            add_action('woocommerce_product_options_shipping', array($this, 'additional_product_shipping_options'), 8);
            add_action('woocommerce_process_product_meta', array($this, 'save_additional_product_shipping_options'));
        }

        /**
         * Add the meta box for shipment info on the order page
         */
        public function additional_product_shipping_options()
        {
            global $thepostid, $post;

            $thepostid = empty($thepostid) ? $post->ID : $thepostid;

	        $countries_obj   = new WC_Countries();
	        $options = $countries_obj->__get('countries');
	        $options = array('' => __('Select country', 'smart-send-logistics')) + $options;// A select to the top
	        woocommerce_wp_select(
		        array(
			        'id'          => '_ss_country_of_origin',
			        'label'       => __('Country of origin', 'smart-send-logistics'),
			        'description' => __('ISO3166-alpha2 code of the country where the item was produced', 'smart-send-logistics'),
			        'desc_tip'    => 'true',
			        'options'     => $options,
		        )
	        );

	        woocommerce_wp_text_input(
		        array(
			        'id'          => '_ss_customs_desc',
			        'label'       => __('Customs description', 'smart-send-logistics'),
			        'description' => '',
			        'desc_tip'    => 'false',
			        'placeholder' => __('Example: T-shirt', 'smart-send-logistics'),
		        )
	        );

	        woocommerce_wp_text_input(
		        array(
			        'id'          => '_ss_hs_code',
			        'label'       => __('Harmonized Tariff Schedule', 'smart-send-logistics'),
			        'description' => __('Harmonized Tariff Schedule is a number assigned to every possible commodity that can be imported or exported from any country.',
				        'smart-send-logistics'),
			        'desc_tip'    => 'true',
			        'placeholder' => __('Example: 12345678', 'smart-send-logistics'),
		        )
	        );
        }

        public function save_additional_product_shipping_options($post_id)
        {
	        //Country of origin
	        if (isset($_POST['_ss_country_of_origin'])) {
		        update_post_meta($post_id, '_ss_country_of_origin', wc_clean($_POST['_ss_country_of_origin']));
	        }
	        //Custom description value
	        if (isset($_POST['_ss_customs_desc'])) {
		        update_post_meta($post_id, '_ss_customs_desc', wc_clean($_POST['_ss_customs_desc']));
	        }
	        //HS code value
	        if (isset($_POST['_ss_hs_code'])) {
		        update_post_meta($post_id, '_ss_hs_code', wc_clean($_POST['_ss_hs_code']));
	        }
        }
    }

endif;
