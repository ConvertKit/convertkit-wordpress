<?php
/**
 * Tests Broadcast Settings functionality at Settings > ConvertKit > Broadcasts.
 *
 * @since   2.2.8
 */
class BroadcastsSettingsCest
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
		$I->seeElement('input.enabled');

		// Check the WordPress Cron task was scheduled.
		$I->seeCronEvent($I, 'convertkit_resource_refresh_broadcasts');

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
		$I->dontSeeElement('input.enabled');

		// Check the WordPress Cron task was unscheduled.
		$I->dontSeeCronEvent($I, 'convertkit_resource_refresh_broadcasts');
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
		$I->fillSelect2Field($I, '#select2-_wp_convertkit_settings_broadcasts_category-container', 'ConvertKit Broadcasts to Posts');
		$I->fillField('_wp_convertkit_settings_broadcasts[send_at_min_date]', '01/01/2023');

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that settings saved.
		$I->seeCheckboxIsChecked('#enabled');
		$I->seeInField('_wp_convertkit_settings_broadcasts[category]', 'ConvertKit Broadcasts to Posts');
		$I->seeInField('_wp_convertkit_settings_broadcasts[send_at_min_date]', '2023-01-01');
	}

	/**
	 * Tests that the Member Content setting is not displayed when Member Content is disabled at
	 * Settings > ConvertKit > Member Content.
	 *
	 * @since   2.2.8
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentSettingHiddenWhenRestrictContentDisabled(AcceptanceTester $I)
	{
		// Go to the Plugin's Broadcasts Screen.
		$I->loadConvertKitSettingsBroadcastsScreen($I);

		// Enable Broadcasts to Posts.
		$I->checkOption('#enabled');

		// Confirm no Restrict Content option is displayed.
		$I->dontSeeElementInDOM('select#_wp_convertkit_settings_broadcasts_restrict_content');
	}

	/**
	 * Tests that the Member Content setting is displayed when Member Content is disabled at
	 * Settings > ConvertKit > Member Content.
	 *
	 * @since   2.2.8
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentSettingDisplayedWhenRestrictContentEnabled(AcceptanceTester $I)
	{
		// Enable Restrict Content.
		$I->setupConvertKitPluginRestrictContent(
			$I,
			[
				'enabled' => true,
			]
		);

		// Go to the Plugin's Broadcasts Screen.
		$I->loadConvertKitSettingsBroadcastsScreen($I);

		// Enable Broadcasts to Posts.
		$I->checkOption('#enabled');

		// Confirm no Restrict Content option is displayed.
		$I->fillSelect2Field($I, '#select2-_wp_convertkit_settings_broadcasts_restrict_content-container', $_ENV['CONVERTKIT_API_PRODUCT_NAME']);

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm the setting saved.
		$I->seeInField('_wp_convertkit_settings_broadcasts[restrict_content]', $_ENV['CONVERTKIT_API_PRODUCT_NAME']);
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
