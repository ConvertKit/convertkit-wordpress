/**
 * Handles registration of TinyMCE buttons.
 *
 * @since   1.9.6
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers the given block as a TinyMCE Plugin, with a button in
 * the Visual Editor toolbar.
 *
 * @since 	1.9.6
 *
 * @param 	object 	block 	Block
 */
function convertKitTinyMCERegisterPlugin( block ) {

	( function( $ ) {

		tinymce.PluginManager.add(
			'convertkit_' + block.name,
			function( editor, url ) {

				// Add Button to Visual Editor Toolbar.
				editor.addButton(
					'convertkit_' + block.name,
					{
						title: 	block.title,
						image: 	url + '../../../../' + block.icon,
						cmd: 	'convertkit_' + block.name,
					}
				);

				// Load View when button clicked.
				editor.addCommand(
					'convertkit_' + block.name,
					function() {

						// Open the TinyMCE Modal.
						editor.windowManager.open(
							{
								id: 	'convertkit-modal-body',
								title: 	block.title,
								width: 	block.modal.width,
								height: block.modal.height,
								inline: 1,
								buttons:[],
							}
						);

						// Perform an AJAX call to load the modal's view.
						$.post(
							ajaxurl,
							{
								'action': 		'convertkit_admin_tinymce_output_modal',
								'nonce':  		convertkit_admin_tinymce.nonce,
								'editor_type':  'tinymce',
								'shortcode': 	block.name
							},
							function( response ) {

								// Inject HTML into modal.
								$( '#convertkit-modal-body-body' ).html( response );

								// Initialize color pickers.
								$( '.convertkit-color-picker' ).wpColorPicker();

							}
						);

					}
				);

			}
		);

	} )( jQuery );

}
