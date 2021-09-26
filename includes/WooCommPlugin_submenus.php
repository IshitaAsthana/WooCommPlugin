<?php
namespace WooCommPlugin;


if ( ! defined( 'ABSPATH' ) ) {
	die; // Exit if accessed directly
}

if ( !class_exists( 'Submenus' ) ) :

class Submenus 
{
    public $options_page_hook;
	
	
	protected static $_instance = null;
	public $TnC;
	public $Refund;
	public $Refund1;
	public $cartObject;
	public $billing_location;


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
		
		// include settings classes
		// $this->TnC = include( 'WooCommPlugin_TnC_submenu.php' );
		// $this->Refund = include( 'WooCommPlugin_Refund_Policy_submenu.php');
		$this->Refund1 = include( 'WooCommPlugin_Terms_and_Conditions_post_type.php');

		global $current_section;
		// Invoice menu item
		// add_action( 'load_menus', array( $this, 'invoice_settings' ), 999 ); // Add menu\
		//Tax menu
		add_action( 'load_menus', array( $this, 'woocommplugin_tax_menu' ), 999 ); // Add menu\
		//add checkbox to use GST settings
		add_filter( 'woocommerce_tax_settings', array($this, 'tax_setting_for_gst') );

		// add_action('woocommerce_settings_save_tax',  array($this, 'modify_tax_csv'));
		add_action('woocommerce_update_options'.$current_section, array($this, 'modify_tax_csv'));
		
		add_filter('woocommerce_countries_inc_tax_or_vat',function($arg){
			$arg  =  __( '(incl. GST)', 'woocommerce' ) ;
			return $arg;
		});

		add_filter( 'woocommerce_countries_ex_tax_or_vat', function($arg){
			$arg = __('(ex. GST)','woocommerce');
			return  $arg;
		} );
		
		add_filter('woocommerce_countries_tax_or_vat',function($arg){
			$arg = __('GST', 'woocommerce');
			return $arg;
		});

		$this->set_billing_location();
		add_filter('woocommerce_cart_totals_order_total_html',function($value){
			$brk1 = strpos($value,'(');
			$brk2 =  strpos($value,')');
			$begin = substr($value,0,$brk1);
			$end = substr($value,$brk2,strlen($value)-$brk2);
			$value = $begin.$end."(includes GST)";


			// print_r( WC()->cart->get_tax_totals());
			return $value;
		});

		add_filter('woocommerce_cart_tax_totals',function($tax_totals){
			$keys = array_keys($tax_totals);
			foreach($tax_totals as $tax_object)
			{
				$tax_object->amount = 0.00;
			}
			return $tax_totals;
		});
		//Product hsn code
		// add_action('woocommerce_product_options_general_product_data', array( $this , 'add_product_custom_meta_box_hsn_code') );
		// add_action( 'woocommerce_process_product_meta', array($this,'save_hsn_code_field' ));
		// add_filter( 'woocommerce_tax_settings', array($this, 'tax_menus_allowed') );
		
