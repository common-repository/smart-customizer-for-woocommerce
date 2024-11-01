<?php


defined('ABSPATH') || exit;

class Smartcustomizer_Email
{


	protected static $sent_to_admin = false;

	public function __construct()
	{


	}


	public static function init()
	{
		add_action('woocommerce_email_order_meta', array(__CLASS__, 'email_order_meta'), 10, 3);
		add_action('woocommerce_order_item_meta_start', array(__CLASS__, 'order_item_meta_start'), 10, 3);
	}


	public static function order_item_meta_start($item_id, $item, $order)
	{
		
		$data = $item->get_meta('smartcustomizer_data');
		if (empty($data['design_id']) || $order->status !== 'completed') {
			return;
		}
		
		$link_suffix = self::$sent_to_admin ? 'zip' : 'designzip';

		$link = smartcustomizer_get_link($data['design_id'], $link_suffix);
		printf('<a style="display:block; float:none;" href="%s" target="_blank">Download design files</a>', esc_url($link));
					
		
	}


	public static function email_order_meta($order, $sent_to_admin, $plain_text)
	{
		self::$sent_to_admin = $sent_to_admin;
	}
}

Smartcustomizer_Email::init();
