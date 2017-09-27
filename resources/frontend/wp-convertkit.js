jQuery(document).ready(function($) {

    // Manage visit cookie
    var user = $.cookie( 'ck_visit' );
    var subscriber_id = $.cookie( 'ck_subscriber_id' );

    if ( user ) {
        console.log( 'found user cookie: ' + user );
    } else {
        user = 0;
        console.log('no user cookie');
        console.log('ajax call: ' + ck_data.ajaxurl);
    }

    if ( subscriber_id ) {
        console.log( 'found subscriber_id cookie: ' + subscriber_id );
    } else {
        subscriber_id = ckGetQueryVariable('ck_subscriber_id');
        if ( subscriber_id ) {
            console.log( 'found subscriber_id url param: ' + subscriber_id );
        } else {
            subscriber_id = 0;
            console.log('no subscriber cookie or url param');
        }
    }

    $.ajax({
        type: "POST",
        data: {
            action: 'ck_add_user_visit',
            user: user,
            subscriber_id: subscriber_id,
            url: document.URL
        },
        url: ck_data.ajaxurl,
        success: function (response) {

            var values = JSON.parse(response);

            console.log('user cookie ' + values.user );
            if (0 != values.user) {
                $.cookie('ck_visit', values.user, {expires: 365, path: '/'});
            } else {
                console.log('not setting user cookie');
            }

            console.log('subscriber cookie ' + values.subscriber_id);
            if ( 0 != values.subscriber_id) {
                $.cookie('ck_subscriber_id', values.subscriber_id, {expires: 365, path: '/'});
            } else {
                console.log('not setting subscriber cookie');
            }
        }

    }).fail(function (response) {
        if ( window.console && window.console.log ) {
            console.log( "AJAX ERROR" + response );
        }
    });

    /**
     * This function will check for the `ck_subscriber_id` query parameter
     * and if it exists return the value and remove it from the URL.
     *
     * @param variable
     * @returns {*}
     */
    function ckGetQueryVariable(variable)
    {
        var query = window.location.search.substring(1);
        var vars = query.split("&");
        for (var i=0;i<vars.length;i++) {
            var pair = vars[i].split("=");
            if(pair[0] == variable){
                ckRemoveSubscriberId();
                return pair[1];
            }
        }
        return(false);
    }

    /**
     * Remove the
     *
     * The 'ck_subscriber_id' should only be set on URLs included on
     * links from a ConvertKit email with no other URL parameters.
     * This function removes the parameters so a customer won't share
     * a URL with their subscriber ID in it.
     *
     * TODO: Improve this so it preserves other URL parameters
     *
     * @param key
     * @param url
     */
    function ckRemoveSubscriberId(key,url)
    {
        var clean_url = url.substring(0, url.indexOf("?"));
        var title = document.getElementsByTagName("title")[0].innerHTML;
        window.history.pushState( null, title, clean_url );
    }

});
