<?php
/**
 * ConvertKit Broadcasts Importer class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Class to publish WordPress Posts based on ConvertKit Broadcasts,
 * if the Broadcasts functionality is enabled in the Plugin's settings.
 *
 * @since   2.2.9
 */
class ConvertKit_Broadcasts_Importer {

	/**
	 * Holds the Broadcasts Settings class.
	 *
	 * @since   2.2.9
	 *
	 * @var     bool|ConvertKit_Settings_Broadcasts
	 */
	private $broadcasts_settings = false;

	/**
	 * Constructor. Registers actions and filters to output ConvertKit Forms and Landing Pages
	 * on the frontend web site.
	 *
	 * @since   2.2.9
	 */
	public function __construct() {

		// Create WordPress Posts when the ConvertKit Posts Resource is refreshed.
		add_action( 'convertkit_resource_refreshed_posts', array( $this, 'refresh' ) );

	}

	/**
	 * When the list of broadcasts is refreshed by the resource class, iterate through
	 * each Broadcast, to check if a Post exists in WordPress for it.
	 *
	 * If not, creates the WordPress Post.
	 *
	 * @since   2.2.9
	 *
	 * @param   array $broadcasts     Broadcasts.
	 */
	public function refresh( $broadcasts ) {

		// Initialize required classes.
		$this->broadcasts_settings = new ConvertKit_Settings_Broadcasts();
		$settings                  = new ConvertKit_Settings();
		$log                       = new ConvertKit_Log( CONVERTKIT_PLUGIN_PATH );

		// Bail if Broadcasts to Posts are disabled.
		if ( ! $this->broadcasts_settings->enabled() ) {
			return;
		}

		// Bail if no Broadcasts exist.
		if ( ! count( $broadcasts ) ) {
			return;
		}

		// Bail if the Plugin API keys have not been configured.
		if ( ! $settings->has_api_key_and_secret() ) {
			return;
		}

		// Initialize the API.
		$api = new ConvertKit_API( $settings->get_api_key(), $settings->get_api_secret(), $settings->debug_enabled() );

		// Check that we're using the ConvertKit WordPress Libraries 1.3.8 or higher.
		// If another ConvertKit Plugin is active and out of date, its libraries might
		// be loaded that don't have this method.
		if ( ! method_exists( $api, 'get_post' ) ) {
			return;
		}

		// Iterate through each Broadcast.
		foreach ( $broadcasts as $broadcast_id => $broadcast ) {
			// If a WordPress Post exists for this Broadcast ID, we previously imported it - skip it.
			if ( $this->broadcast_exists_as_post( $broadcast_id ) ) {
				if ( $settings->debug_enabled() ) {
					$log->add( 'ConvertKit_Broadcasts_Importer::refresh(): Broadcast #' . $broadcast_id . ' already exists as a WordPress Post. Skipping...' );
				}
				continue;
			}

			// Fetch Broadcast's content.
			// We need to query wordpress/posts/{id} to fetch the full Broadcast information and content.
			$broadcast = $api->get_post( $broadcast_id );

			// Skip if an error occured fetching the Broadcast.
			if ( is_wp_error( $broadcast ) ) {
				if ( $settings->debug_enabled() ) {
					$log->add( 'ConvertKit_Broadcasts_Importer::refresh(): Broadcast #' . $broadcast_id . '. Error fetching from API: ' . $broadcast->get_error_message() );
				}
				continue;
			}

			// Skip if the published_at date is older than the 'Earliest Date' setting.
			if ( strtotime( $broadcast['published_at'] ) < strtotime( $this->broadcasts_settings->published_at_min_date() ) ) {
				if ( $settings->debug_enabled() ) {
					$log->add( 'ConvertKit_Broadcasts_Importer::refresh(): Broadcast #' . $broadcast_id . ' published_at date is before ' . $this->broadcasts_settings->published_at_min_date() . '. Skipping...' );
				}
				continue;
			}

			// Create Post as a draft.
			$post_id = wp_insert_post(
				$this->build_post_args(
					$broadcast,
					$this->broadcasts_settings->author_id(),
					$this->broadcasts_settings->category_id()
				),
				true
			);

			// Skip if an error occured.
			if ( is_wp_error( $post_id ) ) {
				if ( $settings->debug_enabled() ) {
					$log->add( 'ConvertKit_Broadcasts_Importer::refresh(): Broadcast #' . $broadcast_id . '. Error on wp_insert_post(): ' . $post_id->get_error_message() );
				}
				continue;
			}

			// If a Product is specified, apply it as the Restrict Content setting.
			if ( $broadcast['is_paid'] && $broadcast['product_id'] ) {
				// Fetch Post's settings.
				$convertkit_post = new ConvertKit_Post( $post_id );
				$meta            = $convertkit_post->get();

				// Define Restrict Content setting.
				$meta['restrict_content'] = 'product_' . $broadcast['product_id'];

				// Save Post's settings.
				$convertkit_post->save( $meta );

				if ( $settings->debug_enabled() ) {
					$log->add( 'ConvertKit_Broadcasts_Importer::refresh(): Broadcast #' . $broadcast_id . '. Set Restrict Content = ' . $broadcast['product_id'] );
				}
			}

			// If the Broadcast has an image, save it to the Media Library and link it to the Post.
			$this->add_broadcast_image_to_post( $broadcast, $post_id );

			// Transition the Post to the defined Post Status in the settings, now that the image has been added to it.
			$post_id = wp_update_post(
				array(
					'ID'          => $post_id,
					'post_status' => $this->broadcasts_settings->post_status(),
				),
				true
			);

			// Maybe log if an error occured updating the Post to the publish status.
			if ( is_wp_error( $post_id ) ) {
				if ( $settings->debug_enabled() ) {
					$log->add( 'ConvertKit_Broadcasts_Importer::refresh(): Broadcast #' . $broadcast_id . '. Error on wp_update_post(): ' . $post_id->get_error_message() );
				}
			}
			if ( $settings->debug_enabled() ) {
				$log->add( 'ConvertKit_Broadcasts_Importer::refresh(): Broadcast #' . $broadcast_id . '. Added as Post ID #' . $post_id );
			}
		}

	}

