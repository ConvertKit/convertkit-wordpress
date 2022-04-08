<?php
/**
 * ConvertKit Admin Upgrade class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Runs upgrade routines when the Plugin is updated to a newer version
 * in WordPress.
 *
 * @since   1.9.6
 */
class ConvertKit_Admin_Upgrade {

	/**
	 * Runs the upgrade routine once the plugin has loaded
	 *
	 * @since   1.9.6
	 */
	public function run() {

		// Get installed Plugin version.
		$current_version = get_option( 'convertkit_version' );

		/**
		 * 1.4.1: Change ID to form_id for API version 3.0.
		 */
		if ( ! $current_version || version_compare( $current_version, '1.4.1', '<' ) ) {
			$this->change_id_to_form_id();
		}

		/**
		 * 1.6.1+: Refresh Forms, Landing Pages and Tags data stored in settings,
		 * to get new Forms Builder Settings.
		 */
		if ( version_compare( $current_version, '1.6.1', '<' ) ) {
			$this->refresh_resources();
		}

		/**
		 * 1.9.6+: Migrate _wp_convertkit_settings[default_form] to _wp_convertkit_settings[page_form] and
		 * _wp_convertkit_settings[post_form], now that each Post Type has its own Default Form setting
		 * in Settings > ConvertKit > General.
		 */
		if ( version_compare( $current_version, '1.9.6', '<' ) ) {
			$this->migrate_default_form_settings();
		}

		// Update the installed version number in the options table.
		update_option( 'convertkit_version', CONVERTKIT_PLUGIN_VERSION );

	}

	/**
	 * 1.9.6+: Migrate _wp_convertkit_settings[default_form] to _wp_convertkit_settings[page_form] and
	 * _wp_convertkit_settings[post_form], now that each Post Type has its own Default Form setting
	 * in Settings > ConvertKit > General.
	 */
	private function migrate_default_form_settings() {

		$convertkit_settings = new ConvertKit_Settings();

		// Bail if no default_form setting exists.
		$settings = get_option( $convertkit_settings::SETTINGS_NAME );
		if ( ! $settings ) {
			return;
		}
		if ( ! array_key_exists( 'default_form', $settings ) ) {
			return;
		}

		// Restructure settings.
		$settings['page_form'] = $settings['default_form'];
		$settings['post_form'] = $settings['default_form'];

		// Remove obsolete default_form setting.
		unset( $settings['default_form'] );

		// Update.
		update_option( $convertkit_settings::SETTINGS_NAME, $settings );

	}

	/**
	 * 1.6.1: Refresh Forms, Landing Pages and Tags data stored in settings,
	 * to get new Forms Builder Settings.
	 */
	private function refresh_resources() {

		$forms         = new ConvertKit_Resource_Forms();
		$landing_pages = new ConvertKit_Resource_Landing_Pages();
		$tags          = new ConvertKit_Resource_Tags();

		$forms->refresh();
		$landing_pages->refresh();
		$tags->refresh();

	}

	/**
	 * 1.4.1: Change ID to form_id for API version 3.0.
	 *
	 * @since   1.4.1
	 */
	private function change_id_to_form_id() {

		// Bail if the API isn't configured.
		$settings = new ConvertKit_Settings();
		if ( ! $settings->has_api_key_and_secret() ) {
			return;
		}

		// Get all posts and pages to track what has been updated.
		$posts = get_option( '_wp_convertkit_upgrade_posts' );
		if ( ! $posts ) {
			$args = array(
				'post_type' => array( 'post', 'page' ),
				'fields'    => 'ids',
			);

			$result = new WP_Query( $args );
			$posts  = $result->posts;
			update_option( '_wp_convertkit_upgrade_posts', $posts );
		}

		// Initialize the API.
		$api = new ConvertKit_API( $settings->get_api_key(), $settings->get_api_secret(), $settings->debug_enabled() );

		// Get form mappings.
		$mappings = $api->get_subscription_forms();

		// Bail if no form mappings exist.
		if ( ! $mappings ) {
			return;
		}

		// 1. Update global form.
		$settings_data                 = $settings->get();
		$settings_data['default_form'] = isset( $mappings[ $settings->get_default_form( 'post' ) ] ) ? $mappings[ $settings->get_default_form( 'post' ) ] : 0;
		update_option( $settings::SETTINGS_NAME, $settings );

		// 2. Scan posts/pages for _wp_convertkit_post_meta and update IDs
		// Scan content for shortcode and update
		// Remove page_id from posts array after page is updated.
		foreach ( $posts as $key => $post_id ) {
			$post_settings = get_post_meta( $post_id, '_wp_convertkit_post_meta', true );

			if ( isset( $post_settings['form'] ) && ( 0 < $post_settings['form'] ) ) {
				$post_settings['form'] = isset( $mappings[ $post_settings['form'] ] ) ? $mappings[ $post_settings['form'] ] : 0;
			}
			if ( isset( $post_settings['landing_page'] ) && ( 0 < $post_settings['landing_page'] ) ) {
				$post_settings['landing_page'] = isset( $mappings[ $post_settings['landing_page'] ] ) ? $mappings[ $post_settings['landing_page'] ] : 0;
			}
			update_post_meta( $post_id, '_wp_convertkit_post_meta', $post_settings );
			unset( $posts[ $key ] );
			update_option( '_wp_convertkit_upgrade_posts', $posts );
		}

	}

}
