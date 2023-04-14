/**
 * Form Trigger Block specific functions for Gutenberg.
 *
 * @since   2.2.0
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Custom callback function to render the ConvertKit Form Trigger Block preview in the Gutenberg Editor.
 *
 * @since 	2.2.0
 */
function convertKitGutenbergFormTriggerBlockRenderPreview( block, props ) {

	// If no Form has been selected for display, return a prompt to tell the editor
	// what to do.
	if ( props.attributes.form === '' ) {
		return wp.element.createElement(
			'div',
			{
				// convertkit-no-content class allows resources/backend/css/gutenberg.css
				// to apply styling/branding to the block.
				className: 'convertkit-' + block.name + ' convertkit-no-content'
			},
			block.gutenberg_help_description
		);
	}

	// A Form is specified.
	// Use the block's PHP's render() function by calling the ServerSideRender component.
	return wp.element.createElement(
		wp.serverSideRender,
		{
			block: 'convertkit/' + block.name,
			attributes: props.attributes,
			className: 'convertkit-' + block.name,
		}
	);

}
