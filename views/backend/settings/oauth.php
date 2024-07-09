<?php
/**
 * Settings > Tools view
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>
<div class="metabox-holder">
	<div id="oauth" class="postbox">
		<h2><?php esc_html_e( 'Connect to ConvertKit', 'convertkit' ); ?></h2>

		<p class="description">
			<?php esc_html_e( 'For the ConvertKit Plugin to function, please connect your ConvertKit account using the button below.', 'convertkit' ); ?><br />
		</p>

		<p>
			<a href="<?php echo esc_url( $oauth_url ); ?>" class="button button-primary"><?php esc_html_e( 'Connect', 'convertkit' ); ?></a>
		</p>
	</div><!-- .postbox -->

	<?php
	wp_nonce_field( 'convertkit-settings-oauth', '_convertkit_settings_oauth_nonce' );
	?>
</div><!-- .metabox-holder -->
