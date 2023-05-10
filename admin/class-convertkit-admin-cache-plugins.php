<?php
/**
 * ConvertKit Cache Plugins class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * If Restrict Content is enabled, run functions for common caching plugins
 * to either configure them to disable caching when the ck_subscriber_id
 * is present, or show a notice in the WordPress Administration interface
 * where we cannot automatically configure a caching plugin.
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_Admin_Cache_Plugins {

	/**
	 * Holds the cookie name.
	 *
	 * @since   2.2.1
	 *
	 * @var     string
	 */
	private $key = 'ck_subscriber_id';

	/**
	 * Registers action and filter hooks.
	 *
	 * @since   2.2.1
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'maybe_configure_cache_plugins' ) );

	}

	/**
	 * If Restrict Content is enabled, run functions for common caching plugins
	 * to either configure them to disable caching when the ck_subscriber_id
	 * is present, or show a notice in the WordPress Administration interface
	 * where we cannot automatically configure a caching plugin.
	 * 
	 * @since 	2.2.1
	 */
	public function maybe_configure_cache_plugins() {

		// Bail if Restrict Content is disabled.
		$restrict_content_settings = new ConvertKit_Settings_Restrict_Content();
		if ( ! $restrict_content_settings->enabled() ) {
			return;
		}

		$this->w3_total_cache();
		$this->wp_super_cache();
		$this->wp_fastest_cache();

	}

	public function w3_total_cache() {

		// Bail if the W3 Total Cache plugin is not active.
		if ( ! is_plugin_active( 'w3-total-cache/w3-total-cache.php' ) ) {
			return;
		}

		// Bail if caching isn't enabled in the W3 Total Cache Plugin.
		$config = new \W3TC\Config();
		if ( ! $config->get_boolean( 'pgcache.enabled' ) ) {
			return;
		}

		// If the exclusion rule exists, no need to modify anything.
		if ( in_array( $this->key, $config->get_array( 'pgcache.reject.cookie' ) ) ) {
			return;
		}

		// W3 Total Cache is active, with page caching enabled, but has not been configured to disable
		// caching when the ck_subscriber_id cookie is present.
		// Show a notice in the WordPress Administration, as we can't directly write to the W3
		// Total Cache configuration files.
		WP_ConvertKit()->get_class( 'admin_notices' )->add( 'w3_total_cache' );

		// Define the output of the persistent notice.
		add_filter( 'convertkit_admin_notices_output_w3_total_cache', array( $this, 'w3_total_cache_notice' ) );

	}

	/**
	 * Define the notice text to display in the WordPress Administration interface
	 * when W3 Total Cache is active, its caching enabled and no rule to disable caching
	 * exists when the ck_subscriber_id cookie is present.
	 * 
	 * @since 	2.2.1
	 *
	 * @param 	string 	$notice 	Notice text.
	 * @return 	string 				Notice text
	 */
	public function w3_total_cache_notice( $notice ) {

		return sprintf(
			'%s %s %s %s',
			esc_html__( 'ConvertKit: Member Content: Please add', 'convertkit' ),
			'<code>ck_subscriber_id</code>',
			sprintf(
				'<a href="%s">%s</a>',
				esc_url( 
					admin_url(
						add_query_arg(
							array(
								'page' => 'w3tc_pgcache#pgcache_reject_cookie',
							),
							'admin.php'
						)
					)
				),
				esc_html__( 'to W3 Total Cache\'s "Rejected Cookies" setting by clicking here.', 'convertkit' )
			),
			esc_html__( 'Failing to do so will result in errors.', 'convertkit' ),
		);

	}

	/**
	 * Show a notice in the WordPress Administration interface if
	 * WP Super Cache is active, its caching enabled and no rule to disable caching
	 * exists when the ck_subscriber_id cookie is present.
	 * 
	 * @since 	2.2.1
	 */
	public function wp_super_cache() {

		// Bail if the WP Super Cache plugin is not active.
		if ( ! is_plugin_active( 'wp-super-cache/wp-cache.php' ) ) {
			return;
		}

		// Bail if caching isn't enabled in the WP Super Cache Plugin.
		global $cache_enabled;
		if ( ! $cache_enabled ) {
			return;
		}

		// If the exclusion rule exists, no need to modify anything.
		global $wpsc_rejected_cookies;
		if ( is_array( $wpsc_rejected_cookies ) && in_array( $this->key, $wpsc_rejected_cookies ) ) {
			return;
		}

		// WP Super Cache is active, with caching enabled, but has not been configured to disable
		// caching when the ck_subscriber_id cookie is present.
		// Show a notice in the WordPress Administration, as we can't directly write to the WP
		// Super Cache configuration files.
		WP_ConvertKit()->get_class( 'admin_notices' )->add( 'wp_super_cache' );

		// Define the output of the persistent notice.
		add_filter( 'convertkit_admin_notices_output_wp_super_cache', array( $this, 'wp_super_cache_notice' ) );

	}

	/**
	 * Define the notice text to display in the WordPress Administration interface
	 * when WP Super Cache is active, its caching enabled and no rule to disable caching
	 * exists when the ck_subscriber_id cookie is present.
	 * 
	 * @since 	2.2.1
	 *
	 * @param 	string 	$notice 	Notice text.
	 * @return 	string 				Notice text
	 */
	public function wp_super_cache_notice( $notice ) {

		return sprintf(
			'%s %s %s %s',
			esc_html__( 'ConvertKit: Member Content: Please add', 'convertkit' ),
			'<code>ck_subscriber_id</code>',
			sprintf(
				'<a href="%s">%s</a>',
				esc_url( 
					admin_url(
						add_query_arg(
							array(
								'page' => 'wpsupercache',
								'tab' => 'settings#rejectcookies',
							),
							'options-general.php'
						)
					)
				),
				esc_html__( 'to WP Super Cache\'s "Rejected Cookies" setting by clicking here.', 'convertkit' )
			),
			esc_html__( 'Failing to do so will result in errors.', 'convertkit' ),
		);

	}

	/**
	 * Add a rule to WP Fastest Cache to prevent caching when
	 * the ck_subscriber_id cookie is present.
	 * 
	 * @since 	2.2.1
	 */
	public function wp_fastest_cache() {

		// Bail if the WP Fastest Cache plugin is not active.
		if ( ! is_plugin_active( 'wp-fastest-cache/wpFastestCache.php' ) ) {
			return;
		}

		// Fetch exclusion rules.
		$exclusion_rules = get_option( 'WpFastestCacheExclude' );

		// If the exclusion exists, no need to modify anything.
		if ( strpos( $exclusion_rules, $this->key ) !== false ) {
			return;
		}

		// Define the rule.
		$rule = array(
			'prefix'  => 'contain',
			'content' => $this->key,
			'type' 	  => 'cookie',
		);

		// If no rules exist, add the rule.
		if ( ! $exclusion_rules ) {
			return update_option( 'WpFastestCacheExclude', wp_json_encode( array( $rule ) ) );
		}

		// Append the rule to the existing ruleset.
		$exclusion_rules = json_decode( $exclusion_rules );
		$exclusion_rules[] = $rule;
		return update_option( 'WpFastestCacheExclude', wp_json_encode( $exclusion_rules ) );

	}

}
