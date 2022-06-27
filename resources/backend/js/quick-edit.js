/**
 * Quick Edit
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Reads and saves Post meta values for this Plugin when editing a Page,
 * Post or Custom Post type using WordPress' Quick Edit functionality
 *
 * @since 	1.9.8.0
 */
jQuery( document ).ready(

	function( $ ) {

		var wp_inline_edit_function = inlineEditPost.edit;

		// Extend WordPress' quick edit function.
		inlineEditPost.edit = function( post_id ) {

			// Merge arguments from original function.
			wp_inline_edit_function.apply( this, arguments );

			// Get Post ID.
			var id = 0;
			if ( typeof( post_id ) === 'object' ) {
				id = parseInt( this.getId( post_id ) );
			}

			// 
			if ( id > 0 ) {

				// @TODO Get hidden data.

				// add rows to variables
				/*
				var specific_post_edit_row = $( '#edit-' + id ),
				    specific_post_row = $( '#post-' + id ),
				    product_price = $( '.column-price', specific_post_row ).text().substring(1), //  remove $ sign
				    featured_product = false; // let's say by default checkbox is unchecked
				*/

				// Populate inputs with hidden data.
				// @TODO.
				$( ':input[name="price"]', specific_post_edit_row ).val( product_price );
				$( ':input[name="featured"]', specific_post_edit_row ).prop('checked', featured_product );

			}
		}

	}
	
);
