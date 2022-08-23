<?php
/**
 * Outputs the template for Restrict Content Setup screen
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>
<header id="convertkit-onboarding-header">
	<h1><?php echo esc_html( CONVERTKIT_PLUGIN_NAME ); ?></h1>
</header>

<div class="convertkit-onboarding">
	<?php
	// If an error occured, display an error notice.
	if ( $this->error ) {
		?>
		<div class="wrap">
			<div class="notice notice-error is-dismissible">
				<p><?php echo esc_html( $this->error ); ?></p>
			</div>
		</div>
		<?php
	}
	?>

	<form action="admin.php?page=convertkit-restrict-content-setup&post_type=<?php echo esc_attr( $this->post_type ); ?>" method="POST" id="convertkit-onboarding-form">
		<div id="convertkit-onboarding-content">
			<h1><?php esc_html_e( 'Member Content', 'convertkit' ); ?></h1>
			<p>
				<?php esc_html_e( 'This will generate content that visitors can access once subscribed to a ConvertKit form / tag, or purchase a product.', 'convertkit' ); ?>
			</p>

			<hr />

			<h2><?php esc_html_e( 'What type of content are you offering?', 'convertkit' ); ?></h2>

			<div class="convertkit-onboarding-selection">
				<label for="download">
					<span>
						<strong><?php esc_html_e( 'Downloads', 'convertkit' ); ?></strong>
					</span>
					<input type="radio" name="type" id="download" value="download" checked />
					<span class="description">
						<?php esc_html_e( 'Require visitors subscribe to a form / tag, or purchase a product, to access a single Page\'s content, which includes downloadable assets.', 'convertkit' ); ?>
					</span>
				</label>

				<label for="course">
					<span>
						<strong><?php esc_html_e( 'Course', 'convertkit' ); ?></strong>
					</span>
					<input type="radio" name="type" id="course" value="course" />
					<span class="description">
						<?php esc_html_e( 'Require visitors subscribe to a form / tag, or purchase a product, to access a sequential series of Pages, such as a course, lessons or tutorials.', 'convertkit' ); ?>
					</span>
				</label>
			</div>

			<div>
				<label for="title">
					<?php esc_html_e( 'What is the name of the content?', 'convertkit' ); ?>
				</label>
				<input type="text" name="title" id="title" class="widefat" placeholder="<?php esc_attr_e( 'e.g. Free PDF, Macro Photography Course', 'convertkit' ); ?>" required />
			</div>

			<div>
				<label for="description">
					<?php esc_html_e( 'Describe the content for non-members.', 'convertkit' ); ?>
				</label>
				<textarea name="description" id="description" class="widefat" required></textarea>
				<p class="description"><?php esc_html_e( 'This will be displayed above the form, tag or product call to action.', 'convertkit' ); ?></p>
			</div>

			<div class="course">
				<div>
					<label for="number_of_pages">
						<?php esc_html_e( 'How many lessons does this course consist of?', 'convertkit' ); ?>
					</label>
					<input type="number" name="number_of_pages" min="1" max="99" step="1" id="number_of_pages" value="3" required />
				</div>
			</div>

			<div>
				<label for="wp-convertkit-restrict_content">
					<?php esc_html_e( 'The ConvertKit form / tag the visitor must be be subscribed to, or the product they must purchase, to see the content.', 'convertkit' ); ?>
				</label>

				<div class="convertkit-select2-container">
					<select name="restrict_content" id="wp-convertkit-restrict_content" class="convertkit-select2 widefat">
						<?php
						// Forms.
						if ( $this->forms->exist() ) {
							?>
							<optgroup label="<?php esc_attr_e( 'Forms', 'convertkit' ); ?>">
								<?php
								foreach ( $this->forms->get() as $form ) {
									?>
									<option value="form_<?php echo esc_attr( $form['id'] ); ?>">
										<?php echo esc_attr( $form['name'] ); ?>
									</option>
									<?php
								}
								?>
							</optgroup>
							<?php
						}

						// Tags.
						if ( $this->tags->exist() ) {
							?>
							<optgroup label="<?php esc_attr_e( 'Tags', 'convertkit' ); ?>">
								<?php
								foreach ( $this->tags->get() as $convertkit_tag ) {
									?>
									<option value="tag_<?php echo esc_attr( $convertkit_tag['id'] ); ?>">
										<?php echo esc_attr( $convertkit_tag['name'] ); ?>
									</option>
									<?php
								}
								?>
							</optgroup>
							<?php
						}

						// Products.
						if ( $this->products->exist() ) {
							?>
							<optgroup label="<?php esc_attr_e( 'Products', 'convertkit' ); ?>">
								<?php
								foreach ( $this->products->get() as $product ) {
									?>
									<option value="product_<?php echo esc_attr( $product['id'] ); ?>">
										<?php echo esc_attr( $product['name'] ); ?>
									</option>
									<?php
								}
								?>
							</optgroup>
							<?php
						}
						?>
					</select>
				</div>
			</div>
		</div>

		<div id="convertkit-onboarding-footer">
			<?php
			if ( isset( $back_button_label ) ) {
				?>
				<div class="left">
					<a href="<?php echo esc_attr( $back_button_url ); ?>" class="button"><?php echo esc_html( $back_button_label ); ?></a>
				</div>
				<?php
			}

			if ( isset( $next_button_label ) ) {
				?>
				<div class="right">
					<?php wp_nonce_field( 'convertkit-restrict-content-setup' ); ?>
					<button class="button button-primary button-large"><?php echo esc_html( $next_button_label ); ?></button>
				</div>
				<?php
			}
			?>
		</div>
	</form>
</div>
