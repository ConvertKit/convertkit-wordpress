/**
 * Displays or hides settings in the UI, depending on which settings are enabled
 * or disabled.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Displays or hides settings in the UI, depending on which settings are enabled
 * or disabled.
 *
 * @since 	2.2.4
 */
jQuery( document ).ready(
	function ( $ ) {

		// Update settings and refresh UI when a setting is changed.
		$( 'input#enabled' ).on(
			'change',
			function () {

				convertKitConditionallyDisplaySettings( $( this ).attr( 'id' ), $( this ).prop( 'checked' ) );

			}
		);

		convertKitConditionallyDisplaySettings( 'enabled', $( 'input#enabled' ).prop( 'checked' ) );

	}
);

/**
 * Shows all table rows on a ConvertKit settings screen, and then hides
 * table rows related to a setting, if that setting is disabled.
 *
 * @since 	2.2.4
 */
function convertKitConditionallyDisplaySettings( name, display ) {

	( function ( $ ) {

		// Show all rows.
		$( 'table.form-table tr' ).show();

		// Don't do anything else if display is true.
		if ( display ) {
			return;
		}

		// Iterate through the table rows, hiding any settings.
		$( 'table.form-table tr' ).each(
			function () {

				// Skip if this table row is for the setting we've just checked/unchecked.
				if ( $( '[id="' + name + '"]', $( this ) ).length > 0 ) {
					return;
				}

				// Hide this row if the input, select, link or span element within the row has the CSS class of the setting name.
				if ( $( 'input, select, a, span', $( this ) ).hasClass( name ) ) {
					$( this ).hide();
				}

			}
		);

	} )( jQuery );

}
