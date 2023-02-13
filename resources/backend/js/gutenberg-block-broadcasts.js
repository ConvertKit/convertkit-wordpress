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

	// If no Broadcasts exist, return a prompt to tell the editor what to do.
	if ( ! block.has_posts ) {
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

	// A Product is specified.
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
