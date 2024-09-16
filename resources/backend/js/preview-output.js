/**
 * Preview Output Wizard
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Appends a <select> field value to a link, to build a preview link for e.g. forms.
 *
 * @since 	1.9.8.5
 */
document.addEventListener( 'DOMContentLoaded', function() {
    // Appends a <select> field value to a link. Used for previews.
    document.querySelectorAll( 'select.convertkit-preview-output-link' ).forEach( function( select ) {
        select.addEventListener( 'change', function() {
            var target = this.dataset.target;
            var link = this.dataset.link + this.value;

            document.querySelector( target ).setAttribute( 'href', link );
        } );

        // Trigger change event on load.
        var event = new Event( 'change' );
        select.dispatchEvent( event );
    } );
} );
