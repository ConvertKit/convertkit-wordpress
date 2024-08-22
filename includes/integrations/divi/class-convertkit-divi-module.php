<?php
/**
 * Divi Module
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers blocks as Divi Modules.
 *
 * @package ConvertKit
 * @author  ConvertKit
 */
class ConvertKit_Divi_Module extends ET_Builder_Module {

	/**
	 * How modules are supported in the Visual Builder (off|partial|on)
	 *
	 * @since   2.5.6
	 *
	 * @var     string
	 */
	public $vb_support = 'on';

	/**
	 * Holds the block definition, properties and fields.
	 *
	 * @since   2.5.6
	 *
	 * @var     bool|array
	 */
	public $block = false;

	/**
	 * Defines the Module name
	 *
	 * @since   2.5.6
	 */
	public function init() {

		// Get block.
		$blocks = convertkit_get_blocks();

		// Bail if no blocks are available.
		if ( ! is_array( $blocks ) || ! count( $blocks ) ) {
			return;
		}

		// Bail if the block doesn't exist.
		if ( ! array_key_exists( $this->block_name, $blocks ) ) {
			return;
		}

		// Define the block and its name.
		$this->block = $blocks[ $this->block_name ];
		$this->name  = esc_html( $this->block['title'] );

	}

	/**
	 * Defines the fields that can be configured for this Module
	 *
	 * @since   2.5.6
	 */
	public function get_fields() {

		// Bail if no block.
		if ( ! $this->block ) {
			return array();
		}

		// Bail if no fields.
		if ( ! is_array( $this->block['fields'] ) ) {
			return array();
		}

		// Build fields.
		$fields = array();
		foreach ( $this->block['fields'] as $field_name => $field ) {
			// Start building field definition.
			$fields[ $field_name ] = array(
				'label'           => $field['label'],
				'type'            => $field['type'],
				'option_category' => 'basic_option',
				'description'     => ( isset( $field['description'] ) ? $field['description'] : '' ),
				'toggle_slug'     => 'main_content',
				'default'         => $this->get_default_value( $field ),
			);
		}

		// Return.
		return $fields;

	}

	/**
	 * Renders the shortcode syntax, converted from the module's properties array
	 *
	 * @since   2.5.6
	 *
	 * @param   array|string $unprocessed_props  Unprocessed properties.
	 * @param   array|string $content            Content.
	 * @param   string       $render_slug        Slug.
	 */
	public function render( $unprocessed_props, $content, $render_slug ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter

		return 'test';

	}

	/**
	 * Returns the default value for the given field configuration.
	 *
	 * If the field's default value is an array, it's converted to a string,
	 * to prevent Divi builder timeout errors on the frontend.
	 *
	 * @since   2.5.6
	 *
	 * @param   array $field  Field.
	 * @return  string|int|object         Default Value
	 */
	private function get_default_value( $field ) {

		// Return a blank string if the field doesn't specify a default value.
		if ( ! array_key_exists( 'default_value', $field ) ) {
			return '';
		}

		return $field['default_value'];

	}

}
