<?php
namespace WooCommPlugin;


if ( ! defined( 'ABSPATH' ) ) {
	die; // Exit if accessed directly
}

if ( !class_exists( 'Tax_Modifier' ) ) :

class Tax_Modifier 
{
    public $store_location;
    public $shipping_location;
    public $billing_location;
    public $billing_location_set;
    public $cart_items_list;
    public $calculated_tax;
    public $tax_slab;
    public $new_total;

	public function instance() 
	{
		echo('instance');
		if ( is_null( self::$_instance ) ) 
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

    public function __construct()
    {
        $this->billing_location_set = 0;
        $this->calculated_tax = array("SGST"=>0.0,"CGST"=>0.0,"IGST"=>0.0);
        $this->cart_items_list = array();
        
        //address of store
        $this->store_details();
        // remove taxes before checkout
        // add_action( 'woocommerce_calculate_totals', array($this, 'action_cart_calculate_totals'), 10, 1 );
        //set total same as subtotal before checkout
		// add_filter( 'woocommerce_calculated_total', array($this,'change_calculated_total'), 10, 2 );
  //       //add checkbox to use GST settings
		// add_filter( 'woocommerce_tax_settings', array($this, 'tax_setting_for_gst') );

        //modify checkout total
        // add_filter( 'woocommerce_cart_totals_order_total_html', array($this,'order_total'));

        
        //ajax scripts for gst state

        add_action( 'wp_enqueue_scripts', array($this,'blog_scripts') ); 
        
        add_action('wp_ajax_get_states_by_ajax', array($this,'set_billing_location'));
        add_action('wp_ajax_nopriv_get_states_by_ajax',array($this, 'set_billing_location'));
        // add_filter( 'woocommerce_cart_item_product', array($this, 'cart_contents'),10,3 );

        add_filter( 'woocommerce_cart_totals_get_item_tax_rates', array($this,'change_tax_rates'),10,3 );
        
        // add_action( 'woocommerce_checkout_create_order', array($this,'change_total_on_checking'), 20, 2 );

        
    }
    public function change_total_on_checking( $order,$data ) 
    {
        // print_r($data);
        // // Get order total
        // $total = $order->get_total();
    
        // ## -- Make your checking and calculations -- ##
        // $new_total = $total * 1.12; // <== Fake calculation
    
        // // Set the new calculated total
        // $order->set_total( $new_total );
        return $order;
    }

    public function set_billing_location()
    {
        $this->billing_location_set = 1;
        //grab the selected state
        if(isset($_POST["state"]))
        {
            $this->billing_location = $_POST['state'];
        }
        else
        {
            $this->billing_location = $this->store_location['state'];
        }

    }

    public function change_tax_rates($item_tax_rates, $item, $cart)
    {
        //url check to avoid cart total change
        $url = $_SERVER['REQUEST_URI'];
        
        $uri_list = array( explode('/',$url));
        
        global $wpdb;
        $hsn = $item->product->get_meta('hsn_prod_id');
        $rate = $wpdb->get_results("SELECT IGSTRate FROM wp_gst_data WHERE HSNCode = $hsn");
        
        foreach($rate as $rates)
        {
            if($uri_list[0][count($uri_list[0])-2]=="cart")
            {
                $keys = array_keys($item_tax_rates);
                foreach($keys as $key)
                {
                    $item_tax_rates[$key]['rate'] = 0;
                }  
            }
            else
            {
                
                $this->set_billing_location();
                $keys = array_keys($item_tax_rates);
                foreach($keys as $key)
                {
                    if($this->billing_location == $this->store_location['state'])
                    {
                        if($item_tax_rates[$key]['label'] != "IGST")
                        {
                            $item_tax_rates[$key]['rate'] = $rates->IGSTRate/2;
                        }
                        else
                        {
                            $item_tax_rates[$key]['rate'] = $rates->IGSTRate;
                        }
                    }
                    else
                    {
                        $item_tax_rates[$key]['rate'] = $rates->IGSTRate;
                    }
                }
                
            }  
        }

        return $item_tax_rates;
    }


    //ajax for gst state
    public function blog_scripts() {
        // Register the script
        wp_register_script( 'custom-script', '/wp-content/plugins/WooCommPlugin/includes/WooCommPlugin_tax.js', array('jquery'), false, true );
      
        // Localize the script with new data
        $script_data_array = array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'security' => wp_create_nonce( 'load_states' ),
        );
        wp_localize_script( 'custom-script', 'blog', $script_data_array );
      
        // Enqueued script with localized data.
        wp_enqueue_script( 'custom-script' );


    }

 //    //add checkbox to use GST settings
 //    public function tax_setting_for_gst($settings)
	// {
 //        $res = array_slice($settings, 0, count($settings)-1, true);
 //        array_push($res,array(
	// 		'title'   => __( 'Use default GST', 'woocommerce' ),
	// 		'desc'    => __( 'Use in-built GST data for tax calculation. You won\'t need to setup woocommerce tax if you tick this box.', 'woocommerce' ),
	// 		'id'      => 'woocommplugin_use_default_gst',
	// 		'default' => 'yes',
	// 		'type'    => 'number',
 //        ));
 //        array_push($res,array(
 //            'type' => 'sectionend',
 //            'id'   => 'tax_options',
 //        ));
        
	// 	return $res;
	// }

    //store details
    public function store_details()
    {
        $this->store_location =  wc_get_base_location();
    }


    //cart contents
    public function cart_contents($product, $cart_item, $cart_item_key)
    {
        
        $hsn = $product->get_meta('hsn_prod_id');
        $qty = $cart_item['quantity'];
        $subtotal = $qty*$product->get_price();

        global $wpdb;
        $rate = $wpdb->get_results("SELECT IGSTRate FROM wp_gst_data WHERE HSNCode = $hsn");

        foreach($rate as $rates)
        {
            if(!array_search($hsn,$this->cart_items_list,true))
            {
                array_push($this->cart_items_list,array("hsn"=>$hsn,"quantity"=>$qty,"subtotal"=>$subtotal, "tax_rate" => $rates->IGSTRate, "product_tax" => $rates->IGSTRate*$subtotal*0.01));
            }
        }
        
        return $product;
    }
}

endif;

return new Tax_Modifier();