<?php
/**
 * ConvertKit Product Button Block class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * ConvertKit Product Button Block for Gutenberg and Shortcode.
 *
 * @package ConvertKit
 * @author  ConvertKit
 */
class ConvertKit_Block_Product extends ConvertKit_Block {

	/**
	 * Constructor
	 *
	 * @since   1.9.8.5
	 */
	public function __construct() {

		// Register this as a shortcode in the ConvertKit Plugin.
		add_filter( 'convertkit_shortcodes', array( $this, 'register' ) );

		// Register this as a Gutenberg block in the ConvertKit Plugin.
		add_filter( 'convertkit_blocks', array( $this, 'register' ) );

		// Enqueue scripts and styles for this Gutenberg Block in the editor view.
		add_action( 'convertkit_gutenberg_enqueue_scripts', array( $this, 'enqueue_scripts_editor' ) );

		// Enqueue scripts and styles for this Gutenberg Block in the editor and frontend views.
		add_action( 'convertkit_gutenberg_enqueue_scripts_editor_and_frontend', array( $this, 'enqueue_scripts' ) );
		add_action( 'convertkit_gutenberg_enqueue_styles_editor_and_frontend', array( $this, 'enqueue_styles' ) );

	}

	/**
	 * Enqueues scripts for this Gutenberg Block in the editor view.
	 *
	 * @since   1.9.8.5
	 */
	public function enqueue_scripts_editor() {

		wp_enqueue_script( 'convertkit-gutenberg-block-product', CONVERTKIT_PLUGIN_URL . 'resources/backend/js/gutenberg-block-product.js', array( 'convertkit-gutenberg' ), CONVERTKIT_PLUGIN_VERSION, true );

	}

	/**
	 * Enqueues scripts for this Gutenberg Block in the editor and frontend views.
	 *
	 * @since   1.9.8.5
	 */
	public function enqueue_scripts() {

		// Get URL for commerce.js from Products.
		$convertkit_products = new ConvertKit_Resource_Products();
		$commerce_js_url     = $convertkit_products->get_commerce_js_url();

		// Bail if the commerce.js URL could not be fetched, as this means there are no Products.
		if ( ! $commerce_js_url ) {
			return;
		}

		// Enqueue.
		wp_enqueue_script( 'convertkit-commerce', $commerce_js_url, array(), false, true ); // phpcs:ignore

	}

	/**
	 * Enqueues styles for this Gutenberg Block in the editor and frontend views.
	 *
	 * @since   1.9.8.5
	 */
	public function enqueue_styles() {

		wp_enqueue_style( 'convertkit-button', CONVERTKIT_PLUGIN_URL . 'resources/frontend/css/button.css', array(), CONVERTKIT_PLUGIN_VERSION );

	}

	/**
	 * Returns this block's programmatic name, excluding the convertkit- prefix.
	 *
	 * @since   1.9.8.5
	 */
	public function get_name() {

		/**
		 * This will register as:
		 * - a shortcode, with the name [convertkit_product].
		 * - a Gutenberg block, with the name convertkit/product.
		 */
		return 'product';

	}

	/**
	 * Returns this block's Title, Icon, Categories, Keywords and properties.
	 *
	 * @since   1.9.8.5
	 */
	public function get_overview() {

		return array(
			'title'                             => __( 'ConvertKit Product', 'convertkit' ),
			'description'                       => __( 'Displays a button to purchase a ConvertKit product.', 'convertkit' ),
			'icon'                              => 'resources/backend/images/block-icon-product.png',
			'category'                          => 'convertkit',
			'keywords'                          => array(
				__( 'ConvertKit', 'convertkit' ),
				__( 'Product', 'convertkit' ),
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
			'gutenberg_icon'                => file_get_contents( CONVERTKIT_PLUGIN_PATH . '/resources/backend/images/block-icon-product.svg' ), /* phpcs:ignore */

			// Gutenberg: Example image showing how this block looks when choosing it in Gutenberg.
			'gutenberg_example_image'           => CONVERTKIT_PLUGIN_URL . 'resources/backend/images/block-example-product.png',

			// Gutenberg: Help description, displayed when no settings defined for a newly added Block.
			'gutenberg_help_description'        => __( 'Select a Product using the Product option in the Gutenberg sidebar.', 'convertkit' ),

			// Gutenberg: JS function to call when rendering the block preview in the Gutenberg editor.
			// If not defined, render_callback above will be used.
			'gutenberg_preview_render_callback' => 'convertKitGutenbergProductBlockRenderPreview',
		);

	}

	/**
	 * Returns this block's Attributes
	 *
	 * @since   1.9.8.5
	 */
	public function get_attributes() {

		return array(
			// Block attributes.
			'product'              => array(
				'type'    => 'string',
				'default' => $this->get_default_value( 'product' ),
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
	 * @since   1.9.8.5
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
	 * @since   1.9.8.5
	 *
	 * @return  bool|array
	 */
	public function get_fields() {

		// Bail if the request is not for the WordPress Administration or frontend editor.
		if ( ! WP_ConvertKit()->is_admin_or_frontend_editor() ) {
			return false;
		}

		// Get ConvertKit Products.
		$products            = array();
		$convertkit_products = new ConvertKit_Resource_Products();
		if ( $convertkit_products->exist() ) {
			foreach ( $convertkit_products->get() as $product ) {
				$products[ absint( $product['id'] ) ] = sanitize_text_field( $product['name'] );
			}
		}

		// Gutenberg's built-in fields (such as styling, padding etc) don't need to be defined here, as they'll be included
		// automatically by Gutenberg.
		return array(
			'product'          => array(
				'label'  => __( 'Product', 'convertkit' ),
				'type'   => 'select',
				'values' => $products,
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
	 * @since   1.9.8.5
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
					'product',
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
	 * @since   1.9.8.5
	 *
	 * @return  array
	 */
	public function get_default_values() {

		return array(
			'product'          => '',
			'text'             => __( 'Buy my product', 'convertkit' ),
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
	 * @since   1.9.8.5
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

		// Get Products Resource.
		$convertkit_products = new ConvertKit_Resource_Products();

		// Build HTML.
		$html = $convertkit_products->get_html( $atts['product'], $atts['text'], $atts['_css_classes'], $atts['_css_styles'], $this->is_block_editor_request() );

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
		 * @since   1.9.8.5
		 *
		 * @param   string  $html   ConvertKit Product button HTML.
		 * @param   array   $atts   Block Attributes.
		 */
		$html = apply_filters( 'convertkit_block_product_render', $html, $atts );

		return $html;

	}

}
