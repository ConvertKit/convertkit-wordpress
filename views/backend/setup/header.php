<?php
/**
 * Outputs the header template for the Restrict Content Setup screen
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta name="viewport" content="width=device-width"/>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title><?php echo esc_html( CONVERTKIT_PLUGIN_NAME ); ?> &lsaquo; <?php bloginfo( 'name' ); ?>  &#8212; WordPress</title>
		<?php
		do_action( 'admin_print_scripts' );
		do_action( 'admin_print_styles' );
		do_action( 'admin_head' );
		?>
	</head>
	<body class="wp-admin wp-core-ui convertkit">
