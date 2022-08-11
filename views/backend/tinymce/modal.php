<?php
/**
 * TinyMCE Modal view
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>

<!-- .wp-core-ui ensures styles are applied on frontend editors for e.g. buttons.css -->
<form class="convertkit-tinymce-popup wp-core-ui">
	<?php
	// Output each Field.
	foreach ( $shortcode['fields'] as $field_name => $field ) {
		include 'modal-field-row.php';
	}
	?>

	<div class="convertkit-option buttons">
		<div class="left">
			<button type="button" class="close button"><?php esc_html_e( 'Cancel', 'convertkit' ); ?></button>
		</div>
		<div class="right">
			<input type="hidden" name="shortcode" value="convertkit_<?php echo esc_attr( $shortcode['name'] ); ?>" />
			<input type="hidden" name="editor_type" value="<?php echo esc_attr( $editor_type ); // quicktags|tinymce. ?>" />

			<?php
			if ( $shortcode['shortcode_include_closing_tag'] ) {
				?>
				<input type="hidden" name="close_shortcode" value="1" />
				<?php
			}
			?>
			<input type="button" value="<?php esc_html_e( 'Insert', 'convertkit' ); ?>" class="button button-primary right" />
		</div>
	</div>
</form>
