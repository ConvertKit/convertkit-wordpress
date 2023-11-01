/**
 * Frontend functionality for Restrict Content functionality.
 *
 * @since   2.3.6
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Register events
 */
jQuery( document ).ready(
	function ( $ ) {

		convertKitRestrictContentOTPField();

		// Show link to display modal.
		$( '.convertkit-restrict-content-modal-open' ).show();

		// Move content to within the modal, ready for when it is opened.
		$( '#convertkit-restrict-content-modal-content' ).appendTo( '#convertkit-restrict-content-modal' );

		// Open modal.
		$( '.convertkit-restrict-content-modal-open' ).on( 'click', function( e ) {
			e.preventDefault();
			convertKitRestrictContentOpenModal();
		} );

		// Close modal.
		$( '#convertkit-restrict-content-modal-close' ).on( 'click', function( e ) {
			e.preventDefault();
			convertKitRestrictContentCloseModal();
		} );

	}
);

/**
 * Opens the modal, displaying the content stored within the
 * #convertkit-restrict-content-modal-content element.
 * 
 * @since 	2.3.6
 */
function convertKitRestrictContentOpenModal() {

	( function ( $ ) {

		$( '#convertkit-restrict-content-modal-background' ).show();
		$( '#convertkit-restrict-content-modal' ).show();

	} )( jQuery );

}

/**
 * Closes the modal.
 * 
 * @since 	2.3.6
 */
function convertKitRestrictContentCloseModal() {

	( function ( $ ) {

		$( '#convertkit-restrict-content-modal-background' ).hide();
		$( '#convertkit-restrict-content-modal' ).hide();

	} )( jQuery );

}

/**
 * Defines the `--opt-digit` CSS var, so that the background color shifts to the next input
 * when entering the one time code.
 * 
 * @since 	2.3.6
 */
function convertKitRestrictContentOTPField() {

	// Bail if the OTP input isn't displayed on screen.
	const convertKitRestrictContentSubscriberCodeInput = document.querySelector( '#convertkit_subscriber_code' );
	if ( convertKitRestrictContentSubscriberCodeInput == null ) {
		return;
	}

	convertKitRestrictContentSubscriberCodeInput.addEventListener( 'input', function() {
		convertKitRestrictContentSubscriberCodeInput.style.setProperty( '--_otp-digit', convertKitRestrictContentSubscriberCodeInput.selectionStart );

		// If all 6 digits have been entered, move the caret input to the start, to avoid numbers shifting in input,
		// and blur the input now that all numbers are entered.
		// When served in a modal, there won't be a submit button, so this event will also be used to submit the form.
		if ( convertKitRestrictContentSubscriberCodeInput.selectionStart === 6 ) {
			convertKitRestrictContentSubscriberCodeInput.setSelectionRange(0, 0);
			convertKitRestrictContentSubscriberCodeInput.blur();
		}
	} );

}