<?php
/**
 * ConvertKit Resource class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Abstract class defining variables and functions for a ConvertKit API Resource
 * (forms, landing pages, tags).
 *
 * @since   1.9.6
 */
class ConvertKit_Resource {

	/**
	 * Constructor. Populate the resources array of e.g. forms, landing pages or tags.
	 *
	 * @since   1.9.6
	 */
	public function __construct() {

		// Get resources from options.
		$resources = get_option( $this->settings_name );

		// If resources exist in the options table, use them.
		if ( is_array( $resources ) ) {
			$this->resources = $resources;
		} else {
			// No options exist in the options table. Fetch them from the API, storing
			// them in the options table.
			$this->resources = $this->refresh();
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

		if ( is_wp_error( $this->resources ) ) {
			return false;
		}

		if ( is_null( $this->resources ) ) {
			return false;
		}

		return ( count( $this->resources ) ? true : false );

	}

	/**
	 * Fetches resources (forms, landing pages or tags) from the API, storing them in the options table.
	 *
	 * @since   1.9.6
	 *
	 * @return  mixed           WP_Error | array
	 */
	public function refresh() {

		// Bail if the API Key and Secret hasn't been defined in the Plugin Settings.
		$settings = new ConvertKit_Settings();
		if ( ! $settings->has_api_key_and_secret() ) {
			return;
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
		}

		// Bail if an error occured.
		if ( is_wp_error( $results ) ) {
			return $results;
		}

		// Update options table data.
		update_option( $this->settings_name, $results );

		// Store in resource class.
		$this->resources = $results;

		return $results;

	}

	/**
	 * Sorts the given array of resources by name.
	 *
	 * @since   1.9.6
	 *
	 * @param   array $data   Forms or Landing Pages from API.
	 * @return  array           Sorted Forms or Landing Pages.
	 */
	private function sort_alphabetically( $data ) {

		return usort(
			$data,
			function( $a, $b ) {

				return strcmp( $a['name'], $b['name'] );

			}
		);

	}

}
