<?php

defined('ABSPATH') || exit;


class Smartcustomizer_REST_Access_Controller extends WC_REST_Controller
{

	protected $namespace = 'smartcustomizer/v1';

	protected $rest_base = 'access';

	public function register_routes()
	{

		register_rest_route($this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array($this, 'setup'),
				'permission_callback' => array($this, 'check_permission'),
				'args'                => array_merge(
					$this->get_endpoint_args_for_item_schema(WP_REST_Server::CREATABLE),
					array(
						'user_id' => array(
							'type'        => 'string',
							'description' => __('Smartcustomizer User ID', 'smartcustomizer'),
							'required'    => true
						),
						'api_key' => array(
							'type'        => 'string',
							'description' => __('Smartcustomizer API key', 'smartcustomizer'),
							'required'    => true
						),
					)
				),
			),
			'schema' => array($this, 'get_public_item_schema'),
		));
	}

	public function check_permission($request)
	{

		if (current_user_can('manage_options') || current_user_can('manage_woocommerce') /* || current_user_can( Smartcustomizer::CAPABILITY ) */) {
			return true;
		}

		return new WP_Error(
			'woocommerce_rest_cannot_create',
			__('Access error.', 'woocommerce'),
			array('status' => rest_authorization_required_code())
		);
	}


	public function setup($request)
	{
		$user_id  = $request['user_id'];
		$api_key = $request['api_key'];

		$integration = new Smartcustomizer_Integration();

		$integration->update_option('user_id', $user_id);
		$integration->update_option('api_key', $api_key);

		$request->set_param('context', 'edit');
		$response = $this->prepare_item_for_response($request, $request);
		$response = rest_ensure_response($response);

		$this->createWebhooks($request['api_key']);
		return $response;
	}

	private function createWebhooks($secret_key)
	{
		$webhooks = [
			'order.created' => 'Smartcustomizer Order Create',
			'order.updated' => 'Smartcustomizer Order Update'
		];

		foreach ($webhooks as $key => $name) {
			$webhook = new WC_Webhook();
			$webhook->set_user_id(1);
			$webhook->set_name($name);
			$webhook->set_topic($key);
			$webhook->set_secret($secret_key);
			$webhook->set_delivery_url(SMARTCUSTOMIZER_URL . 'wcapi,action.webhooks');
			$webhook->set_status('active');
			$webhook->save();
		}
	}


	public function prepare_item_for_response($item, $request)
	{

		$data = [
			'user_id' => $item['user_id'],
			'api_key' => $item['api_key']
		];

		$context = !empty($request['context']) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object($data, $request);
		$data    = $this->filter_response_by_context($data, $context);

		$response = rest_ensure_response($data);

		return $response;
	}
}
