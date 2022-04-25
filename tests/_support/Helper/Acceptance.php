<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Acceptance extends \Codeception\Module
{
	/**
	 * Helper method to assert that there are non PHP errors, warnings or notices output
	 * 
	 * @since 	1.9.6
	 */
	public function checkNoWarningsAndNoticesOnScreen($I)
	{
		// Check that no Xdebug errors exist.
		$I->dontSeeElement('.xdebug-error');
		$I->dontSeeElement('.xe-notice');
	}

	/**
	 * Helper method to enter text into a jQuery Select2 Field, selecting the option that appears.
	 * 
	 * @since 	1.9.6.4
	 * 
	 * @param 	AcceptanceTester 	$I
	 * @param 	string 				$container 	Field CSS Class / ID
	 * @param 	string 				$value 		Field Value
	 * @param 	string 				$ariaAttributeName 	Aria Attribute Name (aria-controls|aria-owns)
	 */
	public function fillSelect2Field($I, $container, $value, $ariaAttributeName = 'aria-controls')
	{
		$fieldID = $I->grabAttributeFrom($container, 'id');
		$fieldName = str_replace('-container', '', str_replace('select2-', '', $fieldID));
		$I->click('#'.$fieldID);
		$I->waitForElementVisible('.select2-search__field[' . $ariaAttributeName . '="select2-' . $fieldName . '-results"]');
		$I->fillField('.select2-search__field[' . $ariaAttributeName . '="select2-' . $fieldName . '-results"]', $value);
		$I->waitForElementVisible('ul#select2-' . $fieldName . '-results li.select2-results__option--highlighted');
		$I->pressKey('.select2-search__field[' . $ariaAttributeName . '="select2-' . $fieldName . '-results"]', \Facebook\WebDriver\WebDriverKeys::ENTER);
	}

	/**
	 * Helper method to close the Gutenberg "Welcome to the block editor" dialog, which
	 * might show for each Page/Post test performed due to there being no persistence
	 * remembering that the user dismissed the dialog.
	 * 
	 * @since 	1.9.6
	 */
	public function maybeCloseGutenbergWelcomeModal($I)
	{
		try {
			$I->performOn('.components-modal__screen-overlay', [
				'click' => '.components-modal__screen-overlay .components-modal__header button.components-button'
			], 3);
		} catch ( \Facebook\WebDriver\Exception\TimeoutException $e ) {
		}
	}

	/**
	 * Add the given block when adding or editing a Page, Post or Custom Post Type
	 * in Gutenberg.
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @param 	AcceptanceTester 	$I 						Acceptance Tester.
	 * @param 	string 				$blockName 				Block Name (e.g. 'ConvertKit Form')
	 * @param 	string 				$blockProgrammaticName 	Programmatic Block Name (e.g. 'convertkit-form')
	 */
	public function addGutenbergBlock($I, $blockName, $blockProgrammaticName)
	{
		// Click Add Block Button.
		$I->click('button.edit-post-header-toolbar__inserter-toggle');

		// When the Blocks sidebar appears, search for the block.
		// Removed [aria-label] selector, as its contents change between different WordPress versions.
		$I->waitForElementVisible('.interface-interface-skeleton__secondary-sidebar');
		$I->fillField('.block-editor-inserter__content input[type=search]', $blockName);
		$I->seeElementInDOM('.block-editor-inserter__panel-content button.editor-block-list-item-' . $blockProgrammaticName);
		$I->click('.block-editor-inserter__panel-content button.editor-block-list-item-' . $blockProgrammaticName);
	}

	/**
	 * Helper method to activate the ConvertKit Plugin, checking
	 * it activated and no errors were output.
	 * 
	 * @since 	1.9.6
	 */
	public function activateConvertKitPlugin($I)
	{
		$I->activateThirdPartyPlugin($I, 'convertkit');
	}

	/**
	 * Helper method to deactivate the ConvertKit Plugin, checking
	 * it activated and no errors were output.
	 * 
	 * @since 	1.9.6
	 */
	public function deactivateConvertKitPlugin($I)
	{
		$I->deactivateThirdPartyPlugin($I, 'convertkit');
	}

	/**
	 * Helper method to activate a third party Plugin, checking
	 * it activated and no errors were output.
	 * 
	 * @since 	1.9.6.7
	 * 
	 * @param 	string 	$name 	Plugin Slug.
	 */
	public function activateThirdPartyPlugin($I, $name)
	{
		// Login as the Administrator
		$I->loginAsAdmin();

		// Go to the Plugins screen in the WordPress Administration interface.
		$I->amOnPluginsPage();

		// Activate the Plugin.
		$I->activatePlugin($name);

		// Check that the Plugin activated successfully.
		$I->seePluginActivated($name);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Helper method to activate a third party Plugin, checking
	 * it activated and no errors were output.
	 * 
	 * @since 	1.9.6.7
	 * 
	 * @param 	string 	$name 	Plugin Slug.
	 */
	public function deactivateThirdPartyPlugin($I, $name)
	{
		// Login as the Administrator
		$I->loginAsAdmin();

		// Go to the Plugins screen in the WordPress Administration interface.
		$I->amOnPluginsPage();

		// Deactivate the Plugin.
		$I->deactivatePlugin($name);

		// Check that the Plugin deactivated successfully.
		$I->seePluginDeactivated($name);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Helper method to setup the Plugin's API Key and Secret.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	mixed 	$apiKey 	API Key (if specified, used instead of CONVERTKIT_API_KEY)
	 * @param 	mixed 	$apiSecret 	API Secret (if specified, used instead of CONVERTKIT_API_SECRET)
	 */
	public function setupConvertKitPlugin($I, $apiKey = false, $apiSecret = false)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Determine API Key and Secret to use.
		$convertKitAPIKey = ($apiKey !== false ? $apiKey : $_ENV['CONVERTKIT_API_KEY']);
		$convertKitAPISecret = ($apiSecret !== false ? $apiSecret : $_ENV['CONVERTKIT_API_SECRET']);

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
	 * @since 	1.9.6
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

		// Return Form ID for Pages
		return $I->grabValueFrom('_wp_convertkit_settings[page_form]');
	}

	/**
	 * Helper method to setup the Plugin's Default Legacy Form setting for Pages and Posts.
	 * 
	 * @since 	1.9.6
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

		// Return Form ID for Pages
		return $I->grabValueFrom('_wp_convertkit_settings[page_form]');
	}

	/**
	 * Helper method to setup the Plugin's Default Form setting for WooCommerce Products.
	 * 
	 * @since 	1.9.6
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

		// Return Form ID
		return $I->grabValueFrom('_wp_convertkit_settings[product_form]');
	}

	/**
	 * Helper method to reset the ConvertKit Plugin settings, as if it's a clean installation.
	 * 
	 * @since 	1.9.6.7
	 */
	public function resetConvertKitPlugin($I)
	{
		// Plugin Settings.
		$I->dontHaveOptionInDatabase('_wp_convertkit_settings');
		$I->dontHaveOptionInDatabase('convertkit_version');

		// Resources.
		$I->dontHaveOptionInDatabase('convertkit_forms');
		$I->dontHaveOptionInDatabase('convertkit_landing_pages');
		$I->dontHaveOptionInDatabase('convertkit_tags');

		// Review Request.
		$I->dontHaveOptionInDatabase('convertkit-review-request');
		$I->dontHaveOptionInDatabase('convertkit-review-dismissed');

		// Upgrades.
		$I->dontHaveOptionInDatabase('_wp_convertkit_upgrade_posts');	
	}

	/**
	 * Helper method to load the Plugin's Settings > General screen.
	 * 
	 * @since 	1.9.6
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
	 * @since 	1.9.6
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
	 * @since 	1.9.6
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
	 * Helper method to determine if the given entry exists in the Plugin Debug Log screen's textarea.
	 * 
	 * @since 	1.9.6
	 */
	public function seeInPluginDebugLog($I, $entry)
	{
		$I->loadConvertKitSettingsToolsScreen($I);
		$I->seeInSource($entry);
	}

	/**
	 * Helper method to determine if the given entry does not exist in the Plugin Debug Log screen's textarea.
	 * 
	 * @since 	1.9.6
	 */
	public function dontSeeInPluginDebugLog($I, $entry)
	{
		$I->loadConvertKitSettingsToolsScreen($I);
		$I->dontSeeInSource($entry);
	}

	/**
	 * Helper method to setup the WishList Member Plugin.
	 * 
	 * @since 	1.9.6
	 */
	public function setupWishListMemberPlugin($I)
	{
		// Load WishList Member Settings screen, which will load the first time configuration wizard.
		$I->amOnAdminPage('admin.php?page=WishListMember');

		// Skip Licensing
		$I->click('a.skip-license');
		$I->performOn( 'a[next-screen="start"]', function($I) {
			$I->click('a[next-screen="start"]');
		});
		
		// Step 1
		$I->fillField('input[name="name"]', 'Bronze');
		$I->click('.step-1 a[next-screen="step-2"]');

		// Step 2
		$I->click('.step-2 a[next-screen="step-3"]');

		// Step 3
		$I->click('.step-3 a[next-screen="step-4"]');

		// Step 4
		$I->click('.step-4 a[next-screen="step-5"]');

		// Save
		$I->click('.step-5 a.save-btn');

		$I->performOn('.-congrats-gs', function($I) {
			$I->click('a.next-btn');
			$I->seeInSource('Bronze');
		});
	}

	/**
	 * Generates a unique email address for use in a test, comprising of a prefix,
	 * date + time and PHP version number.
	 * 
	 * This ensures that if tests are run in parallel, the same email address
	 * isn't used for two tests across parallel testing runs.
	 * 
	 * @since 	1.9.6.7
	 */
	public function generateEmailAddress()
	{
		return 'wordpress-' . date( 'Y-m-d-H-i-s' ) . '-php-' . PHP_VERSION_ID . '@convertkit.com';
	}

	/**
	 * Check the given email address exists as a subscriber.
	 * 
	 * @param 	AcceptanceTester $I 			AcceptanceTester
	 * @param 	string 			$emailAddress 	Email Address
	 */ 	
	public function apiCheckSubscriberExists($I, $emailAddress)
	{
		// Run request.
		$results = $this->apiRequest('subscribers', 'GET', [
			'email_address' => $emailAddress,
		]);

		// Check at least one subscriber was returned and it matches the email address.
		$I->assertGreaterThan(0, $results['total_subscribers']);
		$I->assertEquals($emailAddress, $results['subscribers'][0]['email_address']);
	}

	/**
	 * Check the given email address does not exists as a subscriber.
	 * 
	 * @param 	AcceptanceTester $I 			AcceptanceTester
	 * @param 	string 			$emailAddress 	Email Address
	 */ 	
	public function apiCheckSubscriberDoesNotExist($I, $emailAddress)
	{
		// Run request.
		$results = $this->apiRequest('subscribers', 'GET', [
			'email_address' => $emailAddress,
		]);

		// Check no subscribers are returned by this request.
		$I->assertEquals(0, $results['total_subscribers']);
	}

	/**
	 * Unsubscribes the given email address. Useful for clearing the API
	 * between tests.
	 * 
	 * @param 	string 			$emailAddress 	Email Address
	 */ 	
	public function apiUnsubscribe($emailAddress)
	{
		// Run request.
		$this->apiRequest('unsubscribe', 'PUT', [
			'email' => $emailAddress,
		]);
	}

	/**
	 * Sends a request to the ConvertKit API, typically used to read an endpoint to confirm
	 * that data in an Acceptance Test was added/edited/deleted successfully.
	 * 
	 * @param 	string 	$endpoint 	Endpoint
	 * @param 	string 	$method 	Method (GET|POST|PUT)
	 * @param 	array 	$params 	Endpoint Parameters
	 */
	public function apiRequest($endpoint, $method = 'GET', $params = array())
	{
		// Build query parameters.
		$params = array_merge($params, [
			'api_key' => $_ENV['CONVERTKIT_API_KEY'],
			'api_secret' => $_ENV['CONVERTKIT_API_SECRET'],
		]);

		// Send request.
		try {
			$client = new \GuzzleHttp\Client();
			$result = $client->request($method, 'https://api.convertkit.com/v3/' . $endpoint . '?' . http_build_query($params), [
				'headers' => [
					'Accept-Encoding' => 'gzip',
					'timeout'         => 5,
				],
			]);

			// Return JSON decoded response.
			return json_decode($result->getBody()->getContents(), true);
		} catch(\GuzzleHttp\Exception\ClientException $e) {
			return [];
		}
	}
}
