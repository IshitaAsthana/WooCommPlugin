<?php
include('..\woocommerce\includes\class-wc-cart.php');
function add_to_cart() {
    defined( 'WC_ABSPATH' ) || exit;

    // Load cart functions which are loaded only on the front-end.
    include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
    include_once WC_ABSPATH . 'includes/class-wc-cart.php';

    if ( is_null( WC()->cart ) ) {
        wc_load_cart();
    }

    // I'm simply returning the cart item key. But you can return anything you want...
    // return WC()->cart;
}
// add_to_cart();
// global $woocommerce;
// $items = $woocommerce->cart->cart_contents_count;
// echo $items;
// $array = new WC_Cart();
// echo $array;
// $cart = WC()->cart;
// // $arr = $array->cart_contents;
// echo $cart->cart_contents;
// $a1 = apply_filters( 'woocommerce_get_cart_contents', (array) $this->cart_contents );
// foreach($a1 as $a)
// {
//     echo $a;
// }
// get_cart_contents();
    // foreach($items as $item => $values) { 
    //     $_product =  wc_get_product( $values['data']->get_id()); 
    //     echo "<b>".$_product->get_title().'</b>  <br> Quantity: '.$values['quantity'].'<br>'; 
    //     $price = get_post_meta($values['product_id'] , '_price', true);
    //     echo "  Price: ".$price."<br>";
    // } 
// global $woocommerce;
// $GLOBALS['woocommerce'];
    if(function_exists('WC'))
    {
        echo'hi';
        echo WC()->cart;
        // foreach ( WC()->cart->get_cart() as $item ) {
		// 	if ( $item['data'] ) {
		// 		echo $item['data'];
		// 	}
		// }
    }
    // echo = wc_get_product( '18' );
    if(class_exists('WC_Cart'))
    {
        echo 'yes<br>';
    }
    if ( function_exists( 'wc_get_order' ) ) {

        echo wc_get_order( '19' );
        echo 'exists';

    } else {

        echo new WC_Order( '19' );
    }
    echo '<br>';

    if( function_exists('WC'))
    {
        echo WC()->customer;

    }
    if( ! is_admin() ) { 
        echo WC()->cart->get_cart();
        echo 'hi';
    }
    wc()->frontend_includes();
    if(!is_null(WC()->cart)) {
        echo WC()->cart->get_cart_total();
        echo WC()->cart->get_cart_contents_count();
    } else {
        echo '‘0’';
        // $cart = new WC()->cart;
        // WC()->cart->add_to_cart( '18', 1 );
    }
    // echo WC()->cart->get_cart_contents();
?>