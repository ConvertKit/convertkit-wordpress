<?php
/**
 * ConvertKit Pre-publish Action class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * ConvertKit Pre-publish Action definition for Gutenberg and Shortcode.
 *
 * @package ConvertKit
 * @author  ConvertKit
 */
class ConvertKit_Pre_Publish_Action {

	/**
	 * Holds the Post Meta Key prefix that stores the ConvertKit pre-publish action setting on a per-Post basis
	 *
	 * @var     string
	 * 
	 * @since 	2.4.0
	 */
	const POST_META_KEY_PREFIX = '_convertkit_action_';

	private $meta_key = '';

	public function __construct() {

		// Define the meta key.
		$this->meta_key = self::POST_META_KEY_PREFIX . $this->get_name();

		// Register meta key.
		add_action( 'init', array( $this, 'register_meta_key' ) );

		// Register this as a pre-publish action in the ConvertKit Plugin.
		add_filter( 'convertkit_get_pre_publish_actions', array( $this, 'register' ) );

		add_action( 'save_post', array( $this, 'save_post_meta' ) );

		// Perform pre-publish action.
		add_action( 'transition_post_status', array( $this, 'run' ), 10, 3 );

	}

	/**
	 * Registers the action's meta key in WordPress.
	 * This is required for Gutenberg to save the 'convertkit_action_{$name}'
	 * meta key/value pair when a Post is published.
	 * 
	 * @since 	2.4.0
	 */
	public function register_meta_key() {

		// Register action as a meta key.
		register_meta(
			'post',
			$this->meta_key,
			array(
			    'show_in_rest'  => true,
			    'single' 		=> true,
			    'type' 			=> 'boolean'
			)
		);

		// Save post meta?


	}

	/**
	 * Registers this pre-publish action with the ConvertKit Plugin.
	 *
	 * @since   2.4.0
	 *
	 * @param   array $pre_publish_actions    Pre-publish actions to register.
	 * @return  array               		  Pre-publish actions to register.
	 */
	public function register( $pre_publish_actions ) {

		$pre_publish_actions[ $this->get_name() ] = array(
			'name'           => $this->get_name(),
			'label' 		 => $this->get_label(),
			'description'    => $this->get_description(),
		);

		return $pre_publish_actions;

	}

	/**
	 * Saves... @TODO.
	 *
	 * @since  2.4.0
	 *
	 * @param   int $post_id    Post ID.
	 */
	public function save_post_meta( $post_id ) {

		// Bail if this is an autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// Bail if this is a post revision.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Bail if no nonce field exists.
		if ( ! isset( $_POST['wp-convertkit-pre-publish-actions-nonce'] ) ) {
			return;
		}

		// Bail if the nonce verification fails.
		if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['wp-convertkit-pre-publish-actions-nonce'] ) ), 'wp-convertkit-pre-publish-actions' ) ) {
			return;
		}

		// Delete meta key if this action's checkbox has not been checked.
		if ( ! array_key_exists( $this->meta_key, $_POST ) ) {
			return delete_post_meta( $post_id, $this->meta_key );
		}

		// Save setting.
		update_post_meta( $post_id, $this->meta_key, true );

	}

	/**
	 * Performs the pre-publish action, if the Post has been transitioned to published.
	 * 
	 * @since 	2.4.0
	 * 
	 * @param   string  $new_status     New Status.
	 * @param   string  $old_status     Old Status.
	 * @param   WP_Post $post           Post.
	 */
	public function run( $new_status, $old_status, $post ) {

		// Ignore if the Post is not transitioning to published.
		if ( $new_status !== 'publish' ) {
			return;
		}

		// Ignore if the statuses match i.e. it's already a published Post that is being updated.
		if ( $new_status === $old_status ) {
			return;
		}

		// Check the action was enabled on this Post by the user.
		if ( ! $this->enabled( $post->ID ) ) {
			return;
		}

		/**
		 * Run this pre-publish action, as the WordPress Post has just transitioned to publish
		 * from another state.
		 * 
		 * @since 	2.4.0
		 * 
		 * @param 	WP_Post 	$post 	Post.
		 */
		do_action( 'convertkit_pre_publish_action_run_' . $this->get_name(), $post );

	}

	/**
	 * Returns this action's programmatic name, excluding the convertkit- prefix.
	 *
	 * @since   2.4.0
	 *
	 * @return  string
	 */
	public function get_name() {

		/**
		 * This will register as:
		 * - a Classic Editor pre-publish action, displayed as a checkbox on a draft Post.
		 * - a Gutenberg pre-publish action, with the name convertkit-action-{$name}.
		 */
		return '';

	}

	/**
	 * Returns this action's label.
	 *
	 * @since   2.4.0
	 *
	 * @return  string
	 */
	public function get_label() {

		return '';

	}

	/**
	 * Returns this action's description.
	 *
	 * @since   2.4.0
	 *
	 * @return  string
	 */
	public function get_description() {

		return '';

	}

	/**
	 * Returns whether this action has been enabled by the user to be run
	 * when the Post is published.
	 * 
	 * @since 	2.4.0
	 * 
	 * @param 	int 	$post_id 	Post ID.
	 * @return 	bool
	 */
	public function is_enabled( $post_id ) {

		return (bool) get_post_meta( $post_id, $this->meta_key . $this->get_name(), true );

	}

}
