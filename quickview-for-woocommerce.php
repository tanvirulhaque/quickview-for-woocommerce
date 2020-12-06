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


// Add Quickview button
add_action( 'woocommerce_after_shop_loop_item', function() {
	global $product;

	$html = '
	<button class="button" id="wc-quickview-button" data-product_id="'.$product->get_id().'">Quickview</button>

	<script>
	jQuery(document).ready( function() {
	   jQuery("#wc-quickview-button").on( "click", function(e) {
	      e.preventDefault(); 

	      var product_id = jQuery(this).attr("data-product_id");

	      

	      jQuery.ajax({
	         type: "post",
	         dataType: "json",
	         url: "'.admin_url('admin-ajax.php').'",
	         data: {
	            action: "wc_get_quickview_data", 
	            product_id : product_id 
	         },

	         success: function(response) {
	            if(response.type == "success") {
	               // jQuery("#like_counter").html(response.like_count);
	               console.log(product_id);
	            }
	            else {
	               alert("Your like could not be added");
	            }
	         }

	      });

	   });
	});
	</script>
	';

	echo $html;
}, 11 );


// Ajax Function
function wc_quickview_ajax_handler() {
	$product_id = $_POST['product_id'];
	$p = get_product($product_id);
	echo "Hello";
	wp_die();
}
add_action( 'wp_ajax_wc_get_quickview_data', 'wc_quickview_ajax_handler' );
add_action( 'wp_ajax_nopriv_wc_get_quickview_data', 'wc_quickview_ajax_handler' );


// Enqueue Scripts
// add_action( 'wp_enqueue_scripts', function() {

// 	wp_register_script( 'quickview-for-woocommerce', plugin_dir_url(__file__).'assets/js/quickview-for-woocommerce.js', array('jquery') );
// 	wp_localize_script( 'quickview-for-woocommerce-ajax', 'quickviewAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

// 	wp_enqueue_script( 'quickview-for-woocommerce' );
// 	wp_enqueue_script( 'jquery' );

// });