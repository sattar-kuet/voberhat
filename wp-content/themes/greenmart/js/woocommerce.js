(function($) {
	
    // add to cart modal
    var product_info = null;
    jQuery('body').bind('adding_to_cart', function( button, data , data2 ) {
       product_info = data2;
    });

    jQuery('body').bind('added_to_cart', function( fragments, cart_hash ){
        if ( product_info ) {
            jQuery('#tbay-cart-modal').modal();
            var url = greenmart_ajax.ajaxurl + '?action=greenmart_add_to_cart_product&product_id=' + product_info.product_id;
            jQuery.get(url,function(data,status) {
                jQuery('#tbay-cart-modal .modal-body .modal-body-content').html(data);
            });
            jQuery('#tbay-cart-modal').on('hidden.bs.modal',function() {
                jQuery(this).find('.modal-body .modal-body-content').empty();
            });
        }
    });

	// Ajax QuickView
	jQuery(document).ready(function(){
		
        /*Product video iframe*/
        $("#productvideo").tbayIframe();

		$(document).on( 'added_to_wishlist removed_from_wishlist', function(){
		   var counter = $('.count_wishlist');
			   $.ajax({
			   url: yith_wcwl_l10n.ajax_url,
			   data: {
			   action: 'yith_wcwl_update_wishlist_count'
			   },
			   dataType: 'json',
			   success: function( data ){
				counter.html( data.count );
			   },
			   beforeSend: function(){
			   counter.block();
			   },
			   complete: function(){
			   counter.unblock();
			   }
			   })
		  } );

		// Ajax delete product in the cart
        $(document).on('click', '.mini_cart_content a.remove', function (e)
        {
            e.preventDefault();

            var product_id = $(this).attr("data-product_id"),
                cart_item_key = $(this).attr("data-cart_item_key"),
                product_container = $(this).parents('.mini_cart_item');

            var thisItem = $(this).closest('.widget_shopping_cart_content'); 

            // Add loader
            product_container.block({
                message: null,
                overlayCSS: {
                    cursor: 'none'
                }
            });

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: wc_add_to_cart_params.ajax_url,
                data: {
                    action: "product_remove",
                    product_id: product_id,
                    cart_item_key: cart_item_key
                },
                 beforeSend: function() {
                    thisItem.find('.mini_cart_content').append('<div class="ajax-loader-wapper"><div class="ajax-loader"></div></div>');
                    thisItem.find('.mini_cart_content').fadeTo("slow", 0.3);
                    e.stopPropagation();
                },
                success: function(response) {
                    if ( ! response || response.error )
                        return;

                    var fragments = response.fragments;

                    // Replace fragments
                    if ( fragments ) {
                        $.each( fragments, function( key, value ) {
                            $( key ).replaceWith( value );
                        });
                    }

                    $('.add_to_cart_button.added').each(function( index ) {
                        if( $(this).data('product_id') == product_id ) {
                            $(this).removeClass('added');
                            $(this).next('.added_to_cart').removeClass('wc-forward');
                        }
                    });

                }
            });
        });
	
	});
    $(document).on('click', '.plus, .minus', function() {
        // Get values
        var $qty        = $(this).closest('.quantity').find('.qty'),
            currentVal  = parseFloat($qty.val()),
            max         = '',
            min         = 1,
            step        = 1;

        // Format values
        if(! currentVal || currentVal === '' || currentVal === 'NaN') currentVal = 0;
        if(max === '' || max === 'NaN') max = '';
        if(min === '' || min === 'NaN') min = 0;
        if(step === 'any' || step === '' || step === undefined || parseFloat(step) === 'NaN') step = 1;

        // Change the value
        if($(this).is('.plus')) {
            if(max &&(max == currentVal || currentVal > max)) {
                $qty.val(max);
            } else {
                $qty.val(currentVal + parseFloat(step));
            }

        } else {
            if(min &&(min == currentVal || currentVal < min)) {
                $qty.val(min).trigger('change');;
            } else if(currentVal > 0) {
                $qty.val(currentVal - parseFloat(step));
            }
        }

        // Trigger change event
        $qty.change();

    });	

    $(document).on('click', '.tbay-body-woocommerce-quantity-mod .plus, .tbay-body-woocommerce-quantity-mod .minus', function() {

        var add_to_cart_button = jQuery( this ).parents( ".groups-button" ).find( ".add_to_cart_button" );

        // For AJAX add-to-cart actions
        add_to_cart_button.attr( "data-quantity", jQuery( this ).parent( ".quantity" ).find( ".qty" ).attr("value") );

	});

	
	// thumb image
	$('.thumbnails-image .thumb-link').click(function(e){
		e.preventDefault();
		var image_url = $(this).attr('href');
		var image_full_url = $(this).data('image');
		$('.woocommerce-main-image .featured-image').attr('href', image_full_url);
		$('.woocommerce-main-image .featured-image img').attr('src', image_url);
		$('.cloud-zoom').CloudZoom();
	});
	
	$(window).load(function () {
		tbay_slick_slider();
	});

        /*Single product video iframe*/
    $.fn.tbayIframe = function( options ) {
        var self = this;
        var settings = $.extend({
            classBtn: '.tbay-modalButton',
            defaultW: 640,
            defaultH: 360
        }, options );
      
        $(settings.classBtn).on('click', function(e) {
          var allowFullscreen = $(this).attr('data-tbayVideoFullscreen') || false;
          
          var dataVideo = {
            'src': $(this).attr('data-tbaySrc'),
            'height': $(this).attr('data-tbayHeight') || settings.defaultH,
            'width': $(this).attr('data-tbayWidth') || settings.defaultW
          };
          
          if ( allowFullscreen ) dataVideo.allowfullscreen = "";
          
          // stampiamo i nostri dati nell'iframe
          $(self).find("iframe").attr(dataVideo);
        });
      
        // se si chiude la modale resettiamo i dati dell'iframe per impedire ad un video di continuare a riprodursi anche quando la modale è chiusa
        this.on('hidden.bs.modal', function(){
          $(this).find('iframe').html("").attr("src", "");
        });
      
        return this;
    };
	
	function tbay_slick_slider() {      

        $('.style-horizontal .flex-control-thumbs').each(function () {
            if ( $(this).children().length == 0 ) {
                return;
            }
            var _config = [];

			var number = $(this).parent('.woocommerce-product-gallery').data('columns');
			
            _config.slidesToShow  = number;
            _config.infinite      = false;
            _config.focusOnSelect = true;
            _config.settings 	  = "unslick";
            _config.prevArrow     = '<span class="owl-prev"></span>';
            _config.nextArrow     = '<span class="owl-next"></span>';
            _config.responsive    = [
                {
                    breakpoint: 1025,
                    settings: {
                        slidesToShow: 3,
                    }             
                }
            ];
            if ( $('body').hasClass('rtl') ) {
                _config.rtl = true;
            }

            $(this).slick(_config); 
        });
        $('.style-vertical .flex-control-thumbs').each(function () {

            if ( $(this).children().length == 0 ) {
                return;
            }
            var _config = [];

			
			
			var number = $(this).parent('.woocommerce-product-gallery').data('columns');
			
            _config.vertical      = true;
            _config.slidesToShow  = number;
            _config.infinite      = false;
            _config.infinite      = false;
            _config.focusOnSelect = true;
            _config.settings 	  = "unslick";
            _config.prevArrow     = '<span class="owl-prev"></span>';
            _config.nextArrow     = '<span class="owl-next"></span>';
            _config.responsive    = [
                {
                    breakpoint: 1025,
                    settings: {
                        vertical: false,
                        slidesToShow: 3
                    }
                }
            ];
            if ( $('body').hasClass('rtl') ) {
                _config.rtl = true;
            }

            $(this).slick(_config);
        });
    }
	
})(jQuery)