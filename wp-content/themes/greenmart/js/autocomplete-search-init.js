jQuery(document).ready(function ($){

    var acs_action = 'greenmart_autocomplete_search';

    $("input[name=s]").autocomplete({ 
        source: function(req, response){
            $.ajax({
                url: greenmart_ajax.ajaxurl+'?callback=?&action='+acs_action,
                dataType: "json",
                data: {
                    term : req.term,
                    category : $(".tbay-search-form .dropdown_product_cat").val(),
                    post_type : $(".tbay-search-form .post_type").val()
                },
                success: function(data,event, ui) {
                    response(data); 
                },
            });
        },
        minLength: 2,
        autoFocus: true,
        search: function(event, ui) {
            $(event.currentTarget).parents('.tbay-search-form').addClass('load');
        },
        select: function(event, ui) {
            window.location.href = ui.item.link;
        },
        create: function() {

            $(this).data('ui-autocomplete')._renderItem = function( ul, item ) {
                var string = '';
                if ( item.image != '' ) {
                    var string = '<a href="' + item.link + '" title="'+ item.label +'"><img class="pull-left" src="'+ item.image+'" style="margin-right:10px;"></a>';
                }
                string = string + '<div class="name"><a href="' + item.link + '" title="'+ item.label +'">'+ item.label +'</a></div>';
                if ( item.price != '' ) {
                    string = string + '<div class="price">'+ item.price +'</div> ';
                }
                return $( "<li>" ).append( string ).appendTo( ul );
            };

        }, 
        response: function(event, ui) {
            // ui.content is the array that's about to be sent to the response callback.
            if (ui.content.length === 0) {
                $(".tbay-preloader").text("No results found");
                $(".tbay-preloader").addClass('no-results');
            } else {
                $(".tbay-preloader").empty();
                $(".tbay-preloader").removeClass('no-results');
            }
        },
        open: function(event, ui) {
            $(event.target).parents('.tbay-search-form').removeClass('load');
            $(event.target).parents('.tbay-search-form').addClass('active');
        },
        close: function() {
        }
    });

    $('.tbay-preloader').on('click', function(){    
        $(this).parents('.tbay-search-form').removeClass('active');          
        $(this).parents('.tbay-search-form').find('input[name=s]').val('');                  
    });

});