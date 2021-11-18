<?php
/**
 * ConvertKit Log class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Class to read and write to the ConvertKit log file.
 *
 * @since   1.9.6
 */
class ConvertKit_Log {

	/**
	 * The path and filename of the log file.
	 *
	 * @since   1.9.6
	 *
	 * @var     string
	 */
	private $log_file;

	/**
	 * Constructor. Defines the log file location.
	 *
	 * @since   1.9.6
	 */
	public function __construct() {

		$this->log_file = trailingslashit( CONVERTKIT_PLUGIN_PATH ) . 'log.txt';

	}

	/**
	 * Returns the path and filename of the log file.
	 *
	 * @since   1.9.6
	 *
	 * @return string
	 */
	public function get_filename() {

		return $this->log_file;

	}

	/**
	 * Whether the log file exists.
	 *
	 * @since   1.9.6
	 *
	 * @return  bool
	 */
	public function exists() {

		return file_exists( $this->get_filename() );

	}

	/**
	 * Adds an entry to the log file.
	 *
	 * @since   1.9.6
	 *
	 * @param   string $entry  Log Line Entry.
	 */
	public function add( $entry ) {

		// Initialize WordPress file system.
		global $wp_filesystem;
 		WP_Filesystem();

		// Prefix the entry with a date and time.
		$entry = '(' . gmdate( 'Y-m-d H:i:s' ) . ') ' . $entry . "\n";

		// Append to log file.
		$wp_filesystem->put_contents(
			$this->log_file,
			$entry
		);

	}

	/**
	 * Reads the given number of lines from the log file.
	 *
	 * @since   1.9.6
	 *
	 * @param   int $number_of_lines    Number of Lines.
	 * @return  string                      Log file data
	 */
	public function read( $number_of_lines = 500 ) {

		// Initialize WordPress file system.
		global $wp_filesystem;
 		WP_Filesystem();

		// Bail if the log file does not exist.
		if ( ! file_exists( $this->log_file ) ) {
			return '';
		}

		// Open log file.
		$log = $wp_filesystem->get_contents_array( $this->log_file ); // phpcs:ignore

		// Bail if the log file is empty.
		if ( ! is_array( $log ) || ! count( $log ) ) {
			return '';
		}

		// Return a limited number of log lines for output.
		return implode( "\n", array_slice( $log, 0, $number_of_lines ) );

	}

	/**
	 * Clears the log file without deleting the log file.
	 *
	 * @since   1.9.6
	 */
	public function clear() {

		// Initialize WordPress file system.
		global $wp_filesystem;
 		WP_Filesystem();

		$wp_filesystem->put_contents( $this->log_file, '' );

	}

	/**
	 * Deletes the log file.
	 *
	 * @since   1.9.6
	 */
	public function delete() {

		unlink( $this->log_file );

	}

}
