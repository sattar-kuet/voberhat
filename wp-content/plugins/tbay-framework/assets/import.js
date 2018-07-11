jQuery(document).ready(function($){
    var source = '';
    
    if ( $('.tbay-btn-import').data('disabled') ) {
        $(this).attr('disabled', 'disabled');
    }
    
    $('.tbay-btn-import').click(function(){
        // all
        source = $('.tbay-demo-import-wrapper .source-data').val();
        if ( confirm('Do you want to import demo now ?') ) {
            
            $(this).attr('disabled', 'disabled');
            $('.tbay-progress-content').show();
            
            $('.first_settings span').hide();
            $('.first_settings .installing').show();
            $('.steps li').removeClass('active');
            $('.first_settings').addClass('active');

            tbay_import_type('first_settings');
        }
    });

    function tbay_import_type( type ) {
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'tbay_import_sample',
                demo_source: source,
                ajax: 1,
                import_type: type
            },
            dataType: 'json',
            success: function (res) {
                var next = res.next;

                if ( res.status == false ) {
                    tbay_import_error( res );
                    return false;
                }

                if ( next == 'done' ) {
                    tbay_import_complete( type );
                    return false;
                }
                
                if ( next == 'error' ) {
                    tbay_import_error( res );
                    return false;
                }

                tbay_import_complete_step( type, res );
                tbay_import_type( next );
            },
            error: function (html) {
                $('.tbay_progress_error_message .tbay-error .content').append('<p>' + html + '</p>');
                $('.tbay_progress_error_message').show();
                return false;
            }
        });

        return false;
    }

    function tbay_import_complete_step(type, res) {
        $( '.' + type + ' span' ).hide();
        $( '.' + type + ' .installed' ).show();

        var next = res.next;
        if ( next == 'done' ) {
            $('.tbay-complete').show();
            $('.steps li').removeClass('active');
        } else {
            $('.' + next + ' span').hide();
            $('.' + next + ' .installing').show();
            $('.steps li').removeClass('active');
            $('.' + next).addClass('active');
        }
    }

    function tbay_import_complete(type) {
        $( '.' + type + ' span' ).hide();
        $( '.' + type + ' .installed' ).show();
        $('.tbay-complete').show();
    }

    function tbay_import_error(res) {
        if ( res.msg !== undefined && res.msg != '' ) {
            $('.tbay_progress_error_message .tbay-error .content').append('<p>' + res.msg + '</p>');
            $('.tbay_progress_error_message').show();
        }
    }

});


