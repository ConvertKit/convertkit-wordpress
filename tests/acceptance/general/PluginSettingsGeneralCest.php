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
	 * UTM parameters are included in links displayed on the Plugins' Setting screen.
	 *
	 * @since   1.9.7.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAccessibilityAndUTMParameters(AcceptanceTester $I)
	{
		// Setup Plugin.
		$I->setupConvertKitPlugin($I);

		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Confirm that settings have label[for] attributes.
		$I->seeInSource('<label for="_wp_convertkit_settings_page_form">');
		$I->seeInSource('<label for="_wp_convertkit_settings_post_form">');
		$I->seeInSource('<label for="debug">');
		$I->seeInSource('<label for="no_scripts">');
		$I->seeInSource('<label for="no_css">');

		// Confirm that the UTM parameters exist for the documentation links.
		$I->seeInSource('<a href="https://help.convertkit.com/en/articles/2502591-the-convertkit-wordpress-plugin?utm_source=wordpress&amp;utm_term=en_US&amp;utm_content=convertkit" class="convertkit-docs" target="_blank">Help</a>');
		$I->seeInSource('<a href="https://help.convertkit.com/en/articles/2502591-the-convertkit-wordpress-plugin?utm_source=wordpress&amp;utm_term=en_US&amp;utm_content=convertkit" target="_blank">plugin documentation</a>');
	}

	/**
	 * Test that no PHP errors or notices are displayed on the Plugin's Setting screen
	 * and a Connect button is displayed when no credentials exist.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testNoCredentials(AcceptanceTester $I)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Confirm no option is displayed to save changes, as the Plugin isn't authenticated.
		$I->dontSeeElementInDOM('input#submit');

		// Confirm the Connect button displays.
		$I->see('Connect');
		$I->dontSee('Disconnect');

		// Check that a link to the OAuth auth screen exists and includes the state parameter.
		$I->seeInSource('<a href="https://app.convertkit.com/oauth/authorize?client_id=' . $_ENV['CONVERTKIT_OAUTH_CLIENT_ID'] . '&amp;response_type=code&amp;redirect_uri=' . urlencode( $_ENV['CONVERTKIT_OAUTH_REDIRECT_URI'] ) );
		$I->seeInSource(
			'&amp;state=' . $I->apiEncodeState(
				$_ENV['TEST_SITE_WP_URL'] . '/wp-admin/options-general.php?page=_wp_convertkit_settings',
				$_ENV['CONVERTKIT_OAUTH_CLIENT_ID']
			)
		);

		// Click the connect button.
		$I->click('Connect');

		// Confirm the ConvertKit hosted OAuth login screen is displayed.
		$I->waitForElementVisible('body.sessions');
		$I->seeInSource('oauth/authorize?client_id=' . $_ENV['CONVERTKIT_OAUTH_CLIENT_ID']);
	}

	/**
	 * Test that no PHP errors or notices are displayed on the Plugin's Setting screen,
	 * and a warning is displayed that the supplied credentials are invalid, when
	 * e.g. the access token has been revoked.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testInvalidCredentials(AcceptanceTester $I)
	{
		// Setup Plugin.
		$I->setupConvertKitPlugin(
			$I,
			[
				'access_token'  => 'fakeAccessToken',
				'refresh_token' => 'fakeRefreshToken',
			]
		);

		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Confirm the Connect button displays.
		$I->see('Connect');
		$I->dontSee('Disconnect');
		$I->dontSeeElementInDOM('input#submit');

		// Navigate to the WordPress Admin.
		$I->amOnAdminPage('index.php');

		// Check that a notice is displayed that the API credentials are invalid.
		$I->seeErrorNotice($I, 'ConvertKit: Authorization failed. Please connect your ConvertKit account.');
	}

	/**
	 * Test that no PHP errors or notices are displayed on the Plugin's Setting screen,
	 * when valid credentials exist.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testValidCredentials(AcceptanceTester $I)
	{
		// Setup Plugin.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Confirm the Disconnect and Save Changes buttons display.
		$I->see('Disconnect');
		$I->seeElementInDOM('input#submit');

		// Check the order of the Form resources are alphabetical, with 'None' as the first choice.
		$I->checkSelectFormOptionOrder(
			$I,
			'#_wp_convertkit_settings_page_form',
			[
				'None',
			]
		);

		// Save Changes to confirm credentials are not lost.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm the Disconnect and Save Changes buttons display.
		$I->see('Disconnect');
		$I->seeElementInDOM('input#submit');

		// Navigate to the WordPress Admin.
		$I->amOnAdminPage('index.php');

		// Check that no notice is displayed that the API credentials are invalid.
		$I->dontSeeErrorNotice($I, 'ConvertKit: Authorization failed. Please connect your ConvertKit account.');

		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Disconnect the Plugin connection to ConvertKit.
		$I->click('Disconnect');

		// Confirm the Connect button displays.
		$I->see('Connect');
		$I->dontSee('Disconnect');
		$I->dontSeeElementInDOM('input#submit');

		// Check that the option table no longer contains cached resources.
		$I->dontSeeOptionInDatabase('convertkit_creator_network_recommendations');
		$I->dontSeeOptionInDatabase('convertkit_forms');
		$I->dontSeeOptionInDatabase('convertkit_landing_pages');
		$I->dontSeeOptionInDatabase('convertkit_posts');
		$I->dontSeeOptionInDatabase('convertkit_products');
		$I->dontSeeOptionInDatabase('convertkit_tags');
	}

	/**
	 * Test that an error notice displays when the `error_description` is present in the URL,
	 * typically when the user denies access via OAuth or exchanging a code for an access token failed.
	 *
	 * @since   2.5.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testErrorNoticeDisplaysOnOAuthFailure($I)
	{
		// Go to the Plugin's Settings Screen, as if we came back from OAuth where the user did not
		// grant access, or exchanging a code for an access token failed.
		$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings&error_description=Client+authentication+failed+due+to+unknown+client%2C+no+client+authentication+included%2C+or+unsupported+authentication+method.');

		// Check that a notice is displayed that the API credentials are invalid.
		$I->seeErrorNotice($I, 'Client authentication failed due to unknown client, no client authentication included, or unsupported authentication method.');

		// Confirm the Connect button displays.
		$I->see('Connect');
		$I->dontSee('Disconnect');
		$I->dontSeeElementInDOM('input#submit');
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

		// Select Default Form for Pages, and change the Position.
		$I->fillSelect2Field($I, '#select2-_wp_convertkit_settings_page_form-container', $_ENV['CONVERTKIT_API_FORM_NAME']);
		$I->selectOption('_wp_convertkit_settings[page_form_position]', 'Before Content');

		// Open preview.
		$I->click('a#convertkit-preview-form-page');
		$I->wait(2); // Required, otherwise switchToNextTab fails.

		// Switch to newly opened tab.
		$I->switchToNextTab();

		// Confirm that the preview is a WordPress Page.
		$I->seeElementInDOM('body.page');

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeFormOutput($I, $_ENV['CONVERTKIT_API_FORM_ID']);

		// Close newly opened tab.
		$I->closeTab();

		// Select Default Form for Posts.
		$I->fillSelect2Field($I, '#select2-_wp_convertkit_settings_post_form-container', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Open preview.
		$I->click('a#convertkit-preview-form-post');
		$I->wait(2); // Required, otherwise switchToNextTab fails.

		// Switch to newly opened tab.
		$I->switchToNextTab();

		// Confirm that the preview is a WordPress Post.
		$I->seeElementInDOM('body.single-post');

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeFormOutput($I, $_ENV['CONVERTKIT_API_FORM_ID']);

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
		$I->seeInField('_wp_convertkit_settings[page_form_position]', 'Before Content');
		$I->seeInField('_wp_convertkit_settings[post_form]', $_ENV['CONVERTKIT_API_FORM_NAME']);
		$I->seeInField('_wp_convertkit_settings[post_form_position]', 'After Content');
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
	 * Test that a Default Form setting for a public Custom Post Type exists in the settings screen,
	 * and no Default Form setting for a private Custom Post Type exists.
	 *
	 * @since   1.9.8.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testPublicPrivateCustomPostTypeSettingsExist(AcceptanceTester $I)
	{
		// Create a public Custom Post Type called Articles, using the Custom Post Type UI Plugin.
		$I->registerCustomPostType($I, 'article', 'Articles', 'Article');

		// Create a non-public Custom Post Type called Private, using the Custom Post Type UI Plugin.
		$I->registerCustomPostType($I, 'private', 'Private', 'Private', false);

		// Setup Plugin, without defining default Forms.
		$I->setupConvertKitPluginNoDefaultForms($I);

		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Select Default Form for Articles.
		$I->fillSelect2Field($I, '#select2-_wp_convertkit_settings_article_form-container', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Confirm no Default Form option is displayed for the Private CPT.
		$I->dontSeeElementInDOM('#_wp_convertkit_settings_private_form');

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the value of the fields match the inputs provided.
		$I->seeInField('_wp_convertkit_settings[article_form]', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Unregister CPTs.
		$I->unregisterCustomPostType($I, 'article');
		$I->unregisterCustomPostType($I, 'private');
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
