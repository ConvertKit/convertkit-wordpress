<?php
/**
 * ConvertKit Broadcasts List Block class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * ConvertKit Broadcasts List Block for Gutenberg and Shortcode.
 *
 * @package ConvertKit
 * @author  ConvertKit
 */
class ConvertKit_Block_Broadcasts extends ConvertKit_Block {

	/**
	 * Constructor
	 *
	 * @since   1.9.7.4
	 */
	public function __construct() {

		// Register this as a shortcode in the ConvertKit Plugin.
		add_filter( 'convertkit_shortcodes', array( $this, 'register' ) );

		// Register this as a Gutenberg block in the ConvertKit Plugin.
		add_filter( 'convertkit_blocks', array( $this, 'register' ) );

		// Enqueue stylesheets for this Gutenberg block.
		add_action( 'convertkit_gutenberg_enqueue_styles', array( $this, 'enqueue_styles' ) ); // Editor.
		add_action( 'enqueue_block_assets', array( $this, 'enqueue_styles' ) ); // Frontend.

	}

	/**
	 * Enqueues CSS for this block.
	 *
	 * @since   1.9.7.4
	 */
	public function enqueue_styles() {

		// Don't load styles if the Disable CSS option is on.
		$settings = new ConvertKit_Settings();
		if ( $settings->css_disabled() ) {
			return;
		}

		wp_enqueue_style( 'convertkit-' . $this->get_name(), CONVERTKIT_PLUGIN_URL . 'resources/frontend/css/gutenberg-block-broadcasts.css', array(), CONVERTKIT_PLUGIN_VERSION );

	}

	/**
	 * Returns this block's programmatic name, excluding the convertkit- prefix.
	 *
	 * @since   1.9.7.4
	 */
	public function get_name() {

		/**
		 * This will register as:
		 * - a shortcode, with the name [convertkit_broadcasts].
		 * - a Gutenberg block, with the name convertkit/broadcasts.
		 */
		return 'broadcasts';

	}

	/**
	 * Returns this block's Title, Icon, Categories, Keywords and properties.
	 *
	 * @since   1.9.7.4
	 */
	public function get_overview() {

		return array(
			'title'                         => __( 'ConvertKit Broadcasts', 'convertkit' ),
			'description'                   => __( 'Displays a list of your ConvertKit broadcasts.', 'convertkit' ),
			'icon'                          => 'resources/backend/images/block-icon-broadcasts.png',
			'category'                      => 'convertkit',
			'keywords'                      => array(
				__( 'ConvertKit', 'convertkit' ),
				__( 'Broadcasts', 'convertkit' ),
				__( 'Posts', 'convertkit' ),
			),

			// Function to call when rendering as a block or a shortcode on the frontend web site.
			'render_callback'               => array( $this, 'render' ),

			// Shortcode: TinyMCE / QuickTags Modal Width and Height.
			'modal'                         => array(
				'width'  => 500,
				'height' => 100,
			),

			// Shortcode: Include a closing [/shortcode] tag when using TinyMCE or QuickTag Modals.
			'shortcode_include_closing_tag' => false,

			// Gutenberg: Block Icon in Editor.
			'gutenberg_icon'                    => file_get_contents( CONVERTKIT_PLUGIN_PATH . '/resources/backend/images/block-icon-broadcasts.svg' ), /* phpcs:ignore */

			// Gutenberg: Example image showing how this block looks when choosing it in Gutenberg.
			'gutenberg_example_image'       => CONVERTKIT_PLUGIN_URL . '/resources/backend/images/block-example-broadcasts.png',

			// Gutenberg: Help description, displayed when no settings defined for a newly added Block.
			'gutenberg_help_description'    => __( 'Define this Block\'s settings in the Gutenberg sidebar to display a list of your broadcasts.', 'convertkit' ),
		);

	}

	/**
	 * Returns this block's Attributes
	 *
	 * @since   1.9.7.4
	 */
	public function get_attributes() {

		return array(
			// Block attributes.
			'date_format'          => array(
				'type'    => 'string',
				'default' => $this->get_default_value( 'date_format' ),
			),
			'limit'                => array(
				'type'    => 'number',
				'default' => $this->get_default_value( 'limit' ),
			),

			// get_supports() color attribute.
			'style'                => array(
				'type' => 'object',
			),
			'backgroundColor'      => array(
				'type' => 'string',
			),
			'textColor'            => array(
				'type' => 'string',
			),

			// Always required for Gutenberg.
			'is_gutenberg_example' => array(
				'type'    => 'boolean',
				'default' => false,
			),
		);

	}

	/**
	 * Returns this block's supported built-in Attributes.
	 *
	 * @since   1.9.7.4
	 *
	 * @return  array   Supports
	 */
	public function get_supports() {

		return array(
			'className' => true,
			'color'     => array(
				'link'       => true,
				'background' => true,
				'text'       => true,
			),
		);

	}

