<?php
/**
 * Tests for the ConvertKit Product Gutenberg Block.
 *
 * @since   2.4.1
 */
class PageBlockProductMobileCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   2.4.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->changeUserAgent($_ENV['TEST_SITE_HTTP_USER_AGENT_MOBILE']);
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);
	}



	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   2.4.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
		$I->changeUserAgent($_ENV['TEST_SITE_HTTP_USER_AGENT']);
	}
}
