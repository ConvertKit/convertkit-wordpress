<?php
/**
 * Outputs the content for the Restrict Content Setup Wizard > Configure step.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>

<h1>
	<?php
	printf(
		/* translators: Type of content (download, course) */
		esc_html__( 'Configure %s', 'convertkit' ),
		esc_html( $this->type_label )
	);
	?>
</h1>

<hr />

<div>
	<label for="title">
		<?php esc_html_e( 'What is the name of the content?', 'convertkit' ); ?>
	</label>
	<input type="text" name="title" id="title" class="widefat" placeholder="<?php esc_attr_e( 'e.g. Free PDF, Macro Photography Course', 'convertkit' ); ?>" required />
	<input type="hidden" name="type" value="<?php echo esc_attr( $this->type ); ?>" />
</div>

<div>
	<label for="description">
		<?php esc_html_e( 'Describe the content for non-members.', 'convertkit' ); ?>
	</label>
	<textarea name="description" id="description" class="widefat" required></textarea>
	<p class="description"><?php esc_html_e( 'This will be displayed above product\'s call to action button.', 'convertkit' ); ?></p>
</div>

<?php
if ( $this->type === 'course' ) {
	?>
	<div class="course">
		<div>
			<label for="number_of_pages">
				<?php esc_html_e( 'How many lessons does this course consist of?', 'convertkit' ); ?>
			</label>
			<input type="number" name="number_of_pages" min="1" max="99" step="1" id="number_of_pages" value="3" required />
		</div>
	</div>
	<?php
}
?>

<div>
	<label for="wp-convertkit-restrict_content">
		<?php esc_html_e( 'The Kit Product or Tag the visitor must subscribe to, in order to see the content', 'convertkit' ); ?>
	</label>

	<div class="convertkit-select2-container">
		<select name="restrict_content" id="wp-convertkit-restrict_content" class="convertkit-select2 widefat">
			<?php
			// Tags.
			if ( $this->tags->exist() ) {
				?>
				<optgroup label="<?php esc_attr_e( 'Tags', 'convertkit' ); ?>" data-resource="tags">
					<?php
					foreach ( $this->tags->get() as $convertkit_tag ) {
						?>
						<option value="tag_<?php echo esc_attr( $convertkit_tag['id'] ); ?>"><?php echo esc_attr( $convertkit_tag['name'] ); ?></option>
						<?php
					}
					?>
				</optgroup>
				<?php
			}

			// Products.
			if ( $this->products->exist() ) {
				?>
				<optgroup label="<?php esc_attr_e( 'Products', 'convertkit' ); ?>" data-resource="products">
					<?php
					foreach ( $this->products->get() as $product ) {
						?>
						<option value="product_<?php echo esc_attr( $product['id'] ); ?>"><?php echo esc_attr( $product['name'] ); ?></option>
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
