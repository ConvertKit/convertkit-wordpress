<?php
/**
 * ConvertKit Admin Setup Wizard for Landing Pages class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Provides a UI for setting up a new WordPress Page that displays a ConvertKit Landing Page.
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_Admin_Setup_Wizard_Landing_Page extends ConvertKit_Admin_Setup_Wizard {

	/**
	 * Holds the Post Type to generate Members Content for.
	 *
	 * @since   2.5.5
	 *
	 * @var     string
	 */
	public $post_type = 'page';

	/**
	 * Holds the type of Member's Content to generate (course|download).
	 *
	 * @since   2.5.5
	 *
	 * @var     string
	 */
	public $type = 'download';

	/**
	 * Holds the ConvertKit Products resource class.
	 *
	 * @since   2.5.5
	 *
	 * @var     bool|ConvertKit_Resource_Landing_Pages
	 */
	public $landing_pages = false;

	/**
	 * Holds the Pages created by this setup wizard.
	 *
	 * @since   2.5.5
	 *
	 * @var     bool|array
	 */
	public $pages = false;

	/**
	 * Holds the URL to the current setup wizard screen.
	 *
	 * @since   2.5.5
	 *
	 * @var     bool|string
	 */
	public $current_url = false;

	/**
	 * The required user capability to access the setup wizard.
	 *
	 * @since   2.5.5
	 *
	 * @var     string
	 */
	public $required_capability = 'edit_posts';

	/**
	 * The programmatic name for this wizard.
	 *
	 * @since   2.5.5
	 *
	 * @var     string
	 */
	public $page_name = 'convertkit-landing-page-setup';

	/**
	 * The URL to take the user to when they click the Exit link.
	 *
	 * @since   2.5.5
	 *
	 * @var     string
	 */
	public $exit_url = 'edit.php?post_type=page';

	/**
	 * Registers action and filter hooks.
	 *
	 * @since   2.5.5
	 */
	public function __construct() {

		// Define details for each step in the setup process.
		$this->steps = array(
			1 => array(
				'name' => __( 'Setup', 'convertkit' ),
			),
			2 => array(
				'name'        => __( 'Configure', 'convertkit' ),
				'next_button' => array(
					'label' => __( 'Submit', 'convertkit' ),
				),
			),
			3 => array(
				'name' => __( 'Done', 'convertkit' ),
			),
		);

		add_action( 'convertkit_admin_setup_wizard_process_form_convertkit-landing-page-setup', array( $this, 'process_form' ) );
		add_action( 'convertkit_admin_setup_wizard_load_screen_data_convertkit-landing-page-setup', array( $this, 'load_screen_data' ) );

		// Call parent class constructor.
		parent::__construct();

	}

	/**
	 * Process posted data from the submitted form.
	 *
	 * @since   2.5.5
	 *
	 * @param   int $step   Current step.
	 */
	public function process_form( $step ) {

		// Run security checks.
		if ( ! isset( $_REQUEST['_wpnonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), $this->page_name ) ) {
			$this->error = __( 'Invalid nonce specified.', 'convertkit' );
			return;
		}

		// Depending on the step, process the form data.
		switch ( $step ) {
			case 3:
				// Sanitize configuration.
				$configuration = array(
					'type'             => sanitize_text_field( stripslashes( $_POST['type'] ) ),
					'title'            => sanitize_text_field( stripslashes( $_POST['title'] ) ),
					'description'      => sanitize_textarea_field( stripslashes( $_POST['description'] ) ),
					'number_of_pages'  => ( isset( $_POST['number_of_pages'] ) ? absint( $_POST['number_of_pages'] ) : 0 ),
					'restrict_content' => sanitize_text_field( stripslashes( $_POST['restrict_content'] ) ),
					'post_type'        => $this->post_type,
				);

				// Depending on the type of content selected, create WordPress Page(s) now.
				switch ( $configuration['type'] ) {
					/**
					 * Download
					 * - Single page with a link to a downloadable product.
					 */
					case 'download':
						$result = $this->create_download( $configuration );
						break;

					/**
					 * Course
					 */
					case 'course':
					default:
						$result = $this->create_course( $configuration );
						break;
				}

				// If here, an error occured as create_download() and create_course() perform a redirect on success.
				// Show an error message if Account Details could not be fetched e.g. API credentials supplied are invalid.
				// Decrement the step.
				$this->step  = ( $this->step - 1 );
				$this->error = $result->get_error_message();
				return;

		}

		// phpcs:enable

	}

	/**
	 * Load any data into class variables for the given setup wizard name and current step.
	 *
	 * @since   2.5.5
	 *
	 * @param   int $step   Current step.
	 */
	public function load_screen_data( $step ) {

		// Show an error screen if API credentials have not been specified.
		// This shouldn't happen, because the 'Add New Member Content' button is only displayed
		// if valid credentials have been specified.
		$settings = new ConvertKit_Settings();
		if ( ! $settings->has_access_and_refresh_token() ) {
			wp_die( esc_html__( 'Connect your ConvertKit account in the ConvertKit Plugin\'s settings to get started', 'convertkit' ) );
		}

		// Bail if the Post Type isn't supported.
		$this->post_type = isset( $_REQUEST['ck_post_type'] ) ? sanitize_text_field( $_REQUEST['ck_post_type'] ) : 'page'; // phpcs:ignore WordPress.Security.NonceVerification
		if ( ! in_array( $this->post_type, convertkit_get_supported_post_types(), true ) ) {
			wp_die(
				sprintf(
					/* translators: Post Type */
					esc_html__( 'The post type `%s` is not supported for Member Content.', 'convertkit' ),
					esc_html( $this->post_type )
				),
				esc_html__( 'WordPress Error', 'convertkit' ),
				array(
					'back_link' => true,
				)
			);
		}

		// Define Exit URL to take the user back to the WP_List_Table for the Post Type they were viewing.
		$this->exit_url = add_query_arg(
			array(
				'post_type' => $this->post_type,
			),
			admin_url( 'edit.php' )
		);

		// Load data depending on the current step.
		switch ( $step ) {
			case 1:
				// Fetch Landing Pages.
				$this->landing_pages = new ConvertKit_Resource_Products( 'landing_page_wizard' );

				// Refresh Landing Page resources, in case the user just created their first Product or Tag
				// in ConvertKit.
				$this->landing_pages->refresh();

				// If no Landing Pages exist in ConvertKit, change the next button label and make it a link to reload
				// the screen.
				if ( ! $this->landing_pages->exist() ) {
					unset( $this->steps[1]['next_button'] );
					$this->current_url = add_query_arg(
						array(
							'page'         => $this->page_name,
							'ck_post_type' => $this->post_type,
							'step'         => 1,
						),
						admin_url( 'options.php' )
					);
				}
				break;

			case 2:
				// Fetch Landing Pages.
				$this->landing_pages = new ConvertKit_Resource_Products( 'landing_page_wizard' );
				break;
		}

	}

	/**
	 * Creates a WordPress Page for the given title, restricted to the given restrict content setting.
	 *
	 * @since   2.5.5
	 *
	 * @param   string      $title                      Page Title.
	 * @param   string      $content                    Non-restricted Content.
	 * @param   string      $post_type                  Post Type.
	 * @param   bool|string $restricted_content         Restricted Content.
	 * @param   bool|string $restrict_content_setting   ConvertKit Form, Tag or Product to restrict content to.
	 * @param   int         $page_number                Page Number.
	 * @param   int         $total_pages                Total Pages that will be created.
	 * @param   bool|int    $parent_page_id             Parent Page ID (false if none).
	 * @return  WP_Error|int                            Error or Page ID
	 */
	private function create_page( $title, $content, $post_type = 'page', $restricted_content = false, $restrict_content_setting = false, $page_number = 0, $total_pages = 0, $parent_page_id = false ) {

		// Build content.
		$content = $this->element_paragraph( $content );

		// If restricted content is defined, append it to the post content using a more block.
		if ( $restricted_content && $restrict_content_setting ) {
			$content .= $this->element_more_tag();
			$content .= $this->element_paragraph( $restricted_content );
		}

		// Define previous / next links, depending on the page number and total pages.
		$content .= $this->element_navigation( $page_number, $total_pages );

		// Build arguments to create Page.
		$args = array(
			'post_type'    => $post_type,
			'post_status'  => 'publish',
			'post_author'  => get_current_user_id(),
			'post_title'   => $title,
			'post_content' => $content,
			'menu_order'   => $page_number,
		);

		// If a parent page is specified, apply it to the arguments.
		if ( $parent_page_id !== false ) {
			$args['post_parent'] = $parent_page_id;
		}

		// Create Page.
		$page_id = wp_insert_post( $args, true );

		// Bail if an error occured.
		if ( is_wp_error( $page_id ) ) {
			return $page_id;
		}

		// Define Page's settings, ensuring no default Form displays.
		// If a restrict content setting was supplied, it's set to the Page now.
		WP_ConvertKit()->get_class( 'admin_post' )->save_post_settings(
			$page_id,
			array(
				'form'             => '0', // Don't display a Form.
				'restrict_content' => ( $restrict_content_setting !== false ? $restrict_content_setting : '0' ),
			)
		);

		// Return.
		return $page_id;

	}

}
