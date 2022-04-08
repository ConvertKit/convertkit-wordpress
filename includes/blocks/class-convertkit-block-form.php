<?php
/**
 * ConvertKit Form Block class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * ConvertKit Form Block for Gutenberg and Shortcode.
 *
 * @package ConvertKit
 * @author  ConvertKit
 */
class ConvertKit_Block_Form extends ConvertKit_Block {

	/**
	 * Constructor
	 *
	 * @since   1.9.6
	 */
	public function __construct() {

		// Register this as a shortcode in the ConvertKit Plugin.
		add_filter( 'convertkit_shortcodes', array( $this, 'register' ) );

		// Register this as a Gutenberg block in the ConvertKit Plugin.
		add_filter( 'convertkit_blocks', array( $this, 'register' ) );

		// Register additional scripts for this Gutenberg Block.
		add_action( 'convertkit_gutenberg_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Register additional stylesheets for this Gutenberg Block.
		add_action( 'convertkit_gutenberg_enqueue_styles', array( $this, 'enqueue_styles' ) );

	}

	/**
	 * Enqueues additional scripts for this Gutenberg Block.
	 *
	 * @since   1.9.6.5
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'convertkit-gutenberg-block-form', CONVERTKIT_PLUGIN_URL . '/resources/backend/js/gutenberg-block-form.js', array( 'convertkit-gutenberg' ), CONVERTKIT_PLUGIN_VERSION, true );

	}

	/**
	 * Enqueues additional scripts for this Gutenberg Block.
	 *
	 * @since   1.9.6.9
	 */
	public function enqueue_styles() {

		wp_enqueue_style( 'convertkit-gutenberg-block-form', CONVERTKIT_PLUGIN_URL . '/resources/backend/css/gutenberg-block-form.css', array( 'wp-edit-blocks' ), CONVERTKIT_PLUGIN_VERSION );

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
		 * - a shortcode, with the name [convertkit_form].
		 * - a shortcode, with the name [convertkit], for backward compat.
		 * - a Gutenberg block, with the name convertkit/form.
		 */
		return 'form';

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
			'title'                             => __( 'ConvertKit Form', 'convertkit' ),
			'description'                       => __( 'Displays a ConvertKit Form.', 'convertkit' ),
			'icon'                              => 'resources/backend/images/block-icon-form.png',
			'category'                          => 'convertkit',
			'keywords'                          => array(
				__( 'ConvertKit', 'convertkit' ),
				__( 'Form', 'convertkit' ),
			),

			// Function to call when rendering as a block or a shortcode on the frontend web site.
			'render_callback'                   => array( $this, 'render' ),

			// Shortcode: TinyMCE / QuickTags Modal Width and Height.
			'modal'                             => array(
				'width'  => 500,
				'height' => 100,
			),

			// Shortcode: Include a closing [/shortcode] tag when using TinyMCE or QuickTag Modals.
			'shortcode_include_closing_tag'     => false,

			// Gutenberg: Block Icon in Editor.
			'gutenberg_icon'                    => file_get_contents( CONVERTKIT_PLUGIN_PATH . '/resources/backend/images/block-icon-form.svg' ), /* phpcs:ignore */

			// Gutenberg: Example image showing how this block looks when choosing it in Gutenberg.
			'gutenberg_example_image'           => CONVERTKIT_PLUGIN_URL . '/resources/backend/images/block-example-form.png',

			// Gutenberg: Help description, displayed when no settings defined for a newly added Block.
			'gutenberg_help_description'        => __( 'Select a Form using the Form option in the Gutenberg sidebar.', 'convertkit' ),

			// Gutenberg: JS function to call when rendering the block preview in the Gutenberg editor.
			// If not defined, render_callback above will be used.
			'gutenberg_preview_render_callback' => 'convertKitGutenbergFormBlockRenderPreview',

			// General: Any other strings for use in JS that need to support translation / i18n.
			'i18n'                              => array(
				/* translators: Form name in ConvertKit */
				'gutenberg_form_modal'      => __( 'Modal form "%s" selected. View on the frontend site to see the modal form.', 'convertkit' ),

				/* translators: Form name in ConvertKit */
				'gutenberg_form_slide_in'   => __( 'Slide in form "%s" selected. View on the frontend site to see the slide in form.', 'convertkit' ),

				/* translators: Form name in ConvertKit */
				'gutenberg_form_sticky_bar' => __( 'Sticky bar form "%s" selected. View on the frontend site to see the sticky bar form.', 'convertkit' ),
			),
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
			'form'                 => array(
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

		// Get ConvertKit Forms.
		$forms            = array();
		$convertkit_forms = new ConvertKit_Resource_Forms();
		if ( $convertkit_forms->exist() ) {
			foreach ( $convertkit_forms->get() as $form ) {
				$forms[ absint( $form['id'] ) ] = sanitize_text_field( $form['name'] );
			}
		}

		// Get Settings.
		$settings = new ConvertKit_Settings();

		return array(
			'form' => array(
				'label'  => __( 'Form', 'convertkit' ),
				'type'   => 'select',
				'values' => $forms,
				'data'   => array(
					'forms'   => $convertkit_forms->get(),
					'api_key' => $settings->get_api_key(),
				),
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
					'form',
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
			'form' => '',
			'id'   => '', // Backward compat.
		);

	}

	/**
	 * Returns the block's output, based on the supplied configuration attributes.
	 *
	 * @since   1.9.6
	 *
	 * @param   array $atts   Block / Shortcode Attributes.
	 * @return  string          Output
	 */
	public function render( $atts ) {

		// Parse shortcode attributes, defining fallback defaults if required.
		$atts = shortcode_atts(
			$this->get_default_values(),
			$this->sanitize_atts( $atts ),
			$this->get_name()
		);

		// Setup Settings class.
		$settings = new ConvertKit_Settings();

		// Determine Form ID.
		// 'id' attribute is for backward compat.
		$form_id = 0;
		if ( $atts['form'] > 0 ) {
			$form_id = $atts['form'];
		} elseif ( $atts['id'] > 0 ) {
			$form_id = $atts['id'];
		}

		// If no Form ID specified, bail.
		if ( ! $form_id ) {
			if ( $settings->debug_enabled() ) {
				return '<!-- No Form ID Specified  -->';
			}

			return '';
		}

		// Get Form HTML.
		$forms = new ConvertKit_Resource_Forms();
		$form  = $forms->get_html( $form_id );

		// If an error occured, it might be that we're requesting a Form ID that exists in ConvertKit
		// but does not yet exist in the Plugin's Form Resources.
		// If so, refresh the Form Resources and try again.
		if ( is_wp_error( $form ) ) {
			// Refresh Forms from the API.
			$forms->refresh();

			// Get Form HTML again.
			$form = $forms->get_html( $form_id );
		}

		// If an error still occured, the shortcode might be from the ConvertKit App for a Legacy Form ID
		// These ConvertKit App shortcodes, for some reason, use a different Form ID than the one presented
		// to us in the API.
		// For example, a Legacy Form ID might be 470099, but the ConvertKit app says to use the shortcode [convertkit form=5281783]).
		// In this instance, fetch the Form HTML without checking that the Form ID exists in the Form Resources.
		if ( is_wp_error( $form ) ) {
			// Initialize the API.
			$api = new ConvertKit_API( $settings->get_api_key(), $settings->get_api_secret(), $settings->debug_enabled() );

			// Return Legacy Form HTML from the API, which bypasses any internal Plugin check to see if the Form ID exists.
			$form = $api->get_form_html( $form_id );
		}

		// Finally, if we still get an error, there's nothing more we can do. The Form ID isn't valid.
		if ( is_wp_error( $form ) ) {
			if ( $settings->debug_enabled() ) {
				return '<!-- ' . $form->get_error_message() . ' -->';
			}

			return '';
		}

		/**
		 * Filter the block's content immediately before it is output.
		 *
		 * @since   1.9.6
		 *
		 * @param   string  $form   ConvertKit Form HTML.
		 * @param   array   $atts   Block Attributes.
		 */
		$form = apply_filters( 'convertkit_block_form_render', $form, $atts );

		/**
		 * Backward compat. filter for < 1.9.6. Filter the block's content immediately before it is output.
		 *
		 * @since   1.0.0
		 *
		 * @param   string  $form   ConvertKit Form HTML.
		 * @param   array   $atts   Block Attributes.
		 */
		$form = apply_filters( 'wp_convertkit_get_form_embed', $form, $atts );

		return $form;

	}

}
