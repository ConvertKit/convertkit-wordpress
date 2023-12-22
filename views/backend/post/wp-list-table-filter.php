<?php
/**
 * Outputs a dropdown filter comprising of Tags and Products
 *
 * @package ConvertKit
 * @author ConvertKit
 */

?>
<select name="convertkit_restrict_content" id="wp-convertkit-restrict-content-filter">
	<option value="0"><?php esc_html_e( 'All content', 'convertkit' ); ?></option>

	<?php
	// Tags.
	if ( $this->tags->exist() ) {
		?>
		<optgroup label="<?php esc_attr_e( 'Tags', 'convertkit' ); ?>">
			<?php
			foreach ( $this->tags->get() as $convertkit_tag ) {
				?>
				<option value="tag_<?php echo esc_attr( $convertkit_tag['id'] ); ?>"<?php selected( 'tag_' . $convertkit_tag['id'], $this->restrict_content_filter ); ?>><?php echo esc_attr( $convertkit_tag['name'] ); ?></option>
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
				<option value="product_<?php echo esc_attr( $product['id'] ); ?>"<?php selected( 'product_' . $product['id'], $this->restrict_content_filter ); ?>><?php echo esc_attr( $product['name'] ); ?></option>
				<?php
			}
			?>
		</optgroup>
		<?php
	}
	?>
</select>
