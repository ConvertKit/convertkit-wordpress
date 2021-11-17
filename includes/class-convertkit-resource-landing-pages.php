<?php
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
	 * Holds the forms from the ConvertKit API
	 *
	 * @var     array
	 */
	public $resources = array();

	/**
	 * Returns the HTML/JS markup for the given Landing Page ID
	 *
	 * @since   1.9.6
	 *
	 * @param   int $id     Form ID
	 * @return  mixed           WP_Error | string
	 */
	public function get_html( $id ) {

		// Cast ID to integer.
		$id = absint( $id );

		// Bail if the resource doesn't exist
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

		// Get markup
		$api = new ConvertKit_API();
		return $api->get_landing_page_html( $this->resources[ $id ]['embed_url'] );

	}

}
