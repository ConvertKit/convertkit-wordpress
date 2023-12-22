<?php
/**
 * ConvertKit Media Library class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Class to import a remote image to the WordPress Media Library.
 *
 * @since   2.2.8
 */
class ConvertKit_Media_Library {

	/**
	 * Imports a remote image into the WordPress Media Library
	 *
	 * @since   2.2.8
	 *
	 * @param   string $remote_image_url   Source URL.
	 * @param   int    $post_id            Post ID to attach the Media Library image to.
	 * @param   string $alt_tag            Image Alt Tag (optional).
	 * @return  WP_Error|int
	 */
	public function import_remote_image( $remote_image_url, $post_id, $alt_tag = '' ) {

		// Load required functions for image importing.
		if ( ! function_exists( 'media_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
		}

		// Get the remote image.
		$tmp = download_url( $remote_image_url );
		if ( is_wp_error( $tmp ) ) {
			return $tmp;
		}

		// Get image type.
		$type = getimagesize( $tmp );
		if ( ! isset( $type['mime'] ) ) {
			return new WP_Error( __( 'Could not identify MIME type of imported image.', 'convertkit' ) );
		}
		list( $type, $ext ) = explode( '/', $type['mime'] );
		unset( $type );

		// Define image filename, excluding any parameters.
		$file_array['name']     = strtok( basename( $remote_image_url ), '?' );
		$file_array['tmp_name'] = $tmp;

		// Add the extension to the filename, if it doesn't exist.
		// This happens if we streamed an image URL with no extension specified.
		// e.g. https://embed.filekitcdn.com/e/pX62TATVeCKK5QzkXWNLw3/iLRneK6yxY4WwQUdkkUMaq.
		switch ( $ext ) {
			case 'jpeg':
			case 'jpg':
				// If neither .jpeg or .jpg exist, append the extension.
				if ( strpos( $file_array['name'], '.jpg' ) === false && strpos( $file_array['name'], '.jpeg' ) === false ) {
					$file_array['name'] .= '.jpg';
				}
				break;

			default:
				if ( strpos( $file_array['name'], '.' . $ext ) === false ) {
					$file_array['name'] .= '.' . $ext;
				}
				break;
		}

		// Import the image into the Media Library.
		$image_id = media_handle_sideload( $file_array, $post_id, '' );
		if ( is_wp_error( $image_id ) ) {
			return $image_id;
		}

		// If an alt tag has been specified, set it now.
		if ( ! empty( $alt_tag ) ) {
			update_post_meta( $image_id, '_wp_attachment_image_alt', $alt_tag );
		}

		// Return the image ID.
		return $image_id;

	}

}
