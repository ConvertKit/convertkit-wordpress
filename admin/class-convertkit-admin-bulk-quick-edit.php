<?php
/**
 * ConvertKit Admin Bulk and Quick Edit class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers a metabox on Posts, Pages and public facing Custom Post Types
 * and saves its settings when the Post is saved in the WordPress Administration
 * interface.
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_Admin_Bulk_Quick_Edit {

	public $column_name = 'convertkit';

	/**
	 * Registers action and filter hooks.
	 *
	 * @since   1.9.6
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'manage_page_posts_columns', array( $this, 'define_columns' ) );
		add_action( 'manage_pages_custom_column', array( $this, 'output_columns' ), 10, 2 );
		add_action( 'quick_edit_custom_box',  array( $this, 'quick_edit_fields' ), 10, 2 );
		add_action( 'save_post', array( $this, 'quick_edit_save' ) );

	}

	public function enqueue_scripts( $pagehook ) {

		// Bail if we're not on a Post Type Edit screen.
		if ( 'edit.php' !== $pagehook ) {
			return;
		}

		wp_enqueue_script( 'convertkit-quick-edit', CONVERTKIT_PLUGIN_URL . 'resources/backend/js/quick-edit.js', array( 'jquery' ) );

	}

	/**
	 * Adds a ConvertKit column to a Post, Page or Custom Post WP_List_Table immediately
	 * after the Author column.
	 * 
	 * @since 	1.9.8.0
	 * 
	 * @param 	array 	$columns 	Table Columns.
	 * @return 	array 				Table Columns
	 */
	public function define_columns( $columns ) {

		// Insert the ConvertKit column after the Title column.
		return $this->array_insert_after(
			$columns,
			'author',
			array(
				$this->column_name => __( 'ConvertKit', 'convertkit' ),
			)
		);

	}

	/**
	 * Outputs data in the ConvertKit column, as well as hidden data attributes
	 * used by the Quick Edit functionality.
	 * 
	 * @since 	1.9.8.0
	 * 
	 * @param 	string 	$column_name 	Column Name.
	 * @param 	int 	$post_id 		Post ID.
	 */
	public function output_columns( $column_name, $post_id ) {

		// Do nothing if the column name isn't for this Plugin.
		if ( $column_name !== $this->column_name ) {
			return;
		}

		// Fetch Post's Settings.
		$settings = new ConvertKit_Post( $post_id );

		// Output Form.
		echo 'Default';

		// Output as data- attributes for Quick Edit.
		foreach ( $settings->get() as $key => $value ) {
			?>
			<span class="convertkit_post" data-key="<?php echo esc_attr( $key ); ?>" data-value="<?php echo esc_attr( $value ); ?>"></span>
			<?php
		}

	}

	public function quick_edit_fields( $column_name, $post_type ) {

		// Do nothing if the column name isn't for this Plugin.
		if ( $column_name !== $this->column_name ) {
			return;
		}

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
