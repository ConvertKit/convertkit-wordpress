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

	// If no API Key has been defined in the Plugin, return a prompt to tell the editor
	// what to do.
	if ( ! block.has_api_key ) {
		return convertKitGutenbergDisplayBlockNoticeWithLink(
			block.name,
			block.no_api_key.notice,
			block.no_api_key.link,
			block.no_api_key.link_text
		);
	}

	// If no Forms exist in ConvertKit, return a prompt to tell the editor
	// what to do.
	if ( ! block.has_resources ) {
		return convertKitGutenbergDisplayBlockNoticeWithLink(
			block.name,
			block.no_resources.notice,
			block.no_resources.link,
			block.no_resources.link_text
		);
	}

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
			className: 'convertkit-' + block.name,
		}
	);

}
