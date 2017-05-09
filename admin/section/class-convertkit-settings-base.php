<?php
/**
 * ConvertKit Settings class
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Class ConvertKit_Settings_Base
 */
abstract class ConvertKit_Settings_Base {

	/**
	 * Setting
	 *
	 * @var bool
	 */
	public $is_registerable = true;

	/**
	 * Section name
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Section title
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Section tab text
	 *
	 * @var string
	 */
	public $tab_text;

	/**
	 * Database key
	 *
	 * @var string
	 */
	public $settings_key;

	/**
	 * API instance
	 *
	 * @var ConvertKitAPI
	 */
	public $api;

	/**
	 * Options array
	 *
	 * @var mixed|void
	 */
	public $options;

	/**
	 * Constructor
	 */
	public function __construct() {
		global $convertkit_settings;

		$this->api      = $convertkit_settings->api;
		$this->options  = get_option( $this->settings_key );
		if ( empty( $this->tab_text ) ) {
			$this->tab_text = $this->title;
		}

		$this->register_section();
	}

	/**
	 * Register settings section
	 */
	public function register_section() {
		if ( false === get_option( $this->settings_key ) ) {
			add_option( $this->settings_key );
		}

		add_settings_section(
			$this->name,
			$this->title,
			array( $this, 'print_section_info' ),
			$this->settings_key
		);

		$this->register_fields();

		register_setting(
			$this->settings_key,
			$this->settings_key,
			array( $this, 'sanitize_settings' )
		);
	}

	/**
	 * Renders the section
	 */
	public function render() {
		do_settings_sections( $this->settings_key );
		settings_fields( $this->settings_key );
		submit_button();
	}

	/**
	 * Register settings fields
	 */
	abstract public function register_fields();

	/**
	 * Prints help info for this section
	 */
	abstract public function print_section_info();
}
