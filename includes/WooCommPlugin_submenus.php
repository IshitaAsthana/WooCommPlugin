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
		add_action('woocommerce_update_options_tax_'.$current_section, array($this, 'modify_tax_csv'));
		
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
		// echo $hsn;
		// print_r($hsn);
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