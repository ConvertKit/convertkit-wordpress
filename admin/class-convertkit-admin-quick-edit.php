<?php
/**
 * ConvertKit Admin Quick Edit class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers settings fields for output when using WordPress' Quick Edit functionality
 * in a Post, Page or Custom Post Type WP_List_Table.
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_Admin_Quick_Edit {

	/**
	 * Registers action and filter hooks.
	 *
	 * @since   1.9.8.0
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'add_inline_data', array( $this, 'quick_edit_inline_data' ), 10, 2 );
		add_action( 'in_admin_footer',  array( $this, 'quick_edit_fields' ), 10, 2 );
		add_action( 'save_post', array( $this, 'quick_edit_save' ) );

	}

	/**
	 * Enqueues scripts for quick edit functionality in the Post, Page and Custom Post WP_List_Tables
	 * 
	 * @since 	1.9.8.0
	 * 
	 * @param 	string 	$pagehook 	Page hook name.
	 */
	public function enqueue_scripts( $pagehook ) {

		// Bail if we're not on a Post Type Edit screen.
		if ( 'edit.php' !== $pagehook ) {
			return;
		}

		wp_enqueue_script( 'convertkit-quick-edit', CONVERTKIT_PLUGIN_URL . 'resources/backend/js/quick-edit.js', array( 'jquery' ) );

	}

	/**
	 * Outputs hidden inline data in each Post's Title column, which the Quick Edit
	 * JS can read when the user clicks the Quick Edit link in a WP_List_Table.
	 * 
	 * @since 	1.9.8.0
	 * 
	 * @param 	string 	$column_name 	Column Name.
	 * @param 	int 	$post_id 		Post ID.
	 */
	public function quick_edit_inline_data( $post, $post_type_object ) {

		// Fetch Post's Settings.
		$settings = new ConvertKit_Post( $post->ID );

		// Output the Post's ConvertKit settings as hidden data- attributes, which
		// the Quick Edit JS can read.
		foreach ( $settings->get() as $key => $value ) {
			?>
			<div class="convertkit" data-setting="<?php echo esc_attr( $key ); ?>" data-value="<?php echo esc_attr( $value ); ?>"><?php echo esc_attr( $value ); ?></div>
			<?php
		}

	}

	/**
	 * Outputs Quick Edit settings fields.
	 * 
	 * @since 	1.9.8.0
	 */
	public function quick_edit_fields() {

		// Output Quick Edit fields.
		echo '<fieldset class="inline-edit-col-right">
					<div class="inline-edit-col">
						<div class="inline-edit-group wp-clearfix">
							<label class="alignleft">
								<span class="title">Form</span>
								<span class="input-text-wrap"><input type="text" name="form" value=""></span>
							</label>
						</div>
					</div>
				</fieldset>';

	}

	/**
	 * Saves submitted Quick Edit fields.
	 * 
	 * @since 	1.9.8.0
	 * 
	 * @param 	int 	$post_id 	Post ID.
	 */
	public function quick_edit_save( $post_id ) {

	}

	/**
	 * Insert an array value after the given key for the given array
	 *
	 * @since   1.9.8.0
	 *
	 * @param   array  $array      Current Array.
	 * @param   string $key        Key (new array will be inserted after this key).
	 * @param   array  $new        Array data to insert.
	 * @return  array               New Array
	 */
	private function array_insert_after( array $array, $key, array $new ) {

		$keys  = array_keys( $array );
		$index = array_search( $key, $keys ); /* phpcs:ignore */
		$pos   = false === $index ? count( $array ) : $index + 1; /* phpcs:ignore */
		return array_merge( array_slice( $array, 0, $pos ), $new, array_slice( $array, $pos ) );

	}

}
