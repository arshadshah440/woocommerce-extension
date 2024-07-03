<?php

/**
 * Plugin Name: Smart Send Shipping for WooCommerce
 * Plugin URI: https://wordpress.org/plugins/smart-send-logistics/
 * Description: Smart Send Shipping for WooCommerce
 * Author: Smart Send ApS
 * Author URI: https://www.smartsend.io
 * Text Domain: smart-send-logistics
 * Version: 8.1.1
 * Requires Plugins: woocommerce
 * WC requires at least: 4.7.0
 * WC tested up to: 7.9
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('SS_Shipping_WC')) :

    class SS_Shipping_WC
    {

        private $version = "8.1.1";

        /**
         * Instance to call certain functions globally within the plugin
         *
         * @var SS_Shipping_WC
         */
        protected static $_instance = null;

        /**
         * Smart Send Shipping Order for label and tracking.
         *
         * @var SS_Shipping_WC_Order
         */
        public $ss_shipping_wc_order = null;
        /**
         * Smart Send Shipping Product
         *
         * @var SS_Shipping_WC_Order
         */
        public $ss_shipping_wc_product = null;

        /**
         * Smart Send Frontend
         *
         * @var SS_Shipping_Frontend
         */
        protected $ss_shipping_frontend = null;

        /**
         * Smart Send Shipping Order for label and tracking.
         *
         * @var SS_Shipping_Logger
         */
        protected $logger = null;

        /**
         * Smart Send agent address formats
         *
         * @var array
         */
        protected $agents_address_format = array();

        /**
         * Smart Send api handle
         *
         * @var object
         */
        protected $api_handle = null;

        /**
         * Smart Send Plugin Screen Updates
         *
         * @var object
         */
        protected $ss_plugin_screen_updates = null;

        /**
         * Construct the plugin.
         */
        public function __construct()
        {
            add_action('before_woocommerce_init', [$this, 'declaring_hpos_compatibility']);

            $this->define_constants();
            $this->includes();
            $this->init_hooks();

            $this->agents_address_format = array(
                '1' => __('#Company', 'smart-send-logistics') . ', ' . __('#Street', 'smart-send-logistics'),
                '2' => __('#Company', 'smart-send-logistics') . ', ' . __(
                    '#Street',
                    'smart-send-logistics'
                ) . ', ' . __('#Zipcode', 'smart-send-logistics'),
                '3' => __('#Company', 'smart-send-logistics') . ', ' . __(
                    '#Street',
                    'smart-send-logistics'
                ) . ', ' . __('#City', 'smart-send-logistics'),
                '4' => __('#Company', 'smart-send-logistics') . ', ' . __(
                    '#Street',
                    'smart-send-logistics'
                ) . ', ' . __('#Zipcode', 'smart-send-logistics') . ' ' . __(
                    '#City',
                    'smart-send-logistics'
                ),
                '5' => __('#Company', 'smart-send-logistics') . ', ' . __('#Zipcode', 'smart-send-logistics'),
                '6' => __('#Company', 'smart-send-logistics') . ', ' . __(
                    '#Zipcode',
                    'smart-send-logistics'
                ) . ', ' . __('#City', 'smart-send-logistics'),
                '7' => __('#Company', 'smart-send-logistics') . ', ' . __('#City', 'smart-send-logistics'),
            );
        }

        public function declaring_hpos_compatibility()
        {
            if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
            }
        }

        /**
         * Main Smart Send Shipping Instance.
         *
         * Ensures only one instance is loaded or can be loaded.
         *
         * @static
         * @see SS_Shipping_WC()
         * @return SS_Shipping_WC - Main instance.
         */
        public static function instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * Define WC Constants.
         */
        private function define_constants()
        {
            $upload_dir = wp_upload_dir();

            // Path related defines
            $this->define('SS_SHIPPING_PLUGIN_FILE', __FILE__);
            $this->define('SS_SHIPPING_PLUGIN_BASENAME', plugin_basename(__FILE__));
            $this->define('SS_SHIPPING_PLUGIN_DIR_PATH', untrailingslashit(plugin_dir_path(__FILE__)));
            $this->define('SS_SHIPPING_PLUGIN_DIR_URL', untrailingslashit(plugins_url('/', __FILE__)));
            $this->define('SS_SHIPPING_VERSION', $this->version);
            $this->define('SS_SHIPPING_LOG_DIR', $upload_dir['basedir'] . '/wc-logs/');
            $this->define('SS_SHIPPING_METHOD_ID', 'smart_send_shipping');
            $this->define('SS_BUTTON_TEST_CONNECTION', __('Validate API Token', 'smart-send-logistics'));
            $this->define('SS_SHIPPINF_PLUGIN_DIRNAME', dirname(__FILE__));
        }

        /**
         * Include required core files used in admin and on the frontend.
         */
        public function includes()
        {
            // Auto loader class
            include_once('includes/class-ss-shipping-autoloader.php');
            include_once('includes/lib/Smartsend/Api.php');
            require_once plugin_dir_path(__FILE__) . 'includes/index.assest.php';
        }

        protected function init_hooks()
        {
            add_action('init', array($this, 'init'), 0);
            add_action('init', array($this, 'load_textdomain'));

            add_filter('plugin_action_links_' . SS_SHIPPING_PLUGIN_BASENAME, array($this, 'plugin_action_links'));
            add_filter('plugin_row_meta', array($this, 'ss_shipping_plugin_row_meta'), 10, 2);

            add_action('wp_enqueue_scripts', array($this, 'enqueue_shipping_heading_slotfill_script'));

            // Register the integration with WooCommerce Blocks for the cart and checkout blocks.
            add_action(
                'woocommerce_blocks_cart_block_registration',
                function ($integration_registry) {
                    $integration_registry->register(new WooCommerce_Example_Plugin_Integration());
                }
            );

            add_action(
                'woocommerce_blocks_checkout_block_registration',
                function ($integration_registry) {
                    $integration_registry->register(new WooCommerce_Example_Plugin_Integration());
                }
            );
            add_action('admin_enqueue_scripts', array($this, 'ss_shipping_theme_enqueue_admin_styles'));
            add_action('wp_enqueue_scripts', array($this, 'ss_shipping_theme_enqueue_frontend_styles'));

            add_filter('woocommerce_shipping_methods', array($this, 'add_shipping_method'));
            add_filter('woocommerce_package_rates', array($this, 'ss_sort_shipping_methods'));

            // Test connection
            add_action('wp_ajax_ss_test_connection', array($this, 'ss_test_connection_callback'));
        }


        /**
         * Initialize the plugin.
         */
        public function init()
        {

            // Checks if WooCommerce 2.6 is installed.
            if (defined('WOOCOMMERCE_VERSION') && version_compare(WOOCOMMERCE_VERSION, '2.6', '>=')) {
                $this->ss_shipping_frontend = new SS_Shipping_Frontend();
                $this->ss_shipping_wc_order = new SS_Shipping_WC_Order();
                $this->ss_shipping_wc_product = new SS_Shipping_WC_Product();
                $this->ss_plugin_screen_updates = new SS_Plugins_Screen_Updates();
            } else {
                // Throw an admin error informing the user this plugin needs WooCommerce to function
                add_action('admin_notices', array($this, 'notice_wc_required'));
            }
        }

        /**
         * Localisation
         */
        public function load_textdomain()
        {
            load_plugin_textdomain('smart-send-logistics', false, dirname(plugin_basename(__FILE__)) . '/lang/');
        }

        /**
         * Load Admin CSS
         */
        public function ss_shipping_theme_enqueue_admin_styles()
        {
            wp_enqueue_style('ss-shipping-admin-css', SS_SHIPPING_PLUGIN_DIR_URL . '/assets/css/ss-shipping-admin.css');
        }

        /**
         * Load Frontend CSS
         */
        public function ss_shipping_theme_enqueue_frontend_styles()
        {
            wp_enqueue_style(
                'ss-shipping-frontend-css',
                SS_SHIPPING_PLUGIN_DIR_URL . '/assets/css/ss-shipping-frontend.css'
            );
        }

        function enqueue_shipping_heading_slotfill_script()
        {
            wp_enqueue_script('ss-shipping-frontend-js', SS_SHIPPING_PLUGIN_DIR_URL . '/dist/bundle.js', array('wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-i18n'), SS_SHIPPING_VERSION, true);
        }
        /**
         * Define constant if not already set.
         *
         * @param  string $name
         * @param  string|bool $value
         */
        public function define($name, $value)
        {
            if (!defined($name)) {
                define($name, $value);
            }
        }


        /**
         * Show action links on the plugin screen.
         *
         * @param    mixed $links Plugin Action links
         * @return    array
         */
        public static function plugin_action_links($links)
        {
            $action_links = array(
                'settings' => '<a href="' . admin_url('admin.php?page=wc-settings&tab=shipping&section=smart_send_shipping') . '" aria-label="' . esc_attr__(
                    'View WooCommerce settings',
                    'smart-send-logistics'
                ) . '">' . esc_html__('Settings', 'smart-send-logistics') . '</a>',
            );

            return array_merge($action_links, $links);
        }

        /**
         * Show row meta on the plugin screen.
         *
         * @param    mixed $links Plugin Row Meta
         * @param    mixed $file Plugin Base file
         * @return    array
         */
        function ss_shipping_plugin_row_meta($links, $file)
        {

            if (SS_SHIPPING_PLUGIN_BASENAME == $file) {
                $row_meta = array(
                    'configuration' => '<a href="' . esc_url(apply_filters(
                        'smart_send_configuration_url',
                        'https://smartsend.io/woocommerce/configuration/'
                    )) . '" title="' . esc_attr(__(
                        'Configuration guide',
                        'smart-send-logistics'
                    )) . '" target="_blank">' . __(
                        'Configuration guide',
                        'smart-send-logistics'
                    ) . '</a>',
                    'support'       => '<a href="' . esc_url(apply_filters(
                        'smart_send_support_url',
                        'https://smartsend.io/support/'
                    )) . '" title="' . esc_attr(__(
                        'Support',
                        'smart-send-logistics'
                    )) . '" target="_blank">' . __(
                        'Support',
                        'smart-send-logistics'
                    ) . '</a>',
                );

                return array_merge($links, $row_meta);
            }

            return (array)$links;
        }

        /**
         * Add a new integration to WooCommerce.
         */
        public function add_shipping_method($shipping_method)
        {
            $ss_shipping_shipping_method = 'SS_Shipping_WC_Method';
            $shipping_method['smart_send_shipping'] = $ss_shipping_shipping_method;

            return $shipping_method;
        }

        /**
         * Admin error notifying user that WC is required
         */
        public function notice_wc_required()
        {
?>
            <div class="error">
                <p><?php _e(
                        'Smart Send Shipping requires WooCommerce 2.6 and above to be installed and activated!',
                        'smart-send-logistics'
                    ); ?></p>
            </div>
<?php
        }

        /**
         * Get Smart Send Shipping settings
         */
        public function get_ss_shipping_settings()
        {
            return get_option('woocommerce_' . SS_SHIPPING_METHOD_ID . '_settings');
        }

        /**
         * Log debug message
         */
        public function log_msg($msg)
        {
            $shipping_ss_settings = $this->get_ss_shipping_settings();
            $ss_debug = isset($shipping_ss_settings['ss_debug']) ? $shipping_ss_settings['ss_debug'] : 'yes';

            if (!$this->logger) {
                $this->logger = new SS_Shipping_Logger($ss_debug);
            }

            $this->logger->write($msg);
        }

        /**
         * Get debug log file URL
         */
        public function get_log_url()
        {
            $shipping_ss_settings = $this->get_ss_shipping_settings();
            $ss_debug = isset($shipping_ss_settings['ss_debug']) ? $shipping_ss_settings['ss_debug'] : 'yes';

            if (!$this->logger) {
                $this->logger = new SS_Shipping_Logger($ss_debug);
            }

            return $this->logger->get_log_url();
        }

        /**
         * Get Agent Address Format
         */
        public function get_agents_address_format()
        {
            return $this->agents_address_format;
        }

        /**
         * Get Smart Shipping Order Object
         */
        public function get_ss_shipping_wc_order()
        {
            return $this->ss_shipping_wc_order;
        }

        /**
         * Get the human readable name of the Smart Send shipping method
         * Example: 'PostNord: Closest pick-up point (MyPack Collect)'
         *
         * Details: This method loops over all WC_Shipping methods and finds the
         * instance of SS_Shipping_WC_Method. It then takes the SS_Shipping_WC_Method
         * instance and finds the human readable shipping method name using this.
         *
         * @param string $shipping_method_code    Id that identifies the Smart Send method. Example 'postnord_collect'
         * @return string
         */
        public function get_shipping_method_name_from_all_shipping_method_instances($shipping_method_code)
        {
            /*
             * Returns all registered shipping methods for usage.
             *
             * @access public
             * @return array
             */
            $shipping_methods = WC()->shipping->get_shipping_methods();

            if (is_array($shipping_methods)) {
                foreach ($shipping_methods as $key => $shipping_method_instance) {
                    if ($shipping_method_instance instanceof SS_Shipping_WC_Method) {
                        return $shipping_method_instance->get_shipping_method_name($shipping_method_code);
                    }
                }
            }
            return '';
        }

        /**
         * Get the Carrier of the shipping method
         *
         * @return string
         */
        public function get_shipping_method_carrier($ship_method)
        {

            $ship_method_parts = $this->get_shipping_method_part($ship_method);

            $arr_size = sizeof($ship_method_parts);

            if (isset($ship_method_parts[0])) {
                return $ship_method_parts[0];
            }

            return $ship_method;
        }

        /**
         * Get the Method of the shipping method
         *
         * @return string
         */
        public function get_shipping_method_type($ship_method)
        {

            $ship_method_parts = $this->get_shipping_method_part($ship_method);

            $arr_size = sizeof($ship_method_parts);

            if (isset($ship_method_parts[1])) {
                return $ship_method_parts[1];
            }

            return $ship_method;
        }

        /**
         * Shipping Method helper function
         *
         * @return array
         */
        protected function get_shipping_method_part($ship_method)
        {

            if (empty($ship_method)) {
                return $ship_method;
            }

            // Assumes format 'carrier_type'
            $new_ship_method = explode('_', $ship_method);

            return $new_ship_method;
        }

        public function get_api_handle()
        {

            if (!$this->api_handle) {
                $api_token = $this->get_api_token_setting();

                // Initiate an API handle with the login credentials.
                $demo_mode = $this->get_demo_mode_setting();
                $website_url = $this->get_website_url();
                $this->api_handle = new \Smartsend\Api($api_token, $website_url, $demo_mode);
            }

            return $this->api_handle;
        }

        /**
         * Get the url of the current site like example.com
         *
         * @param string|null $website url
         * @return string
         */
        public function get_website_url($website = null)
        {
            if (!$website) {
                $website = get_site_url();
            }
            return parse_url($website, PHP_URL_HOST);
        }

        /**
         * Get the setting 'demo-mode'
         *
         * @return boolean
         */
        public function get_demo_mode_setting()
        {
            $ss_shipping_settings = $this->get_ss_shipping_settings();
            return empty($ss_shipping_settings['demo']) ? true : ($ss_shipping_settings['demo'] == 'yes' ? true : false);
        }

        /**
         * Get the setting 'save_shipping_labels_in_uploads'
         *
         * @return boolean
         */
        public function get_setting_save_shipping_labels_in_uploads()
        {
            $ss_shipping_settings = $this->get_ss_shipping_settings();
            return empty($ss_shipping_settings['save_shipping_labels_in_uploads']) ? false : ($ss_shipping_settings['save_shipping_labels_in_uploads'] == 'yes' ? true : false);
        }

        /**
         * Get the url of the current site
         *
         * @param string|null $api_token
         * @return string
         */
        public function get_api_token_setting($api_token = null)
        {
            if (!$api_token) {
                $ss_shipping_settings = $this->get_ss_shipping_settings();
                $api_token = empty($ss_shipping_settings['api_token']) ? null : $ss_shipping_settings['api_token'];
            }

            if (strpos($api_token, ',') && strpos($api_token, ':')) {
                //The API Token field contains multiple tokens in the format:
                //site1:apitoken1,site2:apitoken2,....
                $tokens = array();
                $site_and_tokens = explode(',', $api_token);
                foreach ($site_and_tokens as $site_and_token) {
                    $parts = explode(':', $site_and_token);
                    if (!empty($parts[0]) && !empty($parts[1])) {
                        $tokens[$parts[0]] = $parts[1]; //key=site, value=apitoken
                    }
                }

                if (!empty($tokens[$this->get_website_url()])) {
                    return $tokens[$this->get_website_url()];
                }
            }

            return $api_token;
        }

        /**
         * Validate the API token
         *
         * @return boolean
         */
        public function validate_api_token()
        {

            if ($this->get_api_handle()) {
                if ($this->api_handle->getAuthenticatedUser()) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        /**
         * Test connection AJAX call
         */
        public function ss_test_connection_callback()
        {
            check_ajax_referer('ss-test-connection', 'test_connection_nonce');

            if ($this->validate_api_token()) {
                $connection_msg = sprintf(
                    __(
                        'API Token verified: Connected to Smart Send as %s from %s',
                        'smart-send-logistics'
                    ),
                    $this->get_api_handle()->getData()->email,
                    $this->get_api_handle()->getData()->website
                );
                $error = 0;
            } elseif ($this->get_api_handle()) {
                $connection_msg = sprintf(__(
                    'API Token validation failed: %s. Make sure to save the settings before validating.',
                    'smart-send-logistics'
                ), $this->get_api_handle()->getError()->message);
                $error = 1;
            } else {
                $connection_msg = __(
                    'API Token validation failed: Please enter an API Token and save the settings before validating.',
                    'smart-send-logistics'
                );
                $error = 1;
            }

            $this->log_msg($connection_msg);

            wp_send_json(array(
                'message'    => $connection_msg,
                'error'      => $error,
                'button_txt' => SS_BUTTON_TEST_CONNECTION,
            ));

            wp_die();
        }

        /**
         * Find the closest agents by address - Convenience wrapper
         *
         * @param $carrier string unique carrier code
         * @param $country string ISO3166-A2 Country code
         * @param $postal_code string
         * @param $street string
         * @param $city string optional but providing a city yields better accuracy for geocoding
         *
         * @return array
         */
        public function ss_find_closest_agents_by_address($carrier, $country, $postal_code, $street, $city = null)
        {
            return $this->ss_shipping_frontend
                ->find_closest_agents_by_address($carrier, $country, $postal_code, $city, $street);
        }

        /**
         * Sort the shipping methods according to setting
         *
         * @param array $available_shipping_methods
         *
         * @return array
         */
        public function ss_sort_shipping_methods($available_shipping_methods)
        {
            //  if there are no rates don't do anything
            if (!$available_shipping_methods) {
                return $available_shipping_methods;
            }

            // Get setting
            $ss_shipping_settings = $this->get_ss_shipping_settings();
            if (!empty($ss_shipping_settings['sort_methods_by_cost']) && $ss_shipping_settings['sort_methods_by_cost'] == 'yes') {
                // get an array of prices
                $prices = array();
                foreach ($available_shipping_methods as $shipping_method) {
                    // the price is the cost + taxes
                    $prices[] = $shipping_method->cost + array_sum($shipping_method->taxes);
                }

                // use the prices to sort the rates
                array_multisort($prices, $available_shipping_methods);

                // write to log
                $this->log_msg('Shipping methods sorted by cost');
            }

            // return the rates
            return $available_shipping_methods;
        }
    }

endif;

function SS_SHIPPING_WC()
{
    return SS_Shipping_WC::instance();
}

$SS_Shipping_WC = SS_SHIPPING_WC();
