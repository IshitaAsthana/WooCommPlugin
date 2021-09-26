<?php

/**
 * Fired during plugin activation
 *
 * @since      1.0.0
 *
 * @package    WooCommPlugin
 * @subpackage WooCommPlugin/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    WooCommPlugin
 * @subpackage WooCommPlugin/includes
 * @author     Ishita Asthana
 */

class WooCommPlugin_Activator
{
    public static function activate() 
    {
        //flush rewrite rules.
        flush_rewrite_rules();
        global $wpdb;
 

		if($wpdb->get_var("SHOW TABLES LIKE 'wp_gst_data'") != 'wp_gst_data'):
        
            $table_name = $wpdb->prefix . "gst_data";
            
            $charset_collate = $wpdb->get_charset_collate();
            
            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
              HSNCode INT NOT NULL,
              CGSTRate DECIMAL(5,2) NOT NULL,
              SGSTRate DECIMAL(5,2) NOT NULL,
              IGSTRate DECIMAL(5,2) NOT NULL,
              PRIMARY KEY (HSNCode)
            ) 
            $charset_collate;";
        
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);          

		    $wpdb->query("LOAD DATA INFILE './../../htdocs/MyWebsite/wp-content/plugins/WooCommPlugin/public/HSN_codes.csv' 
		    INTO TABLE wp_gst_data 
		    FIELDS TERMINATED BY ','
		    ENCLOSED BY ''
		    LINES TERMINATED BY '\n'
		    IGNORE 1 ROWS;");

        endif;


        $myFile = plugin_dir_path( __FILE__ ) ."../public/tax_rates.csv";
        $myFileLink = fopen($myFile, 'r');
        $myFileContents = fread($myFileLink, filesize($myFile));
        fclose($myFileLink);
        
        if(!strpos($myFileContents,"CGST")&&!strpos($myFileContents,"SGST")):

        $store_location = wc_get_base_location();
        $state_pos = strpos($myFileContents,$store_location['state']);
        $len=0;
        for(;substr($myFileContents,$state_pos+$len,2) != "IN"; $len++);
        $igst_row = substr($myFileContents,$state_pos,$len);
        $I = strpos($igst_row,"IGST");
        
        $sgst_row = substr_replace($igst_row,"S", $I , 1);
        
        $cgst_row = substr_replace($igst_row,"C", $I , 1);
        $priority = strpos($cgst_row,"7");
        $cgst_row = substr_replace($cgst_row,"6", $priority , 1);
        $cgst_row = "IN,".$cgst_row;
        $new_row = $sgst_row.$cgst_row;
        $first = substr($myFileContents,0,$state_pos);
        $last = substr($myFileContents,$state_pos+$len);
        
        $newFileContent = $first.$new_row.$last;


        
        $myFileLink2 = fopen($myFile, 'w+');
        
        fwrite($myFileLink2, $newFileContent);
        fclose($myFileLink2);

        endif;
    }
}

?>