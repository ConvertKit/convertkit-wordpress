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
document.addEventListener( 'DOMContentLoaded', function() {

    // Update settings and refresh UI when a setting is changed.
    const enabledInput = document.querySelector( 'input#enabled' );
    enabledInput.addEventListener( 'change', function() {
        convertKitConditionallyDisplaySettings( this.id, this.checked );
    } );

    convertKitConditionallyDisplaySettings( 'enabled', enabledInput.checked );

} );

/**
 * Shows all table rows on a ConvertKit settings screen, and then hides
 * table rows related to a setting, if that setting is disabled.
 *
 * @since 	2.2.4
 */
function convertKitConditionallyDisplaySettings( name, display ) {

    // Show all rows.
    const rows = document.querySelectorAll( 'table.form-table tr');
    rows.forEach( row => row.style.display = '' );

    // Don't do anything else if display is true.
    if ( display ) {
        return;
    }

    // Iterate through the table rows, hiding any settings.
    rows.forEach( function( row ) {
        // Skip if this table row is for the setting we've just checked/unchecked.
        if ( row.querySelector( `[id="${name}"]` ) ) {
            return;
        }

        // Hide this row if the input, select, link or span element within the row has the CSS class of the setting name.
        if ( row.querySelector( `input.${name}, select.${name}, a.${name}, span.${name}` ) ) {
            row.style.display = 'none';
        }
    } );

}
