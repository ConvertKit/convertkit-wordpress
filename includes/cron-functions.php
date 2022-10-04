<?php
/**
 * ConvertKit WordPress Cron functions.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Refresh the Posts Resource cache, triggered by WordPress' Cron.
 *
 * @since   1.9.7.4
 */
function convertkit_resource_refresh_posts() {

	// Get Settings and Log classes.
	$settings = new ConvertKit_Settings();
	$log      = new ConvertKit_Log( CONVERTKIT_PLUGIN_PATH );

	// If debug logging is enabled, write to it now.
	if ( $settings->debug_enabled() ) {
		$log->add( 'CRON: convertkit_resource_refresh_posts(): Started' );
	}

	// Refresh Posts Resource.
	$posts  = new ConvertKit_Resource_Posts( 'cron' );
	$result = $posts->refresh();

	// If debug logging is enabled, write to it now.
	if ( $settings->debug_enabled() ) {
		// If an error occured, log it.
		if ( is_wp_error( $result ) ) {
			$log->add( 'CRON: convertkit_resource_refresh_posts(): Error: ' . $result->get_error_message() );
		}
		if ( is_array( $result ) ) {
			$log->add( 'CRON: convertkit_resource_refresh_posts(): Success: ' . count( $result ) . ' broadcasts fetched from API and cached.' );
		}

		$log->add( 'CRON: convertkit_resource_refresh_posts(): Finished' );
	}

}

// Register action to run above function; this action is created by WordPress' wp_schedule_event() function
// in the ConvertKit_Resource_Posts class.
add_action( 'convertkit_resource_refresh_posts', 'convertkit_resource_refresh_posts' );

/**
 * Send Usage Data to ConvertKit.
 *
 * @since   1.9.8.6
 */
function convertkit_send_usage_tracking_data() {

	// Get Settings, Usage Tracking and Log classes.
	$settings       = new ConvertKit_Settings();
	$usage_tracking = new ConvertKit_Usage_Tracking();
	$log            = new ConvertKit_Log( CONVERTKIT_PLUGIN_PATH );

	// Bail if Usage Tracking is disabled.
	if ( ! $settings->usage_tracking_enabled() ) {
		return;
	}

	// If debug logging is enabled, write to it now.
	if ( $settings->debug_enabled() ) {
		$log->add( 'CRON: convertkit_send_usage_tracking_data(): Started' );
	}

	// Send data.
	$usage_tracking->send();

	// If debug logging is enabled, write to it now.
	if ( $settings->debug_enabled() ) {
		$log->add( 'CRON: convertkit_send_usage_tracking_data(): Finished' );
	}

}

// Register action to run above function; this action is created by WordPress' wp_schedule_event() function
// in the ConvertKit_Usage_Tracking class.
add_action( 'convertkit_send_usage_tracking_data', 'convertkit_send_usage_tracking_data' );
