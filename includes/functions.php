<?php
/**
 * ConvertKit general plugin functions.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Helper: Is ConvertKit WP's debug option enabled?
 *
 * @return bool
 */
function convertkit_wp_debug_enabled() {
	$options = get_option( WP_ConvertKit::SETTINGS_PAGE_SLUG );

	return ! empty( $options['debug'] ) && true == $options['debug']; // phpcs:ignore -- Okay use of loose comparison.
}

/**
 * Gets a customized version of the WordPress default user agent; includes WP Version, PHP version, and ConvertKit plugin version.
 *
 * @return string
 */
function convertkit_wp_get_user_agent() {

	// Include an unmodified $wp_version.
	require ABSPATH . WPINC . '/version.php';

	return sprintf(
		'WordPress/%1$s;PHP/%2$s;ConvertKit/%3$s;%4$s',
		$wp_version,
		phpversion(),
		CONVERTKIT_PLUGIN_VERSION,
		home_url( '/' )
	);
}
