/**
 * Moves any buttons added from the filter list in a WP_List_Table
 * to be displayed next to the "Add New" button.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

jQuery( document ).ready(
	function ( $ ) {

		// Move any buttons from the filter list to display next to the Add New button.
		$( 'ul.subsubsub a' ).each(
			function () {

				// Ignore if not a ConvertKit Group Action.
				if ( ! $( this ).hasClass( 'convertkit-action' ) ) {
					return;
				}

				// Move.
				$( this ).clone().removeClass( 'hidden' ).insertAfter( 'a.page-title-action' );

				// Remove original.
				$( this ).parent().remove();

			}
		);

	}
);
