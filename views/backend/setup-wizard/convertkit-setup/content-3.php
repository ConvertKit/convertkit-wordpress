<?php
/**
 * Outputs the content for the Plugin Setup Wizard > Form Configuration step.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

// If no Forms exist on the ConvertKit account, show the user how to add a Form to ConvertKit,
// with an option to refresh this page so that they can then select the Form to be displayed on e.g. Posts.
if ( ! $this->forms->exist() ) {
	?>
	<h1><?php esc_html_e( 'Create your first ConvertKit Form', 'convertkit' ); ?></h1>
	<p>
		<?php
		esc_html_e( 'To capture email leads, you first need to create a form in ConvertKit. Click the button below to get started.', 'convertkit' );
		?>
	</p>

	<a href="https://app.convertkit.com/forms/new?format=inline" target="_blank" class="button button-primary">
		<?php esc_html_e( 'Create form', 'convertkit' ); ?>
	</a>

	<p>
		<?php
		esc_html_e( 'Not sure how to do this? Follow the video below.', 'convertkit' );
		?>
	</p>

	<div>
		<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/_RmI6vQhGu8" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</div>

	<a href="<?php echo esc_attr( $this->current_step_url ); ?>" class="button button-primary">
		<?php esc_html_e( 'I\'ve created a form in ConvertKit.', 'convertkit' ); ?>
	</a>
	<?php
} else {
	// Show options to configure Form to display on Posts and Pages.
	?>
	<h1><?php esc_html_e( 'Display an email capture form', 'convertkit' ); ?></h1>
	<p>
		<?php
		esc_html_e( 'To capture email leads, you need to display a ConvertKit form on your site, using the options below.', 'convertkit' );
		?>
	</p>

	<hr />

	<div>
		<label for="wp-convertkit-form-posts">
			<?php esc_html_e( 'Which form would you like to display on individual blog posts?', 'convertkit' ); ?>
		</label>
		<select name="post_form" id="wp-convertkit-form-posts" class="convertkit-select2 convertkit-update-link widefat" data-target="#convertkit-preview-form-post" data-link="<?php echo esc_attr( $this->preview_post_url ); ?>&convertkit_form_id=">
			<option value="0">
				<?php esc_html_e( 'Don\'t display an email subscription form on Posts.', 'convertkit' ); ?>
			</option>	
			<?php
			foreach ( $this->forms->get() as $form ) {
				?>
				<option value="<?php echo esc_attr( $form['id'] ); ?>"<?php selected( $this->settings->get_default_form( 'post' ), $form['id'] ); ?>>
					<?php echo esc_attr( $form['name'] ); ?>
				</option>
				<?php
			}
			?>
		</select>

		<p class="description">
			<?php
			echo sprintf(
				'%s %s %s',
				esc_html__( 'Select a form above.', 'convertkit' ),
				'<a href="' . esc_attr( $this->preview_post_url ) . '" id="convertkit-preview-form-post" target="_blank">' . esc_html__( 'Click here', 'convertkit' ) . '</a>',
				esc_html__( 'to preview how this will look on individual Posts.', 'convertkit' )
			);
			?>
		</p>
	</div>

	<div>
		<label for="wp-convertkit-form-pages">
			<?php esc_html_e( 'Which form would you like to display on Pages?', 'convertkit' ); ?>
		</label>
		<select name="page_form" id="wp-convertkit-form-pages" class="convertkit-select2 convertkit-update-link widefat" data-target="#convertkit-preview-form-page" data-link="<?php echo esc_attr( $this->preview_page_url ); ?>&convertkit_form_id=">	
			<option value="0">
				<?php esc_html_e( 'Don\'t display an email subscription form on Pages.', 'convertkit' ); ?>
			</option>
			<?php
			foreach ( $this->forms->get() as $form ) {
				?>
				<option value="<?php echo esc_attr( $form['id'] ); ?>"<?php selected( $this->settings->get_default_form( 'page' ), $form['id'] ); ?>>
					<?php echo esc_attr( $form['name'] ); ?>
				</option>
				<?php
			}
			?>
		</select>

		<p class="description">
			<?php
			echo sprintf(
				'%s %s %s',
				esc_html__( 'Select a form above.', 'convertkit' ),
				'<a href="' . esc_attr( $this->preview_page_url ) . '" id="convertkit-preview-form-page" target="_blank">' . esc_html__( 'Click here', 'convertkit' ) . '</a>',
				esc_html__( 'to preview how this will look on individual Pages.', 'convertkit' )
			);
			?>
		</p>
	</div>
	<?php
}
