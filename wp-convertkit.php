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
 * Description: Display ConvertKit email subscription forms, landing pages, products, broadcasts and more.
 * Version: 2.4.6
 * Author: ConvertKit
 * Author URI: https://convertkit.com/
 * Text Domain: convertkit
 */

// Bail if ConvertKit is alread loaded.
if ( class_exists( 'WP_ConvertKit' ) ) {
	return;
}

// Define ConverKit Plugin paths and version number.
define( 'CONVERTKIT_PLUGIN_NAME', 'ConvertKit' ); // Used for user-agent in API class.
define( 'CONVERTKIT_PLUGIN_FILE', plugin_basename( __FILE__ ) );
define( 'CONVERTKIT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CONVERTKIT_PLUGIN_PATH', __DIR__ );
define( 'CONVERTKIT_PLUGIN_VERSION', '2.4.6' );

// Load shared classes, if they have not been included by another ConvertKit Plugin.
if ( ! class_exists( 'ConvertKit_API' ) ) {
	require_once CONVERTKIT_PLUGIN_PATH . '/vendor/convertkit/convertkit-wordpress-libraries/src/class-convertkit-api.php';
}
if ( ! class_exists( 'ConvertKit_Log' ) ) {
	require_once CONVERTKIT_PLUGIN_PATH . '/vendor/convertkit/convertkit-wordpress-libraries/src/class-convertkit-log.php';
}
if ( ! class_exists( 'ConvertKit_Resource' ) ) {
	require_once CONVERTKIT_PLUGIN_PATH . '/vendor/convertkit/convertkit-wordpress-libraries/src/class-convertkit-resource.php';
}
if ( ! class_exists( 'ConvertKit_Review_Request' ) ) {
	require_once CONVERTKIT_PLUGIN_PATH . '/vendor/convertkit/convertkit-wordpress-libraries/src/class-convertkit-review-request.php';
}

// Load plugin files that are always required.
require_once CONVERTKIT_PLUGIN_PATH . '/includes/cron-functions.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/functions.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-wp-convertkit.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-ajax.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-broadcasts-exporter.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-broadcasts-importer.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-cache-plugins.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-cron.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-gutenberg.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-media-library.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-output.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-output-restrict-content.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-post.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-preview-output.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-resource-creator-network-recommendations.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-resource-forms.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-resource-landing-pages.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-resource-posts.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-resource-products.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-resource-tags.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-settings.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-settings-broadcasts.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-settings-restrict-content.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-setup.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-shortcodes.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-subscriber.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-term.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-user.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/class-convertkit-widgets.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/blocks/class-convertkit-block.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/blocks/class-convertkit-block-broadcasts.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/blocks/class-convertkit-block-content.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/blocks/class-convertkit-block-form-trigger.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/blocks/class-convertkit-block-form.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/blocks/class-convertkit-block-product.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/block-formatters/class-convertkit-block-formatter.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/block-formatters/class-convertkit-block-formatter-form-link.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/block-formatters/class-convertkit-block-formatter-product-link.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/pre-publish-actions/class-convertkit-pre-publish-action.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/pre-publish-actions/class-convertkit-pre-publish-action-broadcast-export.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/widgets/class-ck-widget-form.php';

// Contact Form 7 Integration.
require_once CONVERTKIT_PLUGIN_PATH . '/includes/integrations/contactform7/class-convertkit-contactform7.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/integrations/contactform7/class-convertkit-contactform7-settings.php';

// Elementor Integration.
require_once CONVERTKIT_PLUGIN_PATH . '/includes/integrations/elementor/class-convertkit-elementor.php';

// Forminator Integration.
require_once CONVERTKIT_PLUGIN_PATH . '/includes/integrations/forminator/class-convertkit-forminator.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/integrations/forminator/class-convertkit-forminator-settings.php';

// WishList Member Integration.
require_once CONVERTKIT_PLUGIN_PATH . '/includes/integrations/wishlist/class-convertkit-wishlist.php';
require_once CONVERTKIT_PLUGIN_PATH . '/includes/integrations/wishlist/class-convertkit-wishlist-settings.php';

// WooCommerce Integration.
require_once CONVERTKIT_PLUGIN_PATH . '/includes/integrations/woocommerce/class-convertkit-woocommerce-product-form.php';

// Load files that are only used in the WordPress Administration interface.
if ( is_admin() ) {
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/class-convertkit-admin-bulk-edit.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/class-convertkit-admin-quick-edit.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/class-convertkit-admin-cache-plugins.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/class-convertkit-admin-category.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/class-convertkit-admin-notices.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/class-convertkit-admin-post.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/class-convertkit-admin-refresh-resources.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/class-convertkit-admin-settings.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/class-convertkit-admin-tinymce.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/class-convertkit-admin-user.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/class-convertkit-admin-setup-wizard.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/class-multi-value-field-table.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/section/class-convertkit-settings-base.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/section/class-convertkit-settings-general.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/section/class-convertkit-settings-tools.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/setup-wizard/class-convertkit-admin-setup-wizard-plugin.php';

	// Contact Form 7 Integration.
	require_once CONVERTKIT_PLUGIN_PATH . '/includes/integrations/contactform7/class-convertkit-contactform7-admin-settings.php';

	// Forminator Integration.
	require_once CONVERTKIT_PLUGIN_PATH . '/includes/integrations/forminator/class-convertkit-forminator-admin-settings.php';

	// WishList Member Integration.
	require_once CONVERTKIT_PLUGIN_PATH . '/includes/integrations/wishlist/class-convertkit-wishlist-admin-settings.php';

	// Restrict Content Integration.
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/class-convertkit-admin-restrict-content.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/class-convertkit-admin-settings-restrict-content.php';
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/setup-wizard/class-convertkit-admin-setup-wizard-restrict-content.php';

	// Broadcasts Integration.
	require_once CONVERTKIT_PLUGIN_PATH . '/admin/section/class-convertkit-admin-settings-broadcasts.php';
}

// Register Plugin activation and deactivation functions.
register_activation_hook( __FILE__, 'convertkit_plugin_activate' );
add_action( 'wp_insert_site', 'convertkit_plugin_activate_new_site' );
add_action( 'activate_blog', 'convertkit_plugin_activate_new_site' );
register_deactivation_hook( __FILE__, 'convertkit_plugin_deactivate' );

/**
 * Main function to return Plugin instance.
 *
 * @since   1.9.6
 */
function WP_ConvertKit() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName

	return WP_ConvertKit::get_instance();

}

// Finally, initialize the Plugin.
WP_ConvertKit();
