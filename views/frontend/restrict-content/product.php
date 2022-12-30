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
	<?php
	require 'notices.php';
	?>

	<div class="convertkit-restrict-content-actions">
		<p><?php echo esc_html( $this->restrict_content_settings->get_by_key( 'subscribe_text' ) ); ?></p>

		<?php
		echo $button; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>

		<hr />

		<p>
			<?php
			echo esc_html( $this->restrict_content_settings->get_by_key( 'email_text' ) );
			?>
		</p>

		<form class="convertkit-restrict-content-login" action="<?php echo esc_attr( add_query_arg( array( 'convertkit_login' => 1 ), get_permalink( $post_id ) ) ); ?>#convertkit-restrict-content" method="post">
			<div>
				<input type="email" name="convertkit_email" id="convertkit_email" value="" placeholder="example@convertkit.com" />
			</div>
			<div>
				<input type="submit" class="wp-block-button__link wp-block-button__link" value="<?php echo esc_attr( $this->restrict_content_settings->get_by_key( 'email_button_label' ) ); ?>" />

				<?php wp_nonce_field( 'convertkit_restrict_content_login' ); ?>
			</div>
		</form>
	</div>
</div>
