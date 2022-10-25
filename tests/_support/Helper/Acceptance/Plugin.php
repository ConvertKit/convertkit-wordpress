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
	 * Helper method to setup the Plugin's API Key and Secret.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I          AcceptanceTester.
	 * @param   mixed            $apiKey     API Key (if specified, used instead of CONVERTKIT_API_KEY).
	 * @param   mixed            $apiSecret  API Secret (if specified, used instead of CONVERTKIT_API_SECRET).
	 */
	public function setupConvertKitPlugin($I, $apiKey = false, $apiSecret = false)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Determine API Key and Secret to use.
		$convertKitAPIKey    = ( $apiKey !== false ? $apiKey : $_ENV['CONVERTKIT_API_KEY'] );
		$convertKitAPISecret = ( $apiSecret !== false ? $apiSecret : $_ENV['CONVERTKIT_API_SECRET'] );

		// Complete API Fields.
		$I->fillField('_wp_convertkit_settings[api_key]', $convertKitAPIKey);
		$I->fillField('_wp_convertkit_settings[api_secret]', $convertKitAPISecret);

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the value of the fields match the inputs provided.
		$I->seeInField('_wp_convertkit_settings[api_key]', $convertKitAPIKey);
		$I->seeInField('_wp_convertkit_settings[api_secret]', $convertKitAPISecret);
	}

	/**
	 * Helper method to setup the Plugin's Default Form setting for Pages and Posts.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I     AcceptanceTester.
	 */
	public function setupConvertKitPluginDefaultForm($I)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Select Default Form for Pages and Posts.
		$I->fillSelect2Field($I, '#select2-_wp_convertkit_settings_page_form-container', $_ENV['CONVERTKIT_API_FORM_NAME']);
		$I->fillSelect2Field($I, '#select2-_wp_convertkit_settings_post_form-container', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the value of the fields match the inputs provided.
		$I->seeInField('_wp_convertkit_settings[page_form]', $_ENV['CONVERTKIT_API_FORM_NAME']);
		$I->seeInField('_wp_convertkit_settings[post_form]', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Return Form ID for Pages.
		return $I->grabValueFrom('_wp_convertkit_settings[page_form]');
	}

	/**
	 * Helper method to setup the Plugin's Default Legacy Form setting for Pages and Posts.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I     AcceptanceTester.
	 */
	public function setupConvertKitPluginDefaultLegacyForm($I)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Select Default Form for Pages and Posts.
		$I->fillSelect2Field($I, '#select2-_wp_convertkit_settings_page_form-container', $_ENV['CONVERTKIT_API_LEGACY_FORM_NAME']);
		$I->fillSelect2Field($I, '#select2-_wp_convertkit_settings_post_form-container', $_ENV['CONVERTKIT_API_LEGACY_FORM_NAME']);

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the value of the fields match the inputs provided.
		$I->seeInField('_wp_convertkit_settings[page_form]', $_ENV['CONVERTKIT_API_LEGACY_FORM_NAME']);
		$I->seeInField('_wp_convertkit_settings[post_form]', $_ENV['CONVERTKIT_API_LEGACY_FORM_NAME']);

		// Return Form ID for Pages.
		return $I->grabValueFrom('_wp_convertkit_settings[page_form]');
	}

	/**
	 * Helper method to setup the Plugin's Default Form setting for WooCommerce Products.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I     AcceptanceTester.
	 */
	public function setupConvertKitPluginDefaultFormForWooCommerceProducts($I)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Select option.
		$I->fillSelect2Field($I, '#select2-_wp_convertkit_settings_product_form-container', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the value of the fields match the inputs provided.
		$I->seeInField('_wp_convertkit_settings[product_form]', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Return Form ID.
		return $I->grabValueFrom('_wp_convertkit_settings[product_form]');
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
	 * Helper method to enable the Plugin's Settings > General > Debug option.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I     AcceptanceTester.
	 */
	public function enableDebugLog($I)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Tick field.
		$I->checkOption('#debug');

		// Click the Save Changes button.
		$I->click('Save Changes');
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
			'utm_source=WordPress&utm_content=convertkit',
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
