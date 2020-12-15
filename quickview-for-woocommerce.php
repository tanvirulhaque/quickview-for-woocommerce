<?php
/*
 * Plugin Name: Quickview for WooCommerce
 * Plugin URI: https://wordpress.org/
 * Description: A simple quickview plugin for WooCommerce
 * Author: N/A
 * Version: 1.0.0
 * Author URI: 
 * Text Domain: quickview-for-woocommerce
 * Domain Path: /languages
 * WC requires at least: 3.2
 * WC tested up to: 4.7.1
 * License: GPLv2+
*/

// Don't call the file directly
defined( 'ABSPATH' ) or die( 'Keep Silent' );


// Add Quick View button on shop archive product
add_action( 'woocommerce_after_shop_loop_item', function () {
    global $product;

    $html = '';

    if ( $product ) {
        $product_id = $product->get_id();
        
        $html .= '<a href="#" class="button quick-view-button" id="quick-view-button" data-featherlight="#mylightbox-' . $product_id . '" data-product_id="' . $product_id . '">Quick View</a>';
    }

    echo $html;
}, 11 );


// Add popup markup on footer
add_action( 'wp_footer', function () {

    $html = '<div id="woo-quickview-modal"></div>';

    echo $html;
} );


// Ajax Call Function
function woo_get_quickview_data() {
    $product_id = (int) $_POST['product_id'];

    $query = new WP_Query( array(
        'post_type' => array( 'product', 'product_variation' ),
        'p'         => $product_id,
    ) );

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();

           require_once( plugin_dir_path( __FILE__ ) . 'templates/quickview-template.php' );

        }
    }

    wp_reset_postdata();
    die();
}

add_action( 'wp_ajax_woo_get_quickview_data', 'woo_get_quickview_data' );
add_action( 'wp_ajax_nopriv_woo_get_quickview_data', 'woo_get_quickview_data' );



function woo_qv_lightbox() {
    global $woocommerce;
    wp_enqueue_script( 'wc-add-to-cart-variation' ); //Variable product Script

    wp_enqueue_script( 'prettyPhoto', $woocommerce->plugin_url() . '/assets/js/prettyPhoto/jquery.prettyPhoto.min.js', array( 'jquery' ), '1.7', true );
    wp_enqueue_style( 'woocommerce_prettyPhoto_css', $woocommerce->plugin_url() . '/assets/css/prettyPhoto.css' );
}
add_action( 'wp_footer', 'woo_qv_lightbox' );

// Add quick view content
add_action( 'woo_quickview_product_images', 'woocommerce_show_product_sale_flash', 10 );
add_action( 'woo_quickview_product_images', 'woocommerce_show_product_images', 20 );
add_action( 'woo_quickview_product_summary', 'woocommerce_template_single_title', 5 );
add_action( 'woo_quickview_product_summary', 'woocommerce_template_single_rating', 10 );
add_action( 'woo_quickview_product_summary', 'woocommerce_template_single_price', 15 );
add_action( 'woo_quickview_product_summary', 'woocommerce_template_single_excerpt', 20 );
add_action( 'woo_quickview_product_summary', 'woocommerce_template_single_add_to_cart', 25 );
add_action( 'woo_quickview_product_summary', 'woocommerce_template_single_meta', 30 );


// Enqueue Scripts
add_action( 'wp_enqueue_scripts', function () {

    wp_enqueue_style( 'featherlight', plugin_dir_url( __file__ ) . 'assets/css/featherlight.css', array(), '1.7.13' );
    wp_enqueue_style( 'quickview-for-woocommerce', plugin_dir_url( __file__ ) . 'assets/css/quickview-for-woocommerce.css', array(), '1.0.0' );

    wp_enqueue_script( 'quickview-for-woocommerce', plugin_dir_url( __file__ ) . 'assets/js/quickview-for-woocommerce.js', array( 'jquery' ) );
    wp_localize_script( 'quickview-for-woocommerce', 'quickviewAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
    wp_enqueue_script( 'featherlight', plugin_dir_url( __file__ ) . 'assets/js/featherlight.js', array( 'jquery' ), '1.7.13' );

} );