	/**
	 * Helper method to determine if the given ConvertKit Broadcast already exists
	 * as a WordPress Post.
	 *
	 * @since   2.2.7
	 *
	 * @param   int $broadcast_id   ConvertKit Broadcast ID.
	 * @return  bool                    Broadcast exists as a WordPress Post
	 */
	private function broadcast_exists_as_post( $broadcast_id ) {

		$posts = new WP_Query(
			array(
				'post_type'         => 'post',
				'post_status'       => 'any',
				'meta_query'        => array(
					array(
						'key'   => '_convertkit_broadcast_id',
						'value' => $broadcast_id,
					),
				),
				'fields'            => 'ids',
				'update_post_cache' => false,
			)
		);

		if ( ! $posts->post_count ) {
			return false;
		}

		return true;

	}

	/**
	 * Defines the wp_insert_post() compatible arguments for importing the given ConvertKit
	 * Broadcast to a new WordPress Post.
	 *
	 * @since   2.2.9
	 *
	 * @param   array    $broadcast          Broadcast.
	 * @param   int      $author_id          WordPress User to assign as the author of the Post.
	 * @param   bool|int $category_id        Category ID.
	 * @return  array                           wp_insert_post() compatible arguments.
	 */
	private function build_post_args( $broadcast, $author_id, $category_id = false ) {

		// Define array for the wp_insert_post() compatible arguments.
		$post_args = array(
			'post_type'     => 'post',
			'post_title'    => $broadcast['title'],
			'post_excerpt'  => ( ! is_null( $broadcast['description'] ) ? $broadcast['description'] : '' ),
			'post_content'  => $this->parse_broadcast_content( $broadcast['content'] ),
			'post_date_gmt' => gmdate( 'Y-m-d H:i:s', strtotime( $broadcast['published_at'] ) ),
			'post_author'   => $author_id,
		);

		// If a Category was supplied, assign the Post to the given Category ID when created.
		$post_args['post_category'] = array( $category_id );

		/**
		 * Define the wp_insert_post() compatible arguments for importing a ConvertKit Broadcast
		 * to a new WordPress Post.
		 *
		 * @since   2.2.9
		 *
		 * @param   array   $post_args  Post arguments.
		 * @param   array   $broadcast  Broadcast.
		 */
		$post_args = apply_filters( 'convertkit_broadcasts_build_post_args', $post_args, $broadcast );

		// Deliberate: force the Post Status = draft. This will be changed to scheduled or publish after
		// any image is imported to the Post's Featured Image. So many plugins wrongly publish a Post
		// and then import the Featured Image, which breaks a lot of social media sharing Plugins.
		$post_args['post_status'] = 'draft';

		// Deliberate: ensure the Broadcast ID is always defined.
		if ( ! array_key_exists( 'meta_input', $post_args ) ) {
			$post_args['meta_input'] = array();
		}
		$post_args['meta_input']['_convertkit_broadcast_id'] = $broadcast['id'];

		return $post_args;

	}

