<?php
/**
 * Outputs the Restrict Content by Product Email Login Modal.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>

<div id="convertkit-restrict-content-modal-background">
	<div id="convertkit-restrict-content-modal">
		<button id="convertkit-restrict-content-modal-close"><?php esc_html_e( 'Close', 'convertkit' ); ?></button>
		<div id="convertkit-restrict-content-modal-content">
			<h3><?php echo esc_html( $this->restrict_content_settings->get_by_key( 'email_heading' ) ); ?></h3>
			<?php
			require 'email-login-form.php';
			?>
		</div>
	</div>
</div>