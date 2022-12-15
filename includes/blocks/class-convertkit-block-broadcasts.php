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

		// Enqueue scripts and styles for this Gutenberg Block in the editor view.
		add_action( 'convertkit_gutenberg_enqueue_scripts', array( $this, 'enqueue_scripts_editor' ) );

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
	 * @since   2.0.1
	 */
	public function enqueue_scripts_editor() {

		wp_enqueue_script( 'convertkit-gutenberg-block-broadcasts', CONVERTKIT_PLUGIN_URL . 'resources/backend/js/gutenberg-block-broadcasts.js', array( 'convertkit-gutenberg' ), CONVERTKIT_PLUGIN_VERSION, true );

	}

	/**
	 * Enqueues scripts for this Gutenberg Block in the editor and frontend views.
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

		// Fetch Posts.
		$posts = new ConvertKit_Resource_Posts( 'output_broadcasts' );

		return array(
			'title'                             => __( 'ConvertKit Broadcasts', 'convertkit' ),
			'description'                       => __( 'Displays a list of your ConvertKit broadcasts.', 'convertkit' ),
			'icon'                              => 'resources/backend/images/block-icon-broadcasts.png',
			'category'                          => 'convertkit',
			'keywords'                          => array(
				__( 'ConvertKit', 'convertkit' ),
				__( 'Broadcasts', 'convertkit' ),
				__( 'Posts', 'convertkit' ),
			),

			// Function to call when rendering as a block or a shortcode on the frontend web site.
			'render_callback'                   => array( $this, 'render' ),

			// Shortcode: TinyMCE / QuickTags Modal Width and Height.
			'modal'                             => array(
				'width'  => 500,
				'height' => 580,
			),

			// Shortcode: Include a closing [/shortcode] tag when using TinyMCE or QuickTag Modals.
			'shortcode_include_closing_tag'     => false,

			// Gutenberg: Block Icon in Editor.
			'gutenberg_icon'                		=> file_get_contents( CONVERTKIT_PLUGIN_PATH . '/resources/backend/images/block-icon-broadcasts.svg' ), /* phpcs:ignore */

			// Gutenberg: Example image showing how this block looks when choosing it in Gutenberg.
			'gutenberg_example_image'           => CONVERTKIT_PLUGIN_URL . 'resources/backend/images/block-example-broadcasts.png',

			// Gutenberg: Help description, displayed when no Posts exist.
			'gutenberg_help_description'        => __( 'No Broadcasts exist in ConvertKit. Send your first Broadcast in ConvertKit to see the link to it here.', 'convertkit' ),

			// Gutenberg: JS function to call when rendering the block preview in the Gutenberg editor.
			// If not defined, render_callback above will be used.
			'gutenberg_preview_render_callback' => 'convertKitGutenbergBroadcastsBlockRenderPreview',

			// Flag to determine if Broadcasts exist.
			'has_posts'                         => $posts->exist(),
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

			// These fields will only display on the shortcode, and are deliberately not registered in get_attributes(),
			// because Gutenberg will register its own color pickers for link, background and text.
			'link_color'          => array(
				'label' => __( 'Link color', 'convertkit' ),
				'type'  => 'color',
			),
			'background_color'    => array(
				'label' => __( 'Background color', 'convertkit' ),
				'type'  => 'color',
			),
			'text_color'          => array(
				'label' => __( 'Text color', 'convertkit' ),
				'type'  => 'color',
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
					'link_color',
					'background_color',
					'text_color',
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
			'link_color'          => '',
			'background_color'    => '',
			'text_color'          => '',

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
		$posts = new ConvertKit_Resource_Posts( 'output_broadcasts' );

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
			'date_format'         => stripslashes( sanitize_text_field( $_REQUEST['date_format'] ) ),
			'limit'               => stripslashes( sanitize_text_field( $_REQUEST['limit'] ) ),
			'page'                => absint( $_REQUEST['page'] ),
			'paginate'            => absint( $_REQUEST['paginate'] ),
			'paginate_label_next' => stripslashes( sanitize_text_field( $_REQUEST['paginate_label_next'] ) ),
			'paginate_label_prev' => stripslashes( sanitize_text_field( $_REQUEST['paginate_label_prev'] ) ),
			'link_color'          => stripslashes( sanitize_text_field( $_REQUEST['link_color'] ) ),
		);

		// Parse attributes, defining fallback defaults if required
		// and moving some attributes (such as Gutenberg's styles), if defined.
		$atts = $this->sanitize_and_declare_atts( $atts );

		// Fetch Posts.
		$posts = new ConvertKit_Resource_Posts( 'output_broadcasts' );

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
			$html = '<div class="' . implode( ' ', map_deep( $atts['_css_classes'], 'sanitize_html_class' ) ) . '" style="' . implode( ';', map_deep( $atts['_css_styles'], 'esc_attr' ) ) . '" ' . $this->get_atts_as_html_data_attributes( $atts ) . '>';
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
				<time datetime="' . esc_attr( date_i18n( 'Y-m-d', $date_timestamp ) ) . '">' . esc_html( date_i18n( $atts['date_format'], $date_timestamp ) ) . '</time>
				<a href="' . esc_attr( $url ) . '" target="_blank" rel="nofollow noopener"' . $this->get_link_style_tag( $atts ) . '>' . esc_html( $broadcast['title'] ) . '</a>
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

		return '<a href="' . esc_attr( $this->get_pagination_link( $atts['page'] - 1, $nonce ) ) . '" title="' . esc_attr( $atts['paginate_label_prev'] ) . '" data-page="' . esc_attr( (string) ( $atts['page'] - 1 ) ) . '" data-nonce="' . esc_attr( $nonce ) . '"' . $this->get_link_style_tag( $atts ) . '>
			' . esc_html( $atts['paginate_label_prev'] ) . '
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

		return '<a href="' . esc_attr( $this->get_pagination_link( $atts['page'] + 1, $nonce ) ) . '" title="' . esc_attr( $atts['paginate_label_next'] ) . '" data-page="' . esc_attr( (string) ( $atts['page'] + 1 ) ) . '" data-nonce="' . esc_attr( $nonce ) . '"' . $this->get_link_style_tag( $atts ) . '>
			' . esc_html( $atts['paginate_label_next'] ) . '
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

		global $post, $wp;

		// Determine the base Permalink, depending on whether we're viewing an individual Page/Post or not.
		if ( ! is_null( $post ) ) {
			$permalink = get_permalink( $post->ID );
		} else {
			// Fallback to WordPress' request object to identify the current slug, as we are not viewing
			// an individual Page or Post e.g. we're on the Home Page and this block is in a footer widget.
			$permalink = home_url( $wp->request );
		}

		return add_query_arg(
			array(
				'convertkit-broadcasts-page'  => absint( $page ),
				'convertkit-broadcasts-nonce' => $nonce,
			),
			$permalink
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

	/**
	 * If a link_color attribute exists in the given array of attributes, we're rendering a shortcode, and therefore
	 * need to include inline styling for links.
	 *
	 * The Gutenberg block doesn't need this, because WordPress generates its own inline styles when a link color is selected.
	 *
	 * @since   1.9.8.5
	 *
	 * @param   array $atts   Block attributes.
	 * @return  string          style attribute (blank string if no styles need to be applied)
	 */
	private function get_link_style_tag( $atts ) {

		if ( ! isset( $atts['link_color'] ) ) {
			return '';
		}

		if ( empty( $atts['link_color'] ) ) {
			return '';
		}

		return ' style="color:' . esc_attr( $atts['link_color'] ) . '"';

	}

}
