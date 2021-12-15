/**
 * Frontend functionality for subscribers and tags.
 *
 * @since   1.9.6
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Tags the given subscriber ID with the given tag
 *
 * @since   1.9.6
 *
 * @param   int     subscriber_id   Subscriber ID
 * @param   string  tag             Tag
 * @param   int     post_id         WordPress Post ID
 */
function convertKitTagSubscriber( subscriber_id, tag, post_id ) {

	if ( convertkit.debug ) {
		console.log( 'convertKitTagSubscriber' );
		console.log( convertkit );
		console.log( subscriber_id );
		console.log( tag );
		console.log( post_id );
	}

	( function( $ ) {

		$.ajax(
			{
				type: 'POST',
				data: {
					action: 'convertkit_tag_subscriber',
					convertkit_nonce: convertkit.nonce,
					subscriber_id: subscriber_id,
					tag: tag,
					post_id: post_id
				},
				url: convertkit.ajaxurl,
				success: function ( response ) {
					if ( convertkit.debug ) {
						console.log( response );
					}
					convertKitRemoveSubscriberIDFromURL( window.location.href );
				}
			}
		).fail(
			function (response) {
				if ( convertkit.debug ) {
					console.log( response );
				}
				convertKitRemoveSubscriberIDFromURL( window.location.href );
			}
		);

	} )( jQuery );

}

/**
 * Gets the subscriber ID for the given email address, storing
 * it in the `ck_subscriber_id` cookie if it exists.
 *
 * Typically called when the user completes a ConvertKit Form
 * that has either "Auto-confirm new subscribers" or
 * "Send subscriber to thank you page" enabled (both scenarios
 * include a ck_subscriber_id).
 *
 * @since   1.9.6
 *
 * @param   int  id   Subscriber ID
 */
function convertStoreSubscriberIDInCookie( subscriber_id ) {

	if ( convertkit.debug ) {
		console.log( 'convertStoreSubscriberIDInCookie' );
		console.log( subscriber_id );
	}

	( function( $ ) {

		$.ajax(
			{
				type: 'POST',
				data: {
					action: 'convertkit_store_subscriber_id_in_cookie',
					convertkit_nonce: convertkit.nonce,
					subscriber_id: subscriber_id
				},
				url: convertkit.ajaxurl,
				success: function ( response ) {
					if ( convertkit.debug ) {
						console.log( response );
					}

					convertKitRemoveSubscriberIDFromURL( window.location.href );
				}
			}
		).fail(
			function (response) {
				if ( convertkit.debug ) {
					console.log( response );
				}

				convertKitRemoveSubscriberIDFromURL( window.location.href );
			}
		);

	} )( jQuery );

}

/**
 * Gets the subscriber ID for the given email address, storing
 * it in the `ck_subscriber_id` cookie if it exists.
 *
 * Typically called when the user completes a ConvertKit Form
 * that has either "Auto-confirm new subscribers" or
 * "Send subscriber to thank you page" enabled (both scenarios
 * include a ck_subscriber_id).
 *
 * @since   1.9.6
 *
 * @param   string  email_address   Email Address
 */
function convertStoreSubscriberEmailAsIDInCookie( email_address ) {

	if ( convertkit.debug ) {
		console.log( 'convertStoreSubscriberEmailAsIDInCookie' );
		console.log( email_address );
	}

	( function( $ ) {

		$.ajax(
			{
				type: 'POST',
				data: {
					action: 'convertkit_store_subscriber_email_as_id_in_cookie',
					convertkit_nonce: convertkit.nonce,
					email:  email_address
				},
				url: convertkit.ajaxurl,
				success: function ( response ) {
					if ( convertkit.debug ) {
						console.log( response );
					}
				}
			}
		).fail(
			function (response) {
				if ( convertkit.debug ) {
					console.log( response );
				}
			}
		);

	} )( jQuery );

}

/**
 * Remove the url subscriber_id url param
 *
 * The 'ck_subscriber_id' should only be set on URLs included on
 * links from a ConvertKit email with no other URL parameters.
 * This function removes the parameters so a customer won't share
 * a URL with their subscriber ID in it.
 *
 * @param url
 */
function convertKitRemoveSubscriberIDFromURL( url ) {

	var clean_url = url.substring( 0, url.indexOf( "?ck_subscriber_id" ) );
	var title     = document.getElementsByTagName( "title" )[0].innerHTML;
	if ( clean_url ) {
		window.history.pushState( null, title, clean_url );
	}

}

/**
 * Utility function to pause for the given number of milliseconds
 *
 * @since   1.9.6
 *
 * @param   int     milliseconds
 */
function convertKitSleep( milliseconds ) {

	var start = new Date().getTime();
	for (var i = 0; i < 1e7; i++) {
		if ((new Date().getTime() - start) > milliseconds) {
			break;
		}
	}

}

/**
 * Register events
 */
jQuery( document ).ready(
	function( $ ) {

		if ( convertkit.subscriber_id > 0 && convertkit.tag && convertkit.post_id ) {
			// If the user can be detected as a ConvertKit Subscriber (i.e. their Subscriber ID is in a cookie or the URL),
			// and the Page/Post they are viewing has a Tag specified, subscribe them to the tag.
			convertKitTagSubscriber( convertkit.subscriber_id, convertkit.tag, convertkit.post_id );
		} else if ( convertkit.subscriber_id > 0 ) {
			// If the user can be detected as a ConvertKit Subscriber (i.e. their Subscriber ID is in a cookie or the URL),
			// update the cookie now.
			convertStoreSubscriberIDInCookie( convertkit.subscriber_id );
		}

		// Store subscriber ID as a cookie from the email address used when a ConvertKit Form is submitted.
		$( document ).on(
			'click',
			'.formkit-submit',
			function() {
				var email_address = $( 'input[name="email_address"]' ).val();
				convertKitSleep( 2000 );
				convertStoreSubscriberEmailAsIDInCookie( email_address );
			}
		);

	}
);
