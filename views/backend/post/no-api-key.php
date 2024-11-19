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
		esc_html__( 'For the Kit Plugin to function, please', 'convertkit' ),
		sprintf(
			'<a href="%s">%s</a>',
			esc_url( $api->get_oauth_url( admin_url( 'options-general.php?page=_wp_convertkit_settings' ), get_site_url() ) ),
			esc_html__( 'connect your Kit account.', 'convertkit' )
		)
	);
	?>
</p>
