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
		esc_html_e( 'For the ConvertKit Plugin to function, please connect your ConvertKit account using the button below.', 'convertkit' );
		?>
	</p>

	<hr />
	<?php
}
?>
