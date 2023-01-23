<?php
/**
 * ConvertKit Posts Resource class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Reads ConvertKit Posts from the options table, and refreshes
 * ConvertKit Posts data stored locally from the API.
 *
 * @since   1.9.7.4
 */
class ConvertKit_Resource_Posts extends ConvertKit_Resource {

	/**
	 * Holds the Settings Key that stores site wide ConvertKit settings
	 *
	 * @var     string
	 */
	public $settings_name = 'convertkit_posts';

	/**
	 * The type of resource
	 *
	 * @var     string
	 */
	public $type = 'posts';

	/**
	 * The number of seconds resources are valid, before they should be
	 * fetched again from the API.
	 *
	 * @var     int
	 */
	public $cache_duration = DAY_IN_SECONDS;

	/**
	 * How often to refresh this resource through WordPress' Cron.
	 * If false, won't be refreshed through WordPress' Cron
	 * If a string, must be a value from wp_get_schedules().
	 *
	 * @since   1.9.7.4
	 *
	 * @var     bool|string
	 */
	public $wp_cron_schedule = 'hourly';

	/**
	 * The key to use when alphabetically sorting resources.
	 *
	 * @since   2.0.8
	 *
	 * @var     string
	 */
	public $order_by = 'published_at';

	/**
	 * The order to return resources.
	 *
	 * @since   2.0.8
	 *
	 * @var     string
	 */
	public $order = 'desc';

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
