<?php
/**
 * Outputs success / error notices in the restrict content call to action.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

// If a success message exists, show it now.
if ( $this->success !== false ) {
	?>
	<div class="convertkit-restrict-content-notice convertkit-restrict-content-notice-success">
		<?php echo esc_html( $this->success ); ?>
	</div>
	<?php
}

// If an error occured, show it now.
if ( is_wp_error( $this->error ) ) {
	?>
	<div class="convertkit-restrict-content-notice convertkit-restrict-content-notice-error">
		<?php echo esc_html( $this->error->get_error_message() ); ?>
	</div>
	<?php
}
