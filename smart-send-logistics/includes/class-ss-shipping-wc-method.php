<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('SS_Shipping_WC_Method')) :

    class SS_Shipping_WC_Method extends WC_Shipping_Flat_Rate
    {

        private $shipping_method = array();

        /**
         * Init and hook in the integration.
         */
        public function __construct($instance_id = 0)
        {
            $this->id = SS_SHIPPING_METHOD_ID;
            $this->instance_id = absint($instance_id);
            $this->method_title = __('Smart Send', 'smart-send-logistics');
            $this->method_description = __('Advanced shipping solution for PostNord, GLS and Bring.',
                'smart-send-logistics');

            $this->supports = array(
                'settings',
                'shipping-zones', // support shipping zones shipping method
                'instance-settings',
            );

            $this->shipping_method = array(
                '0'                 => __('- Select Method -', 'smart-send-logistics'),
                'PostNord'          =>
                    array(
                        'postnord_agent'                   => __('PostNord: Select pick-up point (MyPack Collect)',
                            'smart-send-logistics'),
                        'postnord_collect'                 => __('PostNord: Closest pick-up point (MyPack Collect)',
                            'smart-send-logistics'),
                        'postnord_homedelivery'            => __('PostNord: Private delivery to address (MyPack Home)',
                            'smart-send-logistics'),
                        'postnord_doorstep'                => __('PostNord: Leave at door (Flexdelivery)',
	                        'smart-send-logistics'),
                        'postnord_flexhome'                => __('PostNord: Flexible home delivery (FlexChange)',
	                        'smart-send-logistics'),
                        'postnord_homedeliveryeconomy'     => __('PostNord: Private economy delivery to address (MyPack Home Economy)',
	                        'smart-send-logistics'),
                        'postnord_homedeliverysmall'       => __('PostNord: Private delivery to address Small (MyPack Home Small)',
	                        'smart-send-logistics'),
                        'postnord_commercial'              => __('PostNord: Commercial delivery to address (Parcel)',
                            'smart-send-logistics'),
                        'postnord_valuableparcel'          => __('PostNord: Valuable parcel',
	                        'smart-send-logistics'),
                        'postnord_valuemaillarge'          => __('PostNord: Tracked Valuemail Large',
                            'smart-send-logistics'),
                        'postnord_valuemailmaxi'           => __('PostNord: Tracked Valuemail Maxi',
                            'smart-send-logistics'),
                        'postnord_valuemailfirstclass'     => __('PostNord: Tracked Valuemail First Class',
                            'smart-send-logistics'),
                        'postnord_valuemaileconomy'        => __('PostNord: Tracked Valuemail Economy',
                            'smart-send-logistics'),
                        'postnord_valuemaileco'            => __('PostNord: Tracked Valuemail Eco Friendly',
                            'smart-send-logistics'),
                        'postnord_valuemailuntrackedlarge' => __('PostNord: Untracked Valuemail Large',
                            'smart-send-logistics'),
                        'postnord_valuemailuntrackedmaxi'  => __('PostNord: Untracked Valuemail Maxi',
                            'smart-send-logistics'),
                        'postnord_letterregistered'        => __('PostNord: Registred letter',
	                        'smart-send-logistics'),
                        'postnord_lettertracked'           => __('PostNord: Tracked letter',
	                        'smart-send-logistics'),
                        'postnord_lettertrackedlarge'      => __('PostNord: Tracked letter Large',
                            'smart-send-logistics'),
                        'postnord_letteruntracked'         => __('PostNord: Untracked letter',
	                        'smart-send-logistics'),
                        'postnord_expressletter'           => __('PostNord: Express Letter',
                            'smart-send-logistics'),
                        'postnord_fullpallet'              => __('PostNord: Full size pallet',
                            'smart-send-logistics'),
                        'postnord_halfpallet'              => __('PostNord: Half size pallet',
                            'smart-send-logistics'),
                        'postnord_quarterpallet'           => __('PostNord: Quarter size pallet',
                            'smart-send-logistics'),
			'postnord_specialpallet'           => __('PostNord: Speciel size pallet',
                            'smart-send-logistics'),
                    ),
                'GLS'               =>
                    array(
                        'gls_agent'        => __('GLS: Select pick-up point (ParcelShop)', 'smart-send-logistics'),
                        'gls_collect'      => __('GLS: Closest pick-up point (ParcelShop)', 'smart-send-logistics'),
                        'gls_homedelivery' => __('GLS: Private delivery to address (PrivateDelivery)',
                            'smart-send-logistics'),
                        'gls_doorstep'     => __('GLS: Leave at door (DepositService)', 'smart-send-logistics'),
                        'gls_flexhome'     => __('GLS: Flexible home delivery (FlexDelivery)', 'smart-send-logistics'),
                        'gls_commercial'   => __('GLS: Commercial delivery to address (BusinessParcel)',
                            'smart-send-logistics'),
                    ),
                'DAO'               =>
                    array(
                        'dao_agent'           => __('DAO: Select pick-up point (ParcelShop)', 'smart-send-logistics'),
                        'dao_collect'         => __('DAO: Closest pick-up point (ParcelShop)', 'smart-send-logistics'),
                        'dao_doorstep'        => __('DAO: Leave at door (Direct)', 'smart-send-logistics'),
                        'dao_dropoffagent'    => __('DAO: From pick-up point to pick-up point (Shop2Shop)', 'smart-send-logistics'),
                        'dao_dropoffdoorstep' => __('DAO: From pick-up point to doorstep (ParcelShop to Direct)', 'smart-send-logistics'),
                    ),
		'Budbee'            =>
                    array(
                        'budbee_home'         => __('Budbee: Home', 'smart-send-logistics'),
                    ),
		'Burd'              =>
                    array(
                        'burd_home'           => __('Burd: Home Delivery', 'smart-send-logistics'),
                    ),
                'Bring'             =>
                    array(
                        'bring_agent'                   => __('Bring: Select pick-up point (PickUp Parcel / Serviceparcel)',
                            'smart-send-logistics'),
                        'bring_bulkagent'               => __('Bring: Select pick-up point, send as bulk (PickUp Parcel Bulk)',
                            'smart-send-logistics'),
                        'bring_collect'                 => __('Bring: Closest pick-up point (PickUp Parcel / Serviceparcel)',
                            'smart-send-logistics'),
                        'bring_bulkcollect'             => __('Bring: Closest pick-up point, send as bulk (PickUp Parcel Bulk)',
                            'smart-send-logistics'),
                        'bring_homedelivery'            => __('Bring: Private delivery to address (Home Delivery Parcel)',
                            'smart-send-logistics'),
                        'bring_commercial'              => __('Bring: Commercial delivery to address (Business Parcel)',
                            'smart-send-logistics'),
                        'bring_bulkcommercial'          => __('Bring: Commercial delivery to address, send as bulk (Business Parcel Bulk)',
                            'smart-send-logistics'),
                        'bring_commercialfullpallet'    => __('Bring: Commercial delivery of full size pallet (Business Pallet)',
                            'smart-send-logistics'),
                        'bring_commercialhalfpallet'    => __('Bring: Commercial delivery of half size pallet (Business Pallet)',
                            'smart-send-logistics'),
                        'bring_commercialquarterpallet' => __('Bring: Commercial delivery of quarter size pallet (Business Pallet)',
                            'smart-send-logistics'),
                        'bring_express9'                => __('Bring: Express delivery before 9:00 (Express Nordic 09:00)',
                            'smart-send-logistics'),
                        'bring_bulkexpress9'            => __('Bring: Express delivery before 9:00, send as bulk (Express Nordic 09:00 Bulk)',
                            'smart-send-logistics'),
                    ),
                'Bifrost Logistics' =>
                    array(
                        // eTail Tracked
                        'bifrost_etailtracked'            => __('Bifrost Logistics: eTail Tracked',
                            'smart-send-logistics'),
                        'bifrost_etailtrackedlarge'       => __('Bifrost Logistics: eTail Tracked large',
                            'smart-send-logistics'),
                        // eTail Gain
                        'bifrost_etailgainsmall'          => __('Bifrost Logistics: eTail Gain small',
                            'smart-send-logistics'),
                        'bifrost_etailgainlarge'          => __('Bifrost Logistics: eTail Gain large',
                            'smart-send-logistics'),
                        // Express
                        'bifrost_expresscollect'          => __('Bifrost Logistics: Nordic Express Collect',
                            'smart-send-logistics'),
                        'bifrost_expresshome'             => __('Bifrost Logistics: Nordic Express Home',
                            'smart-send-logistics'),
                        /*
                        // Letter Priority
                        'bifrost_letterprioritysmall'     => __('Bifrost Logistics: Letter priority small',
                            'smart-send-logistics'),
                        'bifrost_letterprioritylarge'     => __('Bifrost Logistics: Letter priority large',
                            'smart-send-logistics'),
                        'bifrost_letterprioritymaxi'      => __('Bifrost Logistics: Letter priority maxi',
                            'smart-send-logistics'),
                        //Letter Economy
                        'bifrost_lettereconomysmall'      => __('Bifrost Logistics: Letter economy small',
                            'smart-send-logistics'),
                        'bifrost_lettereconomylarge'      => __('Bifrost Logistics: Letter economy large',
                            'smart-send-logistics'),
                        'bifrost_lettereconomymaxi'       => __('Bifrost Logistics: Letter economy maxi',
                            'smart-send-logistics'),
                        // Press Priority
                        'bifrost_pressprioritylarge'      => __('Bifrost Logistics: Press priority large',
                            'smart-send-logistics'),
                        'bifrost_pressprioritymaxi'       => __('Bifrost Logistics: Press priority maxi',
                            'smart-send-logistics'),
                        // Advertising Economy
                        'bifrost_advertisingeconomysmall' => __('Bifrost Logistics: Advertising economy small',
                            'smart-send-logistics'),
                        'bifrost_advertisingeconomylarge' => __('Bifrost Logistics: Advertising economy large',
                            'smart-send-logistics'),
                        'bifrost_advertisingeconomymaxi'  => __('Bifrost Logistics: Advertising economy maxi',
                            'smart-send-logistics'),
                        // Ecom priority large
                        'bifrost_ecomprioritylarge'       => __('Bifrost Logistics: Ecom priority large',
                            'smart-send-logistics'),
                        'bifrost_ecomprioritymaxi'        => __('Bifrost Logistics: Ecom priority maxi',
                            'smart-send-logistics'),
                        */
                    ),
            );

            $this->return_shipping_method = array(
                '0'        => __('- Select Method -', 'smart-send-logistics'),
                'PostNord' =>
                    array(
                        'postnord_returndropoff' => __('PostNord: Return from pick-up point (Return Drop Off)',
                            'smart-send-logistics'),
                        'postnord_returnpickup'  => __('PostNord: Return from address (Return Pickup)',
                            'smart-send-logistics'),
                    ),
                'GLS'      =>
                    array(
                        'gls_returndropoff' => __('GLS: Return from pick-up point (ShopReturn)',
                            'smart-send-logistics'),
                    ),
                'DAO'      =>
                    array(
                        'dao_returndropoff' => __('DAO: Return from pick-up point (ParcelShop Return)',
                            'smart-send-logistics'),
                    ),
                'Bring'    =>
                    array(
                        'bring_returndropoff' => __('Bring: Return from pick-up point (PickUp Parcel Return)',
                            'smart-send-logistics'),
                        'bring_returnpickup'  => __('Bring: Return from address (Parcel Return)',
                            'smart-send-logistics'),
                    ),
            );

            $this->init();
        }

        /**
         * init function.
         */
        public function init()
        {

            $this->init_instance_form_fields();
            $this->init_form_fields();

            $this->init_settings();

            // Set title so can be viewed in zone screen
            $this->title = $this->get_option('title');

            // add_action( 'admin_notices', array( $this, 'environment_check' ) );
            add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
            // Admin script
            add_action('admin_enqueue_scripts', array($this, 'load_admin_scripts'));
        }

        /**
         * load admin scripts on settings page only
         */
        public function load_admin_scripts($hook)
        {

            if ('woocommerce_page_wc-settings' != $hook) {
                // Only applies to WC Settings panel
                return;
            }

            wp_enqueue_script('smart-send-shipping-admin-js',
                SS_SHIPPING_PLUGIN_DIR_URL . '/assets/js/ss-shipping-admin.js', array('jquery'), SS_SHIPPING_VERSION);

            $test_con_data = array(
                'ajax_url'              => admin_url('admin-ajax.php'),
                'test_connection_nonce' => wp_create_nonce('ss-test-connection'),
                'validating_connection' => __('Validating connection...', 'smart-send-logistics'),
            );

            wp_enqueue_script('smart-send-test-connection',
                SS_SHIPPING_PLUGIN_DIR_URL . '/assets/js/ss-shipping-test-connection.js', array('jquery'),
                SS_SHIPPING_VERSION);
            wp_localize_script('smart-send-test-connection', 'ss_test_connection_obj', $test_con_data);
        }

        /**
         * Initialize integration settings form fields.
         *
         * @return void
         */
        public function init_form_fields()
        {
            $log_path = SS_SHIPPING_WC()->get_log_url();
            $agents_address_format = SS_SHIPPING_WC()->get_agents_address_format();

            $this->form_fields = array(
                'api_token'                         => array(
                    // Note that this can be input for multiple sites using
                    // site1:apitoken1,site2:apitoken2,....
                    'title'       => __('API Token', 'smart-send-logistics'),
                    'type'        => 'text',
                    'default'     => '',
                    'description' => sprintf(__('Sign up for a Smart Send account <a href="%s" target="_blank">here</a>.',
                        'smart-send-logistics'), esc_url('https://smartsend.io/')),
                    'desc_tip'    => false,
                ),
                'api_token_validate'                => array(
                    'title'             => SS_BUTTON_TEST_CONNECTION,
                    'type'              => 'button',
                    'custom_attributes' => array(
                        'onclick' => "ssTestConnection('#woocommerce_smart_send_shipping_api_token_validate');",
                    ),
                    'description'       => __('Save the settings before clicking the button to validate API Token.',
                        'smart-send-logistics'),
                    'desc_tip'          => false,
                ),
                'demo'                              => array(
                    'title'       => __('Demo mode', 'smart-send-logistics'),
                    'description' => __('Demo mode is used for testing on a staging site. No data will be send to the shipping carrier.',
                        'smart-send-logistics'),
                    'type'        => 'checkbox',
                    'default'     => 'yes',
                    'label'       => __('Enable demo mode', 'smart-send-logistics'),
                ),
                'ss_debug'                          => array(
                    'title'       => __('Debug Log', 'smart-send-logistics'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable logging', 'smart-send-logistics'),
                    'default'     => 'no',
                    'description' => sprintf(__('A log file containing the communication to the Smart Send server will be maintained if this option is checked. This can be used in case of technical issues and can be found %shere%s.',
                        'smart-send-logistics'), '<a href="' . $log_path . '" target = "_blank">', '</a>'),
                ),
                'title_labels'                      => array(
                    'title'       => __('Shipping Labels', 'smart-send-logistics'),
                    'type'        => 'title',
                    'description' => __('Settings for generating shipping labels.', 'smart-send-logistics'),
                ),
                'order_status'                      => array(
                    'title'   => __('Set order status after label print', 'smart-send-logistics'),
                    'id'      => 'smart_send_shipping_order_status',
                    'default' => 'no',
                    'type'    => 'select',
                    'class'   => 'wc-enhanced-select',
                    'options' => array_merge(array('0' => __("Do not change order status", 'smart-send-logistics')),
                        wc_get_order_statuses()),
                ),
                'shipping_method_for_free_shipping' => array(
                    'title'       => __('Shipping method used for WooCommerce method Free Shipping',
                        'smart-send-logistics'),
                    'type'        => 'selectopt',
                    'class'       => 'wc-enhanced-select',
                    'description' => __('Selecting a shipping method will make it possible to make shipping labels for order places with WooCommerces native Free Shipping method.',
                        'smart-send-logistics'),
                    'options'     => $this->shipping_method,
                ),
                'include_order_comment'             => array(
                    'title'    => __('Include order comment on label', 'smart-send-logistics'),
                    'default'  => 'no',
                    'type'     => 'checkbox',
                    'desc_tip' => false,
                ),
                'save_shipping_labels_in_uploads'   => array(
                    'title'       => __('Save a copy of the PDF', 'smart-send-logistics'),
                    'default'     => 'no',
                    'type'        => 'checkbox',
                    'description' => __('This will save a copy of the generated PDF label in the WordPress uploads-folder',
                        'smart-send-logistics'),
                    'desc_tip'    => true,
                ),
                'title_pickup'                      => array(
                    'title'       => __('Pick-up Points', 'smart-send-logistics'),
                    'type'        => 'title',
                    'description' => __('Settings for displaying pick-up points during checkout.',
                        'smart-send-logistics'),
                ),
                'dropdown_display_format'           => array(
                    'title'    => __('Dropdown format', 'smart-send-logistics'),
                    'desc'     => __('How the pick-up points are listed during checkout.', 'smart-send-logistics'),
                    'default'  => '4',
                    'type'     => 'select',
                    'class'    => 'wc-enhanced-select',
                    'desc_tip' => true,
                    'options'  => $agents_address_format,
                ),
                'default_select_agent'              => array(
                    'title'       => __('Select Default', 'smart-send-logistics'),
                    'label'       => __('Enable Select Default', 'smart-send-logistics'),
                    'description' => __('This will automatically select the closest pick-up point and let the customer change to a different pick-up point. This means that the customer will not be forced to select a pick-up point before completing the order.'),
                    'default'     => 'no',
                    'type'        => 'checkbox',
                    'desc_tip'    => true,
                ),
                'title_shipping_methods'            => array(
                    'title'       => __('Shipping methods', 'smart-send-logistics'),
                    'type'        => 'title',
                    'description' => __('Settings for shipping methods.', 'smart-send-logistics'),
                ),
                'sort_methods_by_cost'              => array(
                    'title'       => __('Sort shipping methods', 'smart-send-logistics'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable automatic sorting by cost on checkout page', 'smart-send-logistics'),
                    'default'     => 'no',
                    'description' => sprintf(__('Shipping methods will be sorted in ascending order, according to the cost, instead of by order of appearance in Shipping Zone table as per default',
                        'smart-send-logistics'), '<a href="' . $log_path . '" target = "_blank">', '</a>'),
                ),
            );
        }

        /**
         * Validate the Demo Checkbox Field.
         *
         * If not set, return "no", otherwise return "yes".
         *
         * @param  string $key
         * @param  string|null $value Posted Value
         * @return string
         *
         * @throws Exception
         */
        public function validate_demo_field($key, $value)
        {

            //Trying to disable Demo-mode setting. Check if the API Token entered is valid
            if ($value == 0) {
                $post_data = $this->get_post_data();
                if (empty($post_data['woocommerce_smart_send_shipping_api_token'])) {
                    // No API Token was provided, so need to shown an error and re-enable demo-mode
                    WC_Admin_Settings::add_error(__('Demo mode can only be disabled with a valid API Token. Please enter a valid API Token and save the settings again.',
                        'smart-send-logistics'));
                    $value = 1;
                } else {
                    //Check if API Token is valid
	                $website_url = SS_SHIPPING_WC()->get_website_url();
	                $api_token = SS_SHIPPING_WC()->get_api_token_setting($post_data['woocommerce_smart_send_shipping_api_token']);
                    $api_handle = new \Smartsend\Api($api_token,
                        $website_url, false);
                    if (!$api_handle->getAuthenticatedUser()) {
                        // The API Token was not valid for live mode, so need to shown an error and re-enable demo-mode
                        WC_Admin_Settings::add_error(sprintf(__('Invalid API Token. Demo mode can only be disabled with a valid API Token for %s.',
                            'smart-send-logistics'), $website_url));
                        $value = 1;
                    }
                }
            }

            return $this->validate_checkbox_field($key, $value);
        }

        /**
         * Generate Button HTML.
         *
         * @access public
         * @param mixed $key
         * @param mixed $data
         * @since 8.0.0
         * @return string
         */
        public function generate_button_html($key, $data)
        {
            $field = $this->plugin_id . $this->id . '_' . $key;
            $defaults = array(
                'class'             => 'button-secondary',
                'css'               => '',
                'custom_attributes' => array(),
                'desc_tip'          => false,
                'description'       => '',
                'title'             => '',
            );

            $data = wp_parse_args($data, $defaults);

            ob_start();
            ?>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php echo esc_attr($field); ?>"><?php echo wp_kses_post($data['title']); ?></label>
                    <?php echo $this->get_tooltip_html($data); ?>
                </th>
                <td class="forminp">
                    <fieldset>
                        <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span>
                        </legend>
                        <button class="<?php echo esc_attr($data['class']); ?>" type="button"
                                name="<?php echo esc_attr($field); ?>" id="<?php echo esc_attr($field); ?>"
                                style="<?php echo esc_attr($data['css']); ?>" <?php echo $this->get_custom_attribute_html($data); ?>><?php echo wp_kses_post($data['title']); ?></button>
                        <?php echo $this->get_description_html($data); ?>
                    </fieldset>
                </td>
            </tr>
            <?php
            return ob_get_clean();
        }

        private function get_guest_role()
        {
            return array('guest' => 'Guest');
        }

        public function init_instance_form_fields()
        {
            // Get list of shipping classes
            $wc_shipping = WC_Shipping::instance();
            $wc_shipping_classes = $wc_shipping->get_shipping_classes();
            $shipping_classes = wp_list_pluck($wc_shipping_classes, 'name', 'slug');

            // Get list of user roles, including guest (not logged in)
            global $wp_roles;
            $user_roles = $this->get_guest_role() + $wp_roles->get_names();

            $this->instance_form_fields = array(
                'title'                      => array(
                    'title'       => __('Method Title', 'smart-send-logistics'),
                    'type'        => 'text',
                    'description' => __('This controls the title which the user sees during checkout.',
                        'smart-send-logistics'),
                    'default'     => __('Smart Send', 'smart-send-logistics'),
                    'desc_tip'    => true,
                ),
                'method'                     => array(
                    'title'       => __('Shipping Method', 'smart-send-logistics'),
                    'type'        => 'selectopt',
                    'class'       => 'wc-enhanced-select',
                    'description' => __('This is the shipping method used when generating shipping labels.',
                        'smart-send-logistics'),
                    'desc_tip'    => true,
                    'options'     => $this->shipping_method,
                ),
                'tax_status'                 => array(
                    'title'       => __('Tax status', 'smart-send-logistics'),
                    'type'        => 'select',
                    'class'       => 'wc-enhanced-select',
                    'description' => __('Determines if the shipping cost is taxable. Remember to set a shipping tax in WooCommerce.',
                        'smart-send-logistics'),
                    'default'     => 'taxable',
                    'desc_tip'    => true,
                    'options'     => array(
                        'taxable' => __('Taxable', 'smart-send-logistics'),
                        'none'    => _x('None', 'Tax status', 'smart-send-logistics'),
                    ),
                ),
                'cost_title'                 => array(
                    'title'       => __('Cost', 'smart-send-logistics'),
                    'type'        => 'title',
                    'description' => __('Configure the shipping method cost and free shipping.',
                        'smart-send-logistics'),
                    'class'       => '',
                ),
                'cost_weight'                => array(
                    'type' => 'cost_weight',
                ),
                'requires'                   => array(
                    'title'   => __('Flat rate requires...', 'smart-send-logistics'),
                    'type'    => 'select',
                    'class'   => 'wc-enhanced-select',
                    'default' => '',
                    'options' => array(
                        'disabled'   => __('Always disabled', 'smart-send-logistics'),
                        'enabled'    => __('Always enabled', 'smart-send-logistics'),
                        'coupon'     => __('A valid free shipping coupon', 'smart-send-logistics'),
                        'min_amount' => __('A minimum order amount', 'smart-send-logistics'),
                        'either'     => __('A minimum order amount OR a coupon', 'smart-send-logistics'),
                        'both'       => __('A minimum order amount AND a coupon', 'smart-send-logistics'),
                    ),
                ),
                'flatfee_cost'    => array(
                    'title'       => __('Flat fee cost', 'smart-send-logistics'),
                    'type'        => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('Shipping method cost if rules apply. To apply free shipping the value must be "0".',
                        'smart-send-logistics'),
                    'default'     => '0',
                    'desc_tip'    => true,
                ),
                'min_amount'                 => array(
                    'title'       => __('Minimum order amount', 'smart-send-logistics'),
                    'type'        => 'price',
                    'placeholder' => wc_format_localized_price(0),
                    'description' => __('Users will need to spend at least this amount (including VAT) to get free shipping (if enabled above).',
                        'smart-send-logistics'),
                    'default'     => '0',
                    'desc_tip'    => true,
                ),
                'advanced_title'             => array(
                    'title'       => __('Advanced Settings', 'smart-send-logistics'),
                    'type'        => 'title',
                    'description' => __('Configure the advanced settings.', 'smart-send-logistics'),
                ),
                'advanced_settings_enable'   => array(
                    'title'       => __('Advanced Settings', 'smart-send-logistics'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable', 'smart-send-logistics'),
                    'default'     => 'no',
                    'description' => __('Enable/disable advanced settings and to show/hide settings.',
                        'smart-send-logistics'),
                    'desc_tip'    => false,
                ),
                'display_shipping_class_opt' => array(
                    'title'       => __('Display shipping method if...', 'smart-send-logistics'),
                    'type'        => 'select',
                    'class'       => 'wc-enhanced-select',
                    'description' => __('Select when to display the shipping method based on shipping class.',
                        'smart-send-logistics'),
                    'default'     => '',
                    'options'     => array(
                        'no_shipping_class'   => __('N/A', 'smart-send-logistics'),
                        'all_shipping_class'  => __('ALL products belong to one of the shipping classes',
                            'smart-send-logistics'),
                        'one_shipping_class'  => __('At least ONE product belongs to one of the shipping classes',
                            'smart-send-logistics'),
                        'nall_shipping_class' => __('ALL products do NOT belong to one of the shipping classes',
                            'smart-send-logistics'),
                        'none_shipping_class' => __('At least ONE product does NOT belongs to one of the shipping classes',
                            'smart-send-logistics'),
                    ),
                    'desc_tip'    => true,
                ),
                'display_shipping_class'     => array(
                    'title'       => __('Shipping classes', 'smart-send-logistics'),
                    'type'        => 'multiselect',
                    'class'       => 'wc-enhanced-select',
                    'description' => __('Shipping classes used to display the shipping method.',
                        'smart-send-logistics'),
                    'desc_tip'    => false,
                    'options'     => $shipping_classes,
                ),/*
            'display_company_opt'  => array(
                'title'           => __('Display based on company field', 'smart-send-logistics'),
                'type'            => 'radio',
                'description'     => __('Select when to display the shipping method based on company field.', 'smart-send-logistics'),
                'class'			  => '',
                'default'         => 'no_company',
                'options' => array(
                    'no_company'		=> __('Display regardless of company field', 'smart-send-logistics'),
                    'only_company'	   	=> __('ONLY display if company-field entered', 'smart-send-logistics'),
                    'not_company' 		=> __('Do NOT display if company-field entered', 'smart-send-logistics'),
                ),
                'desc_tip'          => true,
            ),*/
                'user_roles'                 => array(
                    'title'       => __('Exclude User role', 'smart-send-logistics'),
                    'type'        => 'multiselect',
                    'class'       => 'wc-enhanced-select',
                    'description' => __('Do NOT display shipping method for these user roles.', 'smart-send-logistics'),
                    'desc_tip'    => false,
                    'options'     => $user_roles,
                ),
                'return_title'               => array(
                    'title'       => __('Return shipping', 'smart-send-logistics'),
                    'type'        => 'title',
                    'description' => __('Configure how to handle return shipping.', 'smart-send-logistics'),
                    'class'       => '',
                ),
                'return_method'              => array(
                    'title'       => __('Return Shipping Method', 'smart-send-logistics'),
                    'type'        => 'selectopt',
                    'class'       => 'wc-enhanced-select',
                    'description' => __('This is the shipping method used when generating a return shipping labels.',
                        'smart-send-logistics'),
                    'desc_tip'    => true,
                    'options'     => $this->return_shipping_method,
                ),

                'auto_generate_return_label' => array(
                    'title'       => __('Auto Generate Return Label', 'smart-send-logistics'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable', 'smart-send-logistics'),
                    'default'     => 'no',
                    'description' => __('Should a return label automatically be generated whenever a normal shipping labels is generated.',
                        'smart-send-logistics'),
                    'desc_tip'    => false,
                ),
            );

            /*
            $advanced_validation_flag = 'no';
            // Load the advanced validation POST to see if it is enabled and load associated fields
            if( ! empty( $_POST ) ) {
                if( isset( $_POST[ $this->get_field_key('advanced_validation_enable') ] ) ) {
                    $advanced_validation_flag = 'yes';
                }
            } else {
                $instance_settings = get_option( $this->get_instance_option_key(), null );
                $advanced_validation_flag = $instance_settings['advanced_validation_enable'];
            }
            */
        }

        public function validate_title_field($key, $title)
        {

            if (empty($title)) {
                throw new Exception(__('"Method Title" cannot be empty', 'smart-send-logistics'));
            }

            if ($title == $this->method_title) {
                throw new Exception(__('Change the "Method Title" field to something human readable. This is what your customers see at checkout.',
                    'smart-send-logistics'));
            }

            return $this->validate_text_field($key, $title);
        }

        public function validate_method_field($key, $method)
        {

            if (empty($method)) {
                throw new Exception(__('Select a "Shipping Method"', 'smart-send-logistics'));
            }

            return $this->validate_select_field($key, $method);
        }

        public function generate_selectopt_html($key, $data)
        {
            $field_key = $this->get_field_key($key);
            $defaults = array(
                'title'             => '',
                'disabled'          => false,
                'class'             => '',
                'css'               => '',
                'placeholder'       => '',
                'type'              => 'text',
                'desc_tip'          => false,
                'description'       => '',
                'custom_attributes' => array(),
                'options'           => array(),
            );

            $data = wp_parse_args($data, $defaults);

            ob_start();
            ?>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <?php echo $this->get_tooltip_html($data); ?>
                    <label for="<?php echo esc_attr($field_key); ?>"><?php echo wp_kses_post($data['title']); ?></label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span>
                        </legend>
                        <select class="select <?php echo esc_attr($data['class']); ?>"
                                name="<?php echo esc_attr($field_key); ?>" id="<?php echo esc_attr($field_key); ?>"
                                style="<?php echo esc_attr($data['css']); ?>" <?php disabled($data['disabled'],
                            true); ?> <?php echo $this->get_custom_attribute_html($data); ?>>

                            <?php foreach ((array)$data['options'] as $optgroup_key => $optgroup_value) : ?>

                                <?php if (is_array($optgroup_value)) : ?>

                                    <?php echo '<optgroup label="' . esc_attr($optgroup_key) . '">'; ?>

                                    <?php foreach ((array)$optgroup_value as $option_key => $option_value) : ?>

                                        <option value="<?php echo esc_attr($option_key); ?>" <?php selected($option_key,
                                            esc_attr($this->get_option($key))); ?>><?php echo esc_attr($option_value); ?></option>

                                    <?php endforeach; ?>
                                <?php else: ?>

                                    <option value="<?php echo esc_attr($optgroup_key); ?>" <?php selected($optgroup_key,
                                        esc_attr($this->get_option($key))); ?>><?php echo esc_attr($optgroup_value); ?></option>

                                <?php endif; ?>

                                <?php echo '</optgroup>'; ?>

                            <?php endforeach; ?>

                        </select>
                        <?php echo $this->get_description_html($data); ?>
                    </fieldset>
                </td>
            </tr>
            <?php

            return ob_get_clean();
        }


        /**
         * Generate cost weight html.
         *
         * @return string
         */
        public function generate_cost_weight_html()
        {

            ob_start();

            $cost_desc = __('Enter a cost (excl. tax) or sum, e.g. 10.00 * [qty].',
                    'smart-send-logistics') . '<br/><br/>' . __('Use [qty] for the number of items, <br/>[cost] for the total cost of items, and [fee percent=\'10\' min_fee=\'20\' max_fee=\'\'] for percentage based fees.',
                    'smart-send-logistics');

            ?>
            <tr valign="top">
                <th scope="row" class="titledesc"><?php _e('Cost based on weight', 'smart-send-logistics'); ?>:</th>
                <td class="forminp" id="ss_cost_weight">
                    <table class="widefat wc_input_table sortable" cellspacing="0">
                        <thead>
                        <tr>
                            <th class="sort">&nbsp;</th>
                            <th><?php _e('Minimum', 'smart-send-logistics') ?>
                                [<?php echo get_option('woocommerce_weight_unit'); ?>]<a class="tips"
                                                                                         data-tip="<?php _e('Cart weight should be equal to or larger than this value for the shipping rate to be applicable',
                                                                                             'smart-send-logistics'); ?>">[?]</a>
                            </th>
                            <th><?php _e('Maximum', 'smart-send-logistics'); ?>
                                [<?php echo get_option('woocommerce_weight_unit'); ?>]<a class="tips"
                                                                                         data-tip="<?php _e('Cart weight should be strictly less than this value for the shipping rate to be applicable',
                                                                                             'smart-send-logistics'); ?>">[?]</a>
                            </th>
                            <th><?php _e('Cost', 'smart-send-logistics'); ?><a class="tips"
                                                                               data-tip="<?php echo $cost_desc; ?>">[?]</a>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="ss_weight_cost">
                        <?php
                        $i = -1;

                        $weight_costs = $this->get_option('cost_weight',
                            array(
                                array(
                                    'ss_min_weight'  => 0,
                                    'ss_max_weight'  => 20,
                                    'ss_cost_weight' => 15,
                                ),
                            ));

                        if ($weight_costs) {
                            foreach ($weight_costs as $weight_cost) {
                                $i++;

                                echo '<tr class="ss_weight_cost">
                                    <td class="sort"></td>
                                    <td><input type="number" type="number" min="0" step="0.001" value="' . esc_attr($weight_cost['ss_min_weight']) . '" name="ss_min_weight[' . $i . ']" class ="wc_input_decimal" /></td>
                                    <td><input type="number" type="number" min="0" step="0.001" value="' . esc_attr($weight_cost['ss_max_weight']) . '" name="ss_max_weight[' . $i . ']" class ="wc_input_decimal" /></td>
                                    <td><input type="text" value="' . esc_attr($weight_cost['ss_cost_weight']) . '" name="ss_cost_weight[' . $i . ']"  class ="" required/></td>
                                </tr>';
                            }
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="4"><a href="#" class="add button"><?php _e('+ Add shipping rate',
                                        'smart-send-logistics'); ?></a> <a href="#"
                                                                           class="remove_rows button"><?php _e('Remove selected rate(s)',
                                        'smart-send-logistics'); ?></a></th>
                        </tr>
                        </tfoot>
                    </table>
                    <p class="description"><?php _e('Enter the shipping cost excluding tax',
                            'smart-send-logistics'); ?></p>
                    <script type="text/javascript">
                        jQuery(function () {
                            jQuery('#ss_cost_weight').on('click', 'a.add', function () {

                                var size = jQuery('#ss_cost_weight').find('tbody .ss_weight_cost').length;

                                jQuery('<tr class="ss_weight_cost">\
                                    <td class="sort"></td>\
                                    <td><input type="number" min="0" step="0.001" class ="wc_input_decimal" name="ss_min_weight[' + size + ']" /></td>\
                                    <td><input type="number" min="0" step="0.001" class ="wc_input_decimal" name="ss_max_weight[' + size + ']" /></td>\
                                    <td><input type="text" class ="" name="ss_cost_weight[' + size + ']" required/></td>\
                                </tr>').appendTo('#ss_cost_weight table tbody');

                                return false;
                            });
                        });
                    </script>
                </td>
            </tr>
            <?php
            return ob_get_clean();

        }

        public function validate_cost_weight_field()
        {

            $weight_costs = array();

            if (isset($_POST['ss_cost_weight'])) {

                $ss_min_weights = array_map('wc_clean', $_POST['ss_min_weight']);
                $ss_max_weights = array_map('wc_clean', $_POST['ss_max_weight']);
                $ss_cost_weights = array_map('wc_clean', $_POST['ss_cost_weight']);

                foreach ($ss_min_weights as $i => $name) {

                    if (empty($ss_cost_weights[$i])) {
                        continue;
                    }

                    $ss_min_weights[$i] = $this->validate_text_field('ss_min_weight', $ss_min_weights[$i]);
                    $ss_max_weights[$i] = $this->validate_text_field('ss_max_weight', $ss_max_weights[$i]);
                    $ss_cost_weights[$i] = $this->validate_text_field('ss_cost_weight', $ss_cost_weights[$i]);

                    $weight_costs[] = array(
                        'ss_min_weight'  => $ss_min_weights[$i],
                        'ss_max_weight'  => $ss_max_weights[$i],
                        'ss_cost_weight' => $ss_cost_weights[$i],
                    );
                }
            }

            return $weight_costs;
        }

        /**
         * Generate Select HTML.
         *
         * @param  mixed $key
         * @param  mixed $data
         * @since  8.0.0
         * @return string
         */
        public function generate_radio_html($key, $data)
        {
            $field_key = $this->get_field_key($key);
            $defaults = array(
                'title'             => '',
                'disabled'          => false,
                'class'             => '',
                'css'               => '',
                'placeholder'       => '',
                'type'              => 'text',
                'desc_tip'          => false,
                'description'       => '',
                'custom_attributes' => array(),
                'options'           => array(),
            );

            $data = wp_parse_args($data, $defaults);

            ob_start();
            ?>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <?php echo $this->get_tooltip_html($data); ?>
                    <label for="<?php echo esc_attr($field_key); ?>"><?php echo esc_html($data['title']); ?></label>
                </th>
                <td class="forminp forminp-<?php echo sanitize_title($data['type']) ?>">
                    <fieldset>
                        <ul>
                            <?php
                            foreach ($data['options'] as $option_key => $option_value) {
                                ?>
                                <li>
                                    <label><input
                                                name="<?php echo esc_attr($field_key); ?>"
                                                value="<?php echo esc_attr($option_key); ?>"
                                                type="radio"
                                                style="<?php echo esc_attr($data['css']); ?>"
                                                class="<?php echo esc_attr($data['class']); ?>"
                                            <?php echo $this->get_custom_attribute_html($data); ?>
                                            <?php checked($option_key, esc_attr($this->get_option($key))); ?>
                                        /> <?php echo esc_attr($option_value); ?></label>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                        <?php echo $this->get_description_html($data); ?>
                    </fieldset>
                </td>
            </tr>

            <?php

            return ob_get_clean();
        }

        public function calculate_shipping($package = array())
        {
            $rate = array(
                'id'        => $this->get_rate_id(),
                'label'     => $this->title,
                'cost'      => 0,
                'meta_data' => array(
                    'smart_send_shipping_method'            => $this->get_instance_option('method'),
                    'smart_send_return_method'              => $this->get_instance_option('return_method'),
                    'smart_send_auto_generate_return_label' => $this->get_instance_option('auto_generate_return_label'),
                ),
                'package'   => $package,
            );

            // write id of shipping method to log
            SS_SHIPPING_WC()->log_msg('Handling shipping rate <' . $rate['id'] . '> with title: ' . $rate['label']);
            SS_SHIPPING_WC()->log_msg('Rate details (json decode for details): ' . json_encode($rate));

            // Set tax status based on selection otherwise always taxed
            $this->tax_status = $this->get_option('tax_status');

            // Check if free shipping, otherwise claculate based on weight and evaluate formulas
            if ($this->is_free_shipping($package)) {

                $rate['cost'] = $this->get_option('flatfee_cost');
                $this->add_rate($rate);
                // write to log, that shipping rate is added
                SS_SHIPPING_WC()->log_msg('Free shipping rate added');

            } else {
                $cart_weight = WC()->cart->get_cart_contents_weight();
                $weight_costs = $this->get_option('cost_weight', array());

                if ($weight_costs) {
                    foreach ($weight_costs as $weight_cost) {

                        // If empty ignore field and continue, otherwise check if equal or greater than
                        if (empty($weight_cost['ss_min_weight']) || ($cart_weight >= $weight_cost['ss_min_weight'])) {
                            // IF empty ignore field and contine, otherwise check if less than
                            if (empty($weight_cost['ss_max_weight']) || ($cart_weight < $weight_cost['ss_max_weight'])) {
                                // If cost NOT empty add a fee
                                if (!empty($weight_cost['ss_cost_weight'])) {

                                    $rate['cost'] = $this->evaluate_cost($weight_cost['ss_cost_weight'], array(
                                        'qty'  => $this->get_package_item_qty($package),
                                        'cost' => $package['contents_cost'],
                                    ));

                                    $this->add_rate($rate);
                                    // write to log, that shipping rate is added
                                    SS_SHIPPING_WC()->log_msg('Weight based shipping rate added (json decode for details): ' . json_encode($rate));
                                }
                            }
                        }
                    }
                }
            }

            /**
             * Developers can add additional rates based on this one via this action
             *
             * This example shows how you can add an extra rate based on this flat rate via custom function:
             *
             *        add_action( 'woocommerce_smart_send_shipping_shipping_add_rate', 'add_another_custom_rate', 10, 2 );
             *
             *        function add_another_custom_rate( $method, $rate ) {
             *            $new_rate          = $rate;
             *            $new_rate['id']    .= ':' . 'custom_rate_name'; // Append a custom ID.
             *            $new_rate['label'] = 'Rushed Shipping'; // Rename to 'Rushed Shipping'.
             *            $new_rate['cost']  += 2; // Add $2 to the cost.
             *
             *            // Add it to WC.
             *            $method->add_rate( $new_rate );
             *        }.
             */
            do_action('woocommerce_' . $this->id . '_shipping_add_rate', $this, $rate);
        }

        public function is_available($package)
        {
            $is_available = true;
            $one_in_array = false;
            $all_in_array = true;

            if ($this->get_instance_option('advanced_settings_enable') == 'yes') {

                // Display based on shipping class
                $display_shipping_class = $this->get_instance_option('display_shipping_class');
                if (!empty($display_shipping_class)) {

                    foreach ($package['contents'] as $item_id => $values) {

                        if ($values['data']->needs_shipping()) {
                            $found_class = $values['data']->get_shipping_class();

                            if (in_array($found_class, $display_shipping_class)) {
                                $one_in_array = true;
                            } else {
                                $all_in_array = false;
                            }

                        }
                    }

                    $display_shipping_class_opt = $this->get_instance_option('display_shipping_class_opt');

                    switch ($display_shipping_class_opt) {
                        case 'all_shipping_class' :
                            $is_available = $all_in_array;

                            $class_log_message = ', because ALL products belong to one of the shipping classes';
                            break;
                        case 'one_shipping_class' :
                            $is_available = $one_in_array;

                            $class_log_message = ', because at least ONE product belongs to one of the shipping classes';
                            break;
                        case 'nall_shipping_class' :
                            $is_available = !$one_in_array;

                            $class_log_message = ', because ALL products do NOT belong to one of the shipping classes';
                            break;
                        case 'none_shipping_class' :
                            $is_available = !$all_in_array;

                            $class_log_message = ', because at least ONE product does NOT belongs to one of the shipping classes';
                            break;
                    }
                }

                if (!empty($class_log_message)) {
                    if ($is_available) {
                        SS_SHIPPING_WC()->log_msg('Shipping method IS available' . $class_log_message);
                    } else {
                        SS_SHIPPING_WC()->log_msg('Shipping method is NOT available' . $class_log_message);
                    }
                }

                // Exclude customer roles
                $exclude_roles = $this->get_instance_option('user_roles');
                if (!empty($exclude_roles)) {

                    $user_id = get_current_user_id();
                    if (empty($user_id)) {
                        $customer_roles = $this->get_guest_role();
                    } else {
                        $user_meta = get_userdata($user_id);
                        $customer_roles = $user_meta->roles; //array of roles the user is part of.
                    }

                    foreach ($customer_roles as $key => $customer_role) {
                        $customer_role = strtolower($customer_role); // ensure all names are lowercase to compare keys correctly
                        if (in_array($customer_role, $exclude_roles)) {
                            $is_available = false;

                            SS_SHIPPING_WC()->log_msg('Shipping method available NOT available, because customer role "' . $customer_role . '"is being excluded.');
                            break;
                        }
                    }
                }
            }

            return apply_filters('woocommerce_shipping_' . $this->id . '_is_available', $is_available, $package, $this);
        }

        /**
         * See if free shipping is available based on the package and cart.
         *
         * @param array $package Shipping package.
         * @return bool
         */
        public function is_free_shipping($package)
        {
            $has_coupon = false;
            $has_met_min_amount = false;
            $requires = $this->get_instance_option('requires');
            $min_amount = $this->get_instance_option('min_amount');

            if (in_array($requires, array('coupon', 'either', 'both'))) {
                if ($coupons = WC()->cart->get_coupons()) {
                    foreach ($coupons as $code => $coupon) {
                        if ($coupon->is_valid() && $coupon->get_free_shipping()) {
                            $has_coupon = true;
                            break;
                        }
                    }
                }
            }

            if (in_array($requires, array('min_amount', 'either', 'both'))) {
                $total = WC()->cart->get_displayed_subtotal();

                if ('incl' === WC()->cart->tax_display_cart) {
                    $total = round($total - (WC()->cart->get_cart_discount_total() + WC()->cart->get_cart_discount_tax_total()),
                        wc_get_price_decimals());
                } else {
                    $total = round($total - WC()->cart->get_cart_discount_total(), wc_get_price_decimals());
                }

                if ($total >= $min_amount) {
                    $has_met_min_amount = true;
                }
            }

            switch ($requires) {
                case 'min_amount' :
                    $is_available = $has_met_min_amount;

                    $free_log_message = ', because the total is ' . $total . ' a minimum order amount of ' . $min_amount . ' is needed.';
                    break;
                case 'coupon' :
                    $is_available = $has_coupon;

                    $free_log_message = ', because a coupon is needed.';
                    break;
                case 'both' :
                    $is_available = $has_met_min_amount && $has_coupon;

                    $free_log_message = ', because the total is ' . $total . ' a minimum order amount of ' . $min_amount . ' is needed AND a coupon is needed.';
                    break;
                case 'either' :
                    $is_available = $has_met_min_amount || $has_coupon;

                    $free_log_message = ', because the total is ' . $total . ' a minimum order amount of ' . $min_amount . ' is needed OR a coupon is needed.';
                    break;
                case 'enabled' :
                    $is_available = true;

                    $free_log_message = ', because it is always enabled.';
                    break;
                case 'disabled' :
                    $is_available = false;

                    $free_log_message = ', because it is always disabled.';
                    break;
                default :
                    $is_available = false;

                    $free_log_message = ', because it is not available.';
                    break;
            }

            if ($free_log_message) {
                if ($is_available) {
                    SS_SHIPPING_WC()->log_msg('Flat rate shipping IS available' . $free_log_message);
                } else {
                    SS_SHIPPING_WC()->log_msg('Flat rate shipping is NOT available' . $free_log_message);
                }
            }

            return apply_filters('woocommerce_shipping_' . $this->id . '_is_free_shipping', $is_available, $package,
                $this);
        }

	    /**
	     * Get the human readable name of the Smart Send shipping method
	     * Example: 'PostNord: Closest pick-up point (MyPack Collect)'
	     *
	     * Details: This method look for valid method with code $shipping_method_code
         * in the $shipping_method array from this SS_Shipping_WC_Method class
	     *
	     * @param string $shipping_method_code    Id that identifies the Smart Send method. Example 'postnord_collect'
	     * @return string
	     */
        public function get_shipping_method_name($shipping_method_code) {
            if( $this->shipping_method ) {
                foreach ($this->shipping_method as $carrier_name => $carrier_code) {
                    if ( is_array( $carrier_code ) ) {
                        foreach ($carrier_code as $method_code => $method_name) {
                            if ( $method_code == $shipping_method_code ) {
                                return $method_name;
                            }
                        }
                    }
                }
            }
            return '';
        }
    }

endif;
