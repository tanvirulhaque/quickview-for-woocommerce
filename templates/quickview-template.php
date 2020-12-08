<?php
if ( ! defined( 'ABSPATH' ) ) {
    return;
}
?>

<div class="woo-quickview__inner-modal">
    <div class="woo-quickview__container woocommerce single-product">

        <div class="woo-quickview__main">
            <div id="product-<?php the_ID(); ?>" <?php post_class( 'product' ); ?>>

                <div class="woo-quickview__images">
                    <?php do_action( 'woo-quickview-product-images' ) ?>
                </div>

                <div class="woo-quickview__summary">
                    <?php do_action( 'woo-quickview-product-summary' ) ?>
                </div>

            </div>
        </div>

    </div>
</div>
