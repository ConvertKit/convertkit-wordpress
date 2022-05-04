<?php
/**
 * ConvertKit Landing Pages Resource class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Reads ConvertKit Landing Pages from the options table, and refreshes
 * ConvertKit Landing Pages data stored locally from the API.
 *
 * @since   1.9.6
 */
class ConvertKit_Resource_Landing_Pages extends ConvertKit_Resource {

	/**
	 * Holds the Settings Key that stores site wide ConvertKit settings
	 *
	 * @var     string
	 */
	public $settings_name = 'convertkit_landing_pages';

	/**
	 * The type of resource
	 *
	 * @var     string
	 */
	public $type = 'landing_pages';

	/**
	 * Returns the HTML/JS markup for the given Landing Page ID
	 *
	 * @since   1.9.6
	 *
	 * @param   mixed $id     Landing Page ID | Legacy Landing Page URL.
	 * @return  WP_Error|string
	 */
	public function get_html( $id ) {

		// Setup API.
		$api = new ConvertKit_API();

		// If the ID is a URL, this is a Legacy Landing Page defined for use on this Page
		// in a Plugin version < 1.9.6.
		// 1.9.6+ always uses a Landing Page ID.
		if ( strstr( $id, 'http' ) ) {
			// Return Legacy Landing Page HTML for url property.
			return $api->get_landing_page_html( $id );
		}

		// Cast ID to integer.
		$id = absint( $id );

		// Bail if the resources are a WP_Error.
		if ( is_wp_error( $this->resources ) ) {
			return $this->resources;
		}

		// Bail if the resource doesn't exist.
		if ( ! isset( $this->resources[ $id ] ) ) {
			return new WP_Error(
				'convertkit_resource_landing_pages_get_html',
				sprintf(
					/* translators: ConvertKit Landing Page ID */
					__( 'ConvertKit Landing Page ID %s does not exist on ConvertKit.', 'convertkit' ),
					$id
				)
			);
		}

		// If the resource has a 'url' property, this is a Legacy Landing Page, and the 'url' should be used.
		if ( isset( $this->resources[ $id ]['url'] ) ) {
			// Return Legacy Landing Page HTML for url property.
			return $api->get_landing_page_html( $this->resources[ $id ]['url'] );
		}

		// Return Landing Page HTML for embed_url property.
		return $api->get_landing_page_html( $this->resources[ $id ]['embed_url'] );

	}

}