	/**
	 * Parses the given Broadcast's content, removing unnecessary HTML tags and styles.
	 *
	 * @since   2.2.9
	 *
	 * @param   string $broadcast_content  Broadcast Content.
	 * @return  string                      Parsed Content.
	 */
	private function parse_broadcast_content( $broadcast_content ) {

		$content = $broadcast_content;

		// Remove open tracking.
		$content = str_replace( '<img src="https://preview.convertkit-mail2.com/open" alt="">', '', $content );

		// Remove <style> elements.
		$content = preg_replace( '/(<style *?>.*?<\/style>)/is', '', $content );
		$content = preg_replace( '/(<style>.*?<\/style>)/is', '', $content );

		// Remove blank contenteditable table cells.
		$content = str_replace( '<td contenteditable="false"></td>', '', $content );

		// Extract just the permitted HTML.
		$content = $this->get_permitted_html( $content, $this->broadcasts_settings->no_styles() );

		// Remove unsubscribe section.
		$content = preg_replace( '/(<div class="ck-section ck-hide-in-public-posts".*?>.*?<\/tr><\/tbody><\/table>\\n<\/div>\\n)/is', '', $content );

		/**
		 * Parses the given Broadcast's content, removing unnecessary HTML tags and styles.
		 *
		 * @since   2.2.9
		 *
		 * @param   string  $content            Parsed Content.
		 * @param   string  $broadcast_content  Original Broadcast's Content.
		 */
		$content = apply_filters( 'convertkit_broadcasts_parse_broadcast_content', $content, $broadcast_content );

		return $content;

	}

	/**
	 * Returns the given content containing only the permitted HTML tags,
	 * and cleans up empty div elements and multiple newlines.
	 *
	 * @since   2.4.0
	 *
	 * @param   string $content         HTML Content.
	 * @param   bool   $disable_styles  Disable styles.
	 * @return  string                  HTML Content
	 */
	public function get_permitted_html( $content, $disable_styles = false ) {

		// For PHP 7.4 and lower compatibility, convert permitted HTML tags array to a string
		// for use in strip_tags().
		$permitted_html_tags_string = '<' . implode( '><', $this->permitted_html_tags( $disable_styles ) ) . '>';

		// Remove other tags, retaining inner contents.
		// For HTML broadcasts, this will remove e.g. <html>, <head> and <body> tags.
		$content = strip_tags( $content, $permitted_html_tags_string );

		// If Disable Styles is enabled, remove inline styles and class attributes from remaining HTML elements.
		if ( $disable_styles ) {
			$content = preg_replace( '/(<[^>]+) style=".*?"/i', '$1', $content );
			$content = preg_replace( '/(<[^>]+) class=".*?"/i', '$1', $content );
		}

		// Remove empty div elements, which may remain because they contained HTML comments only, which were removed above.
		$content = str_replace( '<div></div>', '', $content );

		// Remove leading and trailing newlines and spaces, so WordPress doesn't convert these to blank paragraph tags.
		$content = preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $content );
		$content = trim( $content );

		return $content;

	}

	/**
	 * Returns an array of permitted HTML tags to retain in the imported Broadcast.
	 *
	 * @since   2.2.9
	 *
	 * @param   bool $disable_styles  Disable styles.
	 * @return  array
	 */
	private function permitted_html_tags( $disable_styles = false ) {

		// Define HTML tags to retain in the content.
		$permitted_html_tags = array(
			'h1',
			'h2',
			'h3',
			'p',
			'ul',
			'ol',
			'li',
			'blockquote',
			'strong',
			'em',
			'u',
			's',
			'a',
			'img',
			'br',
		);

		// If Disable Styles is false, include layout tags.
		if ( ! $disable_styles ) {
			$permitted_html_tags = array_merge(
				$permitted_html_tags,
				array(
					'span',
					'div',
					'table',
					'tbody',
					'tr',
					'td',
				)
			);
		}

		/**
		 * Define the HTML tags to retain in the Broadcast Content.
		 *
		 * @since   2.2.9
		 *
		 * @param   array  $permitted_html_tags    Permitted HTML Tags.
		 */
		$permitted_html_tags = apply_filters( 'convertkit_broadcasts_parse_broadcast_content_permitted_html_tags', $permitted_html_tags );

		// Return.
		return $permitted_html_tags;

	}

	/**
	 * Imports the broadcast's thumbnail_url image to the WordPress Media Library,
	 * assigning it as the WordPress Post's featured image.
	 *
	 * @since   2.2.9
	 *
	 * @param   array $broadcast  ConvertKit Broadcast.
	 * @param   int   $post_id    Post ID.
	 * @return  WP_Error|bool|int
	 */
	private function add_broadcast_image_to_post( $broadcast, $post_id ) {

		// Bail if no image specified.
		if ( empty( $broadcast['thumbnail_url'] ) ) {
			return false;
		}

		// Initialize class.
		$media_library = new ConvertKit_Media_Library();

		// Import Image into the Media Library.
		$image_id = $media_library->import_remote_image(
			$broadcast['thumbnail_url'],
			$post_id,
			$broadcast['thumbnail_alt']
		);

		// Destroy class.
		unset( $media_library );

		// Bail if an error occured.
		if ( is_wp_error( $image_id ) ) {
			return $image_id;
		}

		// Assign the imported Media Library image as the Post's Featured Image.
		update_post_meta( $post_id, '_thumbnail_id', $image_id );

		return $image_id;

	}

}
