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
		 * 2.5.0: Cache legacy resources as they're not returned in the v4 API.
		 */
		if ( ! $current_version || version_compare( $current_version, '2.5.0', '<' ) ) {
			$this->maybe_cache_legacy_resources();
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
	 * 2.5.0: Store legacy forms and landing pages in separate option settings
	 * so they are preserved, as the v4 API won't return legacy forms and
	 * landing pages.
	 *
	 * @since   2.5.0
	 */
	private function maybe_cache_legacy_resources() {

		// Initialize resource classes.
		$forms = new ConvertKit_Resource_Forms();
		$landing_pages = new ConvertKit_Resource_Landing_Pages();

		// Forms.
		$legacy_forms = array();
		if ( $forms->exist() ) {
			foreach ( $forms->get() as $form ) {
				// If no uid is present in the Form API data, this is a legacy form that's served by directly fetching the HTML
				// from forms.convertkit.com.
				if ( array_key_exists( 'uid', $form ) ) {
					continue;
				}

				// Cache this form.
				$legacy_forms[ $form['id'] ] = $form;
			}
		}

		// Landing Pages.
		$legacy_landing_pages = array();
		if ( $landing_pages->exist() ) {
			foreach ( $landing_pages->get() as $landing_page ) {
				// If no uid is present in the Form API data, this is a legacy landing page that's served by directly fetching the HTML
				// from forms.convertkit.com.
				if ( array_key_exists( 'uid', $landing_page ) ) {
					continue;
				}

				// Cache this landing page.
				$legacy_landing_pages[ $landing_page['id'] ] = $landing_page;
			}
		}

		// Cache legacy resources.
		update_option( $forms->legacy_settings_name, $legacy_forms );
		update_option( $landing_pages->legacy_settings_name, $legacy_landing_pages );

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
