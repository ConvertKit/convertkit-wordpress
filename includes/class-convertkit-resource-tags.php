<?php
/**
 * ConvertKit Tags Resource class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Reads ConvertKit Tags from the options table, and refreshes
 * ConvertKit Tags data stored locally from the API.
 *
 * @since   1.9.6
 */
class ConvertKit_Resource_Tags extends ConvertKit_Resource {

	/**
	 * Holds the Settings Key that stores site wide ConvertKit settings
	 *
	 * @var     string
	 */
	public $settings_name = 'convertkit_tags';

	/**
	 * The type of resource
	 *
	 * @var     string
	 */
	public $type = 'tags';

	/**
	 * Constructor.
	 *
	 * @since   1.9.8.4
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

}
