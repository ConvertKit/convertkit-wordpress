jQuery(document).ready(function($) {

    var user = $.cookie( 'ck_visit' );

    if ( user ) {
        console.log( 'found cookie: ' + user );
    } else {
        user = 0;
        console.log('no cookie');
        console.log('ajax call: ' + ck_data.ajaxurl);
    }

    $.ajax({
        type: "POST",
        data: {
            action: 'ck_add_user_visit',
            user: user,
            url: document.URL
        },
        url: ck_data.ajaxurl,
        success: function (response) {
            console.log( 'setting cookie' );
            $.cookie( 'ck_visit', response, { expires: 365, path: '/' } );
        }

    }).fail(function (response) {
        if ( window.console && window.console.log ) {
            console.log( "ERROR" + response );
        }
    });

});
