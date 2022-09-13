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

	var product = block.fields.product.data.products[ props.attributes.product ];

	// If no Product has been selected for display, return a prompt to tell the editor
	// what to do.
	if ( typeof product === 'undefined' ) {
		return wp.element.createElement(
			'div',
			{
				// convertkit-product-no-content class allows resources/backend/css/gutenberg-block-product.css
				// to apply styling/branding to the block.
				className: 'convertkit-' + block.name + ' convertkit-' + block.name + '-no-content'
			},
			block.gutenberg_help_description
		);
	}

	// A Product is specified.
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
