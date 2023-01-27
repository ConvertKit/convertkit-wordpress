<?php
namespace Helper\Acceptance;

/**
 * Helper methods and actions related to the ConvertKit Plugin,
 * which are then available using $I->{yourFunctionName}.
 *
 * @since   1.9.6
 */
class Plugin extends \Codeception\Module
{
	/**
	 * Helper method to activate the ConvertKit Plugin, checking
	 * it activated and no errors were output.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I     AcceptanceTester.
	 */
	public function activateConvertKitPlugin($I)
	{
		$I->activateThirdPartyPlugin($I, 'convertkit');
	}

	/**
	 * Helper method to deactivate the ConvertKit Plugin, checking
	 * it activated and no errors were output.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I     AcceptanceTester.
	 */
	public function deactivateConvertKitPlugin($I)
	{
		$I->deactivateThirdPartyPlugin($I, 'convertkit');
	}

	/**
	 * Helper method to programmatically setup the Plugin's API Key and Secret,
	 * enabling debug logging.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I              AcceptanceTester.
	 * @param   bool|string      $apiKey         API Key (if specified, used instead of CONVERTKIT_API_KEY).
	 * @param   bool|string      $apiSecret      API Secret (if specified, used instead of CONVERTKIT_API_SECRET).
	 * @param   bool|string      $pageFormID     Default Form ID for Pages (if specified, used instead of CONVERTKIT_API_FORM_ID).
	 * @param   bool|string      $postFormID     Default Form ID for Posts (if specified, used instead of CONVERTKIT_API_FORM_ID).
	 * @param   bool|string      $productFormID  Default Form ID for Products (if specified, used instead of CONVERTKIT_API_FORM_ID).
	 */
	public function setupConvertKitPlugin($I, $apiKey = false, $apiSecret = false, $pageFormID = false, $postFormID = false, $productFormID = false)
	{
		// Define the API Key and Secret, with Debug Log enabled.
		$I->haveOptionInDatabase(
			'_wp_convertkit_settings',
			[
				'api_key'      => ( $apiKey !== false ? $apiKey : $_ENV['CONVERTKIT_API_KEY'] ),
				'api_secret'   => ( $apiSecret !== false ? $apiSecret : $_ENV['CONVERTKIT_API_SECRET'] ),
				'debug'        => 'on',
				'no_scripts'   => '',
				'no_css'       => '',
				'post_form'    => ( $postFormID !== false ? $postFormID : $_ENV['CONVERTKIT_API_FORM_ID'] ),
				'page_form'    => ( $pageFormID !== false ? $pageFormID : $_ENV['CONVERTKIT_API_FORM_ID'] ),
				'product_form' => ( $productFormID !== false ? $productFormID : $_ENV['CONVERTKIT_API_FORM_ID'] ),
			]
		);
	}

