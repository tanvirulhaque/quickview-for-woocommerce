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


// Add button on shop archive product
add_action( 'woocommerce_after_shop_loop_item', function () {
    global $product;

    $html = '';

    if ( $product ) {
        $product_id = $product->get_id();
        $html       = '<a href="#" class="button quick-view-button" id="quick-view-button" data-featherlight="#mylightbox" data-product_id="' . $product_id . '">Quick View</a>';
    }

    echo $html;
}, 11 );


// Ajax Call Function
function woo_get_quickview_data() {
    $product_id = $_POST['product_id'];
    $get_product_data = wc_get_product( $product_id );

    echo $get_product_data;

    $query = new WP_Query( array(
        'p'         => $product_id,
        'post_type' => array( 'product' )
    ) );

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();

//            require_once( plugin_dir_path( __FILE__ ) . 'templates/quickview-template.php' );

        }

    }

    wp_reset_postdata();
    wp_die();
}

add_action( 'wp_ajax_woo_get_quickview_data', 'woo_get_quickview_data' );
add_action( 'wp_ajax_nopriv_woo_get_quickview_data', 'woo_get_quickview_data' );


add_action( 'woo_quickview_product_summary', 'woocommerce_template_single_title', 5 );
add_action( 'woo_quickview_product_summary', 'woocommerce_template_single_rating', 10 );
add_action( 'woo_quickview_product_summary', 'woocommerce_template_single_price', 15 );
add_action( 'woo_quickview_product_summary', 'woocommerce_template_single_excerpt', 20 );
add_action( 'woo_quickview_product_summary', 'woocommerce_template_single_add_to_cart', 25 );
add_action( 'woo_quickview_product_summary', 'woocommerce_template_single_meta', 30 );


// Enqueue Scripts
add_action( 'wp_enqueue_scripts', function () {

    wp_enqueue_style( 'featherlight', '//cdn.jsdelivr.net/npm/featherlight@1.7.14/release/featherlight.min.css' );

    wp_register_script( 'quickview-for-woocommerce', plugin_dir_url( __file__ ) . 'assets/js/quickview-for-woocommerce.js', array( 'jquery' ) );
    wp_localize_script( 'quickview-for-woocommerce', 'quickviewAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

    wp_enqueue_script( 'quickview-for-woocommerce' );
    wp_enqueue_script( 'featherlight', '//cdn.jsdelivr.net/npm/featherlight@1.7.14/release/featherlight.min.js' );

} );

add_action('wp_head', function () {
    $html = '';

    $html .= '
        <div id="mylightbox" class="lightbox featherlight-inner">
        <h1>Hello</h1>
        <h1>Hello</h1>
        <h1>Hello</h1>
        </div>
    ';

    echo $html;
});