<?php
/**
 * ConvertKit Admin Settings class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Registers a screen at Settings > ConvertKit in the WordPress Administration
 * interface, and handles saving its data.
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_Admin_Settings {

	/**
	 * Settings sections
	 *
	 * @var array
	 */
	public $sections = array();

	/**
	 * Holds the Settings Page Slug
	 *
	 * @var     string
	 */
	const SETTINGS_PAGE_SLUG = '_wp_convertkit_settings';

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_sections' ) );
		add_filter( 'plugin_action_links_' . CONVERTKIT_PLUGIN_FILE, array( $this, 'add_settings_page_link' ) );

	}

	/**
	 * Enqueue JavaScript in Admin
	 *
	 * @since   1.9.6
	 *
	 * @param   string $hook   Hook.
	 */
	public function enqueue_scripts( $hook ) {

		// Bail if we are not on the Settings screen.
		if ( $hook !== 'settings_page_' . self::SETTINGS_PAGE_SLUG ) {
			return;
		}

		// Enqueue Select2 JS.
		convertkit_select2_enqueue_scripts();

		/**
		 * Enqueue JavaScript for the Settings Screen at Settings > ConvertKit
		 *
		 * @since   1.9.6
		 */
		do_action( 'convertkit_admin_settings_enqueue_scripts' );

	}

	/**
	 * Enqueue CSS for the Settings Screens at Settings > ConvertKit
	 *
	 * @since   1.9.6
	 *
	 * @param   string $hook   Hook.
	 */
	public function enqueue_styles( $hook ) {

		// Bail if we are not on the Settings screen.
		if ( $hook !== 'settings_page_' . self::SETTINGS_PAGE_SLUG ) {
			return;
		}

		// Enqueue Select2 CSS.
		convertkit_select2_enqueue_styles();

		// Enqueue Settings CSS.
		wp_enqueue_style( 'convertkit-admin-settings', CONVERTKIT_PLUGIN_URL . '/resources/backend/css/settings.css', array(), CONVERTKIT_PLUGIN_VERSION );

		/**
		 * Enqueue CSS for the Settings Screen at Settings > ConvertKit
		 *
		 * @since   1.9.6
		 */
		do_action( 'convertkit_admin_settings_enqueue_styles' );

	}

	/**
	 * Adds the options page
	 *
	 * @since   1.9.6
	 */
	public function add_settings_page() {

		add_options_page(
			__( 'ConvertKit', 'convertkit' ),
			__( 'ConvertKit', 'convertkit' ),
			'manage_options',
			self::SETTINGS_PAGE_SLUG,
			array( $this, 'display_settings_page' )
		);

	}

	/**
	 * Outputs the settings screen.
	 *
	 * @since   1.9.6
	 */
	public function display_settings_page() {

		$active_section = $this->get_active_section();
		?>

		<header>
			<h1><?php esc_html_e( 'ConvertKit', 'convertkit' ); ?></h1>
		</header>

		<?php
		$this->maybe_display_notices();
		?>

		<div class="wrap">
			<?php
			if ( count( $this->sections ) > 1 ) {
				$this->display_section_nav( $active_section );
			}
			?>

			<form method="post" action="options.php">
				<?php
				// Iterate through sections to find the active section to render.
				if ( isset( $this->sections[ $active_section ] ) ) {
					$this->sections[ $active_section ]->render();
				}
				?>

				<hr />

				<p class="description">
					<?php
					printf(
						'If you need help setting up the plugin please refer to the %s plugin documentation.</a>',
						'<a href="https://help.convertkit.com/en/articles/2502591-the-convertkit-wordpress-plugin" target="_blank">'
					);
					?>
				</p>
			</form>
		</div>
		<?php

	}

	/**
	 * Gets the active tab section that the user is viewing on the Plugin Settings screen.
	 *
	 * @since   1.9.6
	 *
	 * @return  string  Tab Name
	 */
	private function get_active_section() {

		if ( isset( $_GET['tab'] ) ) { // phpcs:ignore
			return sanitize_text_field( wp_unslash( $_GET['tab'] ) ); // phpcs:ignore
		}

		// First registered section will be the active section.
		return current( $this->sections )->name;

	}

	/**
	 * Display notice(s) immediately after the settings screen header.
	 *
	 * @since   1.9.6
	 */
	private function maybe_display_notices() {

		$notices = array();

		// Check the mbstring extension is loaded.
		if ( ! extension_loaded( 'mbstring' ) ) {
			$notices[] = array(
				'type'    => 'warning',
				'message' => sprintf(
					/* translators: link to php.net manual */
					__( 'Notice: Your server does not support the %s function - this is required for better character encoding. Please contact your webhost to have it installed.', 'convertkit' ),
					'<a href="https://php.net/manual/en/mbstring.installation.php">mbstring</a>'
				),
			);
		}

		// Bail if no notices exist.
		if ( ! count( $notices ) ) {
			return;
		}
		?>
		<div class="notices">
			<?php
			// Output inline notices.
			foreach ( $notices as $notice ) {
				?>
				<div class="inline notice notice-<?php echo esc_attr( $notice['type'] ); ?>">
					<p>
						<?php echo esc_attr( $notice['message'] ); ?>
					</p>
				</div>
				<?php
			}
			?>
		</div>
		<?php

	}

	/**
	 * Define links to display below the Plugin Name on the WP_List_Table at in the Plugins screen.
	 *
	 * @param   array $links      Links.
	 * @return  array               Links
	 */
	public static function add_settings_page_link( $links ) {

		return array_merge(
			array(
				'settings' => sprintf(
					'<a href="%s">%s</a>',
					convertkit_get_settings_link(),
					__( 'Settings', 'convertkit' )
				),
			),
			$links
		);

	}
	/**
	 * Output tabs, one for each registered settings section.
	 *
	 * @param   string $active_section     Currently displayed/selected section.
	 */
	public function display_section_nav( $active_section ) {

		?>
		<h2 class="nav-tab-wrapper">
		<?php
		foreach ( $this->sections as $section ) {
			printf(
				'<a href="?page=%s&tab=%s" class="nav-tab right %s">%s</a>',
				sanitize_text_field( $_REQUEST['page'] ), // phpcs:ignore
				esc_html( $section->name ),
				$active_section === $section->name ? 'nav-tab-active' : '',
				esc_html( $section->tab_text )
			);
		}
		?>
		</h2>
		<?php

	}

	/**
	 * Registers settings sections at Settings > ConvertKit.
	 *
	 * Each section has its own tab.
	 *
	 * @since   1.9.6
	 */
	public function register_sections() {

		// Register the General and Tools settings sections.
		$sections = array(
			'general' => new ConvertKit_Settings_General(),
			'tools'   => new ConvertKit_Settings_Tools(),
		);

		/**
		 * Registers settings sections at Settings > ConvertKit.
		 *
		 * @since   1.9.6
		 *
		 * @param   array   $sections   Array of settings classes that handle individual tabs e.g. General, Tools etc.
		 */
		$sections = apply_filters( 'convertkit_admin_settings_register_sections', $sections );

		// With our sections now registered, assign them to this class.
		$this->sections = $sections;

	}

}
