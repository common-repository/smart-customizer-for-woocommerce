<?php


defined('ABSPATH') || exit;

class Smartcustomizer_Webservice
{

    private $integration;

    public function __construct()
    {
        $this->integration = new Smartcustomizer_Integration();
    }

    public function request($url, $data = [])
    {
		
		$data = wp_json_encode($data);
			
		$args = array(
			'headers' => array(
				'X-SC-Shop' => get_option('siteurl'),
				'X-SC-Token' => $this->integration->api_key,
				'Accept' => 'application/json',
				'Content-Type' => 'application/json',
			),
			'body' => $data,
			'sslverify' => (SMARTCUSTOMIZER_MODE !== 'dev'), // Enable SSL verification in production
			'data_format' => 'body'
		);

		$response = wp_remote_post($url, $args);

		if (!is_wp_error($response)) {
			$result = wp_remote_retrieve_body($response);
		} else {
			$result = '';
		}
		
		return $result;	
		
    }
}
