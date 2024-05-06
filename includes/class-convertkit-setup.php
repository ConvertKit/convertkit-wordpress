<?php
/**
 * Plugin activation, update and deactivation class.
 *
 * @package ConvertKit
 * @author  ConvertKit
 */

/**
 * Runs any steps required on plugin activation, update and deactivation.
 *
 * @package ConvertKit
 * @author  ConvertKit
 * @version 1.9.7.4
 */
class ConvertKit_Setup {

	/**
	 * Runs routines when the Plugin is activated.
	 *
	 * @since   1.9.7.4
	 */
	public function activate() {

		// Call any functions to e.g. schedule WordPress Cron events now.
		$posts = new ConvertKit_Resource_Posts( 'cron' );
		$posts->schedule_cron_event();

	}

	/**
	 * Runs routines when the Plugin version has been updated.
	 *
	 * @since   1.9.7.4
	 */
	public function update() {

		// Get installed Plugin version.
		$current_version = get_option( 'convertkit_version' );

		// If the version number matches the plugin version, no update routines
		// need to run.
		if ( $current_version === CONVERTKIT_PLUGIN_VERSION ) {
			return;
		}

		/**
		 * 2.5.0: Exchange API Key and Secret for Access Token for API version 4.0.
		 */
		if ( ! $current_version || version_compare( $current_version, '2.5.0', '<' ) ) {
			$this->maybe_cache_legacy_resources();
			$this->maybe_exchange_api_key_and_secret_for_access_token();
		}

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

		/**
		 * 1.9.7.4+: Schedule Post Resources' Cron event to refresh Posts cache hourly,
		 * as the activate() routine won't pick this up for existing active installations.
		 */
		if ( version_compare( $current_version, '1.9.7.4', '<' ) ) {
			$posts = new ConvertKit_Resource_Posts( 'cron' );
			$posts->schedule_cron_event();
		}

		// Update the installed version number in the options table.
		update_option( 'convertkit_version', CONVERTKIT_PLUGIN_VERSION );

	}

	private function maybe_cache_legacy_resources() {

		$forms = new ConvertKit_Resource_Forms();
		$landing_pages = new ConvertKit_Resource_Landing_Pages();

		var_dump( $forms->get() );
		var_dump( $landing_pages->get() );
		die();

	}

	/**
	 * 2.5.0: Exchange the existing API Key and Secret for an Access Token,
	 * Refresh Token and Expiry.
	 *
	 * @since   2.5.0
	 */
	private function maybe_exchange_api_key_and_secret_for_access_token() {

		$convertkit_settings = new ConvertKit_Settings();

		// Bail if an Access Token exists; we don't need to exchange anything.
		if ( $convertkit_settings->has_access_token() ) {
			return;
		}

		// Bail if no API Key or Secret.
		if ( empty( $convertkit_settings->get_api_key() ) ) {
			return;
		}
		if ( empty( $convertkit_settings->get_api_secret() ) ) {
			return;
		}

		// Exchange API Key and Secret for an Access Token.
		$api    = new ConvertKit_API( CONVERTKIT_OAUTH_CLIENT_ID, admin_url( 'options-general.php?page=_wp_convertkit_settings' ) );
		$result = $api->exchange_api_key_and_secret_for_access_token(
			$convertkit_settings->get_api_key(),
			$convertkit_settings->get_api_secret()
		);

		// Bail if an error occured.
		if ( is_wp_error( $result ) ) {
			return;
		}

		// Store the new credentials, removing the API Key and Secret.
		// We don't use update_credentials(), because the response
		// includes an `expires_at`, not a `created_at` and `expires_in`.
		$convertkit_settings->save(
			array(
				'access_token'  => $result['oauth']['access_token'],
				'refresh_token' => $result['oauth']['refresh_token'],
				'token_expires' => $result['oauth']['expires_at'],
			)
		);

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

		$forms         = new ConvertKit_Resource_Forms( 'setup' );
		$landing_pages = new ConvertKit_Resource_Landing_Pages( 'setup' );
		$tags          = new ConvertKit_Resource_Tags( 'setup' );

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
		$convertkit_settings = new ConvertKit_Settings();
		if ( ! $convertkit_settings->has_access_and_refresh_token() ) {
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
		$api = new ConvertKit_API(
			CONVERTKIT_OAUTH_CLIENT_ID,
			admin_url( 'options-general.php?page=_wp_convertkit_settings' ),
			$convertkit_settings->get_access_token(),
			$convertkit_settings->get_refresh_token(),
			$convertkit_settings->debug_enabled(),
			'setup'
		);

		// Get form mappings.
		$mappings = $api->get_subscription_forms();

		// Bail if no form mappings exist.
		if ( ! $mappings ) {
			return;
		}

		// 1. Update global form.
		$settings_data                 = $convertkit_settings->get();
		$settings_data['default_form'] = isset( $mappings[ $convertkit_settings->get_default_form( 'post' ) ] ) ? $mappings[ $convertkit_settings->get_default_form( 'post' ) ] : 0;
		update_option( $convertkit_settings::SETTINGS_NAME, $settings_data );

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

	/**
	 * Runs routines when the Plugin is deactivated.
	 *
	 * @since   1.9.7.4
	 */
	public function deactivate() {

		// Call any functions to e.g. unschedule WordPress Cron events now.
		$posts = new ConvertKit_Resource_Posts( 'cron' );
		$posts->unschedule_cron_event();

	}

}
