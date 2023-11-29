<?php
/**
 * Tests for non-inline ConvertKit Forms.
 *
 * @since   2.3.9
 */
class NonInlineFormCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   2.3.9
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		// Activate ConvertKit plugin.
		$I->activateConvertKitPlugin($I);
	}

	/**
	 * Test that the defined default non-inline form displays site wide.
	 *
	 * @since   2.3.9
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testDefaultNonInlineForm(AcceptanceTester $I)
	{
		// Create a Page in the database.
		$I->havePostInDatabase(
			[
				'post_title'  => 'ConvertKit: Default Non Inline Global',
				'post_type'   => 'page',
				'post_status' => 'publish',
			]
		);

		// Setup Plugin.
		$I->setupConvertKitPlugin($I);

		// View the home page.
		$I->amOnPage('/');

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_STICKY_BAR_ID'] . '"]', 1);

		// View Page.
		$I->amOnPage('/convertkit-default-non-inline-global');

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_STICKY_BAR_ID'] . '"]', 1);
	}

	/**
	 * Test that no non-inline form displays site wide when not selected in the Plugin's settings.
	 *
	 * @since   2.3.9
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testNoFormWhenNoDefaultNonInlineForm(AcceptanceTester $I)
	{
		// Setup Plugin.
		$I->setupConvertKitPlugin($I);

		// View the home page.
		$I->amOnPage('/');

		// Confirm that no ConvertKit Form is output in the DOM.
		$I->dontSeeElementInDOM('form[data-sv-form]');
	}

	/**
	 * Test that the non-inline form defined as the Default Form for Pages overrides
	 * the non-inline form defined in the Default Non-Inline Form (Global) setting
	 * when a Page is viewed.
	 *
	 * @since   2.3.9
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testDefaultNonInlineFormIgnoredWhenDefaultPageNonInlineFormDefined(AcceptanceTester $I)
	{

	}

	/**
	 * Test that the non-inline form defined on a Page overrides the non-inline form defined
	 * in the Default Non-Inline Form (Global) setting when a Page is viewed.
	 *
	 * @since   2.3.9
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testDefaultNonInlineFormIgnoredWhenPageNonInlineFormDefined(AcceptanceTester $I)
	{

	}

	/**
	 * Test that the non-inline form defined as the Default Form for Posts overrides
	 * the non-inline form defined in the Default Non-Inline Form (Global) setting
	 * when a Post is viewed.
	 *
	 * @since   2.3.9
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testDefaultNonInlineFormIgnoredWhenDefaultPostNonInlineFormDefined(AcceptanceTester $I)
	{
		
	}

	/**
	 * Test that the non-inline form defined on a Post overrides the non-inline form defined
	 * in the Default Non-Inline Form (Global) setting when a Post is viewed.
	 *
	 * @since   2.3.9
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testDefaultNonInlineFormIgnoredWhenPostNonInlineFormDefined(AcceptanceTester $I)
	{

	}

	/**
	 * Test that the non-inline form defined on a Category overrides the non-inline form defined
	 * in the Default Non-Inline Form (Global) setting when a Post assigned to the Category is viewed.
	 *
	 * @since   2.3.9
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testDefaultNonInlineFormIgnoredWhenPostCategoryNonInlineFormDefined(AcceptanceTester $I)
	{

	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   2.3.9
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
