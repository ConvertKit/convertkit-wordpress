<?php
/**
 * Outputs the content for Setup screen's first step.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>

<h1><?php esc_html_e( 'Welcome to the ConvertKit Setup Wizard', 'convertkit' ); ?></h1>
<p>
	<?php esc_html_e( 'This setup wizard will guide you through adding your first ConvertKit email marketing capture form to your site and begin capturing leads and subscribers.', 'convertkit' ); ?>
</p>

<div class="convertkit-setup-grid">
    <div>
    	<h2><?php esc_html_e( 'I don\'t have a ConvertKit account.', 'convertkit' ); ?></h2>
        <a href="<?php echo esc_attr( convertkit_get_registration_url() ); ?>" class="button button-primary" target="_blank">
            <?php esc_html_e( 'Register', 'convertkit' ); ?>
        </a>
        <span class="description"><?php esc_html_e( 'Sign up to ConvertKit and register your account. It\'s free.', 'convertkit' ); ?></span>
    </div>

    <div>
        <h2><?php esc_html_e( 'I have a ConvertKit account.', 'convertkit' ); ?></h2>
        <a href="admin.php?page=convertkit-setup&step=2" class="button button-primary">
            <?php esc_html_e( 'Connect', 'convertkit' ); ?>
        </a>
        <span class="description"><?php esc_html_e( 'Great! Click the Connect button to get started.', 'convertkit' ); ?></span>
    </div>
</div>
