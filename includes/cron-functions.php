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
