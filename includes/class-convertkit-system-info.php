<?php
/**
 * ConvertKit System Info class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Class to read system information.
 *
 * @since   1.9.6
 */
class ConvertKit_System_Info {

	/**
	 * Get system info
	 *
	 * Adapted from Easy Digital Downloads
	 *
	 * @since       2.0
	 * @global      object $wpdb Used to query the database using the WordPress Database API
	 * @return      string $return A string containing the info to output
	 */
	public function get() {

		global $wpdb;

		if ( ! class_exists( 'Browser' ) ) {
			require_once CONVERTKIT_PLUGIN_PATH . '/lib/browser.php';
		}

		$browser = new Browser();

		// Get theme info.
		$theme_data   = wp_get_theme();
		$theme        = $theme_data->Name . ' ' . $theme_data->Version; // phpcs:ignore WordPress.NamingConventions.ValidVariableName
		$parent_theme = $theme_data->Template; // phpcs:ignore WordPress.NamingConventions.ValidVariableName
		if ( ! empty( $parent_theme ) ) {
			$parent_theme_data = wp_get_theme( $parent_theme );
			$parent_theme      = $parent_theme_data->Name . ' ' . $parent_theme_data->Version; // phpcs:ignore WordPress.NamingConventions.ValidVariableName
		}

		// Try to identify the hosting provider.
		$host = $this->get_host();

		$return = '### Begin System Info ###' . "\n\n";

		// Start with the basics...
		$return .= '-- Site Info' . "\n\n";
		$return .= 'Site URL:                 ' . site_url() . "\n";
		$return .= 'Home URL:                 ' . home_url() . "\n";
		$return .= 'Multisite:                ' . ( is_multisite() ? 'Yes' : 'No' ) . "\n";

		/**
		 * Output System Information immediately after the Site Info section.
		 *
		 * @since   1.9.6
		 *
		 * @param   string  $return     System Info.
		 */
		$return = apply_filters( 'convertkit_sysinfo_after_site_info', $return );

		// Can we determine the site's host?
		if ( $host ) {
			$return .= "\n" . '-- Hosting Provider' . "\n\n";
			$return .= 'Host:                     ' . $host . "\n";

			/**
			 * Output System Information immediately after the Hosting Provider section.
			 *
			 * @since   1.9.6
			 *
			 * @param   string  $return     System Info.
			 */
			$return = apply_filters( 'convertkit_sysinfo_after_host_info', $return );
		}

		// The local users' browser information, handled by the Browser class.
		$return .= "\n" . '-- User Browser' . "\n\n";
		$return .= $browser;

		/**
		 * Output System Information immediately after the User Browser section.
		 *
		 * @since   1.9.6
		 *
		 * @param   string  $return     System Info.
		 */
		$return = apply_filters( 'convertkit_sysinfo_after_user_browser', $return );

		$locale = get_locale();

		// WordPress configuration.
		$return .= "\n" . '-- WordPress Configuration' . "\n\n";
		$return .= 'Version:                  ' . get_bloginfo( 'version' ) . "\n";
		$return .= 'Language:                 ' . ( ! empty( $locale ) ? $locale : 'en_US' ) . "\n";
		$return .= 'Permalink Structure:      ' . ( get_option( 'permalink_structure' ) ? get_option( 'permalink_structure' ) : 'Default' ) . "\n";
		$return .= 'Active Theme:             ' . $theme . "\n";
		if ( $parent_theme !== $theme ) {
			$return .= 'Parent Theme:             ' . $parent_theme . "\n";
		}
		$return .= 'Show On Front:            ' . get_option( 'show_on_front' ) . "\n";

		// Only show page specs if frontpage is set to 'page'.
		if ( get_option( 'show_on_front' ) === 'page' ) {
			$front_page_id = get_option( 'page_on_front' );
			$blog_page_id  = get_option( 'page_for_posts' );

			$return .= 'Page On Front:            ' . ( $front_page_id !== 0 ? get_the_title( $front_page_id ) . ' (#' . $front_page_id . ')' : 'Unset' ) . "\n";
			$return .= 'Page For Posts:           ' . ( $blog_page_id !== 0 ? get_the_title( $blog_page_id ) . ' (#' . $blog_page_id . ')' : 'Unset' ) . "\n";
		}

		$return .= 'ABSPATH:                  ' . ABSPATH . "\n";

		// Make sure wp_remote_post() is working.
		$request['cmd'] = '_notify-validate';

		$params = array(
			'sslverify'  => false,
			'timeout'    => 60,
			'user-agent' => $this->get_user_agent(),
			'body'       => $request,
		);

		$response = wp_remote_post( 'https://www.paypal.com/cgi-bin/webscr', $params );

		if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
			$wp_remote_post = 'wp_remote_post() works';
		} else {
			$wp_remote_post = 'wp_remote_post() does not work';
		}

