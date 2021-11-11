<!-- .wp-core-ui ensures styles are applied on frontend editors for e.g. buttons.css -->
<form class="convertkit-tinymce-popup wp-core-ui">
    <?php
    // Output each Field
    foreach ( $block['fields'] as $field_name => $field ) {
        include( 'modal-field-row.php' );
    }
    ?>
    
    <div class="convertkit-option buttons">
        <div class="left">
            <button type="button" class="close button"><?php _e( 'Cancel', 'convertkit' ); ?></button>
        </div>
        <div class="right">
            <input type="hidden" name="shortcode" value="convertkit_<?php echo $block['name']; ?>" />
            <?php
            if ( $block['shortcode_include_closing_tag'] ) {
            	?>
            	<input type="hidden" name="close_shortcode" value="1" />
            	<?php
            }
            ?>
            <input type="button" value="<?php _e( 'Insert', 'convertkit' ); ?>" class="button button-primary right" />
        </div>
    </div>
</form>