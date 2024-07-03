<?php
use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

/**
 * Class for integrating with WooCommerce Blocks
 */
class WooCommerce_Example_Plugin_Integration implements IntegrationInterface {
	/**
	 * The name of the integration.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'woocommerce-new-plugin';
	}

	/**
	 * When called invokes any initialization/setup for the integration.
	 */
	public function initialize() {
		$script_path = '/src/index.js';
		// $style_path = '/build/style-index.css';

        /**
         * The assets linked below should be a path to a file, for the sake of brevity
         * we will assume \WooCommerce_Example_Plugin_Assets::$plugin_file is a valid file path
        */
		$script_url = plugin_dir_url(__FILE__) . '../src/index.js';
		// $style_url = plugins_url( $style_path, \WooCommerce_Example_Plugin_Assets::$plugin_file );

		$script_asset_path = plugin_dir_path(__FILE__) . '../src/index.js';
		$script_asset = file_exists($script_asset_path)
		? require $script_asset_path
		: array(
			'dependencies' => array('wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-i18n'),
			'version' => $this->get_file_version($script_path),
		);

		// wp_enqueue_style(
		// 	'ss-shipping-frontend-js',
		// 	$style_url,
		// 	[],
		// 	$this->get_file_version( $style_path )
		// );

		wp_register_script(
            'ss-shipping-frontend-js',
            $script_url,
            $script_asset['dependencies'],
            $script_asset['version'],
            true
        );

		wp_script_add_data( 'ss-shipping-frontend-js', 'type', 'text/javascript' );

		// wp_set_script_translations(
		// 	'ss-shipping-frontend-js',
		// 	'woocommerce-example-plugin',
		// 	dirname( \WooCommerce_Example_Plugin_Assets::$plugin_file ) . '/languages'
		// );
	}

	/**
	 * Returns an array of script handles to enqueue in the frontend context.
	 *
	 * @return string[]
	 */
	public function get_script_handles() {
		return array( 'ss-shipping-frontend-js' );
	}

	/**
	 * Returns an array of script handles to enqueue in the editor context.
	 *
	 * @return string[]
	 */
	public function get_editor_script_handles() {
		return array( 'ss-shipping-frontend-js' );
	}

	/**
	 * An array of key, value pairs of data made available to the block on the client side.
	 *
	 * @return array
	 */
	public function get_script_data() {
	    return [];
	}

	/**
	 * Get the file modified time as a cache buster if we're in dev mode.
	 *
	 * @param string $file Local path to the file.
	 * @return string The cache buster value to use for the given file.
	 */
	protected function get_file_version( $file ) {
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG && file_exists( $file ) ) {
			return filemtime( $file );
		}
		
		// As above, let's assume that WooCommerce_Example_Plugin_Assets::VERSION resolves to some versioning number our
		// extension uses.
		return SS_SHIPPING_VERSION;
	}
}