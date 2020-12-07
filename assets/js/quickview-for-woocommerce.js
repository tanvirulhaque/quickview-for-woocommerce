jQuery('document').ready(function (){
    jQuery(".quick-view-button").on("click", function(e) {
        e.preventDefault();
        var product_id = jQuery(this).attr("data-product_id");

        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: quickviewAjax.ajaxurl,
            data: {
                action: "woo_get_quickview_data",
                product_id : product_id
            },

            success: function(response) {
                console.log(response);
                alert('Product Name: ' + response.name + ', Price: ' + response.price);
            },

            error: function(response) {
                alert('Not working')
            }

        });

    });
});