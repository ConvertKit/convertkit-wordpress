<?php
/**
 * Outputs the content for Setup screen's first step.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>

		
<h1><?php esc_html_e( 'Connect your ConvertKit account', 'convertkit' ); ?></h1>
<p>
	<?php
	esc_html_e( 'To connect this Plugin to your ConvertKit account, we need your API Key and Secret.', 'convertkit' );
	?>

	<?php
	esc_html_e( 'Not sure what these are? Follow the video below.', 'convertkit' );
	?>
</p>

<div>

</div>

<div>
	<label for="api_key">
		<?php esc_html_e( 'Enter your ConvertKit API Key', 'convertkit' ); ?>
	</label>
	<input type="text" name="api_key" id="api_key" class="widefat" placeholder="<?php esc_attr_e( 'Click the link below, copy the API Key, and paste it here.', 'convertkit' ); ?>" required />
	<p class="description">
		<?php
		echo sprintf(
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
	<input type="text" name="api_secret" id="api_secret" class="widefat" placeholder="<?php esc_attr_e( 'Click the link below, copy the API Secret, and paste it here.', 'convertkit' ); ?>" required />
	<p class="description">
		<?php
		echo sprintf(
			/* translators: %1$s: Link to ConvertKit Account */
			esc_html__( '%1$s, and enter it in the above field.', 'convertkit' ),
			'<a href="' . esc_url( convertkit_get_api_key_url() ) . '" target="_blank">' . esc_html__( 'Click here to get your ConvertKit API Secret', 'convertkit' ) . '</a>'
		);
		?>
	</p>
</div>