		$return .= 'Remote Post:              ' . $wp_remote_post . "\n";
		$return .= 'Table Prefix:             Length: ' . strlen( $wpdb->prefix ) . '   Status: ' . ( strlen( $wpdb->prefix ) > 16 ? 'ERROR: Too long' : 'Acceptable' ) . "\n";
		$return .= 'WP_DEBUG:                 ' . ( defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set' ) . "\n";
		$return .= 'Memory Limit:             ' . WP_MEMORY_LIMIT . "\n";
		$return .= 'Registered Post Stati:    ' . implode( ', ', get_post_stati() ) . "\n"; // @phpstan-ignore-line.

		/**
		 * Output System Information immediately after the WordPress Configuration section.
		 *
		 * @since   1.9.6
		 *
		 * @param   string  $return     System Info.
		 */
		$return = apply_filters( 'convertkit_sysinfo_after_wordpress_config', $return );

		// ConvertKit configuration.
		$return .= "\n" . '-- ConvertKit Configuration' . "\n\n";
		$return .= 'Version:                  ' . CONVERTKIT_PLUGIN_VERSION . "\n";

		/**
		 * Output System Information immediately after the ConvertKit Configuration section.
		 *
		 * @since   1.9.6
		 *
		 * @param   string  $return     System Info.
		 */
		$return = apply_filters( 'convertkit_sysinfo_after_convertkit_config', $return );

		// Get plugins that have an update.
		$updates = get_plugin_updates();

		// Must-use plugins
		// NOTE: MU plugins can't show updates!
		$muplugins = get_mu_plugins();
		if ( count( $muplugins ) > 0 ) {
			$return .= "\n" . '-- Must-Use Plugins' . "\n\n";

			foreach ( $muplugins as $plugin => $plugin_data ) {
				$return .= $plugin_data['Name'] . ': ' . $plugin_data['Version'] . "\n";
			}

			/**
			 * Output System Information immediately after the Must-Use Plugins section.
			 *
			 * @since   1.9.6
			 *
			 * @param   string  $return     System Info.
			 */
			$return = apply_filters( 'convertkit_sysinfo_after_wordpress_mu_plugins', $return );
		}

		// WordPress active plugins.
		$return .= "\n" . '-- WordPress Active Plugins' . "\n\n";

		$plugins        = get_plugins();
		$active_plugins = get_option( 'active_plugins', array() );

		foreach ( $plugins as $plugin_path => $plugin ) {
			if ( ! in_array( $plugin_path, $active_plugins, true ) ) {
				continue;
			}

			$update  = ( array_key_exists( $plugin_path, $updates ) ) ? ' (needs update - ' . $updates[ $plugin_path ]->update->new_version . ')' : '';
			$return .= $plugin['Name'] . ': ' . $plugin['Version'] . $update . "\n";
		}

		/**
		 * Output System Information immediately after the WordPress Active Plugins section.
		 *
		 * @since   1.9.6
		 *
		 * @param   string  $return     System Info.
		 */
		$return = apply_filters( 'convertkit_sysinfo_after_wordpress_plugins', $return );

		// WordPress inactive plugins.
		$return .= "\n" . '-- WordPress Inactive Plugins' . "\n\n";

		foreach ( $plugins as $plugin_path => $plugin ) {
			if ( in_array( $plugin_path, $active_plugins, true ) ) {
				continue;
			}

			$update  = ( array_key_exists( $plugin_path, $updates ) ) ? ' (needs update - ' . $updates[ $plugin_path ]->update->new_version . ')' : '';
			$return .= $plugin['Name'] . ': ' . $plugin['Version'] . $update . "\n";
		}

		/**
		 * Output System Information immediately after the WordPress Inactive Plugins section.
		 *
		 * @since   1.9.6
		 *
		 * @param   string  $return     System Info.
		 */
		$return = apply_filters( 'convertkit_sysinfo_after_wordpress_plugins_inactive', $return );

		if ( is_multisite() ) {
			// WordPress Multisite active plugins.
			$return .= "\n" . '-- Network Active Plugins' . "\n\n";

			$plugins        = wp_get_active_network_plugins();
			$active_plugins = get_site_option( 'active_sitewide_plugins', array() );

			foreach ( $plugins as $plugin_path ) {
				$plugin_base = plugin_basename( $plugin_path );

				if ( ! array_key_exists( $plugin_base, $active_plugins ) ) {
					continue;
				}

				$update  = ( array_key_exists( $plugin_path, $updates ) ) ? ' (needs update - ' . $updates[ $plugin_path ]->update->new_version . ')' : '';
				$plugin  = get_plugin_data( $plugin_path );
				$return .= $plugin['Name'] . ': ' . $plugin['Version'] . $update . "\n";
			}

			/**
			 * Output System Information immediately after the Network Active Plugins section.
			 *
			 * @since   1.9.6
			 *
			 * @param   string  $return     System Info.
			 */
			$return = apply_filters( 'convertkit_sysinfo_after_wordpress_ms_plugins', $return );
		}

		// Server configuration (really just versioning).
		$return .= "\n" . '-- Webserver Configuration' . "\n\n";
		$return .= 'PHP Version:              ' . PHP_VERSION . "\n";
		$return .= 'MySQL Version:            ' . $wpdb->db_version() . "\n";
		$return .= 'Webserver Info:           ' . $_SERVER['SERVER_SOFTWARE'] . "\n";

		/**
		 * Output System Information immediately after the Webserver Configuration section.
		 *
		 * @since   1.9.6
		 *
		 * @param   string  $return     System Info.
		 */
		$return = apply_filters( 'convertkit_sysinfo_after_webserver_config', $return );

		// PHP configs... now we're getting to the important stuff.
		$return .= "\n" . '-- PHP Configuration' . "\n\n";
		$return .= 'Memory Limit:             ' . ini_get( 'memory_limit' ) . "\n";
		$return .= 'Upload Max Size:          ' . ini_get( 'upload_max_filesize' ) . "\n";
		$return .= 'Post Max Size:            ' . ini_get( 'post_max_size' ) . "\n";
		$return .= 'Upload Max Filesize:      ' . ini_get( 'upload_max_filesize' ) . "\n";
		$return .= 'Time Limit:               ' . ini_get( 'max_execution_time' ) . "\n";
		$return .= 'Max Input Vars:           ' . ini_get( 'max_input_vars' ) . "\n";
		$return .= 'Display Errors:           ' . ( ini_get( 'display_errors' ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A' ) . "\n";
		$return .= 'PHP Arg Separator:        ' . ini_get( 'arg_separator.output' ) . "\n";

		/**
		 * Output System Information immediately after the PHP Configuration section.
		 *
		 * @since   1.9.6
		 *
		 * @param   string  $return     System Info.
		 */
		$return = apply_filters( 'convertkit_sysinfo_after_php_config', $return );

		$curl_version_info = function_exists( 'curl_version' ) ? curl_version() : array(
			'version'     => 'Unknown; cURL not avaible',
			'ssl_version' => 'Unknown; cURL not avaible',
		);

		// PHP extensions and such.
		$return .= "\n" . '-- PHP Extensions' . "\n\n";
		$return .= 'cURL:                     ' . ( function_exists( 'curl_init' ) ? 'Supported' : 'Not Supported' ) . "\n";
		$return .= 'cURL version:             ' . $curl_version_info['version'] . "\n";
		$return .= 'ssl version:              ' . $curl_version_info['ssl_version'] . "\n";
		$return .= 'fsockopen:                ' . ( function_exists( 'fsockopen' ) ? 'Supported' : 'Not Supported' ) . "\n";
		$return .= 'SOAP Client:              ' . ( class_exists( 'SoapClient' ) ? 'Installed' : 'Not Installed' ) . "\n";
		$return .= 'Suhosin:                  ' . ( extension_loaded( 'suhosin' ) ? 'Installed' : 'Not Installed' ) . "\n";

		/**
		 * Output System Information immediately after the PHP Extensions section.
		 *
		 * @since   1.9.6
		 *
		 * @param   string  $return     System Info.
		 */
		$return = apply_filters( 'convertkit_sysinfo_after_php_ext', $return );

		// Session stuff.
		$return .= "\n" . '-- Session Configuration' . "\n\n";

		// The rest of this is only relevant is session is enabled.
		if ( isset( $_SESSION ) ) {
			$return .= 'Session Name:             ' . esc_html( ini_get( 'session.name' ) ) . "\n";
			$return .= 'Cookie Path:              ' . esc_html( ini_get( 'session.cookie_path' ) ) . "\n";
			$return .= 'Save Path:                ' . esc_html( ini_get( 'session.save_path' ) ) . "\n";
			$return .= 'Use Cookies:              ' . ( ini_get( 'session.use_cookies' ) ? 'On' : 'Off' ) . "\n";
			$return .= 'Use Only Cookies:         ' . ( ini_get( 'session.use_only_cookies' ) ? 'On' : 'Off' ) . "\n";
		}

		/**
		 * Output System Information immediately after the Session Configuration section.
		 *
		 * @since   1.9.6
		 *
		 * @param   string  $return     System Info.
		 */
		$return = apply_filters( 'convertkit_sysinfo_after_session_config', $return );

		$return .= "\n" . '### End System Info ###';

		return $return;

	}

	/**
	 * Get user host
	 *
	 * Adapted from Easy Digital Downloads
	 *
	 * Returns the webhost this site is using if possible
	 *
	 * @since   1.6.4
	 * @return  string  Host
	 */
	private function get_host() {
		$host = false;

		if ( defined( 'WPE_APIKEY' ) ) {
			$host = 'WP Engine';
		} elseif ( defined( 'PAGELYBIN' ) ) {
			$host = 'Pagely';
		} elseif ( DB_HOST === 'localhost:/tmp/mysql5.sock' ) { // @phpstan-ignore-line.
			$host = 'ICDSoft';
		} elseif ( DB_HOST === 'mysqlv5' ) {
			$host = 'NetworkSolutions';
		} elseif ( strpos( DB_HOST, 'ipagemysql.com' ) !== false ) {
			$host = 'iPage';
		} elseif ( strpos( DB_HOST, 'ipowermysql.com' ) !== false ) {
			$host = 'IPower';
		} elseif ( strpos( DB_HOST, '.gridserver.com' ) !== false ) {
			$host = 'MediaTemple Grid';
		} elseif ( strpos( DB_HOST, '.pair.com' ) !== false ) {
			$host = 'pair Networks';
		} elseif ( strpos( DB_HOST, '.stabletransit.com' ) !== false ) {
			$host = 'Rackspace Cloud';
		} elseif ( strpos( DB_HOST, '.sysfix.eu' ) !== false ) {
			$host = 'SysFix.eu Power Hosting';
		} elseif ( strpos( $_SERVER['SERVER_NAME'], 'Flywheel' ) !== false ) {
			$host = 'Flywheel';
		} else {
			// Adding a general fallback for data gathering.
			$host = 'DBH: ' . DB_HOST . ', SRV: ' . $_SERVER['SERVER_NAME'];
		}

		return $host;

	}

	/**
	 * Gets a customized version of the WordPress default user agent; includes WP Version, PHP version, and ConvertKit plugin version.
	 *
	 * @since   1.9.6
	 *
	 * @return string
	 */
	private function get_user_agent() {

		global $wp_version;

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

}
