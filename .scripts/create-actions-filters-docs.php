<?php
// Setup Read Actions and Filters class.
require_once 'read-actions-filters.php';
$read_actions_filters = new Read_Actions_Filters();

// Read Plugin filters.
$filter_docs = $read_actions_filters->run(
	// Define Plugin folders to include in Docs. 
    array(
    	'../admin',
    	'../includes',
    	'../views',
    ),
    true, 
    false, 
    'markdown', 
    ( isset( $atts['filter'] ) ? $atts['filter'] : false ), 
    ( isset( $atts['filter_replacement'] ) ? $atts['filter_replacement'] : false ), 
    true 
);
$action_docs = $read_actions_filters->run( 
	// Define Plugin folders to include in Docs.
    array(
    	'../admin',
    	'../includes',
    	'../views',
    ),
    false, 
    true, 
    'markdown', 
    ( isset( $atts['filter'] ) ? $atts['filter'] : false ), 
    ( isset( $atts['filter_replacement'] ) ? $atts['filter_replacement'] : false ), 
    true
);

// Build HTML.
$html = '<h1>Filters</h1>' . $filter_docs;
$html .= '<h1>Actions</h1>' . $action_docs;

// Write to file.
file_put_contents( '../ACTIONS-FILTERS.md', $html );