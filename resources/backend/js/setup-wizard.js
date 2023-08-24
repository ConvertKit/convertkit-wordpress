/**
 * Setup Wizard
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Conditionally shows and hides elements when a button or link is clicked, based on that
 * button/link's configuration.
 *
 * @since 	1.9.8.4
 */
jQuery( document ).ready(
	function ( $ ) {

		// Redirect parent screen to a given URL after clicking a link that opens
		// the href URL in a new tab.
		$( 'a.convertkit-redirect' ).on(
			'click',
			function ( e ) {

				var link = this;

				// Delay the redirect, otherwise browsers will block opening the href attribute
				// thinking it's a popup.
				setTimeout(
					function () {
						// Redirect the parent screen to the link's data-convertkit-redirect-url property.
						window.location.href = $( link ).data( 'convertkit-redirect-url' );
					},
					1000
				);

			}
		);

		// Show a confirmation dialog for specific links.
		$( 'a.convertkit-confirm' ).on(
			'click',
			function ( e ) {

				if ( ! confirm( $( this ).data( 'message' ) ) ) {
					e.preventDefault();
				}

			}
		);

	}
);
