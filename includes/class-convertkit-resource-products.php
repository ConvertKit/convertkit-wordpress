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

}
