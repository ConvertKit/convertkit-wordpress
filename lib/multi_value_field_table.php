<?php

if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Class MultiValueFieldTable
 */
class MultiValueFieldTable extends WP_List_Table {
	private $_bulk_actions     = array();
	private $_columns          = array();
	private $_sortable_columns = array();
	private $_data             = array();

    function __construct() {
		global $status, $page;

        parent::__construct(array(
			'singular' => 'item',
			'plural'   => 'items',
			'ajax'     => false
        ));
	}

	/**
	 * Set default column attributes
	 *
	 * @param  array $item        A singular item (one full row's worth of data)
	 * @param  array $column_name The name/slug of the column to be processed
	 * @return string             Text or HTML to be placed inside the column <td>
	 */
	public function column_default($item, $column_name) {
		return $item[$column_name];
	}

	/**
	 * Provide a callback function to render the checkbox column
	 *
	 * @param  array  $item  A row's worth of data
	 * @return string        The formatted string with a checkbox
	 */
	public function column_cb($item) {
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
	 * @param string  $key      Machine-readable column name
	 * @param string  $title    Title shown to the user
	 * @param boolean $sortable Whether or not this is sortable (defaults false)
	 */
	public function add_column($key, $title, $sortable = false) {
		$this->_columns[$key] = $title;

		if ($sortable) $this->_sortable_columns[$key] = array($key, false);
	}

	/**
	 * Add an item (row) to the table
	 *
	 * @param array $item A row's worth of data
	 */
	public function add_item($item) {
		array_push($this->_data, $item);
	}

	/**
	 * Add a bulk action to the table
	 *
	 * @param string $key  Machine-readable action name
	 * @param string $name Title shown to the user
	 */
	public function add_bulk_action($key, $name) {
		$this->_bulk_actions[$key] = $name;
	}

	/**
	 * Prepares the items (rows) to be rendered
	 */
	public function prepare_items() {
		$total_items = count($this->_data);
		$per_page    = 25;

		$columns  = $this->_columns;
		$hidden   = array();
		$sortable = $this->_sortable_columns;

		$this->_column_headers = array($columns, $hidden, $sortable);

		$current_page = $this->get_pagenum();

		$sorted_data = $this->reorder($this->_data);

		$data = array_slice($sorted_data, (($current_page-1)*$per_page),$per_page);

		$this->items = $data;

		$this->set_pagination_args(array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil($total_items/$per_page)
		));
	}

	/**
	 * Reorder the data according to the sort parameters
	 *
	 * @return array Row data, sorted
	 */
	public function reorder($data) {
		function usort_reorder($a, $b) {
			$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
			$order   = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
			$result  = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
			return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
		}
		usort($data, 'usort_reorder');

		return $data;
	}
}