	/**
	 * Returns this block's Fields
	 *
	 * @since   1.9.7.4
	 *
	 * @return  bool|array
	 */
	public function get_fields() {

		// Bail if the request is not for the WordPress Administration or frontend editor.
		if ( ! WP_ConvertKit()->is_admin_or_frontend_editor() ) {
			return false;
		}

		return array(
			'date_format' => array(
				'label'  => __( 'Date format', 'convertkit' ),
				'type'   => 'select',
				'values' => array(
					'F j, Y' => date_i18n( 'F j, Y', strtotime( 'now' ) ),
					'Y-m-d'  => date_i18n( 'Y-m-d', strtotime( 'now' ) ),
					'm/d/Y'  => date_i18n( 'm/d/Y', strtotime( 'now' ) ),
					'd/m/Y'  => date_i18n( 'd/m/Y', strtotime( 'now' ) ),
				),
			),
			'limit'       => array(
				'label' => __( 'Number of posts', 'convertkit' ),
				'type'  => 'number',
				'min'   => 0,
				'max'   => 999,
				'step'  => 1,
			),
		);

	}

	/**
	 * Returns this block's UI panels / sections.
	 *
	 * @since   1.9.7.4
	 *
	 * @return  bool|array
	 */
	public function get_panels() {

		// Bail if the request is not for the WordPress Administration or frontend editor.
		if ( ! WP_ConvertKit()->is_admin_or_frontend_editor() ) {
			return false;
		}

		return array(
			'general' => array(
				'label'  => __( 'General', 'convertkit' ),
				'fields' => array(
					'date_format',
					'limit',
				),
			),
		);

	}

	/**
	 * Returns this block's Default Values
	 *
	 * @since   1.9.7.4
	 *
	 * @return  array
	 */
	public function get_default_values() {

		return array(
			'date_format'     => 'F j, Y',
			'limit'           => 10,

			// Built-in Gutenberg block attributes.
			'style'           => '',
			'backgroundColor' => '',
			'textColor'       => '',
		);

	}

	/**
	 * Returns the block's output, based on the supplied configuration attributes.
	 *
	 * @since   1.9.7.4
	 *
	 * @param   array $atts   Block / Shortcode Attributes.
	 * @return  string          Output
	 */
	public function render( $atts ) {

		// Parse shortcode attributes, defining fallback defaults if required
		// and moving some attributes (such as Gutenberg's styles), if defined.
		$atts = $this->sanitize_and_declare_atts( $atts );

		// Setup Settings class.
		$settings = new ConvertKit_Settings();

		// Fetch Posts.
		$posts = new ConvertKit_Resource_Posts();

		// If this is an admin request, refresh the Posts resource now from the API,
		// as it's an inexpensive query of ~ 0.5 seconds when we're editing a Page
		// containing this block.
		if ( function_exists( 'is_admin' ) && is_admin() ) {
			$posts->refresh();
		}

		// If no Posts exist, bail.
		if ( ! $posts->exist() ) {
			if ( $settings->debug_enabled() ) {
				return '<!-- ' . __( 'No Broadcasts exist in ConvertKit.', 'convertkit' ) . ' -->';
			}

			return '';
		}

		// Build array of broadcasts to include in the output, based on the attributes.
		$broadcasts = array();
		if ( $atts['limit'] > 0 ) {
			$broadcasts = array_slice( $posts->get(), 0, $atts['limit'] );
		} else {
			$broadcasts = $posts->get();
		}

		// Build HTML.
		$html = $this->build_html( $broadcasts, $atts );

		/**
		 * Filter the block's content immediately before it is output.
		 *
		 * @since   1.9.7.4
		 *
		 * @param   string  $html   ConvertKit Broadcasts HTML.
		 * @param   array   $atts   Block Attributes.
		 */
		$html = apply_filters( 'convertkit_block_broadcasts_render', $html, $atts );

		return $html;

	}

	/**
	 * Helper function to determine if the request is a REST API request.
	 *
	 * @since   1.9.7.4
	 *
	 * @return  bool    Is REST API Request
	 */
	private function is_rest_api_request() {

		if ( ! defined( 'REST_REQUEST' ) ) {
			return false;
		}

		if ( ! REST_REQUEST ) {
			return false;
		}

		return true;

	}

	/**
	 * Returns HTML for the given array of ConvertKit broadcasts.
	 *
	 * @since   1.9.7.4
	 *
	 * @param   array $broadcasts     Broadcasts.
	 * @param   array $atts           Block attributes.
	 * @return  string                  HTML
	 */
	private function build_html( $broadcasts, $atts ) {

		// Start list.
		$html = '<ul class="' . esc_attr( implode( ' ', $atts['_css_classes'] ) ) . '" style="' . implode( ';', $atts['_css_styles'] ) . '">';

		// Iterate through broadcasts.
		foreach ( $broadcasts as $count => $broadcast ) {
			// Convert UTC date to timestamp.
			$date_timestamp = strtotime( $broadcast['published_at'] );

			// Add broadcast as list item.
			$html .= '<li class="convertkit-broadcast">
				<time datetime="' . date_i18n( 'Y-m-d', $date_timestamp ) . '">' . date_i18n( $atts['date_format'], $date_timestamp ) . '</time>
				<a href="' . $broadcast['url'] . '" target="_blank" rel="nofollow noopener">' . $broadcast['title'] . '</a>
			</li>';
		}

		// End list.
		$html .= '</ul>';

		return $html;

	}

}
