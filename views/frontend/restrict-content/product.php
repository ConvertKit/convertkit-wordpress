<?php
/**
 * Outputs the restricted content product message,
 * and a form for the subscriber to enter their
 * email address if they've already subscribed
 * to the ConvertKit Product.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>

<div id="convertkit-restrict-content">
	<h3><?php echo esc_html( $this->restrict_content_settings->get_by_key( 'subscribe_heading' ) ); ?></h3>
	<p>
		<?php
		foreach ( explode( "\n", $this->restrict_content_settings->get_by_key( 'subscribe_text' ) ) as $text_line ) {
			echo esc_html( $text_line ) . '<br />';
		}
		?>
	</p>

	<?php
	echo $button; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	// If scripts are disabled in the Plugin's settings, output the email login form now.
	if ( $this->settings->scripts_disabled() ) {
		?>
		<p>
			<?php echo esc_html( $this->restrict_content_settings->get_by_key( 'email_text' ) ); ?>
		</p>
		<?php
		require 'product-email.php';
	} else {
		// Just output the paragraph with a link to login, which will trigger the modal to display.
		?>
		<p>
			<?php echo esc_html( $this->restrict_content_settings->get_by_key( 'email_text' ) ); ?>
			<a href="#" class="convertkit-restrict-content-modal-open"><?php echo esc_attr( $this->restrict_content_settings->get_by_key( 'email_button_label' ) ); ?></a>
		</p>
		<?php
	}
	?>
</div>
