<?php
namespace WooCommPlugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( 'WooCommPlugin\\WooCommPlugin_Refund_Policy_submenu' ) ) :

    class WooCommPlugin_Refund_Policy_submenu {
    
        protected $option_name = 'woocommplugin_store_policies_Refund_Policy';
    
        function __construct()	{
            
            // add_action( 'admin_init', array( $this, 'init_settings' ) );
            
            add_action( 'woocommplugin_store_policies_page_Refund_Policy', array( $this, 'Refund_Policy' ), 10, 1 );
        }
    
        public function Refund_Policy()
        {
            include('views\Refund_Policy.php');
            submit_button();
        }
    }

endif;

return new WooCommPlugin_Refund_Policy_submenu();