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
 * @since   2.2.7
 */
class ConvertKit_Resource_Creator_Network_Recommendations extends ConvertKit_Resource {

	/**
	 * Holds the Settings Key that stores site wide ConvertKit settings
	 *
	 * @since   2.2.7
	 *
	 * @var     string
	 */
	public $settings_name = 'convertkit_creator_network_recommendations';

	/**
	 * Constructor.
	 *
	 * @since   2.2.7
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
	 * Queries the API to determine whether the Creator Network Recommendation feature is enabled
	 * on the ConvertKit account.
	 *
	 * If enabled, caches the script to use on the frontend site.
	 *
	 * @since   2.2.7
	 *
	 * @return  bool
	 */
	public function enabled() {

		if ( ! $this->api ) {
			return false;
		}

		// Sanity check that we're using the ConvertKit WordPress Libraries 1.3.7 or higher.
		// If another ConvertKit Plugin is active and out of date, its libraries might
		// be loaded that don't have this method.
		if ( ! method_exists( $this->api, 'recommendations_script' ) ) {
			return false;
		}

		// Get script from API.
		$result = $this->api->recommendations_script();

		// Bail if an error occured.
		if ( is_wp_error( $result ) ) {
			delete_option( $this->settings_name );
			return false;
		}

		// Bail if not enabled.
		if ( ! $result['enabled'] ) {
			delete_option( $this->settings_name );
			return false;
		}

		// Store script URL, as Creator Network Recommendations are available on this account.
		update_option( $this->settings_name, $result['embed_js'] );

		return true;

	}

	/**
	 *
	 * Returns the embed script, or false if no script exists i.e. Creator Network Recommendations
	 * are disabled.
	 *
	 * Overrides the get() function of the ConvertKit_Resource class as we store a string
	 * containing the embed script, not an array of data.
	 *
	 * @since   2.2.7
	 *
	 * @return  bool|string|array
	 */
	public function get() {

		return get_option( $this->settings_name );

	}

}
