<?php
/**
 * ConvertKit Usage Tracking class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * If usage tracking is enabled in the Plugin's settings:
 * - Registers items to track usage of,
 * - Sends data once a week to ConvertKit
 *
 * @since   1.9.8.6
 */
class ConvertKit_Usage_Tracking {

	/**
	 * Holds the option table key to stores usage tracking data
	 *
	 * @since 	1.9.8.6
	 *
	 * @var     string
	 */
	const USAGE_TRACKING_NAME = '_wp_convertkit_usage_tracking';

	/**
	 * How often to refresh this resource through WordPress' Cron.
	 * If false, won't be refreshed through WordPress' Cron
	 * If a string, must be a value from wp_get_schedules().
	 *
	 * @since   1.9.8.6
	 *
	 * @var     bool|string
	 */
	private $wp_cron_schedule = 'weekly';

	/**
	 * The name of the Cron event.
	 * 
	 * @since 	1.9.8.6
	 * 
	 * @var 	string
	 */
	private $wp_cron_event_name = 'convertkit_send_usage_tracking_data';

	/**
	 * Holds the Usage Tracking data
	 *
	 * @since 	1.9.8.6
	 *
	 * @var     array
	 */
	private $usage_tracking = array();

	/**
	 * Constructor. Reads usage tracking from options table, falling back to defaults
	 * if no usage tracking exists.
	 *
	 * @since   1.9.8.6
	 */
	public function __construct() {

		// Fetch Usage Tracking data.
		$this->usage_tracking = get_option( self::USAGE_TRACKING_NAME );

		// If no Usage Tracking data exists, define a blank array.
		if ( ! $this->usage_tracking ) {
			$this->usage_tracking = array();
		}

	}

	/**
	 * Returns all Usage Tracking data.
	 *
	 * @since   1.9.8.6
	 *
	 * @return  array
	 */
	public function get() {

		return $this->usage_tracking;

	}

	/**
	 * Sets the given value for the given feature.
	 * 
	 * @since 	1.9.8.6
	 * 
	 * @param 	string 			$feature 	Feature.
	 * @param 	string|int|bool $value 		Value.
	 */
	public function set( $feature, $value ) {

		// Bail if Usage Tracking is disabled.
		$settings = new ConvertKit_Settings;
		if ( ! $settings->usage_tracking_enabled() ) {
			return;
		}

		// Store feature's value in usage tracking data.
		$this->usage_tracking[ $feature ] = $value;

		// Store in database.
		update_option( self::USAGE_TRACKING_NAME, $this->usage_tracking );

	}

	/**
	 * Sends all Usage Tracking data.
	 * 
	 * @since 	1.9.8.6
	 */
	public function send() {

		// @TODO.

	}

	/**
	 * Deletes all Usage Tracking data.
	 * 
	 * @since 	1.9.8.6
	 */
	public function delete() {

		delete_option( self::USAGE_TRACKING_NAME );

	}

	/**
	 * Schedules a WordPress Cron event to refresh this resource based on
	 * the resource's $wp_cron_schedule.
	 *
	 * @since   1.9.8.6
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

		// Schedule event, starting in a week's time and recurring for the given $wp_cron_schedule.
		wp_schedule_event(
			strtotime( '+1 week' ), // Start in a week's time.
			$this->wp_cron_schedule, // Repeat based on the given schedule e.g. weekly.
			$this->wp_cron_event_name // Hook name; see includes/cron-functions.php for function that listens to this hook.
		);

	}

	/**
	 * Unschedules a WordPress Cron event to refresh this resource.
	 *
	 * @since   1.9.8.6
	 */
	public function unschedule_cron_event() {

		wp_clear_scheduled_hook( $this->wp_cron_event_name );

	}

	/**
	 * Returns how often the WordPress Cron event will recur for (e.g. daily).
	 *
	 * Returns false if no schedule exists i.e. wp_schedule_event() has not been
	 * called or failed to register a scheduled event.
	 *
	 * @since   1.9.8.6
	 *
	 * @return  bool|string
	 */
	public function get_cron_event() {

		return wp_get_schedule( $this->wp_cron_event_name );

	}

}
