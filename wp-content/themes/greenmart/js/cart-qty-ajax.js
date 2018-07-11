jQuery( function( $ ) {

    $( document ).on( 'change', '.woocommerce-cart-form input.qty', function() {

        $("[name='update_cart']").trigger('click');

    });

});