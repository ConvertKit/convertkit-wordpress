<?php
/**
 * ConvertKit general plugin functions.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Helper method to get supported Post Types.
 *
 * @since   1.9.6
 *
 * @return  array   Post Types
 */
function convertkit_get_supported_post_types() {

	$post_types = array(
		'page',
		'post',
	);

	/**
	 * Defines the Post Types that support ConvertKit Forms.
	 *
	 * @since   1.9.6
	 *
	 * @param   array   $post_types     Post Types
	 */
	$post_types = apply_filters( 'convertkit_get_supported_post_types', $post_types );

	return $post_types;

}

/**
 * Helper method to get registered Shortcodes.
 *
 * @since   1.9.6.5
 *
 * @return  array   Shortcodes
 */
function convertkit_get_shortcodes() {

	$shortcodes = array();

	/**
	 * Registers shortcodes for the ConvertKit Plugin.
	 *
	 * @since   1.9.6.5
	 *
	 * @param   array   $shortcodes     Shortcodes
	 */
	$shortcodes = apply_filters( 'convertkit_shortcodes', $shortcodes );

	return $shortcodes;

}

/**
 * Helper method to get registered Blocks.
 *
 * @since   1.9.6
 *
 * @return  array   Blocks
 */
function convertkit_get_blocks() {

	$blocks = array();

	/**
	 * Registers blocks for the ConvertKit Plugin.
	 *
	 * @since   1.9.6
	 *
	 * @param   array   $blocks     Blocks
	 */
	$blocks = apply_filters( 'convertkit_blocks', $blocks );

	return $blocks;

}

/**
 * Helper method to return the Plugin Settings Link
 *
 * @since   1.9.6
 *
 * @param   array $query_args     Optional Query Args.
 * @return  string                  Settings Link
 */
function convertkit_get_settings_link( $query_args = array() ) {

	$query_args = array_merge(
		$query_args,
		array(
			'page' => '_wp_convertkit_settings',
		)
	);

	return add_query_arg( $query_args, admin_url( 'options-general.php' ) );

}

/**
 * Helper method to return the URL the user needs to visit to sign in to their ConvertKit account.
 *
 * @since   1.9.6.1
 *
 * @return  string  ConvertKit Login URL.
 */
function convertkit_get_sign_in_url() {

	return 'https://app.convertkit.com/?utm_source=wordpress&utm_content=convertkit';

}

/**
 * Helper method to return the URL the user needs to visit on the ConvertKit app to obtain their API Key and Secret.
 *
 * @since   1.9.6.1
 *
 * @return  string  ConvertKit App URL.
 */
function convertkit_get_api_key_url() {

	return 'https://app.convertkit.com/account_settings/advanced_settings/?utm_source=wordpress&utm_content=convertkit';

}

/**
 * Helper method to enqueue Select2 scripts for use within the ConvertKit Plugin.
 *
 * @since   1.9.6.4
 */
function convertkit_select2_enqueue_scripts() {

	wp_enqueue_script( 'convertkit-select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array( 'jquery' ), CONVERTKIT_PLUGIN_VERSION, false );
	wp_enqueue_script( 'convertkit-admin-select2', CONVERTKIT_PLUGIN_URL . '/resources/backend/js/select2.js', array( 'convertkit-select2' ), CONVERTKIT_PLUGIN_VERSION, false );

}

/**
 * Helper method to enqueue Select2 stylesheets for use within the ConvertKit Plugin.
 *
 * @since   1.9.6.4
 */
function convertkit_select2_enqueue_styles() {

	wp_enqueue_style( 'convertkit-select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), CONVERTKIT_PLUGIN_VERSION );
	wp_enqueue_style( 'convertkit-admin-select2', CONVERTKIT_PLUGIN_URL . '/resources/backend/css/select2.css', array(), CONVERTKIT_PLUGIN_VERSION );

}
