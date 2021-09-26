<?php
namespace WooCommPlugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( 'WooCommPlugin\\WooCommPlugin_Tax_Handler' ) ) :

    class WooCommPlugin_Tax_Handler {
    
	    public $billing_location;
        

        function __construct()
        {
            
		    $this->set_billing_location();
            add_action('wp_ajax_get_states_by_ajax', array($this,'set_billing_location'));
            add_action('wp_ajax_nopriv_get_states_by_ajax',array($this, 'set_billing_location'));
            
		
            //modify tax csv whenever settings are saved
            global $current_section;
            add_action('woocommerce_update_options'.$current_section, array($this, 'modify_tax_csv'));

            //incl. GST
            add_filter('woocommerce_countries_inc_tax_or_vat',array($this,'inclGST'));

            //ex. GST
        	add_filter( 'woocommerce_countries_ex_tax_or_vat',array($this,'exGST') );

		
            //GST
            add_filter('woocommerce_countries_tax_or_vat',array($this,'GST'));

            //includes GST
            add_filter('woocommerce_cart_totals_order_total_html',array($this,'includesGST'));

        }
        
        //set billing location
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
        
        
        //modify tax csv whenever settings are saved
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

            
	    	$myFileRead = plugin_dir_path( __FILE__ ) ."../admin/tax_rates.csv";
	    	$myFileLink = fopen($myFileRead, 'r');
	    	$myFileContents = fread($myFileLink, filesize($myFileRead));
	    	fclose($myFileLink);

	    	$separator = $myFileContents[96];
	    	$data = array();
	    	$first_line = substr($myFileContents,0,96);
	    	$keys = explode(',',$first_line);
            

	    	$rest_lines = substr($myFileContents,97);
	    	$data_lines = explode($separator,$rest_lines);
            

	    	$total_data = array();
	    	foreach($data_lines as $individual_row)
	    	{
	    	    $array_line = explode(',',$individual_row);
	    	    array_push($total_data,array_combine($keys,$array_line));
	    	}
            

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
	    	            
	    	        }
	    	    }
	    	    else
	    	    {
	    	        $data_array['Rate %'] = $tax_rate;
	    	    }
	    	}

            

	    	$new_lines = array();
	    	foreach($total_data as $data_row)
	    	{
	    	    $str = implode(',',$data_row);
	    	    array_push($new_lines,$str);
	    	}
            

	    	$newFileString = implode($separator,$new_lines);
            
	    	$finalContent = $first_line.$separator.$newFileString;
            

	    	$myFileWrite = plugin_dir_path( __FILE__ ) ."../public/tax_rates_to_upload.csv";
	    	$myFileLink2 = fopen($myFileWrite, 'w+');

	    	fwrite($myFileLink2, $finalContent);
	    	fclose($myFileLink2);
	    }
        
        //incl. GST
        public function inclGST($arg)
        {
            $arg  =  __( '(incl. GST)', 'woocommerce' ) ;
            return $arg;
        }

        //ex. GST
        public function exGST($arg){
			$arg = __('(ex. GST)','woocommerce');
			return  $arg;
		}

        //GST
        public function GST($arg){
            $arg = __('GST', 'woocommerce');
            return $arg;
        }

        //imcludes GST
        public function includesGST($value){
			$brk1 = strpos($value,'(');
			$brk2 =  strpos($value,')');
			$begin = substr($value,0,$brk1);
			if(WC()->cart->display_prices_including_tax())
				$end = substr($value,$brk2+1,strlen($value)-$brk2);
			else
				$end = substr($value,$brk2,strlen($value)-$brk2);
			$value = $begin.$end."(includes GST)";
            
			return $value;
		}
    }

endif;

return new WooCommPlugin_Tax_Handler();

