<?php
/**
 * ConvertKit WP Rocket Class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Disables WP Rocket from minifying JS / CSS and lazy loading images on Landing Pages.
 *
 * @since   2.4.4
 */
class ConvertKit_WP_Rocket {

	/**
	 * Holds external hosts to exclude from CSS and JS minification.
	 *
	 * @since   2.4.4
	 *
	 * @var     array
	 */
	public $exclude_hosts = array(
		'cdnjs.cloudflare.com',
		'pages.convertkit.com',
		'convertkit.com',
	);

	/**
	 * Holds internal JS paths to exclude from JS minification.
	 * Supports regular expressions.
	 *
	 * @since   2.4.4
	 *
	 * @var     array
	 */
	public $exclude_plugin_js = array(
		'convertkit/resources/frontend/js/(.*).js',
	);

	/**
	 * Constructor
	 *
	 * @since   2.4.4
	 */
	public function __construct() {

		add_action( 'convertkit_output_landing_page_before', array( $this, 'disable_caching_and_minification_on_landing_pages' ) );

	}

	/**
	 * Disable caching and minification when a WordPress Page configured to display a
	 * ConvertKit Landing Page is viewed.
	 *
	 * @since   2.4.4
	 */
	public function disable_caching_and_minification_on_landing_pages() {

		add_filter( 'rocket_minify_excluded_external_js', array( $this, 'exclude_hosts_from_minification' ) );
		add_filter( 'rocket_exclude_css', array( $this, 'exclude_hosts_from_minification' ) );
		add_filter( 'rocket_exclude_js', array( $this, 'exclude_local_js_from_minification' ) );
		add_filter( 'do_rocket_lazyload', '__return_false' );

	}

	/**
	 * Appends the $exclude_hosts property to an array of existing hosts excluded from
	 * minification.
	 *
	 * @since   2.4.4
	 *
	 * @param   array $hosts  External hosts to ignore.
	 * @return  array
	 */
	public function exclude_hosts_from_minification( $hosts ) {

		return array_merge( $hosts, $this->exclude_hosts );

	}

	/**
	 * Appends the $exclude_plugin_js property to an array of existing JS files excluded from
	 * minification.
	 *
	 * @since   2.4.4
	 *
	 * @param   array $scripts  Internal JS scripts to ignore.
	 * @return  array
	 */
	public function exclude_local_js_from_minification( $scripts ) {

		return array_merge( $scripts, $this->exclude_plugin_js );

	}

}
