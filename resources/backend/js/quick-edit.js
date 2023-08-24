/**
 * Quick Edit
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Copies Quick Edit fields into WordPress' #inline-edit table row on load.
 *
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
	function ( $ ) {

		// Move Quick Edit fields from footer into the hidden inline-edit table row.
		$( 'tr#inline-edit .inline-edit-wrapper fieldset.inline-edit-col-left' ).first().append( $( '#convertkit-quick-edit' ) );

		// Show the Quick Edit fields, as they are now contained in the inline-edit row which WordPress will show/hide as necessary.
		$( '#convertkit-quick-edit' ).show();

		var convertKitInlineEditPost = inlineEditPost.edit;

		// Extend WordPress' inline edit function to load the Plugin's Quick Edit fields.
		inlineEditPost.edit = function ( id ) {

			// Merge arguments from original function.
			convertKitInlineEditPost.apply( this, arguments );

			// Get Post ID.
			if ( typeof( id ) === 'object' ) {
				id = parseInt( this.getId( id ) );
			}

			// Iterate through any ConvertKit inline data, assigning values to Quick Edit fields.
			$( '.convertkit', $( '#inline_' + id ) ).each(
				function () {

					// Assign the setting's value to the setting's Quick Edit field.
					$( '#convertkit-quick-edit select[name="wp-convertkit[' + $( this ).data( 'setting' ) + ']"]' ).val( $( this ).data( 'value' ) );

				}
			);

		}

	}
);
