<?php
/**
 * TinyMCE Modal Missing view
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>

<form class="convertkit-tinymce-popup">
	<div class="notice error" style="display:block;">
		<?php esc_html_e( 'The block could not be found. Check it is registered and its class initialized.', 'convertkit' ); ?>
	</div>

	<button type="button" class="close button"><?php esc_html_e( 'Cancel', 'convertkit' ); ?></button>
</form>
