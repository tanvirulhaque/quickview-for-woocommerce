<?php
if ( ! defined( 'ABSPATH' ) ) {
    return;
}
?>

<div class="woo-quickview__container">
    <div class="woo-quickview__main woocommerce single-product">
        <div id="product-<?php the_ID(); ?>" <?php post_class( 'product' ); ?>>

            <div class="woo-quickview__images">
                <?php do_action( 'woo_quickview_product_images' ) ?>
            </div>

            <div class="woo-quickview__summary">
                <?php do_action( 'woo_quickview_product_summary' ) ?>
            </div>

        </div>
    </div>
</div>
