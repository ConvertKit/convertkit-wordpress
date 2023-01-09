/**
 * Displays or hides settings in the UI, depending on which settings are enabled
 * or disabled.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

var convertKitSettings = {
	'enabled': false
};

/**
 * Displays or hides settings in the UI, depending on which settings are enabled
 * or disabled.
 *
 * @since 	2.1.0
 */
jQuery( document ).ready(
	function( $ ) {

		// Update settings.
		convertKitSettings = {
			'enabled': $( 'input[name="_wp_convertkit_settings_restrict_content[enabled]"]' ).prop( 'checked' )
		};

		// Refresh UI.
		convertKitSettingsRefreshUI();

		// Update settings and refresh UI when a setting is changed.
		$( 'input[type=checkbox]' ).on(
			'change',
			function() {

				// Update settings.
				convertKitSettings[ $( this ).attr( 'id' ) ] = $( this ).prop( 'checked' );

				// Refresh UI.
				convertKitSettingsRefreshUI();

			}
		);

	}
);

/**
 * Shows all table rows on the integration settings screen, and then hides
 * table rows related to a setting, if that setting is disabled.
 *
 * @since 	2.1.0
 */
function convertKitSettingsRefreshUI() {

	( function( $ ) {

		// Show all rows.
		$( 'table.form-table tr' ).each(
			function() {
				$( this ).show();
			}
		);

		// Iterate through settings.
		for ( let setting in convertKitSettings ) {
			if ( ! convertKitSettings[ setting ] ) {
				$( 'table.form-table tr' ).each(
					function() {
						// Skip if this table row is for the setting we've just checked/unchecked.
						if ( $( '[id="' + setting + '"]', $( this ) ).length > 0 ) {
							return;
						}

						// Hide this row if the input, select, link or span element within the row has the CSS class of the setting name.
						if ( $( 'input, select, a, span', $( this ) ).hasClass( setting ) ) {
							$( this ).hide();
						}
					}
				);

				// Don't do anything else.
				break;
			}
		}

	} )( jQuery );

}
