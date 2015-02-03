<?php
/**
 * Plugin Name: Iugu WooCommerce
 * Plugin URI: https://github.com/iugu/iugu-woocommerce
 * Description: Gateway de pagamento Iugu para WooCommerce.
 * Author: Iugu
 * Author URI: http://iugu.com/
 * Version: 1.0.0
 * License: GPLv2 or later
 * Text Domain: iugu-woocommerce
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Iugu' ) ) :

/**
 * WooCommerce Iugu main class.
 */
class WC_Iugu {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	const VERSION = '1.0.0';

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin actions.
	 */
	public function __construct() {
		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Checks with WooCommerce is installed.
		if ( class_exists( 'WC_Payment_Gateway' ) ) {

			// Include the WC_Iugu_Gateway class.
			include_once 'includes/iuguApi/lib/Iugu.php';
			include_once 'includes/class-wc-iugu-gateway.php';
			include_once 'includes/iugu-checkout-custom-fields.php';

			// Links for reach the setting page from plugin list
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );

			//Hook to add Iugu Gateway to WooCommerce
			add_filter( 'woocommerce_payment_gateways', array( $this, 'add_gateway' ) );

		} else {
			// Notifications
			add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
		}
	}

	/**
	 * Return an instance of this class.
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Load the plugin text domain for translation.
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'iugu-woocommerce' );

		load_textdomain( 'iugu-woocommerce', trailingslashit( WP_LANG_DIR ) . 'iugu-woocommerce/iugu-woocommerce-' . $locale . '.mo' );
		load_plugin_textdomain( 'iugu-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Add the gateway to WooCommerce.
	 *
	 * @param  array $methods WooCommerce payment methods.
	 *
	 * @return array          Payment methods with Iugu.
	 */
	public function add_gateway( $methods ) {
		$methods[] = 'WC_Iugu_Gateway';

		return $methods;
	}

	/**
	 * WooCommerce fallback notice.
	 *
	 * @return string
	 */
	public function woocommerce_missing_notice() {
		echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Iugu Gateway depends on the last version of %s to work!', 'iugu-woocommerce' ), '<a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a>' ) . '</p></div>';
	}

	/**
	 * Hooked function to create a link to settings from plugins list.
	 *
	 * @param  array $links
	 *
	 * @return array
	 */
	public function plugin_action_links( $links ) {
		$links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_iugu_gateway' ) . '">' . __( 'Settings', 'iugu-woocommerce' ) . '</a>';

		return $links;
	}
}

add_action( 'plugins_loaded', array( 'WC_Iugu', 'get_instance' ) );

endif;
