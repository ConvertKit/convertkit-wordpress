<?php

/**
 * ConvertKit Tools Settings class
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Class ConvertKit_Settings_Tools
 */
class ConvertKit_Settings_Tools extends ConvertKit_Settings_Base {

	/**
     * We are hiding the submit button.
     *
	 * @var bool
	 */
    protected $show_submit = false;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->settings_key = '_wp_convertkit_integration_tools_settings';
		$this->name         = 'tools';
		$this->title        = __( 'Tools', 'convertkit' );
		$this->tab_text     = __( 'Tools', 'convertkit' );

		parent::__construct();
	}

	/**
	 * Register and add settings
	 */
	public function register_fields() {
		add_settings_field(
			'log',
			'Log',
			array( $this, 'view_log' ),
			$this->settings_key,
			$this->name
		);
	}

	/**
	 * Prints help info for this section
	 */
	public function print_section_info() {
		?>
		<p><?php esc_html_e( 'Tools to help you manage ConvertKit on your site.', 'convertkit' ); ?></p>
		<?php
	}

	/*
	 * View the Log
	 */
	public function view_log() {
	    $log_file = trailingslashit( CONVERTKIT_PLUGIN_PATH ) . 'log.txt';
        $log = file_get_contents( $log_file );

        if ( ! $log ) {
            ?>
            <p class="description"><?php esc_html_e( 'Log file is empty', 'convertkit' ); ?></p>
            <?php
            return;
        }
	    ?>
        <button type="submit" class="button button-secondary" name="convertkit_clear_log"><?php esc_html_e( 'Clear the Log', 'converkit' ); ?></button>
        <div class="convertkit-log-viewer">
		    <pre><?php echo esc_html( file_get_contents( $log_file ) ); ?></pre>
        </div>
	    <?php
    }

	/**
	 * Sanitizes the settings.
     * We are also using this to clear the Log file.
	 *
	 * @param  array $settings The settings fields submitted.
	 * @return array           Sanitized settings.
	 */
	public function sanitize_settings( $settings ) {

		$log_file = trailingslashit( CONVERTKIT_PLUGIN_PATH ) . 'log.txt';
		if ( isset( $_POST['convertkit_clear_log'] ) ) {
			$handle = fopen( $log_file, 'w' );
			fclose( $handle );
		}
		return $settings;
	}
}
