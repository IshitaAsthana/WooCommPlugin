<?php

/**
 * Fired during plugin deactivation
 *
 * @since      1.0.0
 *
 * @package    WooCommPlugin
 * @subpackage WooCommPlugin/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    WooCommPlugin
 * @subpackage WooCommPlugin/includes
 * @author     Your Name <email@example.com>
 */
class WooCommPlugin_Deactivator 
{
	public static function deactivate() 
    {
		//flush rewrite rules.
		flush_rewrite_rules();
	}
}
