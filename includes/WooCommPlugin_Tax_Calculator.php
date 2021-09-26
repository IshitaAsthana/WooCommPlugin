<?php
namespace WooCommPlugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( 'WooCommPlugin\\WooCommPlugin_Tax_Calculator' ) ) :

    class WooCommPlugin_Tax_Calculator {
    
        protected $hsn_code;

        function __construct()
        {
            $this->get_products();
        }

        public function get_products()
        {
            $args = array(
                'post_type'      => 'product',
            );
        
            $loop = new WP_Query( $args );
        
            while ( $loop->have_posts() ) : $loop->the_post();
                global $product;
                // echo '<br /><a href="'.get_permalink().'">' . woocommerce_get_product_thumbnail().' '.get_the_title().'</a>';
                // echo $product['title'];
                $this->hsn_code = $product->get_meta('hsn_prod_id');
                echo array($this, 'get_tax_slab');
            endwhile;
        
            wp_reset_query();
        }

        public function get_tax_slab($hsn_code)
        {
            // include('views/tax_calculator.js');
        }
        
    }

endif;

return new WooCommPlugin_Tax_Calculator();

