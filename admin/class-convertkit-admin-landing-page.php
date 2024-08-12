<?php
/**
 * ConvertKit Admin Landing Page class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Modifies the Pages WP_List_Table to provide:
 * - an 'Add New Landing Page' button next to the 'Add New' button
 * - a 'ConvertKit Landing Page' label appended to the Page's title when a Landing Page is selected
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_Admin_Landing_Page {

	/**
	 * Holds the ConvertKit Tags resource class.
	 *
	 * @since   2.5.5
	 *
	 * @var     bool|ConvertKit_Resource_Tags
	 */
	public $tags = false;

	/**
	 * Holds the ConvertKit Products resource class.
	 *
	 * @since   2.5.5
	 *
	 * @var     bool|ConvertKit_Resource_Products
	 */
	public $products = false;

	/**
	 * Registers action and filter hooks.
	 *
	 * @since   2.5.5
	 */
	public function __construct() {

		// Add New Landing Page Wizard button to Pages.
		add_filter( 'convertkit_admin_post_register_add_new_buttons', array( $this, 'register_add_new_button' ), 10, 2 );

		// Filter Page's post state to maybe include a label denoting that a Landing Page is enabled.
		add_filter( 'display_post_states', array( $this, 'maybe_display_landing_page_post_state' ), 10, 2 );

	}

	/**
	 * Registers a button in the Pages WP_List_Table linking to the the Landing Page Setup Wizard.
	 *
	 * @since   2.5.5
	 *
	 * @param   array  $buttons    Buttons.
	 * @param   string $post_type  Post Type.
	 * @return  array               Views
	 */
	public function register_add_new_button( $buttons, $post_type ) {

		// If no API credentials have been set, don't output the button.
		$settings = new ConvertKit_Settings();
		if ( ! $settings->has_access_and_refresh_token() ) {
			return $buttons;
		}

		// Bail if the Post Type isn't supported.
		if ( $post_type !== 'page' ) {
			return $buttons;
		}

		// Register button.
		$buttons['convertkit_landing_page_setup'] = array(
			'url'   => add_query_arg(
				array(
					'page'         => 'convertkit-landing-page-setup',
					'ck_post_type' => $post_type,
				),
				admin_url( 'options.php' )
			),
			'label' => __( 'Add New Landing Page', 'convertkit' ),
		);

		return $buttons;

	}

	/**
	 * Appends the 'ConvertKit Member Content' text to a Page's Title in the WP_List_Table,
	 * if the given Page has a Landing Page setting.
	 *
	 * @param   string[] $post_states    An array of post display states.
	 * @param   WP_Post  $post           The current post object.
	 * @return  string[]                    An array of post display states
	 */
	public function maybe_display_landing_page_post_state( $post_states, $post ) {

		// Bail if we're not on a WP_List_Table screen for a supported Post Type.
		if ( ! $this->is_wp_list_table_request_for_supported_post_type() ) {
			return $post_states;
		}

		// Fetch Post's settings.
		$convertkit_post = new ConvertKit_Post( $post->ID );

		// Return post states, unedited, if a Landing Page isn't enabled on this Page.
		if ( ! $convertkit_post->has_landing_page() ) {
			return $post_states;
		}

		// Add Post State.
		$post_states['convertkit_landing_page'] = esc_html__( 'ConvertKit Landing Page', 'convertkit' );

		// Return.
		return $post_states;

	}

	/**
	 * Determines if the current request is for a WP_List_Table, and if so that
	 * the Post Type we're viewing supports Landing Page functionality.
	 *
	 * @since   2.5.5
	 *
	 * @return  bool    Is WP_List_Table request for a supported Post Type.
	 */
	private function is_wp_list_table_request_for_supported_post_type() {

		// Bail if we cannot determine the screen.
		if ( ! function_exists( 'get_current_screen' ) ) {
			return false;
		}

		// Get screen.
		$screen = get_current_screen();

		// Bail if we're not on an edit.php screen.
		if ( $screen->base !== 'edit' ) {
			return false;
		}

		// Return whether Post Type is supported for Landing Page functionality.
		return ( $screen->post_type === 'page' );

	}

}
