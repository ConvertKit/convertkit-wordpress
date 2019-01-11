<?php
/**
 * CK_Blocks is used to define everything we need for using CK Blocks in Gutenberg.
 */

/**
 * Class CK_Blocks
 */
class CK_Blocks {

	/**
	 * CK_Blocks constructor.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'run' ) );
	}

	/**
	 * Run it and load Gutenberg blocks.
	 */
	public function run() {
		global $wp_version;

		if ( version_compare( '5.0.0', $wp_version, '>' ) && ! function_exists( 'is_gutenberg_page' ) ) {
			return;
		}

		add_action( 'wp_ajax_convertkit_get_forms', array( $this, 'get_forms' ) );
		add_action( 'wp_ajax_convertkit_get_block_tags', array( $this, 'get_tags' ) );
		add_action( 'wp_ajax_convertkit_get_form', array( $this, 'get_form' ) );
		add_action( 'init', array( $this, 'register_blocks' ) );
	}

	/**
	 * Get the saved convertkit forms.
	 */
	public function get_forms() {

		wp_send_json_success( array( 'forms' => get_option( 'convertkit_forms', array() ) ) );

		wp_die();
	}

	/**
	 * Get the saved convertkit tags.
	 */
	public function get_tags() {

		wp_send_json_success( array( 'tags' => get_option( 'convertkit_tags', array() ) ) );

		wp_die();
	}

	/**
	 * Get the HTML of a single form.
	 */
	public function get_form() {

		$attributes = array( 'id' => isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0 );

		$form = WP_ConvertKit::shortcode( $attributes );
		wp_send_json_success( array( 'form' =>  $form ) );

		wp_die();
	}

	/**
	 * Register the Blocks.
	 */
	public function register_blocks() {

		wp_register_script(
			'ck-gutenberg',
			plugins_url( 'resources/dist/js/gutenberg.js', CONVERTKIT_PLUGIN_FILE ),
			array( 'wp-blocks', 'wp-element', 'wp-editor' )
		);

		wp_register_style(
			'ck-gutenberg',
			plugins_url( 'resources/dist/css/gutenberg.css', CONVERTKIT_PLUGIN_FILE )
		);

		register_block_type( 'convertkit/form', array(
			'editor_script'   => 'ck-gutenberg',
			'render_callback' => array( $this, 'render_ck_form' ),
		) );

		register_block_type( 'convertkit/custom-content', array(
			'editor_script'   => 'ck-gutenberg',
			'editor_style'    => 'ck-gutenberg',
			'render_callback' => array( $this, 'render_ck_custom_content' ),
		) );
	}

	/**
	 * @param $attributes
	 *
	 * @return mixed|void
	 */
	public function render_ck_form( $attributes, $content = '' ) {
		$attributes['id'] = absint( $attributes['id'] );
		return WP_ConvertKit::shortcode( $attributes ) . $content;
	}

	/**
	 * @param $attributes
	 *
	 * @return mixed|void
	 */
	public function render_ck_custom_content( $attributes = array(), $content = '' ) {
		return ConvertKit_Custom_Content::shortcode( $attributes, $content );
	}
}

new CK_Blocks();