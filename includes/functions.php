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
