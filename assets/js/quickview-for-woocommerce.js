jQuery(document).ready( function() {
   jQuery('#wc-quickview-button').on( 'click', function(e) {
      e.preventDefault(); 

      var product_id = jQuery(this).attr("data-product_id");

      jQuery.ajax({
         type: "post",
         dataType: "json",
         url: quickviewAjax.ajaxurl,
         data: {
            action: "wc_get_quickview_data", 
            product_id : product_id 
         },

         success: function(response) {
            if(response.type == "success") {
               // jQuery("#like_counter").html(response.like_count);
               alert("Working");
            }
            else {
               alert("Your like could not be added");
            }
         }

      });

   });
});