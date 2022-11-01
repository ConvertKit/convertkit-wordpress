<h1>Filters</h1><table>
				<thead>
					<tr>
						<th>File</th>
						<th>Filter Name</th>
						<th>Description</th>
					</tr>
				</thead>
				<tbody><tr>
						<td colspan="3">../admin/class-convertkit-admin-settings.php</td>
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
						<td><a href="#convertkit_shortcodes"><code>convertkit_shortcodes</code></a></td>
						<td>Registers shortcodes for the ConvertKit Plugin.</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_blocks"><code>convertkit_blocks</code></a></td>
						<td>Registers blocks for the ConvertKit Plugin.</td>
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
						<td colspan="3">../includes/blocks/class-convertkit-block-form.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_block_form_render"><code>convertkit_block_form_render</code></a></td>
						<td>Filter the block's content immediately before it is output.</td>
					</tr><tr>
						<td colspan="3">../includes/class-convertkit-post.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_post_get_default_settings"><code>convertkit_post_get_default_settings</code></a></td>
						<td>The default settings, used to populate the Post's Settings when a Post has no Settings.</td>
					</tr><tr>
						<td colspan="3">../includes/class-convertkit-post-type-product.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_post_type_product_register"><code>convertkit_post_type_product_register</code></a></td>
						<td>Filter the arguments for registering the Products Custom Post Type</td>
					</tr><tr>
						<td colspan="3">../includes/class-convertkit-term.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_term_get_default_settings"><code>convertkit_term_get_default_settings</code></a></td>
						<td>The default settings, used to populate the Term's Settings when a Term has no Settings.</td>
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
						<td colspan="3">../includes/integrations/wishlist/class-convertkit-wishlist-settings.php</td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_wishlist_settings_get_defaults"><code>convertkit_wishlist_settings_get_defaults</code></a></td>
						<td>The default settings, used when WishList's Settings haven't been saved e.g. on a new installation or when the WishList Plugin has just been activated for the first time.</td>
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
					</tr>
					</tbody>
				</table><h3 id="convertkit_admin_settings_register_sections">
						convertkit_admin_settings_register_sections
						<code>admin/class-convertkit-admin-settings.php::302</code>
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
<h3 id="convertkit_shortcodes">
						convertkit_shortcodes
						<code>includes/functions.php::145</code>
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
						<code>includes/functions.php::169</code>
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
						<code>includes/blocks/class-convertkit-block-product.php::396</code>
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
						<code>includes/blocks/class-convertkit-block-broadcasts.php::408</code>
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
						<code>includes/blocks/class-convertkit-block-broadcasts.php::455</code>
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
<h3 id="convertkit_block_form_render">
						convertkit_block_form_render
						<code>includes/blocks/class-convertkit-block-form.php::324</code>
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
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_block_form_render', function( $form, $atts ) {
	// ... your code here
	// Return value
	return $form;
}, 10, 2 );
</pre>
<h3 id="convertkit_post_get_default_settings">
						convertkit_post_get_default_settings
						<code>includes/class-convertkit-post.php::232</code>
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
<h3 id="convertkit_post_type_product_register">
						convertkit_post_type_product_register
						<code>includes/class-convertkit-post-type-product.php::98</code>
					</h3><h4>Overview</h4>
						<p>Filter the arguments for registering the Products Custom Post Type</p><h4>Parameters</h4>
					<table>
						<thead>
							<tr>
								<th>Parameter</th>
								<th>Type</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody><tr>
							<td>register_post_type()</td>
							<td>array $args</td>
							<td>compatible</td>
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
add_filter( 'convertkit_post_type_product_register', function( $args ) {
	// ... your code here
	// Return value
	return $args;
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
						<code>includes/integrations/contactform7/class-convertkit-contactform7-settings.php::128</code>
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
<h3 id="convertkit_wishlist_settings_get_defaults">
						convertkit_wishlist_settings_get_defaults
						<code>includes/integrations/wishlist/class-convertkit-wishlist-settings.php::152</code>
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
<h3 id="convertkit_output_page_takeover_landing_page_id">
						convertkit_output_page_takeover_landing_page_id
						<code>includes/class-convertkit-output.php::128</code>
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
						<code>includes/class-convertkit-output.php::183</code>
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
						<code>includes/class-convertkit-output.php::247</code>
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
							<td>N/A</td>
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
						<code>includes/class-wp-convertkit.php::302</code>
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
						<td></td>
					</tr><tr>
						<td>&nbsp;</td>
						<td><a href="#convertkit_admin_settings_enqueue_styles"><code>convertkit_admin_settings_enqueue_styles</code></a></td>
						<td></td>
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
						<code>admin/section/class-convertkit-settings-base.php::110</code>
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
						<code>admin/section/class-convertkit-settings-base.php::127</code>
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
<h3 id="convertkit_settings_base_render_before">
						convertkit_settings_base_render_before
						<code>admin/section/class-convertkit-settings-tools.php::327</code>
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
						<code>admin/section/class-convertkit-settings-tools.php::341</code>
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
						<code>admin/class-convertkit-admin-settings.php::69</code>
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
do_action( 'convertkit_admin_settings_enqueue_scripts', function(  ) {
	// ... your code here
}, 10, 0 );
</pre>
<h3 id="convertkit_admin_settings_enqueue_styles">
						convertkit_admin_settings_enqueue_styles
						<code>admin/class-convertkit-admin-settings.php::98</code>
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
do_action( 'convertkit_admin_settings_enqueue_styles', function(  ) {
	// ... your code here
}, 10, 0 );
</pre>
<h3 id="convertkit_admin_category_enqueue_scripts">
						convertkit_admin_category_enqueue_scripts
						<code>admin/class-convertkit-admin-category.php::71</code>
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
						<code>admin/class-convertkit-admin-category.php::92</code>
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
						<code>admin/class-convertkit-admin-post.php::67</code>
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
						<code>admin/class-convertkit-admin-setup-wizard.php::213</code>
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
						<code>admin/class-convertkit-admin-setup-wizard.php::273</code>
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
						<code>includes/class-convertkit-gutenberg.php::157</code>
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
						</tr>
						</tbody>
					</table><h4>Usage</h4>
<pre>
do_action( 'convertkit_gutenberg_enqueue_scripts', function( $blocks ) {
	// ... your code here
}, 10, 1 );
</pre>
<h3 id="convertkit_gutenberg_enqueue_styles">
						convertkit_gutenberg_enqueue_styles
						<code>includes/class-convertkit-gutenberg.php::181</code>
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
						<code>includes/class-convertkit-gutenberg.php::205</code>
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
						<code>includes/class-convertkit-gutenberg.php::229</code>
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
						<code>includes/class-convertkit-output.php::83</code>
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
						<code>includes/class-wp-convertkit.php::81</code>
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
						<code>includes/class-wp-convertkit.php::102</code>
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
						<code>includes/class-wp-convertkit.php::123</code>
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
						<code>includes/class-wp-convertkit.php::146</code>
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
						<code>includes/class-wp-convertkit.php::182</code>
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
