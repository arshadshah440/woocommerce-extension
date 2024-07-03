<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * WooCommerce Smart Send Shipping Order Payload.
 *
 * @package  SS_Shipping_Shipment
 * @category Shipping
 * @author   Smart Send
 */

if (!class_exists('SS_Shipping_Shipment')) :

    class SS_Shipping_Shipment
    {

    	/*
    	 * WC_Order object
    	 */
        protected $order = null;
        
        /*
         * SS_Shipping_WC_Order object
         */
        protected $shipping_order = null;

        /*
         * \Smartsend\Models\Shipment object
         */
        protected $shipment = null;


        /**
         * Init and hook in the integration.
         *
         * @param WC_Order|integer $order
         * @param array $shipping_order
         */
        public function __construct($order, $shipping_order)
        {

            if (is_numeric($order) && $order > 0) {
                $this->order = wc_get_order($order);

                if (!$this->order) {
                    return;
                }

            } elseif ($order instanceof WC_Order) {
                $this->order = $order;
            } else {
                return;
            }

            $this->shipping_order = $shipping_order;

            //New shipment model
            $this->shipment = new \Smartsend\Models\Shipment();
        }

        /**
         * Create single order
         *
         * @param boolean $return
         * @return boolean
         */
        public function make_single_shipment_api_call( $return )
        {
            $this->make_single_shipment_api_payload( $return );
            $this->make_single_shipment_api_request();

            if (SS_SHIPPING_WC()->get_api_handle()->isSuccessful()) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * Get API call data
         *
         * @return object
         */
        public function get_shipping_data()
        {
            return SS_SHIPPING_WC()->get_api_handle()->getData();
        }

        /**
         * Get error message
         *
         * @return string
         */
        public function get_error_msg()
        {
            return SS_SHIPPING_WC()->get_api_handle()->getErrorString();
        }

        /**
         * Get shipment object
         *
         * @return \Smartsend\Models\Shipment
         */
        public function get_shipment()
        {
            return $this->shipment;
        }

        /**
         * Create Payload for API request
         *
         * @param boolean $return
         * @return void
         */
        protected function make_single_shipment_api_payload( $return )
        {
            $order_id = $this->getOrderId($this->order);
            
            $ss_args = array();

            // Get shipping method
            $ss_shipping_method_id = $this->shipping_order->get_smart_send_method_id($order_id, $return);

            if ($return && isset($ss_shipping_method_id['smart_send_return_method'])) {
                // If no return method set return error
                if (empty($ss_shipping_method_id['smart_send_return_method'])) {
                    return array('error' => __('No return method set', 'smart-send-logistics'));
                } else {
                    $ss_shipping_method_id = $ss_shipping_method_id['smart_send_return_method'];
                }

            } else {
                $ss_args['ss_agent'] = $this->shipping_order->get_ss_shipping_order_agent($order_id);
            }

            // Determine shipping method and carrier from return settings
            $shipping_method_carrier = SS_SHIPPING_WC()->get_shipping_method_carrier($ss_shipping_method_id);
            $shipping_method_type = SS_SHIPPING_WC()->get_shipping_method_type($ss_shipping_method_id);

            $ss_args['ss_carrier'] = $shipping_method_carrier;
            $ss_args['ss_type'] = $shipping_method_type;
            $ss_args['ss_parcels'] = $this->shipping_order->get_ss_shipping_order_parcels($order_id);

            /*
             * Filter the arguments used when creating a shipping label
             *
             * @param array $ss_args contains info about shipping carrier, shipping method, agent and parcels
             * @param int  $order_id  Order ID
             * @param boolean $return Whether or not the label is return (true) or normal (false)
             */
            $ss_args = apply_filters('smart_send_shipping_label_args', $ss_args, $order_id, $return);

            $ss_settings = SS_SHIPPING_WC()->get_ss_shipping_settings();

            // Get address related information
            $billing_address = $this->order->get_address();
            $shipping_address = apply_filters(
            	'smart_send_order_receiver',
	            $this->order->get_address('shipping'),
                $this->getOrderId($this->order)
            );

            // The shipping phone field was added in WooCommerce 5.6 but often added by hooks/plugins before that.
            // Note that the field is not shown on checkout per default but can be enabled by filters/plugins.
            // See: https://github.com/woocommerce/woocommerce/pull/30097#issuecomment-943114632
            if (isset($shipping_address['phone']) && $shipping_address['phone']) { // Field did not exist prior to WooCommerce 5.6
                $phone = $shipping_address['phone'];
            } elseif ($shipping_address['country'] == $billing_address['country']) { // Require as only local phone numbers is accepted
                $phone = $billing_address['phone'];
            } else {
                $phone = null;
            }

            // The shipping email field does not exist in default WP installations but can be added by filters/hooks.
            if (!isset($shipping_address['email']) && isset($billing_address['email'])) {
                $shipping_address['email'] = $billing_address['email'];
            }

            // Make receiver object.
            $receiver = new \Smartsend\Models\Shipment\Receiver();
            $receiver->setInternalId($this->getOrderId($this->order))
                ->setInternalReference($this->order->get_order_number() ? $this->order->get_order_number() : null)
                ->setCompany($shipping_address['company'] ?: null)
                ->setNameLine1($shipping_address['first_name'] ?: null)
                ->setNameLine2($shipping_address['last_name'] ?: null)
                ->setAddressLine1($shipping_address['address_1'] ?: null)
                ->setAddressLine2($shipping_address['address_2'] ?: null)
                ->setPostalCode($shipping_address['postcode'] ?: null)
                ->setCity($shipping_address['city'] ?: null)
                ->setCountry($shipping_address['country'] ?: null)
                ->setSms($phone ?: null)
                ->setEmail($shipping_address['email'] ?: null);

            // Add the receiver to the shipment
            $this->shipment->setReceiver($receiver);

            // Add the sender to the shipment (we use the system default for now)
            //$this->shipment->setSender(Sender $sender);

            $ss_agent = apply_filters(
              'smart_send_order_agent',
              empty($ss_args['ss_agent']) ? null : $ss_args['ss_agent'],
              $this->getOrderId($this->order)
            );

            if (!empty($ss_agent)) {
                // Add an agent (pick-up point) to the shipment
                $agent = new Smartsend\Models\Shipment\Agent();
                $agent->setInternalId(isset($ss_agent->id) ? $ss_agent->id : $ss_agent->agent_no)
                    ->setInternalReference(isset($ss_agent->id) ? $ss_agent->id : $ss_agent->agent_no)
                    ->setAgentNo($ss_agent->agent_no ?: null)
                    ->setCompany($ss_agent->company ?: null)
                    // ->setNameLine1(null)
                    // ->setNameLine2(null)
                    ->setAddressLine1($ss_agent->address_line1 ?: null)
                    ->setAddressLine2($ss_agent->address_line2 ?: null)
                    ->setPostalCode($ss_agent->postal_code ?: null)
                    ->setCity($ss_agent->city ?: null)
                    ->setCountry($ss_agent->country ?: null);
                // ->setSms(null)
                // ->setEmail(null);

                // Add the agent to the shipment
                $this->shipment->setAgent($agent);
            }

            // Get order item specific data
            $ordered_items = $this->order->get_items();

            // Get product info from the same routine so that we won't have
            // to iterate through the ordered items once again.
            $item_data = array();

            if (!empty($ordered_items)) {
                $items = array();
                $index = 0;
                $weight_total = 0;
                foreach ($ordered_items as $key => $item) {
                    $product = wc_get_product($item['product_id']);

                    if (!empty($item['variation_id'])) {
                        $product_variation = wc_get_product($item['variation_id']);
                        // Ensure id is string and not int
                        $product_id = $item['variation_id'];
                        $product_sku = $product_variation->get_sku() ? $product_variation->get_sku() : strval($item['variation_id']);

                        // $product_attribute = wc_get_product_variation_attributes($item['variation_id']);
                        // $product_description .= ' : ' . current( $product_attribute );

                    } else {
                        $product_variation = $product;
                        $product_id = $item['product_id'];
                        // Ensure id is string and not int
                        $product_sku = $product->get_sku() ? $product->get_sku() : strval($item['product_id']);
                    }

                    $product_description = $product->get_title();

                    // WC 3.0 code!
                    if (defined('WOOCOMMERCE_VERSION') && version_compare(WOOCOMMERCE_VERSION, '3.0', '>=')) {
                        // $product_val_tax = wc_get_price_including_tax( $product_variation );
                        // $args['qty'] = $item['qty'];
                        // $product_val_tax_total = wc_get_price_including_tax( $product_variation, $args );

                        // Total w/o tax and individual w/o tax
                        $product_val_total = (float)$item->get_subtotal();
                        $product_val = $product_val_total / $item['qty'];

                        // Total tax
                        $product_tax_total = (float)$item->get_subtotal_tax();

                        // Total w/ tax and indivdual w/ tax
                        $product_val_tax_total = $product_val_total + $product_tax_total;
                        $product_val_tax = $product_val_tax_total / $item['qty'];


                        if (!empty($product->get_short_description())) {
                            $product_description = $product->get_short_description();
                        } elseif (!empty($product->get_description())) {
                            $product_description = $product->get_description();
                        }

                    } else {
                        // Total w/o tax and individual w/o tax
                        $product_val_total = (float)$item['line_subtotal'];
                        $product_val = $product_val_total / $item['qty'];

                        // Total tax
                        $product_tax_total = (float)$item['line_tax'];

                        // Total w/ tax and indivdual w/ tax
                        $product_val_tax_total = $product_val_total + $product_tax_total;
                        $product_val_tax = $product_val_tax_total / $item['qty'];

                        if (!empty($product->post->post_excerpt)) {
                            $product_description = $product->post->post_excerpt;
                        } elseif (!empty($product->post->post_content)) {
                            $product_description = $product->post->post_content;
                        }

                    }

                    $product_weight = round(wc_get_weight($product_variation->get_weight(), 'kg'), 2);
                    if ($product_weight) {
                        $weight_total += ($item['qty'] * $product_weight);
                    }

                    // Update product/item data array
                    $item_data[$product_id] = array(
                        'product_val'     => $product_val,
                        'product_val_tax' => $product_val_tax,
                        'product_weight'  => $product_weight,
                    );

                    $product_img_id = $product->get_image_id();
                    $product_img_url = wp_get_attachment_url($product_img_id);

                    $hs_code = get_post_meta($item['product_id'], '_ss_hs_code', true);
                    $custom_desc = get_post_meta($item['product_id'], '_ss_customs_desc', true);
	                $country_of_origin = get_post_meta($item['product_id'], '_ss_country_of_origin', true);

                    $items[$index] = new \Smartsend\Models\Shipment\Item();
                    $items[$index]->setInternalId($product_id ?: null)
                        ->setInternalReference($product_id ?: null)
                        ->setSku($product_sku ?: null)
                        ->setName($product->get_title() ?: null)
                        ->setDescription($custom_desc ?: null)//$product_description can be used, but is often to long (255)
                        ->setHsCode($hs_code ?: null)
	                    ->setCountryOfOrigin($country_of_origin ?: null)
                        ->setImageUrl(null)//$product_img_url can be used, but sometimes include spaces (bug) which causes validation error
                        ->setUnitWeight($product_weight > 0 ? $product_weight : null)
                        ->setUnitPriceExcludingTax($product_val ?: null)
                        ->setUnitPriceIncludingTax($product_val_tax ?: null)
                        ->setQuantity($item['qty'] ?: null)
                        ->setTotalPriceExcludingTax($product_val_total ?: null)
                        ->setTotalPriceIncludingTax($product_val_tax_total ?: null)
                        ->setTotalTaxAmount($product_tax_total ?: null);

                    // Store product item in data item array for later reference
                    $item_data[$product_id]['product_item'] = $items[$index];

                    $index++;
                }

                $order_note = null;
                // Create the parcels array
                if (defined('WOOCOMMERCE_VERSION') && version_compare(WOOCOMMERCE_VERSION, '3.0', '>=')) {
                    if ($ss_settings['include_order_comment'] == 'yes') {
                        $order_note = $this->order->get_customer_note();
                    }
                    $order_currency = $this->order->get_currency();
                } else {
                    if ($ss_settings['include_order_comment'] == 'yes') {
                        $order_note = $this->order->customer_note;
                    }
                    $order_currency = $this->order->get_currency();
                }

                /*
                 * Filter the order note which can be printed as freetext on the shipping label
                 *
                 * @param string $order_note is the customer note of the order
                 * @param WC_Order object
                 */
                $order_note = apply_filters('smart_send_order_note', $order_note, $this->order);

                // Order totals
                $order_total = $this->order->get_total();
                $order_total_tax = $this->order->get_total_tax();
                $order_total_excl = $order_total - $order_total_tax;

                // Shipping totals
                $order_shipping = $this->order->get_shipping_total();
                $order_shipping_tax = $this->order->get_shipping_tax();
                $order_shipping_excl = $order_shipping - $order_shipping_tax;

                // Order totals without shipping
                $order_subtotal = $order_total - $order_shipping;
                $order_subtotal_tax = $order_total_tax - $order_shipping_tax;
                $order_subtotal_excl = $order_total_excl - $order_subtotal_tax;

                $parcels = array();
                if (!empty($ss_args['ss_parcels'])) {
                    if (is_array($ss_args['ss_parcels'])) {

                        $boxes = array();
                        foreach ($ss_args['ss_parcels'] as $parcel) {
                            $boxes[$parcel['value']][] = array(
                                'id'   => $parcel['id'],
                                'name' => $parcel['name'],
                            );
                        }

                        foreach ($boxes as $box_number => $items) {
                            $item_total_wo_tax = 0;
                            $item_total_tax = 0;
                            $item_total_incl_tax = 0;
                            $item_weight_total = 0;
                            $product_items = array();

                            foreach ($items as $item) {
                                $data = $item_data[$item['id']];

                                $item_total_wo_tax += floatval($data['product_val']);
                                $item_total_incl_tax += floatval($data['product_val_tax']);
                                $item_weight_total += floatval($data['product_weight']);

                                // Compute for the total tax per individual
                                $item_total_tax += $item_total_incl_tax - $item_total_wo_tax;

                                array_push($product_items, $data['product_item']);
                            }

							$parcel_total_weight = apply_filters(
								'smart_send_parcel_weight',
								$item_weight_total ? $item_weight_total : null,
								$this->getOrderId($this->order)
							);

                            $parcel = new \Smartsend\Models\Shipment\Parcel();
                            $parcel->setInternalId($this->getOrderId($this->order) ?: null)
                                ->setInternalReference($this->order->get_order_number() ?: null)
                                ->setWeight($parcel_total_weight)
                                ->setHeight(null)
                                ->setWidth(null)
                                ->setLength(null)
                                ->setFreetext($order_note ?: null)
                                ->setItems($product_items)// Alternatively add each item using $parcel->addItem(Item $item)
                                ->setTotalPriceExcludingTax($item_total_wo_tax ?: null)
                                ->setTotalPriceIncludingTax($item_total_incl_tax ?: null)
                                ->setTotalTaxAmount($item_total_tax ?: null);

                            array_push($parcels, $parcel);
                        }
                    }
                } else {
                    // Create a parcel containing the items just defined
                    $parcels[0] = new \Smartsend\Models\Shipment\Parcel();
                    $parcels[0]->setInternalId($this->getOrderId($this->order) ?: null)
                        ->setInternalReference($this->order->get_order_number() ?: null)
                        ->setWeight($weight_total ?: null)
                        ->setHeight(null)
                        ->setWidth(null)
                        ->setLength(null)
                        ->setFreetext($order_note ?: null)
                        ->setItems($items)// Alternatively add each item using $parcel->addItem(Item $item)
                        ->setTotalPriceExcludingTax($order_subtotal_excl ?: null)
                        ->setTotalPriceIncludingTax($order_subtotal ?: null)
                        ->setTotalTaxAmount($order_subtotal_tax ?: null);
                }
            }

            // Create services
            $services = new \Smartsend\Models\Shipment\Services();
            $services->setSmsNotification($receiver->getSms())//Always enable SMS notification
            ->setEmailNotification($receiver->getEmail()); //Always enable Email notification

            // Determine shipping method and carrier from return settings
            $shipping_method_carrier = $ss_args['ss_carrier'];
            $shipping_method_type = $ss_args['ss_type'];

            // Add final parameters to shipment
            $this->shipment->setInternalId($this->getOrderId($this->order) ?: null)
                ->setInternalReference($this->order->get_order_number() ?: null)
                ->setShippingCarrier($shipping_method_carrier ?: null)
                ->setShippingMethod($shipping_method_type ?: null)
                ->setShippingDate(date('Y-m-d'))
                ->setParcels($parcels)// Alternatively add each parcel using $this->shipment->addParcel(Parcel $parcel);
                ->setServices($services)
                ->setSubtotalPriceExcludingTax($order_subtotal_excl ?: null)
                ->setSubtotalPriceIncludingTax($order_subtotal ?: null)
                ->setTotalPriceExcludingTax($order_total_excl ?: null)
                ->setTotalPriceIncludingTax($order_total ?: null)
                ->setShippingPriceExcludingTax($order_shipping_excl ?: null)
                ->setShippingPriceIncludingTax($order_shipping ?: null)
                ->setTotalTaxAmount($order_total_tax ?: null)
                ->setCurrency($order_currency ?: null);
        }

        /**
         * Call Smart Send Shipment API, log response
         *
         * @return void
         */
        protected function make_single_shipment_api_request()
        {
            // Make API Request
            SS_SHIPPING_WC()->get_api_handle()->createShipmentAndLabels($this->shipment);

            // Log API request
            SS_SHIPPING_WC()->log_msg('Called "createShipmentAndLabels" with arguments: ' . SS_SHIPPING_WC()->get_api_handle()->getRequestBody());

            // Log API response
            if (SS_SHIPPING_WC()->get_api_handle()->isSuccessful()) {
                SS_SHIPPING_WC()->log_msg('Response from "createShipmentAndLabels" : ' . SS_SHIPPING_WC()->get_api_handle()->getResponseBody());
            } else {
                SS_SHIPPING_WC()->log_msg('Error response from "createShipmentAndLabels" : ' . SS_SHIPPING_WC()->get_api_handle()->getResponseBody());
            }
        }

        /**
         * Get the order id
         *
         * @param WC_Order
         * @return string
         */
        protected function getOrderId($order)
        {
            // WC 3.0 code!
            if (defined('WOOCOMMERCE_VERSION') && version_compare(WOOCOMMERCE_VERSION, '3.0', '>=')) {
                return $order->get_id();
            } else {
                return $order->id;
            }
        }
    }

endif;
