/**
 * Product Block specific functions for Gutenberg.
 *
 * @since   1.9.6.5
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Custom callback function to render the ConvertKit Product Block preview in the Gutenberg Editor.
 *
 * @since 	1.9.8.5
 */
function convertKitGutenbergProductBlockRenderPreview( block, props ) {

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

	// If no Products exist in ConvertKit, return a prompt to tell the editor
	// what to do.
	if ( ! block.has_resources ) {
		return convertKitGutenbergDisplayBlockNoticeWithLink(
			block.name,
			block.no_resources.notice,
			block.no_resources.link,
			block.no_resources.link_text
		);
	}

	// If no Product has been selected for display, return a prompt to tell the editor
	// what to do.
	if ( props.attributes.product === '' ) {
		return convertKitGutenbergDisplayBlockNotice( block.name, block.gutenberg_help_description );
	}

	// A Product is specified.
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
