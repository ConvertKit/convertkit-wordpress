<?php
/**
 * ConvertKit Admin TinyMCE class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * ConvertKit TinyMCE class. Registers buttons.
 *
 * @since 1.5.0
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_Admin_TinyMCE {

	/**
	 * Constructor
	 */
	public function __construct() {

		// Outputs the TinyMCE and QuickTag Modal.
		add_action( 'wp_ajax_convertkit_admin_tinymce_output_modal', array( $this, 'output_modal' ) );

		// Add filters to register QuickTag Plugins.
		add_action( 'admin_enqueue_scripts', array( $this, 'register_quicktags' ) ); // WordPress Admin.
		add_action( 'wp_enqueue_scripts', array( $this, 'register_quicktags' ) ); // Frontend Editors.

		// Add filters to register TinyMCE Plugins.
		// Low priority ensures this works with Frontend Page Builders.
		add_filter( 'mce_external_plugins', array( $this, 'register_tinymce_plugins' ), 99999 );
		add_filter( 'mce_buttons', array( $this, 'register_tinymce_buttons' ), 99999 );

	}

	/**
	 * Loads the view for a block's modal in the TinyMCE and Text Editors.
	 *
	 * @since   1.9.6
	 */
	public function output_modal() {

		// Check nonce.
		check_ajax_referer( 'convertkit_admin_tinymce', 'nonce' );

		// Get blocks.
		$blocks = convertkit_get_blocks();

		// Get requested block name.
		$block_name = sanitize_text_field( $_REQUEST['block'] );

		// If the block is not registered, return a view in the modal to tell the user.
		if ( ! isset( $blocks[ $block_name ] ) ) {
			require_once CONVERTKIT_PLUGIN_PATH . '/views/backend/tinymce/modal-missing.php';
			die();
		}

		// Define block.
		$block = $blocks[ $block_name ];

		// Output the modal.
		require_once CONVERTKIT_PLUGIN_PATH . '/views/backend/tinymce/modal.php';
		die();

	}

	/**
	 * Registers QuickTags JS for the TinyMCE Text (non-Visual) Editor
	 *
	 * @since   3.0.0
	 */
	public function register_quicktags() {

		// Get blocks.
		$blocks = convertkit_get_blocks();

		// Bail if no blocks are available.
		if ( ! is_array( $blocks ) || ! count( $blocks ) ) {
			return;
		}

		// Enqueue Quicktag JS.
		wp_enqueue_script( 'convertkit-admin-quicktags', CONVERTKIT_PLUGIN_URL . 'resources/backend/js/quicktags.js', array( 'jquery', 'quicktags' ), CONVERTKIT_PLUGIN_VERSION, true );
		wp_enqueue_script( 'convertkit-admin-modal', CONVERTKIT_PLUGIN_URL . 'resources/backend/js/modal.js', array( 'jquery' ), CONVERTKIT_PLUGIN_VERSION, true );

		// Make blocks available as convertkit_quicktags JS variable.
		wp_localize_script( 'convertkit-admin-quicktags', 'convertkit_quicktags', $blocks );

		// Register JS variable convertkit_admin_tinymce.nonce for AJAX calls.
		wp_localize_script(
			'convertkit-admin-quicktags',
			'convertkit_admin_tinymce',
			array(
				'nonce' => wp_create_nonce( 'convertkit_admin_tinymce' ),
			)
		);

		// Enqueue Quicktag CSS.
		wp_enqueue_style( 'convertkit-admin-quicktags', CONVERTKIT_PLUGIN_URL . '/resources/backend/css/quicktags.css', false, CONVERTKIT_PLUGIN_VERSION );

		// Output Backbone View Template.
		?>
		<script type="text/template" id="tmpl-convertkit-quicktags-modal">
			<div id="convertkit-quicktags-modal">
				<div class="media-frame-title"><h1>Title</h1></div>
				<div class="media-frame-content">Content</div>
			</div>
		</script>
		<?php

	}

	/**
	 * Register JS plugins for the TinyMCE Editor
	 *
	 * @since   1.5.0
	 *
	 * @param   array $plugins    JS Plugins.
	 * @return  array             JS Plugins
	 */
	public function register_tinymce_plugins( $plugins ) {

		// Get blocks.
		$blocks = convertkit_get_blocks();

		// Bail if no blocks are available.
		if ( ! is_array( $blocks ) || ! count( $blocks ) ) {
			return;
		}

		// Enqueue TinyMCE CSS and JS.
		wp_enqueue_script( 'convertkit-admin-tinymce', CONVERTKIT_PLUGIN_URL . 'resources/backend/js/tinymce.js', array( 'jquery' ), CONVERTKIT_PLUGIN_VERSION, true );
		wp_enqueue_script( 'convertkit-admin-modal', CONVERTKIT_PLUGIN_URL . 'resources/backend/js/modal.js', array( 'jquery' ), CONVERTKIT_PLUGIN_VERSION, true );
		wp_enqueue_style( 'convertkit-admin-tinymce', CONVERTKIT_PLUGIN_URL . '/resources/backend/css/tinymce.css', false, CONVERTKIT_PLUGIN_VERSION );

		// Register JS variable convertkit_admin_tinymce.nonce for AJAX calls.
		wp_localize_script(
			'convertkit_admin_tinymce',
			'convertkit_admin_tinymce',
			array(
				'nonce' => wp_create_nonce( 'convertkit_admin_tinymce' ),
			)
		);

		// Register TinyMCE Javascript Plugin.
		foreach ( $blocks as $block => $properties ) {
			$plugins[ 'convertkit_' . $block ] = CONVERTKIT_PLUGIN_URL . 'resources/backend/js/tinymce-' . $block . '.js';
		}

		return $plugins;

	}

	/**
	 * Registers buttons in the TinyMCE Editor
	 *
	 * @since   1.5.0
	 *
	 * @param   array $buttons    Buttons.
	 * @return  array             Buttons
	 */
	public function register_tinymce_buttons( $buttons ) {

		// Get blocks.
		$blocks = convertkit_get_blocks();

		// Bail if no blocks are available.
		if ( ! is_array( $blocks ) || ! count( $blocks ) ) {
			return $buttons;
		}

		// Register each Block as a TinyMCE Button.
		foreach ( $blocks as $block => $properties ) {
			$buttons[] = 'convertkit_' . $block;
		}

		return $buttons;

	}

}
