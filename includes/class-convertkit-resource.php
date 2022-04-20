<?php
/**
 * ConvertKit Resource class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Abstract class defining variables and functions for a ConvertKit API Resource
 * (forms, landing pages, tags), which is stored in the WordPress option table.
 *
 * @since   1.9.6
 */
class ConvertKit_Resource {

	/**
	 * Holds the key that stores the resources in the option database table.
	 *
	 * @var     string
	 */
	public $settings_name = '';

	/**
	 * The type of resource
	 *
	 * @var     string
	 */
	public $type = '';

	/**
	 * The number of seconds resources are valid, before they should be
	 * fetched again from the API.
	 *
	 * @var     int
	 */
	public $cache_for = YEAR_IN_SECONDS;

	/**
	 * Holds the resources from the ConvertKit API
	 *
	 * @var     WP_Error|array
	 */
	public $resources = array();

	/**
	 * Timestamp for when the resources stored in the option database table
	 * are stale and need to be refreshed by querying the API to fetch the latest
	 * resources.
	 *
	 * @since   1.9.7.4
	 *
	 * @var     int
	 */
	public $expiry = YEAR_IN_SECONDS;

	/**
	 * Constructor.
	 *
	 * @since   1.9.6
	 */
	public function __construct() {

		$this->init();

	}

	/**
	 * Initialization routine. Populate the resources array of e.g. forms, landing pages or tags,
	 * depending on whether resources are already cached, if the resources have expired etc.
	 *
	 * @since   1.9.7.4
	 */
	public function init() {

		// Get expiry time and existing resources.
		$this->expiry    = get_option( $this->settings_name . '_expiry' );
		$this->resources = get_option( $this->settings_name );

		// If no expiry time exists, refresh the resources now, which will set
		// an expiry time.  This handles upgrades from < 1.9.7.4 where resources
		// would never expire.
		if ( ! $this->expiry ) {
			$this->refresh();
			return;
		}

		// If no resources exist, refresh them now.
		if ( ! $this->resources ) {
			$this->refresh();
			return;
		}

		// If the resources have expired, refresh them now.
		if ( time() > $this->expiry ) {
			$this->refresh();
			return;
		}

	}

	/**
	 * Returns all resources.
	 *
	 * @since   1.9.6
	 *
	 * @return  array
	 */
	public function get() {

		return $this->resources;

	}

	/**
	 * Returns whether any resources exist in the options table.
	 *
	 * @since   1.9.6
	 *
	 * @return  bool
	 */
	public function exist() {

		if ( $this->resources === false ) { // @phpstan-ignore-line.
			return false;
		}

		if ( is_wp_error( $this->resources ) ) {
			return false;
		}

		if ( is_null( $this->resources ) ) {
			return false;
		}

		return ( count( $this->resources ) ? true : false );

	}

	/**
	 * Fetches resources (forms, landing pages or tags) from the API, storing them in the options table
	 * with an expiry timestamp.
	 *
	 * @since   1.9.6
	 *
	 * @return  bool|WP_Error|array
	 */
	public function refresh() {

		// Bail if the API Key and Secret hasn't been defined in the Plugin Settings.
		$settings = new ConvertKit_Settings();
		if ( ! $settings->has_api_key_and_secret() ) {
			return false;
		}

		// Initialize the API.
		$api = new ConvertKit_API( $settings->get_api_key(), $settings->get_api_secret(), $settings->debug_enabled() );

		// Fetch resources.
		switch ( $this->type ) {
			case 'forms':
				$results = $api->get_forms();
				break;

			case 'landing_pages':
				$results = $api->get_landing_pages();
				break;

			case 'tags':
				$results = $api->get_tags();
				break;

			case 'posts':
				$results = $api->get_posts();
				break;

			default:
				$results = new WP_Error(
					'convertkit_resource_refresh_error',
					sprintf(
						/* translators: Resource Type */
						__( 'Resource type %s is not supported in ConvertKit_Resource class.', 'convertkit' ),
						$this->type
					)
				);
				break;
		}

		// Bail if an error occured.
		if ( is_wp_error( $results ) ) {
			return $results;
		}

		// Define expiration for these resources.
		$expiry = time() + $this->cache_for;

		// Store resources and their expiry in the options table.
		// We don't use WordPress' Transients API (i.e. auto expiring options), because they're prone to being
		// flushed by some third party "optimization" Plugins. They're also not guaranteed to remain in the options
		// table for the amount of time specified; any expiry is a maximum, not a minimum.
		// We don't want to keep querying the ConvertKit API for a list of e.g. forms, tags that rarely change as
		// a result of transients not being honored, so storing them as options with a separate, persistent expiry
		// value is more reliable here.
		update_option( $this->settings_name, $results );
		update_option( $this->settings_name . '_expiry', $expiry );

		// Store resources and expiry in class variables.
		$this->resources = $results;
		$this->expiry    = $expiry;

		// Return resources.
		return $results;

	}

}
