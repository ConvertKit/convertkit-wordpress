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
			<?php
			require 'product-modal-content-email.php';
			?>
		</div>
	</div>
	<div id="convertkit-restrict-content-modal-loading"></div>
</div>