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
	function ( $ ) {

		$( 'button.wp-convertkit-refresh-resources' ).on(
			'click',
			function ( e ) {

				// Prevent default button behaviour.
				e.preventDefault();

				// Remove any existing error notices that might be displayed.
				convertKitRefreshResourcesRemoveNotices();

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

							// Show an error if the request wasn't successful.
							if ( ! response.success ) {
								// Show error notice.
								convertKitRefreshResourcesOutputErrorNotice( response.data );

								// Enable button.
								$( button ).prop( 'disabled', false );

								return;
							}

							// Get currently selected option.
							var selectedOption = $( field ).val();

							// Remove existing select options.
							$( 'option', $( field ) ).each(
								function () {
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
								function ( item ) {
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
					function ( response ) {
						if ( convertkit_admin_refresh_resources.debug ) {
							console.log( response );
						}

						// Remove any existing error notices that might be displayed.
						convertKitRefreshResourcesRemoveNotices();

						// Show error notice.
						convertKitRefreshResourcesOutputErrorNotice( 'ConvertKit: ' + response.status + ' ' + response.statusText );

						// Enable button.
						$( button ).prop( 'disabled', false );
					}
				);

			}
		);

	}
);

/**
 * Removes any existing ConvertKit WordPress style error notices.
 *
 * @since 	1.9.8.3
 */
function convertKitRefreshResourcesRemoveNotices() {

	// If we're editing a Page, Post or Custom Post Type in Gutenberg, use wp.data.dispatch to remove the error.
	if ( typeof wp !== 'undefined' && typeof wp.blocks !== 'undefined' ) {
		// Gutenberg Editor.
		wp.data.dispatch( 'core/notices' ).removeNotice( 'convertkit-error' );
		return;
	}

	// Classic Editor, WP_List_Table (Bulk/Quick edit) or Edit Term.
	( function ( $ ) {

		$( 'div.convertkit-error' ).remove();

	} )( jQuery );

}

/**
 * Removes any existing ConvertKit WordPress style error notices, before outputting
 * an error notice.
 *
 * @since 	1.9.8.3
 *
 * @param 	string 	message 	Error message to display.
 */
function convertKitRefreshResourcesOutputErrorNotice( message ) {

	// Prefix the message with the Plugin name.
	message = 'ConvertKit: ' + message;

	// If we're editing a Page, Post or Custom Post Type in Gutenberg, use wp.data.dispatch to show the error.
	if ( typeof wp !== 'undefined' && typeof wp.blocks !== 'undefined' ) {
		// Gutenberg Editor.
		wp.data.dispatch( 'core/notices' ).createErrorNotice(
			message,
			{
				id: 'convertkit-error'
			}
		);

		return;
	}

	// Classic Editor, WP_List_Table (Bulk/Quick edit) or Edit Term.
	( function ( $ ) {

		var notice = '<div id="message" class="error convertkit-error notice is-dismissible"><p>' + message + '</p></div>';

		// Append the WordPress style error notice, depending on the screen.
		if ( $( 'hr.wp-header-end' ).length > 0 ) {
			$( 'hr.wp-header-end' ).after( notice );
		} else if ( $( '#ajax-response' ).length > 0 ) {
			$( '#ajax-response' ).after( notice );
		}

		// Notify WordPress that a new dismissible notification exists, triggering WordPress' makeNoticesDismissible() function,
		// which adds a dismiss button and binds necessary events to hide the notification.
		// We can't directly call makeNoticesDismissible(), as its minified function name will be different.
		$( document ).trigger( 'wp-updates-notice-added' );

	} )( jQuery );

}
