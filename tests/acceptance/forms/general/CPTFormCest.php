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

		// Setup ConvertKit plugin .
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Create a Custom Post Type called Articles, using the Custom Post Type UI Plugin.
		$I->registerCustomPostType($I, 'article', 'Articles', 'Article');
	}

	/**
	 * Tests that:
	 * - no ConvertKit options are displayed when adding a new Custom Post Type,
	 * - no debug output is displayed when viewing a Custom Post Type.
	 *
	 * @since   2.3.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testNoOptionsOrOutputOnCustomPostType(AcceptanceTester $I)
	{
		// Add an Article using the Gutenberg editor.
		$I->addGutenbergPage($I, 'article', 'ConvertKit: Article: Form: None');

		// Check that the metabox is not displayed.
		$I->dontSeeElementInDOM('#wp-convertkit-meta-box');

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that no ConvertKit Form is displayed.
		$I->dontSeeElementInDOM('form[data-sv-form]');

		// Confirm that no debug data is output, as this isn't a supported Post Type.
		$I->dontSeeInSource('<!-- ConvertKit append_form_to_content()');
	}

	/**
	 * Tests that no ConvertKit options are display when quick or bulk editing in a Custom Post Type.
	 *
	 * @since   2.3.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testNoBulkOrQuickEditOptionsOnCustomPostType(AcceptanceTester $I)
	{
		// Programmatically create two Articles.
		$postIDs = array(
			$I->havePostInDatabase(
				[
					'post_type'  => 'article',
					'post_title' => 'ConvertKit: Article: #1',
				]
			),
			$I->havePostInDatabase(
				[
					'post_type'  => 'article',
					'post_title' => 'ConvertKit: Article: #2',
				]
			),
		);

		// Navigate to Articles.
		$I->amOnAdminPage('edit.php?post_type=article');

		// Confirm no Bulk or Quick Edit settings are available.
		$I->dontSeeElementInDOM('#convertkit-bulk-edit');
		$I->dontSeeElementInDOM('#convertkit-quick-edit');
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
		$I->unregisterCustomPostType($I, 'article');
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
