<?php
/**
 * Tests for WordPress Custom Post Types.
 *
 * @since   2.3.5
 */
class CPTFormCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   2.3.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		// Activate ConvertKit plugin.
		$I->activateConvertKitPlugin($I);

		// Create a Custom Post Type using the Custom Post Type UI Plugin.
		// @TODO.
	}

	/**
	 * Tests that:
	 * - no ConvertKit options are displayed when adding a new Custom Post Type,
	 * - no debug output is displayed when viewing a Custom Post Type.
	 * 
	 * @since 	2.3.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testNoOptionsOrOutputOnCustomPostType(AcceptanceTester $I)
	{
		
	}

	/**
	 * Tests that no ConvertKit options are display when quick or bulk editing in a Custom Post Type.
	 * 
	 * @since 	2.3.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testNoBulkOrQuickEditOptionsOnCustomPostType(AcceptanceTester $I)
	{

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
