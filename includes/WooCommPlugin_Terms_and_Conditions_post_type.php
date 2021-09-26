<?php
namespace WooCommPlugin;


if ( ! defined( 'ABSPATH' ) ) {
	die; // Exit if accessed directly
}

if ( !class_exists( 'Refund_Policy' ) ) :

class Refund_Policy
{
    public function __construct()
    {
        add_action( 'init', array($this,'refund_policy_post_type'), 0 );
    }
    
    public function refund_policy_post_type() {
        $labels = array(
         'name'                => _x( 'Terms & Conditions', 'Post Type General Name', 'acsweb' ),
         'singular_name'       => _x( 'Terms & Conditions', 'Post Type Singular Name', 'acsweb' ),
         'menu_name'           => __( 'Terms & Conditions', 'acsweb' ),
         'parent_item_colon'   => __( 'Parent refund_policy', 'acsweb' ),
         'all_items'           => __( 'All Terms & Conditions', 'acsweb' ),
         'view_item'           => __( 'View Terms & Conditions', 'acsweb' ),
         'add_new_item'        => __( 'Add New Terms & Conditions', 'acsweb' ),
         'add_new'             => __( 'Add New', 'acsweb' ),
         'edit_item'           => __( 'Edit Terms & Conditions', 'acsweb' ),
         'update_item'         => __( 'Update Terms & Conditions', 'acsweb' ),
         'search_items'        => __( 'Search Terms & Conditions', 'acsweb' ),
         'not_found'           => __( 'Not Found', 'acsweb' ),
         'not_found_in_trash'  => __( 'Not found in Trash', 'acsweb' ),
        );
        $args = array(
         'label'               => __( 'Terms & Conditions', 'acsweb' ),
         'description'         => __( 'Terms & Conditions news and reviews', 'acsweb' ),
         'labels'              => $labels,
         'has_archive'         => true,
         'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', ),
         'taxonomies'          => array( 'genres' ), 
         'hierarchical'        => false,
         'public'              => true,
         'show_ui'             => true,
         'show_in_menu'        => true,
         'show_in_nav_menus'   => true,
         'show_in_admin_bar'   => true,
        
         'menu_position'       => 5,
         'menu_icon'           => 'dashicons-format-image',
         'can_export'          => true,
         'exclude_from_search' => false,
         'publicly_queryable'  => true,
         'capability_type'     => 'page',
         'taxonomies'          => array( 'category' ),
        );
        register_post_type( 'refund_policy', $args );
    }
}

endif;

return new Refund_Policy();