<?php
/**
 * Outputs the content for the Plugin Setup Wizard > Connect Account step.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>

<h1><?php esc_html_e( 'Connect your ConvertKit account', 'convertkit' ); ?></h1>

<?php
if ( ! $this->is_modal() ) {
	?>
	<p>
		<?php
		esc_html_e( 'To connect this Plugin to your ConvertKit account, we need your API Key and Secret.', 'convertkit' );
		?>
	</p>

	<hr />
	<?php
}
?>

<div>
	<label for="api_key">
		<?php esc_html_e( 'Enter your ConvertKit API Key', 'convertkit' ); ?>
	</label>
	<input type="text" name="api_key" id="api_key" value="<?php echo esc_attr( $this->settings->get_api_key() ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'Click the link below, copy the API Key, and paste it here.', 'convertkit' ); ?>" required />
	<p class="description">
		<?php
		printf(
			/* translators: %1$s: Link to ConvertKit Account */
			esc_html__( '%1$s, and enter it in the above field.', 'convertkit' ),
			'<a href="' . esc_url( convertkit_get_api_key_url() ) . '" target="_blank">' . esc_html__( 'Click here to get your ConvertKit API Key', 'convertkit' ) . '</a>'
		);
		?>
	</p>
</div>

<div>
	<label for="api_secret">
		<?php esc_html_e( 'Enter your ConvertKit API Secret', 'convertkit' ); ?>
	</label>
	<input type="text" name="api_secret" id="api_secret" value="<?php echo esc_attr( $this->settings->get_api_secret() ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'Click the link below, copy the API Secret, and paste it here.', 'convertkit' ); ?>" required />
	<p class="description">
		<?php
		printf(
			/* translators: %1$s: Link to ConvertKit Account */
			esc_html__( '%1$s, and enter it in the above field.', 'convertkit' ),
			'<a href="' . esc_url( convertkit_get_api_key_url() ) . '" target="_blank">' . esc_html__( 'Click here to get your ConvertKit API Secret', 'convertkit' ) . '</a>'
		);
		?>
	</p>
</div>


