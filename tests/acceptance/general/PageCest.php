<?php
/**
 * Tests for ConvertKit Settings on WordPress Pages when no API Credentials specified.
 *
 * @since   1.9.6
 */
class PageCest
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
	 * Test that the ConvertKit Page Settings displays a message with a link to the Plugin Settings
	 * telling the user to connect their ConvertKit account, when no credentials exist.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewPageShowsLinkToPluginSettingsWhenNoCredentialsSpecified(AcceptanceTester $I)
	{
		// Navigate to Pages > Add New.
		$I->amOnAdminPage('post-new.php?post_type=page');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the metabox is displayed.
		$I->seeElementInDOM('#wp-convertkit-meta-box');

		// Check that the Form option is not displayed.
		$I->dontSeeElementInDOM('#wp-convertkit-form');

		// Check that an expected message is displayed.
		$I->see('For the ConvertKit Plugin to function, please connect your ConvertKit account.');

		// Check that a link to the OAuth auth screen exists and includes the state parameter.
		$I->seeInSource('<a href="https://app.convertkit.com/oauth/authorize?client_id=' . $_ENV['CONVERTKIT_OAUTH_CLIENT_ID'] . '&amp;response_type=code&amp;redirect_uri=' . urlencode( $_ENV['CONVERTKIT_OAUTH_REDIRECT_URI'] ) );
		$I->seeInSource(
			'&amp;state=' . $I->apiEncodeState(
				$_ENV['TEST_SITE_WP_URL'] . '/wp-admin/options-general.php?page=_wp_convertkit_settings',
				$_ENV['CONVERTKIT_OAUTH_CLIENT_ID']
			)
		);

		// Click the link.
		$I->click('connect your ConvertKit account.');

		// Confirm the ConvertKit hosted OAuth login screen is displayed.
		$I->waitForElementVisible('body.sessions');
		$I->seeInSource('oauth/authorize?client_id=' . $_ENV['CONVERTKIT_OAUTH_CLIENT_ID']);
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
