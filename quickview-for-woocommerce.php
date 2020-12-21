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

if ( ! class_exists( 'Woo_Quickview_For_WooCommerce' ) ) {

    /**
     * Main Class
     * @since 1.0.0
     */
    class Woo_Quickview_For_WooCommerce {

        /**
         * Version
         *
         * @since 1.0.0
         * @var  string
         */
        public $version = '1.0.0';


        /**
         * The single instance of the class.
         */
        protected static $instance = null;


        /**
         * Constructor for the class
         *
         * Sets up all the appropriate hooks and actions
         *
         * @return void
         * @since 1.0.0
         */
        public function __construct() {
            // Define constants
            $this->define_constants();

            // Include required files
            $this->includes();

            // Initialize the action hooks
            $this->init_hooks();
        }


        /**
         * Initializes the class
         *
         * Checks for an existing instance
         * and if it doesn't find one, creates it.
         *
         * @return object Class instance
         * @since 1.0.0
         */
        public static function instance() {
            if ( null === self::$instance ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Define constants
         */
        private function define_constants() {
            define( 'WQV_VERSION', $this->version );
            define( 'WQV_FILE', __FILE__ );
            define( 'WQV_DIR_PATH', plugin_dir_path( WQV_FILE ) );
            define( 'WQV_DIR_URI', plugin_dir_url( WQV_FILE ) );
            define( 'WQV_ADMIN', WQV_DIR_PATH . 'admin' );
            define( 'WQV_INCLUDES', WQV_DIR_URI . 'includes' );
            define( 'WQV_ASSETS', WQV_DIR_URI . 'assets' );
        }


        /**
         * Include required files
         */
        private function includes() {
            if ( is_admin() ) {
                require_once plugin_dir_path( __FILE__ ) . '/includes/class-wqv-settings-api.php';
                require_once plugin_dir_path( __FILE__ ) . '/includes/class-wqv-settings.php';
            }
        }


        /**
         * Init Hooks
         *
         * @return void
         * @since 1.0.0
         */
        private function init_hooks() {
            add_action( 'init', array( $this, 'localization_setup' ) );
            add_action( 'admin_notices', array( $this, 'php_requirement_notice' ) );
            add_action( 'admin_notices', array( $this, 'wc_requirement_notice' ) );
            add_action( 'admin_notices', array( $this, 'wc_version_requirement_notice' ) );
            add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_settings_links' ) );

            $is_enable_quick_view = self::get_settings( 'enable_quick_view', 'on' );

            if ( 'on' != $is_enable_quick_view ) {
                return;
            }

            $btn_position = self::get_settings( 'quick_view_btn_position', 'after_add_to_cart' );
            $btn_priority = ( 'after_add_to_cart' == $btn_position ) ? 11 : '';

            add_action( 'woocommerce_after_shop_loop_item', array( $this, 'add_quickview_button' ), $btn_priority );
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
            add_action( 'wp_footer', array( $this, 'add_quickview_popup_markup' ) );
            add_action( 'wp_ajax_woo_get_quickview_data', array( $this, 'get_quickview_data' ) );
            add_action( 'wp_ajax_nopriv_woo_get_quickview_data', array( $this, 'get_quickview_data' ) );

            // Add quick view content
            add_action( 'woo_quickview_product_images', 'woocommerce_show_product_sale_flash', 10 );
            add_action( 'woo_quickview_product_images', 'woocommerce_show_product_images', 20 );
            add_action( 'woo_quickview_product_summary', 'woocommerce_template_single_title', 5 );
            add_action( 'woo_quickview_product_summary', 'woocommerce_template_single_rating', 10 );
            add_action( 'woo_quickview_product_summary', 'woocommerce_template_single_price', 15 );
            add_action( 'woo_quickview_product_summary', 'woocommerce_template_single_excerpt', 20 );
            add_action( 'woo_quickview_product_summary', 'woocommerce_template_single_add_to_cart', 25 );
            add_action( 'woo_quickview_product_summary', 'woocommerce_template_single_meta', 30 );
        }


        /**
         * Initialize plugin for localization
         *
         * @return void
         * @since 1.0.0
         *
         */
        public function localization_setup() {
            load_plugin_textdomain( 'quickview-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
        }

        /**
         * Plugin action links
         *
         * @param array $links
         *
         * @return array
         */
        public function plugin_settings_links( $links ) {

            $links[] = '<a href="' . admin_url( 'admin.php?page=' ) . 'woo-quick-view">' . __( 'Settings', 'quickview-for-woocommerce' ) . '</a>';

            return $links;

        }

        /**
         * Get Settings Function
         */
        public static function get_settings( $key, $default = false, $section = 'wqv_general' ) {

            $settings = get_option( $section, [] );

            return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;

        }

        /**
         * Enqueue Scripts
         */
        public function enqueue_scripts() {
            $is_enable_quick_view = self::get_settings( 'enable_quick_view', 'on' );

            if ( 'on' != $is_enable_quick_view ) {
                return;
            }

            wp_enqueue_style( 'featherlight', plugin_dir_url( __file__ ) . 'assets/css/featherlight.css', array(), '1.7.13' );
            wp_enqueue_style( 'quickview-for-woocommerce', plugin_dir_url( __file__ ) . 'assets/css/quickview-for-woocommerce.css', array(), '1.0.0' );

            wp_enqueue_script( 'wc-add-to-cart-variation' );
            wp_enqueue_script( 'featherlight', plugin_dir_url( __file__ ) . 'assets/js/featherlight.js', array( 'jquery' ), '1.7.13', true );
            wp_enqueue_script( 'quickview-for-woocommerce', plugin_dir_url( __file__ ) . 'assets/js/quickview-for-woocommerce.js', array(
                'jquery',
                'wc-add-to-cart-variation'
            ), true );
            wp_localize_script( 'quickview-for-woocommerce', 'quickviewAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
        }


        // Add quick view button on shop archive product
        public function add_quickview_button() {
            global $product;
            $btn_text = self::get_settings( 'quick_view_btn_text', 'Quick View' );

            $html = '';

            if ( $product ) {
                $product_id = $product->get_id();

                $html .= '<a href="#" class="button quick-view-button" id="quick-view-button" data-featherlight="#mylightbox-' . $product_id . '" data-product_id="' . $product_id . '">' . $btn_text . '</a>';
            }

            echo $html;
        }


        // Add quick view popup markup on footer
        public function add_quickview_popup_markup() {

            $html = '<div id="woo-quickview-modal"></div>';

            echo $html;
        }


        // Get quick view data by Ajax
        public function get_quickview_data() {
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


        /**
         * PHP Version
         *
         * @return bool|int
         */
        public function is_required_php_version() {
            return version_compare( PHP_VERSION, '5.6.0', '>=' );
        }


        /**
         * PHP Requirement Notice
         */
        public function php_requirement_notice() {
            if ( ! $this->is_required_php_version() ) {
                $class   = 'notice notice-error';
                $text    = esc_html__( 'Please check PHP version requirement.', 'quickview-for-woocommerce' );
                $link    = esc_url( 'https://docs.woocommerce.com/document/server-requirements/' );
                $message = wp_kses( __( "It's required to use latest version of PHP to use <strong>Quickview for WooCommerce</strong>.", 'quickview-for-woocommerce' ), array( 'strong' => array() ) );

                printf( '<div class="%1$s"><p>%2$s <a target="_blank" href="%3$s">%4$s</a></p></div>', $class, $message, $link, $text );
            }
        }


        /**
         * WooCommerce Requirement Notice
         */
        public function wc_requirement_notice() {
            if ( ! $this->is_wc_active() ) {
                $class = 'notice notice-error';
                $text  = esc_html__( 'WooCommerce', 'quickview-for-woocommerce' );

                $link = esc_url( add_query_arg( array(
                    'tab'       => 'plugin-information',
                    'plugin'    => 'woocommerce',
                    'TB_iframe' => 'true',
                    'width'     => '640',
                    'height'    => '500',
                ), admin_url( 'plugin-install.php' ) ) );

                $message = wp_kses( __( "<strong>Quickview for WooCommerce</strong> is an add-on of ", 'quickview-for-woocommerce' ), array( 'strong' => array() ) );

                printf( '<div class="%1$s"><p>%2$s <a class="thickbox open-plugin-details-modal" href="%3$s"><strong>%4$s</strong></a></p></div>', $class, $message, $link, $text );
            }
        }


        /**
         * WooCommerce Version
         */
        public function is_required_wc_version() {
            return version_compare( WC_VERSION, '3.2', '>' );
        }


        /**
         * WooCommerce Version Requirement Notice
         */
        public function wc_version_requirement_notice() {
            if ( $this->is_wc_active() && ! $this->is_required_wc_version() ) {
                $class   = 'notice notice-error';
                $message = sprintf( esc_html__( "Currently, you are using older version of WooCommerce. It's recommended to use latest version of WooCommerce to work with %s.", 'quickview-for-woocommerce' ), esc_html__( 'Quickview for WooCommerce', 'quickview-for-woocommerce' ) );
                printf( '<div class="%1$s"><p><strong>%2$s</strong></p></div>', $class, $message );
            }
        }


        /**
         * Check WooCommerce Activated
         */
        public function is_wc_active() {
            return class_exists( 'WooCommerce' );
        }
    }
}

/**
 * Initialize the plugin
 *
 * @return object
 */
function woo_quickview_for_woocommerce() {
    return Woo_Quickview_For_WooCommerce::instance();
}

// Kick Off
woo_quickview_for_woocommerce();



