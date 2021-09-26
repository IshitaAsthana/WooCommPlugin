<?php
/**
 * @package WooCommPlugin
 * @version 1.0.0
 */

/**
 * Plugin Name: WooCommPlugin
 * Plugin URI: http://wordpress.org/plugins/my_plugin/my_plugin/
 * Description: This is a test sample of the Plugin to be created in future supporting woocommerce websites.
 * Author: Ishita Asthana
 * Version: 1.0.0
 */

 /**
  * Lisence.
  */

if ( ! defined( 'ABSPATH') ) 
{
	die;	//Exit if accessed directly.
}

if ( !class_exists( 'WooCommPlugin') ) : 

class WooCommPlugin
{
	public $version = '2.9.3';
	public $plugin_basename;
	public $submenus;
	public $tax_modifier;

	public $gst_table;


	protected static $_instance = null;

	/**
	 * Main Plugin Instance
	 *
	 * Ensures only one instance of plugin is loaded or can be loaded.
	 */
	public static function instance() 
	{
		if ( is_null( self::$_instance ) ) 
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() 
	{
		$this->plugin_basename = plugin_basename(__FILE__);

		$this->define( 'WooCommPlugin_VERSION', $this->version );

		add_action( 'plugins_loaded', array( $this, 'load_classes' ) );
	}


	/**
	 * Define constant if not already set
	 * @param  string $name
	 * @param  string|bool $value
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	* Function for activation.
	*/
	function activation()
	{
		require_once plugin_dir_path( __FILE__ ) . 'includes/WooCommPlugin_activator.php';
		WooCommPlugin_Activator::activate();
		
	}

	/**
	* Function for deactivation.
	*/
	function deactivation()
	{
		require_once plugin_dir_path( __FILE__ ) . 'includes/WooCommPlugin_deactivator.php';
		WooCommPlugin_Deactivator::deactivate();		
	}

	/**
	 * Load the main plugin classes and functions
	 */
	public function includes() 
	{
		$this->submenus = require_once( plugin_dir_path( __FILE__ ) . '/includes/WooCommPlugin_submenus.php' );
		if (!wc_prices_include_tax ())
        {
			$this->tax_modifier = require_once( plugin_dir_path( __FILE__ ) . '/includes/WooCommPlugin_Tax_Modifier.php' );
		}

		add_action( 'admin_menu', array( $this, 'load_menus' ), 999 ); 
	}

	
	/**
	 * Instantiate classes when woocommerce is activated
	 */
	public function load_classes() {
		if ( $this->is_woocommerce_activated() === false ) {
			add_action( 'admin_notices', array ( $this, 'need_woocommerce' ) );
			return;
		}

		if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
			add_action( 'admin_notices', array ( $this, 'required_php_version' ) );
			return;
		}

		if (  version_compare( PHP_VERSION, '7.1', '<' ) ) {
			add_action( 'admin_notices', array ( $this, 'required_php_version' ) );
		}

		// all systems ready - GO!
		$this->includes();
	}

	/**
	 * Loads all the sub menus.
	 */
	public function load_menus() 
    {
		do_action( 'load_menus' );
		
	}
	
	/**
	 * Check if woocommerce is activated
	 */
	public function is_woocommerce_activated() {
		$blog_plugins = get_option( 'active_plugins', array() );
		$site_plugins = is_multisite() ? (array) maybe_unserialize( get_site_option('active_sitewide_plugins' ) ) : array();

		if ( in_array( 'woocommerce/woocommerce.php', $blog_plugins ) || isset( $site_plugins['woocommerce/woocommerce.php'] ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * WooCommerce not active notice.
	 *
	 * @return string Fallack notice.
	 */
	public function need_woocommerce() {
		/* translators: <a> tags */
		$error = sprintf( __( 'WooCommPlugin requires %1$sWooCommerce%2$s to be installed & activated!' , 'woocommplugin' ), '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>' );
		
		$message = '<div class="error"><p>' . $error . '</p></div>';
	
		echo $message;
	}

	
	/**
	 * PHP version requirement notice
	 */
	public function required_php_version() {
		$error_message	= __( 'WooCommPlugin requires PHP 7.1 (7.4 or higher recommended).', 'woocommplugin' );
		/* translators: <a> tags */
		$php_message	= __( 'We strongly recommend to %1$supdate your PHP version%2$s.', 'woocommplugin' );
		
		$message = '<div class="error">';
		$message .= sprintf( '<p>%s</p>', $error_message );
		$message .= sprintf( '<p>'.$php_message.'</p>', '<a href="https://docs.wpovernight.com/general/how-to-update-your-php-version/" target="_blank">', '</a>' );
		$message .= '</div>';

		echo $message;
	}
}

// activation
register_activation_hook( __FILE__ , array( WooCommPlugin::instance(),'activation' ) );

// deactivation
register_deactivation_hook( __FILE__ , array( WooCommPlugin::instance(),'deactivation' ) );

endif;