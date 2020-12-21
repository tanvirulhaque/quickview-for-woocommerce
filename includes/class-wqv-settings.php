<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'WQV_Settings' ) ):
    class WQV_Settings {

        private $settings_api;

        function __construct() {
            $this->settings_api = new WQV_Settings_API();

            add_action( 'admin_init', array( $this, 'admin_init' ) );
            add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        }

        function admin_init() {
            //set the settings
            $this->settings_api->set_sections( $this->get_settings_sections() );
            $this->settings_api->set_fields( $this->get_settings_fields() );

            //initialize settings
            $this->settings_api->admin_init();
        }

        function admin_menu() {
            add_menu_page( 'WooCommerce Quick View Settings', 'Woo Quick View Settings', 'edit_theme_options', 'woo-quick-view', array(
                $this,
                'plugin_page'
            ), 'dashicons-visibility', 32 );
        }

        function get_settings_sections() {
            $sections = array(
                array(
                    'id'    => 'wqv_general',
                    'title' => __( 'General', 'quickview-for-woocommerce' )
                ),
                array(
                    'id'    => 'wqv_advanced',
                    'title' => __( 'Advanced', 'quickview-for-woocommerce' )
                )
            );

            return $sections;
        }

        /**
         * Returns all the settings fields
         *
         * @return array settings fields
         */
        function get_settings_fields() {
            $settings_fields = array(
                'wqv_general' => array(
                    array(
                        'name'  => 'enable_quick_view',
                        'label' => __( 'Enable Quick View', 'quickview-for-woocommerce' ),
                        'desc'  => __( 'Show quick view button on archive / shop page.', 'quickview-for-woocommerce' ),
                        'type'  => 'checkbox',
                        'default' => 'on',
                    ),
                    array(
                        'name'              => 'quick_view_btn_text',
                        'label'             => __( 'Quick View Button Text', 'quickview-for-woocommerce' ),
                        'desc'              => __( 'Change quick view button text here.', 'quickview-for-woocommerce' ),
                        'placeholder'       => __( 'Quick View', 'quickview-for-woocommerce' ),
                        'type'              => 'text',
                        'default'           => 'Quick View',
                        'sanitize_callback' => 'sanitize_text_field'
                    ),
                    array(
                        'name'    => 'quick_view_btn_position',
                        'label'   => __( 'Quick View Button Position', 'quickview-for-woocommerce' ),
                        'desc'    => __( 'Select quick view button position.', 'quickview-for-woocommerce' ),
                        'type'    => 'select',
                        'default' => 'after_add_to_cart',
                        'options' => array(
                            'before_add_to_cart' => 'Before Add to Cart button',
                            'after_add_to_cart'  => 'After Add to Cart button'
                        )
                    ),
                ),

//                'wqv_advanced' => array()
            );

            return $settings_fields;
        }

        function plugin_page() {
            echo '<div class="wrap">';

            $this->settings_api->show_page_title();
            $this->settings_api->show_navigation();
            $this->settings_api->show_forms();

            echo '</div>';
        }

        /**
         * Get all the pages
         *
         * @return array page names with key value pairs
         */
        function get_pages() {
            $pages         = get_pages();
            $pages_options = array();
            if ( $pages ) {
                foreach ( $pages as $page ) {
                    $pages_options[ $page->ID ] = $page->post_title;
                }
            }

            return $pages_options;
        }

    }
endif;

new WQV_Settings();