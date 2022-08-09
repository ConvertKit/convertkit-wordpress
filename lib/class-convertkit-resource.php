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
	 * The API class
	 *
	 * @var     bool|ConvertKit_API
	 */
	public $api = false;

	/**
	 * The number of seconds resources are valid, before they should be
	 * fetched again from the API.
	 *
	 * @var     int
	 */
	public $cache_duration = YEAR_IN_SECONDS;

	/**
	 * How often to refresh this resource through WordPress' Cron.
	 * If false, won't be refreshed through WordPress' Cron
	 * If a string, must be a value from wp_get_schedules().
	 *
	 * @since   1.9.7.4
	 *
	 * @var     bool|string
	 */
	public $wp_cron_schedule = false;

	/**
	 * Holds the resources from the ConvertKit API
	 *
	 * @var     WP_Error|array
	 */
	public $resources = array();

	/**
	 * Timestamp for when the resources stored in the option database table
	 * were last queried from the API.
	 *
	 * @since   1.9.7.4
	 *
	 * @var     int
	 */
	public $last_queried = 0;

	/**
	 * Constructor.
	 *
	 * @since   1.9.6
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

		$this->init();

	}

	/**
	 * Initialization routine. Populate the resources array of e.g. forms, landing pages or tags,
	 * depending on whether resources are already cached, if the resources have expired etc.
	 *
	 * @since   1.9.7.4
	 */
	public function init() {

		// Get last query time and existing resources.
		$this->last_queried = get_option( $this->settings_name . '_last_queried' );
		$this->resources    = get_option( $this->settings_name );

		// If no last query time exists, refresh the resources now, which will set
		// a last query time.  This handles upgrades from < 1.9.7.4 where resources
		// would never expire.
		if ( ! $this->last_queried ) {
			$this->refresh();
			return;
		}

		// If the resources have expired, refresh them now.
		if ( time() > ( $this->last_queried + $this->cache_duration ) ) {
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
	 * Returns an individual resource by its ID.
	 *
	 * @since   1.9.7.7
	 *
	 * @param   int $id     Resource ID (Form, Tag, Sequence).
	 * @return  mixed           bool | array
	 */
	public function get_by_id( $id ) {

		foreach ( $this->get() as $resource ) {
			// If this resource's ID matches the ID we're looking for, return it.
			if ( (int) $resource['id'] === $id ) {
				return $resource;
			}
		}

		return false;

	}


	/**
	 * Returns a paginated subset of resources, including whether
	 * previous and next resources in the array exist.
	 *
	 * @since   1.9.7.6
	 *
	 * @param   int $page   Current Page.
	 * @param   int $per_page   Number of resources to return per page.
	 * @return  array
	 */
	public function get_paginated_subset( $page, $per_page ) {

		// Calculate the maximum value for $page.
		$total_pages = ( ( $per_page > 0 ) ? ceil( $this->count() / $per_page ) : 1 );

		// If $page exceeds the total number of possible pages, reduce it.
		if ( $page > $total_pages ) {
			$page = $total_pages;
		}

		// If $page is less than 1, set it to 1.
		if ( $page < 1 ) {
			$page = 1;
		}

		return array(
			// The subset of items based on the pagination.
			'items'         => array_slice( $this->resources, ( $page * $per_page ) - $per_page, $per_page ),

			// Sanitized inputs.
			'page'          => $page,
			'per_page'      => $per_page,

			// The total number of pages in the pagination.
			'total_pages'   => $total_pages,

			// If the request page is lower than the total number of pages in the pagination, there's a next page.
			'has_next_page' => ( ( $page < $total_pages ) ? true : false ),

			// If the request page is higher than 1, there's a previous page.
			'has_prev_page' => ( ( $page > 1 ) ? true : false ),
		);

	}

	/**
	 * Returns the number of resources.
	 *
	 * @since   1.9.7.6
	 *
	 * @return  int
	 */
	public function count() {

		return count( $this->resources );

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
	 * with a last queried timestamp.
	 *
	 * @since   1.9.6
	 *
	 * @return  bool|WP_Error|array
	 */
	public function refresh() {

		// Bail if no API class was defined.
		if ( ! $this->api ) {
			return false;
		}

		// Fetch resources.
		switch ( $this->type ) {
			case 'forms':
				$results = $this->api->get_forms();
				break;

			case 'landing_pages':
				$results = $this->api->get_landing_pages();
				break;

			case 'tags':
				$results = $this->api->get_tags();
				break;

			case 'sequences':
				$results = $this->api->get_sequences();
				break;

			case 'custom_fields':
				$results = $this->api->get_custom_fields();
				break;

			case 'posts':
				$results = $this->api->get_all_posts();
				break;

			default:
				$results = new WP_Error(
					'convertkit_resource_refresh_error',
					sprintf(
						'Resource type %s is not supported in ConvertKit_Resource class.',
						$this->type
					)
				);
				break;
		}

		// Bail if an error occured.
		if ( is_wp_error( $results ) ) {
			return $results;
		}

		// Define last query time now.
		$last_queried = time();

		// Store resources and their last query timestamp in the options table.
		// We don't use WordPress' Transients API (i.e. auto expiring options), because they're prone to being
		// flushed by some third party "optimization" Plugins. They're also not guaranteed to remain in the options
		// table for the amount of time specified; any expiry is a maximum, not a minimum.
		// We don't want to keep querying the ConvertKit API for a list of e.g. forms, tags that rarely change as
		// a result of transients not being honored, so storing them as options with a separate, persistent expiry
		// value is more reliable here.
		update_option( $this->settings_name, $results );
		update_option( $this->settings_name . '_last_queried', $last_queried );

		// Store resources and last queried time in class variables.
		$this->resources    = $results;
		$this->last_queried = $last_queried;

		// Return resources.
		return $results;

	}

	/**
	 * Schedules a WordPress Cron event to refresh this resource based on
	 * the resource's $wp_cron_schedule.
	 *
	 * @since   1.9.7.4
	 */
	public function schedule_cron_event() {

		// Bail if no cron schedule is defined for this resource.
		if ( ! $this->wp_cron_schedule ) {
			return;
		}

		// Bail if the event already exists; we don't need to schedule it again.
		if ( $this->get_cron_event() !== false ) {
			return;
		}

		// Schedule event, starting in an hour's time and recurring for the given $wp_cron_schedule.
		wp_schedule_event(
			strtotime( '+1 hour' ), // Start in an hour's time.
			$this->wp_cron_schedule, // Repeat based on the given schedule e.g. hourly.
			'convertkit_resource_refresh_' . $this->type // Hook name; see includes/cron-functions.php for function that listens to this hook.
		);

	}

	/**
	 * Unschedules a WordPress Cron event to refresh this resource.
	 *
	 * @since   1.9.7.4
	 */
	public function unschedule_cron_event() {

		wp_clear_scheduled_hook( 'convertkit_resource_refresh_' . $this->type );

	}

	/**
	 * Returns how often the WordPress Cron event will recur for (e.g. daily).
	 *
	 * Returns false if no schedule exists i.e. wp_schedule_event() has not been
	 * called or failed to register a scheduled event.
	 *
	 * @since   1.9.7.4
	 *
	 * @return  bool|string
	 */
	public function get_cron_event() {

		return wp_get_schedule( 'convertkit_resource_refresh_' . $this->type );

	}

	/**
	 * Deletes resources (forms, landing pages or tags) from the options table.
	 *
	 * @since   1.9.7.8
	 */
	public function delete() {

		delete_option( $this->settings_name );

	}

}
