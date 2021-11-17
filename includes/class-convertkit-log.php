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
	 * @param   string $entry  Log Line Entry
	 */
	public function add( $entry ) {

		// Prefix the entry with a date and time.
		$entry = '(' . date( 'Y-m-d H:i:s' ) . ') ' . $entry . "\n";

		// Append to log file.
		file_put_contents( $this->log_file, $entry, FILE_APPEND );

	}

	/**
	 * Reads the given number of lines from the log file.
	 *
	 * @since   1.9.6
	 *
	 * @param   int $number_of_lines    Number of Lines
	 * @return  string                      Log file data
	 */
	public function read( $number_of_lines = 500 ) {

		// Bail if the log file does not exist.
		if ( ! file_exists( $this->log_file ) ) {
			return '';
		}

		// Open log file.
		$log = file_get_contents( $this->log_file );

		// Bail if the log file is empty.
		if ( empty( $log ) ) {
			return '';
		}

		// Return a limited number of log lines for output.
		unset( $log );
		$log = '';
		$fp  = fopen( $this->log_file, 'r' );
		for ( $i = 0; $i < $number_of_lines; $i++ ) {
			if ( feof( $fp ) ) {
				break;
			}

			$log .= fgets( $fp );
		}

		fclose( $fp );

		return $log;

	}

	/**
	 * Clears the log file without deleting it.
	 *
	 * @since   1.9.6
	 */
	public function clear() {

		file_put_contents( $this->log_file, '' );

	}

	/**
	 * Deletes the log file
	 *
	 * @since   1.9.6
	 */
	public function delete() {

		unlink( $this->log_file );

	}

}
