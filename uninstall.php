<?php

defined('ABSPATH') || exit;

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

if (!current_user_can('activate_plugins')) {
    return;
}

require_once plugin_dir_path(__FILE__) . 'includes/class-smartcustomizer-integration.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-smartcustomizer-webservice.php';
require_once plugin_dir_path(__FILE__) . 'includes/smartcustomizer-config.php';
require_once plugin_dir_path(__FILE__) . 'includes/smartcustomizer-functions.php';

$data_store = WC_Data_Store::load('webhook');

$webhooks_ids   = $data_store->search_webhooks(['search' => 'Smartcustomizer']);

foreach ($webhooks_ids as $webhook_id) {
    $webhook = new WC_Webhook();
    $webhook->set_id($webhook_id);
    $webhook->delete();
}

$webservice = new Smartcustomizer_Webservice();
$uninstall_url = smartcustomizer_get_app_url() . 'wcapi/,action.uninstall';
$webservice->request($uninstall_url, []);

delete_option('woocommerce_smartcustomizer_settings');

global $wpdb;
$delete = $wpdb->query('DELETE FROM ' . $wpdb->prefix . 'woocommerce_api_keys WHERE description LIKE \'Smartcustomizer%\'');
