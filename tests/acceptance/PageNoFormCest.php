<?php
/**
 * Tests for WordPress Pages when no ConvertKit Forms exist in the ConvertKit account.
 * 
 * @since 	1.9.6.1
 */
class PageNoFormCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 * 
	 * @since 	1.9.6.1
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _before(AcceptanceTester $I)
	{
		// Activate and Setup ConvertKit plugin
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPlugin($I, $_ENV['CONVERTKIT_API_KEY_NO_DATA'], $_ENV['CONVERTKIT_API_SECRET_NO_DATA']);
		$I->enableDebugLog($I);
		
		// Navigate to Pages > Add New
		$I->amOnAdminPage('post-new.php?post_type=page');

		// Close the Gutenberg "Welcome to the block editor" dialog if it's displayed
		$I->maybeCloseGutenbergWelcomeModal($I);
	}

	/**
	 * Test that text is displayed stating no forms / landing pages exist when using API Keys
	 * linked to a ConvertKit account that has no forms or landing pages.
	 * 
	 * @since 	1.9.6.1
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testNoFormsExistTextDisplayed(AcceptanceTester $I)
	{
		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the metabox is displayed.
		$I->seeElementInDOM('#wp-convertkit-meta-box');

		// Check that the correct text is displayed.
		$I->seeInSource('No Forms exist in ConvertKit.');
		$I->seeInSource('No Landing Pages exist in ConvertKit.');
	}

	/**
	 * Test that UTM parameters are included in links displayed in the metabox for the user to sign in to
	 * their ConvertKit account.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testUTMParametersExist(AcceptanceTester $I)
	{
		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the metabox is displayed.
		$I->seeElementInDOM('#wp-convertkit-meta-box');

		// Confirm that UTM parameters exist for the 'sign in to ConvertKit' link.
		$I->seeInSource('<a href="https://app.convertkit.com/?utm_source=wordpress&amp;utm_content=convertkit" target="_blank">sign in to ConvertKit</a>');
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