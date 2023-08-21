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
jQuery( document ).ready(
	function ( $ ) {

		$( 'input[name=type]' ).on(
			'change',
			function ( e ) {

				// For all type radio buttons, hide elements with a class matching the value.
				$( 'input[name=type]' ).each(
					function () {
						$( 'div.' + $( this ).val() ).hide();
					}
				);

				// For the selected radio button, show elements with a class matching the value.
				$( 'div.' + $( 'input[name=type]:checked' ).val() ).show();

			}
		).trigger( 'change' );

	}
);
