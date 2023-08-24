<?php
/**
 * Outputs a message in the metabox when no API Key is defined.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>

<p>
	<?php
	printf(
		/* translators: %1$s: Post Type Singular Name, %2$s: Link to Plugin Settings */
		esc_html__( 'To configure the ConvertKit Form / Landing Page to display on this %1$s, enter your ConvertKit API credentials in the %2$s', 'convertkit' ),
		esc_attr( $post_type->labels->singular_name ),
		'<a href="' . esc_url( convertkit_get_settings_link() ) . '">' . esc_html__( 'Plugin Settings', 'convertkit' ) . '</a>'
	);
	?>
</p>
