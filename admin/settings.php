<?php

class ConvertKitSettings {
	public $api;
	public $sections = array();
	public $settings_key  = WP_ConvertKit::SETTINGS_PAGE_SLUG;

	public function __construct() {
		$general_options = get_option($this->settings_key);
		$api_key         = $general_options && array_key_exists("api_key", $general_options) ? $general_options['api_key'] : null;
		$api_secret      = $general_options && array_key_exists("api_secret", $general_options) ? $general_options['api_secret'] : null;
		$debug           = $general_options && array_key_exists("debug", $general_options) ? $general_options['debug'] : null;
		$this->api       = new ConvertKitAPI( $api_key, $api_secret, $debug );

		add_action('admin_menu', array($this, 'add_settings_page'));
		add_action('admin_init', array($this, 'register_sections'));
	}

	/**
	 * Add the options page
	 */
	public function add_settings_page() {
		$settings = add_options_page(
			__('ConvertKit', 'convertkit'),
			__('ConvertKit', 'convertkit'),
			'manage_options',
			$this->settings_key,
			array($this, 'display_settings_page')
		);

		add_action('admin_print_styles', array($this, 'admin_styles'));
	}

	/**
	 * Options page callback
	 */
	public function display_settings_page() {
		$active_section = (isset($_GET['tab'])) ? $_GET['tab'] : $this->sections[0]->name;
		?>
		<div class="wrap convertkit-settings-wrap">
		<?php
			if(count($this->sections) > 1) {
				$this->display_section_nav($active_section);
			} else {
				?>
				<h2><?php _e('ConvertKit', 'convertkit'); ?></h2>
				<?php
			}
		?>

		<form method="post" action="options.php">
		<?php
			foreach($this->sections as $section):
				if ($active_section == $section->name):
					$section->render();
				endif;
			endforeach;
		?><p class="description"><?php _e( 'If you need help setting up the plugin please refer to the <a href="http://help.convertkit.com/article/99-the-convertkit-wordpress-plugin" target="_blank">plugin documentation.</a>', 'convertkit' ); ?></p>
		</form>
		</div>
		<?php
	}

	/**
	 * Queue up the admin styles
	 */
	public function admin_styles() {
		wp_enqueue_style('wp-convertkit-admin');
	}

	/**
	 * Render a tab for each section
	 *
	 * @param string $active_section The currently active section
	 */
	public function display_section_nav($active_section) {
        ?>
        <h1><?php _e('ConvertKit', 'convertkit'); ?></h1>
        <h2 class="nav-tab-wrapper">
        <?php
		foreach($this->sections as $section):
			printf(
			'<a href="?page=%s&tab=%s" class="nav-tab right %s">%s</a>',
				$this->settings_key,
				$section->name,
				$active_section == $section->name ? 'nav-tab-active' : '',
				esc_html($section->tab_text)
			);
		endforeach;
		?>
		</h2>
		<?php
	}

	/**
	 * Adds a section to be displayed
	 *
	 * @param string $section A section class name
	 */
	public function register_section($section) {
		$section_instance = new $section();

		if ($section_instance->is_registerable) {
			array_push($this->sections, $section_instance);
		}
	}

	/**
	 * Register each section
	 */
	public function register_sections() {
		wp_register_style( 'wp-convertkit-admin', plugins_url('../resources/backend/wp-convertkit.css', __FILE__) );

		$this->register_section('ConvertKitSettingsGeneral');
		$this->register_section('ConvertKitSettingsWishlistMember');
		$this->register_section('ConvertKitSettingsContactForm7');
	}
}

if( is_admin() ) {
	$convertkit_settings = new ConvertKitSettings();

	include 'section/base.php';
	include 'section/general.php';
	include 'section/wishlist_member.php';
	include 'section/contactform7.php';
}
