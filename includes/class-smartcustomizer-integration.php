<?php

defined('ABSPATH') || exit;

if (!class_exists('Smartcustomizer_Integration')) :

	class Smartcustomizer_Integration extends WC_Integration
	{

		public function __construct()
		{

			global $woocommerce;

			$this->id = 'smartcustomizer';
			$this->method_title = __('Smartcustomizer', 'smartcustomizer');
			$this->method_description = __('Smartcustomizer', 'smartcustomizer');

			$this->init_form_fields();
			$this->init_settings();

			$this->api_key = $this->get_option('api_key');
			$this->user_id = $this->get_option('user_id');

			add_action('woocommerce_update_options_integration_' .  $this->id, array($this, 'process_admin_options'));
		}

		public function init_form_fields()
		{
			$this->form_fields = array(
				'user_id' => array(
					'title'             => __('User ID', 'smartcustomizer'),
					'type'              => 'text',
					'description'       => __('User ID', 'smartcustomizer'),
					'desc_tip'          => true,
					'default'           => ''
				),
				'api_key' => array(
					'title'             => __('API Key', 'smartcustomizer'),
					'type'              => 'text',
					'description'       => __('API Key', 'smartcustomizer'),
					'desc_tip'          => true,
					'default'           => ''
				)

			);
		}


		public function validate_api_key_field($key, $value)
		{
			if (isset($value) && strlen($value) !== 32) {
				WC_Admin_Settings::add_error(esc_html__('Wrong API KEY length', 'smartcustomizer'));
				return false;
			}
			return $value;
		}
	}

endif;