	/**
	 * Helper method to define cached Resources (Forms, Landing Pages, Posts, Products and Tags),
	 * directly into the database, instead of querying the API for them via the Resource classes.
	 *
	 * This can safely be done for Acceptance tests, as WPUnit tests ensure that
	 * caching Resources from calls made to the API work and store data in the expected
	 * structure.
	 *
	 * Defining cached Resources here reduces the number of API calls made for each test,
	 * reducing the likelihood of hitting a rate limit due to running tests in parallel.
	 * 
	 * Resources are deliberately not in order, to emulate how the data might not always
	 * be in alphabetical / published order from the API. 
	 *
	 * @since   2.0.7
	 *
	 * @param   AcceptanceTester $I              AcceptanceTester.
	 */
	public function setupConvertKitPluginResources($I)
	{
		// Define Forms as if the Forms resource class populated them from the API.
		$I->haveOptionInDatabase(
			'convertkit_forms',
			[
				3003590 => [
					'id'         => 3003590,
					'name'       => 'Third Party Integrations Form',
					'created_at' => '2022-02-17T15:05:31.000Z',
					'type'       => 'embed',
					'format'     => 'inline',
					'embed_js'   => 'https://cheerful-architect-3237.ck.page/71cbcc4042/index.js',
					'embed_url'  => 'https://cheerful-architect-3237.ck.page/71cbcc4042',
					'archived'   => false,
					'uid'        => '71cbcc4042',
				],
				2780977 => [
					'id'         => 2780977,
					'name'       => 'Modal Form',
					'created_at' => '2021-11-17T04:22:06.000Z',
					'type'       => 'embed',
					'format'     => 'modal',
					'embed_js'   => 'https://cheerful-architect-3237.ck.page/397e876257/index.js',
					'embed_url'  => 'https://cheerful-architect-3237.ck.page/397e876257',
					'archived'   => false,
					'uid'        => '397e876257',
				],
				2780979 => [
					'id'         => 2780979,
					'name'       => 'Slide In Form',
					'created_at' => '2021-11-17T04:22:24.000Z',
					'type'       => 'embed',
					'format'     => 'slide in',
					'embed_js'   => 'https://cheerful-architect-3237.ck.page/e0d65bed9d/index.js',
					'embed_url'  => 'https://cheerful-architect-3237.ck.page/e0d65bed9d',
					'archived'   => false,
					'uid'        => 'e0d65bed9d',
				],
				2765139 => [
					'id'         => 2765139,
					'name'       => 'Page Form',
					'created_at' => '2021-11-11T15:30:40.000Z',
					'type'       => 'embed',
					'format'     => 'inline',
					'embed_js'   => 'https://cheerful-architect-3237.ck.page/85629c512d/index.js',
					'embed_url'  => 'https://cheerful-architect-3237.ck.page/85629c512d',
					'archived'   => false,
					'uid'        => '85629c512d',
				],
				470099  => [
					'id'                  => 470099,
					'name'                => 'Legacy Form',
					'created_at'          => null,
					'type'                => 'embed',
					'url'                 => 'https://app.convertkit.com/landing_pages/470099',
					'embed_js'            => 'https://api.convertkit.com/api/v3/forms/470099.js?api_key=' . $_ENV['CONVERTKIT_API_KEY'],
					'embed_url'           => 'https://api.convertkit.com/api/v3/forms/470099.html?api_key=' . $_ENV['CONVERTKIT_API_KEY'],
					'title'               => 'Join the newsletter',
					'description'         => '<p>Subscribe to get our latest content by email.</p>',
					'sign_up_button_text' => 'Subscribe',
					'success_message'     => 'Success! Now check your email to confirm your subscription.',
					'archived'            => false,
				],
				2780980 => [
					'id'         => 2780980,
					'name'       => 'Sticky Bar Form',
					'created_at' => '2021-11-17T04:22:42.000Z',
					'type'       => 'embed',
					'format'     => 'sticky bar',
					'embed_js'   => 'https://cheerful-architect-3237.ck.page/9f5c601482/index.js',
					'embed_url'  => 'https://cheerful-architect-3237.ck.page/9f5c601482',
					'archived'   => false,
					'uid'        => '9f5c601482',
				],
			]
		);

		// Define Landing Pages.
		$I->haveOptionInDatabase(
			'convertkit_landing_pages',
			[
				2765196 => [
					'id'         => 2765196,
					'name'       => 'Landing Page',
					'created_at' => '2021-11-11T15:45:33.000Z',
					'type'       => 'hosted',
					'format'     => null,
					'embed_js'   => 'https://cheerful-architect-3237.ck.page/99f1db6843/index.js',
					'embed_url'  => 'https://cheerful-architect-3237.ck.page/99f1db6843',
					'archived'   => false,
					'uid'        => '99f1db6843',
				],
				2849151 => [
					'id'         => 2849151,
					'name'       => 'Character Encoding',
					'created_at' => '2021-12-16T14:55:58.000Z',
					'type'       => 'hosted',
					'format'     => null,
					'embed_js'   => 'https://cheerful-architect-3237.ck.page/cc5eb21744/index.js',
					'embed_url'  => 'https://cheerful-architect-3237.ck.page/cc5eb21744',
					'archived'   => false,
					'uid'        => 'cc5eb21744',
				],
				470103  => [
					'id'                  => 470103,
					'name'                => 'Legacy Landing Page',
					'created_at'          => null,
					'type'                => 'hosted',
					'url'                 => 'https://app.convertkit.com/landing_pages/470103',
					'embed_js'            => 'https://api.convertkit.com/api/v3/forms/470103.js?api_key=' . $_ENV['CONVERTKIT_API_KEY'],
					'embed_url'           => 'https://api.convertkit.com/api/v3/forms/470103.html?api_key=' . $_ENV['CONVERTKIT_API_KEY'],
					'title'               => '',
					'description'         => '',
					'sign_up_button_text' => 'Register',
					'success_message'     => null,
					'archived'            => false,
				],
			]
		);

		// Define Posts.
		$I->haveOptionInDatabase(
			'convertkit_posts',
			[
				489467 => [
					'id'           => 489467,
					'title'        => 'Broadcast 1',
					'url'          => 'https://cheerful-architect-3237.ck.page/posts/broadcast-1',
					'published_at' => '2022-04-08T00:00:00.000Z',
					'is_paid'      => false,
				],
				224758 => [
					'id'           => 224758,
					'title'        => 'Test Subject',
					'url'          => 'https://cheerful-architect-3237.ck.page/posts/test-subject',
					'published_at' => '2022-01-24T00:00:00.000Z',
					'is_paid'      => null,
				],
				489480 => [
					'id'           => 489480,
					'title'        => 'Broadcast 2',
					'url'          => 'https://cheerful-architect-3237.ck.page/posts/broadcast-2',
					'published_at' => '2022-04-08T00:00:00.000Z',
					'is_paid'      => null,
				],
				572575 => [
					'id'           => 572575,
					'title'        => 'Paid Subscriber Broadcast',
					'url'          => 'https://cheerful-architect-3237.ck.page/posts/paid-subscriber-broadcast',
					'published_at' => '2022-05-03T14:51:50.000Z',
					'is_paid'      => true,
				],
			]
		);

		// Define Products.
		$I->haveOptionInDatabase(
			'convertkit_products',
			[
				36377 => [
					'id'        => 36377,
					'name'      => 'Newsletter Subscription',
					'url'       => 'https://cheerful-architect-3237.ck.page/products/newsletter-subscription',
					'published' => true,
				],
			]
		);

		// Define Tags.
		$I->haveOptionInDatabase(
			'convertkit_tags',
			[
				2744672 => [
					'id'         => 2744672,
					'name'       => 'wordpress',
					'created_at' => '2021-11-11T19:30:06.000Z',
				],
				2907192 => [
					'id'         => 2907192,
					'name'       => 'gravityforms-tag-1',
					'created_at' => '2022-02-02T14:06:32.000Z',
				],
				2907193 => [
					'id'         => 2907193,
					'name'       => 'gravityforms-tag-2',
					'created_at' => '2022-02-02T14:06:38.000Z',
				],
			]
		);

		// Define last queried to now for all resources, so they're not automatically immediately refreshed by the Plugin's logic.
		$I->haveOptionInDatabase( 'convertkit_forms_last_queried', strtotime( 'now' ) );
		$I->haveOptionInDatabase( 'convertkit_landing_pages_last_queried', strtotime( 'now' ) );
		$I->haveOptionInDatabase( 'convertkit_posts_last_queried', strtotime( 'now' ) );
		$I->haveOptionInDatabase( 'convertkit_products_last_queried', strtotime( 'now' ) );
		$I->haveOptionInDatabase( 'convertkit_tags_last_queried', strtotime( 'now' ) );
	}

