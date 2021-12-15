/**
 * Handles the Insert and Cancel events on TinyMCE and QuickTag Modals
 *
 * @since   1.9.6
 *
 * @package ConvertKit
 * @author ConvertKit
 */

jQuery( document ).ready(
	function( $ ) {

		// Cancel.
		$( 'body' ).on(
			'click',
			'form.convertkit-tinymce-popup button.close',
			function( e ) {

				// TinyMCE.
				if ( typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor && ! tinyMCE.activeEditor.isHidden() ) {
					tinymce.activeEditor.windowManager.close();
					return;
				}

				// Text Editor.
				convertKitQuickTagsModal.close();

			}
		);

		// Insert.
		$( 'body' ).on(
			'click',
			'form.convertkit-tinymce-popup div.buttons input[type=button]',
			function( e ) {

				// Prevent default action.
				e.preventDefault();

				// Get containing form.
				var form = $( this ).closest( 'form.convertkit-tinymce-popup' );

				// Build Shortcode.
				var shortcode  = '[' + $( 'input[name="shortcode"]', $( form ) ).val(),
				shortcodeClose = ( $( 'input[name="close_shortcode"]', $( form ) ).val() == '1' ? true : false );

				$( 'input, select', $( form ) ).each(
					function( i ) {
						// Skip if no data-shortcode attribute.
						if ( typeof $( this ).data( 'shortcode' ) === 'undefined' ) {
							return true;
						}

						// Skip if the value is empty.
						if ( ! $( this ).val() ) {
							return true;
						}
						if ( $( this ).val().length == 0 ) {
							return true;
						}

						// Get shortcode attribute.
						var key = $( this ).data( 'shortcode' ),
						trim    = ( $( this ).data( 'trim' ) == '0' ? false : true ),
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

				/**
				 * Finish building the link, and insert it, depending on whether we were initialized from
				 * the Visual Editor or Text Editor.
				 */
				if ( typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor && ! tinyMCE.activeEditor.isHidden() ) {
					// Insert into editor.
					tinyMCE.activeEditor.execCommand( 'mceReplaceContent', false, shortcode );

					// Close modal.
					tinyMCE.activeEditor.windowManager.close();

					// Done.
					return;
				}

				// Text Editor.
				if ( typeof QTags !== 'undefined' ) {
					// Insert into editor.
					QTags.insertContent( shortcode );

					// Close modal.
					convertKitQuickTagsModal.close();

					// Done.
					return;
				}

			}
		);

	}
);

// QuickTags: Setup Backbone Modal and Template.
if ( typeof wp !== 'undefined' && typeof wp.media !== 'undefined' ) {
	var convertKitQuickTagsModal        = new wp.media.view.Modal(
		{
			controller: { trigger: function() {} },
			className: 'convertkit-quicktags-modal'
		}
	);
	var convertKitQuickTagsModalContent = wp.Backbone.View.extend(
		{
			template: wp.template( 'convertkit-quicktags-modal' )
		}
	);
	convertKitQuickTagsModal.content( new convertKitQuickTagsModalContent() );
}
