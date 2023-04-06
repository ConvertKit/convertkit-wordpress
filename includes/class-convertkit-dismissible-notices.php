<?php
/**
 * ConvertKit Dissmissible Notices class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Add, display and delete dismissible notices across all
 * WordPress Administration screens.
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_Dismissible_Notices {

	/**
	 * The key prefix to use for stored notices
	 *
	 * @since   2.2.0
	 *
	 * @var     string
	 */
	private $key_prefix = 'convertkit-dismissible-notices';

	/**
	 * Holds success, warning and error notices to be displayed
	 *
	 * @since   2.2.0
	 *
	 * @var     bool|array
	 */
	public $notices = false;

	/**
	 * How often to fetch notices from the ConvertKit account through WordPress' Cron.
	 * If false, won't be refreshed through WordPress' Cron
	 * If a string, must be a value from wp_get_schedules().
	 *
	 * @since   2.2.0
	 *
	 * @var     bool|string
	 */
	public $wp_cron_schedule = false;

	/**
	 * The action name in cron-functions.php to schedule through WordPress' Cron.
	 * 
	 * @since 	2.2.0
	 * 
	 * @var 	string
	 */
	public $wp_cron_action_name = 'convertkit_get_notices';

	/**
	 * Register output function to display dismissible notices
	 * in the WordPress Administration, if any exist.
	 *
	 * @since   2.2.0
	 */
	public function __construct() {

		add_action( 'admin_notices', array( $this, 'output' ) );

	}

	/**
	 * Returns how often the WordPress Cron event will recur for (e.g. daily).
	 *
	 * Returns false if no schedule exists i.e. wp_schedule_event() has not been
	 * called or failed to register a scheduled event.
	 *
	 * @since   2.2.0
	 *
	 * @return  bool|string
	 */
	public function get_cron_event() {

		return wp_get_schedule( $this->wp_cron_action_name );

	}

	/**
	 * Schedules a WordPress Cron event to fetch notices from the ConvertKit account.
	 *
	 * @since   2.2.0
	 */
	public function schedule_cron_event() {

		// Bail if no cron schedule is defined for this resource.
		if ( ! $this->wp_cron_schedule ) {
			return;
		}

		// Bail if the event already exists; we don't need to schedule it again.
		if ( $this->get_cron_event() !== false ) {
			return;
		}

		// Schedule event, starting in an hour's time and recurring for the given $wp_cron_schedule.
		wp_schedule_event(
			strtotime( '+1 hour' ),  // Start in an hour's time.
			$this->wp_cron_schedule, // Repeat based on the given schedule e.g. hourly.
			$this->wp_cron_action_name // Hook name; see includes/cron-functions.php for function that listens to this hook.
		);

	}

	/**
	 * Unschedules the WordPress Cron event to fetch notices from the ConvertKit account.
	 *
	 * @since   2.2.0
	 */
	public function unschedule_cron_event() {

		wp_clear_scheduled_hook( $this->wp_cron_action_name );

	}

	/**
	 * Outputs notices.
	 * 
	 * @since 	2.2.0
	 */
	public function output() {

		// Get notices for display.
		$this->notices = $this->get_notices();

		// Bail if no notices exist.
		if ( ! $this->notices ) {
			return;
		}

		// Output notices.
		foreach ( $this->notices as $notice_type => $notices ) {
			foreach ( $notices as $notice ) {
				?>
				<div class="notice notice-<?php echo esc_attr( $notice_type ); ?> is-dismissible">
					<p>
						<?php echo esc_html( stripslashes_deep( $notice ) ); ?>
					</p>
				</div>
			<?php
			}
		}

		// Delete the notices as they have now been displayed, and don't need to display again.
		$this->delete_notices();

	}

	/**
	 * Add a single Success Notice
	 *
	 * @since   2.2.0
	 *
	 * @param   string $value  Message.
	 * @return  bool            Success
	 */
	public function add_success_notice( $value ) {

		return $this->add_notice( 'success', $value );

	}

	/**
	 * Add a single Warning Notice
	 *
	 * @since   3.1.6
	 *
	 * @param   string $value  Message.
	 */
	public function add_warning_notice( $value ) {

		return $this->add_notice( 'warning', $value );

	}

	/**
	 * Add a single Error Notice
	 *
	 * @since   2.2.0
	 *
	 * @param   string $value  Message.
	 */
	public function add_error_notice( $value ) {

		return $this->add_notice( 'error', $value );

	}

	/**
	 * Adds a success, warning or error notice to the existing notices store.
	 * 
	 * @since 	2.2.0
	 * 
	 * @param 	string $notice_type 	Notice Type (success,warning,error).
	 * @param 	string $value 			Message.
	 * @return 	bool 					Success
	 */
	private function add_notice( $notice_type, $value ) {

		// Get any existing notices from the transient.
		$this->notices = $this->get_notices();

		// If no notices exist, set up the array now.
		if ( ! $this->notices ) {
			$this->notices = array(
				'success' => array(),
				'warning' => array(),
				'error' => array(),
			);
		}

		// Bail if the notice already exists.
		if ( in_array( $value, $this->notices['success'], true ) ) {
			return true;
		}

		// Add the notice.
		$this->notices[ $notice_type ][] = $value;

		// Remove any duplicate notices.
		$this->notices[ $notice_type ] = array_values( array_unique( $this->notices[ $notice_type ] ) );

		// Store notices.
		$this->save_notices( $this->notices );

		return true;

	}

	/**
	 * Returns all stored notices.
	 *
	 * @since   2.2.0
	 *
	 * @return  bool|array   Notices
	 */
	private function get_notices() {

		// Get notices.
		$notices = get_transient( $this->key_prefix );

		// If no notices exist, bail.
		if ( ! is_array( $notices ) ) {
			return false;
		}
		
		// If some keys aren't set, define them now.
		if ( ! isset( $notices['success'] ) ) {
			$notices['success'] = array();
		}
		if ( ! isset( $notices['warning'] ) ) {
			$notices['warning'] = array();
		}
		if ( ! isset( $notices['error'] ) ) {
			$notices['error'] = array();
		}

		// Return.
		return $notices;

	}

	/**
	 * Saves the given notices array.
	 *
	 * @since    2.2.0
	 *
	 * @param    array $notices   Notices.
	 * @return   bool               Success
	 */
	private function save_notices( $notices ) {

		// Store notices for 24 hours.
		set_transient( $this->key_prefix, $notices, DAY_IN_SECONDS );

		return true;

	}

	/**
	 * Deletes all notices
	 *
	 * @since   2.2.0
	 */
	private function delete_notices() {

		// Delete from class.
		$this->notices['success'] = array();
		$this->notices['warning'] = array();
		$this->notices['error']   = array();

		// Delete notices.
		delete_transient( $this->key_prefix );

		return true;

	}

	/**
	 * When the class is destroyed, remove any registered hooks.
	 * 
	 * @since 	2.2.0
	 */
	public function __destruct() {

		remove_action( 'admin_notices', array( $this, 'output' ) );

	}

}
