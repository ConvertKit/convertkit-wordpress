<?php
/**
 * ConvertKit Gutenberg class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers blocks defined in the `convertkit_blocks` filter in Gutenberg.
 *
 * @package ConvertKit
 * @author  ConvertKit
 */
class ConvertKit_Gutenberg {

	/**
	 * Constructor
	 *
	 * @since   1.9.6
	 */
	public function __construct() {

		// Register Gutenberg Block Categories and Blocks.
		if ( get_bloginfo( 'version' ) >= 5.8 ) {
			// Filter changed in 5.8.
			add_filter( 'block_categories_all', array( $this, 'add_block_categories' ), 10, 2 );
		} else {
			add_filter( 'block_categories', array( $this, 'add_block_categories' ), 10, 2 );
		}

		// Register Gutenberg Blocks.
		add_action( 'init', array( $this, 'add_blocks' ) );

	}

	/**
	 * Registers the ConvertKit Block Category.
	 *
	 * @since   1.9.6
	 *
	 * @param   array   $categories     Block Categories.
	 * @param   WP_Post $post           WordPress Post.
	 * @return  array                   Block Categories
	 */
	public function add_block_categories( $categories, $post ) {

		// Define block categories.
		$categories = array_merge(
			$categories,
			array(
				array(
					'slug'  => 'convertkit',
					'title' => 'ConvertKit',
				),
			)
		);

		/**
		 * Adds block categories to the default Gutenberg Block Categories
		 *
		 * @since   1.9.6
		 *
		 * @param   array   $categories     Block Categories
		 * @param   WP_Post  $post           WordPress Post
		 */
		$categories = apply_filters( 'convertkit_admin_gutenberg_add_block_categories', $categories, $post );

		// Return filtered results.
		return $categories;

	}

	/**
	 * Registers Blocks, so that they can be used in the Gutenberg Editor
	 *
	 * @since   1.9.6
	 */
	public function add_blocks() {

		// Bail if Gutenberg isn't available.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		// Get blocks.
		$blocks = convertkit_get_blocks();

		// Bail if no blocks are available.
		if ( ! is_array( $blocks ) || ! count( $blocks ) ) {
			return;
		}

		// Get registered blocks.
		$registered_blocks = array_keys( WP_Block_Type_Registry::get_instance()->get_all_registered() );

		// Iterate through blocks, registering them.
		foreach ( $blocks as $block => $properties ) {

			// Skip if this block has already been registered.
			if ( is_array( $registered_blocks ) && in_array( 'convertkit/' . $block, $registered_blocks, true ) ) {
				continue;
			}

			// Register block.
			register_block_type(
				'convertkit/' . $block,
				array(
					'attributes'      => $properties['attributes'],
					'editor_script'   => 'convertkit-gutenberg',
					'render_callback' => array(
						$properties['render_callback'][0],
						$properties['render_callback'][1],
					),
				)
			);
		}

		// Enqueue Gutenberg Block scripts and styles.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_styles' ) );

	}

	/**
	 * Enqueues scripts for Gutenberg blocks in the editor view.
	 *
	 * @since   1.9.6
	 */
	public function enqueue_scripts() {

		// Bail if request isn't for the Admin or a Frontend Editor.
		if ( ! WP_ConvertKit()->is_admin_or_frontend_editor() ) {
			return;
		}

		// Get settings.
		$settings = new ConvertKit_Settings();

		// Get blocks.
		$blocks = convertkit_get_blocks();

		// Enqueue Gutenberg Javascript, and set the blocks data.
		wp_enqueue_script( 'convertkit-gutenberg', CONVERTKIT_PLUGIN_URL . '/resources/backend/js/gutenberg.js', array( 'jquery' ), CONVERTKIT_PLUGIN_VERSION, true );
		wp_localize_script( 'convertkit-gutenberg', 'convertkit_blocks', $blocks );

		/**
		 * Enqueue any additional scripts for Gutenberg blocks that have been registered.
		 *
		 * @since   1.9.6.5
		 *
		 * @param   array   $blocks     ConvertKit Blocks.
		 */
		do_action( 'convertkit_gutenberg_enqueue_scripts', $blocks );

	}

	/**
	 * Enqueues scripts for Gutenberg blocks in the editor view.
	 *
	 * Use wp_enqueue_style() hooked to the enqueue_block_assets hook for frontend styles:
	 * https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/applying-styles-with-stylesheets/
	 *
	 * @since   1.9.6.9
	 */
	public function enqueue_styles() {

		// Bail if request isn't for the Admin or a Frontend Editor.
		if ( ! WP_ConvertKit()->is_admin_or_frontend_editor() ) {
			return;
		}

		/**
		 * Enqueue styles for Gutenberg blocks that have been registered.
		 *
		 * @since   1.9.6.9
		 */
		do_action( 'convertkit_gutenberg_enqueue_styles' );

	}

}
