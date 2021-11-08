<?php
/**
 * Reads ConvertKit Forms from the options table, and refreshes
 * ConvertKit Forms data stored locally from the API.
 * 
 * @since 	1.9.6
 */
class ConvertKit_Resource_Forms extends ConvertKit_Resource {

	/**
	 * Holds the Settings Key that stores site wide ConvertKit settings
	 * 
	 * @var 	string
	 */
	public $settings_name = 'convertkit_forms';

	/**
	 * The type of resource
	 * 
	 * @var 	string
	 */
	public $type = 'forms';

	/**
	 * Holds the forms from the ConvertKit API
	 * 
	 * @var 	array
	 */
	public $resources = array();

	/**
	 * Returns the HTML/JS markup for the given Form ID
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	int 	$id 	Form ID
	 * @return 	mixed 			WP_Error | string
	 */
	public function get_html( $id ) {

		// Cast ID to integer.
		$id = absint( $id );

		// Bail if the resource doesn't exist.
		if ( ! isset( $this->resources[ $id ] ) ) {
			return new WP_Error( 
				'convertkit_resource_forms_get_html',
				sprintf(
					/* translators: ConvertKit Form ID */
					__( 'ConvertKit Form ID %s does not exist on ConvertKit.', 'convertkit' ),
					$id
				)
			);
		}

		return '<script async data-uid="' . $this->resources[ $id ]['uid'] . '" src="' . $this->resources[ $id ]['embed_js'] . '"></script>';

	}

}