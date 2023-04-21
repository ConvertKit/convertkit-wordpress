<?php
/**
 * ConvertKit Form Trigger Button Block class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * ConvertKit Form Trigger Button Block for Gutenberg and Shortcode.
 *
 * @package ConvertKit
 * @author  ConvertKit
 */
class ConvertKit_Block_Form_Trigger extends ConvertKit_Block {

	/**
	 * Constructor
	 *
	 * @since   2.2.0
	 */
	public function __construct() {

		// Register this as a shortcode in the ConvertKit Plugin.
		add_filter( 'convertkit_shortcodes', array( $this, 'register' ) );

		// Register this as a Gutenberg block in the ConvertKit Plugin.
		add_filter( 'convertkit_blocks', array( $this, 'register' ) );

		// Enqueue scripts and styles for this Gutenberg Block in the editor view.
		add_action( 'convertkit_gutenberg_enqueue_scripts', array( $this, 'enqueue_scripts_editor' ) );

		// Enqueue scripts and styles for this Gutenberg Block in the editor and frontend views.
		add_action( 'convertkit_gutenberg_enqueue_styles_editor_and_frontend', array( $this, 'enqueue_styles' ) );

	}

	/**
	 * Enqueues scripts for this Gutenberg Block in the editor view.
	 *
	 * @since   2.2.0
	 */
	public function enqueue_scripts_editor() {

		wp_enqueue_script( 'convertkit-gutenberg-block-form-trigger', CONVERTKIT_PLUGIN_URL . 'resources/backend/js/gutenberg-block-form-trigger.js', array( 'convertkit-gutenberg' ), CONVERTKIT_PLUGIN_VERSION, true );

	}

	/**
	 * Enqueues styles for this Gutenberg Block in the editor and frontend views.
	 *
	 * @since   2.2.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( 'convertkit-button', CONVERTKIT_PLUGIN_URL . 'resources/frontend/css/button.css', array(), CONVERTKIT_PLUGIN_VERSION );

	}

	/**
	 * Returns this block's programmatic name, excluding the convertkit- prefix.
	 *
	 * @since   2.2.0
	 */
	public function get_name() {

		/**
		 * This will register as:
		 * - a shortcode, with the name [convertkit_formtrigger].
		 * - a Gutenberg block, with the name convertkit/formtrigger.
		 */
		return 'formtrigger';

	}

