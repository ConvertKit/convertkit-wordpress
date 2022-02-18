<?php
/**
 * Tests for ConvertKit Settings on WordPress Pages when no API Credentials specified.
 * 
 * @since 	1.9.6
 */
class PageCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
	}

	/**
	 * Test that the ConvertKit Page Settings displays a message with a link to the Plugin Settings
	 * telling the user to configure their API Credentials, when no API Credentials exist.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testAddNewPageShowsLinkToPluginSettingsWhenNoAPICredentialsSpecified(AcceptanceTester $I)
	{
		// Navigate to Pages > Add New
		$I->amOnAdminPage('post-new.php?post_type=page');

		// Close the Gutenberg "Welcome to the block editor" dialog if it's displayed
		$I->maybeCloseGutenbergWelcomeModal($I);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the metabox is displayed.
		$I->seeElementInDOM('#wp-convertkit-meta-box');

		// Check that the Form option is not displayed.
		$I->dontSeeElementInDOM('#wp-convertkit-form');

		// Check that an expected message is displayed.
		$I->seeInSource('To configure the ConvertKit Form / Landing Page to display on this Page, enter your ConvertKit API credentials');

		// Check that a link to the Plugin Settings exists.
		$I->seeInSource('<a href="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-admin/options-general.php?page=_wp_convertkit_settings">Plugin Settings</a>');
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 * 
	 * @since 	1.9.6.7
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}