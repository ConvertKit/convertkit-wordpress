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
		esc_html_e( 'To get email subscribers, you first need to create a form in ConvertKit. Click the button below to get started.', 'convertkit' );
		?>
	</p>

	<hr />

	<a href="<?php echo esc_url( convertkit_get_new_form_url() ); ?>" target="_blank" class="button button-primary button-hero">
		<?php esc_html_e( 'Create form', 'convertkit' ); ?>
	</a>

	<p>
		<?php
		printf(
			'%1$s <a href="https://help.convertkit.com/en/articles/3860348-how-to-create-your-first-form-in-convertkit" target="_blank">%2$s</a>',
			esc_html__( 'Not sure how to do this in ConvertKit?', 'convertkit' ),
			esc_html__( 'Follow our step by step documentation', 'convertkit' )
		);
		?>
	</p>
	<?php
} else {
	// Show options to configure Form to display on Posts and Pages.
	?>
	<h1><?php esc_html_e( 'Display an email capture form', 'convertkit' ); ?></h1>
	<p>
		<?php
		esc_html_e( 'To get email subscribers, you need to display a ConvertKit form on your site, using the options below.', 'convertkit' );
		?>
	</p>

	<hr />

	<div>
		<label for="wp-convertkit-form-posts">
			<?php esc_html_e( 'Which form would you like to display below all blog posts?', 'convertkit' ); ?>
		</label>
		<select name="post_form" id="wp-convertkit-form-posts" class="convertkit-select2 convertkit-preview-output-link widefat" data-target="#convertkit-preview-form-post" data-link="<?php echo esc_attr( $this->preview_post_url ); ?>&convertkit_form_id=">
			<option value="0"><?php esc_html_e( 'Don\'t display an email subscription form on posts.', 'convertkit' ); ?></option>	
			<?php
			foreach ( $this->forms->get() as $form ) {
				?>
				<option value="<?php echo esc_attr( $form['id'] ); ?>"<?php selected( $this->settings->get_default_form( 'post' ), $form['id'] ); ?>><?php echo esc_attr( $form['name'] ); ?></option>
				<?php
			}
			?>
		</select>

		<p class="description">
			<?php
			if ( $this->preview_post_url ) {
				printf(
					'%s %s %s',
					esc_html__( 'Select a form above.', 'convertkit' ),
					'<a href="' . esc_url( $this->preview_post_url ) . '" id="convertkit-preview-form-post" target="_blank">' . esc_html__( 'Click here', 'convertkit' ) . '</a>',
					esc_html__( 'to preview how this will look on individual posts.', 'convertkit' )
				);
			} else {
				esc_html_e( 'Select a form above.', 'convertkit' );
			}
			?>
		</p>
	</div>

	<div>
		<label for="wp-convertkit-form-pages">
			<?php esc_html_e( 'Which form would you like to display below all Pages?', 'convertkit' ); ?>
		</label>
		<select name="page_form" id="wp-convertkit-form-pages" class="convertkit-select2 convertkit-preview-output-link widefat" data-target="#convertkit-preview-form-page" data-link="<?php echo esc_attr( $this->preview_page_url ); ?>&convertkit_form_id=">	
			<option value="0"><?php esc_html_e( 'Don\'t display an email subscription form on pages.', 'convertkit' ); ?></option>
			<?php
			foreach ( $this->forms->get() as $form ) {
				?>
				<option value="<?php echo esc_attr( $form['id'] ); ?>"<?php selected( $this->settings->get_default_form( 'page' ), $form['id'] ); ?>><?php echo esc_attr( $form['name'] ); ?></option>
				<?php
			}
			?>
		</select>

		<p class="description">
			<?php
			if ( $this->preview_page_url ) {
				printf(
					'%s %s %s',
					esc_html__( 'Select a form above.', 'convertkit' ),
					'<a href="' . esc_url( $this->preview_page_url ) . '" id="convertkit-preview-form-page" target="_blank">' . esc_html__( 'Click here', 'convertkit' ) . '</a>',
					esc_html__( 'to preview how this will look on individual pages.', 'convertkit' )
				);
			} else {
				esc_html_e( 'Select a form above.', 'convertkit' );
			}
			?>
		</p>
	</div>

	<div class="notice notice-info">
		<p class="description">
			<?php
			esc_html_e( 'To embed email subscriber forms in particular sections of your content on specific Pages or Posts, use the ConvertKit Form block or shortcode when editing a Page or Post.', 'convertkit' );
			?>
		</p>
	</div>
	<?php
}
