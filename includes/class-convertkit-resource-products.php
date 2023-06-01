<?php
/**
 * ConvertKit Products Resource class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Reads ConvertKit Products from the options table, and refreshes
 * ConvertKit Products data stored locally from the API.
 *
 * @since   2.0.0
 */
class ConvertKit_Resource_Products extends ConvertKit_Resource {

	/**
	 * Holds the Settings Key that stores site wide ConvertKit settings
	 *
	 * @var     string
	 */
	public $settings_name = 'convertkit_products';

	/**
	 * The type of resource
	 *
	 * @var     string
	 */
	public $type = 'products';

	/**
	 * Constructor.
	 *
	 * @since   2.0.0
	 *
	 * @param   bool|string $context    Context.
	 */
	public function __construct( $context = false ) {

		// Initialize the API if the API Key and Secret have been defined in the Plugin Settings.
		$settings = new ConvertKit_Settings();
		if ( $settings->has_api_key_and_secret() ) {
			$this->api = new ConvertKit_API(
				$settings->get_api_key(),
				$settings->get_api_secret(),
				$settings->debug_enabled(),
				$context
			);
		}

		// Call parent initialization function.
		parent::init();

	}

	/**
	 * Returns the commerce.js URL based on the account's ConvertKit Domain.
	 *
	 * @since   2.0.0
	 *
	 * @return  bool|string     false (if no products) | URL.
	 */
	public function get_commerce_js_url() {

		// Bail if no Products exist in this resource.
		if ( ! $this->exist() ) {
			return false;
		}

		// Fetch the first Product.
		$products = $this->get();
		$product  = reset( $products );

		// Parse the URL.
		$parsed_url = wp_parse_url( $product['url'] );

		// Bail if parsing the URL failed.
		if ( ! $parsed_url ) {
			return false;
		}

		// Bail if the scheme and host could not be obtained from the URL.
		if ( ! array_key_exists( 'scheme', $parsed_url ) || ! array_key_exists( 'host', $parsed_url ) ) {
			return false;
		}

		// Return commerce.js URL.
		return $parsed_url['scheme'] . '://' . $parsed_url['host'] . '/commerce.js';

	}

	/**
	 * Returns the HTML button markup for the given Product ID.
	 *
	 * @since   2.0.0
	 *
	 * @param   int    $id             Product ID.
	 * @param   string $button_text    Button Text.
	 * @param   array  $css_classes    CSS classes to apply to link (typically included when using Gutenberg).
	 * @param   array  $css_styles     CSS inline styles to apply to link (typically included when using Gutenberg).
	 * @param   bool   $return_as_span If true, returns a <span> instead of <a>. Useful for the block editor so that the element is interactible.
	 * @return  WP_Error|string         Button HTML
	 */
	public function get_html( $id, $button_text, $css_classes = array(), $css_styles = array(), $return_as_span = false ) {

		// Cast ID to integer.
		$id = absint( $id );

		// Bail if the resources are a WP_Error.
		if ( is_wp_error( $this->resources ) ) {
			return $this->resources;
		}

		// Bail if the resource doesn't exist.
		if ( ! isset( $this->resources[ $id ] ) ) {
			return new WP_Error(
				'convertkit_resource_products_get_html',
				sprintf(
					/* translators: ConvertKit Product ID */
					__( 'ConvertKit Product ID %s does not exist on ConvertKit.', 'convertkit' ),
					$id
				)
			);
		}

		// Build button HTML.
		$html = '<div class="convertkit-product">';

		if ( $return_as_span ) {
			$html .= '<span';
		} else {
			$html .= '<a href="' . esc_url( $this->resources[ $id ]['url'] ) . '"';
		}

		$html .= ' class="wp-block-button__link ' . implode( ' ', map_deep( $css_classes, 'sanitize_html_class' ) ) . '" style="' . implode( ';', map_deep( $css_styles, 'esc_attr' ) ) . '" data-commerce>';
		$html .= esc_html( $button_text );

		if ( $return_as_span ) {
			$html .= '</span>';
		} else {
			$html .= '</a>';
		}

		$html .= '</div>';

		// Return.
		return $html;

	}

}
