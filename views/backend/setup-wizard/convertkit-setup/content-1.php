<?php
/**
 * Outputs the content for the Plugin Setup Wizard > Setup step.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>

<h1><?php esc_html_e( 'Welcome to the ConvertKit Setup Wizard', 'convertkit' ); ?></h1>
<p>
	<?php esc_html_e( 'This setup wizard will guide you through adding your first ConvertKit email marketing capture form to your site and begin capturing leads and subscribers.', 'convertkit' ); ?>
</p>

<p>
	<a href="<?php echo esc_attr( $this->next_step_url ); ?>" class="button button-primary">
		<?php esc_html_e( 'Start Wizard', 'convertkit' ); ?>
	</a>
</p>
