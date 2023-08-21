/**
 * Frontend functionality for subscribers and tags.
 *
 * @since   1.9.6
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Register events
 */
jQuery( document ).ready(
	function ( $ ) {

		$( document ).on(
			'click',
			'ul.convertkit-broadcasts-pagination a',
			function ( e ) {

				e.preventDefault();

				// Get block container and build object of data-* attributes.
				let blockContainer = $( this ).closest( 'div.convertkit-broadcasts' );
				let atts           = {
					display_date: $( blockContainer ).data( 'display-date' ),
					date_format: $( blockContainer ).data( 'date-format' ),
					display_image: $( blockContainer ).data( 'display-image' ),
					display_description: $( blockContainer ).data( 'display-description' ),
					display_read_more: $( blockContainer ).data( 'display-read-more' ),
					read_more_label: $( blockContainer ).data( 'read-more-label' ),
					limit: $( blockContainer ).data( 'limit' ),
					paginate: $( blockContainer ).data( 'paginate' ),
					paginate_label_prev: $( blockContainer ).data( 'paginate-label-prev' ),
					paginate_label_next: $( blockContainer ).data( 'paginate-label-next' ),
					link_color: $( blockContainer ).data( 'link-color' ),
					page: $( this ).data( 'page' ), // Page is supplied as a data- attribute on the link clicked, not the container.
					nonce: $( this ).data( 'nonce' ) // Nonce is supplied as a data- attribute on the link clicked, not the container.
				};

				convertKitBroadcastsRender( blockContainer, atts );

			}
		);

	}
);

/**
 * Sends an AJAX request to request HTML based on the supplied block attributes,
 * when pagination is used on a Broadcast block.
 *
 * @since 	1.9.7.6
 *
 * @param 	object 	blockContainer 	DOM object of the block to refresh the HTML in.
 * @param 	object 	atts 			Block attributes
 */
function convertKitBroadcastsRender( blockContainer, atts ) {

	( function ( $ ) {

		// Append action.
		atts.action = convertkit_broadcasts.action;

		if ( convertkit_broadcasts.debug ) {
			console.log( 'convertKitBroadcastsRender()' );
			console.log( atts );
		}

		// Show loading indicator.
		$( blockContainer ).addClass( 'convertkit-broadcasts-loading' );

		$.ajax(
			{
				url:        convertkit_broadcasts.ajax_url,
				type:       'POST',
				async:      true,
				data:      	atts,
				success: function ( result ) {
					if ( convertkit_broadcasts.debug ) {
						console.log( result );
					}

					// Remove loading indicator.
					$( blockContainer ).removeClass( 'convertkit-broadcasts-loading' );

					// Replace block container's HTML with response data.
					$( blockContainer ).html( result.data );
				}
			}
		).fail(
			function (response) {
				// Remove loading indicator.
				$( blockContainer ).removeClass( 'convertkit-broadcasts-loading' );

				if ( convertkit.debug ) {
					console.log( response );
				}
			}
		);

	} )( jQuery );

}
