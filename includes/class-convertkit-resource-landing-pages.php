<?php
/**
 * ConvertKit Landing Pages Resource class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Reads ConvertKit Landing Pages from the options table, and refreshes
 * ConvertKit Landing Pages data stored locally from the API.
 *
 * @since   1.9.6
 */
class ConvertKit_Resource_Landing_Pages extends ConvertKit_Resource {

	/**
	 * Holds the Settings Key that stores site wide ConvertKit settings
	 *
	 * @var     string
	 */
	public $settings_name = 'convertkit_landing_pages';

	/**
	 * Holds the Settings Key that stores legacy Landing pages
	 * 
	 * @since 	2.5.0
	 * 
	 * @var 	string
	 */
	public $legacy_settings_name = 'convertkit_landing_pages_legacy';

	/**
	 * The type of resource
	 *
	 * @var     string
	 */
	public $type = 'landing_pages';

	/**
	 * Constructor.
	 *
	 * @since   1.9.8.4
	 *
	 * @param   bool|string $context    Context.
	 */
	public function __construct( $context = false ) {

		// Initialize the API if the Access Token has been defined in the Plugin Settings.
		$settings = new ConvertKit_Settings();
		if ( $settings->has_access_and_refresh_token() ) {
			$this->api = new ConvertKit_API(
				CONVERTKIT_OAUTH_CLIENT_ID,
				admin_url( 'options-general.php?page=_wp_convertkit_settings' ),
				$settings->get_access_token(),
				$settings->get_refresh_token(),
				$settings->debug_enabled(),
				'landing_pages'
			);
		}

		// Call parent initialization function.
		parent::init();

	}

	/**
	 * Override the base class' get method, to include returning legacy forms
	 * that might be stored in `legacy_settings_name`.
	 *
	 * @since   2.5.0
	 *
	 * @return  array
	 */
	public function get() {

		// Don't mutate the underlying resources, so multiple calls to get()
		// with different order_by and order properties are supported.
		$resources = $this->resources;

		// Don't attempt sorting if no resources exist.
		if ( ! $this->exist() ) {
			return $resources;
		}

		// If legacy resources exist, merge them now.
		$resources = array_merge( $resources, $this->get_legacy() );

		// Return resources sorted by order_by and order.
		return $this->sort( $resources );

	}

	/**
	 * Returns an array of legacy forms that might have been cached when upgrading to 2.5.0
	 * by maybe_cache_legacy_resources().
	 * 
	 * @since 	2.5.0
	 * 
	 * @return 	array
	 */
	public function get_legacy() {

		$resources = get_option( $this->legacy_settings_name );

		if ( ! $resources ) {
			return array();
		}

		return $resources;

	}

	/**
	 * Returns the HTML/JS markup for the given Landing Page ID
	 *
	 * @since   1.9.6
	 *
	 * @param   int|string $id     Landing Page ID or Legacy Landing Page URL.
	 * @return  WP_Error|string
	 */
	public function get_html( $id ) {

		// Setup API.
		$settings  = new ConvertKit_Settings();
		$this->api = new ConvertKit_API(
			CONVERTKIT_OAUTH_CLIENT_ID,
			admin_url( 'options-general.php?page=_wp_convertkit_settings' ),
			$settings->get_access_token(),
			$settings->get_refresh_token(),
			$settings->debug_enabled(),
			'output_landing_page'
		);

		// If the ID is a URL, this is a Legacy Landing Page defined for use on this Page
		// in a Plugin version < 1.9.6.
		// 1.9.6+ always uses a Landing Page ID.
		if ( strstr( $id, 'http' ) ) {
			// Return Legacy Landing Page HTML for url property.
			return $this->api->get_landing_page_html( $id, $settings->debug_enabled() );
		}

		// Cast ID to integer.
		$id = absint( $id );

		// Bail if the resources are a WP_Error.
		if ( is_wp_error( $this->resources ) ) {
			return $this->resources;
		}

		// Bail if the resource doesn't exist.
		if ( ! isset( $this->resources[ $id ] ) ) {
			return new WP_Error(
				'convertkit_resource_landing_pages_get_html',
				sprintf(
					/* translators: ConvertKit Landing Page ID */
					__( 'ConvertKit Landing Page ID %s does not exist on ConvertKit.', 'convertkit' ),
					$id
				)
			);
		}

		// If the resource has a 'url' property, this is a Legacy Landing Page, and the 'url' should be used.
		if ( isset( $this->resources[ $id ]['url'] ) ) {
			// Return Legacy Landing Page HTML for url property.
			return $this->api->get_landing_page_html( $this->resources[ $id ]['url'], $settings->debug_enabled() );
		}

		// Return Landing Page HTML for embed_url property.
		return $this->api->get_landing_page_html( $this->resources[ $id ]['embed_url'], $settings->debug_enabled() );

	}

	/**
	 * Replaces the favicon in the given Landing Page HTML with the site icon specified
	 * in WordPress.
	 *
	 * If no site icon is specified in WordPress, returns the original Landing Page HTML.
	 *
	 * @since   2.3.0
	 *
	 * @param   string $html   Landing Page HTML.
	 * @return  string          Landing Page HTML
	 */
	public function replace_favicon( $html ) {

		// Get link rel and meta tags for site icon.
		$site_icon = $this->get_site_icon();

		// Bail if no site icon specified.
		if ( empty( $site_icon ) ) {
			return $html;
		}

		// Define the ConvertKit favicon tag that exists in Landing Pages.
		$convertkit_favicon_tag = '<link rel="shortcut icon" type="image/x-icon" href="https://pages.convertkit.com/templates/favicon.ico">';

		// If the ConvertKit favicon tag does not exist in the HTML, this is a legacy landing page, which doesn't specify a link rel="shortcut icon".
		if ( strpos( $html, $convertkit_favicon_tag ) === false ) {
			// Prepend the WordPress site icon tags imemdiately before the closing </head> tag.
			$html = str_replace(
				'</head>',
				$site_icon . "\n" . '</head>',
				$html
			);

			return $html;
		}

		// This is a standard landing page that contains link rel="shortcut icon".
		// Replace the link rel="shortcut icon" with the above.
		$html = str_replace(
			$convertkit_favicon_tag,
			$site_icon,
			$html
		);

		// Return.
		return $html;

	}

	/**
	 * Returns the output of the WordPress wp_site_icon() function, which returns
	 * the necessary link rel and meta tags to display this site's favicon.
	 *
	 * If no site icon is specified in WordPress, returns a blank string.
	 *
	 * @since   2.3.0
	 *
	 * @return  string
	 */
	private function get_site_icon() {

		ob_start();
		wp_site_icon();
		return trim( ob_get_clean() );

	}

}
