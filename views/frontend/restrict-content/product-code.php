<?php
/**
 * Outputs the subscriber code form, where the user can enter
 * the six digit code sent to their email after using the login
 * form, confirming they own the email address entered.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>

<div id="convertkit-restrict-content">
	<h4><?php echo esc_html( $this->restrict_content_settings->get_by_key( 'email_check_heading' ) ); ?></h4>
	<p>
		<?php echo esc_html( $this->restrict_content_settings->get_by_key( 'email_check_text' ) ); ?>
	</p>

	<div class="convertkit-restrict-content-actions">
		<form class="convertkit-restrict-content-subscriber-code" action="<?php echo esc_attr( add_query_arg( array( 'convertkit_login' => 1 ), get_permalink( $post_id ) ) ); ?>" method="post">
			<div id="convertkit-subscriber-code-container" class="<?php echo sanitize_html_class( ( is_wp_error( $error ) ? 'convertkit-restrict-content-error' : '' ) ); ?>">
				<input type="text" name="subscriber_code" id="convertkit_subscriber_code" inputmode="numeric" maxlength="6" pattern="[0-9]{6}" autocomplete="one-time-code" value="" />
			</div>

			<?php
			require 'notices.php';
			?>

			<div>
				<input type="submit" class="wp-block-button__link" value="<?php esc_html_e( 'Verify', 'convertkit' ); ?>" />
				<input type="hidden" name="token" value="<?php echo esc_attr( $this->token ); ?>" />
				<?php wp_nonce_field( 'convertkit_restrict_content_subscriber_code' ); ?>
			</div>
		</form>
	</div>
</div>
