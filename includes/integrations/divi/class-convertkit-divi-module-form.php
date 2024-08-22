<?php
/**
 * Divi Module: ConvertKit Form.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers the ConvertKit Form Block as a Divi Module.
 *
 * @package ConvertKit
 * @author  ConvertKit
 */
class ConvertKit_Divi_Module_Form extends ET_Builder_Module {

	public function init() {
		$this->name       = et_builder_i18n( 'ConvertKit Form' );
		$this->plural     = esc_html__( 'ConvertKit Forms', 'et_builder' );
		$this->slug       = 'convertkit_form';
		$this->vb_support = 'partial';
	}

	public function get_fields() {
		return array();
	}

	public function render( $unprocessed_props, $content, $render_slug ) {
		return 'convertkitform';
	}
}

new ConvertKit_Divi_Module_Form;
