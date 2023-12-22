/**
 * Handles the Insert and Cancel events on TinyMCE and QuickTag Modals
 *
 * @since   1.9.6
 *
 * @package ConvertKit
 * @author ConvertKit
 */

jQuery( document ).ready(
	function ( $ ) {

		// Cancel.
		$( 'body' ).on(
			'click',
			'#convertkit-modal-body div.mce-cancel button, #convertkit-quicktags-modal .media-toolbar .media-toolbar-secondary button.cancel',
			function ( e ) {

				// TinyMCE.
				if ( typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor && ! tinyMCE.activeEditor.isHidden() ) {
					tinymce.activeEditor.windowManager.close();
					return;
				}

				// Text Editor.
				// Close the QuickTags modal.
				convertKitQuickTagsModal.close();

			}
		);

		// Insert.
		$( 'body' ).on(
			'click',
			'#convertkit-modal-body div.mce-insert button, #convertkit-quicktags-modal .media-toolbar .media-toolbar-primary button.button-primary',
			function ( e ) {

				// Prevent default action.
				e.preventDefault();

				// Get containing form.
				let form = $( 'form.convertkit-tinymce-popup' );

				// Build Shortcode.
				let shortcode  = '[' + $( 'input[name="shortcode"]', $( form ) ).val(),
				shortcodeClose = ( $( 'input[name="close_shortcode"]', $( form ) ).val() === '1' ? true : false );

				$( 'input, select', $( form ) ).each(
					function ( i ) {
						// Skip if no data-shortcode attribute.
						if ( typeof $( this ).data( 'shortcode' ) === 'undefined' ) {
							return true;
						}

						// Skip if the value is empty.
						if ( ! $( this ).val() ) {
							return true;
						}
						if ( $( this ).val().length === 0 ) {
							return true;
						}

						// Get shortcode attribute.
						let key = $( this ).data( 'shortcode' ),
						trim    = ( $( this ).data( 'trim' ) === '0' ? false : true ),
						val     = $( this ).val();

						// Skip if the shortcode is empty.
						if ( ! key.length ) {
							return true;
						}

						// Trim the value, unless the shortcode attribute disables string trimming.
						if ( trim ) {
							val = val.trim();
						}

						// Append attribute and value to shortcode string.
						shortcode += ' ' + key.trim() + '="' + val + '"';
					}
				);

				// Close Shortcode.
				shortcode += ']';

				// If the shortcode includes a closing element, append it now.
				if ( shortcodeClose ) {
					shortcode += '[/' + $( 'input[name="shortcode"]', $( form ) ).val() + ']';
				}

				// Depending on the editor type, insert the shortcode.
				switch ( $( 'input[name="editor_type"]', $( form ) ).val() ) {
					case 'tinymce':
						// Sanity check that a Visual editor exists and is active.
						if ( typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor && ! tinyMCE.activeEditor.isHidden() ) {
							// Insert into editor.
							tinyMCE.activeEditor.execCommand( 'mceReplaceContent', false, shortcode );

							// Close modal.
							tinyMCE.activeEditor.windowManager.close();
						}
						break;

					case 'quicktags':
						// Insert into editor.
						QTags.insertContent( shortcode );

						// Close modal.
						convertKitQuickTagsModal.close();
						break;
				}
			}
		);

	}
);


// QuickTags: Setup Backbone Modal and Template.
if ( typeof wp !== 'undefined' && typeof wp.media !== 'undefined' ) {

	// Declared globally, as used in this file and quicktags.js.
	var convertKitQuickTagsModal          = new wp.media.view.Modal(
		{
			controller: { trigger: function () {} },
			className: 'convertkit-quicktags-modal'
		}
	);
	const convertKitQuickTagsModalContent = wp.Backbone.View.extend(
		{
			template: wp.template( 'convertkit-quicktags-modal' )
		}
	);
	convertKitQuickTagsModal.content( new convertKitQuickTagsModalContent() );

	/**
	 * Resets the content of the convertKitQuickTagsModal when closing.
	 *
	 * If this isn't performed, switching from Text to Visual Editor for the same shortcode results
	 * code picking up data from the QuickTags modal, not the TinyMCE one, due to this 'stale'
	 * modal remaining in the DOM, resulting in e.g. the tabbed UI not loading correctly.
	 */
	convertKitQuickTagsModal.on(
		'close',
		function ( e ) {
			this.content( new convertKitQuickTagsModalContent() );
		}
	);

}
