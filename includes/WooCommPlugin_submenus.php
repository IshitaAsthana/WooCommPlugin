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
		
		// include TnC post type
		$this->Refund1 = include( 'WooCommPlugin_Terms_and_Conditions_post_type.php');

		//add checkbox to use GST settings
		add_filter( 'woocommerce_tax_settings', array($this, 'tax_setting_for_gst') );

		////

		///for testing purposes

		// //Tax menu
		// add_action( 'load_menus', array( $this, 'woocommplugin_tax_menu' ), 999 ); // Add menu\


		////
    }
	

    //add number input to fill HSN code category for shop products
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

	// public function woocommplugin_tax_menu()
	// {
	// 	$parent_slug = 'edit.php?post_type=product';

	// 	$this->options_page_hook = add_submenu_page(
	// 		$parent_slug,
	// 		'Tax',
	// 		'Tax',
	// 		'manage_woocommerce',
	// 		'woocommplugin_tax_submenu',
    //         array($this,'woocommplugin_tax_callback')
	// 	);
	// }

	// public function woocommplugin_tax_callback()
	// {
	// 	$settings_tabs = apply_filters( 'woocommplugin_tax_tabs', array (
	// 			'Tax_Sample'	=> __('Tax Samples', 'woocommplugin' ),
	// 		)
	// 	);
		
	// 	$active_tab1 = isset( $_GET[ 'tab' ] ) ? sanitize_text_field( $_GET[ 'tab' ] ) : 'TnC';
	// 	$active_section1 = isset( $_GET[ 'section' ] ) ? sanitize_text_field( $_GET[ 'section' ] ) : '';

	// 	include('views/check1.php');
		
	// }
}


endif;  // calss_exists

return new Submenus();