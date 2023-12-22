<?php
/**
 * Tests for the Settings > ConvertKit > General screens.
 *
 * @since   1.9.6
 */
class PluginSettingsGeneralCest
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
		$I->activateConvertKitPlugin($I);
	}

	/**
	 * Test that the Settings > ConvertKit > General screen has expected a11y output, such as label[for], and
	 * UTM parameters are included in links displayed on the Plugins' Setting screen for the user to obtain
	 * their API Key and Secret, or sign in to their ConvertKit account.
	 *
	 * @since   1.9.7.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAccessibilityAndUTMParameters(AcceptanceTester $I)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Confirm that settings have label[for] attributes.
		$I->seeInSource('<label for="api_key">');
		$I->seeInSource('<label for="api_secret">');
		$I->seeInSource('<label for="_wp_convertkit_settings_page_form">');
		$I->seeInSource('<label for="_wp_convertkit_settings_post_form">');
		$I->seeInSource('<label for="debug">');
		$I->seeInSource('<label for="no_scripts">');
		$I->seeInSource('<label for="no_css">');

		// Confirm that UTM parameters exist for the 'Get your ConvertKit API Key' link.
		$I->seeInSource('<a href="https://app.convertkit.com/account_settings/advanced_settings/?utm_source=wordpress&amp;utm_term=en_US&amp;utm_content=convertkit" target="_blank">Get your ConvertKit API Key.</a>');

		// Confirm that UTM parameters exist for the 'Get your ConvertKit API Secret' link.
		$I->seeInSource('<a href="https://app.convertkit.com/account_settings/advanced_settings/?utm_source=wordpress&amp;utm_term=en_US&amp;utm_content=convertkit" target="_blank">Get your ConvertKit API Secret.</a>');

		// Confirm that UTM parameters exist for the 'Click here to create your first form' link.
		$I->seeInSource('<a href="https://app.convertkit.com/forms/new/?utm_source=wordpress&amp;utm_term=en_US&amp;utm_content=convertkit" target="_blank">Click here to create your first form</a>');

		// Confirm that the UTM parameters exist for the documentation links.
		$I->seeInSource('<a href="https://help.convertkit.com/en/articles/2502591-the-convertkit-wordpress-plugin?utm_source=wordpress&amp;utm_term=en_US&amp;utm_content=convertkit" class="convertkit-docs" target="_blank">Help</a>');
		$I->seeInSource('<a href="https://help.convertkit.com/en/articles/2502591-the-convertkit-wordpress-plugin?utm_source=wordpress&amp;utm_term=en_US&amp;utm_content=convertkit" target="_blank">plugin documentation</a>');
	}

	/**
	 * Test that no PHP errors or notices are displayed on the Plugin's Setting screen when the Save Changes
	 * button is pressed and no settings are specified.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSaveBlankSettings(AcceptanceTester $I)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the 'Click here to create your first form' link is displayed and links
		// to creating an inline Form in ConvertKit.
		$I->see('No Forms exist in ConvertKit.');
		$I->seeInSource('<a href="https://app.convertkit.com/forms/new/?utm_source=wordpress&amp;utm_term=en_US&amp;utm_content=convertkit" target="_blank">Click here to create your first form</a>');
	}

	/**
	 * Test that no PHP errors or notices are displayed on the Plugin's Setting screen,
	 * and a warning is displayed that the supplied API credentials are invalid, when
	 * saving invalid API credentials.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSaveInvalidAPICredentials(AcceptanceTester $I)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Complete API Fields with incorrect data.
		$I->fillField('_wp_convertkit_settings[api_key]', 'fakeApiKey');
		$I->fillField('_wp_convertkit_settings[api_secret]', 'fakeApiSecret');

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that a notice is displayed that the API credentials are invalid.
		$I->seeErrorNotice($I, 'Authorization Failed: API Key not valid');

		// Confirm option exists in DB.
		$I->seeOptionInDatabase('convertkit-admin-notices');

		// Navigate to the WordPress Admin.
		$I->amOnAdminPage('index.php');

		// Check that a notice is displayed that the API credentials are invalid.
		$I->seeErrorNotice($I, 'Convertkit: Authorization failed. Please enter valid API credentials on the settings screen.');
	}

	/**
	 * Test that no PHP errors or notices are displayed on the Plugin's Setting screen,
	 * when valid API credentials are saved.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSaveValidAPICredentials(AcceptanceTester $I)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Complete API Fields.
		$I->fillField('_wp_convertkit_settings[api_key]', $_ENV['CONVERTKIT_API_KEY']);
		$I->fillField('_wp_convertkit_settings[api_secret]', $_ENV['CONVERTKIT_API_SECRET']);

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the value of the fields match the inputs provided.
		$I->seeInField('_wp_convertkit_settings[api_key]', $_ENV['CONVERTKIT_API_KEY']);
		$I->seeInField('_wp_convertkit_settings[api_secret]', $_ENV['CONVERTKIT_API_SECRET']);

		// Check the order of the Form resources are alphabetical, with 'None' as the first choice.
		$I->checkSelectFormOptionOrder(
			$I,
			'#_wp_convertkit_settings_page_form',
			[
				'None',
			]
		);

		// Check that no notice is displayed that the API credentials are invalid.
		$I->dontSeeErrorNotice($I, 'Authorization Failed: API Key not valid');

		// Navigate to the WordPress Admin.
		$I->amOnAdminPage('index.php');

		// Check that no notice is displayed that the API credentials are invalid.
		$I->dontSeeErrorNotice($I, 'Convertkit: Authorization failed. Please enter valid API credentials on the settings screen.');
	}

	/**
	 * Test that no PHP errors or notices are displayed on the Plugin's Setting screen,
	 * when valid API credentials are saved, but the ConvertKit account for the given API
	 * credentials have no forms.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSaveValidAPICredentialsWithNoForms(AcceptanceTester $I)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Complete API Fields.
		$I->fillField('_wp_convertkit_settings[api_key]', $_ENV['CONVERTKIT_API_KEY_NO_DATA']);
		$I->fillField('_wp_convertkit_settings[api_secret]', $_ENV['CONVERTKIT_API_SECRET_NO_DATA']);

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the 'Click here to create your first form' link is displayed and links
		// to creating an inline Form in ConvertKit.
		$I->see('No Forms exist in ConvertKit.');
		$I->seeInSource('<a href="https://app.convertkit.com/forms/new/?utm_source=wordpress&amp;utm_term=en_US&amp;utm_content=convertkit" target="_blank">Click here to create your first form</a>');
	}

	/**
	 * Test that no PHP errors or notices are displayed on the Plugin's Setting screen,
	 * when the Default Form for Pages and Posts are changed, and that the preview links
	 * work when the Default Form is changed.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testChangeDefaultFormSettingAndPreviewFormLinks(AcceptanceTester $I)
	{
		// Create a Page and a Post, so that preview links display.
		$I->havePostInDatabase(
			[
				'post_title'  => 'ConvertKit: Preview Form Links: Page',
				'post_type'   => 'page',
				'post_status' => 'publish',
			]
		);
		$I->havePostInDatabase(
			[
				'post_title'  => 'ConvertKit: Preview Form Links: Post',
				'post_type'   => 'post',
				'post_status' => 'publish',
			]
		);

		// Setup Plugin, without defining default Forms.
		$I->setupConvertKitPluginNoDefaultForms($I);

		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Select Default Form for Pages.
		$I->fillSelect2Field($I, '#select2-_wp_convertkit_settings_page_form-container', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Open preview.
		$I->click('a#convertkit-preview-form-page');
		$I->wait(2); // Required, otherwise switchToNextTab fails.

		// Switch to newly opened tab.
		$I->switchToNextTab();

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]', 1);

		// Close newly opened tab.
		$I->closeTab();

		// Select Default Form for Posts.
		$I->fillSelect2Field($I, '#select2-_wp_convertkit_settings_post_form-container', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Open preview.
		$I->click('a#convertkit-preview-form-post');
		$I->wait(2); // Required, otherwise switchToNextTab fails.

		// Switch to newly opened tab.
		$I->switchToNextTab();

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]', 1);

		// Close newly opened tab.
		$I->closeTab();

		// Select a non-inline form for the Default non-inline form setting.
		$I->fillSelect2Field($I, '#select2-_wp_convertkit_settings_non_inline_form-container', $_ENV['CONVERTKIT_API_FORM_FORMAT_STICKY_BAR_NAME']);

		// Open preview.
		$I->click('a#convertkit-preview-non-inline-form');
		$I->wait(2); // Required, otherwise switchToNextTab fails.

		// Switch to newly opened tab.
		$I->switchToNextTab();

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_STICKY_BAR_ID'] . '"]', 1);

		// Close newly opened tab.
		$I->closeTab();

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the value of the fields match the inputs provided.
		$I->seeInField('_wp_convertkit_settings[page_form]', $_ENV['CONVERTKIT_API_FORM_NAME']);
		$I->seeInField('_wp_convertkit_settings[post_form]', $_ENV['CONVERTKIT_API_FORM_NAME']);
		$I->seeInField('_wp_convertkit_settings[non_inline_form]', $_ENV['CONVERTKIT_API_FORM_FORMAT_STICKY_BAR_NAME']);
	}

	/**
	 * Test that the settings screen does not display preview links
	 * when no Pages and Posts exist in WordPress.
	 *
	 * @since   1.9.8.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testPreviewFormLinksWhenNoPostsOrPagesExist(AcceptanceTester $I)
	{
		// Setup Plugin.
		$I->setupConvertKitPlugin($I);

		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Confirm no Page or Post preview links exist, because there are no Pages or Posts in WordPress.
		$I->dontSeeElementInDOM('a#convertkit-preview-form-post');
		$I->dontSeeElementInDOM('a#convertkit-preview-form-page');
	}

	/**
	 * Test that no PHP errors or notices are displayed on the Plugin's Setting screen
	 * when Debug settings are enabled and disabled.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testEnableAndDisableDebugSettings(AcceptanceTester $I)
	{
		// Setup Plugin.
		$I->setupConvertKitPlugin($I);

		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Tick field.
		$I->checkOption('#debug');

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the field remains ticked.
		$I->seeCheckboxIsChecked('#debug');

		// Untick field.
		$I->uncheckOption('#debug');

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the field remains unticked.
		$I->dontSeeCheckboxIsChecked('#debug');
	}

	/**
	 * Test that no PHP errors or notices are displayed on the Plugin's Setting screen
	 * when the Disable JavaScript settings are enabled and disabled.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testEnableAndDisableJavaScriptSettings(AcceptanceTester $I)
	{
		// Setup Plugin.
		$I->setupConvertKitPlugin($I);

		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Tick field.
		$I->checkOption('#no_scripts');

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the field remains ticked.
		$I->seeCheckboxIsChecked('#no_scripts');

		// Untick field.
		$I->uncheckOption('#no_scripts');

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the field remains unticked.
		$I->dontSeeCheckboxIsChecked('#no_scripts');
	}

	/**
	 * Test that no PHP errors or notices are displayed on the Plugin's Setting screen
	 * when the Disable CSS settings is unchecked, and that CSS is output
	 * on the frontend web site.
	 *
	 * @since   1.9.6.9
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testEnableAndDisableCSSSetting(AcceptanceTester $I)
	{
		// Setup Plugin.
		$I->setupConvertKitPlugin($I);

		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Tick field.
		$I->checkOption('#no_css');

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the field remains ticked.
		$I->seeCheckboxIsChecked('#no_css');

		// Navigate to the home page.
		$I->amOnPage('/');

		// Confirm no CSS is output by the Plugin.
		$I->dontSeeInSource('broadcasts.css');
		$I->dontSeeInSource('button.css');

		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Untick field.
		$I->uncheckOption('#no_css');

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the field remains unticked.
		$I->dontSeeCheckboxIsChecked('#no_css');

		// Navigate to the home page.
		$I->amOnPage('/');

		// Confirm CSS is output by the Plugin.
		$I->seeInSource('<link rel="stylesheet" id="convertkit-broadcasts-css" href="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-content/plugins/convertkit/resources/frontend/css/broadcasts.css');
		$I->seeInSource('<link rel="stylesheet" id="convertkit-button-css" href="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-content/plugins/convertkit/resources/frontend/css/button.css');
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
