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
		
		//Product hsn code
		add_action('woocommerce_product_options_general_product_data', array( $this , 'add_product_custom_meta_box_hsn_code') );
		add_action( 'woocommerce_process_product_meta', array($this,'save_hsn_code_field' ));
		// add_filter( 'woocommerce_tax_settings', array($this, 'tax_menus_allowed') );
		
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
		$myFile = plugin_dir_path( __FILE__ ) ."../public/tax_rates.csv";
		$myFileLink = fopen($myFile, 'r');
		$myFileContents = fread($myFileLink, filesize($myFile));
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

		$myFileLink2 = fopen($myFile, 'w+');

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