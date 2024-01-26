<?php
/**
 * ConvertKit Cache and Minification Plugins class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Configures third party caching Plugins to ensure ConvertKit functionality
 * (such as Landing Pages) works correctly by disabling minification and/or
 * caching as applicable.
 *
 * @since   2.4.3
 */
class ConvertKit_Cache_Minification_Plugins {

	/**
	 * Holds sensible external hosts to exclude from CSS and JS minification.
	 *
	 * @since   2.4.3
	 *
	 * @var     array
	 */
	public $exclude_hosts = array(
		'cdnjs.cloudflare.com',
		'pages.convertkit.com',
		'convertkit.com',
	);

	/**
	 * Constructor
	 *
	 * @since   2.4.3
	 */
	public function __construct() {

		add_action( 'convertkit_output_landing_page_before', array( $this, 'disable_caching_and_minification_on_landing_pages' ) );

	}

	/**
	 * Disable caching and minification when a WordPress Page configured to display a
	 * ConvertKit Landing Page is viewed.
	 *
	 * @since   2.4.3
	 */
	public function disable_caching_and_minification_on_landing_pages() {

		// WP-Rocket.
		add_filter( 'rocket_minify_excluded_external_js', array( $this, 'exclude_hosts_from_minification' ) );
		add_filter( 'rocket_exclude_css', array( $this, 'exclude_hosts_from_minification' ) );
		add_filter( 'do_rocket_lazyload', '__return_false' );

	}

	/**
	 * Appends the $exclude_hosts property to an array of existing hosts excluded from
	 * minification for third party Plugins.
	 *
	 * @since   2.4.3
	 *
	 * @param   array $hosts  External hosts to ignore.
	 * @return  array
	 */
	public function exclude_hosts_from_minification( $hosts ) {

		return array_merge( $hosts, $this->exclude_hosts );

	}

}
