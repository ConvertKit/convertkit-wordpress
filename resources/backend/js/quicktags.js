/**
 * Registers Blocks in the text editor as QuickTag Buttons.
 *
 * @since   1.9.6
 *
 * @package ConvertKit
 * @author ConvertKit
 */

for ( const block in convertkit_quicktags ) {

	convertKitQuickTagRegister( convertkit_quicktags[ block ] );

}

/**
 * Registers the given block as a Quick Tag, with a button in
 * the Text Editor toolbar.
 *
 * @since 	1.9.6
 *
 * @param 	object 	block 	Block
 */
function convertKitQuickTagRegister( block ) {

	( function( $ ) {

		QTags.addButton(
			'convertkit-' + block.name,
			block.title,
			function() {

				// Perform an AJAX call to load the modal's view.
				$.post(
					ajaxurl,
					{
						'action': 		'convertkit_admin_tinymce_output_modal',
						'nonce':  		convertkit_admin_tinymce.nonce,
						'editor_type':  'quicktags',
						'shortcode': 	block.name

					},
					function( response ) {

						// Show Modal.
						convertKitQuickTagsModal.open();

						// Set Title.
						$( '#convertkit-quicktags-modal .media-frame-title h1' ).text( block.title );

						// Inject HTML into modal.
						$( '#convertkit-quicktags-modal .media-frame-content' ).html( response );

						// Initialize color pickers.
						$( '.convertkit-color-picker' ).wpColorPicker();

						// Resize Modal height up to a maximum of 580px.
						// This ensures shortcodes with fewer options don't display whitespace below form fields,
						// and shortcodes with many options won't result in the modal exceeding the size of the screen.
						// Content will overflow-y to show a scrollbar where necessary.
						let height = $( 'div.convertkit-quicktags-modal form.convertkit-tinymce-popup' ).height();
						if ( height > 580 ) {
							height = 580;
						}
						
						$( 'div.convertkit-quicktags-modal div.media-modal.wp-core-ui' ).css(
							{
								height: ( $( 'div.convertkit-quicktags-modal div.media-frame-title h1' ).outerHeight() + height + 6 ) + 'px' // Additional 6px prevents a vertical scroll bar due to larger title vs. TinyMCE.
							}
						);

					}
				);

			}
		);

	} )( jQuery );

}
