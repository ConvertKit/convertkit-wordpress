<?php
/**
 * ConvertKit General Settings class
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Include WP_List_Table if not defined.
 */
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class Multi_Value_Field_Table
 */
class Multi_Value_Field_Table extends WP_List_Table {

	/**
	 * Holds the supported bulk actions.
	 *
	 * @var     array
	 */
	private $_bulk_actions     = array(); // phpcs:ignore

	/**
	 * Holds the table columns.
	 *
	 * @var     array
	 */
	private $_columns          = array(); // phpcs:ignore

	/**
	 * Holds the sortable table columns.
	 *
	 * @var     array
	 */
	private $_sortable_columns = array(); // phpcs:ignore

	/**
	 * Holds the table rows and their data.
	 *
	 * @var     array
	 */
	private $_data             = array(); // phpcs:ignore

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct(
			array(
				'singular' => 'item',
				'plural'   => 'items',
				'ajax'     => false,
			)
		);

	}

	/**
	 * Set default column attributes
	 *
	 * @since   1.0.0
	 *
	 * @param  array  $item A singular item (one full row's worth of data).
	 * @param  string $column_name The name/slug of the column to be processed.
	 * @return string Text or HTML to be placed inside the column <td>
	 */
	public function column_default( $item, $column_name ) {

		return $item[ $column_name ];

	}

	/**
	 * Provide a callback function to render the checkbox column
	 *
	 * @param  array $item  A row's worth of data.
	 * @return string The formatted string with a checkbox
	 */
	public function column_cb( $item ) {

		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'],
			$item['id']
		);

	}

	/**
	 * Get the bulk actions for this table
	 *
	 * @return array Bulk actions
	 */
	public function get_bulk_actions() {

		return $this->_bulk_actions;

	}

	/**
	 * Get a list of columns
	 *
	 * @return array
	 */
	public function get_columns() {

		return $this->_columns;

	}

	/**
	 * Add a column to the table
	 *
	 * @param string  $key Machine-readable column name.
	 * @param string  $title Title shown to the user.
	 * @param boolean $sortable Whether or not this is sortable (defaults false).
	 */
	public function add_column( $key, $title, $sortable = false ) {

		$this->_columns[ $key ] = $title;

		if ( $sortable ) {
			$this->_sortable_columns[ $key ] = array( $key, false );
		}

	}

	/**
	 * Add an item (row) to the table
	 *
	 * @param array $item A row's worth of data.
	 */
	public function add_item( $item ) {

		array_push( $this->_data, $item );

	}

	/**
	 * Add a bulk action to the table
	 *
	 * @param string $key  Machine-readable action name.
	 * @param string $name Title shown to the user.
	 */
	public function add_bulk_action( $key, $name ) {

		$this->_bulk_actions[ $key ] = $name;

	}

	/**
	 * Prepares the items (rows) to be rendered
	 */
	public function prepare_items() {

		$total_items = count( $this->_data );
		$per_page    = 25;

		$columns  = $this->_columns;
		$hidden   = array();
		$sortable = $this->_sortable_columns;

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$current_page = $this->get_pagenum();

		$sorted_data = $this->reorder( $this->_data );

		$data = array_slice( $sorted_data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		$this->items = $data;

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);

	}

	/**
	 * Reorder the data according to the sort parameters
	 *
	 * @param array $data   Row data, unsorted.
	 * @return array Row data, sorted
	 */
	public function reorder( $data ) {

		usort(
			$data,
			function( $a, $b ) {

			if ( empty( $_REQUEST['orderby'] ) ) { // phpcs:ignore
					$orderby = 'title';
				} else {
					$orderby = sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) ); // phpcs:ignore
				}

			if ( empty( $_REQUEST['order'] ) ) { // phpcs:ignore
					$order = 'asc';
				} else {
					$order = sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ); // phpcs:ignore
				}
				$result = strcmp( $a[ $orderby ], $b[ $orderby ] ); // Determine sort order.
				return ( 'asc' === $order ) ? $result : -$result; // Send final sort direction to usort.

			}
		);

		return $data;

	}

	/**
	 * Display the table without the nonce at the top.
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function display_no_nonce() {

		$singular = $this->_args['singular'];

		$this->display_tablenav( 'bottom' );

		$this->screen->render_screen_reader_content( 'heading_list' );
		?>
		<table class="wp-list-table <?php echo esc_attr( implode( ' ', $this->get_table_classes() ) ); ?>">
			<thead>
			<tr>
				<?php $this->print_column_headers(); ?>
			</tr>
			</thead>

			<tbody id="the-list"
			<?php
			if ( $singular ) {
				echo " data-wp-lists='list:" . esc_attr( $singular ) . "'";
			}
			?>
			>
			<?php $this->display_rows_or_placeholder(); ?>
			</tbody>

			<tfoot>
			<tr>
				<?php $this->print_column_headers( false ); ?>
			</tr>
			</tfoot>

		</table>
		<?php
		$this->display_tablenav( 'bottom' );

	}
}
