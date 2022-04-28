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
function convertkit_cron_refresh_resource_posts( $network_wide ) {

	// Initialise Plugin.
	$convertkit = WP_ConvertKit();

	// Refresh Posts Resource.
	$posts = new ConvertKit_Resource_Posts();
	$posts->refresh();

	// Shutdown.
	unset( $convertkit );

}

// Register action to run above function; this action is created by WordPress' wp_schedule_event() function
// in the ConvertKit_Resource_Posts class.
add_action( 'convertkit_cron_refresh_resource_posts', 'convertkit_cron_refresh_resource_posts' );
