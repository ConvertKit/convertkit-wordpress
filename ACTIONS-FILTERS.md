<h1>Filters</h1><table>
				<thead>
					<tr>
						<th>File</th>
						<th>Filter Name</th>
						<th>Description</th>
					</tr>
				</thead>
				<tbody><tr>
						<td colspan="3">../admin/class-convertkit-admin-notices.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_admin_notices_output_  notice"><code>convertkit_admin_notices_output_  notice</code></a></td>
						<td>Define the text to output in an admin error notice.</td>
					</tr><tr>
						<td colspan="3">../admin/class-convertkit-admin-settings.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_plugin_screen_action_links"><code>convertkit_plugin_screen_action_links</code></a></td>
						<td>Define links to display below the Plugin Name on the WP_List_Table at Plugins > Installed Plugins.</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_admin_settings_register_sections"><code>convertkit_admin_settings_register_sections</code></a></td>
						<td>Registers settings sections at Settings > ConvertKit.</td>
					</tr><tr>
						<td colspan="3">../includes/functions.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_get_supported_post_types"><code>convertkit_get_supported_post_types</code></a></td>
						<td>Defines the Post Types that support ConvertKit Forms.</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_get_supported_restrict_content_post_types"><code>convertkit_get_supported_restrict_content_post_types</code></a></td>
						<td>Defines the Post Types that support Restricted Content / Members Content functionality.</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_shortcodes"><code>convertkit_shortcodes</code></a></td>
						<td>Registers shortcodes for the ConvertKit Plugin.</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_blocks"><code>convertkit_blocks</code></a></td>
						<td>Registers blocks for the ConvertKit Plugin.</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_get_block_formatters"><code>convertkit_get_block_formatters</code></a></td>
						<td>Registers block formatters in Gutenberg for the ConvertKit Plugin.</td>
					</tr><tr>
						<td colspan="3">../includes/blocks/class-convertkit-block-content.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_block_content_render"><code>convertkit_block_content_render</code></a></td>
						<td>Filters the content in the ConvertKit Custom Content block/shortcode immediately before it is output.</td>
					</tr><tr>
						<td colspan="3">../includes/blocks/class-convertkit-block-product.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_block_product_render"><code>convertkit_block_product_render</code></a></td>
						<td>Filter the block's content immediately before it is output.</td>
					</tr><tr>
						<td colspan="3">../includes/blocks/class-convertkit-block-broadcasts.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_block_broadcasts_render"><code>convertkit_block_broadcasts_render</code></a></td>
						<td>Filter the block's content immediately before it is output.</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_block_broadcasts_render_ajax"><code>convertkit_block_broadcasts_render_ajax</code></a></td>
						<td>Filter the block's inner content immediately before it is output by AJAX, which occurs when pagination was clicked.</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_block_broadcasts_build_html_list_item"><code>convertkit_block_broadcasts_build_html_list_item</code></a></td>
						<td>Defines the HTML for an individual broadcast item in the Broadcasts block.</td>
					</tr><tr>
						<td colspan="3">../includes/blocks/class-convertkit-block-form.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_block_form_render"><code>convertkit_block_form_render</code></a></td>
						<td>Filter the block's content immediately before it is output.</td>
					</tr><tr>
						<td colspan="3">../includes/blocks/class-convertkit-block-form-trigger.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_block_form_trigger_render"><code>convertkit_block_form_trigger_render</code></a></td>
						<td>Filter the block's content immediately before it is output.</td>
					</tr><tr>
						<td colspan="3">../includes/class-convertkit-post.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_post_get_default_settings"><code>convertkit_post_get_default_settings</code></a></td>
						<td>The default settings, used to populate the Post's Settings when a Post has no Settings.</td>
					</tr><tr>
						<td colspan="3">../includes/class-convertkit-term.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_term_get_default_settings"><code>convertkit_term_get_default_settings</code></a></td>
						<td>The default settings, used to populate the Term's Settings when a Term has no Settings.</td>
					</tr><tr>
						<td colspan="3">../includes/class-convertkit-settings-broadcasts.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_settings_broadcasts_get_defaults"><code>convertkit_settings_broadcasts_get_defaults</code></a></td>
						<td>The default settings, used when the ConvertKit Broadcasts Settings haven't been saved e.g. on a new installation.</td>
					</tr><tr>
						<td colspan="3">../includes/class-convertkit-settings-restrict-content.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_settings_restrict_content_get_defaults"><code>convertkit_settings_restrict_content_get_defaults</code></a></td>
						<td>The default settings, used when the ConvertKit Restrict Content Settings haven't been saved e.g. on a new installation.</td>
					</tr><tr>
						<td colspan="3">../includes/class-convertkit-user.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_user_get_default_settings"><code>convertkit_user_get_default_settings</code></a></td>
						<td>The default settings, used to populate the User's Settings when a User has no Settings.</td>
					</tr><tr>
						<td colspan="3">../includes/class-convertkit-gutenberg.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_admin_gutenberg_add_block_categories"><code>convertkit_admin_gutenberg_add_block_categories</code></a></td>
						<td>Adds block categories to the default Gutenberg Block Categories</td>
					</tr><tr>
						<td colspan="3">../includes/integrations/contactform7/class-convertkit-contactform7-settings.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_contactform7_settings_get_defaults"><code>convertkit_contactform7_settings_get_defaults</code></a></td>
						<td>The default settings, used when Contact Form 7's Settings haven't been saved e.g. on a new installation or when the Contact Form 7 Plugin has just been activated for the first time.</td>
					</tr><tr>
						<td colspan="3">../includes/integrations/forminator/class-convertkit-forminator-settings.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_forminator_settings_get_defaults"><code>convertkit_forminator_settings_get_defaults</code></a></td>
						<td>The default settings, used when Forminator's Settings haven't been saved e.g. on a new installation or when the Forminator Plugin has just been activated for the first time.</td>
					</tr><tr>
						<td colspan="3">../includes/integrations/wishlist/class-convertkit-wishlist-settings.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_wishlist_settings_get_defaults"><code>convertkit_wishlist_settings_get_defaults</code></a></td>
						<td>The default settings, used when WishList's Settings haven't been saved e.g. on a new installation or when the WishList Plugin has just been activated for the first time.</td>
					</tr><tr>
						<td colspan="3">../includes/class-convertkit-output-restrict-content.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_output_restrict_content_get_resource_type"><code>convertkit_output_restrict_content_get_resource_type</code></a></td>
						<td>Define the ConvertKit Resource Type that the visitor must be subscribed against to access this content, overriding the Post setting. Return false or an empty string to not restrict content.</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_output_restrict_content_get_resource_id"><code>convertkit_output_restrict_content_get_resource_id</code></a></td>
						<td>Define the ConvertKit Resource ID that the visitor must be subscribed against to access this content, overriding the Post setting. Return 0 to not restrict content.</td>
					</tr><tr>
						<td colspan="3">../includes/class-convertkit-output.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_output_page_takeover_landing_page_id"><code>convertkit_output_page_takeover_landing_page_id</code></a></td>
						<td>Define the ConvertKit Landing Page ID to display for the given Post ID, overriding the Post settings. Return false to not display any ConvertKit Landing Page.</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_output_append_form_to_content_form_id"><code>convertkit_output_append_form_to_content_form_id</code></a></td>
						<td>Define the ConvertKit Form ID to display for the given Post ID, overriding the Post, Category or Plugin settings. Return false to not display any ConvertKit Form.</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_frontend_append_form"><code>convertkit_frontend_append_form</code></a></td>
						<td>Filter the Post's Content, which includes a ConvertKit Form, immediately before it is output.</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_output_scripts_footer"><code>convertkit_output_scripts_footer</code></a></td>
						<td>Define an array of scripts to output in the footer of the WordPress site.</td>
					</tr><tr>
						<td colspan="3">../includes/class-convertkit-settings.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_settings_get_defaults"><code>convertkit_settings_get_defaults</code></a></td>
						<td>The default settings, used when the ConvertKit Plugin Settings haven't been saved e.g. on a new installation.</td>
					</tr><tr>
						<td colspan="3">../includes/class-wp-convertkit.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_is_admin_or_frontend_editor"><code>convertkit_is_admin_or_frontend_editor</code></a></td>
						<td>Filters whether the current request is a WordPress Administration / Frontend Editor request or not. Page Builders can set this to true to allow ConvertKit to load its administration functionality.</td>
					</tr><tr>
						<td colspan="3">../includes/class-convertkit-broadcasts-importer.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_broadcasts_build_post_args"><code>convertkit_broadcasts_build_post_args</code></a></td>
						<td>Define the wp_insert_post() compatible arguments for importing a ConvertKit Broadcast to a new WordPress Post.</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_broadcasts_parse_broadcast_content"><code>convertkit_broadcasts_parse_broadcast_content</code></a></td>
						<td>Parses the given Broadcast's content, removing unnecessary HTML tags and styles.</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_broadcasts_parse_broadcast_content_permitted_html_tags"><code>convertkit_broadcasts_parse_broadcast_content_permitted_html_tags</code></a></td>
						<td>Define the HTML tags to retain in the Broadcast Content.</td>
					</tr>
					</tbody>
				</table><h3 id="convertkit_admin_notices_output_  notice">
						convertkit_admin_notices_output_  notice
						<code>admin/class-convertkit-admin-notices.php::87</code>
					</h3><h4>Overview</h4>
						<p>Define the text to output in an admin error notice.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$notice</td>
							<td>string</td>
							<td>Admin notice name.</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_admin_notices_output_  notice', function( $output ) {
	// ... your code here
	// Return value
	return $output;
}, 10, 1 );
</pre>
<h3 id="convertkit_plugin_screen_action_links">
						convertkit_plugin_screen_action_links
						<code>admin/class-convertkit-admin-settings.php::209</code>
					</h3><h4>Overview</h4>
						<p>Define links to display below the Plugin Name on the WP_List_Table at Plugins > Installed Plugins.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$links</td>
							<td>array</td>
							<td>HTML Links.</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_plugin_screen_action_links', function( $links ) {
	// ... your code here
	// Return value
	return $links;
}, 10, 1 );
</pre>
<h3 id="convertkit_admin_settings_register_sections">
						convertkit_admin_settings_register_sections
						<code>admin/class-convertkit-admin-settings.php::309</code>
					</h3><h4>Overview</h4>
						<p>Registers settings sections at Settings > ConvertKit.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$sections</td>
							<td>array</td>
							<td>Array of settings classes that handle individual tabs e.g. General, Tools etc.</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_admin_settings_register_sections', function( $sections ) {
	// ... your code here
	// Return value
	return $sections;
}, 10, 1 );
</pre>
<h3 id="convertkit_get_supported_post_types">
						convertkit_get_supported_post_types
						<code>includes/functions.php::121</code>
					</h3><h4>Overview</h4>
						<p>Defines the Post Types that support ConvertKit Forms.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$post_types</td>
							<td>array</td>
							<td>Post Types</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_get_supported_post_types', function( $post_types ) {
	// ... your code here
	// Return value
	return $post_types;
}, 10, 1 );
</pre>
<h3 id="convertkit_get_supported_restrict_content_post_types">
						convertkit_get_supported_restrict_content_post_types
						<code>includes/functions.php::147</code>
					</h3><h4>Overview</h4>
						<p>Defines the Post Types that support Restricted Content / Members Content functionality.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$post_types</td>
							<td>array</td>
							<td>Post Types</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_get_supported_restrict_content_post_types', function( $post_types ) {
	// ... your code here
	// Return value
	return $post_types;
}, 10, 1 );
</pre>
<h3 id="convertkit_shortcodes">
						convertkit_shortcodes
						<code>includes/functions.php::171</code>
					</h3><h4>Overview</h4>
						<p>Registers shortcodes for the ConvertKit Plugin.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$shortcodes</td>
							<td>array</td>
							<td>Shortcodes</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_shortcodes', function( $shortcodes ) {
	// ... your code here
	// Return value
	return $shortcodes;
}, 10, 1 );
</pre>
<h3 id="convertkit_blocks">
						convertkit_blocks
						<code>includes/functions.php::195</code>
					</h3><h4>Overview</h4>
						<p>Registers blocks for the ConvertKit Plugin.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$blocks</td>
							<td>array</td>
							<td>Blocks</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_blocks', function( $blocks ) {
	// ... your code here
	// Return value
	return $blocks;
}, 10, 1 );
</pre>
<h3 id="convertkit_get_block_formatters">
						convertkit_get_block_formatters
						<code>includes/functions.php::219</code>
					</h3><h4>Overview</h4>
						<p>Registers block formatters in Gutenberg for the ConvertKit Plugin.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$block_formatters</td>
							<td>array</td>
							<td>Block formatters.</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_get_block_formatters', function( $block_formatters ) {
	// ... your code here
	// Return value
	return $block_formatters;
}, 10, 1 );
</pre>
<h3 id="convertkit_block_content_render">
						convertkit_block_content_render
						<code>includes/blocks/class-convertkit-block-content.php::275</code>
					</h3><h4>Overview</h4>
						<p>Filters the content in the ConvertKit Custom Content block/shortcode immediately before it is output.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$content</td>
							<td>string</td>
							<td>Content</td>
						</tr><tr>
							<td>$atts</td>
							<td>array</td>
							<td>Block / Shortcode Attributes</td>
						</tr><tr>
							<td>$subscriber_id</td>
							<td>int</td>
							<td>ConvertKit Subscriber's ID</td>
						</tr><tr>
							<td>$tags</td>
							<td>array</td>
							<td>ConvertKit Subscriber's Tags</td>
						</tr><tr>
							<td>$tag</td>
							<td>array</td>
							<td>ConvertKit Subscriber's Tag that matches $atts['tag']</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_block_content_render', function( $content, $atts, $subscriber_id, $tags, $tag ) {
	// ... your code here
	// Return value
	return $content;
}, 10, 5 );
</pre>
<h3 id="convertkit_block_product_render">
						convertkit_block_product_render
						<code>includes/blocks/class-convertkit-block-product.php::409</code>
					</h3><h4>Overview</h4>
						<p>Filter the block's content immediately before it is output.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$html</td>
							<td>string</td>
							<td>ConvertKit Product button HTML.</td>
						</tr><tr>
							<td>$atts</td>
							<td>array</td>
							<td>Block Attributes.</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_block_product_render', function( $html, $atts ) {
	// ... your code here
	// Return value
	return $html;
}, 10, 2 );
</pre>
<h3 id="convertkit_block_broadcasts_render">
						convertkit_block_broadcasts_render
						<code>includes/blocks/class-convertkit-block-broadcasts.php::618</code>
					</h3><h4>Overview</h4>
						<p>Filter the block's content immediately before it is output.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$html</td>
							<td>string</td>
							<td>ConvertKit Broadcasts HTML.</td>
						</tr><tr>
							<td>$atts</td>
							<td>array</td>
							<td>Block Attributes.</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_block_broadcasts_render', function( $html, $atts ) {
	// ... your code here
	// Return value
	return $html;
}, 10, 2 );
</pre>
<h3 id="convertkit_block_broadcasts_render_ajax">
						convertkit_block_broadcasts_render_ajax
						<code>includes/blocks/class-convertkit-block-broadcasts.php::533</code>
					</h3><h4>Overview</h4>
						<p>Filter the block's inner content immediately before it is output by AJAX, which occurs when pagination was clicked.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$html</td>
							<td>string</td>
							<td>ConvertKit Broadcasts HTML.</td>
						</tr><tr>
							<td>$atts</td>
							<td>array</td>
							<td>Block Attributes.</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_block_broadcasts_render_ajax', function( $html, $atts ) {
	// ... your code here
	// Return value
	return $html;
}, 10, 2 );
</pre>
<h3 id="convertkit_block_broadcasts_build_html_list_item">
						convertkit_block_broadcasts_build_html_list_item
						<code>includes/blocks/class-convertkit-block-broadcasts.php::699</code>
					</h3><h4>Overview</h4>
						<p>Defines the HTML for an individual broadcast item in the Broadcasts block.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$html</td>
							<td>string</td>
							<td>HTML.</td>
						</tr><tr>
							<td>$broadcast</td>
							<td>array</td>
							<td>Broadcast.</td>
						</tr><tr>
							<td>$atts</td>
							<td>array</td>
							<td>Block attributes.</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_block_broadcasts_build_html_list_item', function( $html, $broadcast, $atts ) {
	// ... your code here
	// Return value
	return $html;
}, 10, 3 );
</pre>
<h3 id="convertkit_block_form_render">
						convertkit_block_form_render
						<code>includes/blocks/class-convertkit-block-form.php::344</code>
					</h3><h4>Overview</h4>
						<p>Filter the block's content immediately before it is output.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$form</td>
							<td>string</td>
							<td>ConvertKit Form HTML.</td>
						</tr><tr>
							<td>$atts</td>
							<td>array</td>
							<td>Block Attributes.</td>
						</tr><tr>
							<td>$form_id</td>
							<td>int</td>
							<td>Form ID.</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_block_form_render', function( $form, $atts, $form_id ) {
	// ... your code here
	// Return value
	return $form;
}, 10, 3 );
</pre>
<h3 id="convertkit_block_form_trigger_render">
						convertkit_block_form_trigger_render
						<code>includes/blocks/class-convertkit-block-form-trigger.php::383</code>
					</h3><h4>Overview</h4>
						<p>Filter the block's content immediately before it is output.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$html</td>
							<td>string</td>
							<td>ConvertKit Button HTML.</td>
						</tr><tr>
							<td>$atts</td>
							<td>array</td>
							<td>Block Attributes.</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_block_form_trigger_render', function( $html, $atts ) {
	// ... your code here
	// Return value
	return $html;
}, 10, 2 );
</pre>
<h3 id="convertkit_post_get_default_settings">
						convertkit_post_get_default_settings
						<code>includes/class-convertkit-post.php::316</code>
					</h3><h4>Overview</h4>
						<p>The default settings, used to populate the Post's Settings when a Post has no Settings.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$defaults</td>
							<td>array</td>
							<td>Default Settings.</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_post_get_default_settings', function( $defaults ) {
	// ... your code here
	// Return value
	return $defaults;
}, 10, 1 );
</pre>
<h3 id="convertkit_term_get_default_settings">
						convertkit_term_get_default_settings
						<code>includes/class-convertkit-term.php::148</code>
					</h3><h4>Overview</h4>
						<p>The default settings, used to populate the Term's Settings when a Term has no Settings.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$defaults</td>
							<td>string</td>
							<td>Default Form</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_term_get_default_settings', function( $defaults ) {
	// ... your code here
	// Return value
	return $defaults;
}, 10, 1 );
</pre>
<h3 id="convertkit_settings_broadcasts_get_defaults">
						convertkit_settings_broadcasts_get_defaults
						<code>includes/class-convertkit-settings-broadcasts.php::214</code>
					</h3><h4>Overview</h4>
						<p>The default settings, used when the ConvertKit Broadcasts Settings haven't been saved e.g. on a new installation.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$defaults</td>
							<td>array</td>
							<td></td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_settings_broadcasts_get_defaults', function( $defaults ) {
	// ... your code here
	// Return value
	return $defaults;
}, 10, 1 );
</pre>
<h3 id="convertkit_settings_restrict_content_get_defaults">
						convertkit_settings_restrict_content_get_defaults
						<code>includes/class-convertkit-settings-restrict-content.php::132</code>
					</h3><h4>Overview</h4>
						<p>The default settings, used when the ConvertKit Restrict Content Settings haven't been saved e.g. on a new installation.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$defaults</td>
							<td>array</td>
							<td></td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_settings_restrict_content_get_defaults', function( $defaults ) {
	// ... your code here
	// Return value
	return $defaults;
}, 10, 1 );
</pre>
<h3 id="convertkit_user_get_default_settings">
						convertkit_user_get_default_settings
						<code>includes/class-convertkit-user.php::107</code>
					</h3><h4>Overview</h4>
						<p>The default settings, used to populate the User's Settings when a User has no Settings.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$defaults</td>
							<td>array</td>
							<td>Default Settings.</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_user_get_default_settings', function( $defaults ) {
	// ... your code here
	// Return value
	return $defaults;
}, 10, 1 );
</pre>
<h3 id="convertkit_admin_gutenberg_add_block_categories">
						convertkit_admin_gutenberg_add_block_categories
						<code>includes/class-convertkit-gutenberg.php::66</code>
					</h3><h4>Overview</h4>
						<p>Adds block categories to the default Gutenberg Block Categories</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$categories</td>
							<td>array</td>
							<td>Block Categories</td>
						</tr><tr>
							<td>$post</td>
							<td>WP_Post</td>
							<td>WordPress Post</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_admin_gutenberg_add_block_categories', function( $categories, $post ) {
	// ... your code here
	// Return value
	return $categories;
}, 10, 2 );
</pre>
<h3 id="convertkit_contactform7_settings_get_defaults">
						convertkit_contactform7_settings_get_defaults
						<code>includes/integrations/contactform7/class-convertkit-contactform7-settings.php::149</code>
					</h3><h4>Overview</h4>
						<p>The default settings, used when Contact Form 7's Settings haven't been saved e.g. on a new installation or when the Contact Form 7 Plugin has just been activated for the first time.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$defaults</td>
							<td>array</td>
							<td>Default Settings.</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_contactform7_settings_get_defaults', function( $defaults ) {
	// ... your code here
	// Return value
	return $defaults;
}, 10, 1 );
</pre>
<h3 id="convertkit_forminator_settings_get_defaults">
						convertkit_forminator_settings_get_defaults
						<code>includes/integrations/forminator/class-convertkit-forminator-settings.php::153</code>
					</h3><h4>Overview</h4>
						<p>The default settings, used when Forminator's Settings haven't been saved e.g. on a new installation or when the Forminator Plugin has just been activated for the first time.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$defaults</td>
							<td>array</td>
							<td>Default Settings.</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_forminator_settings_get_defaults', function( $defaults ) {
	// ... your code here
	// Return value
	return $defaults;
}, 10, 1 );
</pre>
<h3 id="convertkit_wishlist_settings_get_defaults">
						convertkit_wishlist_settings_get_defaults
						<code>includes/integrations/wishlist/class-convertkit-wishlist-settings.php::149</code>
					</h3><h4>Overview</h4>
						<p>The default settings, used when WishList's Settings haven't been saved e.g. on a new installation or when the WishList Plugin has just been activated for the first time.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$defaults</td>
							<td>array</td>
							<td>Default Settings.</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_wishlist_settings_get_defaults', function( $defaults ) {
	// ... your code here
	// Return value
	return $defaults;
}, 10, 1 );
</pre>
<h3 id="convertkit_output_restrict_content_get_resource_type">
						convertkit_output_restrict_content_get_resource_type
						<code>includes/class-convertkit-output-restrict-content.php::531</code>
					</h3><h4>Overview</h4>
						<p>Define the ConvertKit Resource Type that the visitor must be subscribed against to access this content, overriding the Post setting. Return false or an empty string to not restrict content.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>Resource</td>
							<td>string $resource_type</td>
							<td>Type</td>
						</tr><tr>
							<td>$post_id</td>
							<td>int</td>
							<td>Post ID</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_output_restrict_content_get_resource_type', function( $resource_type, $post_id ) {
	// ... your code here
	// Return value
	return $resource_type;
}, 10, 2 );
</pre>
<h3 id="convertkit_output_restrict_content_get_resource_id">
						convertkit_output_restrict_content_get_resource_id
						<code>includes/class-convertkit-output-restrict-content.php::567</code>
					</h3><h4>Overview</h4>
						<p>Define the ConvertKit Resource ID that the visitor must be subscribed against to access this content, overriding the Post setting. Return 0 to not restrict content.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$resource_id</td>
							<td>int</td>
							<td>Resource ID</td>
						</tr><tr>
							<td>$post_id</td>
							<td>int</td>
							<td>Post ID</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_output_restrict_content_get_resource_id', function( $resource_id, $post_id ) {
	// ... your code here
	// Return value
	return $resource_id;
}, 10, 2 );
</pre>
<h3 id="convertkit_output_page_takeover_landing_page_id">
						convertkit_output_page_takeover_landing_page_id
						<code>includes/class-convertkit-output.php::139</code>
					</h3><h4>Overview</h4>
						<p>Define the ConvertKit Landing Page ID to display for the given Post ID, overriding the Post settings. Return false to not display any ConvertKit Landing Page.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$landing_page_id</td>
							<td>int</td>
							<td>Landing Page ID</td>
						</tr><tr>
							<td>$post_id</td>
							<td>int</td>
							<td>Post ID</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_output_page_takeover_landing_page_id', function( $landing_page_id, $post_id ) {
	// ... your code here
	// Return value
	return $landing_page_id;
}, 10, 2 );
</pre>
<h3 id="convertkit_output_append_form_to_content_form_id">
						convertkit_output_append_form_to_content_form_id
						<code>includes/class-convertkit-output.php::197</code>
					</h3><h4>Overview</h4>
						<p>Define the ConvertKit Form ID to display for the given Post ID, overriding the Post, Category or Plugin settings. Return false to not display any ConvertKit Form.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$form_id</td>
							<td>bool|int</td>
							<td>Form ID</td>
						</tr><tr>
							<td>$post_id</td>
							<td>int</td>
							<td>Post ID</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_output_append_form_to_content_form_id', function( $form_id, $post_id ) {
	// ... your code here
	// Return value
	return $form_id;
}, 10, 2 );
</pre>
<h3 id="convertkit_frontend_append_form">
						convertkit_frontend_append_form
						<code>includes/class-convertkit-output.php::261</code>
					</h3><h4>Overview</h4>
						<p>Filter the Post's Content, which includes a ConvertKit Form, immediately before it is output.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$content</td>
							<td>string</td>
							<td>Post Content</td>
						</tr><tr>
							<td>$form</td>
							<td>string</td>
							<td>ConvertKit Form HTML</td>
						</tr><tr>
							<td>$post_id</td>
							<td>int</td>
							<td>Post ID</td>
						</tr><tr>
							<td>$form_id</td>
							<td>int</td>
							<td>ConvertKit Form ID</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_frontend_append_form', function( $content, $form, $post_id, $form_id ) {
	// ... your code here
	// Return value
	return $content;
}, 10, 4 );
</pre>
<h3 id="convertkit_output_scripts_footer">
						convertkit_output_scripts_footer
						<code>includes/class-convertkit-output.php::430</code>
					</h3><h4>Overview</h4>
						<p>Define an array of scripts to output in the footer of the WordPress site.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$scripts</td>
							<td>array</td>
							<td>Scripts.</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_output_scripts_footer', function( $scripts ) {
	// ... your code here
	// Return value
	return $scripts;
}, 10, 1 );
</pre>
<h3 id="convertkit_settings_get_defaults">
						convertkit_settings_get_defaults
						<code>includes/class-convertkit-settings.php::274</code>
					</h3><h4>Overview</h4>
						<p>The default settings, used when the ConvertKit Plugin Settings haven't been saved e.g. on a new installation.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$defaults</td>
							<td>array</td>
							<td>Default Settings.</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_settings_get_defaults', function( $defaults ) {
	// ... your code here
	// Return value
	return $defaults;
}, 10, 1 );
</pre>
<h3 id="convertkit_is_admin_or_frontend_editor">
						convertkit_is_admin_or_frontend_editor
						<code>includes/class-wp-convertkit.php::311</code>
					</h3><h4>Overview</h4>
						<p>Filters whether the current request is a WordPress Administration / Frontend Editor request or not. Page Builders can set this to true to allow ConvertKit to load its administration functionality.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$is_admin_or_frontend_editor</td>
							<td>bool</td>
							<td>Is WordPress Administration / Frontend Editor request.</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_is_admin_or_frontend_editor', function( $is_admin_or_frontend_editor ) {
	// ... your code here
	// Return value
	return $is_admin_or_frontend_editor;
}, 10, 1 );
</pre>
<h3 id="convertkit_broadcasts_build_post_args">
						convertkit_broadcasts_build_post_args
						<code>includes/class-convertkit-broadcasts-importer.php::239</code>
					</h3><h4>Overview</h4>
						<p>Define the wp_insert_post() compatible arguments for importing a ConvertKit Broadcast to a new WordPress Post.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$post_args</td>
							<td>array</td>
							<td>Post arguments.</td>
						</tr><tr>
							<td>$broadcast</td>
							<td>array</td>
							<td>Broadcast.</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_broadcasts_build_post_args', function( $post_args, $broadcast ) {
	// ... your code here
	// Return value
	return $post_args;
}, 10, 2 );
</pre>
<h3 id="convertkit_broadcasts_parse_broadcast_content">
						convertkit_broadcasts_parse_broadcast_content
						<code>includes/class-convertkit-broadcasts-importer.php::310</code>
					</h3><h4>Overview</h4>
						<p>Parses the given Broadcast's content, removing unnecessary HTML tags and styles.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$content</td>
							<td>string</td>
							<td>Parsed Content.</td>
						</tr><tr>
							<td>$broadcast_content</td>
							<td>string</td>
							<td>Original Broadcast's Content.</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_broadcasts_parse_broadcast_content', function( $content, $broadcast_content ) {
	// ... your code here
	// Return value
	return $content;
}, 10, 2 );
</pre>
<h3 id="convertkit_broadcasts_parse_broadcast_content_permitted_html_tags">
						convertkit_broadcasts_parse_broadcast_content_permitted_html_tags
						<code>includes/class-convertkit-broadcasts-importer.php::366</code>
					</h3><h4>Overview</h4>
						<p>Define the HTML tags to retain in the Broadcast Content.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$permitted_html_tags</td>
							<td>array</td>
							<td>Permitted HTML Tags.</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_broadcasts_parse_broadcast_content_permitted_html_tags', function( $permitted_html_tags ) {
	// ... your code here
	// Return value
	return $permitted_html_tags;
}, 10, 1 );
</pre>
<h1>Actions</h1><table>
				<thead>
					<tr>
						<th>File</th>
						<th>Filter Name</th>
						<th>Description</th>
					</tr>
				</thead>
				<tbody><tr>
						<td colspan="3">../admin/section/class-convertkit-settings-base.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_settings_base_render_before"><code>convertkit_settings_base_render_before</code></a></td>
						<td></td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_settings_base_render_after"><code>convertkit_settings_base_render_after</code></a></td>
						<td></td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_settings_base_sanitize_settings"><code>convertkit_settings_base_sanitize_settings</code></a></td>
						<td></td>
					</tr><tr>
						<td colspan="3">../admin/section/class-convertkit-settings-tools.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_settings_base_render_before"><code>convertkit_settings_base_render_before</code></a></td>
						<td></td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_settings_base_render_after"><code>convertkit_settings_base_render_after</code></a></td>
						<td></td>
					</tr><tr>
						<td colspan="3">../admin/class-convertkit-admin-settings.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_admin_settings_enqueue_scripts"><code>convertkit_admin_settings_enqueue_scripts</code></a></td>
						<td>Enqueue JavaScript for the Settings Screen at Settings > ConvertKit</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_admin_settings_enqueue_styles"><code>convertkit_admin_settings_enqueue_styles</code></a></td>
						<td>Enqueue CSS for the Settings Screen at Settings > ConvertKit</td>
					</tr><tr>
						<td colspan="3">../admin/class-convertkit-admin-category.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_admin_category_enqueue_scripts"><code>convertkit_admin_category_enqueue_scripts</code></a></td>
						<td></td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_admin_category_enqueue_styles"><code>convertkit_admin_category_enqueue_styles</code></a></td>
						<td></td>
					</tr><tr>
						<td colspan="3">../admin/class-convertkit-admin-post.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_admin_post_enqueue_scripts"><code>convertkit_admin_post_enqueue_scripts</code></a></td>
						<td></td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_admin_post_enqueue_styles"><code>convertkit_admin_post_enqueue_styles</code></a></td>
						<td></td>
					</tr><tr>
						<td colspan="3">../admin/class-convertkit-admin-setup-wizard.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_admin_setup_wizard_process_form_  this-page_name"><code>convertkit_admin_setup_wizard_process_form_  this-page_name</code></a></td>
						<td>Process submitted form data for the given setup wizard name and current step.</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_admin_setup_wizard_load_screen_data_  this-page_name"><code>convertkit_admin_setup_wizard_load_screen_data_  this-page_name</code></a></td>
						<td>Load any data into class variables for the given setup wizard name and current step.</td>
					</tr><tr>
						<td colspan="3">../includes/class-convertkit-gutenberg.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_gutenberg_enqueue_scripts"><code>convertkit_gutenberg_enqueue_scripts</code></a></td>
						<td>Enqueue any additional scripts for Gutenberg blocks that have been registered.</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_gutenberg_enqueue_styles"><code>convertkit_gutenberg_enqueue_styles</code></a></td>
						<td></td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_gutenberg_enqueue_scripts_editor_and_frontend"><code>convertkit_gutenberg_enqueue_scripts_editor_and_frontend</code></a></td>
						<td></td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_gutenberg_enqueue_styles_editor_and_frontend"><code>convertkit_gutenberg_enqueue_styles_editor_and_frontend</code></a></td>
						<td></td>
					</tr><tr>
						<td colspan="3">../includes/class-convertkit-output.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_output_output_form"><code>convertkit_output_output_form</code></a></td>
						<td></td>
					</tr><tr>
						<td colspan="3">../includes/class-wp-convertkit.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_initialize_admin"><code>convertkit_initialize_admin</code></a></td>
						<td></td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_initialize_admin_or_frontend_editor"><code>convertkit_initialize_admin_or_frontend_editor</code></a></td>
						<td></td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_initialize_cli_cron"><code>convertkit_initialize_cli_cron</code></a></td>
						<td></td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_initialize_frontend"><code>convertkit_initialize_frontend</code></a></td>
						<td></td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_initialize_global"><code>convertkit_initialize_global</code></a></td>
						<td></td>
					</tr>
					</tbody>
				</table><h3 id="convertkit_settings_base_render_before">
						convertkit_settings_base_render_before
						<code>admin/section/class-convertkit-settings-base.php::124</code>
					</h3><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_settings_base_render_before', function(  ) {
	// ... your code here
}, 10, 0 );
</pre>
<h3 id="convertkit_settings_base_render_after">
						convertkit_settings_base_render_after
						<code>admin/section/class-convertkit-settings-base.php::141</code>
					</h3><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_settings_base_render_after', function(  ) {
	// ... your code here
}, 10, 0 );
</pre>
<h3 id="convertkit_settings_base_sanitize_settings">
						convertkit_settings_base_sanitize_settings
						<code>admin/section/class-convertkit-settings-base.php::487</code>
					</h3><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$name</td>
							<td>Unknown</td>
							<td>N/A</td>
						</tr><tr>
							<td>$settings</td>
							<td>Unknown</td>
							<td>N/A</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_settings_base_sanitize_settings', function( $name, $settings ) {
	// ... your code here
}, 10, 2 );
</pre>
<h3 id="convertkit_settings_base_render_before">
						convertkit_settings_base_render_before
						<code>admin/section/class-convertkit-settings-tools.php::311</code>
					</h3><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_settings_base_render_before', function(  ) {
	// ... your code here
}, 10, 0 );
</pre>
<h3 id="convertkit_settings_base_render_after">
						convertkit_settings_base_render_after
						<code>admin/section/class-convertkit-settings-tools.php::325</code>
					</h3><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_settings_base_render_after', function(  ) {
	// ... your code here
}, 10, 0 );
</pre>
<h3 id="convertkit_admin_settings_enqueue_scripts">
						convertkit_admin_settings_enqueue_scripts
						<code>admin/class-convertkit-admin-settings.php::68</code>
					</h3><h4>Overview</h4>
						<p>Enqueue JavaScript for the Settings Screen at Settings > ConvertKit</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$section</td>
							<td>string</td>
							<td>Settings section / tab (general|tools|restrict-content).</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_admin_settings_enqueue_scripts', function( $section ) {
	// ... your code here
}, 10, 1 );
</pre>
<h3 id="convertkit_admin_settings_enqueue_styles">
						convertkit_admin_settings_enqueue_styles
						<code>admin/class-convertkit-admin-settings.php::99</code>
					</h3><h4>Overview</h4>
						<p>Enqueue CSS for the Settings Screen at Settings > ConvertKit</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$section</td>
							<td>string</td>
							<td>Settings section / tab (general|tools|restrict-content).</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_admin_settings_enqueue_styles', function( $section ) {
	// ... your code here
}, 10, 1 );
</pre>
<h3 id="convertkit_admin_category_enqueue_scripts">
						convertkit_admin_category_enqueue_scripts
						<code>admin/class-convertkit-admin-category.php::62</code>
					</h3><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_admin_category_enqueue_scripts', function(  ) {
	// ... your code here
}, 10, 0 );
</pre>
<h3 id="convertkit_admin_category_enqueue_styles">
						convertkit_admin_category_enqueue_styles
						<code>admin/class-convertkit-admin-category.php::93</code>
					</h3><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_admin_category_enqueue_styles', function(  ) {
	// ... your code here
}, 10, 0 );
</pre>
<h3 id="convertkit_admin_post_enqueue_scripts">
						convertkit_admin_post_enqueue_scripts
						<code>admin/class-convertkit-admin-post.php::47</code>
					</h3><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_admin_post_enqueue_scripts', function(  ) {
	// ... your code here
}, 10, 0 );
</pre>
<h3 id="convertkit_admin_post_enqueue_styles">
						convertkit_admin_post_enqueue_styles
						<code>admin/class-convertkit-admin-post.php::70</code>
					</h3><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_admin_post_enqueue_styles', function(  ) {
	// ... your code here
}, 10, 0 );
</pre>
<h3 id="convertkit_admin_setup_wizard_process_form_  this-page_name">
						convertkit_admin_setup_wizard_process_form_  this-page_name
						<code>admin/class-convertkit-admin-setup-wizard.php::229</code>
					</h3><h4>Overview</h4>
						<p>Process submitted form data for the given setup wizard name and current step.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$step</td>
							<td>int</td>
							<td>Current step number.</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_admin_setup_wizard_process_form_  this-page_name', function( $step ) {
	// ... your code here
}, 10, 1 );
</pre>
<h3 id="convertkit_admin_setup_wizard_load_screen_data_  this-page_name">
						convertkit_admin_setup_wizard_load_screen_data_  this-page_name
						<code>admin/class-convertkit-admin-setup-wizard.php::292</code>
					</h3><h4>Overview</h4>
						<p>Load any data into class variables for the given setup wizard name and current step.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$step</td>
							<td>int</td>
							<td>Current step number.</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_admin_setup_wizard_load_screen_data_  this-page_name', function( $step ) {
	// ... your code here
}, 10, 1 );
</pre>
<h3 id="convertkit_gutenberg_enqueue_scripts">
						convertkit_gutenberg_enqueue_scripts
						<code>includes/class-convertkit-gutenberg.php::171</code>
					</h3><h4>Overview</h4>
						<p>Enqueue any additional scripts for Gutenberg blocks that have been registered.</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>$blocks</td>
							<td>array</td>
							<td>ConvertKit Blocks.</td>
						</tr><tr>
							<td>$block_formatters</td>
							<td>array</td>
							<td>ConvertKit Block Formatters.</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_gutenberg_enqueue_scripts', function( $blocks, $block_formatters ) {
	// ... your code here
}, 10, 2 );
</pre>
<h3 id="convertkit_gutenberg_enqueue_styles">
						convertkit_gutenberg_enqueue_styles
						<code>includes/class-convertkit-gutenberg.php::195</code>
					</h3><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_gutenberg_enqueue_styles', function(  ) {
	// ... your code here
}, 10, 0 );
</pre>
<h3 id="convertkit_gutenberg_enqueue_scripts_editor_and_frontend">
						convertkit_gutenberg_enqueue_scripts_editor_and_frontend
						<code>includes/class-convertkit-gutenberg.php::219</code>
					</h3><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_gutenberg_enqueue_scripts_editor_and_frontend', function(  ) {
	// ... your code here
}, 10, 0 );
</pre>
<h3 id="convertkit_gutenberg_enqueue_styles_editor_and_frontend">
						convertkit_gutenberg_enqueue_styles_editor_and_frontend
						<code>includes/class-convertkit-gutenberg.php::243</code>
					</h3><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_gutenberg_enqueue_styles_editor_and_frontend', function(  ) {
	// ... your code here
}, 10, 0 );
</pre>
<h3 id="convertkit_output_output_form">
						convertkit_output_output_form
						<code>includes/class-convertkit-output.php::94</code>
					</h3><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_output_output_form', function(  ) {
	// ... your code here
}, 10, 0 );
</pre>
<h3 id="convertkit_initialize_admin">
						convertkit_initialize_admin
						<code>includes/class-wp-convertkit.php::85</code>
					</h3><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_initialize_admin', function(  ) {
	// ... your code here
}, 10, 0 );
</pre>
<h3 id="convertkit_initialize_admin_or_frontend_editor">
						convertkit_initialize_admin_or_frontend_editor
						<code>includes/class-wp-convertkit.php::106</code>
					</h3><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_initialize_admin_or_frontend_editor', function(  ) {
	// ... your code here
}, 10, 0 );
</pre>
<h3 id="convertkit_initialize_cli_cron">
						convertkit_initialize_cli_cron
						<code>includes/class-wp-convertkit.php::127</code>
					</h3><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_initialize_cli_cron', function(  ) {
	// ... your code here
}, 10, 0 );
</pre>
<h3 id="convertkit_initialize_frontend">
						convertkit_initialize_frontend
						<code>includes/class-wp-convertkit.php::151</code>
					</h3><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_initialize_frontend', function(  ) {
	// ... your code here
}, 10, 0 );
</pre>
<h3 id="convertkit_initialize_global">
						convertkit_initialize_global
						<code>includes/class-wp-convertkit.php::191</code>
					</h3><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_initialize_global', function(  ) {
	// ... your code here
}, 10, 0 );
</pre>