	/**
	 * Helper method to define cached Resources (Forms, Landing Pages, Posts, Products and Tags),
	 * directly into the database, instead of querying the API for them via the Resource classes
	 * as if the ConvertKit account is new and has no resources defined in ConvertKit.
	 *
	 * @since   2.0.7
	 *
	 * @param   AcceptanceTester $I              AcceptanceTester.
	 */
	public function setupConvertKitPluginResourcesNoData($I)
	{
		// Define Forms as if the Forms resource class populated them from the API.
		$I->haveOptionInDatabase(
			'convertkit_forms',
			[]
		);

		// Define Landing Pages.
		$I->haveOptionInDatabase(
			'convertkit_landing_pages',
			[]
		);

		// Define Posts.
		$I->haveOptionInDatabase(
			'convertkit_posts',
			[]
		);

		// Define Products.
		$I->haveOptionInDatabase(
			'convertkit_products',
			[]
		);

		// Define Tags.
		$I->haveOptionInDatabase(
			'convertkit_tags',
			[]
		);

		// Define last queried to now for all resources, so they're not automatically immediately refreshed by the Plugin's logic.
		$I->haveOptionInDatabase( 'convertkit_forms_last_queried', strtotime( 'now' ) );
		$I->haveOptionInDatabase( 'convertkit_landing_pages_last_queried', strtotime( 'now' ) );
		$I->haveOptionInDatabase( 'convertkit_posts_last_queried', strtotime( 'now' ) );
		$I->haveOptionInDatabase( 'convertkit_products_last_queried', strtotime( 'now' ) );
		$I->haveOptionInDatabase( 'convertkit_tags_last_queried', strtotime( 'now' ) );
	}

