<?php
/**
 * Outputs error notices in the restrict content call to action.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

// If an error occured, show it now.
if ( is_wp_error( WP_ConvertKit()->get_class( 'output_restrict_content' )->error ) ) {
	?>
	<div class="convertkit-restrict-content-notice convertkit-restrict-content-notice-error"><?php echo esc_html( WP_ConvertKit()->get_class( 'output_restrict_content' )->error->get_error_message() ); ?></div>
	<?php
}
