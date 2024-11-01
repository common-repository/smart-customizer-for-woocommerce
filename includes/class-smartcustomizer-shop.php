<?php


defined('ABSPATH') || exit;

class Smartcustomizer_Shop
{


	public static $is_block_active = null;

	public function __construct()
	{
	}

	private static function is_block_active()
	{
		if (self::$is_block_active === null) {

			self::$is_block_active = false;
			$type = null;

			if (is_cart()) {
				$type = 'cart';
			} else if (is_checkout()) {
				$type = 'checkout';
			}
			if ($type) {
				self::$is_block_active = WC_Blocks_Utils::has_block_in_page(wc_get_page_id($type), 'woocommerce/' . $type);
			}
		}
		return self::$is_block_active;
	}


	public static function init()
	{
		add_action('woocommerce_blocks_loaded', array(__CLASS__, 'blocks_loaded'), 10, 3);
		add_filter('woocommerce_add_cart_item_data', array(__CLASS__, 'add_cart_item_data'), 10, 2);
		add_filter('woocommerce_get_item_data', array(__CLASS__, 'item_meta_display'), 20, 2);
		add_action('woocommerce_checkout_create_order_line_item', array(__CLASS__, 'checkout_create_order_line_item'), 10, 4);
		add_action('woocommerce_after_cart_item_name', array(__CLASS__, 'after_cart_item_name'), 21, 2);
		add_filter('woocommerce_cart_item_thumbnail', array(__CLASS__, 'cart_item_thumbnail'), 10, 3);
	}



	public static function blocks_loaded()
	{
		add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_scripts'), 20);
	}



	public static function item_meta_display($item_data, $cart_item)
	{
		if ((!is_cart() && !is_checkout()) || empty($cart_item['smartcustomizer_data'])) {
			return $item_data;
		}

		if (self::is_block_active()) {
			$item_data[] = [
				'key' => 'smartcustomizer-design-id',
				'value' => $cart_item['smartcustomizer_data']['design_id'],
				'image' => smartcustomizer_get_image($cart_item['smartcustomizer_data']['design_id'], null, true)
			];
		} else if (is_checkout()) {
			$item_data[] = [
				'key' => 'Design Preview',
				'value' => $cart_item['smartcustomizer_data']['design_id'],
				'display' => smartcustomizer_get_image($cart_item['smartcustomizer_data']['design_id'])
			];
		}

		return $item_data;
	}



	public static function cart_item_thumbnail($product_image, $cart_item, $cart_item_key)
	{

		if (empty($cart_item['smartcustomizer_data']['design_id'])) {
			return $product_image;
		}

		return smartcustomizer_get_image($cart_item['smartcustomizer_data']['design_id'], $product_image);
	}


	public static function after_cart_item_name($cart_item, $cart_item_key)
	{

		if (empty($cart_item['smartcustomizer_data']['design_id'])) {
			return;
		}

		echo '<a href="#" class="smartcustomizer-link-edit" data-params="hash.' . esc_html($cart_item['smartcustomizer_data']['design_id']) . ',sa.order_update">Edit Design</a>';
	}


	public static function add_cart_item_data($cart_item_data, $product_id)
	{


		$props = ['design_id'];

		foreach ($props as $prop) {
			if (!empty($_REQUEST[$prop])) {
				$cart_item_data['smartcustomizer_data'][$prop] = sanitize_text_field($_REQUEST[$prop]);
			}
		}

		return $cart_item_data;
	}


	public static function checkout_create_order_line_item($item, $cart_item_key, $values, $order)
	{

		if (!empty($values['smartcustomizer_data'])) {
			$item->update_meta_data('smartcustomizer_data', $values['smartcustomizer_data']);
		}
	}

	public static function enqueue_scripts()
	{

		if (get_post_type(get_the_ID()) !== 'product' && !is_cart() && !is_checkout()) {
			return;
		}

		wp_register_script('smartcustomizer', SMARTCUSTOMIZER_URL . 'assets/scripts/wc.smartcustomizer.js?shop=' . get_site_url(), [], SMARTCUSTOMIZER_VERSION);
		wp_enqueue_script('smartcustomizer');
		wp_localize_script('smartcustomizer', 'sc_config', ['site_url' => get_option('siteurl'), 'shop_url' => get_permalink(wc_get_page_id('shop'))]);
		wp_register_style('smartcustomizer', plugin_dir_url(__DIR__) . 'assets/css/smartcustomizer.css', [], SMARTCUSTOMIZER_VERSION);
		wp_enqueue_style('smartcustomizer');
	}
}

Smartcustomizer_Shop::init();
