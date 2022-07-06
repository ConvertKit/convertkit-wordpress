/**
 * Quick Edit
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Populates Quick Edit fields for this Plugin with the values output
 * in the `add_inline_data` WordPress action when the user clicks
 * a Quick Edit link in a Post, Page or Custom Post Type WP_List_Table.
 * 
 * WordPress' built in Quick Edit functionality does not do this automatically
 * for Plugins that register settings, which is why we have this code here.
 *
 * @since 	1.9.8.0
 */
jQuery( document ).ready(

	function( $ ) {

		var convertKitInlineEditPost = inlineEditPost.edit;

		// Extend WordPress' quick edit function.
		inlineEditPost.edit = function( id ) {

			// Merge arguments from original function.
			convertKitInlineEditPost.apply( this, arguments );

			// Get Post ID.
			if ( typeof( id ) === 'object' ) {
				id = parseInt( this.getId( id ) );
			}

			// Iterate through any ConvertKit inline data, assigning values to Quick Edit fields.
			$( '.convertkit', $( '#inline_' + id ) ).each( function() {

				// Assign the setting's value to the setting's Quick Edit field.
				$( 'select[name="wp-convertkit[' + $( this ).data( 'setting' ) + ']"]' ).val( $( this ).data( 'value' ) );

			} );

		}

	}
	
);
