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
		<h3><?php echo esc_html( $this->restrict_content_settings->get_by_key( 'subscribe_heading_tag' ) ); ?></h3>
		<p>
			<?php
			foreach ( explode( "\n", $this->restrict_content_settings->get_by_key( 'subscribe_text' ) ) as $text_line ) {
				echo esc_html( $text_line ) . '<br />';
			}
			?>
		</p>

		<form class="convertkit-restrict-content-login" action="<?php echo esc_attr( add_query_arg( array( 'convertkit_login' => 1 ), get_permalink( $post_id ) ) ); ?>#convertkit-restrict-content" method="post">
			<div>
				<input type="email" name="convertkit_email" id="convertkit_email" value="" placeholder="<?php esc_attr_e( 'Email Address', 'convertkit' ); ?>" />
				<input type="submit" class="wp-block-button__link wp-block-button__link" value="<?php echo esc_attr( $this->restrict_content_settings->get_by_key( 'subscribe_button_label' ) ); ?>" />
				<input type="hidden" name="convertkit_resource_type" value="<?php echo esc_attr( $resource_type ); ?>" />
				<input type="hidden" name="convertkit_resource_id" value="<?php echo esc_attr( $resource_id ); ?>" />
				
				<?php wp_nonce_field( 'convertkit_restrict_content_login' ); ?>
			</div>
		</form>
	</div>
</div>
