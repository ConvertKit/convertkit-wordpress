<?php
/**
 * Tests for the ConvertKit Broadcasts Divi Module.
 *
 * @since   2.5.7
 */
class DiviBroadcastsCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
		$I->activateThirdPartyPlugin($I, 'divi-builder');
	}

	/**
	 * Test the Broadcasts module works when added
	 * using Divi's backend editor.
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsModuleInBackendEditor(AcceptanceTester $I)
	{
		// Setup Plugin, without defining default Forms.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);

		// Activate Classic Editor Plugin.
		$I->activateThirdPartyPlugin($I, 'classic-editor');

		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Broadcasts: Divi: Backend Editor');

		// Configure metabox's Form setting = None, ensuring we only test the block in Gutenberg.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'None' ],
			]
		);

		// Scroll to Publish meta box, so its buttons are not hidden.
		$I->scrollTo('#submitdiv');

		// Wait for the Publish button to change its state from disabled (WordPress disables it for a moment when auto-saving).
		$I->waitForElementVisible('input#publish:not(:disabled)');

		// Click the Publish button twice, because Divi is flaky at best.
		$I->click('input#publish');
		$I->wait(2);
		$I->click('input#publish');

		// Wait for notice to display.
		$I->waitForElementNotVisible('.et-fb-preloader');
		$I->waitForElementVisible('.notice-success');

		// Remove transient set by Divi that would show the welcome modal.
		$I->dontHaveTransientInDatabase('et_builder_show_bfb_welcome_modal');

		// Click Divi Builder button.
		$I->click('#et_pb_toggle_builder');

		// Dismiss modal if displayed.
		// May have been dismissed by other tests in the suite e.g. DiviFormCest.
		try {
			$I->waitForElementVisible('.et-core-modal-action-dont-restore');
			$I->click('.et-core-modal-action-dont-restore');
		} catch ( \Facebook\WebDriver\Exception\NoSuchElementException $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// No modal exists, so nothing to dismiss.
		}

		// Click Build from scratch button.
		$I->waitForElementVisible('.et-fb-page-creation-card-build_from_scratch');
		$I->click('Start Building', '.et-fb-page-creation-card-build_from_scratch');

		// Insert row.
		$I->waitForElementVisible('li[data-layout="4_4"]');
		$I->click('li[data-layout="4_4"]');

		// Search for module.
		$I->waitForElementVisible('input[name="filterByTitle"]');
		$I->fillField('filterByTitle', 'ConvertKit Broadcasts');

		// Insert module.
		$I->waitForElementVisible('li.convertkit_broadcasts');
		$I->click('li.convertkit_broadcasts');

		// Save Divi module and view the page on the frontend site.
		$I->saveDiviModuleInBackendEditorAndViewPage($I);

		// Confirm that the block displays.
		$I->seeBroadcastsOutput($I);

		// Confirm that the default date format is as expected.
		$I->seeInSource('<time datetime="' . date( 'Y-m-d', strtotime( $_ENV['CONVERTKIT_API_BROADCAST_FIRST_DATE'] ) ) . '">' . date( 'F j, Y', strtotime( $_ENV['CONVERTKIT_API_BROADCAST_FIRST_DATE'] ) ) . '</time>');

		// Confirm that the default expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', [ 1, 10 ]);

		// Confirm that the expected Broadcast name is displayed first links to the expected URL, with UTM parameters.
		$I->assertEquals(
			$I->grabAttributeFrom('div.convertkit-broadcasts ul.convertkit-broadcasts-list li.convertkit-broadcast:nth-child(2) a', 'href'),
			$_ENV['CONVERTKIT_API_BROADCAST_FIRST_URL'] . '?utm_source=wordpress&utm_term=en_US&utm_content=convertkit'
		);

		// Deactivate Classic Editor.
		$I->deactivateThirdPartyPlugin($I, 'classic-editor');
	}

	/**
	 * Test the Broadcasts module works when added
	 * using Divi's frontend editor.
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsModuleInFrontendEditor(AcceptanceTester $I)
	{
		// Setup Plugin, without defining default Forms.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Divi: Frontend');

		// Configure metabox's Form setting = None, ensuring we only test the block in Gutenberg.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'None' ],
			]
		);

		// Publish Page.
		$url = $I->publishGutenbergPage($I);

		// Click Divi Builder button.
		$I->click('Use Divi Builder');

		// Reload page to dismiss modal.
		$I->wait(5);
		$I->amOnUrl($url . '?et_fb=1&PageSpeed=off');

		// Click Build from scratch button.
		$I->waitForElementVisible('.et-fb-page-creation-card-build_from_scratch', 30);
		$I->click('Start Building', '.et-fb-page-creation-card-build_from_scratch');

		// Insert row.
		$I->waitForElementVisible('li[data-layout="4_4"]');
		$I->click('li[data-layout="4_4"]');

		// Search for module.
		$I->waitForElementVisible('input[name="filterByTitle"]');
		$I->fillField('filterByTitle', 'ConvertKit Broadcasts');

		// Insert module.
		$I->waitForElementVisible('li.convertkit_broadcasts');
		$I->click('li.convertkit_broadcasts');

		// Save module.
		$I->click('button[data-tip="Save Changes"]');

		// Save page.
		$I->click('.et-fb-page-settings-bar__toggle-button');
		$I->waitForElementVisible('button.et-fb-button--publish');
		$I->click('button.et-fb-button--publish');
		$I->wait(3);

		// Load page without Divi frontend builder.
		$I->amOnUrl($url);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the block displays.
		$I->seeBroadcastsOutput($I);

		// Confirm that the default date format is as expected.
		$I->seeInSource('<time datetime="' . date( 'Y-m-d', strtotime( $_ENV['CONVERTKIT_API_BROADCAST_FIRST_DATE'] ) ) . '">' . date( 'F j, Y', strtotime( $_ENV['CONVERTKIT_API_BROADCAST_FIRST_DATE'] ) ) . '</time>');

		// Confirm that the default expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', [ 1, 10 ]);

		// Confirm that the expected Broadcast name is displayed first links to the expected URL, with UTM parameters.
		$I->assertEquals(
			$I->grabAttributeFrom('div.convertkit-broadcasts ul.convertkit-broadcasts-list li.convertkit-broadcast:nth-child(2) a', 'href'),
			$_ENV['CONVERTKIT_API_BROADCAST_FIRST_URL'] . '?utm_source=wordpress&utm_term=en_US&utm_content=convertkit'
		);
	}

	/**
	 * Test the Broadcasts module displays the expected message when the Plugin has no credentials
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsModuleInFrontendEditorWhenNoCredentials(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Divi: Broadcasts: Frontend: No Credentials');

		// Publish Page.
		$url = $I->publishGutenbergPage($I);

		// Click Divi Builder button.
		$I->click('Use Divi Builder');

		// Reload page to dismiss modal.
		$I->wait(5);
		$I->amOnUrl($url . '?et_fb=1&PageSpeed=off');

		// Click Build from scratch button.
		$I->waitForElementVisible('.et-fb-page-creation-card-build_from_scratch', 30);
		$I->click('Start Building', '.et-fb-page-creation-card-build_from_scratch');

		// Insert row.
		$I->waitForElementVisible('li[data-layout="4_4"]');
		$I->click('li[data-layout="4_4"]');

		// Search for module.
		$I->waitForElementVisible('input[name="filterByTitle"]');
		$I->fillField('filterByTitle', 'ConvertKit Broadcasts');

		// Insert module.
		$I->waitForElementVisible('li.convertkit_broadcasts');
		$I->click('li.convertkit_broadcasts');

		// Confirm the on screen message displays.
		$I->seeInSource('Not connected to ConvertKit');
		$I->seeInSource('Connect your ConvertKit account at Settings > ConvertKit, and then refresh this page to configure broadcasts to display.');
	}

	/**
	 * Test the Broadcasts module displays the expected message when the ConvertKit account
	 * has no broadcasts.
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsModuleInFrontendEditorWhenNoBroadcasts(AcceptanceTester $I)
	{
		// Setup Plugin.
		$I->setupConvertKitPluginCredentialsNoData($I);
		$I->setupConvertKitPluginResourcesNoData($I);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Divi: Frontend: No Broadcasts');

		// Publish Page.
		$url = $I->publishGutenbergPage($I);

		// Click Divi Builder button.
		$I->click('Use Divi Builder');

		// Reload page to dismiss modal.
		$I->wait(5);
		$I->amOnUrl($url . '?et_fb=1&PageSpeed=off');

		// Click Build from scratch button.
		$I->waitForElementVisible('.et-fb-page-creation-card-build_from_scratch', 30);
		$I->click('Start Building', '.et-fb-page-creation-card-build_from_scratch');

		// Insert row.
		$I->waitForElementVisible('li[data-layout="4_4"]');
		$I->click('li[data-layout="4_4"]');

		// Search for module.
		$I->waitForElementVisible('input[name="filterByTitle"]');
		$I->fillField('filterByTitle', 'ConvertKit Broadcasts');

		// Insert module.
		$I->waitForElementVisible('li.convertkit_broadcasts');
		$I->click('li.convertkit_broadcasts');

		// Confirm the on screen message displays.
		$I->seeInSource('No broadcasts exist in ConvertKit');
		$I->seeInSource('Add a broadcast to your ConvertKit account, and then refresh this page to configure broadcasts to display.');
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateThirdPartyPlugin($I, 'divi-builder');
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
