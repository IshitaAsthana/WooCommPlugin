<?php

/**
 * Trigger this file on Plugin uninstall.
 * 
 * @package WooCommPlugin
 */

 if( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
 {
     die;
 }

 //Clear database stored data
 global $wpdb;

 $wpdb->query("DROP TABLE wp_gst_data");