	/**
	 * Returns this block's Title, Icon, Categories, Keywords and properties.
	 *
	 * @since   2.2.0
	 */
	public function get_overview() {

		return array(
			'title'                             => __( 'ConvertKit Form Trigger', 'convertkit' ),
			'description'                       => __( 'Displays a modal, sticky bar or slide in form to display when the button is pressed.', 'convertkit' ),
			'icon'                              => 'resources/backend/images/block-icon-formtrigger.png',
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
				'height' => 290,
			),

			// Shortcode: Include a closing [/shortcode] tag when using TinyMCE or QuickTag Modals.
			'shortcode_include_closing_tag'     => false,

			// Gutenberg: Block Icon in Editor.
			'gutenberg_icon'                => file_get_contents( CONVERTKIT_PLUGIN_PATH . '/resources/backend/images/block-icon-formtrigger.svg' ), /* phpcs:ignore */

			// Gutenberg: Example image showing how this block looks when choosing it in Gutenberg.
			'gutenberg_example_image'           => CONVERTKIT_PLUGIN_URL . 'resources/backend/images/block-example-formtrigger.png',

			// Gutenberg: Help description, displayed when no settings defined for a newly added Block.
			'gutenberg_help_description'        => __( 'Select a Form using the Form option in the Gutenberg sidebar.', 'convertkit' ),

			// Gutenberg: JS function to call when rendering the block preview in the Gutenberg editor.
			// If not defined, render_callback above will be used.
			'gutenberg_preview_render_callback' => 'convertKitGutenbergFormTriggerBlockRenderPreview',
		);

	}

	/**
	 * Returns this block's Attributes
	 *
	 * @since   2.2.0
	 */
	public function get_attributes() {

		return array(
			// Block attributes.
			'form'                 => array(
				'type'    => 'string',
				'default' => $this->get_default_value( 'form' ),
			),
			'text'                 => array(
				'type'    => 'string',
				'default' => $this->get_default_value( 'text' ),
			),

			// The below are built in Gutenberg attributes registered in get_supports().

			// Color.
			'backgroundColor'      => array(
				'type' => 'string',
			),
			'textColor'            => array(
				'type' => 'string',
			),

			// Typography.
			'fontSize'             => array(
				'type' => 'string',
			),

			// Spacing/Dimensions > Padding.
			'style'                => array(
				'type'        => 'object',
				'visualizers' => array(
					'type'    => 'object',
					'padding' => array(
						'type'   => 'object',
						'top'    => array(
							'type' => 'boolean',
						),
						'bottom' => array(
							'type' => 'boolean',
						),
						'left'   => array(
							'type' => 'boolean',
						),
						'right'  => array(
							'type' => 'boolean',
						),
					),
				),
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
	 * @since   2.2.0
	 *
	 * @return  array   Supports
	 */
	public function get_supports() {

		return array(
			'className'  => true,
			'color'      => array(
				'background' => true,
				'text'       => true,
			),
			'typography' => array(
				'fontSize' => true,
			),
			'spacing'    => array(
				'padding' => array(
					'horizontal',
					'vertical',
				),
			),
		);

	}

	/**
	 * Returns this block's Fields
	 *
	 * @since   2.2.0
	 *
	 * @return  bool|array
	 */
	public function get_fields() {

		// Bail if the request is not for the WordPress Administration or frontend editor.
		if ( ! WP_ConvertKit()->is_admin_or_frontend_editor() ) {
			return false;
		}

		// Get ConvertKit Forms.
		$forms            = array();
		$convertkit_forms = new ConvertKit_Resource_Forms( 'block_edit' );
		if ( $convertkit_forms->exist() ) {
			foreach ( $convertkit_forms->get() as $form ) {
				// Ignore inline forms; this button link is only for modal, slide in and sticky bar forms.
				if ( ! array_key_exists( 'format', $form ) ) {
					continue;
				}
				if ( $form['format'] === 'inline' ) {
					continue;
				}

				// Ignore forms that are missing a uid.
				if ( ! array_key_exists( 'uid', $form ) ) {
					continue;
				}

				$forms[ absint( $form['id'] ) ] = sanitize_text_field( $form['name'] );
			}
		}

		// Gutenberg's built-in fields (such as styling, padding etc) don't need to be defined here, as they'll be included
		// automatically by Gutenberg.
		return array(
			'form'             => array(
				'label'       => __( 'Form', 'convertkit' ),
				'type'        => 'select',
				'values'      => $forms,
				'description' => __( 'The modal, sticky bar or slide in form to display when the button is pressed. To embed a form, use the ConvertKit Form block instead.', 'convertkit' ),
			),
			'text'             => array(
				'label'       => __( 'Button Text', 'convertkit' ),
				'type'        => 'text',
				'description' => __( 'The text to display for the button.', 'convertkit' ),
			),

			// These fields will only display on the shortcode, and are deliberately not registered in get_attributes(),
			// because Gutenberg will register its own color pickers for link, background and text.
			'background_color' => array(
				'label' => __( 'Background color', 'convertkit' ),
				'type'  => 'color',
			),
			'text_color'       => array(
				'label' => __( 'Text color', 'convertkit' ),
				'type'  => 'color',
			),
		);

	}

	/**
	 * Returns this block's UI panels / sections.
	 *
	 * @since   2.2.0
	 *
	 * @return  bool|array
	 */
	public function get_panels() {

		// Bail if the request is not for the WordPress Administration or frontend editor.
		if ( ! WP_ConvertKit()->is_admin_or_frontend_editor() ) {
			return false;
		}

		// Gutenberg's built-in fields (such as styling, padding etc) don't need to be defined here, as they'll be included
		// automatically by Gutenberg.
		return array(
			'general' => array(
				'label'  => __( 'General', 'convertkit' ),
				'fields' => array(
					'form',
					'text',
					'background_color',
					'text_color',
				),
			),
		);

	}

	/**
	 * Returns this block's Default Values
	 *
	 * @since   2.2.0
	 *
	 * @return  array
	 */
	public function get_default_values() {

		return array(
			'form'             => '',
			'text'             => __( 'Subscribe', 'convertkit' ),
			'background_color' => '',
			'text_color'       => '',

			// Built-in Gutenberg block attributes.
			'backgroundColor'  => '',
			'textColor'        => '',
			'fontSize'         => '',
			'style'            => array(
				'visualizers' => array(
					'padding' => array(
						'top'    => '',
						'bottom' => '',
						'left'   => '',
						'right'  => '',
					),
				),
			),
		);

	}

	/**
	 * Returns the block's output, based on the supplied configuration attributes.
	 *
	 * @since   2.2.0
	 *
	 * @param   array $atts   Block / Shortcode Attributes.
	 * @return  string          Output
	 */
	public function render( $atts ) {

		// Parse attributes, defining fallback defaults if required
		// and moving some attributes (such as Gutenberg's styles), if defined.
		$atts = $this->sanitize_and_declare_atts( $atts );

		// Setup Settings class.
		$settings = new ConvertKit_Settings();

		// Build HTML.
		$html = $this->get_html( $atts['form'], $atts['text'], $atts['_css_classes'], $atts['_css_styles'], $this->is_block_editor_request() );

		// Bail if an error occured.
		if ( is_wp_error( $html ) ) {
			if ( $settings->debug_enabled() ) {
				return '<!-- ' . $html->get_error_message() . ' -->';
			}

			return '';
		}

		/**
		 * Filter the block's content immediately before it is output.
		 *
		 * @since   2.2.0
		 *
		 * @param   string  $html   ConvertKit Button HTML.
		 * @param   array   $atts   Block Attributes.
		 */
		$html = apply_filters( 'convertkit_block_form_trigger_render', $html, $atts );

		return $html;

	}

	/**
	 * Returns the HTML button markup for the given Form ID.
	 *
	 * @since   2.0.0
	 *
	 * @param   int    $id             Form ID.
	 * @param   string $button_text    Button Text.
	 * @param   array  $css_classes    CSS classes to apply to link (typically included when using Gutenberg).
	 * @param   array  $css_styles     CSS inline styles to apply to link (typically included when using Gutenberg).
	 * @param   bool   $return_as_span If true, returns a <span> instead of <a>. Useful for the block editor so that the element is interactible.
	 * @return  WP_Error|string        Button HTML
	 */
	private function get_html( $id, $button_text, $css_classes = array(), $css_styles = array(), $return_as_span = false ) {

		// Cast ID to integer.
		$id = absint( $id );

		// Load classes.
		$convertkit_forms = new ConvertKit_Resource_Forms( 'render' );

		// Get form.
		$form = $convertkit_forms->get_by_id( $id );

		// Bail if the form could not be found.
		if ( ! $form ) {
			return new WP_Error(
				'convertkit_block_form_trigger_get_html',
				sprintf(
					/* translators: ConvertKit Form ID */
					__( 'ConvertKit Form ID %s does not exist on ConvertKit.', 'convertkit' ),
					$id
				)
			);
		}

		// Bail if no uid or embed_js properties exist.
		if ( ! array_key_exists( 'uid', $form ) ) {
			return new WP_Error(
				'convertkit_block_form_trigger_get_html',
				sprintf(
					/* translators: ConvertKit Form ID */
					__( 'ConvertKit Form ID %s has no uid property.', 'convertkit' ),
					$id
				)
			);
		}
		if ( ! array_key_exists( 'embed_js', $form ) ) {
			return new WP_Error(
				'convertkit_block_form_trigger_get_html',
				sprintf(
					/* translators: ConvertKit Form ID */
					__( 'ConvertKit Form ID %s has no embed_js property.', 'convertkit' ),
					$id
				)
			);
		}

		// Build button HTML.
		$html = '<div class="convertkit-button">';

		if ( $return_as_span ) {
			$html .= '<span';
		} else {
			$html .= '<a data-formkit-toggle="' . esc_attr( $form['uid'] ) . '" href="' . esc_attr( $form['embed_url'] ) . '"';
		}

		$html .= ' class="wp-block-button__link ' . implode( ' ', map_deep( $css_classes, 'sanitize_html_class' ) ) . '" style="' . implode( ';', map_deep( $css_styles, 'esc_attr' ) ) . '">';
		$html .= esc_html( $button_text );

		if ( $return_as_span ) {
			$html .= '</span>';
		} else {
			$html .= '</a>';
		}

		$html .= '</div>';

		// Register the script, so it's only loaded once for this non-inline form across the entire page.
		add_filter(
			'convertkit_output_scripts_footer',
			function( $scripts ) use ( $form ) {

				$scripts[] = array(
					'async'    => true,
					'data-uid' => $form['uid'],
					'src'      => $form['embed_js'],
				);

				return $scripts;

			}
		);

		// Return.
		return $html;
	}

}
