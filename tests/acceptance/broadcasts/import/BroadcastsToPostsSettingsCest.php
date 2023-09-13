<?php
/**
 * Tests Broadcast Settings functionality at Settings > ConvertKit > Broadcasts.
 *
 * @since   2.2.8
 */
class BroadcastsToPostsSettingsCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   2.2.8
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		// Activate ConvertKit Plugin.
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPlugin($I);

		// Create a Category named 'ConvertKit Broadcasts to Posts'.
		$I->haveTermInDatabase('ConvertKit Broadcasts to Posts', 'category');
	}

	/**
	 * Test that the Settings > ConvertKit > Broadcasts screen has expected a11y output, such as label[for].
	 *
	 * @since   2.3.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAccessibility(AcceptanceTester $I)
	{
		// Go to the Plugin's Broadcasts Screen.
		$I->loadConvertKitSettingsBroadcastsScreen($I);

		// Confirm that settings have label[for] attributes.
		$I->seeInSource('<label for="enabled">');
		$I->seeInSource('<label for="category_id">');
		$I->seeInSource('<label for="published_at_min_date">');
		$I->seeInSource('<label for="no_styles">');	
	}

	/**
	 * Tests that enabling and disabling Broadcasts works with no errors,
	 * and that other form fields show / hide depending on the setting.
	 *
	 * @since   2.2.8
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testEnableDisable(AcceptanceTester $I)
	{
		// Go to the Plugin's Broadcasts Screen.
		$I->loadConvertKitSettingsBroadcastsScreen($I);

		// Confirm that additional fields are hidden, because the 'Enable' option is not checked.
		$I->dontSeeElement('input.enabled');

		// Enable Broadcasts to Posts.
		$I->checkOption('#enabled');

		// Confirm that additional fields are now displayed.
		$I->waitForElementVisible('input.enabled');

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that settings saved and additional fields remain displayed.
		$I->seeCheckboxIsChecked('#enabled');
		$I->seeElement('table.form-table tbody tr td a.button');
		$I->seeElement('div.convertkit-select2-container');
		$I->seeElement('input#published_at_min_date');
		$I->seeElement('input#no_styles');

		// Check the next import date and time is displayed.
		$I->see('Broadcasts will next import at approximately');

		// Disable Broadcasts to Posts.
		$I->uncheckOption('#enabled');

		// Confirm that additional fields are hidden, because the 'Enable' option is not checked.
		$I->waitForElementNotVisible('input.enabled');

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that settings saved and additional fields are hidden, because the 'Enable' option is not checked.
		$I->dontSeeCheckboxIsChecked('#enabled');
		$I->dontSeeElement('table.form-table tbody tr td a.button');
		$I->dontSeeElement('div.convertkit-select2-container');
		$I->dontSeeElement('input#published_at_min_date');
		$I->dontSeeElement('input#no_styles');

		// Check the next import date and time is not displayed.
		$I->dontSee('Broadcasts will next import at approximately');
	}

	/**
	 * Tests that saving settings works.
	 *
	 * @since   2.2.8
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSaveSettings(AcceptanceTester $I)
	{
		// Go to the Plugin's Broadcasts Screen.
		$I->loadConvertKitSettingsBroadcastsScreen($I);

		// Enable Broadcasts to Posts, and modify settings.
		$I->checkOption('#enabled');
		$I->fillSelect2Field($I, '#select2-_wp_convertkit_settings_broadcasts_category_id-container', 'ConvertKit Broadcasts to Posts');
		$I->fillField('_wp_convertkit_settings_broadcasts[published_at_min_date]', '01/01/2023');

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that settings saved.
		$I->seeCheckboxIsChecked('#enabled');
		$I->seeInField('_wp_convertkit_settings_broadcasts[category_id]', 'ConvertKit Broadcasts to Posts');
		$I->seeInField('_wp_convertkit_settings_broadcasts[published_at_min_date]', '2023-01-01');
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   2.2.8
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);

		// Remove Category named 'ConvertKit Broadcasts to Posts'.
		$I->dontHaveTermInDatabase(
			array(
				'name' => 'ConvertKit Broadcasts to Posts',
			)
		);
	}
}
