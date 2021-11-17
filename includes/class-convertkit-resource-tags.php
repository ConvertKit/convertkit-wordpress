<?php
/**
 * ConvertKit Tags Resource class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Reads ConvertKit Tags from the options table, and refreshes
 * ConvertKit Tags data stored locally from the API.
 *
 * @since   1.9.6
 */
class ConvertKit_Resource_Tags extends ConvertKit_Resource {

	/**
	 * Holds the Settings Key that stores site wide ConvertKit settings
	 *
	 * @var     string
	 */
	public $settings_name = 'convertkit_tags';

	/**
	 * The type of resource
	 *
	 * @var     string
	 */
	public $type = 'tags';

	/**
	 * Holds the forms from the ConvertKit API
	 *
	 * @var     array
	 */
	public $resources = array();

}
