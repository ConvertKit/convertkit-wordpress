/**
 * Frontend functionality for Restrict Content functionality.
 *
 * @since   2.3.7
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

		// Update --opt-digit when code is input into the OTP field.
		convertKitRestrictContentOTPField();

		// Open modal.
		$( '.convertkit-restrict-content-modal-open' ).on(
			'click',
			function ( e ) {
				e.preventDefault();
				convertKitRestrictContentOpenModal();
			}
		);

		// Handle modal form submissions.
		$( 'body' ).on( 'submit', '#convertkit-restrict-content-modal form#convertkit-restrict-content-form', function( e ) {

			e.preventDefault();

			// Determine if this is the email or code submission.
			if ( $( 'input#convertkit_subscriber_code' ).length > 0 ) {
				// Code submission.
				convertKitRestrictContentSubscriberVerification(
					$( 'input[name="_wpnonce"]' ).val(),
					$( 'input[name="subscriber_code"]' ).val(),
					$( 'input[name="token"]' ).val(),
					$( 'input[name="convertkit_post_id"]' ).val()
				);
				return;
			}

			// Email submission.
			convertKitRestrictContentSubscriberAuthenticationSendCode(
				$( 'input[name="_wpnonce"]' ).val(),
				$( 'input[name="convertkit_email"]' ).val(),
				$( 'input[name="convertkit_resource_type"]' ).val(),
				$( 'input[name="convertkit_resource_id"]' ).val(),
				$( 'input[name="convertkit_post_id"]' ).val()
			);

		} );

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
 * @since 	2.3.7
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
 * @since 	2.3.7
 */
function convertKitRestrictContentCloseModal() {

	( function ( $ ) {

		$( '#convertkit-restrict-content-modal-background' ).hide();
		$( '#convertkit-restrict-content-modal' ).hide();

	} )( jQuery );

}

function convertKitRestrictContentSubscriberAuthenticationSendCode( nonce, email, resource_type, resource_id, post_id ) {

	console.log( 'convertKitRestrictContentSubscriberAuthenticationSendCode' );
	console.log( nonce );
	console.log( email );
	console.log( resource_type );
	console.log( resource_id );
	console.log( post_id );

	( function ( $ ) {

		$.ajax(
			{
				type: 'POST',
				data: {
					action: 'convertkit_subscriber_authentication_send_code',
					'_wpnonce': nonce,
					convertkit_email: email,
					convertkit_resource_type: resource_type,
					convertkit_resource_id: resource_id,
					convertkit_post_id: post_id
				},
				url: convertkit_restrict_content.ajaxurl,
				success: function ( response ) {

					if ( convertkit_restrict_content.debug ) {
						console.log( response );
					}

					// Output response, which will be a form with/without an error message.
					$( '#convertkit-restrict-content-modal-content' ).html( response.data );

					// Update --opt-digit when code is input into the OTP field.
					convertKitRestrictContentOTPField();

				}
			}
		).fail(
			function ( response ) {
				if ( convertkit_restrict_content.debug ) {
					console.log( response );
				}

			}
		);

	} )( jQuery );

}

function convertKitRestrictContentSubscriberVerification( nonce, code, token, post_id ) {

	console.log( 'convertKitRestrictContentSubscriberVerification' );
	console.log( nonce );
	console.log( code );
	console.log( token );
	console.log( post_id );

	( function ( $ ) {

		$.ajax(
			{
				type: 'POST',
				data: {
					action: 'subscriber_verification',
					'_wpnonce': nonce,
					code: code,
					token: token,
					convertkit_post_id: post_id
				},
				url: convertkit_restrict_content.ajaxurl,
				success: function ( response ) {

					if ( convertkit_restrict_content.debug ) {
						console.log( response );
					}

					// @TODO Check output to determine if we need to redirect or not.
					$( '#convertkit-restrict-content-modal-content' ).html( response.data );

				}
			}
		).fail(
			function ( response ) {
				if ( convertkit_restrict_content.debug ) {
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
 * @since 	2.3.7
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

			// If all 6 digits have been entered:
			// - move the caret input to the start, to avoid numbers shifting in input,
			// - blur the input now that all numbers are entered,
			// - submit the form.
			if ( convertKitRestrictContentSubscriberCodeInput.selectionStart === 6 ) {
				convertKitRestrictContentSubscriberCodeInput.setSelectionRange( 0, 0 );
				convertKitRestrictContentSubscriberCodeInput.blur();
				document.querySelector( '#convertkit-restrict-content-form' ).submit();
			}
		}
	);

}