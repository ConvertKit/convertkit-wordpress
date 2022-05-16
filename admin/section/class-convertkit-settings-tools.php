<?php
/**
 * ConvertKit Settings Tools class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers Tools for debugging and system information that can be accessed at Settings > ConvertKit > Tools.
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_Settings_Tools extends ConvertKit_Settings_Base {

	/**
	 * Constructor
	 */
	public function __construct() {

		// Initialize WP_Filesystem.
		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();

		$this->settings_key = '_wp_convertkit_tools'; // Required for ConvertKit_Settings_Base, but we don't save settings on the Tools screen.
		$this->name         = 'tools';
		$this->title        = __( 'Tools', 'convertkit' );
		$this->tab_text     = __( 'Tools', 'convertkit' );

		parent::__construct();

		$this->maybe_perform_actions();
	}

	/**
	 * Possibly perform some actions, such as clearing the log, downloading the log,
	 * downloading system information or any third party actions now.
	 *
	 * @since   1.9.7.4
	 */
	private function maybe_perform_actions() {

		// Bail if nonce is invalid.
		if ( ! $this->verify_nonce() ) {
			return;
		}

		$this->maybe_clear_log();
		$this->maybe_download_log();
		$this->maybe_download_system_info();
		$this->maybe_export_configuration();
		$this->maybe_import_configuration();

	}

	/**
	 * Clears the Log.
	 *
	 * @since   1.9.6
	 */
	private function maybe_clear_log() {

		// Bail if the submit button for clearing the debug log was not clicked.
		if ( ! array_key_exists( 'convertkit-clear-debug-log', $_REQUEST ) ) { // phpcs:ignore
			return;
		}

		// Clear Log.
		$log = new ConvertKit_Log();
		$log->clear();

		// Redirect to Tools screen.
		$this->redirect_to_tools_screen();

	}

	/**
	 * Prompts a browser download for the log file, if the user clicked
	 * the Download Log button.
	 *
	 * @since   1.9.6
	 */
	private function maybe_download_log() {

		global $wp_filesystem;

		// Bail if the submit button for downloading the debug log was not clicked.
		if ( ! array_key_exists( 'convertkit-download-debug-log', $_REQUEST ) ) { // phpcs:ignore
			return;
		}

		// Get Log and download.
		$log = new ConvertKit_Log();

		// Download.
		header( 'Content-type: application/octet-stream' );
		header( 'Content-Disposition: attachment; filename=convertkit-log.txt' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );
		echo $wp_filesystem->get_contents( $log->get_filename() ); // phpcs:ignore
		exit();

	}

	/**
	 * Prompts a browser download for the system information, if the user clicked
	 * the Download System Info button.
	 *
	 * @since   1.9.6
	 */
	private function maybe_download_system_info() {

		global $wp_filesystem;

		// Bail if the submit button for downloading the system info was not clicked.
		if ( ! array_key_exists( 'convertkit-download-system-info', $_REQUEST ) ) { // phpcs:ignore
			return;
		}

		// Get System Info.
		$system_info = new ConvertKit_System_Info();

		// Write contents to temporary file.
		$tmpfile  = tmpfile();
		$filename = stream_get_meta_data( $tmpfile )['uri'];
		$wp_filesystem->put_contents(
			$filename,
			$system_info->get()
		);

		// Download.
		header( 'Content-type: application/octet-stream' );
		header( 'Content-Disposition: attachment; filename=convertkit-system-info.txt' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );
		echo $wp_filesystem->get_contents( $filename ); // phpcs:ignore
		$wp_filesystem->delete( $filename );
		exit();

	}

	/**
	 * Prompts a browser download for the configuration file, if the user clicked
	 * the Export button.
	 *
	 * @since   1.9.7.4
	 */
	private function maybe_export_configuration() {

		// Bail if the submit button for exporting the configuration was not clicked.
		if ( ! array_key_exists( 'convertkit-export', $_REQUEST ) ) { // phpcs:ignore
			return;
		}

		// Define configuration data to include in the export file.
		$settings = new ConvertKit_Settings();
		$json     = wp_json_encode(
			array(
				'settings' => $settings->get(),
			)
		);

		// Download.
		header( 'Content-type: application/x-msdownload' );
		header( 'Content-Disposition: attachment; filename=convertkit-export.json' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );
		echo $json; /* phpcs:ignore */
		exit();

	}

	/**
	 * Imports the configuration file, if it's included in the form request
	 * and has the expected structure.
	 *
	 * @since   1.9.7.4
	 */
	private function maybe_import_configuration() {

		// Allow us to easily interact with the filesystem.
		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();
		global $wp_filesystem;

		// Bail if the submit button for importing the configuration was not clicked.
		if ( ! array_key_exists( 'convertkit-import', $_REQUEST ) ) { // phpcs:ignore
			return;
		}

		// Bail if no configuration file was supplied.
		if ( ! is_array( $_FILES ) ) {
			$this->redirect_to_tools_screen();
		}
		if ( $_FILES['import']['error'] !== 0 ) {
			$this->redirect_to_tools_screen( 'import_configuration_upload_error' );
		}

		// Read file.
		$json = $wp_filesystem->get_contents( $_FILES['import']['tmp_name'] );

		// Decode.
		$import = json_decode( $json, true );

		// Bail if the data isn't JSON.
		if ( is_null( $import ) ) {
			$this->redirect_to_tools_screen( 'import_configuration_invalid_file_type' );
		}

		// Bail if no settings exist.
		if ( ! array_key_exists( 'settings', $import ) ) {
			$this->redirect_to_tools_screen( 'import_configuration_empty' );
		}

		// Import: Settings.
		$settings = new ConvertKit_Settings();
		update_option( $settings::SETTINGS_NAME, $import['settings'] );

		// Redirect to Tools screen.
		$this->redirect_to_tools_screen( false, 'import_configuration_success' );

	}

	/**
	 * Verifies if the _convertkit_settings_tools_nonce nonce was included in the request,
	 * and if so whether the nonce action is valid.
	 *
	 * @since   1.9.6
	 *
	 * @return  bool
	 */
	private function verify_nonce() {

		// Bail if nonce verification fails.
		if ( ! isset( $_REQUEST['_convertkit_settings_tools_nonce'] ) ) {
			return false;
		}

		return wp_verify_nonce( $_REQUEST['_convertkit_settings_tools_nonce'], 'convertkit-settings-tools' );

	}

	/**
	 * Redirects to the ConvertKit > Tools screen.
	 *
	 * @since   1.9.7.4
	 *
	 * @param   false|string $error      The error message key.
	 * @param   false|string $success    The success message key.
	 */
	private function redirect_to_tools_screen( $error = false, $success = false ) {

		// Build URL to redirect to, depending on whether a message is included.
		$args = array(
			'page' => '_wp_convertkit_settings',
			'tab'  => 'tools',
		);
		if ( $error !== false ) {
			$args['error'] = $error;
		}
		if ( $success !== false ) {
			$args['success'] = $success;
		}

		// Redirect to ConvertKit > Tools screen.
		wp_safe_redirect( add_query_arg( $args, 'options-general.php' ) );
		exit();

	}

	/**
	 * Register fields for this section
	 */
	public function register_fields() {

		// No fields are registered for the Debug Log.
		// This function is deliberately blank.
	}

	/**
	 * Outputs the Debug Log and System Info view.
	 *
	 * @since   1.9.6
	 */
	public function render() {

		// Get Log and System Info.
		$log         = new ConvertKit_Log();
		$system_info = new ConvertKit_System_Info();

		// Define messages that might be displayed as a notification.
		$messages = array(
			'import_configuration_upload_error'      => __( 'An error occured uploading the configuration file.', 'convertkit' ),
			'import_configuration_invalid_file_type' => __( 'The uploaded configuration file isn\'t valid.', 'convertkit' ),
			'import_configuration_empty'             => __( 'The uploaded configuration file contains no settings.', 'convertkit' ),
			'import_configuration_success'           => __( 'Configuration imported successfully.', 'convertkit' ),
		);
		$error    = false;
		if ( isset( $_REQUEST['error'] ) && array_key_exists( sanitize_text_field( $_REQUEST['error'] ), $messages ) ) { // phpcs:ignore
			$error = $messages[ sanitize_text_field( $_REQUEST['error'] ) ]; // phpcs:ignore
		}

		$success = false;
		if ( isset( $_REQUEST['success'] ) && array_key_exists( sanitize_text_field( $_REQUEST['success'] ), $messages ) ) { // phpcs:ignore
			$success = $messages[ sanitize_text_field( $_REQUEST['success'] ) ]; // phpcs:ignore
		}

		// Output view.
		require_once CONVERTKIT_PLUGIN_PATH . '/views/backend/settings/tools.php';

	}

	/**
	 * Prints help info for this section
	 */
	public function print_section_info() {
		?>
		<p><?php esc_html_e( 'Tools to help you manage ConvertKit on your site.', 'convertkit' ); ?></p>
		<?php
	}

}
