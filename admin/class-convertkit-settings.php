<?php
/**
 * ConvertKit Settings class
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Class ConvertKit_Settings
 */
class ConvertKit_Settings {
	/**
	 * ConvertKit API instance
	 *
	 * @var ConvertKit_API
	 */
	public $api;

	/**
	 * Settings sections
	 *
	 * @var array
	 */
	public $sections = array();

	/**
	 * Page slug
	 *
	 * @var string
	 */
	public $settings_key  = WP_ConvertKit::SETTINGS_PAGE_SLUG;

	/**
	 * Constructor
	 */
	public function __construct() {
		$general_options = get_option( $this->settings_key );
		$api_key         = $general_options && array_key_exists( 'api_key', $general_options ) ? $general_options['api_key'] : null;
		$api_secret      = $general_options && array_key_exists( 'api_secret', $general_options ) ? $general_options['api_secret'] : null;
		$debug           = $general_options && array_key_exists( 'debug', $general_options ) ? $general_options['debug'] : null;
		$this->api       = new ConvertKit_API( $api_key, $api_secret, $debug );

		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_sections' ) );
	}

	/**
	 * Add the options page
	 */
	public function add_settings_page() {
		add_options_page(
			__( 'ConvertKit', 'convertkit' ),
			__( 'ConvertKit', 'convertkit' ),
			'manage_options',
			$this->settings_key,
			array( $this, 'display_settings_page' )
		);

		add_action( 'admin_print_styles', array( $this, 'admin_styles' ) );
	}

	/**
	 * Options page callback
	 */
	public function display_settings_page() {
		if ( isset( $_GET['tab'] ) ) { // WPCS: CSRF ok.
			$active_section = sanitize_text_field( wp_unslash( $_GET['tab'] ) ); // WPCS: CSRF ok.
		} else {
			$active_section = $this->sections[0]->name;
		}

		?>
		<div class="wrap convertkit-settings-wrap">
		<?php
		if ( count( $this->sections ) > 1 ) {
			$this->display_section_nav( $active_section );
		} else {
			?>
			<h2><?php esc_html_e( 'ConvertKit', 'convertkit' ); ?></h2>
			<?php
		}
		?>

		<form method="post" action="options.php">
		<?php
		foreach ( $this->sections as $section ) :
			if ( $active_section === $section->name ) :
				$section->render();
			endif;
		endforeach;

		// Check for Multibyte string PHP extension.
		if ( ! extension_loaded( 'mbstring' ) ) {
			?><p><strong><?php
			echo  sprintf( __( 'Note: Your server does not support the %s functions - this is required for better character encoding. Please contact your webhost to have it installed.', 'woocommerce' ), '<a href="https://php.net/manual/en/mbstring.installation.php">mbstring</a>' ) . '</mark>';
			?></strong></p><?php
		}
		?><p class="description"><?php
				printf( 'If you need help setting up the plugin please refer to the %s plugin documentation.</a>', '<a href="http://help.convertkit.com/article/99-the-convertkit-wordpress-plugin" target="_blank">' ); ?></p>
		</form>
		</div>
		<?php
	}

	/**
	 * Queue up the admin styles
	 */
	public function admin_styles() {
		wp_enqueue_style( 'wp-convertkit-admin' );
	}

	/**
	 * Render a tab for each section
	 *
	 * @param string $active_section The currently active section.
	 */
	public function display_section_nav( $active_section ) {
		?>
		<h1><?php esc_html_e( 'ConvertKit', 'convertkit' ); ?></h1>
		<h2 class="nav-tab-wrapper">
		<?php
		foreach ( $this->sections as $section ) :
			printf(
				'<a href="?page=%s&tab=%s" class="nav-tab right %s">%s</a>',
				esc_html( $this->settings_key ),
				esc_html( $section->name ),
				$active_section === $section->name ? 'nav-tab-active' : '',
				esc_html( $section->tab_text )
			);
		endforeach;
		?>
		</h2>
		<?php
	}

	/**
	 * Adds a section to be displayed
	 *
	 * @param string $section A section class name.
	 */
	public function register_section( $section ) {
		$section_instance = new $section();

		if ( $section_instance->is_registerable ) {
			array_push( $this->sections, $section_instance );
		}
	}

	/**
	 * Register each section
	 */
	public function register_sections() {
		wp_register_style( 'wp-convertkit-admin', plugins_url( '../resources/backend/wp-convertkit.css', __FILE__ ) );
		$this->register_section( 'ConvertKit_Settings_General' );
		$this->register_section( 'ConvertKit_Settings_Wishlist' );
		$this->register_section( 'ConvertKit_Settings_ContactForm7' );
	}
}

if ( is_admin() ) {
	$convertkit_settings = new ConvertKit_Settings();

	include plugin_dir_path( __FILE__ ) . '../lib/class-multi-value-field-table.php';
	include 'section/class-convertkit-settings-base.php';
	include 'section/class-convertkit-settings-general.php';
	include 'section/class-convertkit-settings-wishlist.php';
	include 'section/class-convertkit-settings-contactform7.php';
}
