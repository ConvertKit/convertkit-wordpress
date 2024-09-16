/**
 * Restrict Content
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Displays and hides form fields on the Restrict Content setup screen depending
 * on other form field options that have been selected.
 *
 * @since 	2.1.0
 */
document.addEventListener( 'DOMContentLoaded', function() {
	const typeInputs = document.querySelectorAll( 'input[name=type]' );

	function convertKitRestrictContentUpdateDisplayedFields() {
		// For all type radio buttons, hide elements with a class matching the value.
		typeInputs.forEach( function( input ) {
			document.querySelectorAll( 'div.' + input.value ).forEach( function( div ) {
				div.style.display = 'none';
			} );
		} );

		// For the selected radio button, show elements with a class matching the value.
		const checkedInput = document.querySelector( 'input[name=type]:checked' );
		if ( checkedInput ) {
			document.querySelectorAll( 'div.' + checkedInput.value ).forEach( function( div ) {
				div.style.display = 'block';
			} );
		}
	}

	typeInputs.forEach( function( input ) {
		input.addEventListener( 'change', updateVisibility );
	} );

	// Trigger the change event on load.
	convertKitRestrictContentUpdateDisplayedFields();
} );
