/**
 * Form Block specific functions for Gutenberg.
 *
 * @since   1.9.6.5
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Custom callback function to render the ConvertKit Form Block preview in the Gutenberg Editor.
 *
 * @since 	1.9.6.5
 */
function convertKitGutenbergFormBlockRenderPreview( block, props ) {

	var form = block.fields.form.data.forms[ props.attributes.form ];

	// If no Form has been selected for display, return a prompt to tell the editor
	// what to do.
	if ( typeof form === 'undefined' ) {
		return wp.element.createElement(
			'div',
			{
				// convertkit-form-no-content class allows resources/backend/css/gutenberg-block-form.css
				// to apply styling/branding to the block.
				className: 'convertkit-' + block.name + ' convertkit-' + block.name + '-no-content'
			},
			block.gutenberg_help_description
		);
	}

	// If the Form is a <script> embed, use the SandBox because the Gutenberg editor
	// will not execute inline scripts.
	if ( typeof form.uid !== 'undefined' ) {
		// Determine the Form's format (inline, sticky bar etc).
		// This isn't available in API responses prior to Feb 2022, so check the Form object contains this property.
		var format    = ( ( typeof form.format !== 'undefined' ) ? form.format : 'inline' ),
			html      = '<script async data-uid="' + form.uid + '" src="' + form.embed_js + '"></script>',
			className = [ 'convertkit-' + block.name ];

		// If the format isn't inline, define the Gutenberg Block preview's HTML to explain why the Form won't be
		// rendered in the editor i.e because it is a Sticky Bar that is displayed at the top/bottom of the document.
		// Also add a CSS class to the block in the editor, so that resources/backend/css/gutenberg-block-form.css
		// can apply styling/branding to the block.
		switch ( format ) {
			case 'modal':
				html = block.i18n.gutenberg_form_modal.replace( '%s', form.name );
				className.push( 'convertkit-' + block.name + '-no-content' );
				break;

			case 'slide in':
				html = block.i18n.gutenberg_form_slide_in.replace( '%s', form.name );
				className.push( 'convertkit-' + block.name + '-no-content' );
				break;

			case 'sticky bar':
				html = block.i18n.gutenberg_form_sticky_bar.replace( '%s', form.name );
				className.push( 'convertkit-' + block.name + '-no-content' );
				break;
		}

		// Return SandBox.
		return wp.element.createElement(
			'div',
			{
				className: className.join( ' ' )
			},
			wp.components.SandBox(
				{
					html: html,
					title: block.name,
					styles: [
					'body{font-family:-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; text-align:center;}',
					],
				}
			)
		);
	}

	// This is a Legacy Form.
	// Use the block's PHP's render() function by calling the ServerSideRender component.
	return wp.element.createElement(
		wp.components.ServerSideRender,
		{
			block: 'convertkit/' + block.name,
			attributes: props.attributes,
			className: 'convertkit-' + block.name,
		}
	);

}
