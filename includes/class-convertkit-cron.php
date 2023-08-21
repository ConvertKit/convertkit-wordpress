<?php
/**
 * ConvertKit Cron class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Provides functionality for manually running WordPress Cron events registered
 * by this Plugin.
 *
 * @since   2.2.9
 */
class ConvertKit_Cron {

	/**
	 * Runs an existing scheduled event immediately through WordPress' Cron system.
	 *
	 * The scheduled event will still run as its original date and time,
	 * based on the event's existing schedule e.g. daily / weekly.
	 *
	 * @since   2.2.9
	 *
	 * @param   string $event_name     Event Name.
	 */
	public function run( $event_name ) {

		// Only allow Administrators to run events.
		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error(
				'convertkit_cron_run_error',
				esc_html__( 'You are not allowed to run cron events.', 'convertkit' )
			);
		}

		// Get the event's arguments.
		$event_args = $this->get_event_args( $event_name );

		// Bail if no matching event could be found.
		if ( is_wp_error( $event_args ) ) {
			return $event_args;
		}

		// Schedule the event to run now.
		return $this->schedule_event_now( $event_name, $event_args );

	}

	/**
	 * Returns an array of all scheduled WordPress Cron Events.
	 *
	 * @since   2.2.9
	 *
	 * @return  array
	 */
	private function get_all_events() {

		$events = _get_cron_array();

		if ( empty( $events ) ) {
			$events = array();
		}

		return $events;

	}

	/**
	 * Returns the given event name's arguments array, if any were supplied when
	 * the event was registered using wp_schedule_event().
	 *
	 * @since   2.2.9
	 *
	 * @param   string $event_name     Event Name.
	 * @return  WP_Error|array
	 */
	private function get_event_args( $event_name ) {

		// Fetch all scheduled events.
		$events = $this->get_all_events();

		// Find the scheduled event matching the given event name.
		foreach ( $events as $timestamp => $scheduled_event ) {
			// Skip if this event isn't the one we want.
			if ( ! array_key_exists( $event_name, $scheduled_event ) ) {
				continue;
			}

			// Found a matching event.
			$event = reset( $scheduled_event[ $event_name ] );
			return $event['args'];
		}

		// No event found.
		return new WP_Error(
			'convertkit_cron_get_event_args_error',
			sprintf(
				'%s %s %s',
				esc_html__( 'The event', 'convertkit' ),
				esc_html( $event_name ),
				esc_html__( 'could not be found in WordPress\' cron.', 'convertkit' )
			)
		);

	}

	/**
	 * Schedules the given event name to run immediately in WordPress' Cron system.
	 *
	 * The scheduled event will still run as its original date and time,
	 * based on the event's existing schedule e.g. daily / weekly.
	 *
	 * @since   2.2.9
	 *
	 * @param   string $event_name     Event Name.
	 * @param   array  $event_args     Event function arguments.
	 * @return  WP_Error|bool
	 */
	private function schedule_event_now( $event_name, $event_args ) {

		// Clear the WordPress Cron transient.
		delete_transient( 'doing_cron' );

		// Fetch all scheduled events.
		$events = $this->get_all_events();

		// Set the timestamp to 1, to run it now.
		$timestamp = 1;

		// Define the key and add the event to the array of scheduled events.
		$serialized_args = serialize( $event_args ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
		$key             = md5( $serialized_args );
		$events[ $timestamp ][ $event_name ][ $key ] = array(
			'schedule' => false,
			'args'     => $event_args,
		);
		ksort( $events );

		// Update WordPress' Cron events, returning the result.
		return _set_cron_array( $events, true );

	}

}
