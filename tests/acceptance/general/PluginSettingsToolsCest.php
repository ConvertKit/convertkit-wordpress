<?php
/**
 * Tests for the Settings > ConvertKit > Tools screens.
 *
 * @since   1.9.6
 */
class PluginSettingsToolsCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		// Activate and Setup ConvertKit plugin.
		$I->activateConvertKitPlugin($I);
	}

	/**
	 * Test that the Debug Log section is populated when debugging is enabled and an action is
	 * performed that will populate the log.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testDebugLogExists(AcceptanceTester $I)
	{
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);
		$I->loadConvertKitSettingsToolsScreen($I);

		// Check that the Debug Log textarea contains some expected output i.e.
		// does not show the 'No logs have been generated.' message.
		$I->dontSeeInField('#debug-log-textarea', 'No logs have been generated.');
	}

	/**
	 * Test that the Download Log option works.
	 *
	 * @since   1.9.7.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testDownloadLog(AcceptanceTester $I)
	{
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);
		$I->loadConvertKitSettingsToolsScreen($I);

		// Click the Export button.
		// This will download the file to $_ENV['WP_ROOT_FOLDER'].
		$I->click('input#convertkit-download-debug-log');

		// Wait 2 seconds for the download to complete.
		sleep(2);

		// Check downloaded file exists and contains some expected information.
		$I->openFile($_ENV['WP_ROOT_FOLDER'] . '/convertkit-log.txt');
		$I->seeInThisFile('API: account()');

		// Delete the file.
		$I->deleteFile($_ENV['WP_ROOT_FOLDER'] . '/convertkit-log.txt');
	}

	/**
	 * Test that the System Info section is populated.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSystemInfoExists(AcceptanceTester $I)
	{
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);
		$I->loadConvertKitSettingsToolsScreen($I);

		// Check that the System Info textarea contains some expected output.
		$I->assertNotFalse(strpos($I->grabValueFrom('#system-info-textarea'), '### wp-core'));
		$I->assertNotFalse(strpos($I->grabValueFrom('#system-info-textarea'), '### wp-active-theme'));
		$I->assertNotFalse(strpos($I->grabValueFrom('#system-info-textarea'), '### wp-themes-inactive'));
		$I->assertNotFalse(strpos($I->grabValueFrom('#system-info-textarea'), '### wp-plugins-active'));
		$I->assertNotFalse(strpos($I->grabValueFrom('#system-info-textarea'), '### wp-plugins-inactive'));
		$I->assertNotFalse(strpos($I->grabValueFrom('#system-info-textarea'), '### wp-media'));
		$I->assertNotFalse(strpos($I->grabValueFrom('#system-info-textarea'), '### wp-server'));
		$I->assertNotFalse(strpos($I->grabValueFrom('#system-info-textarea'), '### wp-database'));
		$I->assertNotFalse(strpos($I->grabValueFrom('#system-info-textarea'), '### wp-constants'));
		$I->assertNotFalse(strpos($I->grabValueFrom('#system-info-textarea'), '### wp-filesystem'));
	}

	/**
	 * Test that the Download System Info option works.
	 *
	 * @since   1.9.7.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testDownloadSystemInfo(AcceptanceTester $I)
	{
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);
		$I->loadConvertKitSettingsToolsScreen($I);

		// Click the Export button.
		// This will download the file to $_ENV['WP_ROOT_FOLDER'].
		$I->click('input#convertkit-download-system-info');

		// Wait 2 seconds for the download to complete.
		sleep(2);

		// Check downloaded file exists and contains some expected information.
		$I->openFile($_ENV['WP_ROOT_FOLDER'] . '/convertkit-system-info.txt');
		$I->seeInThisFile('### wp-core');
		$I->seeInThisFile('### wp-active-theme');
		$I->seeInThisFile('### wp-themes-inactive');
		$I->seeInThisFile('### wp-plugins-active');
		$I->seeInThisFile('### wp-plugins-inactive');
		$I->seeInThisFile('### wp-media');
		$I->seeInThisFile('### wp-server');
		$I->seeInThisFile('### wp-database');
		$I->seeInThisFile('### wp-constants');
		$I->seeInThisFile('### wp-filesystem');

		// Delete the file.
		$I->deleteFile($_ENV['WP_ROOT_FOLDER'] . '/convertkit-system-info.txt');
	}

	/**
	 * Test that the Export Configuration option works.
	 *
	 * @since   1.9.7.4
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testExportAndImportValidConfiguration(AcceptanceTester $I)
	{
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);
		$I->loadConvertKitSettingsToolsScreen($I);

		// Click the Export button.
		// This will download the file to $_ENV['WP_ROOT_FOLDER'].
		$I->scrollTo('#export');
		$I->click('input#convertkit-export');

		// Wait 2 seconds for the download to complete.
		sleep(2);

		// Check downloaded file exists.
		$I->openFile($_ENV['WP_ROOT_FOLDER'] . '/convertkit-export.json');

		// Confirm some expected general settings data is included.
		$I->seeInThisFile('{"settings":{"access_token":"' . $_ENV['CONVERTKIT_OAUTH_ACCESS_TOKEN'] . '","refresh_token":"' . $_ENV['CONVERTKIT_OAUTH_REFRESH_TOKEN'] . '"');

		// Confirm some expected Restrict Content settings data is included.
		$I->seeInThisFile('"restrict_content":{"permit_crawlers":');

		// Copy the file to the tests/_data folder.
		$I->writeToFile('tests/_data/convertkit-export.json', file_get_contents($_ENV['WP_ROOT_FOLDER'] . '/convertkit-export.json'));

		// Import the created configuration file.
		// Load Tools screen.
		$I->loadConvertKitSettingsToolsScreen($I);

		// Select the configuration file at tests/_data/convertkit-export.json to import.
		$I->attachFile('input[name=import]', 'convertkit-export.json');

		// Click the Import button.
		$I->click('input#convertkit-import');

		// Confirm success message displays.
		$I->see('Configuration imported successfully.');

		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Check the fields are ticked.
		$I->seeCheckboxIsChecked('#debug');

		// Go to the Plugin's Restrict Content Settings Screen.
		$I->loadConvertKitSettingsRestrictContentScreen($I);

		// Confirm that the text fields contain the expected data.
		$I->checkRestrictContentSettings($I, $I->getRestrictedContentDefaultSettings());

		// Delete export files.
		$I->deleteFile($_ENV['WP_ROOT_FOLDER'] . '/convertkit-export.json');
		$I->deleteFile('tests/_data/convertkit-export.json');
	}

	/**
	 * Test that the Import Configuration option returns the expected error when no file
	 * is selected.
	 *
	 * @since   1.9.7.4
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testImportConfigurationWithNoFile(AcceptanceTester $I)
	{
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);
		$I->loadConvertKitSettingsToolsScreen($I);

		// Scroll to Import section.
		$I->scrollTo('#import');

		// Click the Import button.
		$I->click('input#convertkit-import');

		// Confirm error message displays.
		$I->see('An error occured uploading the configuration file.');
	}

	/**
	 * Test that the Import Configuration option returns the expected error when an invalid file
	 * is selected.
	 *
	 * @since   1.9.7.4
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testImportConfigurationWithInvalidFile(AcceptanceTester $I)
	{
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);
		$I->loadConvertKitSettingsToolsScreen($I);

		// Scroll to Import section.
		$I->scrollTo('#import');

		// Select the invalid configuration file at tests/_data/convertkit-export-invalid.json to import.
		$I->attachFile('input[name=import]', 'convertkit-export-invalid.json');

		// Wait for page to load.
		$I->waitForElementVisible('#wpfooter');

		// Click the Import button.
		$I->click('input#convertkit-import');

		// Confirm error message displays.
		$I->see('The uploaded configuration file contains no settings.');
	}

	/**
	 * Test that the Import Configuration option returns the expected error when a file
	 * that appears to be JSON is selected, but its content are not JSON.
	 *
	 * @since   1.9.7.4
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testImportConfigurationWithFakeJSONFile(AcceptanceTester $I)
	{
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);
		$I->loadConvertKitSettingsToolsScreen($I);

		// Scroll to Import section.
		$I->scrollTo('#import');

		// Select the invalid configuration file at tests/_data/convertkit-export-invalid.json to import.
		$I->attachFile('input[name=import]', 'convertkit-export-fake.json');

		// Click the Import button.
		$I->click('input#convertkit-import');

		// Wait for page to load.
		$I->waitForElementVisible('#wpfooter');

		// Confirm error message displays.
		$I->see('The uploaded configuration file isn\'t valid.');
	}

	/**
	 * Test that any $_REQUEST['page'] parameter on a settings screen is correctly escaped on output
	 * to prevent XSS.
	 *
	 * @since   2.2.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testTabParameterEscaping(AcceptanceTester $I)
	{
		// Define a page with a form that exploits the query parameter not being escaped.
		$I->havePageInDatabase(
			[
				'post_name'    => 'convertkit-settings-tab-parameter-escaping',
				'post_content' => '<form action="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-admin/options-general.php?page=_wp_convertkit_settings&tab=tools" method="POST">
      <input type="hidden" name="page" value=\'"style=animation-name:rotation onanimationstart=document.write(/XSS/)//\' />
      <input type="submit" value="Submit" />
    </form>',
			]
		);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-settings-tab-parameter-escaping');

		// Wait for frontend web site to load.
		$I->waitForElementVisible('body.page-template-default');

		// Click the submit button.
		$I->click('Submit');

		// Check that document.write did not work, which confirms XSS isn't possible as the query parameter is correctly escaped.
		$I->dontSee('/XSS/');
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   1.9.6.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
