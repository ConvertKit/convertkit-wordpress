/**
 * Broadcasts Block specific functions for Gutenberg.
 *
 * @since   2.0.1
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Custom callback function to render the ConvertKit Broadcasts Block preview in the Gutenberg Editor.
 *
 * @since 	2.0.1
 */
function convertKitGutenbergBroadcastsBlockRenderPreview( block, props ) {

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
