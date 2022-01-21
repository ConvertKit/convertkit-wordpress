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
				className: 'convertkit-' + block.name
			},
			block.gutenberg_help_description
		);
	}

	// If the Form is a <script> embed, use the SandBox because the Gutenberg editor
	// will not execute inline scripts.
	if ( typeof form.uid !== 'undefined' ) {
		return wp.element.createElement(
			'div',
			{
				className: 'convertkit-' + block.name
			},
			wp.components.SandBox(
				{
					html: '<script async data-uid="' + form.uid + '" src="' + form.embed_js + '"></script>',
					title: block.name,
					styles: [
					'body{font-family:-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;}',
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
