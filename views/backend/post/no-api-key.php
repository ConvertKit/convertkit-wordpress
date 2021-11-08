<p>
	<?php 
	echo sprintf(
		/* translators: %1$s: Post Type Singular Name, %2$s: Link to Plugin Settings */
		__( 'To configure the ConvertKit Form / Landing Page to display on this %1$s, enter your ConvertKit API credentials in the %2$s', 'convertkit' ),
		$post_type->labels->singular_name,
		'<a href="' . convertkit_get_settings_link() . '">' . __( 'Plugin Settings', 'convertkit' ) . '</a>'
	);
	?>
</p>