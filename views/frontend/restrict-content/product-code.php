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
	<?php
	require 'notices.php';
	?>

	<div class="convertkit-restrict-content-actions">
		<form class="convertkit-restrict-content-subscriber-code" action="<?php echo esc_attr( add_query_arg( array( 'convertkit_login' => 1 ), get_permalink( $post_id ) ) ); ?>" method="post">
			<div>
				<input type="text" name="subscriber_code" id="convertkit_subscriber_code" inputmode="numeric" pattern="[0-9]{6}" autocomplete="one-time-code" value="" placeholder="<?php esc_attr_e( 'Email Code', 'convertkit' ); ?>" />

				<input type="submit" class="wp-block-button__link" value="<?php esc_html_e( 'Verify', 'convertkit' ); ?>" />

				<input type="hidden" name="token" value="<?php echo esc_attr( $this->token ); ?>" />

				<?php wp_nonce_field( 'convertkit_restrict_content_subscriber_code' ); ?>
			</div>
		</form>
	</div>
</div>
