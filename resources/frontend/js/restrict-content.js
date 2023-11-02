/**
 * Frontend functionality for Restrict Content functionality.
 *
 * @since   2.3.6
 *
 * @package ConvertKit
 * @author ConvertKit
 */

console.log( convertkit_restrict_content );

/**
 * Register events
 */
jQuery( document ).ready(
	function ( $ ) {

		convertKitRestrictContentOTPField();

		// Open modal.
		$( '.convertkit-restrict-content-modal-open' ).on(
			'click',
			function ( e ) {
				e.preventDefault();
				convertKitRestrictContentOpenModal();
			}
		);

		// Submit form.
		/*
		$( 'form#convertkit-restrict-content-form' ).on( 'submit', function( e ) {

			e.preventDefault();

			convertKitRestrictContentSubmitForm(
				$( 'input[name="_wpnonce"]' ).val(),
				$( 'input[name="convertkit_email"]' ).val(),
				$( 'input[name="convertkit_resource_type"]' ).val(),
				$( 'input[name="convertkit_resource_id"]' ).val()
			);

		} );
		*/

		// Close modal.
		$( '#convertkit-restrict-content-modal-close' ).on(
			'click',
			function ( e ) {
				e.preventDefault();
				convertKitRestrictContentCloseModal();
			}
		);

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

function convertKitRestrictContentSubmitForm( nonce, email, resource_type, resource_id ) {

	console.log( nonce );
	console.log( email );
	console.log( resource_type );
	console.log( resource_id );

	( function ( $ ) {

		$.ajax(
			{
				type: 'POST',
				data: {
					action: 'convertkit_subscriber_authentication_send_code',
					'_wpnonce': nonce,
					convertkit_email: email,
					convertkit_resource_type: resource_type,
					convertkit_resource_id: resource_id
				},
				url: convertkit_restrict_content.ajaxurl,
				success: function ( response ) {
					if ( convertkit.debug ) {
						console.log( response );
					}

				}
			}
		).fail(
			function ( response ) {
				if ( convertkit.debug ) {
					console.log( response );
				}

			}
		);

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

	convertKitRestrictContentSubscriberCodeInput.addEventListener(
		'input',
		function () {
			convertKitRestrictContentSubscriberCodeInput.style.setProperty( '--_otp-digit', convertKitRestrictContentSubscriberCodeInput.selectionStart );

			// If all 6 digits have been entered, move the caret input to the start, to avoid numbers shifting in input,
			// and blur the input now that all numbers are entered.
			// When served in a modal, there won't be a submit button, so this event will also be used to submit the form.
			if ( convertKitRestrictContentSubscriberCodeInput.selectionStart === 6 ) {
				convertKitRestrictContentSubscriberCodeInput.setSelectionRange( 0, 0 );
				convertKitRestrictContentSubscriberCodeInput.blur();
			}
		}
	);

}