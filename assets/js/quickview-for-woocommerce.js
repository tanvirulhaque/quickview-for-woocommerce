jQuery('document').ready(function (){
    jQuery(".quick-view-button").on("click", function(e) {
        e.preventDefault();

        var product_id = jQuery(this).attr("data-product_id");

        jQuery("#woo-quickview-modal").css('display', 'block');

        jQuery.ajax({
            type: "POST",
            url: quickviewAjax.ajaxurl,
            data: {
                action: "woo_get_quickview_data",
                product_id : product_id
            },

            success: function(response) {
                jQuery("#woo-quickview-modal").html(response);
                jQuery.featherlight(jQuery("#woo-quickview-modal"), {});
                jQuery("#woo-quickview-modal").css('display', 'none');
            },

            error: function() {
                alert('Not working')
            }

        });

    });

});