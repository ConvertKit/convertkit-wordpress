<?php
/**
 * ConvertKit Forms Resource class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Reads ConvertKit Forms from the options table, and refreshes
 * ConvertKit Forms data stored locally from the API.
 *
 * @since   1.9.6
 */
class ConvertKit_Resource_Forms extends ConvertKit_Resource {

	/**
	 * Holds the Settings Key that stores site wide ConvertKit settings
	 *
	 * @var     string
	 */
	public $settings_name = 'convertkit_forms';

	/**
	 * The type of resource
	 *
	 * @var     string
	 */
	public $type = 'forms';

	/**
	 * Returns the HTML/JS markup for the given Form ID.
	 *
	 * Legacy Forms will return HTML.
	 * Current Forms will return a <script> embed string.
	 *
	 * @since   1.9.6
	 *
	 * @param   int $id     Form ID.
	 * @return  WP_Error|string
	 */
	public function get_html( $id ) {

		// Cast ID to integer.
		$id = absint( $id );

		// Bail if the resources are a WP_Error.
		if ( is_wp_error( $this->resources ) ) {
			return $this->resources;
		}

		// Bail if the resource doesn't exist.
		if ( ! isset( $this->resources[ $id ] ) ) {
			return new WP_Error(
				'convertkit_resource_forms_get_html',
				sprintf(
					/* translators: ConvertKit Form ID */
					__( 'ConvertKit Form ID %s does not exist on ConvertKit.', 'convertkit' ),
					$id
				)
			);
		}

		// If no uid is present in the Form API data, this is a legacy form that's served by directly fetching the HTML
		// from forms.convertkit.com.
		if ( ! isset( $this->resources[ $id ]['uid'] ) ) {
			// Initialize Settings.
			$settings = new ConvertKit_Settings();

			// Bail if no API Key is specified in the Plugin Settings.
			if ( ! $settings->has_api_key() ) {
				return new WP_Error(
					'convertkit_resource_forms_get_html',
					__( 'ConvertKit Legacy Form could not be fetched as no API Key specified in Plugin Settings', 'convertkit' )
				);
			}

			// Initialize the API.
			$api = new ConvertKit_API( $settings->get_api_key(), $settings->get_api_secret(), $settings->debug_enabled() );

			// Return Legacy Form HTML.
			return $api->get_form_html( $id );
		}

		// If here, return Form <script> embed.
		return '<script async data-uid="' . $this->resources[ $id ]['uid'] . '" src="' . $this->resources[ $id ]['embed_js'] . '"></script>';

	}

}