	/**
	 * Helper method to reset the ConvertKit Plugin settings, as if it's a clean installation.
	 *
	 * @since   1.9.6.7
	 *
	 * @param   AcceptanceTester $I     AcceptanceTester.
	 */
	public function resetConvertKitPlugin($I)
	{
		// Plugin Settings.
		$I->dontHaveOptionInDatabase('_wp_convertkit_settings');
		$I->dontHaveOptionInDatabase('convertkit_version');

		// Resources.
		$I->dontHaveOptionInDatabase('convertkit_forms');
		$I->dontHaveOptionInDatabase('convertkit_forms_last_queried');
		$I->dontHaveOptionInDatabase('convertkit_landing_pages');
		$I->dontHaveOptionInDatabase('convertkit_landing_pages_last_queried');
		$I->dontHaveOptionInDatabase('convertkit_posts');
		$I->dontHaveOptionInDatabase('convertkit_posts_last_queried');
		$I->dontHaveOptionInDatabase('convertkit_products');
		$I->dontHaveOptionInDatabase('convertkit_products_last_queried');
		$I->dontHaveOptionInDatabase('convertkit_tags');
		$I->dontHaveOptionInDatabase('convertkit_tags_last_queried');

		// Review Request.
		$I->dontHaveOptionInDatabase('convertkit-review-request');
		$I->dontHaveOptionInDatabase('convertkit-review-dismissed');

		// Upgrades.
		$I->dontHaveOptionInDatabase('_wp_convertkit_upgrade_posts');
	}

