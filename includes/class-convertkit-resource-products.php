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
 * @since   1.9.8.5
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
	 * @since   1.9.8.5
	 */
	public function __construct() {

		// Initialize the API if the API Key and Secret have been defined in the Plugin Settings.
		$settings = new ConvertKit_Settings();
		if ( $settings->has_api_key_and_secret() ) {
			$this->api = new ConvertKit_API(
				$settings->get_api_key(),
				$settings->get_api_secret(),
				$settings->debug_enabled()
			);
		}

		// Call parent initialization function.
		parent::init();

	}

	/**
	 * Returns the HTML button markup for the given Product ID.
	 *
	 * @since   1.9.8.5
	 *
	 * @param   int    $id             Product ID.
	 * @param   string $button_text    Button Text.
	 * @param   array  $css_classes    CSS classes to apply to link (typically included when using Gutenberg).
	 * @param   array  $css_styles     CSS inline styles to apply to link (typically included when using Gutenberg).
	 * @return  WP_Error|string         Button HTML
	 */
	public function get_html( $id, $button_text, $css_classes = array(), $css_styles = array() ) {

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
		$html  = '<div class="wp-block-button">';
		$html .= '<a href="' . $this->resources[ $id ]['url'] . '" class="wp-block-button__link ' . esc_attr( implode( ' ', $css_classes ) ) . '" style="' . implode( ';', $css_styles ) . '" data-commerce>';
		$html .= esc_html( $button_text );
		$html .= '</a>';
		$html .= '</div>';

		// Return.
		return $html;

	}

}
