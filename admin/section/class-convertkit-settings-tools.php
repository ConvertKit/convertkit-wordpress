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

		$this->maybe_clear_log();
		$this->maybe_download_log();
		$this->maybe_download_system_info();

	}

	/**
	 * Clears the Log.
	 *
	 * @since   1.9.6
	 */
	private function maybe_clear_log() {

		// Bail if nonce is invalid.
		if ( ! $this->verify_nonce() ) {
			return;
		}

		// Bail if the submit button for clearing the debug log was not clicked.
		if ( ! array_key_exists( 'convertkit-clear-debug-log', $_REQUEST ) ) { // phpcs:ignore
			return;
		}

		// Clear Log.
		$log = new ConvertKit_Log();
		$log->clear();

		// Redirect to Tools screen.
		wp_safe_redirect( 'options-general.php?page=_wp_convertkit_settings&tab=tools' );
		exit();

	}

	/**
	 * Prompts a browser download for the log file, if the user clicked
	 * the Download Log button.
	 *
	 * @since   1.9.6
	 */
	private function maybe_download_log() {

		global $wp_filesystem;

		// Bail if nonce is invalid.
		if ( ! $this->verify_nonce() ) {
			return;
		}

		// Bail if the submit button for downloading the debug log was not clicked.
		if ( ! array_key_exists( 'convertkit-download-debug-log', $_REQUEST ) ) { // phpcs:ignore
			return;
		}

		// Get Log and download.
		$log = new ConvertKit_Log();

		// Download.
		header( 'Content-type: application/octet-stream' );
		header( 'Content-Disposition: attachment; filename=' . $log->get_filename() . '.txt' );
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

		// Bail if nonce is invalid.
		if ( ! $this->verify_nonce() ) {
			return;
		}

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
		header( 'Content-Disposition: attachment; filename=system-info.txt' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );
		echo $wp_filesystem->get_contents( $filename ); // phpcs:ignore
		$wp_filesystem->delete( $filename );
		exit();

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
