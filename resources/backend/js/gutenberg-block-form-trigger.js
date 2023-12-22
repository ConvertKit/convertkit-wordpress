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
		return convertKitGutenbergDisplayBlockNotice( block.name, block.gutenberg_help_description );
	}

	// A Form is specified.
	// Use the block's PHP's render() function by calling the ServerSideRender component.
	return wp.element.createElement(
		wp.serverSideRender,
		{
			block: 'convertkit/' + block.name,
			attributes: props.attributes,

			// This is only output in the Gutenberg editor, so must be slightly different from the inner class name used to
			// apply styles with i.e. convertkit-block.name.
			className: 'convertkit-ssr-' + block.name,
		}
	);

}
