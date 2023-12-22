/**
 * Bulk Edit
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Copies Bulk Edit fields into WordPress' #bulk-edit table row on load.
 *
 * WordPress' built in Bulk Edit functionality does not do this automatically
 * for Plugins that register settings, which is why we have this code here.
 *
 * @since 	1.9.8.0
 */
jQuery( document ).ready(
	function ( $ ) {

		// Move Bulk Edit fields from footer into the hidden bulk-edit table row,
		// if a bulk-edit table row exists (it won't exist if searching returns no pages).
		if ( $( 'tr#bulk-edit .inline-edit-wrapper fieldset.inline-edit-col-right' ).length > 0 ) {
			$( 'tr#bulk-edit .inline-edit-wrapper fieldset.inline-edit-col-right' ).first().append( $( '#convertkit-bulk-edit' ) );

			// Show the Bulk Edit fields, as they are now contained in the inline-edit row which WordPress will show/hide as necessary.
			$( '#convertkit-bulk-edit' ).show();
		}

	}
);
