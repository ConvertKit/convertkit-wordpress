<?php
/**
 * Outputs a message in the metabox when no Access Token is defined.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>

<p>
	<?php
	printf(
		'%s %s',
		esc_html__( 'For the ConvertKit Plugin to function, please', 'convertkit' ),
		sprintf(
			'<a href="%s">%s</a>',
			esc_url( $api->get_oauth_url( admin_url( 'options-general.php?page=_wp_convertkit_settings' ) ) ),
			esc_html__( 'connect your ConvertKit account.', 'convertkit' )
		)
	);
	?>
</p>
