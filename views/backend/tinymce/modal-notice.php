<?php
/**
 * Displays an error notice in the TinyMCE modal.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>
<div class="notice notice-error">
	<p><?php echo esc_html( $notice['notice'] ); ?></p>
	<p>
		<a href="<?php echo esc_url( $notice['link'] ); ?>" target="_blank"><?php echo esc_html( $notice['link_text'] ); ?></a>
	</p>
</div>
