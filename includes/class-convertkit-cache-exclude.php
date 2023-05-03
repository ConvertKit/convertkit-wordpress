<?php
/**
 * ConvertKit Cache Exclusion class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Automatically configures third party caching plugins to prevent them
 * from caching content when the ck_subscriber_id cookie is present.
 * 
 * This cookie is used for Restrict Content and Custom Content functionality,
 * both of which may display different content compared to the WordPress Page.
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_Cache_Exclude {

	/**
	 * Holds the cookie name.
	 *
	 * @since   2.2.0
	 *
	 * @var     string
	 */
	private $key = 'ck_subscriber_id';

	/**
	 * Registers action and filter hooks.
	 *
	 * @since   2.2.0
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'wp_fastest_cache' ) );

	}

	/**
	 * Automatically add a rule to WP Fastest Cache to prevent caching when
	 * the ck_subscriber_id cookie is present.
	 * 
	 * @since 	2.2.0
	 */
	public function wp_fastest_cache() {

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
