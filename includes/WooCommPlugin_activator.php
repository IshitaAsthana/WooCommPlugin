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
    }
}

?>