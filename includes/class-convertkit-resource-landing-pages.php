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

		// Initialize the API if the API Key and Secret have been defined in the Plugin Settings.
		$settings = new ConvertKit_Settings();
		if ( $settings->has_api_key_and_secret() ) {
			$this->api = new ConvertKit_API(
				$settings->get_api_key(),
				$settings->get_api_secret(),
				$settings->debug_enabled(),
				$context
			);
		}

		// Call parent initialization function.
		parent::init();

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
		$api = new ConvertKit_API( false, false, true, 'output_landing_page' );

		// If the ID is a URL, this is a Legacy Landing Page defined for use on this Page
		// in a Plugin version < 1.9.6.
		// 1.9.6+ always uses a Landing Page ID.
		if ( strstr( $id, 'http' ) ) {
			// Return Legacy Landing Page HTML for url property.
			return $api->get_landing_page_html( $id );
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
			return $api->get_landing_page_html( $this->resources[ $id ]['url'] );
		}

		// Return Landing Page HTML for embed_url property.
		return $api->get_landing_page_html( $this->resources[ $id ]['embed_url'] );

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
