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

		// Enqueue scripts and styles for this Gutenberg Block in the editor and frontend views.
		add_action( 'convertkit_gutenberg_enqueue_scripts_editor_and_frontend', array( $this, 'enqueue_scripts' ) );
		add_action( 'convertkit_gutenberg_enqueue_styles_editor_and_frontend', array( $this, 'enqueue_styles' ) );

		// Render Broadcasts block via AJAX.
		add_action( 'wp_ajax_nopriv_convertkit_broadcasts_render', array( $this, 'render_ajax' ) );
		add_action( 'wp_ajax_convertkit_broadcasts_render', array( $this, 'render_ajax' ) );

	}

	/**
	 * Enqueues scripts for this Gutenberg Block in the editor view.
	 *
	 * @since   1.9.7.6
	 */
	public function enqueue_scripts() {

		// Get ConvertKit Settings.
		$settings = new ConvertKit_Settings();

		wp_enqueue_script( 'convertkit-' . $this->get_name(), CONVERTKIT_PLUGIN_URL . 'resources/frontend/js/broadcasts.js', array( 'jquery' ), CONVERTKIT_PLUGIN_VERSION, true );
		wp_localize_script(
			'convertkit-' . $this->get_name(),
			'convertkit_broadcasts',
			array(
				// WordPress AJAX URL endpoint.
				'ajax_url' => admin_url( 'admin-ajax.php' ),

				// AJAX action registered in __construct().
				'action'   => 'convertkit_broadcasts_render',

				// Whether debugging is enabled.
				'debug'    => $settings->debug_enabled(),
			)
		);
	}

	/**
	 * Enqueues styles for this Gutenberg Block in the editor and frontend views.
	 *
	 * @since   1.9.7.4
	 */
	public function enqueue_styles() {

		wp_enqueue_style( 'convertkit-' . $this->get_name(), CONVERTKIT_PLUGIN_URL . 'resources/frontend/css/broadcasts.css', array(), CONVERTKIT_PLUGIN_VERSION );

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
				'height' => 405,
			),

			// Shortcode: Include a closing [/shortcode] tag when using TinyMCE or QuickTag Modals.
			'shortcode_include_closing_tag' => false,

			// Gutenberg: Block Icon in Editor.
			'gutenberg_icon'                    => file_get_contents( CONVERTKIT_PLUGIN_PATH . '/resources/backend/images/block-icon-broadcasts.svg' ), /* phpcs:ignore */

			// Gutenberg: Example image showing how this block looks when choosing it in Gutenberg.
			'gutenberg_example_image'       => CONVERTKIT_PLUGIN_URL . 'resources/backend/images/block-example-broadcasts.png',

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
			'page'                 => array(
				'type'    => 'number',
				'default' => $this->get_default_value( 'page' ),
			),
			'paginate'             => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'paginate_label_prev'  => array(
				'type'    => 'string',
				'default' => $this->get_default_value( 'paginate_label_prev' ),
			),
			'paginate_label_next'  => array(
				'type'    => 'string',
				'default' => $this->get_default_value( 'paginate_label_next' ),
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
			'date_format'         => array(
				'label'  => __( 'Date format', 'convertkit' ),
				'type'   => 'select',
				'values' => array(
					'F j, Y' => date_i18n( 'F j, Y', strtotime( 'now' ) ),
					'Y-m-d'  => date_i18n( 'Y-m-d', strtotime( 'now' ) ),
					'm/d/Y'  => date_i18n( 'm/d/Y', strtotime( 'now' ) ),
					'd/m/Y'  => date_i18n( 'd/m/Y', strtotime( 'now' ) ),
				),
			),
			'limit'               => array(
				'label' => __( 'Number of posts', 'convertkit' ),
				'type'  => 'number',
				'min'   => 1,
				'max'   => 999,
				'step'  => 1,
			),
			'paginate'            => array(
				'label'       => __( 'Display pagination', 'convertkit' ),
				'type'        => 'toggle',
				'description' => __( 'If the number of broadcasts exceeds the "Number of posts" settings above, previous/next pagination links will be displayed.', 'convertkit' ),
			),
			'paginate_label_prev' => array(
				'label'       => __( 'Newer posts label', 'convertkit' ),
				'type'        => 'text',
				'description' => __( 'The label to display for the link to newer broadcasts.', 'convertkit' ),
			),
			'paginate_label_next' => array(
				'label'       => __( 'Older posts label', 'convertkit' ),
				'type'        => 'text',
				'description' => __( 'The label to display for the link to older broadcasts.', 'convertkit' ),
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
					'paginate',
					'paginate_label_prev',
					'paginate_label_next',
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
			'date_format'         => 'F j, Y',
			'limit'               => 10,
			'paginate'            => false,
			'paginate_label_prev' => __( 'Previous', 'convertkit' ),
			'paginate_label_next' => __( 'Next', 'convertkit' ),

			// Built-in Gutenberg block attributes.
			'style'               => '',
			'backgroundColor'     => '',
			'textColor'           => '',

			// Not output as a block option, but stores the page requested by the user if using pagination without JS.
			'page'                => $this->get_page(),
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

		// Parse attributes, defining fallback defaults if required
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

		// Build HTML.
		$html = $this->build_html( $posts, $atts );

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
	 * Returns the block's output, based on the supplied configuration attributes,
	 * when requested via AJAX.
	 *
	 * @since   1.9.7.6
	 */
	public function render_ajax() {

		// Check nonce.
		check_ajax_referer( 'convertkit-broadcasts', 'nonce' );

		// Build attributes array.
		$atts = array(
			'date_format'         => sanitize_text_field( $_REQUEST['date_format'] ),
			'limit'               => sanitize_text_field( $_REQUEST['limit'] ),
			'page'                => absint( $_REQUEST['page'] ),
			'paginate'            => absint( $_REQUEST['paginate'] ),
			'paginate_label_next' => sanitize_text_field( $_REQUEST['paginate_label_next'] ),
			'paginate_label_prev' => sanitize_text_field( $_REQUEST['paginate_label_prev'] ),
		);

		// Parse attributes, defining fallback defaults if required
		// and moving some attributes (such as Gutenberg's styles), if defined.
		$atts = $this->sanitize_and_declare_atts( $atts );

		// Fetch Posts.
		$posts = new ConvertKit_Resource_Posts();

		// Build HTML.
		$html = $this->build_html( $posts, $atts, false );

		/**
		 * Filter the block's inner content immediately before it is output by AJAX,
		 * which occurs when pagination was clicked.
		 *
		 * @since   1.9.7.6
		 *
		 * @param   string  $html   ConvertKit Broadcasts HTML.
		 * @param   array   $atts   Block Attributes.
		 */
		$html = apply_filters( 'convertkit_block_broadcasts_render_ajax', $html, $atts );

		// Send HTML as response.
		wp_send_json_success( $html );

	}

	/**
	 * Returns a HTML list of ConvertKit broadcasts, honoring the supplied
	 * attribute's current requested page and limit.
	 *
	 * @since   1.9.7.4
	 *
	 * @param   ConvertKit_Resource_Posts $posts              ConvertKit Posts Resource class.
	 * @param   array                     $atts               Block attributes.
	 * @param   bool                      $include_container  Include container div in HTML.
	 * @return  string                                          HTML
	 */
	private function build_html( $posts, $atts, $include_container = true ) {

		// Get paginated subset of Posts.
		$broadcasts = $posts->get_paginated_subset( $atts['page'], $atts['limit'] );

		// Define a nonce to ensure requests made for paginated broadcasts are protected against e.g. CSRF attacks.
		$nonce = wp_create_nonce( 'convertkit-broadcasts' );

		// Define HTML string.
		$html = '';

		// Include container, if required.
		if ( $include_container ) {
			$html = '<div class="' . esc_attr( implode( ' ', $atts['_css_classes'] ) ) . '" style="' . implode( ';', $atts['_css_styles'] ) . '" ' . $this->get_atts_as_html_data_attributes( $atts ) . '>';
		}

		// Start list.
		$html .= '<ul class="convertkit-broadcasts-list">';

		// Iterate through broadcasts.
		foreach ( $broadcasts['items'] as $count => $broadcast ) {
			// Convert UTC date to timestamp.
			$date_timestamp = strtotime( $broadcast['published_at'] );

			// Build broadcast URL.
			$url = add_query_arg(
				array(
					'utm_source'  => 'wordpress',
					'utm_content' => 'convertkit',
				),
				$broadcast['url']
			);

			// Add broadcast as list item.
			$html .= '<li class="convertkit-broadcast">
				<time datetime="' . date_i18n( 'Y-m-d', $date_timestamp ) . '">' . date_i18n( $atts['date_format'], $date_timestamp ) . '</time>
				<a href="' . $url . '" target="_blank" rel="nofollow noopener">' . $broadcast['title'] . '</a>
			</li>';
		}

		// End list.
		$html .= '</ul>';

		// If pagination is disabled, return the output now.
		if ( ! $atts['paginate'] ) {
			// Close container div, if required.
			if ( $include_container ) {
				$html .= '</div>';
			}

			return $html;
		}

		// If no next or previous page exists, just return the output.
		if ( ! $broadcasts['has_next_page'] && ! $broadcasts['has_prev_page'] ) {
			// Close container div, if required.
			if ( $include_container ) {
				$html .= '</div>';
			}

			return $html;
		}

		// Append pagination.
		$html .= '<ul class="convertkit-broadcasts-pagination">
			<li class="convertkit-broadcasts-pagination-prev">' . ( $broadcasts['has_prev_page'] ? $this->get_pagination_link_prev_html( $atts, $nonce ) : '' ) . '</li>
			<li class="convertkit-broadcasts-pagination-next">' . ( $broadcasts['has_next_page'] ? $this->get_pagination_link_next_html( $atts, $nonce ) : '' ) . '</li>
		</ul>';

		// Close container div, if required.
		if ( $include_container ) {
			$html .= '</div>';
		}

		// Return.
		return $html;

	}

	/**
	 * Returns the HTML link to paginate to the previous page, to view
	 * newer broadcasts.
	 *
	 * @since   1.9.7.6
	 *
	 * @param   array  $atts   Block attributes.
	 * @param   string $nonce  Nonce.
	 * @return  string          HTML Link
	 */
	private function get_pagination_link_prev_html( $atts, $nonce ) {

		return '<a href="' . esc_attr( $this->get_pagination_link( $atts['page'] - 1, $nonce ) ) . '" title="' . esc_attr( $atts['paginate_label_prev'] ) . '" data-page="' . esc_attr( (string) ( $atts['page'] - 1 ) ) . '" data-nonce="' . esc_attr( $nonce ) . '">
			' . esc_attr( $atts['paginate_label_prev'] ) . '
		</a>';

	}

	/**
	 * Returns the HTML link to paginate to the next page, to view
	 * older broadcasts.
	 *
	 * @since   1.9.7.6
	 *
	 * @param   array  $atts   Block attributes.
	 * @param   string $nonce  Nonce.
	 * @return  string          HTML Link
	 */
	private function get_pagination_link_next_html( $atts, $nonce ) {

		return '<a href="' . esc_attr( $this->get_pagination_link( $atts['page'] + 1, $nonce ) ) . '" title="' . esc_attr( $atts['paginate_label_next'] ) . '" data-page="' . esc_attr( (string) ( $atts['page'] + 1 ) ) . '" data-nonce="' . esc_attr( $nonce ) . '">
			' . esc_attr( $atts['paginate_label_next'] ) . '
		</a>';

	}

	/**
	 * Returns the link to paginate to the specified page.
	 *
	 * @since   1.9.7.6
	 *
	 * @param   int    $page   Page Number.
	 * @param   string $nonce  Nonce.
	 * @return  string          URL
	 */
	private function get_pagination_link( $page, $nonce ) {

		global $post;

		return add_query_arg(
			array(
				'convertkit-broadcasts-page'  => absint( $page ),
				'convertkit-broadcasts-nonce' => $nonce,
			),
			get_permalink( $post->ID )
		);

	}

	/**
	 * Returns the current pagination page requested for broadcasts.
	 *
	 * @since   1.9.7.6
	 *
	 * @return  int     Page
	 */
	private function get_page() {

		// Assume we're requesting the first page.
		$page = 1;

		// Return first page number if no nonce exists.
		if ( ! array_key_exists( 'convertkit-broadcasts-nonce', $_REQUEST ) ) {
			return $page;
		}

		// Return first page number if nonce verification fails, as this means we can't reliably trust $_REQUEST['convertkit-broadcasts-page'].
		if ( ! wp_verify_nonce( sanitize_key( $_REQUEST['convertkit-broadcasts-nonce'] ), 'convertkit-broadcasts' ) ) {
			return $page;
		}

		// Return first page number if no specific page was requested.
		if ( ! isset( $_REQUEST['convertkit-broadcasts-page'] ) ) {
			return $page;
		}

		// Return requested page number.
		return absint( $_REQUEST['convertkit-broadcasts-page'] );

	}

}
