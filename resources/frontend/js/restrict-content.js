/**
 * Frontend functionality for Restrict Content functionality.
 *
 * @since   2.3.7
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Register events
 */
jQuery( document ).ready(
	function ( $ ) {

		// Open modal.
		$( '.convertkit-restrict-content-modal-open' ).on(
			'click',
			function ( e ) {
				e.preventDefault();
				convertKitRestrictContentOpenModal();
			}
		);

		// Handle modal form submissions.
		$( document ).on(
			'submit',
			'#convertkit-restrict-content-modal form#convertkit-restrict-content-form',
			function ( e ) {

				e.preventDefault();

				// Disable inputs.
				$( 'input[type="text"], input[type="email"], input[type="submit"]' ).attr( 'disabled', 'disabled' );

				// Show loading overlay.
				$( '#convertkit-restrict-content-modal-loading' ).show();

				// Determine if this is the email or code submission.
				if ( $( 'input#convertkit_subscriber_code' ).length > 0 ) {
					// Code submission.
					convertKitRestrictContentSubscriberVerification(
						$( 'input[name="_wpnonce"]' ).val(),
						$( 'input[name="subscriber_code"]' ).val(),
						$( 'input[name="token"]' ).val(),
						$( 'input[name="convertkit_post_id"]' ).val()
					);

					return false;
				}

				// Email submission.
				convertKitRestrictContentSubscriberAuthenticationSendCode(
					$( 'input[name="_wpnonce"]' ).val(),
					$( 'input[name="convertkit_email"]' ).val(),
					$( 'input[name="convertkit_resource_type"]' ).val(),
					$( 'input[name="convertkit_resource_id"]' ).val(),
					$( 'input[name="convertkit_post_id"]' ).val()
				);

			}
		);

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
 * @since 	2.3.8
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
 * @since 	2.3.8
 */
function convertKitRestrictContentCloseModal() {

	( function ( $ ) {

		$( '#convertkit-restrict-content-modal-background' ).hide();
		$( '#convertkit-restrict-content-modal-loading' ).hide();
		$( '#convertkit-restrict-content-modal' ).hide();

	} )( jQuery );

}

/**
 * Submits the given email address to maybe_run_subscriber_authentication(), which
 * will return either:
 * - the email form view, with an error message e.g. invalid email,
 * - the code form view, where the user can enter the OTP.
 *
 * @since 	2.3.8
 *
 * @param   string   nonce   	     WordPress nonce.
 * @param   string   email   	     Email address.
 * @param   string   resource_type   Resource Type (tag|product).
 * @param   int      resource_id     Resource ID (ConvertKit Tag or Product ID).
 * @param   int      post_id         WordPress Post ID being viewed / accessed.
 */
function convertKitRestrictContentSubscriberAuthenticationSendCode( nonce, email, resource_type, resource_id, post_id ) {

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

					// Hide loading overlay.
					$( '#convertkit-restrict-content-modal-loading' ).hide();

					// Re-bind OTP listener.
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

/**
 * Submits the given email address to maybe_run_subscriber_verification(), which
 * will return either:
 * - the code form view, with an error message e.g. invalid code entered,
 * - the Post's URL, with a `ck-cache-bust` parameter appended, which can then be loaded to show the content.
 *
 * @since 	2.3.8
 *
 * @param   string   nonce   	     WordPress nonce.
 * @param   string   subscriber_code OTP Subscriber Code.
 * @param   string   token           Subscriber Token.
 * @param   int      post_id         WordPress Post ID being viewed / accessed.
 */
function convertKitRestrictContentSubscriberVerification( nonce, subscriber_code, token, post_id ) {

	( function ( $ ) {

		$.ajax(
			{
				type: 'POST',
				data: {
					action: 'convertkit_subscriber_verification',
					'_wpnonce': nonce,
					subscriber_code: subscriber_code,
					token: token,
					convertkit_post_id: post_id
				},
				url: convertkit_restrict_content.ajaxurl,
				success: function ( response ) {

					if ( convertkit_restrict_content.debug ) {
						console.log( response );
					}

					// If the entered code is invalid, show the response in the modal.
					if ( ! response.success ) {
						$( '#convertkit-restrict-content-modal-content' ).html( response.data );

						// Hide loading overlay.
						$( '#convertkit-restrict-content-modal-loading' ).hide();

						// Re-bind OTP listener.
						convertKitRestrictContentOTPField();
						return;
					}

					// Code entered is valid; load the URL in the response data, which will be the
					// current URL with the `ck-cache-bust` parameter appended to it.
					// As cookies are set, the user will now see the restricted content.
					window.location = response.data;

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
 * @since 	2.3.8
 */
function convertKitRestrictContentOTPField() {

	( function ( $ ) {

		// Bail if the OTP input isn't displayed on screen.
		if ( $( '#convertkit_subscriber_code' ) === null ) {
			return;
		}

		$( '#convertkit_subscriber_code' ).on(
			'change keyup input paste',
			function () {

				// Update the --_otp-digit property when the input value changes.
				$( '#convertkit_subscriber_code' ).css( '--_otp-digit', $( '#convertkit_subscriber_code' ).val().length );

				// If all 6 digits have been entered:
				// - move the caret input to the start, to avoid numbers shifting in input,
				// - blur the input now that all numbers are entered,
				// - submit the form.
				if ( $( '#convertkit_subscriber_code' ).val().length === 6 ) {
					$( '#convertkit_subscriber_code' )[0].setSelectionRange( 0, 0 );
					$( '#convertkit_subscriber_code' ).blur();
					$( '#convertkit-restrict-content-form' ).trigger( 'submit' );
				}

			}
		);

	} )( jQuery );

}