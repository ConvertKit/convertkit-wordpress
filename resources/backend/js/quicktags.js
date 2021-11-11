// convertkit_blocks is added to gutenberg.js by wp_localize_script().
for ( const block in convertkit_blocks ) {

	convertKitQuickTagRegister( convertkit_blocks[ block ] );

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

		QTags.addButton( 'convertkit-' + block.name, block.title, function() {

			// Perform an AJAX call to load the modal's view.
			$.post( 
	            ajaxurl,
	            {
	                'action': 	'convertkit_output_tinymce_modal',
	                'block': 	block.name
	            },
	            function( response ) {

	            	// Show Modal.
					convertKitQuickTagsModal.open();

	            	// Resize Modal so it's not full screen.
	            	$( 'div.convertkit-quicktags-modal div.media-modal.wp-core-ui' ).css({
	            		width: ( block.modal.width ) + 'px',
	            		height: ( block.modal.height + 20 ) + 'px' // Prevents a vertical scroll bar
	            	} );

	            	// Set Title.
					$( '#convertkit-quicktags-modal .media-frame-title h1' ).text( block.title );

            		// Inject HTML into modal.
	            	$( '#convertkit-quicktags-modal .media-frame-content' ).html( response );

	            	// Resize HTML height so it fills the modal.
	            	$( 'div.convertkit-quicktags-modal div.media-modal.wp-core-ui div.convertkit-vertical-tabbed-ui' ).css({
	            		height: ( block.modal.height - 50 ) + 'px' // -50px is for the footer buttons.
	            	} );

	            }
	        );

		} );

	} )( jQuery );

}