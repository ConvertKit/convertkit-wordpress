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
		<div id="convertkit-setup">
			<header id="convertkit-setup-header">
				<h1><?php echo esc_html( CONVERTKIT_PLUGIN_NAME ); ?></h1>
			</header>

			<div class="wrap">
				<div id="convertkit-setup-progress">
					<ol>
						<li class="done"><?php esc_html_e( 'Setup', 'page-generator-pro' ); ?></li>
						<li><?php esc_html_e( 'Done', 'page-generator-pro' ); ?></li>
					</ol>
				</div>

				<?php
				// If an error occured, display an error notice.
				if ( $this->error ) {
					?>
					<div class="notice notice-error is-dismissible">
						<p><?php echo esc_html( $this->error ); ?></p>
					</div>
					
					<?php
				}
				?>
			</div>

			<form action="admin.php?page=convertkit-setup" method="POST" id="convertkit-setup-form">
				<div id="convertkit-setup-content">
