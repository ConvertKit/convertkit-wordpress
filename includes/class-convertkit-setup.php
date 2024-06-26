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
		 * 2.5.0: Get Access token for API version 4.0 using a v3 API Key and Secret.
		 */
		if ( ! $current_version || version_compare( $current_version, '2.5.0', '<' ) ) {
			$this->maybe_get_access_token_by_api_key_and_secret();
		}

		/**
		 * 2.4.9.1+: Migrate ck_default_form to _wp_convertkit_term_meta[form], as Term settings
		 * support multiple options (form, position etc).
		 */
		if ( version_compare( $current_version, '2.4.9.1', '<' ) ) {
			$this->migrate_term_form_settings();
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

	/**
	 * 2.5.0: Fetch an Access Token, Refresh Token and Expiry for v4 API use
	 * based on the Plugin setting's v3 API Key and Secret.
	 *
	 * @since   2.5.0
	 */
	private function maybe_get_access_token_by_api_key_and_secret() {

		$convertkit_settings = new ConvertKit_Settings();

		// Bail if an Access Token exists; we don't need to fetch another one.
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

		// Get Access Token by API Key and Secret.
		$api    = new ConvertKit_API_V4( CONVERTKIT_OAUTH_CLIENT_ID, admin_url( 'options-general.php?page=_wp_convertkit_settings' ) );
		$result = $api->get_access_token_by_api_key_and_secret(
			$convertkit_settings->get_api_key(),
			$convertkit_settings->get_api_secret()
		);

		// Bail if an error occured.
		if ( is_wp_error( $result ) ) {
			return;
		}

		// Store the new credentials.
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
	 * Migrate ck_default_form to _wp_convertkit_term_meta[form], as Term settings
	 * support multiple options (form, position etc).
	 *
	 * @since   2.4.9.1
	 */
	private function migrate_term_form_settings() {

		// Get all Terms that have ConvertKit settings defined.
		$query = new WP_Term_Query(
			array(
				'taxonomy'   => 'category',
				'hide_empty' => false,
				'fields'     => 'ids',
				'meta_query' => array(
					array(
						'key'        => 'ck_default_form',
						'comparison' => 'EXISTS',
					),
				),
			)
		);

		// Bail if no Terms exist.
		if ( ! $query->terms ) {
			return;
		}

		// Iterate through Terms, mapping settings.
		foreach ( $query->terms as $term_id ) {
			$term_settings = new ConvertKit_Term( $term_id );
			$term_settings->save(
				array(
					'form'          => get_term_meta( $term_id, 'ck_default_form', true ), // Fetch form setting from old meta key.
					'form_position' => '', // Default to no position.
				)
			);

			// Delete old Term meta.
			delete_term_meta( $term_id, 'ck_default_form' );
		}

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
