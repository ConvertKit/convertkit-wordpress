(function($){
    'use strict';

    function CKAdmin() {

        var self = this;

        this.init = function () {
            this.hooks();
        };

        this.hooks = function () {
            $(document).on('click', '#refreshCKForms', this.refreshForms);
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
                data: { action: 'ck_refresh_forms' },
                success: function (resp) {
                    if ( resp.success ) {
                        $('#default_form').html('');
                        $('#default_form').append($('<option>', {
                            value: 'default',
                            text: ck_admin.option_none
                        }));
                        for( var form_id in resp.data ) {
                            var form = resp.data[ form_id ];
                            $('#default_form').append($('<option>', {
                                value: form_id,
                                text: form.name
                            }));
                        }
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

    }

    // Doc Ready
    $(function () {
        var ckAdmin = new CKAdmin();
        ckAdmin.init();
    });

})(jQuery);