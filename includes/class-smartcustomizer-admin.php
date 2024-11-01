<?php

defined( 'ABSPATH' ) || exit;

class Smartcustomizer_Admin {


public function __construct() {

}
			

	public static function init() {
        add_action('admin_menu', array(__CLASS__, 'smartcustomizer_add_menu_page'));
		add_action( 'woocommerce_before_order_itemmeta', array( __CLASS__, 'before_order_itemmeta' ), 10, 3 );
		add_filter( 'woocommerce_admin_order_item_thumbnail', array( __CLASS__, 'admin_order_item_thumbnail' ), 10, 3 );
	}
	
	
	
	public static function before_order_itemmeta($item_id, $item, $order) {

		$data = $item->get_meta( 'smartcustomizer_data' );
		if (!$data) {
			return;
		}
		
		$link = smartcustomizer_get_link($data['design_id']);
		printf('<a style="display:block; float:none;" href="%s" target="_blank">Download design files</a>', esc_url($link));
	}
	


	public static function admin_order_item_thumbnail($image, $item_id, $item) {
		
		$data = $item->get_meta('smartcustomizer_data');
		
		if (empty($data['design_id'])){
			return $image;
		}
	
		return smartcustomizer_get_image($data['design_id'], $image);
	}

    public static function smartcustomizer_add_menu_page()
    {
          add_menu_page(
            'Smartcustomizer',
            'Smartcustomizer',
            'manage_options',
            'smartcustomizer-admin',
            array(__CLASS__, 'get_page_content'),				'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyBpZD0iTGF5ZXJfMSIgZGF0YS1uYW1lPSJMYXllciAxIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgdmlld0JveD0iMCAwIDIwIDIwIj4KICA8cGF0aCBkPSJtMTksMi43M2MwLC45NS0uNzgsMS43My0xLjczLDEuNzNzLTEuNzMtLjc4LTEuNzMtMS43My43OC0xLjczLDEuNzMtMS43MywxLjczLjc4LDEuNzMsMS43M1ptLTMuNzYsNC4xNmMtLjcxLjI4LTEuMDcsMS4wOC0uNzgsMS43OS4yNi42Ny4zOSwxLjM0LjM5LDIuMDEsMCwzLjA1LTIuNDgsNS41NC01LjU0LDUuNTRzLTUuNTQtMi40OC01LjU0LTUuNTQsMi40OC01LjU0LDUuNTQtNS41NGMuNjcsMCwxLjM1LjEzLDIuMDEuMzkuNzEuMjgsMS41Mi0uMDcsMS43OS0uNzguMjgtLjcxLS4wNy0xLjUyLS43OC0xLjc5LS45OS0uMzktMi4wMS0uNTktMy4wMi0uNTlDNC43MywyLjM4LDEsNi4xMSwxLDEwLjY5czMuNzMsOC4zMSw4LjMxLDguMzEsOC4zMS0zLjczLDguMzEtOC4zMWMwLTEuMDItLjItMi4wMy0uNTktMy4wMi0uMjgtLjcxLTEuMDgtMS4wNi0xLjc5LS43OFoiIGZpbGw9IiMwMDAiLz4KPC9zdmc+',
        );
    }
	
	
	
    public static function get_page_content(){
		
        $integration = new Smartcustomizer_Integration();
		$isConnected = $integration->get_option('api_key');
	
		wp_register_style('smartcustomizer-admin', plugin_dir_url(__DIR__) . 'assets/css/smartcustomizer-admin.css', [], SMARTCUSTOMIZER_VERSION);
		wp_enqueue_style('smartcustomizer-admin');
			
        ?>

		<div class="smartcustomizer-admin-page">	
		<div class="logo"></div>
			<?php if ($isConnected){
				echo '<h1>Configure your Smart Customizer account</h1>
			<div class="description">Go to your Smart Customizer admin to configure your products and all design editing possibilities.</div>';
			$scLinkUrl = SMARTCUSTOMIZER_URL . 'admin/';
			$scLinkText = 'Go to admin';
			
			}
			else{
				echo '<h1>Connect your store to Smart Customizer</h1>
			<div class="description">You are nearly finished connecting your WooCommerce store to Smart Customizer. Just a few more steps to complete!</div>';
			$scLinkUrl = SMARTCUSTOMIZER_URL . 'wcapi/,action.auth?shop=' . get_option('siteurl');
			$scLinkText = 'Connect';
			}
			
			printf('<a href="%s" target="_blank">%s</a>', esc_url($scLinkUrl), esc_html($scLinkText));
			
			?>
			
			</div>
			<hr class="smartcustomizer-admin-page-sep">
        <?php  

        }

}

Smartcustomizer_Admin::init();