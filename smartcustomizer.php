<?php

defined('ABSPATH') || exit;

/*
 * Plugin Name:       Smart Customizer for WooCommerce
 * Plugin URI:        https://www.smartcustomizer.com 
 * Description:       Add a visual product customizer to your online store so that customers can create personalized designs! Smart Customizer is a web-to-print product customization solution that lets customers create their own designs by adding custom text, vector shapes, and images.
 * Version:           1.1.0
 * Requires at least: 4.7
 * Requires PHP:      7.2
 * Tested up to:      6.5.2
 * WC tested up to:   8.7
 * Author:            Smartcustomizer 
 * Author URI:        https://www.smartcustomizer.com/contact-us 
 * License:           GPL2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:       /languages
*/



if (!class_exists('Smartcustomizer')) {
add_action('before_woocommerce_init', function(){
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
});

	class Smartcustomizer
	{

		protected static $_instance = null;


		public static function instance()
		{
			if (is_null(self::$_instance)) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}


		public function __construct()
		{

			if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
				return;
			}

			add_action('plugins_loaded', array($this, 'init'));
		}


		public static function init()
		{

			require_once plugin_dir_path(__FILE__) . 'includes/smartcustomizer-config.php';
			require_once plugin_dir_path(__FILE__) . 'includes/smartcustomizer-functions.php';

			if (!is_admin()) {
				require_once plugin_dir_path(__FILE__) . 'includes/class-smartcustomizer-webservice.php';
				require_once plugin_dir_path(__FILE__) . 'includes/class-smartcustomizer-shop.php';
			} else {
				require_once plugin_dir_path(__FILE__) . 'includes/class-smartcustomizer-admin.php';
			}

			require_once plugin_dir_path(__FILE__) . 'includes/class-smartcustomizer-email.php';
			require_once plugin_dir_path(__FILE__) . 'includes/class-smartcustomizer-integration.php';

			add_action('rest_api_init', function () {
				require_once plugin_dir_path(__FILE__) .  'includes/class-smartcustomizer-rest-access-controller.php';
				$controller = new Smartcustomizer_REST_Access_Controller();
				$controller->register_routes();
			});

			add_filter('woocommerce_rest_is_request_to_rest_api', array(__CLASS__, 'is_request_to_rest_api'));
			add_filter('woocommerce_integrations', array(__CLASS__, 'add_integration'));
		}


		public static function is_request_to_rest_api($is_api_request)
		{
			if ($is_api_request) {
				return true;
			}

			if (empty($_SERVER['REQUEST_URI'])) {
				return false;
			}

			$rest_prefix = trailingslashit(rest_get_url_prefix());
			$request_uri = esc_url_raw(wp_unslash($_SERVER['REQUEST_URI']));

			if (false !== strpos($request_uri, $rest_prefix . 'smartcustomizer/')) {
				return true;
			}

			return false;
		}


		public static function add_integration($integrations)
		{
			$integrations[] = 'Smartcustomizer_Integration';
			return $integrations;
		}
	}


	Smartcustomizer::instance();
}
