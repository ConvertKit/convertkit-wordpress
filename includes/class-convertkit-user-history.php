<?php
/**
 * Class ConvertKit_User_History
 *
 * @since 1.5.0
 */
class ConvertKit_User_History {

	/**
	 * Name of the database table to store user visits
	 *
	 * @var string
	 */
	public $table = 'convertkit_user_history';

	/**
	 * Name of the cookie to store user visits
	 *
	 * @var string
	 */
	public $cookie;

	/**
	 * Version of the visits database
	 */
	public $version = '1.0.0';

	/**
	 * How long the cookie will last
	 *
	 * @var
	 */
	public $cookie_life;

	/**
	 * @var mixed|void
	 */
	protected $options;

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->options   = get_option( '_wp_convertkit_integration_custom_content_settings' );

		if ( isset( $this->options['enable'] ) && 'on' === $this->options['enable'] ) {

			$this->table       = 'convertkit_user_history';
			$this->cookie      = 'convertkit_history';
			$this->cookie_life = 21 * DAY_IN_SECONDS;

			$this->add_actions();
		}
	}

	/**
	 * Add actions related to user history
	 */
	public function add_actions() {

		// TODO this is the non-js way to track user browsing. doesn't work great with cached sites.
		add_action( 'the_post', array( $this, 'maybe_tag_subscriber' ), 50 );
		add_action( 'wp_ajax_nopriv_ck_add_user_visit', array( $this, 'add_user_history' ) );
		add_action( 'wp_ajax_ck_add_user_visit', array( $this, 'add_user_history' ) );
		add_action( 'wp_login', array( $this, 'login_action' ), 50, 2 );

	}

	/**
	 * If the user arrives at the site with a URL parameter of 'ck_subscriber_id' then cookie the user with that value.
	 *
	 * @see https://app.convertkit.com/account/edit#email_settings
	 * @param $post
	 */
	public static function maybe_tag_subscriber( $post ) {

		if ( isset( $_COOKIE['ck_subscriber_id']) && absint( $_COOKIE['ck_subscriber_id'] ) ) {
			$subscriber_id = absint( $_COOKIE['ck_subscriber_id'] );
			$api  = WP_ConvertKit::get_api();
			$meta = get_post_meta( $post->ID, '_wp_convertkit_post_meta', true );
			$tag  = isset( $meta['tag'] ) ? $meta['tag'] : 0;

			// get subscriber's email to add tag ith
			$subscriber = $api->get_subscriber( $subscriber_id );

			if ( $subscriber ) {
				// tag subscriber
				$args = array(
					'email' => $subscriber->email_address,
				);

				if ( $tag ) {
					$api->add_tag( $tag, $args );
					$api->log( "tagging subscriber (" . $subscriber_id . ")" . " with tag (" . $tag . ")" );
				} else {
					$api->log( "post_id (" . $post->ID . ") not found in user history" );
				}
			}
		}

	}

	/**
	 * Track the visitor
	 */
	public function add_user_history() {

		$visitor_cookie = isset( $_POST['user'] ) ? sanitize_text_field( $_POST['user'] ): '';
		$subscriber_id   = isset( $_POST['subscriber_id'] ) ? sanitize_text_field( $_POST['subscriber_id'] ): '';
		$user_id        = get_current_user_id();
		$url            = isset( $_POST['url'] ) ? sanitize_text_field( $_POST['url'] ): '';
		$ip             = $this->get_user_ip();
		$date           = date( 'Y-m-d H:i:s', time() );

		error_log( '-------- in add_history ---------' );
		error_log( 'visitor_cookie: ' . $visitor_cookie );
		error_log( 'subscriber_id: ' . $subscriber_id );
		error_log( 'url: ' . $url );
		error_log( 'user_id: ' . $user_id );

		if ( empty( $visitor_cookie ) ) {
			$visitor_cookie = md5( time() );
			error_log( 'no user adding one: ' . $visitor_cookie );
		}

		$this->insert( array(
			'visitor_cookie' => $visitor_cookie,
			'user_id'        => $user_id,
			'subscriber_id'  => $subscriber_id,
			'url'            => $url,
			'ip'             => $ip,
			'date'           => $date,
		) );

		echo json_encode(
			array(
				'user' => $visitor_cookie,
				'subscriber_id' => $subscriber_id,
			)
		) ;
		exit;
	}

	/**
	 * Runs after the customer has been tagged and subscriber_id has been retrieved
	 *
	 * @param $user_login
	 * @param $user
	 */
	public function login_action( $user_login, $user ) {

		$api = WP_ConvertKit::get_api();
		$api->log( '----login_action for user: ' . $user->ID );

		$subscriber_id = get_user_meta( $user->ID, 'convertkit_subscriber_id', true );

		if ( isset( $_COOKIE['ck_visit'] ) ) {
			$user_cookie = sanitize_text_field( $_COOKIE['ck_visit'] );
			$this->associate_history_with_user( $user_cookie, $subscriber_id, $user->ID );
		}

		if ( $subscriber_id ) {
			$this->process_history( $subscriber_id, $user->ID, $user->user_email );
		}

		$tags = $api->get_subscriber_tags( $subscriber_id );
		update_user_meta( $user->ID, 'convertkit_tags', json_encode( $tags ) );
	}

	/**
	 * @param string $cookie
	 * @param int $subscriber_id
	 * @param int $user_id
	 */
	public function associate_history_with_user( $cookie, $subscriber_id = 0, $user_id = 0 ) {

		if( $user_id ){
			$this->update( 'user_id', strval( $user_id ), 'visitor_cookie', $cookie );
		}

		if( $subscriber_id ){
			$this->update( 'subscriber_id', strval( $subscriber_id ), 'visitor_cookie', $cookie );
		}
	}

	/**
	 * Remove rows in the convertkit_user_history table older than the Expire setting.
	 */
	public function remove_expired_rows() {

		$expire_months = isset( $this->options['expire'] ) ? absint( $this->options['expire'] ) : 4 ;
		$expire_range = apply_filters( 'convertkit_user_history_expire', $expire_months );
		$expire_date = date( 'Y-m-d H:i:s', strtotime( '-' . $expire_range .' months' ) );
		$rows = $this->delete( 'date', $expire_date, '<' );

	}

	/**
	 *
	 * @see https://stackoverflow.com/questions/13646690/how-to-get-real-ip-from-visitor
	 * @return mixed
	 */
	public function get_user_ip(){
		$client  = @$_SERVER['HTTP_CLIENT_IP'];
		$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		$remote  = $_SERVER['REMOTE_ADDR'];

		if(filter_var($client, FILTER_VALIDATE_IP))
		{
			$ip = $client;
		}
		elseif(filter_var($forward, FILTER_VALIDATE_IP))
		{
			$ip = $forward;
		}
		else
		{
			$ip = $remote;
		}

		return $ip;
	}

	/**
	 * @param int $subscriber_id
	 * @param int $user_id
	 * @param string $user_email
	 */
	public function process_history( $subscriber_id = 0, $user_id = 0, $user_email ) {

		// TODO this needs to work with batch processing for larger user history

		$user_rows = array();
		$sub_rows = array();

		// get all rows
		if ( $user_id ) {
			$user_rows = $this->get( 'user_id', $user_id, '=' );
		}
		if ( $subscriber_id ) {
			$sub_rows = $this->get( 'subscriber_id', $subscriber_id, '=' );
		}

		// get unique urls visited
		$visits = array_merge( $user_rows, $sub_rows );
		$urls = wp_list_pluck( $visits, 'url' );
		$urls = array_unique( $urls );

		// get post ids
		$post_ids = $this->get_post_ids_from_url( $urls );

		// for each matching post_id tag customer

		$api = WP_ConvertKit::get_api(); // TODO remove when there is a general logger for plugin
		$args = array(
			'email' => $user_email,
		);

		foreach( $post_ids as $post_id ) {
			$meta = get_post_meta( $post_id, '_wp_convertkit_post_meta', true );
			$tag = isset( $meta['tag'] ) ? $meta['tag'] : 0;
			if ( $tag ) {
				$api->add_tag( $tag, $args );
				$api->log( "tagging user (" . $user_id . ")" . " with tag (" . $tag . ")" );
			} else {
				$api->log( "post_id (" . $post_id . ") not found in user history" );
			}
		}

		// delete all rows
		$this->delete( 'user_id', $user_id, '=' );
		$this->delete( 'subscriber_id', $subscriber_id, '=' );
	}

	/**
	 * @param $urls
	 *
	 * @return array
	 */
	public function get_post_ids_from_url( $urls ) {
		$ids = array();

		foreach ( $urls as $url ) {
			$post_id = url_to_postid( $url );
			if ( $post_id ) {
				$ids[] = $post_id;
			}
		}

		return $ids;
	}

	/**
	 * Database Helper functions
	 */

	/**
	 * @param $data
	 */
	private function insert( $data ) {
		global $wpdb;

		do_action( 'ck_data_before_insert', $data );

		$table_columns = array(
			'visitor_cookie' => '%s',
			'user_id'        => '%d',
			'url'            => '%s',
			'ip'             => '%s',
			'date'           => '%s',
		);

		$wpdb->insert( $wpdb->prefix . $this->table, $data, $table_columns );

		do_action( 'ck_data_after_insert', $data );

	}

	/**
	 * Get rows from the database
	 *
	 * @param $field
	 * @param $value
	 * @param $operator
	 *
	 * @return false|int
	 */
	private function get( $field, $value, $operator ){
		global $wpdb;

		$table_name = $wpdb->prefix . 'convertkit_user_history';
		$sql = 'SELECT * from ' . $table_name . ' WHERE ' . $field . ' ' . $operator . ' \'' . $value . '\'';
		$rows = $wpdb->get_results( $sql );

		return $rows;
	}

	/**
	 * @param $column
	 * @param $value
	 * @param $compare
	 * @param $compare_value
	 *
	 * @return int
	 */
	private function update( $column, $value, $compare, $compare_value ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'convertkit_user_history';
		$data = array(
			$column => $value,
		);
		$where = array(
			$compare => $compare_value,
		);

		$rows = $wpdb->update( $table_name, $data, $where );

		return $rows;
	}

	/**
	 * @param string $column
	 * @param string $value
	 * @param string $operator
	 *
	 * @return false|int
	 */
	private function delete( $column, $value, $operator ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'convertkit_user_history';
		$sql = 'DELETE from ' . $table_name . ' WHERE ' . $column . ' ' . $operator .  ' \'' . $value . '\'';
		$rows = $wpdb->query( $sql );

		return $rows;
	}

	/**
	 * Creates the table to track visits.
	 *
	 * @access public
	 *
	 * @see dbDelta()
	 */
	static function create_table() {
		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$table_name = $wpdb->prefix . 'convertkit_user_history';
		$sql = "CREATE TABLE " . $table_name . " (
			visit_id bigint(20) NOT NULL AUTO_INCREMENT,
			visitor_cookie mediumtext NOT NULL,
			user_id bigint(20) NOT NULL,
			subscriber_id bigint(20) NOT NULL,
			url mediumtext NOT NULL,
			ip tinytext NOT NULL,
			date datetime NOT NULL,
			PRIMARY KEY  (visit_id)
			) CHARACTER SET utf8 COLLATE utf8_general_ci;";

		dbDelta( $sql );

		update_option( 'convertkit_user_history_table' , '1.0.0' );
	}

}

new ConvertKit_User_History();