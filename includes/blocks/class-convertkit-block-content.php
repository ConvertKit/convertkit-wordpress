<?php
/**
 * ConvertKit Custom Content Block class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * ConvertKit Custom Content Block for Gutenberg and Shortcode.
 *
 * @package ConvertKit
 * @author  ConvertKit
 */
class ConvertKit_Block_Content extends ConvertKit_Block {

	/**
	 * Constructor
	 *
	 * @since   1.9.6
	 */
	public function __construct() {

		// Register this as a shortcode in the ConvertKit Plugin.
		add_filter( 'convertkit_shortcodes', array( $this, 'register' ) );

	}

	/**
	 * Returns this block's programmatic name, excluding the convertkit- prefix.
	 *
	 * @since   1.9.6
	 *
	 * @return  string
	 */
	public function get_name() {

		/**
		 * This will register as:
		 * - a shortcode, with the name [convertkit_content]
		 * - a Gutenberg block, with the name convertkit/content
		 */
		return 'content';

	}

	/**
	 * Returns this block's Title, Icon, Categories, Keywords and properties.
	 *
	 * @since   1.9.6
	 *
	 * @return  array
	 */
	public function get_overview() {

		return array(
			'title'                         => __( 'ConvertKit Custom Content', 'convertkit' ),
			'description'                   => __( 'Displays ConvertKit Custom Content for a subscriber if their tag matches the Page\'s tag.', 'convertkit' ),
			'icon'                          => 'resources/backend/images/block-icon-content.png',
			'category'                      => 'convertkit',
			'keywords'                      => array(
				__( 'ConvertKit', 'convertkit' ),
				__( 'Content', 'convertkit' ),
			),

			// TinyMCE / QuickTags Modal Width and Height.
			'modal'                         => array(
				'width'  => 500,
				'height' => 100,
			),

			'shortcode_include_closing_tag' => true,

			// Function to call when rendering the block/shortcode on the frontend web site.
			'render_callback'               => array( $this, 'render' ),
		);

	}

	/**
	 * Returns this block's Attributes
	 *
	 * @since   1.9.6.5
	 *
	 * @return  array
	 */
	public function get_attributes() {

		return array(
			'tag' => array(
				'type' => 'string',
			),
		);

	}

	/**
	 * Returns this block's Fields
	 *
	 * @since   1.9.6
	 *
	 * @return  mixed   bool | array
	 */
	public function get_fields() {

		// Bail if the request is not for the WordPress Administration or frontend editor.
		if ( ! WP_ConvertKit()->is_admin_or_frontend_editor() ) {
			return false;
		}

		// Get ConvertKit Tags.
		$tags            = array();
		$convertkit_tags = new ConvertKit_Resource_Tags();
		if ( $convertkit_tags->exist() ) {
			foreach ( $convertkit_tags->get() as $tag ) {
				$tags[ absint( $tag['id'] ) ] = sanitize_text_field( $tag['name'] );
			}
		}

		return array(
			'tag' => array(
				'label'  => __( 'Tag', 'convertkit' ),
				'type'   => 'select',
				'values' => $tags,
			),
		);

	}

	/**
	 * Returns this block's UI panels / sections.
	 *
	 * @since   1.9.6
	 *
	 * @return  mixed   bool | array
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
					'tag',
				),
			),
		);

	}

	/**
	 * Returns this block's Default Values
	 *
	 * @since   1.9.6
	 *
	 * @return  array
	 */
	public function get_default_values() {

		return array(
			'tag' => '',
		);

	}

	/**
	 * Returns the block's output, based on the supplied configuration attributes.
	 *
	 * @since   1.9.6
	 *
	 * @param   array  $atts       Block / Shortcode Attributes.
	 * @param   string $content    Content.
	 * @return  string              Output
	 */
	public function render( $atts, $content = '' ) {

		// Bail if the request is not for the frontend site.
		if ( is_admin() ) {
			return '';
		}

		// Parse shortcode attributes, defining fallback defaults if required.
		$atts = shortcode_atts(
			$this->get_default_values(),
			$this->sanitize_atts( $atts ),
			$this->get_name()
		);

		// Setup Settings class.
		$settings = new ConvertKit_Settings();

		// Bail if the tag isn't specified.
		if ( ! $atts['tag'] ) {
			if ( $settings->debug_enabled() ) {
				return '<!-- ConvertKit Custom Content: No Tag Specified -->';
			}

			return '';
		}

		// Bail if there is no subscriber ID from the cookie or request.
		$subscriber_id = WP_ConvertKit()->get_class( 'output' )->get_subscriber_id_from_request();
		if ( ! $subscriber_id ) {
			if ( $settings->debug_enabled() ) {
				return '<!-- ConvertKit Custom Content: Subscriber ID does not exist -->';
			}

			return '';
		}

		// Bail if the API hasn't been configured.
		$settings = new ConvertKit_Settings();
		if ( ! $settings->has_api_key_and_secret() ) {
			if ( $settings->debug_enabled() ) {
				return '<!-- ConvertKit Custom Content: No API Key and Secret -->';
			}

			return '';
		}

		// Initialize the API.
		$api = new ConvertKit_API( $settings->get_api_key(), $settings->get_api_secret(), $settings->debug_enabled() );

		// Get the subscriber's tags, to see if they subscribed to this tag.
		$tags = $api->get_subscriber_tags( $subscriber_id );

		// Bail if an error occured i.e. the subscriber has no tags.
		if ( is_wp_error( $tags ) ) {
			if ( $settings->debug_enabled() ) {
				return '<!-- ConvertKit Custom Content: ' . $tags->get_error_message() . ' -->';
			}

			return '';
		}

		// Iterate through ConvertKit Tags to find a match.
		foreach ( $tags as $tag ) {
			// Skip if this ConvertKit Tag isn't the Tag specified in the block.
			if ( absint( $tag['id'] ) !== absint( $atts['tag'] ) ) {
				continue;
			}

			/**
			 * Filters the content in the ConvertKit Custom Content block/shortcode
			 * immediately before it is output.
			 *
			 * @since   1.9.6
			 *
			 * @param   string  $content        Content
			 * @param   array   $atts           Block / Shortcode Attributes
			 * @param   int     $subscriber_id  ConvertKit Subscriber's ID
			 * @param   array   $tags           ConvertKit Subscriber's Tags
			 * @param   array   $tag            ConvertKit Subscriber's Tag that matches $atts['tag']
			 */
			$content = apply_filters( 'convertkit_block_content_render', $content, $atts, $subscriber_id, $tags, $tag );

			/**
			 * Backward compat. filter for < 1.9.6. Filters the content in the ConvertKit Custom Content block/shortcode
			 * immediately before it is output.
			 *
			 * @since   1.0.0
			 *
			 * @param   string  $content        Content
			 * @param   array   $atts           Block / Shortcode Attributes
			 */
			$content = apply_filters( 'wp_convertkit_shortcode_custom_content', $content, $atts );

			return $content;
		}

		// If here, the subscriber does not have the block's tag.
		if ( $settings->debug_enabled() ) {
			return '<!-- ConvertKit Custom Content: Subscriber does not have tag -->';
		}

		return '';

	}

}
