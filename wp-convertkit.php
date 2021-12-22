<?php
/**
 * ConvertKit WordPress Plugin.
 *
 * @package ConvertKit
 * @author ConvertKit
 *
 * @wordpress-plugin
 * Plugin Name: ConvertKit
 * Plugin URI: https://convertkit.com/
 * Description: Quickly and easily integrate ConvertKit forms into your site.
 * Version: 1.9.6.3
 * Author: ConvertKit
 * Author URI: https://convertkit.com/
 * Text Domain: convertkit
 */

// Bail if ConvertKit is alread loaded.
if ( class_exists( 'WP_ConvertKit' ) ) {
	return;
}

// Define ConverKit Plugin paths and version number.
define( 'CONVERTKIT_PLUGIN_FILE', plugin_basename( __FILE__ ) );
define( 'CONVERTKIT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CONVERTKIT_PLUGIN_PATH', __DIR__ );
define( 'CONVERTKIT_PLUGIN_VERSION', '1.9.6.3' );

// Load files that are always required.
require_once CONVERTKIT_PLUGIN_PATH . '/includes/functions.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-wp-convertkit.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-ajax.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-api.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-log.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-output.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-post.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-resource.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-resource-forms.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-resource-landing-pages.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-resource-tags.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-settings.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-shortcodes.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-system-info.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-term.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-user.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-widgets.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/blocks/class-convertkit-block.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/blocks/class-convertkit-block-content.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/blocks/class-convertkit-block-form.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/widgets/class-ck-widget-form.php';

// Contact Form 7 Integration.
require_once CONVERTKIT_PLUGIN_PATH . '/includes/integrations/contactform7/class-convertkit-contactform7.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/integrations/contactform7/class-convertkit-contactform7-settings.php';

// WishList Member Integration.
require_once CONVERTKIT_PLUGIN_PATH . '/includes/integrations/wishlist/class-convertkit-wishlist.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/integrations/wishlist/class-convertkit-wishlist-settings.php';

// WooCommerce Integration.
require_once CONVERTKIT_PLUGIN_PATH . '/includes/integrations/woocommerce/class-convertkit-woocommerce-product-form.php';

// Load files that are only used in the WordPress Administration interface.
if ( is_admin() ) {
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/class-convertkit-admin-category.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/class-convertkit-admin-gutenberg.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/class-convertkit-admin-post.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/class-convertkit-admin-settings.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/class-convertkit-admin-tinymce.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/class-convertkit-admin-upgrade.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/class-convertkit-admin-user.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/class-multi-value-field-table.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/section/class-convertkit-settings-base.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/section/class-convertkit-settings-general.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/section/class-convertkit-settings-tools.php';

	// Contact Form 7 Integration.
	require_once CONVERTKIT_PLUGIN_PATH . '/includes/integrations/contactform7/class-convertkit-contactform7-admin-settings.php';

	// WishList Member Integration.
	require_once CONVERTKIT_PLUGIN_PATH . '/includes/integrations/wishlist/class-convertkit-wishlist-admin-settings.php';
}

/**
 * Main function to return Plugin instance.
 *
 * @since   1.9.6
 */
function WP_ConvertKit() { // phpcs:ignore

	return WP_ConvertKit::get_instance();

}

// Finally, initialize the Plugin.
WP_ConvertKit();