	/**
	 * Helper method to load the Plugin's Settings > General screen.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I     AcceptanceTester.
	 */
	public function loadConvertKitSettingsGeneralScreen($I)
	{
		$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Helper method to load the Plugin's Settings > Tools screen.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I     AcceptanceTester.
	 */
	public function loadConvertKitSettingsToolsScreen($I)
	{
		$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings&tab=tools');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Helper method to clear the Plugin's debug log.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I     AcceptanceTester.
	 */
	public function clearDebugLog($I)
	{
		// Go to the Plugin's Tools Screen.
		$I->loadConvertKitSettingsToolsScreen($I);

		// Click the Clear log button.
		$I->click('Clear log');
	}

	/**
	 * Helper method to determine if the given entry exists in the Plugin Debug Log screen's textarea.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I         AcceptanceTester.
	 * @param   string           $entry     Log entry.
	 */
	public function seeInPluginDebugLog($I, $entry)
	{
		$I->loadConvertKitSettingsToolsScreen($I);
		$I->seeInSource($entry);
	}

	/**
	 * Helper method to determine if the given entry does not exist in the Plugin Debug Log screen's textarea.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I         AcceptanceTester.
	 * @param   string           $entry     Log entry.
	 */
	public function dontSeeInPluginDebugLog($I, $entry)
	{
		$I->loadConvertKitSettingsToolsScreen($I);
		$I->dontSeeInSource($entry);
	}

	/**
	 * Check that expected HTML exists in the DOM of the page we're viewing for
	 * a Broadcasts block or shortcode.
	 *
	 * @since   1.9.7.5
	 *
	 * @param   AcceptanceTester $I                      Tester.
	 * @param   bool|int         $numberOfPosts          Number of Broadcasts listed.
	 * @param   bool|string      $seePrevPaginationLabel Test if the "previous" pagination link is output and matches expected label.
	 * @param   bool|string      $seeNextPaginationLabel Test if the "next" pagination link is output and matches expected label.
	 */
	public function seeBroadcastsOutput($I, $numberOfPosts = false, $seePrevPaginationLabel = false, $seeNextPaginationLabel = false)
	{
		// Confirm that the block displays.
		$I->seeElementInDOM('div.convertkit-broadcasts');
		$I->seeElementInDOM('div.convertkit-broadcasts ul.convertkit-broadcasts-list');
		$I->seeElementInDOM('div.convertkit-broadcasts ul.convertkit-broadcasts-list li.convertkit-broadcast');
		$I->seeElementInDOM('div.convertkit-broadcasts ul.convertkit-broadcasts-list li.convertkit-broadcast a');

		// Confirm that UTM parameters exist on a broadcast link.
		$I->assertStringContainsString(
			'utm_source=wordpress&utm_content=convertkit',
			$I->grabAttributeFrom('div.convertkit-broadcasts ul.convertkit-broadcasts-list li.convertkit-broadcast a', 'href')
		);

		// Confirm that the number of expected broadcasts displays.
		if ($numberOfPosts !== false) {
			$I->seeNumberOfElements('li.convertkit-broadcast', $numberOfPosts);
		}

		// Confirm that previous pagination displays.
		if ($seePrevPaginationLabel !== false) {
			$I->seeElementInDOM('div.convertkit-broadcasts ul.convertkit-broadcasts-pagination li.convertkit-broadcasts-pagination-prev a');
			$I->seeInSource($seePrevPaginationLabel);
		}

		// Confirm that next pagination displays.
		if ($seeNextPaginationLabel !== false) {
			$I->seeElementInDOM('div.convertkit-broadcasts ul.convertkit-broadcasts-pagination li.convertkit-broadcasts-pagination-next a');
		}
	}

	/**
	 * Tests that the Broadcasts pagination works, and that the expected Broadcast
	 * is displayed after using previous and next links.
	 *
	 * @since   2.0.0
	 *
	 * @param   AcceptanceTester $I                      Tester.
	 * @param   string           $previousLabel          Previous / Newer Broadcasts Label.
	 * @param   string           $nextLabel              Next / Older Broadcasts Label.
	 */
	public function testBroadcastsPagination($I, $previousLabel, $nextLabel)
	{
		// Confirm that the block displays one broadcast with a pagination link to older broadcasts.
		$I->seeBroadcastsOutput($I, 1, false, $nextLabel);

		// Click the Older Posts link.
		$I->click('li.convertkit-broadcasts-pagination-next a');

		// Wait for the AJAX request to complete, by checking if the convertkit-broadcasts-loading class has been
		// removed from the block.
		$I->waitForBroadcastsToLoad($I);

		// Confirm that the block displays one broadcast with a pagination link to newer broadcasts.
		$I->seeBroadcastsOutput($I, 1, $previousLabel, false);

		// Fetch Broadcasts from the resource, to determine the name of the most recent two broadcasts.
		$broadcasts      = $I->grabOptionFromDatabase('convertkit_posts');
		$firstBroadcast  = current(array_slice($broadcasts, 0, 1));
		$secondBroadcast = current(array_slice($broadcasts, 1, 1));

		// Confirm that the expected Broadcast name is displayed and links to the expected URL, with UTM parameters.
		$I->seeInSource('<a href="' . $secondBroadcast['url'] . '?utm_source=wordpress&amp;utm_content=convertkit" target="_blank" rel="nofollow noopener"');
		$I->seeInSource($secondBroadcast['title']);

		// Click the Newer Posts link.
		$I->click('li.convertkit-broadcasts-pagination-prev a');

		// Wait for the AJAX request to complete, by checking if the convertkit-broadcasts-loading class has been
		// removed from the block.
		$I->waitForBroadcastsToLoad($I);

		// Confirm that the block displays one broadcast with a pagination link to older broadcasts.
		$I->seeBroadcastsOutput($I, 1, false, $nextLabel);

		// Confirm that the expected Broadcast name is displayed and links to the expected URL, with UTM parameters.
		$I->seeInSource('<a href="' . $firstBroadcast['url'] . '?utm_source=wordpress&amp;utm_content=convertkit" target="_blank" rel="nofollow noopener"');
		$I->seeInSource($firstBroadcast['title']);
	}

	/**
	 * Wait for the AJAX request to complete, by checking if the convertkit-broadcasts-loading class has been
	 * removed from the block.
	 *
	 * @since   1.9.7.6
	 *
	 * @param   AcceptanceTester $I                      Tester.
	 */
	public function waitForBroadcastsToLoad($I)
	{
		$I->waitForElementChange(
			'div.convertkit-broadcasts',
			function(\Facebook\WebDriver\WebDriverElement $el) {
				return ( strpos($el->getAttribute('class'), 'convertkit-broadcasts-loading') === false ? true : false );
			},
			5
		);
	}

	/**
	 * Check that expected HTML exists in the DOM of the page we're viewing
	 * when a ConvertKit Product link was inserted into a paragraph or button,
	 * and that the button loads the expected ConvertKit Product modal.
	 *
	 * @since   2.0.0
	 *
	 * @param   AcceptanceTester $I              Tester.
	 * @param   string           $productURL     Product URL.
	 * @param   bool|string      $text           Test if the link text matches the given value.
	 */
	public function seeProductLink($I, $productURL, $text = false)
	{
		// Confirm that the commerce.js script exists.
		$I->seeInSource('commerce.js');

		// Confirm that the link exists.
		$I->seeElementInDOM('a[data-commerce]');

		// Confirm that the link points to the correct product.
		$I->assertEquals($productURL, $I->grabAttributeFrom('a[data-commerce]', 'href'));

		// Confirm that the button text is as expected.
		if ($text !== false) {
			$I->seeInSource('>' . $text . '</a>');
		}

		// Click the button to confirm that the ConvertKit modal displays; this confirms
		// necessary ConvertKit scripts have been loaded.
		$I->click('a[href="' . $productURL . '"]');
		$I->seeElementInDOM('iframe[data-active]');
	}

	/**
	 * Check that expected HTML exists in the DOM of the page we're viewing for
	 * a Product block or shortcode, and that the button loads the expected
	 * ConvertKit Product modal.
	 *
	 * @since   1.9.8.5
	 *
	 * @param   AcceptanceTester $I              Tester.
	 * @param   string           $productURL     Product URL.
	 * @param   bool|string      $text           Test if the button text matches the given value.
	 * @param   bool|string      $textColor      Test if the given text color is applied.
	 * @param   bool|string      $backgroundColor Test is the given background color is applied.
	 */
	public function seeProductOutput($I, $productURL, $text = false, $textColor = false, $backgroundColor = false)
	{
		// Confirm that the product stylesheet loaded.
		$I->seeInSource('<link rel="stylesheet" id="convertkit-gutenberg-block-product-frontend-css" href="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-content/plugins/convertkit/resources/frontend/css/product.css');

		// Confirm that the block displays.
		$I->seeElementInDOM('a.convertkit-product.wp-block-button__link');

		// Confirm that the button links to the correct product.
		$I->assertEquals($productURL, $I->grabAttributeFrom('a.convertkit-product', 'href'));

		// Confirm that the text is as expected.
		if ($text !== false) {
			$I->see($text);
		}

		// Confirm that the text color is as expected.
		if ($textColor !== false) {
			$I->seeElementInDOM('a.convertkit-product.has-text-color');
			$I->assertStringContainsString(
				'color:' . $textColor,
				$I->grabAttributeFrom('a.convertkit-product', 'style')
			);
		}

		// Confirm that the background color is as expected.
		if ($backgroundColor !== false) {
			$I->seeElementInDOM('a.convertkit-product.has-background');
			$I->assertStringContainsString(
				'background-color:' . $backgroundColor,
				$I->grabAttributeFrom('a.convertkit-product', 'style')
			);
		}

		// Click the button to confirm that the ConvertKit modal displays; this confirms
		// necessary ConvertKit scripts have been loaded.
		$I->click('a.convertkit-product');
		$I->seeElementInDOM('iframe[data-active]');
	}

	/**
	 * Check that expected HTML does exists in the DOM of the page we're viewing for
	 * a Product block or shortcode.
	 *
	 * @since   1.9.8.5
	 *
	 * @param   AcceptanceTester $I      Tester.
	 */
	public function dontSeeProductOutput($I)
	{
		// Confirm that the block does not display.
		$I->dontSeeElementInDOM('div.wp-block-button a.convertkit-product');
	}
}