        add_action('wp_ajax_get_states_by_ajax', array($this,'set_billing_location'));
        add_action('wp_ajax_nopriv_get_states_by_ajax',array($this, 'set_billing_location'));
        // add_filter( 'woocommerce_cart_totals_get_item_tax_rates', array($this,'change_tax_rates'),10,3 );
		add_action( 'wp', function() {

			if ( class_exists( 'woocommerce') ) {
				if ( is_cart()) {
					// // add_filter( 'wc_tax_enabled', '__return_false' );
					// if(wc_prices_include_tax ())
					// // 	add_filter('woocommerce_cart_display_prices_including_tax','__return_true');
					// 	add_filter( 'wc_tax_enabled', '__return_false' );
					// else
					// add_filter( 'wc_tax_enabled', '__return_true' );
					// 	add_filter('woocommerce_cart_display_prices_including_tax','__return_false');

				}
			}
		
		});
    }
	
	public function set_billing_location()
    {
		$store_location = wc_get_base_location();
        $this->billing_location_set = 1;
        //grab the selected state
        if(isset($_POST["state"]))
        {
            $this->billing_location = $_POST['state'];
        }
        else
        {
            $this->billing_location = $store_location['state'];
        }

    }

	public function change_tax_rates($item_tax_rates, $item, $cart)
    {
        //url check to avoid cart total change
        $url = $_SERVER['REQUEST_URI'];
        
        $uri_list = array( explode('/',$url));
        
        global $wpdb;
		$hsn = get_option("woocommplugin_hsn_code");
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
                // $store_location = wc_get_base_location();
                // $this->set_billing_location();
                // $keys = array_keys($item_tax_rates);
                // foreach($keys as $key)
                // {
                //     if($this->billing_location == $store_location['state'])
                //     {
                //         if($item_tax_rates[$key]['label'] != "IGST")
                //         {
                //             $item_tax_rates[$key]['rate'] = $rates->IGSTRate/2;
                //         }
                //         else
                //         {
                //             $item_tax_rates[$key]['rate'] = $rates->IGSTRate;
                //         }
                //     }
                //     else
                //     {
                //         $item_tax_rates[$key]['rate'] = $rates->IGSTRate;
                //     }
                // }
                
            }  
        }

        return $item_tax_rates;
    }

	public function tax_menus_allowed($settings)
	{
		foreach($settings as $setting)
		{
			// if($setting['id'] === 'woocommerce_tax_display_shop')
			// 	unset( $setting );
			print_r($setting);
		}
		
		return $settings;
	}

	public function modify_tax_csv()
	{
		$hsn = get_option("woocommplugin_hsn_code");
		$store_location = wc_get_base_location();
		global $wpdb;
		$rate = $wpdb->get_results("SELECT IGSTRate FROM wp_gst_data WHERE HSNCode = $hsn");
		$tax_rate = 0.0;
		foreach($rate as $rates)
		{
		    $tax_rate = $rates->IGSTRate;
		}

		// echo $hsn."<br>".$tax_rate."<br>";
		// $myFile = plugin_dir_path( __FILE__ ) ."tax_rates.csv";
		$myFileRead = plugin_dir_path( __FILE__ ) ."../admin/tax_rates.csv";
		$myFileLink = fopen($myFileRead, 'r');
		$myFileContents = fread($myFileLink, filesize($myFileRead));
		fclose($myFileLink);

		$separator = $myFileContents[96];
		$data = array();
		$first_line = substr($myFileContents,0,96);
		$keys = explode(',',$first_line);
		// print_r($keys);

		$rest_lines = substr($myFileContents,97);
		$data_lines = explode($separator,$rest_lines);
		// print_r($data_lines);

		$total_data = array();
		foreach($data_lines as $individual_row)
		{
		    $array_line = explode(',',$individual_row);
		    array_push($total_data,array_combine($keys,$array_line));
		}
		// print_r($total_data);

		foreach($total_data as &$data_array)
		{
		    if($data_array['State code']===$store_location['state'])
		    {
		        $data_array['Rate %'] = $tax_rate/2;
		        if($data_array['Tax name']==="IGST")
		        {
		            $data_array['Tax name'] = "SGST";
		            $new_row = array($data_array['Country code'],$data_array['State code'],$data_array['Postcode / ZIP'],$data_array['City'],$data_array['Rate %'],"CGST",$data_array['Priority']-1,$data_array['Compound'],$data_array['Shipping'],$data_array['Tax class']);
		            array_push($total_data,array_combine($keys,$new_row));
		            // Country code,State code,Postcode / ZIP,City,Rate %,Tax name,Priority,Compound,Shipping,Tax class
				
		        }
		    }
		    else
		    {
		        $data_array['Rate %'] = $tax_rate;
		    }
		}

		// print_r($total_data);

		$new_lines = array();
		foreach($total_data as $data_row)
		{
		    $str = implode(',',$data_row);
		    array_push($new_lines,$str);
		}
		// print_r($new_lines);

		$newFileString = implode($separator,$new_lines);
		// echo $newFileString;
		$finalContent = $first_line.$separator.$newFileString;
		// echo "<br>";
		// echo $finalContent;

		$myFileWrite = plugin_dir_path( __FILE__ ) ."../public/tax_rates_to_upload.csv";
		$myFileLink2 = fopen($myFileWrite, 'w+');

		fwrite($myFileLink2, $finalContent);
		fclose($myFileLink2);
	}

	// public function invoice_settings() 
 //    {
	// 	$parent_slug = 'woocommerce';

	// 	$this->options_page_hook = add_submenu_page(
	// 		$parent_slug,
	// 		'Invoice Settings',
	// 		'Invoice Settings',
	// 		'manage_woocommerce',
	// 		'woocommplugin_invoice_settings_submenu',
 //            array($this,'invoice_settings_callback')
	// 	);
	// }
    
 //    public function invoice_settings_callback() 
	// {
	// 	$settings_tabs = apply_filters( 'woocommplugin_invoice_settings_tabs', array (
	// 			'Invoice'	=> __('Invoice', 'woocommplugin' ),
	// 		)
	// 	);
		
	// 	$active_tab = isset( $_GET[ 'tab' ] ) ? sanitize_text_field( $_GET[ 'tab' ] ) : 'TnC';
	// 	$active_section = isset( $_GET[ 'section' ] ) ? sanitize_text_field( $_GET[ 'section' ] ) : '';

	// 	include('views/Store_Policies.php');
 //    }

    //add checkbox to use GST settings
    public function tax_setting_for_gst($settings)
	{
        $res = array_slice($settings, 0, count($settings)-1, true);
        array_push($res,array(
			'title'   => __( 'HSN code', 'woocommerce' ),
			'desc'    => __( 'HSN code for the category of products to be sold in the shop.', 'woocommerce' ),
			'id'      => 'woocommplugin_hsn_code',
			'default' => 'yes',
			'type'    => 'number',
        ));
        array_push($res,array(
            'type' => 'sectionend',
            'id'   => 'tax_options',
        ));
        
		return $res;
	}

	public function woocommplugin_tax_menu()
	{
		$parent_slug = 'edit.php?post_type=product';

		$this->options_page_hook = add_submenu_page(
			$parent_slug,
			'Tax',
			'Tax',
			'manage_woocommerce',
			'woocommplugin_tax_submenu',
            array($this,'woocommplugin_tax_callback')
		);
	}

	public function woocommplugin_tax_callback()
	{
		$settings_tabs = apply_filters( 'woocommplugin_tax_tabs', array (
				'Tax_Sample'	=> __('Tax Samples', 'woocommplugin' ),
			)
		);
		
		$active_tab1 = isset( $_GET[ 'tab' ] ) ? sanitize_text_field( $_GET[ 'tab' ] ) : 'TnC';
		$active_section1 = isset( $_GET[ 'section' ] ) ? sanitize_text_field( $_GET[ 'section' ] ) : '';

		include('views/check1.php');
		
	}
	
    public function add_product_custom_meta_box_hsn_code() {
        woocommerce_wp_text_input( 
            array( 
                'id'            => 'hsn_prod_id', 
                'label'         => __('HSN Code', 'woocommerce' ), 
                'description'   => __( 'HSN Code is mandatory for GST.', 'woocommerce' ),
                'custom_attributes' => array( 'required' => 'required' ),
                'value'         => get_post_meta( get_the_ID(), 'hsn_prod_id', true )
                )
            );
    }

	public function save_hsn_code_field( $post_id ) {
        $value = ( $_POST['hsn_prod_id'] )? sanitize_text_field( $_POST['hsn_prod_id'] ) : '' ;
        update_post_meta( $post_id, 'hsn_prod_id', $value );
    }
}


endif;  // calss_exists

return new Submenus();