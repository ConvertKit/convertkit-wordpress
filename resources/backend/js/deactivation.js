/**
 * Shows the deactivation modal window when the Plugin is deactivated
 * through the Plugins screen, giving the user an option to specify
 * why they are deactivating.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

var convertkit_deactivation_url;

jQuery( document ).ready(
	function( $ ) {

		/**
		 * Show deactivation modal if the user is deactivating our plugin.
		 */
		$( 'span.deactivate a' ).on(
			'click',
			function( e ) {

				// If the link slug doesn't exist, let the request through.
				var plugin_name = $( this ).closest( 'tr' ).data( 'slug' );
				if ( typeof plugin_name === 'undefined' ) {
					return true;
				}

				// If the Plugin being deactivated isn't our one, let the request through.
				if ( plugin_name != convertkit_deactivation.plugin.name ) {
					return true;
				}

				// If here, we're deactivating our plugin.
				e.preventDefault();

				// Store the target URL.
				convertkit_deactivation_url = $( this ).attr( 'href' );

				// Position the modal.
				$( '#convertkit-deactivation-modal' ).css(
					{
						top: ( $( this ).offset().top - $( this ).height() - 25 ) + 'px',
						left: ( $( this ).offset().left + $( this ).width() + 20 ) + 'px'
					}
				);

				// Show the modal.
				$( '#convertkit-deactivation-modal, #convertkit-deactivation-modal-overlay' ).show();

			}
		);

		/**
		 * Update input text field's placeholder when a reason radio button is clicked
		 */
		$( 'input[name="convertkit-deactivation-reason"]' ).on(
			'change',
			function( e ) {

				$( 'input[name="convertkit-deactivation-reason-text"]' ).attr(
					'placeholder',
					$( this ).data( 'placeholder' )
				).show();

			}
		);

		/**
		 * Send the result of the deactivation modal when the submit button is clicked,
		 * and load the deactivation URL so that the plugin gets deactivated.
		 */
		$( 'form#convertkit-deactivation-modal-form' ).on(
			'submit',
			function( e ) {

				e.preventDefault();

				var convertkit_deactivation_reason  = $( 'input[name=convertkit-deactivation-reason]:checked', $( this ) ).val(),
				convertkit_deactivation_reason_text = $( 'input[name=convertkit-deactivation-reason-text]', $( this ) ).val();

				// Submit the form via AJAX if a reason was given.
				if ( typeof convertkit_deactivation_reason !== 'undefined' ) {
					$.ajax(
						{
							url: 		ajaxurl,
							type: 		'POST',
							async:    	true,
							data: 		{
								action: 		'convertkit_deactivation_modal_submit',
								product: 		convertkit_deactivation.plugin.name,
								version: 		convertkit_deactivation.plugin.version,
								reason: 		convertkit_deactivation_reason,
								reason_text: 	convertkit_deactivation_reason_text
							},
							error: function( a, b, c ) {
							},
							success: function( result ) {
							}
						}
					);
				}

				// Hide the modal.
				$( '#convertkit-deactivation-modal, #convertkit-deactivation-modal-overlay' ).hide();

				// Load the deactivation URL.
				window.location.href = convertkit_deactivation_url;

			}
		);

	}
);
