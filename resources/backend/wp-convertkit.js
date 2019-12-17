(function($){
    'use strict';

    function CKAdmin() {

        var self = this;

        this.init = function () {
            this.hooks();
        };

        this.hooks = function () {
            $(document).on('click', '#refreshCKForms', this.refreshForms);
            $(document).on( 'keyup', '#api_key', this.hideShowRefreshButton );
        };

        this.startSpinner = function () {
            $('#refreshCKSpinner').addClass('is-active').css('float', 'none');
        };

        this.stopSpinner = function () {
            $('#refreshCKSpinner').removeClass('is-active');
        };

        this.refreshForms = function (e) {
            e.preventDefault();
            self.startSpinner();
            $.ajax({
                url: window.ajaxurl,
                data: { action: 'ck_refresh_forms', api_key: $('#api_key').val() },
                success: function (resp) {
                    if ( resp.success ) {
                        $('#default_form_container').parent().html( resp.data.default );
                        $('#product_form_container').parent().html( resp.data.woocommerce );
                    } else {
                        alert( resp.data );
                    }
                },
                error: function( resp ) {
                    alert( resp.statusText );
                },
                complete: function () {
                    self.stopSpinner();
                }
            });
        };

        this.hideShowRefreshButton = function(e) {
            var value = $(this).val();

            if ( value ) {
                $('#refreshCKForms').show();
            } else {
                $('#refreshCKForms').hide();
            }
        };
    }

    // Doc Ready
    $(function () {
        var ckAdmin = new CKAdmin();
        ckAdmin.init();
    });

})(jQuery);