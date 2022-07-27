/**
 * Refresh Resources
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Refreshes resources when the Refresh button is clicked.
 *
 * @since 	1.9.8.0
 */
jQuery( document ).ready(
	function( $ ) {

		$( 'button.wp-convertkit-refresh-resources' ).on(
			'click',
			function( e ) {

				e.preventDefault();

				// Fetch some DOM elements.
				var button = this,
				resource   = $( button ).data( 'resource' ),
				field      = $( button ).data( 'field' );

				// Disable button.
				$( button ).prop( 'disabled', true );

				// Perform AJAX request to refresh resource.
				$.ajax(
					{
						type: 'POST',
						data: {
							action: 'convertkit_admin_refresh_resources',
							nonce: convertkit_admin_refresh_resources.nonce,
							resource: resource // e.g. forms, landing_pages, tags.
						},
						url: convertkit_admin_refresh_resources.ajaxurl,
						success: function ( response ) {

							if ( convertkit_admin_refresh_resources.debug ) {
								console.log( response );
							}

							// Get currently selected option.
							var selectedOption = $( field ).val();

							// Remove existing select options.
							$( 'option', $( field ) ).each(
								function() {
									// Skip if data-preserve-on-refresh is specified, as this means we want to keep this specific option.
									// This will be present on the 'None' and 'Default' options.
									if ( typeof $( this ).data( 'preserve-on-refresh' ) !== 'undefined' ) {
										return;
									}

									// Remove this option.
									$( this ).remove();
								}
							);

							// Populate select options from response data.
							response.data.forEach(
								function( item ) {
									$( field ).append( new Option( item.name, item.id, false, ( selectedOption == item.id ? true : false ) ) );
								}
							);

							// Trigger a change event on the select field, to allow Select2 instances to repopulate their options.
							$( field ).trigger( 'change' );

							// Enable button.
							$( button ).prop( 'disabled', false );

						}
					}
				).fail(
					function (response) {
						if ( convertkit_admin_refresh_resources.debug ) {
							console.log( response );
						}
					}
				);

			}
		);

	}
);
