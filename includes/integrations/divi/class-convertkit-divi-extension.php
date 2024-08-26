<?php
/**
 * Divi Extension class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers Plugin as an extension in Divi.
 *
 * @package ConvertKit
 * @author  ConvertKit
 */
class ConvertKit_Divi_Extension extends DiviExtension {

	/**
	 * The gettext domain for the extension's translations.
	 *
	 * @since   2.5.6
	 *
	 * @var     string
	 */
	public $gettext_domain = 'convertkit';

	/**
	 * The extension's WP Plugin name.
	 *
	 * @since   2.5.6
	 *
	 * @var     string
	 */
	public $name = 'convertkit-divi';

	/**
	 * The extension's version.
	 *
	 * @since   2.5.6
	 *
	 * @var     string
	 */
	public $version = '2.5.6';

	/**
	 * Constructor.
	 *
	 * @since   2.5.6
	 *
	 * @param   string $name Extension name.
	 * @param   array  $args Arguments.
	 */
	public function __construct( $name = 'convertkit-divi', $args = array() ) {

		$this->plugin_dir     = CONVERTKIT_PLUGIN_PATH . '/includes/integrations/divi/';
		$this->plugin_dir_url = CONVERTKIT_PLUGIN_URL . 'includes/integrations/divi/';

		// Store any JS data that can be accessed by builder-bundle.min.js using window.ConvertkitDiviBuilderData.
		$this->_builder_js_data = convertkit_get_blocks();

		// Call parent construct.
		parent::__construct( $name, $args );

	}
}

new ConvertKit_Divi_Extension();
